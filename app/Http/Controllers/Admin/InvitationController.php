<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvitationController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.invitations');
    }

    public function data(): JsonResponse
    {
        $invitations = Invitation::query()->with(['user:id,name,email', 'template:id,name']);

        return DataTables::eloquent($invitations)
            ->addIndexColumn()
            ->addColumn('invitation', fn (Invitation $invitation): string => '<strong>'.e($invitation->title).'</strong><small>'.e($invitation->slug).'</small>')
            ->addColumn('customer', fn (Invitation $invitation): string => '<strong>'.e($invitation->user->name).'</strong><small>'.e($invitation->user->email).'</small>')
            ->addColumn('template_name', fn (Invitation $invitation): string => $invitation->template->name)
            ->addColumn('status_label', fn (Invitation $invitation): string => '<span class="status-badge '.e($invitation->status).'"><i></i>'.e($invitation->statusLabel()).'</span>')
            ->addColumn('link', fn (Invitation $invitation): string => $invitation->status === Invitation::STATUS_PUBLISHED ? '<a class="button secondary small" href="'.e(route('public.invitation', $invitation->slug)).'" target="_blank" rel="noopener">Buka</a>' : '<span class="muted">Belum publik</span>')
            ->filterColumn('invitation', fn ($query, string $keyword) => $query->where(fn ($query) => $query->where('title', 'like', "%{$keyword}%")->orWhere('slug', 'like', "%{$keyword}%")))
            ->filterColumn('customer', fn ($query, string $keyword) => $query->whereHas('user', fn ($query) => $query->where('name', 'like', "%{$keyword}%")->orWhere('email', 'like', "%{$keyword}%")))
            ->filterColumn('template_name', fn ($query, string $keyword) => $query->whereHas('template', fn ($query) => $query->where('name', 'like', "%{$keyword}%")))
            ->rawColumns(['invitation', 'customer', 'status_label', 'link'])
            ->toJson();
    }
}
