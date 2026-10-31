<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationMedia;
use App\Models\Payment;
use App\Models\Template;
use App\Models\User;
use App\Models\WeddingMusic;
use App\Support\InvitationData;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InvitationManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_active_customer_creates_invitation_with_json_data(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        Payment::factory()->for($customer)->for($template)->create(['status' => Payment::STATUS_PAID, 'invitation_id' => null]);

        $response = $this->actingAs($customer)->post(route('invitations.store'), $this->validPayload($template));

        $invitation = Invitation::query()->where('slug', 'haikal-fitria')->firstOrFail();
        $response->assertRedirect(route('invitations.edit', $invitation));
        $this->assertSame('Haikal', $invitation->data['groom']['nickname']);
        $this->assertSame(Invitation::STATUS_DRAFT, $invitation->status);
        $this->assertArrayNotHasKey('unexpected', $invitation->data['settings']);
    }

    public function test_customer_saves_invitation_draft_step_by_step(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        $payment = Payment::factory()->for($customer)->for($template)->create(['status' => Payment::STATUS_PAID, 'invitation_id' => null]);

        $response = $this->actingAs($customer)->postJson(route('invitations.steps.store'), [
            'step' => 1,
            'template_id' => $template->id,
            'title' => 'Haikal & Fitria',
            'slug' => 'Haikal dan Fitria',
        ]);

        $invitation = Invitation::query()->where('slug', 'haikal-dan-fitria')->firstOrFail();
        $response->assertOk()
            ->assertJsonPath('invitation_id', $invitation->id)
            ->assertJsonPath('media_upload_url', route('invitations.media.store', $invitation))
            ->assertJsonPath('next_step', 2);
        $this->assertSame($invitation->id, $payment->refresh()->invitation_id);
        $this->assertSame(2, $invitation->editing_step);

        $this->actingAs($customer)->patchJson(route('invitations.steps.update', [$invitation, 2]), [
            'step' => 2,
            'groom' => ['nickname' => 'Haikal', 'full_name' => 'Muhammad Haikal'],
            'bride' => ['nickname' => 'Fitria', 'full_name' => 'Fitria Putri'],
        ])->assertOk()->assertJsonPath('next_step', 3);

        $invitation->refresh();
        $this->assertSame('Haikal & Fitria', $invitation->title);
        $this->assertSame('Haikal', $invitation->data['groom']['nickname']);
        $this->assertSame(3, $invitation->editing_step);
    }

    public function test_step_save_merges_data_without_erasing_other_steps(): void
    {
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create([
            'data' => array_replace_recursive(InvitationData::defaults(), [
                'groom' => ['nickname' => 'Haikal'],
                'bride' => ['nickname' => 'Fitria'],
            ]),
        ]);

        $this->actingAs($customer)->patchJson(route('invitations.steps.update', [$invitation, 3]), [
            'step' => 3,
            'wedding_date' => '2026-09-20',
            'quote' => 'Bersama selamanya',
            'events' => [[
                'name' => 'Akad',
                'date' => '2026-09-20',
                'time' => '08:00',
                'location' => 'Gedung',
                'address' => 'Jalan Merdeka',
            ]],
        ])->assertOk();

        $invitation->refresh();
        $this->assertSame('Haikal', $invitation->data['groom']['nickname']);
        $this->assertSame('2026-09-20', $invitation->data['wedding_date']);
        $this->assertSame('Akad', $invitation->data['events'][0]['name']);
    }

    public function test_step_save_validates_only_active_step_and_enforces_owner(): void
    {
        $owner = User::factory()->active()->create();
        $other = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($owner)->create();

        $this->actingAs($owner)->patchJson(route('invitations.steps.update', [$invitation, 3]), [
            'step' => 3,
            'events' => [['name' => 'Akad']],
        ])->assertUnprocessable()->assertJsonValidationErrors(['events.0.date', 'events.0.time', 'events.0.location', 'events.0.address']);

        $this->actingAs($other)->patchJson(route('invitations.steps.update', [$invitation, 2]), [
            'step' => 2,
            'groom' => ['nickname' => 'Bukan Pemilik'],
        ])->assertForbidden();
    }

    public function test_customer_can_choose_where_catalog_music_starts(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        $music = WeddingMusic::factory()->create();
        Payment::factory()->for($customer)->for($template)->create(['status' => Payment::STATUS_PAID, 'invitation_id' => null]);
        $payload = $this->validPayload($template);
        $payload['wedding_music_id'] = $music->id;
        $payload['music_start_seconds'] = 45;

        $this->actingAs($customer)->post(route('invitations.store'), $payload)->assertRedirect();

        $invitation = Invitation::query()->firstOrFail();
        $this->assertSame($music->id, $invitation->wedding_music_id);
        $this->assertSame(45, $invitation->data['music']['start_seconds']);
    }

    public function test_customer_can_update_where_custom_music_starts(): void
    {
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();
        $payload = $this->validPayload($invitation->template);
        $payload['youtube_url'] = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $payload['music_start_seconds'] = 90;

        $this->actingAs($customer)->put(route('invitations.update', $invitation), $payload)->assertRedirect();

        $this->assertSame(90, $invitation->refresh()->data['music']['start_seconds']);
    }

    #[DataProvider('invalidMusicStartSeconds')]
    public function test_customer_cannot_set_invalid_music_start_seconds(mixed $startSeconds): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        Payment::factory()->for($customer)->for($template)->create(['status' => Payment::STATUS_PAID, 'invitation_id' => null]);
        $payload = $this->validPayload($template);
        $payload['music_start_seconds'] = $startSeconds;

        $this->actingAs($customer)->post(route('invitations.store'), $payload)
            ->assertSessionHasErrors('music_start_seconds');

        $this->assertDatabaseCount('invitations', 0);
    }

    public static function invalidMusicStartSeconds(): array
    {
        return [
            'negative' => [-1],
            'fractional' => ['1.5'],
            'beyond_limit' => [43201],
        ];
    }

    public function test_customer_cannot_update_another_customers_invitation(): void
    {
        $owner = User::factory()->active()->create();
        $otherCustomer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($owner)->create();

        $this->actingAs($otherCustomer)
            ->put(route('invitations.update', $invitation), $this->validPayload($invitation->template))
            ->assertForbidden();
    }

    public function test_reserved_slug_is_rejected(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        Payment::factory()->for($customer)->for($template)->create(['status' => Payment::STATUS_PAID, 'invitation_id' => null]);
        $payload = $this->validPayload($template);
        $payload['slug'] = 'admin';

        $response = $this->actingAs($customer)->post(route('invitations.store'), $payload);

        $response->assertSessionHasErrors('slug');
        $this->assertDatabaseCount('invitations', 0);
    }

    public function test_uploaded_cover_is_reencoded_as_webp(): void
    {
        Storage::fake('public');
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        Payment::factory()->for($customer)->for($template)->create(['status' => Payment::STATUS_PAID, 'invitation_id' => null]);
        $payload = $this->validPayload($template);
        $payload['cover'] = UploadedFile::fake()->image('cover.jpg', 900, 1200);

        $this->actingAs($customer)->post(route('invitations.store'), $payload)->assertRedirect();

        $media = Invitation::query()->firstOrFail()->media()->firstOrFail();
        $this->assertSame('cover', $media->collection);
        $this->assertStringEndsWith('.webp', $media->file_path);
        Storage::disk('public')->assertExists($media->file_path);
    }

    public function test_customer_uploads_gallery_photos_one_request_at_a_time(): void
    {
        Storage::fake('public');
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();
        $url = route('invitations.media.store', $invitation);

        $this->actingAs($customer)->post($url, [
            'collection' => 'gallery',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('first.jpg', 900, 1200),
        ], ['Accept' => 'application/json'])->assertOk()->assertJsonPath('collection', 'gallery');
        $this->actingAs($customer)->post($url, [
            'collection' => 'gallery',
            'replace' => '0',
            'file' => UploadedFile::fake()->image('second.jpg', 900, 1200),
        ], ['Accept' => 'application/json'])->assertOk();

        $photos = $invitation->media()->where('collection', 'gallery')->get();
        $this->assertCount(2, $photos);
        $this->assertSame([0, 1], $photos->pluck('sort_order')->all());
        Storage::disk('public')->assertExists($photos->pluck('file_path')->all());

        $this->actingAs($customer)->patchJson(route('invitations.steps.update', [$invitation, 5]), [
            'step' => 5,
            'music_start_seconds' => 0,
        ])->assertOk();

        $this->assertCount(2, $invitation->media()->where('collection', 'gallery')->get());
    }

    public function test_customer_replacing_one_photo_removes_the_previous_file(): void
    {
        Storage::fake('public');
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();
        $url = route('invitations.media.store', $invitation);

        $this->actingAs($customer)->post($url, [
            'collection' => 'cover',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('old.jpg', 900, 1200),
        ], ['Accept' => 'application/json'])->assertOk();
        $previous = $invitation->media()->firstOrFail();
        $this->actingAs($customer)->post($url, [
            'collection' => 'cover',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('new.jpg', 900, 1200),
        ], ['Accept' => 'application/json'])->assertOk();

        $this->assertSame(1, $invitation->media()->where('collection', 'cover')->count());
        Storage::disk('public')->assertMissing($previous->file_path);
        Storage::disk('public')->assertExists($invitation->media()->firstOrFail()->file_path);
    }

    public function test_image_upload_checks_owner_file_type_and_collection_limit(): void
    {
        Storage::fake('public');
        $owner = User::factory()->active()->create();
        $otherCustomer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($owner)->create();
        $url = route('invitations.media.store', $invitation);

        $this->actingAs($otherCustomer)->post($url, [
            'collection' => 'gallery',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('photo.jpg'),
        ], ['Accept' => 'application/json'])->assertForbidden();
        $this->actingAs($owner)->post($url, [
            'collection' => 'gallery',
            'replace' => '1',
            'file' => UploadedFile::fake()->create('document.txt', 1, 'text/plain'),
        ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('file');
        $this->actingAs($owner)->post($url, [
            'collection' => 'unsupported',
            'replace' => '1',
            'file' => UploadedFile::fake()->image('photo.jpg'),
        ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('collection');

        InvitationMedia::factory()->for($invitation)->count(12)->create();
        $this->actingAs($owner)->post($url, [
            'collection' => 'gallery',
            'replace' => '0',
            'file' => UploadedFile::fake()->image('extra.jpg'),
        ], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('file');

        $this->assertSame(12, $invitation->media()->where('collection', 'gallery')->count());
    }

    public function test_customer_pages_render_and_invitation_can_be_published_then_disabled(): void
    {
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();
        Payment::factory()->for($customer)->for($invitation->template)->create(['status' => Payment::STATUS_PAID, 'invitation_id' => null]);

        $this->actingAs($customer)->get(route('dashboard'))->assertOk()->assertSee('Undangan Anda');
        $this->actingAs($customer)->get(route('invitations.create'))->assertOk();
        $this->actingAs($customer)->get(route('invitations.edit', $invitation))->assertOk();
        $this->actingAs($customer)->get(route('invitations.rsvps', $invitation))->assertOk();
        $this->actingAs($customer)->get(route('invitations.wishes', $invitation))->assertOk();

        $invitation->update(['data' => array_replace_recursive($invitation->data, [
            'groom' => ['nickname' => 'Haikal', 'full_name' => 'Muhammad Haikal'],
            'bride' => ['nickname' => 'Fitria', 'full_name' => 'Fitria Putri'],
            'wedding_date' => '2026-09-20',
            'events' => [['name' => 'Akad Nikah', 'date' => '2026-09-20', 'time' => '08:00', 'location' => 'Gedung Serbaguna', 'address' => 'Jl. Merdeka No. 1']],
        ])]);

        $this->actingAs($customer)->post(route('invitations.publication.store', $invitation))->assertRedirect();
        $this->assertSame(Invitation::STATUS_PUBLISHED, $invitation->refresh()->status);
        $this->assertNotNull($invitation->published_at);

        $this->actingAs($customer)->delete(route('invitations.publication.destroy', $invitation))->assertRedirect();
        $this->assertSame(Invitation::STATUS_INACTIVE, $invitation->refresh()->status);
    }

    public function test_incomplete_invitation_shows_publication_error_alert_without_publishing(): void
    {
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();

        $response = $this->actingAs($customer)
            ->followingRedirects()
            ->from(route('invitations.edit', $invitation))
            ->post(route('invitations.publication.store', $invitation));

        $response->assertOk()
            ->assertSee('class="alert error"', false)
            ->assertSee('Lengkapi nama mempelai, tanggal, dan minimal satu acara sebelum publikasi.');
        $this->assertSame(Invitation::STATUS_DRAFT, $invitation->refresh()->status);
        $this->assertNull($invitation->published_at);
    }

    public function test_inactive_template_shows_publication_error_without_publishing(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create(['is_active' => false]);
        $invitation = Invitation::factory()->for($customer)->for($template)->create();

        $response = $this->actingAs($customer)
            ->from(route('invitations.edit', $invitation))
            ->post(route('invitations.publication.store', $invitation));

        $response->assertRedirect(route('invitations.edit', $invitation))
            ->assertSessionHasErrors(['publication' => 'Template tidak aktif.']);
        $this->assertSame(Invitation::STATUS_DRAFT, $invitation->refresh()->status);
    }

    private function validPayload(Template $template): array
    {
        return [
            'template_id' => $template->id,
            'title' => 'Haikal & Fitria',
            'slug' => 'haikal-fitria',
            'groom' => ['nickname' => 'Haikal', 'full_name' => 'Muhammad Haikal'],
            'bride' => ['nickname' => 'Fitria', 'full_name' => 'Fitria Putri'],
            'wedding_date' => '2026-09-20',
            'events' => [],
            'stories' => [],
            'banks' => [],
            'settings' => ['rsvp' => 1, 'wishes' => 1, 'unexpected' => 1],
        ];
    }
}
