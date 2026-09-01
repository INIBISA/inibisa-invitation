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
        abort_unless($invitation->template()->where('is_active', true)->exists(), 422, 'Template tidak aktif.');

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
