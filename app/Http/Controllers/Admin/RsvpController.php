<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RsvpController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $attendance = $request->string('attendance')->toString();

        if (! in_array($attendance, ['attending', 'not_attending'], true)) {
            $attendance = '';
        }

        $rsvps = Rsvp::query()
            ->with(['invitation:id,user_id,title,slug', 'invitation.user:id,name'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('guest_name', 'like', '%'.$search.'%')
                        ->orWhereHas('invitation', fn ($query) => $query->where('title', 'like', '%'.$search.'%'));
                });
            })
            ->when($attendance !== '', fn ($query) => $query->where('attendance', $attendance))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        $statistics = [
            'total' => Rsvp::query()->count(),
            'attending' => Rsvp::query()->where('attendance', 'attending')->count(),
            'not_attending' => Rsvp::query()->where('attendance', 'not_attending')->count(),
            'guests' => Rsvp::query()->where('attendance', 'attending')->sum('guest_count'),
        ];

        return view('admin.rsvps', compact('rsvps', 'statistics', 'search', 'attendance'));
    }
}
