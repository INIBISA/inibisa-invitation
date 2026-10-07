<?php

namespace App\Support;

use App\Models\WeddingMusic;
use Illuminate\Support\Arr;

class InvitationData
{
    public static function defaults(): array
    {
        return [
            'groom' => ['nickname' => '', 'full_name' => '', 'father' => '', 'mother' => '', 'instagram' => ''],
            'bride' => ['nickname' => '', 'full_name' => '', 'father' => '', 'mother' => '', 'instagram' => ''],
            'wedding_date' => '',
            'quote' => '',
            'events' => [],
            'stories' => [],
            'banks' => [],
            'music' => ['youtube_url' => null, 'youtube_video_id' => null, 'title' => null],
            'settings' => [
                'countdown' => true,
                'gallery' => true,
                'story' => true,
                'gift' => true,
                'rsvp' => true,
                'wishes' => true,
                'music' => true,
            ],
        ];
    }

    public static function fromValidated(array $validated): array
    {
        $data = array_replace_recursive(
            self::defaults(),
            Arr::only($validated, ['groom', 'bride', 'wedding_date', 'quote', 'events', 'stories', 'banks']),
        );

        foreach (array_keys(self::defaults()['settings']) as $setting) {
            $data['settings'][$setting] = (bool) Arr::get($validated, 'settings.'.$setting, false);
        }

        $music = isset($validated['wedding_music_id'])
            ? WeddingMusic::query()->where('is_active', true)->find($validated['wedding_music_id'])
            : null;
        $url = trim($validated['youtube_url'] ?? '');

        if ($url !== '') {
            $data['music'] = ['youtube_url' => $url, 'youtube_video_id' => YouTubeVideo::idFromUrl($url), 'title' => 'Musik pilihan Anda'];
        } elseif ($music) {
            $data['music'] = ['youtube_url' => $music->youtube_url, 'youtube_video_id' => $music->youtube_video_id, 'title' => $music->title];
        }

        return $data;
    }
}
