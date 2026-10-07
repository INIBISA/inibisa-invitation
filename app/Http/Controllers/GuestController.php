<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $guests = $invitation->guests()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->toString().'%'))
            ->when($request->query('contact') === 'with_whatsapp', fn ($query) => $query->whereNotNull('whatsapp'))
            ->when($request->query('contact') === 'without_whatsapp', fn ($query) => $query->whereNull('whatsapp'))
            ->orderBy('name')->paginate(30)->withQueryString();

        return view('guests.index', compact('invitation', 'guests'));
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

    public function destroy(Request $request, Guest $guest): RedirectResponse
    {
        abort_unless($request->user()?->can('update', $guest->invitation), 403);
        $guest->delete();

        return back()->with('success', 'Tamu dihapus.');
    }
}
