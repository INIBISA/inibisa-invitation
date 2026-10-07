<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvitationResponseController extends Controller
{
    public function rsvps(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $rsvps = $invitation->rsvps()
            ->when($request->filled('search'), fn ($query) => $query->where('guest_name', 'like', '%'.$request->string('search')->toString().'%'))
            ->when(in_array($request->query('attendance'), ['attending', 'not_attending'], true), fn ($query) => $query->where('attendance', $request->query('attendance')))
            ->latest('id')->paginate(30)->withQueryString();

        return view('invitations.rsvps', compact('invitation', 'rsvps'));
    }

    public function wishes(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $wishes = $invitation->wishes()
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($searchQuery) => $searchQuery->where('guest_name', 'like', '%'.$request->string('search')->toString().'%')->orWhere('message', 'like', '%'.$request->string('search')->toString().'%')))
            ->latest('id')->paginate(30)->withQueryString();

        return view('invitations.wishes', compact('invitation', 'wishes'));
    }
}
