<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoInvitationSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::query()->updateOrCreate(
            ['email' => 'alexander@example.com'],
            [
                'name' => 'Alexander',
                'password' => 'password',
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_ACTIVE,
            ],
        );

        $template = Template::query()->where('key', 'eternal-ivory')->firstOrFail();

        $invitation = Invitation::query()->updateOrCreate(
            ['slug' => 'alexander-cyntia'],
            [
                'user_id' => $customer->id,
                'template_id' => $template->id,
                'title' => 'Alexander & Cyntia',
                'status' => Invitation::STATUS_PUBLISHED,
                'published_at' => now(),
                'data' => [
                    'groom' => [
                        'nickname' => 'Alexander',
                        'full_name' => 'Alexander',
                        'father' => 'Bapak Ahmad',
                        'mother' => 'Ibu Nurhayati',
                        'instagram' => '@alexander',
                    ],
                    'bride' => [
                        'nickname' => 'Cyntia',
                        'full_name' => 'Cyntia',
                        'father' => 'Bapak Hendra',
                        'mother' => 'Ibu Siti',
                        'instagram' => '@cyntia',
                    ],
                    'wedding_date' => '2026-09-20',
                    'quote' => 'Dua jiwa, satu perjalanan, dan cinta yang tumbuh selamanya.',
                    'events' => [
                        ['name' => 'Akad Nikah', 'date' => '2026-09-20', 'time' => '08:00', 'location' => 'The Ivory Hall', 'address' => 'Jl. Kebahagiaan No. 20, Jakarta', 'maps_url' => 'https://maps.google.com'],
                        ['name' => 'Resepsi', 'date' => '2026-09-20', 'time' => '11:00', 'location' => 'The Ivory Hall', 'address' => 'Jl. Kebahagiaan No. 20, Jakarta', 'maps_url' => 'https://maps.google.com'],
                    ],
                    'stories' => [
                        ['title' => 'Pertama Bertemu', 'date' => '2022-03-12', 'story' => 'Sebuah pertemuan sederhana yang perlahan menjadi rumah.'],
                        ['title' => 'Lamaran', 'date' => '2025-12-20', 'story' => 'Kami memilih melanjutkan perjalanan ini bersama.'],
                    ],
                    'banks' => [
                        ['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'Alexander'],
                    ],
                    'settings' => [
                        'countdown' => true,
                        'gallery' => true,
                        'story' => true,
                        'gift' => true,
                        'rsvp' => true,
                        'wishes' => true,
                        'music' => true,
                    ],
                ],
            ],
        );

        $invitation->rsvps()->updateOrCreate(
            ['guest_name' => 'Muhammad Rizky'],
            ['attendance' => 'attending', 'guest_count' => 2],
        );
        $invitation->wishes()->updateOrCreate(
            ['guest_name' => 'Muhammad Rizky'],
            ['message' => 'Semoga menjadi keluarga yang penuh cinta dan keberkahan.'],
        );
    }
}
