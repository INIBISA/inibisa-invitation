<?php

namespace Tests\Feature;

use App\Models\Template;
use Database\Seeders\VoxelVoyageTemplateSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class VoxelVoyageTemplateSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_standalone_seed_creates_template_and_working_demo(): void
    {
        $this->seed(VoxelVoyageTemplateSeeder::class);

        $this->assertDatabaseCount('templates', 1);
        $this->assertDatabaseCount('template_demos', 1);
        $this->assertDatabaseHas('templates', [
            'key' => 'voxel-voyage',
            'view_path' => 'templates.voxel-voyage.index',
            'thumbnail' => 'images/templates/voxel-voyage/thumbnail.svg',
            'price' => 150000,
            'category' => 'Playful',
            'is_active' => true,
        ]);
        $template = Template::query()->where('key', 'voxel-voyage')->firstOrFail();
        $this->assertDatabaseHas('template_demos', [
            'template_id' => $template->id,
            'slug' => 'nara-raka',
            'title' => 'Nara & Raka',
        ]);
        $this->get(route('templates.show', $template))
            ->assertOk()
            ->assertSee('Nara &amp; Raka', false);
    }

    public function test_standalone_seed_adds_missing_demo_without_overwriting_admin_edits(): void
    {
        $template = Template::factory()->create([
            'key' => 'voxel-voyage',
            'view_path' => 'templates.voxel-voyage.index',
            'price' => 175000,
        ]);

        $this->seed(VoxelVoyageTemplateSeeder::class);

        $this->assertDatabaseCount('templates', 1);
        $this->assertDatabaseCount('template_demos', 1);
        $this->assertSame(175000, $template->refresh()->price);

        $demo = $template->demo;
        $demo->update(['title' => 'Edited by admin']);

        $this->seed(VoxelVoyageTemplateSeeder::class);

        $this->assertDatabaseCount('templates', 1);
        $this->assertDatabaseCount('template_demos', 1);
        $this->assertSame('Edited by admin', $demo->refresh()->title);
    }
}
