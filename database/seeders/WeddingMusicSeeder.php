<?php

namespace Database\Seeders;

use App\Models\WeddingMusic;
use Illuminate\Database\Seeder;

class WeddingMusicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['title' => 'Wedding Piano Songs — CrusaderBeach', 'category' => 'Piano', 'video_id' => '5DS2xhhhLd0'],
            ['title' => 'Baraka Allahu Lakuma — Maher Zain', 'category' => 'Islami', 'video_id' => 'mHpTdsBbYRM'],
            ['title' => 'Baraka Allahu Lakuma (Live & Acoustic) — Maher Zain', 'category' => 'Acoustic', 'video_id' => 'x70E03p93yE'],
        ] as $choice) {
            WeddingMusic::query()->updateOrCreate(
                ['youtube_video_id' => $choice['video_id']],
                [
                    'title' => $choice['title'],
                    'category' => $choice['category'],
                    'youtube_url' => 'https://www.youtube.com/watch?v='.$choice['video_id'],
                    'thumbnail' => 'https://i.ytimg.com/vi/'.$choice['video_id'].'/hqdefault.jpg',
                    'is_active' => true,
                ],
            );
        }
    }
}
