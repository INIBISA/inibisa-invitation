<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicInvitationController extends Controller
{
    public function __invoke(Request $request, string $slug): View
    {
        $invitation = Invitation::query()
            ->with(['template', 'media', 'wishes' => fn ($query) => $query->latest()->limit(20)])
            ->where('slug', $slug)
            ->where('status', Invitation::STATUS_PUBLISHED)
            ->whereHas('template', fn ($query) => $query->where('is_active', true))
            ->firstOrFail();

        $guestName = Str::limit($request->string('to', 'Tamu Undangan')->trim()->toString(), 100, '');
        $isPreview = false;

        return view($invitation->template->view_path, compact('invitation', 'guestName', 'isPreview'));
    }
}
