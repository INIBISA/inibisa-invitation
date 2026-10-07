<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\TemplateDemo;
use App\Models\User;
use Database\Seeders\DemoInvitationSeeder;
use Database\Seeders\TemplateSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDemoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_edit_all_demo_invitation_data(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->where('slug', 'alexander-cyntia')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.demos.edit', $demo))
            ->assertOk()
            ->assertSee('Ubah Demo Templat')
            ->assertSee('Contoh Ucapan Tamu');

        $payload = $this->validPayload($demo->template);
        $payload['title'] = 'Alexander & Cyntia Updated';

        $this->actingAs($admin)->put(route('admin.demos.update', $demo), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $demo->refresh();
        $this->assertSame('Alexander & Cyntia Updated', $demo->title);
        $this->assertSame('demo-updated', $demo->slug);
        $this->assertSame('Bimo', $demo->data['groom']['nickname']);
        $this->assertSame('Bapak Baru', $demo->data['groom']['father']);
        $this->assertSame('Pemberkatan', $demo->data['events'][0]['name']);
        $this->assertSame('Pertemuan Baru', $demo->data['stories'][0]['title']);
        $this->assertSame('Mandiri', $demo->data['banks'][0]['bank_name']);
        $this->assertSame('Admin Demo', $demo->data['wishes'][0]['guest_name']);
        $this->assertTrue($demo->data['settings']['story']);
        $this->assertFalse($demo->data['settings']['gift']);
        $this->assertSame('dQw4w9WgXcQ', $demo->data['music']['youtube_video_id']);
        $this->assertSame(30, $demo->data['music']['start_seconds']);

        $this->actingAs($admin)->get(route('templates.show', $demo->template))
            ->assertOk()
            ->assertSee('Admin Demo')
            ->assertSee('data-youtube-start="30"', false)
            ->assertSee('Selamat untuk kedua mempelai.');
    }

    public function test_admin_can_upload_and_remove_demo_media(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->firstOrFail();
        $payload = $this->validPayload($demo->template);
        $payload['cover'] = UploadedFile::fake()->image('cover.jpg', 900, 1200);
        $payload['gallery'] = [UploadedFile::fake()->image('gallery.jpg', 800, 600)];

        $this->actingAs($admin)->put(route('admin.demos.update', $demo), $payload)->assertRedirect();

        $demo->refresh();
        $cover = collect($demo->data['media'])->firstWhere('collection', 'cover');
        $this->assertStringEndsWith('.webp', $cover['file_path']);
        Storage::disk('public')->assertExists($cover['file_path']);
        $this->assertCount(2, $demo->data['media']);

        $payload = $this->validPayload($demo->template);
        $payload['remove_media'] = ['cover'];
        $this->actingAs($admin)->put(route('admin.demos.update', $demo), $payload)->assertRedirect();

        $demo->refresh();
        $this->assertNull(collect($demo->data['media'])->firstWhere('collection', 'cover'));
        Storage::disk('public')->assertMissing($cover['file_path']);
    }

    public function test_customer_is_forbidden_from_admin_demo_routes(): void
    {
        $customer = User::factory()->active()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->firstOrFail();

        $this->actingAs($customer)->get(route('admin.demos.edit', $demo))->assertForbidden();
        $this->actingAs($customer)->put(route('admin.demos.update', $demo), $this->validPayload($demo->template))->assertForbidden();
    }

    public function test_demo_seeder_preserves_admin_edits(): void
    {
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->where('slug', 'alexander-cyntia')->firstOrFail();
        $demo->update(['title' => 'Edited By Admin']);

        $this->seed(DemoInvitationSeeder::class);

        $this->assertSame('Edited By Admin', $demo->refresh()->title);
    }

    private function validPayload(Template $template): array
    {
        return [
            'template_id' => $template->id,
            'title' => 'Alexander & Cyntia',
            'slug' => 'demo-updated',
            'groom' => [
                'nickname' => 'Bimo',
                'full_name' => 'Bimo Alexander',
                'father' => 'Bapak Baru',
                'mother' => 'Ibu Baru',
                'instagram' => '@bimo',
            ],
            'bride' => [
                'nickname' => 'Cyntia',
                'full_name' => 'Cyntia Ayu',
                'father' => 'Bapak Cyntia',
                'mother' => 'Ibu Cyntia',
                'instagram' => '@cyntia',
            ],
            'wedding_date' => '2026-09-20',
            'quote' => 'Quote demo terbaru.',
            'events' => [[
                'name' => 'Pemberkatan',
                'date' => '2026-09-20',
                'time' => '09:00',
                'location' => 'Gedung Baru',
                'address' => 'Jalan Baru No. 1',
                'maps_url' => 'https://maps.google.com',
            ]],
            'stories' => [[
                'title' => 'Pertemuan Baru',
                'date' => '2024-01-01',
                'story' => 'Cerita demo yang dapat diubah admin.',
            ]],
            'banks' => [[
                'bank_name' => 'Mandiri',
                'account_number' => '123456789',
                'account_name' => 'Bimo Alexander',
            ]],
            'wishes' => [[
                'guest_name' => 'Admin Demo',
                'message' => 'Selamat untuk kedua mempelai.',
            ]],
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'music_start_seconds' => 30,
            'settings' => ['countdown' => 1, 'gallery' => 1, 'story' => 1, 'rsvp' => 1, 'wishes' => 1, 'music' => 1],
        ];
    }
}
