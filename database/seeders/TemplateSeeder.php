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
                'description' => 'Nuansa ivory yang tenang dengan serif anggun dan ornamen floral klasik.',
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
                'description' => 'Nuansa blush romantis dengan bunga sakura yang hangat dan manis.',
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
                'description' => 'Nuansa biru malam mewah dengan aksen emas dan geometri Nusantara.',
                'is_active' => true,
            ],
        );

        Template::query()->firstOrCreate(
            ['key' => 'rustic-forest'],
            [
                'name' => 'Rustic Forest',
                'view_path' => 'templates.rustic-forest.index',
                'thumbnail' => 'images/templates/rustic-forest/thumbnail.svg',
                'price' => 150000,
                'category' => 'Rustic',
                'description' => 'Nuansa kayu dan dedaunan hangat untuk pernikahan outdoor yang intim.',
                'is_active' => true,
            ],
        );

        Template::query()->firstOrCreate(
            ['key' => 'modern-minimalist'],
            [
                'name' => 'Modern Minimalist',
                'view_path' => 'templates.modern-minimalist.index',
                'thumbnail' => 'images/templates/modern-minimalist/thumbnail.svg',
                'price' => 150000,
                'category' => 'Minimalist',
                'description' => 'Desain bersih dengan ruang lapang, fokus pada tipografi dan informasi acara.',
                'is_active' => true,
            ],
        );

        $this->call(VoxelVoyageTemplateSeeder::class);
    }
}
