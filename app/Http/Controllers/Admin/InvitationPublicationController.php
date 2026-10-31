<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;

class InvitationPublicationController extends Controller
{
    public function __invoke(Invitation $invitation): RedirectResponse
    {
        if ($invitation->status !== Invitation::STATUS_PUBLISHED) {
            return back()->withErrors(['publication' => 'Tautan undangan ini sudah tidak aktif.']);
        }

        $invitation->update(['status' => Invitation::STATUS_INACTIVE]);

        return back()->with('success', 'Tautan undangan berhasil dihapus.');
    }
}
