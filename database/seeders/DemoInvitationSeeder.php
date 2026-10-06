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

        $this->seedDemo(
            $customer,
            'eternal-ivory',
            'alexander-cyntia',
            'Alexander & Cyntia',
            [
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
            ],
            [
                ['guest_name' => 'Muhammad Rizky', 'attendance' => 'attending', 'guest_count' => 2],
                ['guest_name' => 'Siti Rahma', 'attendance' => 'attending', 'guest_count' => 1],
            ],
            [
                ['guest_name' => 'Muhammad Rizky', 'message' => 'Semoga menjadi keluarga yang penuh cinta dan keberkahan.'],
                ['guest_name' => 'Siti Rahma', 'message' => 'Selamat untuk kedua mempelai, lancar sampai hari H!'],
            ],
        );

        $this->seedDemo(
            $customer,
            'sweet-blossom',
            'dimas-ayu',
            'Dimas & Ayu',
            [
                'groom' => [
                    'nickname' => 'Dimas',
                    'full_name' => 'Dimas Prasetyo',
                    'father' => 'Bapak Bambang',
                    'mother' => 'Ibu Ratna',
                    'instagram' => '@dimas',
                ],
                'bride' => [
                    'nickname' => 'Ayu',
                    'full_name' => 'Ayu Lestari',
                    'father' => 'Bapak Joko',
                    'mother' => 'Ibu Dewi',
                    'instagram' => '@ayu',
                ],
                'wedding_date' => '2026-10-11',
                'quote' => 'Di antara mekarnya bunga, kami menemukan cinta yang pulang.',
                'events' => [
                    ['name' => 'Akad Nikah', 'date' => '2026-10-11', 'time' => '08:00', 'location' => 'Blossom Garden Hall', 'address' => 'Jl. Mekar Wangi No. 11, Bandung', 'maps_url' => 'https://maps.google.com'],
                    ['name' => 'Resepsi', 'date' => '2026-10-11', 'time' => '11:00', 'location' => 'Blossom Garden Hall', 'address' => 'Jl. Mekar Wangi No. 11, Bandung', 'maps_url' => 'https://maps.google.com'],
                ],
                'stories' => [
                    ['title' => 'Pertama Bertemu', 'date' => '2021-06-05', 'story' => 'Berawal dari sapaan sederhana di sebuah taman kota.'],
                    ['title' => 'Lamaran', 'date' => '2026-01-18', 'story' => 'Di bawah langit sore, kami berjanji untuk saling menjaga.'],
                ],
                'banks' => [
                    ['bank_name' => 'BRI', 'account_number' => '0987654321', 'account_name' => 'Dimas Prasetyo'],
                ],
            ],
            [
                ['guest_name' => 'Andi Pratama', 'attendance' => 'attending', 'guest_count' => 2],
                ['guest_name' => 'Nadia Putri', 'attendance' => 'maybe', 'guest_count' => 1],
            ],
            [
                ['guest_name' => 'Andi Pratama', 'message' => 'MasyaAllah, selamat Dimas & Ayu! Samawa ya!'],
                ['guest_name' => 'Nadia Putri', 'message' => 'Bahagia selalu, tidak sabar hadir di hari bahagianya!'],
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $rsvps
     * @param  array<int, array<string, mixed>>  $wishes
     */
    private function seedDemo(User $customer, string $templateKey, string $slug, string $title, array $data, array $rsvps, array $wishes): void
    {
        $template = Template::query()->where('key', $templateKey)->firstOrFail();

        $data['settings'] = [
            'countdown' => true,
            'gallery' => true,
            'story' => true,
            'gift' => true,
            'rsvp' => true,
            'wishes' => true,
            'music' => true,
        ];

        $invitation = Invitation::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'user_id' => $customer->id,
                'template_id' => $template->id,
                'title' => $title,
                'status' => Invitation::STATUS_PUBLISHED,
                'published_at' => now(),
                'data' => $data,
            ],
        );

        if (! $invitation->wasRecentlyCreated) {
            return;
        }

        foreach ($rsvps as $rsvp) {
            $invitation->rsvps()->firstOrCreate(
                ['guest_name' => $rsvp['guest_name']],
                ['attendance' => $rsvp['attendance'], 'guest_count' => $rsvp['guest_count']],
            );
        }

        foreach ($wishes as $wish) {
            $invitation->wishes()->firstOrCreate(
                ['guest_name' => $wish['guest_name']],
                ['message' => $wish['message']],
            );
        }
    }
}
