<?php

namespace Tests\Feature;

use App\Models\Template;
use Database\Seeders\TemplateSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TemplateSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_rustic_and_minimalist_templates_have_purchase_details(): void
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
