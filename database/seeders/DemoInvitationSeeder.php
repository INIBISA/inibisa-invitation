<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateDemo;
use Illuminate\Database\Seeder;

class DemoInvitationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDemo(
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

        $this->seedDemo(
            'midnight-nusantara',
            'aruna-bima',
            'Aruna & Bima',
            [
                'groom' => [
                    'nickname' => 'Bima',
                    'full_name' => 'Bima Adinata',
                    'father' => 'Bapak Surya Adinata',
                    'mother' => 'Ibu Larasati',
                    'instagram' => '@bimaadinata',
                ],
                'bride' => [
                    'nickname' => 'Aruna',
                    'full_name' => 'Aruna Sekar',
                    'father' => 'Bapak Bagus Pranata',
                    'mother' => 'Ibu Ratih Sekar',
                    'instagram' => '@arunasekar',
                ],
                'wedding_date' => '2026-10-18',
                'quote' => 'Di bawah langit yang sama, dua perjalanan menemukan satu tujuan.',
                'events' => [
                    ['name' => 'Akad Nikah', 'date' => '2026-10-18', 'time' => '08:00', 'location' => 'Pendopo Agung', 'address' => 'Jl. Pusaka No. 18, Yogyakarta', 'maps_url' => 'https://maps.google.com'],
                    ['name' => 'Resepsi', 'date' => '2026-10-18', 'time' => '19:00', 'location' => 'Pendopo Agung', 'address' => 'Jl. Pusaka No. 18, Yogyakarta', 'maps_url' => 'https://maps.google.com'],
                ],
                'stories' => [
                    ['title' => 'Awal Cerita', 'date' => '2022-08-14', 'story' => 'Percakapan singkat menjelma perjalanan yang ingin kami jaga selamanya.'],
                    ['title' => 'Satu Tujuan', 'date' => '2026-02-08', 'story' => 'Di hadapan keluarga, kami memilih melangkah menuju masa depan bersama.'],
                ],
                'banks' => [
                    ['bank_name' => 'Mandiri', 'account_number' => '1357902468', 'account_name' => 'Bima Adinata'],
                ],
            ],
            [
                ['guest_name' => 'Raka Pratama', 'attendance' => 'attending', 'guest_count' => 2],
                ['guest_name' => 'Sekar Ayuningtyas', 'attendance' => 'attending', 'guest_count' => 1],
            ],
            [
                ['guest_name' => 'Raka Pratama', 'message' => 'Semoga perjalanan baru kalian selalu hangat dan penuh berkah.'],
                ['guest_name' => 'Sekar Ayuningtyas', 'message' => 'Selamat Aruna dan Bima, bahagia hingga selamanya!'],
            ],
        );

        $this->seedDemo(
            'voxel-voyage',
            'nara-raka',
            'Nara & Raka',
            [
                'groom' => [
                    'nickname' => 'Raka',
                    'full_name' => 'Raka Mahendra',
                    'father' => 'Bapak Damar Mahendra',
                    'mother' => 'Ibu Kirana',
                    'instagram' => '@rakamahendra',
                ],
                'bride' => [
                    'nickname' => 'Nara',
                    'full_name' => 'Nara Anindya',
                    'father' => 'Bapak Arif Anindya',
                    'mother' => 'Ibu Maya',
                    'instagram' => '@naraanindya',
                ],
                'wedding_date' => '2026-11-18',
                'quote' => 'Dua pemain, satu dunia, dan petualangan yang akan kami bangun bersama.',
                'events' => [
                    ['name' => 'Akad Nikah', 'date' => '2026-11-18', 'time' => '08:00', 'location' => 'Pixel Garden Hall', 'address' => 'Jl. Petualangan No. 18, Surabaya', 'maps_url' => 'https://maps.google.com'],
                    ['name' => 'Resepsi', 'date' => '2026-11-18', 'time' => '11:00', 'location' => 'Pixel Garden Hall', 'address' => 'Jl. Petualangan No. 18, Surabaya', 'maps_url' => 'https://maps.google.com'],
                ],
                'stories' => [
                    ['title' => 'First Spawn', 'date' => '2022-05-08', 'story' => 'Sebuah pertemuan sederhana membuka dunia baru yang ingin kami jelajahi bersama.'],
                    ['title' => 'Next Level', 'date' => '2026-03-14', 'story' => 'Kami memilih melanjutkan permainan kehidupan sebagai satu tim untuk selamanya.'],
                ],
                'banks' => [
                    ['bank_name' => 'BCA', 'account_number' => '2468135790', 'account_name' => 'Nara Anindya'],
                ],
            ],
            [
                ['guest_name' => 'Dito Saputra', 'attendance' => 'attending', 'guest_count' => 2],
                ['guest_name' => 'Alya Putri', 'attendance' => 'attending', 'guest_count' => 1],
            ],
            [
                ['guest_name' => 'Dito Saputra', 'message' => 'Selamat memulai level baru. Semoga selalu kompak dan bahagia!'],
                ['guest_name' => 'Alya Putri', 'message' => 'Semoga perjalanan kalian penuh warna, tawa, dan keberkahan.'],
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $rsvps
     * @param  array<int, array<string, mixed>>  $wishes
     */
    private function seedDemo(string $templateKey, string $slug, string $title, array $data, array $rsvps, array $wishes): void
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
        $data['wishes'] = $wishes;

        $demo = TemplateDemo::query()->firstOrCreate(
            ['template_id' => $template->id],
            ['slug' => $slug, 'title' => $title, 'data' => $data],
        );

        if (! array_key_exists('wishes', $demo->data)) {
            $demo->update(['data' => [...$demo->data, 'wishes' => $wishes]]);
        }
    }
}
