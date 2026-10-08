<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        Template::query()->firstOrCreate(
            ['key' => 'eternal-ivory'],
            [
                'name' => 'Eternal Ivory',
                'view_path' => 'templates.eternal-ivory.index',
                'thumbnail' => 'images/templates/eternal-ivory/thumbnail.svg',
                'price' => 150000,
                'category' => 'Classic',
                'is_active' => true,
            ],
        );

        Template::query()->firstOrCreate(
            ['key' => 'sweet-blossom'],
            [
                'name' => 'Sweet Blossom',
                'view_path' => 'templates.sweet-blossom.index',
                'thumbnail' => 'images/templates/sweet-blossom/floral-banner.webp',
                'price' => 150000,
                'category' => 'Floral',
                'is_active' => true,
            ],
        );

        Template::query()->firstOrCreate(
            ['key' => 'midnight-nusantara'],
            [
                'name' => 'Midnight Nusantara',
                'view_path' => 'templates.midnight-nusantara.index',
                'thumbnail' => 'images/templates/midnight-nusantara/thumbnail.svg',
                'price' => 150000,
                'category' => 'Luxury',
                'is_active' => true,
            ],
        );
    }
}
