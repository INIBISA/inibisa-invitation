<?php

namespace App\Http\Controllers;

use App\Exports\GuestsTemplateExport;
use App\Http\Requests\ImportGuestsRequest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Imports\GuestsImport;
use App\Models\Guest;
use App\Models\Invitation;
use App\Support\WhatsAppInvitationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class GuestController extends Controller
{
    public function index(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $guestStats = $invitation->guests()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN sent_at IS NOT NULL THEN 1 ELSE 0 END) as sent')
            ->first();
        $whatsAppMessageTemplate = $request->user()->whatsapp_message_template ?: WhatsAppInvitationMessage::defaultTemplate();

        return view('guests.index', compact('invitation', 'guestStats', 'whatsAppMessageTemplate'));
    }

    public function data(Request $request, Invitation $invitation): JsonResponse
    {
        Gate::authorize('view', $invitation);
        $guests = $invitation->guests()
            ->when($request->query('contact') === 'with_whatsapp', fn ($query) => $query->whereNotNull('whatsapp'))
            ->when($request->query('contact') === 'without_whatsapp', fn ($query) => $query->whereNull('whatsapp'))
            ->when($request->query('delivery') === 'sent', fn ($query) => $query->whereNotNull('sent_at'))
            ->when($request->query('delivery') === 'unsent', fn ($query) => $query->whereNull('sent_at'));
        $whatsAppMessageTemplate = $request->user()->whatsapp_message_template ?: WhatsAppInvitationMessage::defaultTemplate();

        return DataTables::eloquent($guests)
            ->addIndexColumn()
            ->addColumn('guest', fn (Guest $guest): string => '<div class="guest-person"><span>'.e(mb_strtoupper(mb_substr($guest->name, 0, 1))).'</span><div><strong>'.e($guest->name).'</strong><small>'.e($guest->whatsapp ?: 'Tanpa WhatsApp').'</small></div></div>')
            ->addColumn('delivery', fn (Guest $guest): string => '<span class="status-badge '.($guest->sent_at ? 'published' : 'draft').'"><i></i>'.($guest->sent_at ? 'Terkirim' : 'Belum dikirim').'</span>'.($guest->sent_at ? '<small class="guest-sent-time">'.$guest->sent_at->translatedFormat('d M Y, H:i').'</small>' : ''))
            ->addColumn('share', fn (Guest $guest): string => view('guests.partials.share-actions', ['guest' => $guest, 'invitation' => $invitation, 'whatsAppMessageTemplate' => $whatsAppMessageTemplate])->render())
            ->addColumn('action', fn (Guest $guest): string => view('guests.partials.row-actions', ['guest' => $guest])->render())
            ->filterColumn('guest', fn ($query, string $keyword) => $query->where(fn ($query) => $query->where('name', 'like', "%{$keyword}%")->orWhere('whatsapp', 'like', "%{$keyword}%")))
            ->rawColumns(['guest', 'delivery', 'share', 'action'])
            ->toJson();
    }

    public function store(StoreGuestRequest $request, Invitation $invitation): RedirectResponse
    {
        $invitation->guests()->create($request->validated());

        return back()->with('success', 'Tamu ditambahkan.');
    }

    public function update(UpdateGuestRequest $request, Guest $guest): RedirectResponse
    {
        $guest->update($request->validated());

        return back()->with('success', 'Data tamu diperbarui.');
    }

    public function import(ImportGuestsRequest $request, Invitation $invitation): RedirectResponse
    {
        $import = new GuestsImport($invitation);
        Excel::import($import, $request->file('guest_file'));

        return back()->with('success', "Import selesai: {$import->imported} ditambahkan, {$import->duplicates} duplikat, {$import->invalid} tidak valid.");
    }

    public function template(Invitation $invitation): BinaryFileResponse
    {
        Gate::authorize('view', $invitation);

        return Excel::download(new GuestsTemplateExport, 'template-tamu.xlsx');
    }

    public function delivery(Request $request, Guest $guest): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $guest->invitation);
        $validated = $request->validate(['sent' => ['required', 'boolean']]);
        $guest->update(['sent_at' => $validated['sent'] ? now() : null]);
        $message = $validated['sent'] ? 'Tamu ditandai sudah dikirim.' : 'Tamu ditandai belum dikirim.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'sent_at' => $guest->sent_at?->toISOString()]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, Guest $guest): RedirectResponse
    {
        abort_unless($request->user()?->can('update', $guest->invitation), 403);
        $guest->delete();

        return back()->with('success', 'Tamu dihapus.');
    }
}
