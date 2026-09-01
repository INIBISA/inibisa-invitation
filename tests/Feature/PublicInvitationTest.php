<?php

namespace Tests\Feature;

use App\Models\Invitation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PublicInvitationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_published_invitation_renders_dynamic_template_and_guest_name(): void
    {
        $invitation = Invitation::factory()->published()->create([
            'slug' => 'haikal-fitria',
            'title' => 'Haikal & Fitria',
            'data' => $this->invitationData(),
        ]);

        $response = $this->get(route('public.invitation', ['slug' => $invitation->slug, 'to' => 'Muhammad Rizky']));

        $response->assertOk()->assertSee('Haikal')->assertSee('Fitria')->assertSee('Muhammad Rizky');
    }

    public function test_draft_and_inactive_invitations_are_not_public(): void
    {
        $draft = Invitation::factory()->create(['slug' => 'draft-invitation']);
        $inactive = Invitation::factory()->create(['slug' => 'inactive-invitation', 'status' => Invitation::STATUS_INACTIVE]);

        $this->get(route('public.invitation', $draft->slug))->assertNotFound();
        $this->get(route('public.invitation', $inactive->slug))->assertNotFound();
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
