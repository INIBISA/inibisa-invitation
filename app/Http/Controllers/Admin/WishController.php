<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wish;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $wishes = Wish::query()
            ->with(['invitation:id,user_id,title,slug', 'invitation.user:id,name'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('guest_name', 'like', '%'.$search.'%')
                        ->orWhere('message', 'like', '%'.$search.'%')
                        ->orWhereHas('invitation', fn ($query) => $query->where('title', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('customer'), fn ($query) => $query->whereHas('invitation.user', fn ($users) => $users->where('name', 'like', '%'.$request->string('customer')->toString().'%')))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.wishes', compact('wishes', 'search'));
    }
}
