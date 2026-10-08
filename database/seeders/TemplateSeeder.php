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

        $sweetBlossom = Template::query()->firstOrCreate(
            ['key' => 'sweet-blossom'],
            [
                'name' => 'Sweet Blossom',
                'view_path' => 'templates.sweet-blossom.index',
                'thumbnail' => 'images/templates/sweet-blossom/thumbnail.webp',
                'price' => 150000,
                'category' => 'Floral',
                'is_active' => true,
            ],
        );

        if ($sweetBlossom->thumbnail === 'images/templates/sweet-blossom/floral-banner.webp') {
            $sweetBlossom->update(['thumbnail' => 'images/templates/sweet-blossom/thumbnail.webp']);
        }

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

        Template::query()->updateOrCreate(
            ['key' => 'rustic-forest'],
            [
                'name' => 'Rustic Forest',
                'view_path' => 'templates.rustic-forest.index',
                'thumbnail' => 'images/templates/rustic-forest/thumbnail.svg',
                'is_active' => true,
            ],
        );

        Template::query()->updateOrCreate(
            ['key' => 'modern-minimalist'],
            [
                'name' => 'Modern Minimalist',
                'view_path' => 'templates.modern-minimalist.index',
                'thumbnail' => 'images/templates/modern-minimalist/thumbnail.svg',
                'is_active' => true,
            ],
        );

        $this->call(VoxelVoyageTemplateSeeder::class);
    }
}
