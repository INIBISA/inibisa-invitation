<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDemoInvitationRequest;
use App\Models\Invitation;
use App\Models\Template;
use App\Services\InvitationMediaManager;
use App\Support\InvitationData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DemoInvitationController extends Controller
{
    public function edit(Invitation $invitation): View
    {
        $invitation->load('media');
        $templates = Template::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.demos.edit', compact('invitation', 'templates'));
    }

    public function update(UpdateDemoInvitationRequest $request, Invitation $invitation, InvitationMediaManager $mediaManager): RedirectResponse
    {
        $invitation->update([
            'template_id' => $request->integer('template_id'),
            'title' => $request->string('title')->toString(),
            'slug' => $request->string('slug')->toString(),
            'data' => InvitationData::fromValidated($request->validated()),
        ]);

        $mediaManager->syncFromRequest($invitation, $request);

        return back()->with('success', 'Demo berhasil diperbarui.');
    }
}
