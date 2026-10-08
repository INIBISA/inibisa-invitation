<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerInvitationSectionController extends Controller
{
    public function guests(Request $request): RedirectResponse|View
    {
        return $this->show($request, 'Tamu Undangan', 'Kelola daftar tamu dan bagikan tautan pribadi.', 'invitations.guests.index');
    }

    public function rsvps(Request $request): RedirectResponse|View
    {
        return $this->show($request, 'RSVP', 'Lihat konfirmasi kehadiran dari tamu undangan.', 'invitations.rsvps');
    }

    public function wishes(Request $request): RedirectResponse|View
    {
        return $this->show($request, 'Ucapan', 'Baca ucapan yang dikirim oleh tamu undangan.', 'invitations.wishes');
    }

    private function show(Request $request, string $title, string $description, string $invitationRoute): RedirectResponse|View
    {
        abort_if($request->user()->isAdmin(), 403);

        $invitations = $request->user()->invitations()
            ->latest('id')
            ->paginate(10, ['id', 'title', 'slug', 'status']);

        if ($invitations->total() === 1 && $invitations->isNotEmpty()) {
            return redirect()->route($invitationRoute, $invitations->first());
        }

        return view('invitations.section-index', compact('invitations', 'title', 'description', 'invitationRoute'));
    }
}
