<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rsvp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class RsvpController extends Controller
{
    public function __invoke(Request $request): View
    {
        $statistics = [
            'total' => Rsvp::query()->count(),
            'attending' => Rsvp::query()->where('attendance', 'attending')->count(),
            'not_attending' => Rsvp::query()->where('attendance', 'not_attending')->count(),
            'guests' => Rsvp::query()->where('attendance', 'attending')->sum('guest_count'),
        ];

        return view('admin.rsvps', compact('statistics'));
    }

    public function data(Request $request): JsonResponse
    {
        $attendance = in_array($request->query('attendance'), ['attending', 'not_attending'], true)
            ? $request->string('attendance')->toString()
            : '';
        $rsvps = Rsvp::query()
            ->with(['invitation:id,user_id,title', 'invitation.user:id,name'])
            ->when($attendance !== '', fn ($query) => $query->where('attendance', $attendance));

        return DataTables::eloquent($rsvps)
            ->addIndexColumn()
            ->addColumn('invitation_title', fn (Rsvp $rsvp): string => $rsvp->invitation->title)
            ->addColumn('customer_name', fn (Rsvp $rsvp): string => $rsvp->invitation->user->name)
            ->addColumn('attendance_label', fn (Rsvp $rsvp): string => '<span class="status-badge '.($rsvp->attendance === 'attending' ? 'published' : 'inactive').'"><i></i>'.($rsvp->attendance === 'attending' ? 'Hadir' : 'Tidak hadir').'</span>')
            ->editColumn('created_at', fn (Rsvp $rsvp): string => $rsvp->created_at->translatedFormat('d M Y, H:i'))
            ->filterColumn('invitation_title', fn ($query, string $keyword) => $query->whereHas('invitation', fn ($query) => $query->where('title', 'like', "%{$keyword}%")))
            ->filterColumn('customer_name', fn ($query, string $keyword) => $query->whereHas('invitation.user', fn ($query) => $query->where('name', 'like', "%{$keyword}%")))
            ->rawColumns(['attendance_label'])
            ->toJson();
    }
}
