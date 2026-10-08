<?php

namespace Tests\Feature;

use App\Models\Template;
use Database\Seeders\VoxelVoyageTemplateSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class VoxelVoyageTemplateSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_standalone_seed_creates_only_voxel_voyage_and_can_be_rerun(): void
    {
        $this->seed(VoxelVoyageTemplateSeeder::class);

        $this->assertDatabaseCount('templates', 1);
        $this->assertDatabaseHas('templates', [
            'key' => 'voxel-voyage',
            'view_path' => 'templates.voxel-voyage.index',
            'thumbnail' => 'images/templates/voxel-voyage/thumbnail.svg',
            'price' => 150000,
            'category' => 'Playful',
            'is_active' => true,
        ]);

        Template::query()->where('key', 'voxel-voyage')->update(['price' => 175000]);

        $this->seed(VoxelVoyageTemplateSeeder::class);

        $this->assertDatabaseCount('templates', 1);
        $this->assertDatabaseHas('templates', ['key' => 'voxel-voyage', 'name' => 'Voxel Voyage', 'price' => 175000]);
    }
}
