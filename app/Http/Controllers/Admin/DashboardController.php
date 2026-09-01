<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\Template;
use App\Models\User;
use App\Models\Wish;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statistics = [
            'pending_customers' => User::query()->where('role', User::ROLE_CUSTOMER)->where('status', User::STATUS_PENDING)->count(),
            'active_customers' => User::query()->where('role', User::ROLE_CUSTOMER)->where('status', User::STATUS_ACTIVE)->count(),
            'invitations' => Invitation::query()->count(),
            'published_invitations' => Invitation::query()->where('status', Invitation::STATUS_PUBLISHED)->count(),
            'new_rsvps' => Rsvp::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'templates' => Template::query()->count(),
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
