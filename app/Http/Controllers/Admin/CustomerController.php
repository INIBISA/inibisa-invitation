<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->withCount(['invitations', 'payments'])
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.customers', compact('customers'));
    }
}
