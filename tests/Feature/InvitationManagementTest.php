<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvitationManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_active_customer_creates_invitation_with_json_data(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();

        $response = $this->actingAs($customer)->post(route('invitations.store'), $this->validPayload($template));

        $invitation = Invitation::query()->where('slug', 'haikal-fitria')->firstOrFail();
        $response->assertRedirect(route('invitations.edit', $invitation));
        $this->assertSame('Haikal', $invitation->data['groom']['nickname']);
        $this->assertSame(Invitation::STATUS_DRAFT, $invitation->status);
        $this->assertArrayNotHasKey('unexpected', $invitation->data['settings']);
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
        $payload = $this->validPayload($template);
        $payload['cover'] = UploadedFile::fake()->image('cover.jpg', 900, 1200);

        $this->actingAs($customer)->post(route('invitations.store'), $payload)->assertRedirect();

        $media = Invitation::query()->firstOrFail()->media()->firstOrFail();
        $this->assertSame('cover', $media->collection);
        $this->assertStringEndsWith('.webp', $media->file_path);
        Storage::disk('public')->assertExists($media->file_path);
    }

    public function test_customer_pages_render_and_invitation_can_be_published_then_disabled(): void
    {
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();

        $this->actingAs($customer)->get(route('dashboard'))->assertOk()->assertSee('Create Invitation');
        $this->actingAs($customer)->get(route('invitations.create'))->assertOk();
        $this->actingAs($customer)->get(route('invitations.edit', $invitation))->assertOk();
        $this->actingAs($customer)->get(route('invitations.rsvps', $invitation))->assertOk();
        $this->actingAs($customer)->get(route('invitations.wishes', $invitation))->assertOk();

        $this->actingAs($customer)->post(route('invitations.publication.store', $invitation))->assertRedirect();
        $this->assertSame(Invitation::STATUS_PUBLISHED, $invitation->refresh()->status);
        $this->assertNotNull($invitation->published_at);

        $this->actingAs($customer)->delete(route('invitations.publication.destroy', $invitation))->assertRedirect();
        $this->assertSame(Invitation::STATUS_INACTIVE, $invitation->refresh()->status);
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
