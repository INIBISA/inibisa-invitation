<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvitationPreviewController extends Controller
{
    public function __invoke(Request $request, Invitation $invitation): View
    {
        Gate::authorize('view', $invitation);
        $invitation->load(['template', 'media', 'wishes' => fn ($query) => $query->latest()->limit(20)]);
        $guestName = Str::limit($request->string('to', 'Tamu Undangan')->trim()->toString(), 100, '');
        $isPreview = true;

        return view($invitation->template->view_path, compact('invitation', 'guestName', 'isPreview'));
    }
}
