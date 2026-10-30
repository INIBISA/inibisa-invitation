<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\TemplateDemo;
use App\Models\User;
use App\Models\WeddingMusic;
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

    public function test_demo_editor_shows_only_active_catalog_music(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->firstOrFail();
        $activeMusic = WeddingMusic::factory()->create(['title' => 'Musik aktif']);
        WeddingMusic::factory()->create(['title' => 'Musik nonaktif', 'is_active' => false]);

        $this->actingAs($admin)->get(route('admin.demos.edit', $demo))
            ->assertOk()
            ->assertSee($activeMusic->title)
            ->assertDontSee('Musik nonaktif');
    }

    public function test_admin_can_choose_catalog_music_for_demo(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->firstOrFail();
        $music = WeddingMusic::factory()->create([
            'title' => 'Musik pilihan demo',
            'youtube_url' => 'https://www.youtube.com/watch?v=laMRBmD2aeg',
            'youtube_video_id' => 'laMRBmD2aeg',
        ]);
        $payload = $this->validPayload($demo->template);
        $payload['youtube_url'] = '';
        $payload['wedding_music_id'] = $music->id;

        $this->actingAs($admin)->put(route('admin.demos.update', $demo), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $demo->refresh();
        $this->assertSame($music->id, $demo->data['music']['catalog_music_id']);
        $this->assertSame('laMRBmD2aeg', $demo->data['music']['youtube_video_id']);
        $this->assertSame('Musik pilihan demo', $demo->data['music']['title']);
        $this->assertSame(30, $demo->data['music']['start_seconds']);

        $this->actingAs($admin)->get(route('admin.demos.edit', $demo))
            ->assertSee('value="'.$music->id.'" checked', false)
            ->assertDontSee('name="youtube_url" type="url" value="'.$music->youtube_url.'"', false);

        $this->get(route('templates.show', $demo->template))
            ->assertSee('data-youtube-music="laMRBmD2aeg"', false);
    }

    public function test_admin_cannot_choose_inactive_catalog_music_for_demo(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->firstOrFail();
        $music = WeddingMusic::factory()->create(['is_active' => false]);
        $payload = $this->validPayload($demo->template);
        $payload['youtube_url'] = '';
        $payload['wedding_music_id'] = $music->id;

        $this->actingAs($admin)->put(route('admin.demos.update', $demo), $payload)
            ->assertSessionHasErrors('wedding_music_id');

        $this->assertNotSame($music->youtube_video_id, $demo->refresh()->data['music']['youtube_video_id'] ?? null);
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

    public function test_admin_uploads_demo_photos_individually_and_form_save_preserves_them(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->firstOrFail();
        $url = route('admin.demos.media.store', $demo);

        $this->actingAs($admin)->post($url, [
            'collection' => 'gallery',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('first.jpg', 900, 1200),
        ], ['Accept' => 'application/json'])->assertOk();
        $this->actingAs($admin)->post($url, [
            'collection' => 'gallery',
            'replace' => '0',
            'file' => UploadedFile::fake()->image('second.jpg', 900, 1200),
        ], ['Accept' => 'application/json'])->assertOk();

        $photos = collect($demo->refresh()->data['media'])->where('collection', 'gallery')->values();
        $this->assertCount(2, $photos);
        $this->assertSame([0, 1], $photos->pluck('sort_order')->all());
        Storage::disk('public')->assertExists($photos->pluck('file_path')->all());

        $this->actingAs($admin)->put(route('admin.demos.update', $demo), $this->validPayload($demo->template))->assertRedirect();

        $this->assertCount(2, collect($demo->refresh()->data['media'])->where('collection', 'gallery'));

        $this->actingAs($admin)->post($url, [
            'collection' => 'gallery',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('replacement.jpg', 900, 1200),
        ], ['Accept' => 'application/json'])->assertOk();

        $this->assertCount(1, collect($demo->refresh()->data['media'])->where('collection', 'gallery'));
        Storage::disk('public')->assertMissing($photos->pluck('file_path')->all());
    }

    public function test_customer_cannot_upload_demo_media(): void
    {
        $customer = User::factory()->active()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = TemplateDemo::query()->firstOrFail();

        $this->actingAs($customer)->post(route('admin.demos.media.store', $demo), [
            'collection' => 'cover',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('cover.jpg'),
        ], ['Accept' => 'application/json'])->assertForbidden();
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

    public function test_guest_can_open_an_active_template_demo(): void
    {
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $template = Template::query()->where('key', 'voxel-voyage')->firstOrFail();

        $this->get(route('templates.show', $template))
            ->assertOk()
            ->assertSee('Nara &amp; Raka', false)
            ->assertDontSee('data-async-form="rsvp"', false)
            ->assertDontSee('data-async-form="wish"', false);
    }

    public function test_guest_cannot_open_a_missing_or_inactive_template_demo(): void
    {
        $templateWithoutDemo = Template::factory()->create(['is_active' => true]);
        $inactiveTemplate = Template::factory()->create(['is_active' => false]);
        TemplateDemo::query()->create([
            'template_id' => $inactiveTemplate->id,
            'slug' => 'inactive-demo',
            'title' => 'Inactive Demo',
            'data' => [],
        ]);

        $this->get(route('templates.show', $templateWithoutDemo))->assertNotFound();
        $this->get(route('templates.show', $inactiveTemplate))->assertNotFound();
    }

    public function test_landing_demo_link_uses_the_public_template_preview_route(): void
    {
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $template = Template::query()->where('key', 'voxel-voyage')->firstOrFail();
        $template->update(['price' => 175000]);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-whatsapp-dialog', false)
            ->assertSee('action="https://wa.me/'.config('legal.whatsapp').'"', false)
            ->assertSee('name="text"', false)
            ->assertSee('href="'.route('templates.show', $template).'"', false)
            ->assertSee('href="'.route('register', ['template' => $template->id]).'"', false)
            ->assertSee('Rp 175.000')
            ->assertSee('Daftar &amp; Pilih', false)
            ->assertDontSee('href="'.route('public.invitation', 'nara-raka').'"', false);
    }

    public function test_midnight_nusantara_demo_is_seeded_and_rendered(): void
    {
        $customer = User::factory()->active()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $template = Template::query()->where('key', 'midnight-nusantara')->firstOrFail();

        $this->actingAs($customer)->get(route('templates.show', $template))
            ->assertOk()
            ->assertSee('Aruna &amp; Bima', false)
            ->assertSee('data-midnight-shell', false)
            ->assertSee('Agenda Perayaan')
            ->assertSee('css/templates/midnight-nusantara.css', false)
            ->assertSee('js/templates/midnight-nusantara.js', false)
            ->assertSee('images/templates/midnight-nusantara/couple-placeholder.svg', false)
            ->assertDontSee('templates/eternal-ivory', false)
            ->assertDontSee('images/templates/eternal-ivory', false);

        $this->assertSame('Luxury', $template->category);
        $this->assertSame('templates.midnight-nusantara.index', $template->view_path);
        $this->assertSame('aruna-bima', $template->demo->slug);
    }

    public function test_voxel_voyage_demo_is_seeded_and_rendered(): void
    {
        $customer = User::factory()->active()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $template = Template::query()->where('key', 'voxel-voyage')->firstOrFail();

        $this->actingAs($customer)->get(route('templates.show', $template))
            ->assertOk()
            ->assertSee('Nara &amp; Raka', false)
            ->assertSee('data-voxel-voyage', false)
            ->assertSee('Mission Board')
            ->assertSee('css/templates/voxel-voyage.css', false)
            ->assertSee('js/templates/voxel-voyage.js', false)
            ->assertDontSee('templates/eternal-ivory', false)
            ->assertDontSee('templates/midnight-nusantara', false);

        $this->assertSame('Playful', $template->category);
        $this->assertSame('templates.voxel-voyage.index', $template->view_path);
        $this->assertSame('nara-raka', $template->demo->slug);
    }

    public function test_restored_classic_skin_demos_are_seeded_and_rendered(): void
    {
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);

        foreach ([
            'rustic-forest' => ['Raka &amp; Nadira', 'raka-nadira', 'css/templates/rustic-forest.css', '#efe7d8'],
            'modern-minimalist' => ['Kevin &amp; Laras', 'kevin-laras', 'css/templates/modern-minimalist.css', '#f7f5f1'],
        ] as $key => [$title, $slug, $stylesheet, $background]) {
            $template = Template::query()->where('key', $key)->firstOrFail();

            $this->get(route('templates.show', $template))
                ->assertOk()
                ->assertSee($title, false)
                ->assertSee($stylesheet, false)
                ->assertSee("--template-background: {$background}", false)
                ->assertSee('<link rel="canonical" href="'.route('templates.show', $template).'">', false)
                ->assertSee('js/templates/editorial-invitation.js', false)
                ->assertSee('js/invitation-forms.js', false)
                ->assertDontSee('js/templates/eternal-ivory.js', false)
                ->assertDontSee('css/templates/eternal-ivory.css', false)
                ->assertDontSee('data-async-form="rsvp"', false)
                ->assertDontSee('data-async-form="wish"', false);

            $this->assertTrue($template->is_active);
            $this->assertSame($slug, $template->demo->slug);
        }
    }

    public function test_restored_templates_have_distinct_layout_signatures(): void
    {
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $rustic = Template::query()->where('key', 'rustic-forest')->firstOrFail();
        $modern = Template::query()->where('key', 'modern-minimalist')->firstOrFail();

        $this->get(route('templates.show', $rustic))
            ->assertOk()
            ->assertSee('class="rf-page"', false)
            ->assertSee('class="rf-story-list"', false)
            ->assertSee('class="rf-event-list"', false)
            ->assertDontSee('class="mm-shell"', false)
            ->assertDontSee('class="invitation-wrapper"', false);

        $this->get(route('templates.show', $modern))
            ->assertOk()
            ->assertSee('class="mm-shell"', false)
            ->assertSee('class="mm-people"', false)
            ->assertSee('class="mm-wish-grid"', false)
            ->assertDontSee('class="rf-page"', false)
            ->assertDontSee('class="invitation-wrapper"', false);
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
