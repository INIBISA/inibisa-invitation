<?php

namespace App\Http\Controllers;

use App\Exports\GuestsTemplateExport;
use App\Http\Requests\ImportGuestsRequest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Imports\GuestsImport;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuestController extends Controller
{
    public function index(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $guestStats = $invitation->guests()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN sent_at IS NOT NULL THEN 1 ELSE 0 END) as sent')
            ->first();
        $guests = $invitation->guests()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->toString().'%'))
            ->when($request->query('contact') === 'with_whatsapp', fn ($query) => $query->whereNotNull('whatsapp'))
            ->when($request->query('contact') === 'without_whatsapp', fn ($query) => $query->whereNull('whatsapp'))
            ->when($request->query('delivery') === 'sent', fn ($query) => $query->whereNotNull('sent_at'))
            ->when($request->query('delivery') === 'unsent', fn ($query) => $query->whereNull('sent_at'))
            ->orderBy('name')->paginate(30)->withQueryString();

        return view('guests.index', compact('invitation', 'guests', 'guestStats'));
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
