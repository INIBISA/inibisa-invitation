<?php

namespace App\Support;

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

        return $data;
    }
}
