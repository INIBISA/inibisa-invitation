<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PublicRsvpController extends Controller
{
    public function store(StoreRsvpRequest $request, Invitation $invitation): RedirectResponse|JsonResponse
    {
        $invitation->rsvps()->create([
            'guest_name' => $request->string('guest_name')->toString(),
            'attendance' => $request->string('attendance')->toString(),
            'guest_count' => $request->string('attendance')->toString() === 'attending'
                ? $request->integer('guest_count')
                : 0,
        ]);

        $message = 'Konfirmasi kehadiran Anda telah tersimpan.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('rsvp_success', $message);
    }
}
