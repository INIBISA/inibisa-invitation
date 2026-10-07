<?php

namespace Database\Factories;

use App\Models\WeddingMusic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeddingMusic>
 */
class WeddingMusicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $videoId = 'jfKfPfyJRdk';

        return [
            'title' => fake()->words(3, true),
            'category' => 'Instrumental',
            'youtube_url' => 'https://www.youtube.com/watch?v='.$videoId,
            'youtube_video_id' => $videoId,
            'thumbnail' => 'https://i.ytimg.com/vi/'.$videoId.'/hqdefault.jpg',
            'is_active' => true,
        ];
    }
}
