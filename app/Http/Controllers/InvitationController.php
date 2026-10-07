<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Template;
use App\Models\WeddingMusic;
use App\Services\InvitationMediaManager;
use App\Support\InvitationData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $templates = Template::query()->where('is_active', true)->whereHas('payments', fn ($payments) => $payments->whereBelongsTo(auth()->user())->where('status', Payment::STATUS_PAID)->whereNull('invitation_id'))->orderBy('name')->get();

        if ($request->filled('template_id') && ! $templates->contains('id', $request->integer('template_id'))) {
            $template = Template::query()->where('is_active', true)->findOrFail($request->integer('template_id'));

            return redirect()->route('payments.create', $template);
        }

        if ($templates->isEmpty()) {
            return redirect()->route('templates.index')->with('success', 'Pilih template dan selesaikan pembayaran untuk mulai membuat undangan.');
        }

        $musicChoices = WeddingMusic::query()->where('is_active', true)->orderBy('category')->orderBy('title')->get();

        return view('invitations.create', compact('templates', 'musicChoices'));
    }

    public function store(StoreInvitationRequest $request, InvitationMediaManager $mediaManager): RedirectResponse
    {
        $invitation = DB::transaction(function () use ($request): Invitation {
            $payment = Payment::query()->whereBelongsTo($request->user())->where('template_id', $request->integer('template_id'))->where('status', Payment::STATUS_PAID)->whereNull('invitation_id')->lockForUpdate()->firstOrFail();
            $invitation = $request->user()->invitations()->create([
                'template_id' => $request->integer('template_id'),
                'wedding_music_id' => $request->filled('youtube_url') ? null : $request->input('wedding_music_id'),
                'title' => $request->string('title')->toString(),
                'slug' => $request->string('slug')->toString(),
                'data' => InvitationData::fromValidated($request->validated()),
            ]);
            $payment->update(['invitation_id' => $invitation->id]);

            return $invitation;
        }, attempts: 3);

        $mediaManager->syncFromRequest($invitation, $request);

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Undangan berhasil dibuat.');
    }

    public function edit(Invitation $invitation): View
    {
        Gate::authorize('update', $invitation);
        $invitation->load('media');
        $templates = Template::query()->whereKey($invitation->template_id)->get();
        $musicChoices = WeddingMusic::query()->where('is_active', true)->orderBy('category')->orderBy('title')->get();

        return view('invitations.edit', compact('invitation', 'templates', 'musicChoices'));
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation, InvitationMediaManager $mediaManager): RedirectResponse
    {
        $invitation->update([
            'template_id' => $invitation->template_id,
            'wedding_music_id' => $request->filled('youtube_url') ? null : $request->input('wedding_music_id'),
            'title' => $request->string('title')->toString(),
            'slug' => $request->string('slug')->toString(),
            'data' => InvitationData::fromValidated($request->validated()),
        ]);

        $mediaManager->syncFromRequest($invitation, $request);

        return back()->with('success', 'Perubahan undangan tersimpan.');
    }

    public function destroy(Invitation $invitation, InvitationMediaManager $mediaManager): RedirectResponse
    {
        Gate::authorize('delete', $invitation);
        $mediaManager->deleteAll($invitation);
        $invitation->delete();

        return redirect()->route('dashboard')->with('success', 'Undangan dihapus.');
    }
}
