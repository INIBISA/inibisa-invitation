<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Rsvp;
use App\Models\User;
use App\Models\Wish;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statistics = [
            'customers' => User::query()->where('role', User::ROLE_CUSTOMER)->count(),
            'payments' => Payment::query()->count(),
            'payments_today' => Payment::query()->whereDate('created_at', today())->count(),
            'pending_payments' => Payment::query()->where('status', Payment::STATUS_PENDING)->count(),
            'waiting_approval' => Payment::query()->where('status', Payment::STATUS_WAITING_APPROVAL)->count(),
            'invitations' => Invitation::query()->count(),
            'published_invitations' => Invitation::query()->where('status', Invitation::STATUS_PUBLISHED)->count(),
            'guests' => Guest::query()->count(),
            'rsvps' => Rsvp::query()->count(),
            'wishes' => Wish::query()->count(),
        ];

        $recentInvitations = Invitation::query()
            ->with(['user:id,name', 'template:id,name'])
            ->latest('id')
            ->limit(5)
            ->get();

        $recentRsvp = Rsvp::query()->with('invitation:id,title')->latest('id')->first();
        $recentCustomer = User::query()->where('role', User::ROLE_CUSTOMER)->latest('id')->first();
        $recentWish = Wish::query()->with('invitation:id,title')->latest('id')->first();

        return view('admin.dashboard', compact('statistics', 'recentInvitations', 'recentRsvp', 'recentCustomer', 'recentWish'));
    }
}
