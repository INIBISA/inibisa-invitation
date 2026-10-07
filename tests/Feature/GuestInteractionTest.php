<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GuestInteractionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_submits_rsvp_to_published_invitation(): void
    {
        $invitation = Invitation::factory()->published()->create(['data' => $this->invitationData()]);

        $response = $this->post(route('public.rsvp', $invitation), [
            'guest_name' => 'Muhammad Rizky',
            'attendance' => 'attending',
            'guest_count' => 2,
        ]);

        $response->assertRedirect()->assertSessionHas('rsvp_success');
        $this->assertDatabaseHas('rsvps', ['invitation_id' => $invitation->id, 'guest_name' => 'Muhammad Rizky', 'guest_count' => 2]);
    }

    public function test_guest_submits_rsvp_as_json_without_redirect(): void
    {
        $invitation = Invitation::factory()->published()->create(['data' => $this->invitationData()]);

        $this->postJson(route('public.rsvp', $invitation), [
            'guest_name' => 'Muhammad Rizky',
            'attendance' => 'attending',
            'guest_count' => 2,
        ])->assertOk()->assertJsonPath('message', 'Konfirmasi kehadiran Anda telah tersimpan.');

        $this->assertDatabaseHas('rsvps', ['invitation_id' => $invitation->id, 'guest_name' => 'Muhammad Rizky']);
    }

    public function test_guest_submits_wish_as_json_and_receives_render_data(): void
    {
        $invitation = Invitation::factory()->published()->create(['data' => $this->invitationData()]);

        $this->postJson(route('public.wishes', $invitation), [
            'guest_name' => 'Siti',
            'message' => 'Semoga bahagia selalu',
        ])->assertOk()
            ->assertJsonPath('message', 'Ucapan Anda telah dikirim.')
            ->assertJsonPath('data.guest_name', 'Siti')
            ->assertJsonPath('data.message', 'Semoga bahagia selalu');
    }

    public function test_ajax_wish_validation_returns_json_errors(): void
    {
        $invitation = Invitation::factory()->published()->create(['data' => $this->invitationData()]);

        $this->postJson(route('public.wishes', $invitation), ['guest_name' => '', 'message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['guest_name', 'message']);
    }

    public function test_guest_wish_is_escaped_when_rendered(): void
    {
        $invitation = Invitation::factory()->published()->create(['data' => $this->invitationData()]);
        Wish::factory()->for($invitation)->create([
            'guest_name' => '<script>alert(1)</script>',
            'message' => '<img src=x onerror=alert(1)>',
        ]);

        $response = $this->get(route('public.invitation', $invitation->slug));

        $response->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_disabled_wishes_feature_forbids_submission(): void
    {
        $data = $this->invitationData();
        $data['settings']['wishes'] = false;
        $invitation = Invitation::factory()->published()->create(['data' => $data]);

        $this->post(route('public.wishes', $invitation), ['guest_name' => 'Guest', 'message' => 'Selamat'])->assertForbidden();
        $this->assertDatabaseCount('wishes', 0);
    }

    public function test_guest_interactions_are_rate_limited(): void
    {
        $invitation = Invitation::factory()->published()->create(['data' => $this->invitationData()]);
        $payload = ['guest_name' => 'Guest', 'message' => 'Selamat'];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('public.wishes', $invitation), $payload)->assertRedirect();
        }

        $this->post(route('public.wishes', $invitation), $payload)->assertTooManyRequests();
        $this->assertDatabaseCount('wishes', 5);
    }

    private function invitationData(): array
    {
        return [
            'groom' => ['nickname' => 'Haikal', 'full_name' => 'Muhammad Haikal'],
            'bride' => ['nickname' => 'Fitria', 'full_name' => 'Fitria Putri'],
            'wedding_date' => '2026-09-20',
            'events' => [], 'stories' => [], 'banks' => [],
            'settings' => ['countdown' => true, 'gallery' => true, 'story' => true, 'gift' => true, 'rsvp' => true, 'wishes' => true, 'music' => true],
        ];
    }
}
