<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Models\Invitation;
use App\Models\Template;
use App\Services\InvitationMediaManager;
use App\Support\InvitationData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(): View
    {
        $templates = Template::query()->where('is_active', true)->orderBy('name')->get();

        return view('invitations.create', compact('templates'));
    }

    public function store(StoreInvitationRequest $request, InvitationMediaManager $mediaManager): RedirectResponse
    {
        $invitation = $request->user()->invitations()->create([
            'template_id' => $request->integer('template_id'),
            'title' => $request->string('title')->toString(),
            'slug' => $request->string('slug')->toString(),
            'data' => InvitationData::fromValidated($request->validated()),
        ]);

        $mediaManager->syncFromRequest($invitation, $request);

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Undangan berhasil dibuat.');
    }

    public function edit(Invitation $invitation): View
    {
        Gate::authorize('update', $invitation);
        $invitation->load('media');
        $templates = Template::query()->where('is_active', true)->orderBy('name')->get();

        return view('invitations.edit', compact('invitation', 'templates'));
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation, InvitationMediaManager $mediaManager): RedirectResponse
    {
        $invitation->update([
            'template_id' => $request->integer('template_id'),
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
