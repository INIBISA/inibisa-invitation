<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\Wish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvitationResponseController extends Controller
{
    public function rsvps(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);

        return view('invitations.rsvps', compact('invitation'));
    }

    public function rsvpData(Request $request, Invitation $invitation): JsonResponse
    {
        Gate::authorize('view', $invitation);
        $rsvps = $invitation->rsvps()
            ->when(in_array($request->query('attendance'), ['attending', 'not_attending'], true), fn ($query) => $query->where('attendance', $request->query('attendance')));

        return DataTables::eloquent($rsvps)
            ->addIndexColumn()
            ->addColumn('attendance_label', fn (Rsvp $rsvp): string => '<span class="status-badge '.($rsvp->attendance === 'attending' ? 'published' : 'inactive').'"><i></i>'.($rsvp->attendance === 'attending' ? 'Hadir' : 'Tidak hadir').'</span>')
            ->editColumn('created_at', fn (Rsvp $rsvp): string => $rsvp->created_at->translatedFormat('d M Y H:i'))
            ->rawColumns(['attendance_label'])
            ->toJson();
    }

    public function wishes(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);

        return view('invitations.wishes', compact('invitation'));
    }

    public function wishData(Invitation $invitation): JsonResponse
    {
        Gate::authorize('view', $invitation);

        return DataTables::eloquent($invitation->wishes())
            ->addIndexColumn()
            ->editColumn('created_at', fn (Wish $wish): string => $wish->created_at->translatedFormat('d M Y H:i'))
            ->toJson();
    }
}
