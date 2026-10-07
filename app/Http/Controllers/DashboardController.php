<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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
            ->paginate(10);

        $payments = $request->user()->payments()->with('template')->latest()->limit(10)->get();
        $availablePayments = $request->user()->payments()->with('template')->where('status', Payment::STATUS_PAID)->whereNull('invitation_id')->get();

        return view('dashboard.index', compact('invitations', 'payments', 'availablePayments'));
    }
}
