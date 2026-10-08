<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GuestController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('admin.guests.index');
    }

    public function data(Request $request): JsonResponse
    {
        $guests = Guest::query()->with(['invitation.user:id,name', 'invitation:id,user_id,title'])
            ->when($request->filled('customer'), fn ($query) => $query->whereHas('invitation.user', fn ($users) => $users->where('name', 'like', '%'.$request->string('customer')->toString().'%')))
            ->when($request->filled('invitation'), fn ($query) => $query->whereHas('invitation', fn ($invitations) => $invitations->where('title', 'like', '%'.$request->string('invitation')->toString().'%')));

        return DataTables::eloquent($guests)
            ->addIndexColumn()
            ->addColumn('delivery', fn (Guest $guest): string => '<span class="status-badge '.($guest->sent_at ? 'published' : 'draft').'"><i></i>'.($guest->sent_at ? 'Terkirim' : 'Belum dikirim').'</span>')
            ->addColumn('invitation_title', fn (Guest $guest): string => $guest->invitation->title)
            ->addColumn('customer_name', fn (Guest $guest): string => $guest->invitation->user->name)
            ->filterColumn('invitation_title', fn ($query, string $keyword) => $query->whereHas('invitation', fn ($query) => $query->where('title', 'like', "%{$keyword}%")))
            ->filterColumn('customer_name', fn ($query, string $keyword) => $query->whereHas('invitation.user', fn ($query) => $query->where('name', 'like', "%{$keyword}%")))
            ->rawColumns(['delivery'])
            ->toJson();
    }
}
