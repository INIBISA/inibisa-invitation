<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __invoke(): View
    {
        $invitations = Invitation::query()->with(['user:id,name,email', 'template:id,name'])->latest('id')->paginate(30);

        return view('admin.invitations', compact('invitations'));
    }
}
