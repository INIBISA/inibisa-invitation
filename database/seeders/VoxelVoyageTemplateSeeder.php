<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class VoxelVoyageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        Template::query()->firstOrCreate(
            ['key' => 'voxel-voyage'],
            [
                'name' => 'Voxel Voyage',
                'view_path' => 'templates.voxel-voyage.index',
                'thumbnail' => 'images/templates/voxel-voyage/thumbnail.svg',
                'price' => 150000,
                'category' => 'Playful',
                'is_active' => true,
            ],
        );

        $this->call(VoxelVoyageDemoSeeder::class);
    }
}
