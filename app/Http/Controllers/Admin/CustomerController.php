<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status', User::STATUS_PENDING)->toString();

        if (! in_array($status, [User::STATUS_PENDING, User::STATUS_ACTIVE, User::STATUS_REJECTED], true)) {
            $status = User::STATUS_PENDING;
        }

        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->where('status', $status)
            ->withCount('invitations')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.customers', compact('customers', 'status'));
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        abort_unless($customer->role === User::ROLE_CUSTOMER, 404);
        $validated = $request->validate([
            'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_REJECTED])],
        ]);
        $customer->update($validated);

        return back()->with('success', 'Status customer berhasil diperbarui.');
    }
}
