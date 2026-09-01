<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvitationResponseController extends Controller
{
    public function rsvps(Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $rsvps = $invitation->rsvps()->latest('id')->paginate(30);

        return view('invitations.rsvps', compact('invitation', 'rsvps'));
    }

    public function wishes(Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $wishes = $invitation->wishes()->latest('id')->paginate(30);

        return view('invitations.wishes', compact('invitation', 'wishes'));
    }
}
