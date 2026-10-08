<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishRequest;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PublicWishController extends Controller
{
    public function store(StoreWishRequest $request, Invitation $invitation): RedirectResponse|JsonResponse
    {
        $wish = $invitation->wishes()->create($request->safe()->only(['guest_name', 'message']));
        $message = 'Ucapan Anda telah dikirim.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'data' => [
                    'guest_name' => $wish->guest_name,
                    'message' => $wish->message,
                    'created_at' => $wish->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('wish_success', $message);
    }
}
