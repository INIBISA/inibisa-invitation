<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminTemplateThumbnailTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_default_template_thumbnails_use_four_by_three_dimensions(): void
    {
        $sweetBlossom = new \Imagick(public_path('images/templates/sweet-blossom/thumbnail.webp'));
        $midnightNusantara = simplexml_load_file(public_path('images/templates/midnight-nusantara/thumbnail.svg'));

        $this->assertSame(1200, $sweetBlossom->getImageWidth());
        $this->assertSame(900, $sweetBlossom->getImageHeight());
        $this->assertSame('1200', (string) $midnightNusantara['width']);
        $this->assertSame('900', (string) $midnightNusantara['height']);
    }

    public function test_admin_template_settings_offer_thumbnail_upload(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();

        $this->actingAs($admin)->get(route('admin.templates.index'))
            ->assertSee('Thumbnail Paket')
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee(asset($template->thumbnail));
    }

    public function test_admin_can_upload_a_thumbnail_seen_in_customer_catalog(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();
        $payload = $this->templatePayload($template);
        $payload['thumbnail'] = UploadedFile::fake()->image('package.jpg', 1600, 1000);

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $template->refresh();
        $this->assertStringStartsWith('storage/template-thumbnails/'.$template->id.'/', $template->thumbnail);
        $this->assertStringEndsWith('.webp', $template->thumbnail);
        Storage::disk('public')->assertExists(Str::after($template->thumbnail, 'storage/'));

        $customer = User::factory()->active()->create();
        $this->actingAs($customer)->get(route('templates.index'))->assertSee(asset($template->thumbnail));
    }

    public function test_new_thumbnail_replaces_the_previous_uploaded_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();
        $oldPath = 'template-thumbnails/'.$template->id.'/old.webp';
        Storage::disk('public')->put($oldPath, 'old thumbnail');
        $template->update(['thumbnail' => 'storage/'.$oldPath]);
        $payload = $this->templatePayload($template);
        $payload['thumbnail'] = UploadedFile::fake()->image('replacement.png', 640, 400);

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), $payload)->assertRedirect();

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists(Str::after($template->refresh()->thumbnail, 'storage/'));
    }

    public function test_editing_details_without_a_file_keeps_the_thumbnail(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();
        $originalThumbnail = $template->thumbnail;
        $payload = $this->templatePayload($template);
        $payload['name'] = 'Nama Baru';

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), $payload)->assertRedirect();

        $this->assertSame($originalThumbnail, $template->refresh()->thumbnail);
        $this->assertSame('Nama Baru', $template->name);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();
        $originalThumbnail = $template->thumbnail;
        $payload = $this->templatePayload($template);
        $payload['thumbnail'] = UploadedFile::fake()->create('document.txt', 10, 'text/plain');

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), $payload)
            ->assertSessionHasErrors('thumbnail');

        $this->assertSame($originalThumbnail, $template->refresh()->thumbnail);
        $this->assertSame([], Storage::disk('public')->allFiles('template-thumbnails'));
    }

    public function test_oversized_thumbnail_is_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();
        $payload = $this->templatePayload($template);
        $payload['thumbnail'] = UploadedFile::fake()->image('large.jpg')->size(6000);

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), $payload)
            ->assertSessionHasErrors('thumbnail');

        $this->assertSame('images/templates/eternal-ivory/thumbnail.svg', $template->refresh()->thumbnail);
        $this->assertSame([], Storage::disk('public')->allFiles('template-thumbnails'));
    }

    public function test_customer_cannot_replace_a_template_thumbnail(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();

        $this->actingAs($customer)->patch(route('admin.templates.update', $template), $this->templatePayload($template))
            ->assertForbidden();

        $this->assertSame('images/templates/eternal-ivory/thumbnail.svg', $template->refresh()->thumbnail);
    }

    private function templatePayload(Template $template): array
    {
        return [
            'name' => $template->name,
            'category' => 'Wedding',
            'price' => 150000,
            'is_active' => 1,
        ];
    }
}
