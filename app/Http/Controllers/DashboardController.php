<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $invitations = $request->user()->invitations()
            ->with('template:id,name')
            ->withCount(['rsvps', 'wishes'])
            ->latest('id')
            ->get();

        return view('dashboard.index', compact('invitations'));
    }
}
