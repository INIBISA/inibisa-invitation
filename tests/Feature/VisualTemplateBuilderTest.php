<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Template;
use App\Models\User;
use App\Support\InvitationData;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisualTemplateBuilderTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_edit_and_publish_a_visual_template(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.templates.store'), [
            'name' => 'Garden Romance', 'category' => 'Floral', 'price' => 175000,
            'description' => 'Taman bunga bernuansa romantis.',
        ])->assertRedirect()->assertSessionHas('success');

        $template = Template::query()->where('name', 'Garden Romance')->firstOrFail();
        $this->assertTrue($template->isVisual());
        $this->assertSame('Taman bunga bernuansa romantis.', $template->description);
        $this->assertFalse($template->is_active);
        $this->assertNotNull($template->demo);

        $this->actingAs($admin)->get(route('admin.templates.canvas.edit', $template))
            ->assertOk()->assertSee('Studio Desain')->assertSee('Simpan Draft');

        $design = $template->draftDesignSettings();
        $design['canvas']['cover']['background'] = '#faf0e6';
        $design['canvas']['cover']['content_x'] = 20;
        $design['canvas']['cover']['content_width'] = 60;
        $design['canvas']['cover']['elements'][] = [
            'id' => 'ornament-1', 'kind' => 'shape', 'x' => 10, 'y' => 20,
            'width' => 25, 'height' => 20, 'font_size' => 32, 'rotation' => 0,
            'opacity' => 0.7, 'color' => '#b8965b', 'content' => '',
            'image' => '', 'shape' => 'circle',
        ];

        $this->actingAs($admin)->put(route('admin.templates.canvas.update', $template), [
            'payload' => json_encode($design),
        ])->assertRedirect()->assertSessionHas('success');

        $template->refresh();
        $this->assertSame('#faf0e6', $template->design_draft_settings['canvas']['cover']['background']);
        $this->assertSame(20, $template->design_draft_settings['canvas']['cover']['content_x']);
        $this->assertFalse($template->is_active);

        $this->actingAs($admin)->get(route('admin.templates.design-preview', $template))
            ->assertOk()->assertSee('visual-shape-circle')->assertSee('Arga');

        $this->actingAs($admin)->post(route('admin.templates.design-publication.store', $template))
            ->assertRedirect()->assertSessionHas('success');

        $template->refresh();
        $this->assertTrue($template->is_active);
        $this->assertNull($template->design_draft_settings);
        $this->get(route('templates.show', $template))->assertOk()->assertSee('visual-shape-circle');
    }

    public function test_customer_cannot_create_or_edit_visual_templates(): void
    {
        $customer = User::factory()->create();
        $template = Template::factory()->create(['view_path' => 'templates.visual.index']);

        $this->actingAs($customer)->get(route('admin.templates.create'))->assertForbidden();
        $this->actingAs($customer)->post(route('admin.templates.store'), [
            'name' => 'Hidden', 'category' => 'Wedding', 'price' => 150000,
        ])->assertForbidden();
        $this->actingAs($customer)->get(route('admin.templates.canvas.edit', $template))->assertForbidden();
        $this->actingAs($customer)->put(route('admin.templates.canvas.update', $template), [])->assertForbidden();
    }

    public function test_canvas_rejects_unsafe_image_paths(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['view_path' => 'templates.visual.index']);
        $design = $template->draftDesignSettings();
        $design['canvas']['cover']['elements'][] = [
            'id' => 'unsafe-image', 'kind' => 'image', 'x' => 1, 'y' => 2,
            'width' => 20, 'height' => 20, 'font_size' => 32, 'rotation' => 0,
            'opacity' => 1, 'color' => '#b8965b', 'content' => '',
            'image' => 'https://example.com/tracker.png', 'shape' => 'rectangle',
        ];

        $this->actingAs($admin)->put(route('admin.templates.canvas.update', $template), [
            'payload' => json_encode($design),
        ])->assertInvalid(['canvas.cover.elements.0.image']);

        $this->assertNull($template->refresh()->design_draft_settings);
    }

    public function test_admin_can_upload_a_canvas_ornament_as_a_webp_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['view_path' => 'templates.visual.index']);

        $response = $this->actingAs($admin)->postJson(route('admin.templates.canvas.images.store', $template), [
            'image' => UploadedFile::fake()->image('ornament.png', 120, 120),
        ])->assertOk()->assertJsonStructure(['path', 'url']);

        $path = $response->json('path');
        $this->assertStringStartsWith('storage/template-canvas/'.$template->id.'/', $path);
        Storage::disk('public')->assertExists(substr($path, strlen('storage/')));
    }

    public function test_published_visual_invitation_uses_customer_data_and_live_forms(): void
    {
        $template = Template::factory()->create(['view_path' => 'templates.visual.index']);
        $data = InvitationData::defaults();
        $data['groom']['nickname'] = 'Bima';
        $data['bride']['nickname'] = 'Ayu';
        $data['wedding_date'] = '2027-01-17';
        $invitation = Invitation::factory()->published()->create([
            'template_id' => $template->id,
            'data' => $data,
        ]);

        $this->get(route('public.invitation', ['slug' => $invitation->slug, 'to' => 'Rani']))
            ->assertOk()
            ->assertSee('Bima')
            ->assertSee('Ayu')
            ->assertSee('Rani')
            ->assertSee(route('public.rsvp', $invitation), false)
            ->assertSee(route('public.wishes', $invitation), false);
    }
}
