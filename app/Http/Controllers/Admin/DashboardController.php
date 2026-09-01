<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Template;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statistics = [
            'pending_customers' => User::query()->where('role', User::ROLE_CUSTOMER)->where('status', User::STATUS_PENDING)->count(),
            'active_customers' => User::query()->where('role', User::ROLE_CUSTOMER)->where('status', User::STATUS_ACTIVE)->count(),
            'invitations' => Invitation::query()->count(),
            'templates' => Template::query()->count(),
        ];

        return view('admin.dashboard', compact('statistics'));
    }
}
