<?php

namespace Tests\Feature;

use App\Models\Template;
use Database\Seeders\TemplateSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TemplateSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_restored_templates_are_seeded_active_with_purchase_details(): void
    {
        $this->seed(TemplateSeeder::class);

        $this->assertDatabaseHas('templates', [
            'key' => 'rustic-forest',
            'price' => 150000,
            'category' => 'Rustic',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('templates', [
            'key' => 'modern-minimalist',
            'price' => 150000,
            'category' => 'Minimalist',
            'is_active' => true,
        ]);
    }

    public function test_every_active_seeded_template_has_a_view(): void
    {
        $this->seed(TemplateSeeder::class);

        Template::query()->where('is_active', true)->each(function (Template $template): void {
            $this->assertTrue(view()->exists($template->view_path), $template->key.' has no view.');
        });
    }

    public function test_restored_template_thumbnails_exist_and_use_four_by_three_dimensions(): void
    {
        foreach (['rustic-forest', 'modern-minimalist'] as $template) {
            $thumbnail = simplexml_load_file(public_path("images/templates/{$template}/thumbnail.svg"));

            $this->assertSame('1200', (string) $thumbnail['width']);
            $this->assertSame('900', (string) $thumbnail['height']);
        }
    }

    public function test_reseeding_does_not_overwrite_custom_template_price(): void
    {
        $template = Template::factory()->create([
            'key' => 'rustic-forest',
            'price' => 175000,
            'category' => 'Custom',
        ]);

        $this->seed(TemplateSeeder::class);

        $this->assertSame(175000, $template->refresh()->price);
        $this->assertSame('Custom', $template->category);
    }
}
