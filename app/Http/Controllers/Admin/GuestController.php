<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function __invoke(Request $request): View
    {
        $guests = Guest::query()->with(['invitation.user', 'invitation.template'])
            ->when($request->filled('customer'), fn ($query) => $query->whereHas('invitation.user', fn ($users) => $users->where('name', 'like', '%'.$request->string('customer')->toString().'%')))
            ->when($request->filled('invitation'), fn ($query) => $query->whereHas('invitation', fn ($invitations) => $invitations->where('title', 'like', '%'.$request->string('invitation')->toString().'%')))
            ->orderBy('name')->paginate(30)->withQueryString();

        return view('admin.guests.index', compact('guests'));
    }
}
