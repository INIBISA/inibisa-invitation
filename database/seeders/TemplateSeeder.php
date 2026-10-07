<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        Template::query()->updateOrCreate(
            ['key' => 'eternal-ivory'],
            [
                'name' => 'Eternal Ivory',
                'view_path' => 'templates.eternal-ivory.index',
                'thumbnail' => 'images/templates/eternal-ivory/thumbnail.svg',
                'is_active' => true,
            ],
        );

        Template::query()->updateOrCreate(
            ['key' => 'sweet-blossom'],
            [
                'name' => 'Sweet Blossom',
                'view_path' => 'templates.sweet-blossom.index',
                'thumbnail' => 'images/templates/sweet-blossom/floral-banner.webp',
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
    }
}
