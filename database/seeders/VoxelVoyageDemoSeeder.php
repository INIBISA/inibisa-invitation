<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateDemo;
use Illuminate\Database\Seeder;

class VoxelVoyageDemoSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::query()->where('key', 'voxel-voyage')->firstOrFail();
        $wishes = [
            ['guest_name' => 'Dito Saputra', 'message' => 'Selamat memulai level baru. Semoga selalu kompak dan bahagia!'],
            ['guest_name' => 'Alya Putri', 'message' => 'Semoga perjalanan kalian penuh warna, tawa, dan keberkahan.'],
        ];

        $demo = TemplateDemo::query()->firstOrCreate(
            ['template_id' => $template->id],
            [
                'slug' => 'nara-raka',
                'title' => 'Nara & Raka',
                'data' => [
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
                    'settings' => [
                        'countdown' => true,
                        'gallery' => true,
                        'story' => true,
                        'gift' => true,
                        'rsvp' => true,
                        'wishes' => true,
                        'music' => true,
                    ],
                    'wishes' => $wishes,
                ],
            ],
        );

        if (! array_key_exists('wishes', $demo->data)) {
            $demo->update(['data' => [...$demo->data, 'wishes' => $wishes]]);
        }
    }
}
