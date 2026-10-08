<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.customers');
    }

    public function data(): JsonResponse
    {
        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->withCount(['invitations', 'payments']);

        return DataTables::eloquent($customers)
            ->addIndexColumn()
            ->editColumn('created_at', fn (User $user): string => $user->created_at->translatedFormat('d M Y'))
            ->toJson();
    }
}
