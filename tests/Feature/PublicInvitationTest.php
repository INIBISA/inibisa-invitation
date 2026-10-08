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

        $response->assertOk()
            ->assertSee('Haikal')
            ->assertSee('Fitria')
            ->assertSee('Muhammad Rizky')
            ->assertSee('floral-corner-left.webp')
            ->assertSee('Navigasi undangan');
    }

    public function test_invitation_without_music_uses_default_song(): void
    {
        $invitation = Invitation::factory()->published()->create([
            'slug' => 'tanpa-musik',
            'data' => $this->invitationData(),
        ]);

        $this->get(route('public.invitation', $invitation->slug))
            ->assertOk()
            ->assertSee('data-youtube-music="'.config('music.default_youtube_video_id').'"', false)
            ->assertSee('data-youtube-start="0"', false);
    }

    public function test_invitation_with_chosen_music_keeps_it(): void
    {
        $data = $this->invitationData();
        $data['music'] = ['youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube_video_id' => 'dQw4w9WgXcQ', 'title' => 'Pilihan Saya', 'start_seconds' => 45];
        $invitation = Invitation::factory()->published()->create(['slug' => 'dengan-musik', 'data' => $data]);

        $this->get(route('public.invitation', $invitation->slug))
            ->assertOk()
            ->assertSee('data-youtube-music="dQw4w9WgXcQ"', false)
            ->assertSee('data-youtube-start="45"', false)
            ->assertDontSee('data-youtube-music="'.config('music.default_youtube_video_id').'"', false);
    }

    public function test_disabled_music_section_has_no_player(): void
    {
        $data = $this->invitationData();
        $data['settings']['music'] = false;
        $invitation = Invitation::factory()->published()->create(['slug' => 'musik-mati', 'data' => $data]);

        $this->get(route('public.invitation', $invitation->slug))
            ->assertOk()
            ->assertDontSee('data-youtube-music', false);
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
