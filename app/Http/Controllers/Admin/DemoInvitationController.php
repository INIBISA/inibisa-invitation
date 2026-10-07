<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDemoInvitationRequest;
use App\Models\TemplateDemo;
use App\Services\TemplateDemoMediaManager;
use App\Support\InvitationData;
use App\Support\YouTubeVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DemoInvitationController extends Controller
{
    public function edit(TemplateDemo $demo): View
    {
        $demo->load('template');

        return view('admin.demos.edit', compact('demo'));
    }

    public function update(UpdateDemoInvitationRequest $request, TemplateDemo $demo, TemplateDemoMediaManager $mediaManager): RedirectResponse
    {
        $validated = $request->validated();
        $data = array_replace_recursive(
            InvitationData::defaults(),
            $demo->data,
            collect($validated)->only(['groom', 'bride', 'wedding_date', 'quote', 'events', 'stories', 'banks'])->all(),
        );
        $data['events'] = $validated['events'] ?? [];
        $data['stories'] = $validated['stories'] ?? [];
        $data['banks'] = $validated['banks'] ?? [];

        foreach (array_keys(InvitationData::defaults()['settings']) as $setting) {
            $data['settings'][$setting] = (bool) data_get($validated, 'settings.'.$setting, false);
        }

        $youtubeUrl = trim($validated['youtube_url'] ?? '');
        $data['music'] = [
            'youtube_url' => $youtubeUrl ?: null,
            'youtube_video_id' => YouTubeVideo::idFromUrl($youtubeUrl),
            'title' => $youtubeUrl ? 'Musik demo' : null,
        ];
        $data['wishes'] = collect($validated['wishes'] ?? [])->map(fn (array $wish): array => [
            ...$wish,
            'created_at' => now()->toISOString(),
        ])->all();
        $data = $mediaManager->syncFromRequest($demo, $request, $data);

        $demo->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'data' => $data,
        ]);

        return back()->with('success', 'Demo berhasil diperbarui.');
    }
}
