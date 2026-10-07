<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDemoInvitationRequest;
use App\Models\TemplateDemo;
use App\Models\WeddingMusic;
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
        $musicChoices = WeddingMusic::query()->where('is_active', true)->orderBy('category')->orderBy('title')->get();

        return view('admin.demos.edit', compact('demo', 'musicChoices'));
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
        $music = $youtubeUrl === '' && isset($validated['wedding_music_id'])
            ? WeddingMusic::query()->where('is_active', true)->find($validated['wedding_music_id'])
            : null;
        $data['music'] = [
            'youtube_url' => $youtubeUrl ?: $music?->youtube_url,
            'youtube_video_id' => $youtubeUrl ? YouTubeVideo::idFromUrl($youtubeUrl) : $music?->youtube_video_id,
            'title' => $youtubeUrl ? 'Musik demo' : $music?->title,
            'catalog_music_id' => $music?->id,
            'start_seconds' => (int) ($validated['music_start_seconds'] ?? 0),
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
