<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class InvitationPublicationController extends Controller
{
    public function store(Invitation $invitation): RedirectResponse
    {
        Gate::authorize('update', $invitation);
        if (! $invitation->template()->where('is_active', true)->exists()) {
            return back()->withErrors(['publication' => 'Template tidak aktif.']);
        }

        $data = $invitation->data ?? [];
        if (! (
            filled(data_get($data, 'groom.nickname')) && filled(data_get($data, 'groom.full_name'))
            && filled(data_get($data, 'bride.nickname')) && filled(data_get($data, 'bride.full_name'))
            && filled(data_get($data, 'wedding_date')) && count(data_get($data, 'events', [])) > 0
        )) {
            return back()->withErrors(['publication' => 'Lengkapi nama mempelai, tanggal, dan minimal satu acara sebelum publikasi.']);
        }

        $invitation->update([
            'status' => Invitation::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Undangan berhasil dipublikasikan.');
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        Gate::authorize('update', $invitation);
        $invitation->update(['status' => Invitation::STATUS_INACTIVE]);

        return back()->with('success', 'Undangan dinonaktifkan.');
    }
}
