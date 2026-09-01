<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishRequest;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;

class PublicWishController extends Controller
{
    public function store(StoreWishRequest $request, Invitation $invitation): RedirectResponse
    {
        $invitation->wishes()->create($request->safe()->only(['guest_name', 'message']));

        return back()->with('wish_success', 'Ucapan Anda telah dikirim.');
    }
}
