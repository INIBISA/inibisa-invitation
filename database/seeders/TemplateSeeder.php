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
    }
}
