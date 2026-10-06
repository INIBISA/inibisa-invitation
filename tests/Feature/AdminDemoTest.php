<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Template;
use App\Models\User;
use Database\Seeders\DemoInvitationSeeder;
use Database\Seeders\TemplateSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminDemoTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_edit_demo_invitation(): void
    {
        $admin = User::factory()->admin()->create();
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = Invitation::query()->where('slug', 'alexander-cyntia')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.demos.edit', $demo))->assertOk()->assertSee('Edit Demo');

        $payload = $this->validPayload($demo->template);
        $payload['title'] = 'Alexander & Cyntia Updated';

        $this->actingAs($admin)->put(route('admin.demos.update', $demo), $payload)->assertRedirect();

        $demo->refresh();
        $this->assertSame('Alexander & Cyntia Updated', $demo->title);
        $this->assertSame('Bimo', $demo->data['groom']['nickname']);
        $this->assertSame(Invitation::STATUS_PUBLISHED, $demo->status);
    }

    public function test_customer_is_forbidden_from_admin_demo_routes(): void
    {
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();

        $this->actingAs($customer)->get(route('admin.demos.edit', $invitation))->assertForbidden();
        $this->actingAs($customer)->put(route('admin.demos.update', $invitation))->assertForbidden();
    }

    public function test_demo_seeder_preserves_admin_edits(): void
    {
        $this->seed([TemplateSeeder::class, DemoInvitationSeeder::class]);
        $demo = Invitation::query()->where('slug', 'alexander-cyntia')->firstOrFail();
        $demo->update(['title' => 'Edited By Admin']);

        $this->seed(DemoInvitationSeeder::class);

        $this->assertSame('Edited By Admin', $demo->refresh()->title);
    }

    private function validPayload(Template $template): array
    {
        return [
            'template_id' => $template->id,
            'title' => 'Alexander & Cyntia',
            'slug' => 'alexander-cyntia',
            'groom' => ['nickname' => 'Bimo', 'full_name' => 'Bimo Alexander'],
            'bride' => ['nickname' => 'Cyntia', 'full_name' => 'Cyntia Ayu'],
            'wedding_date' => '2026-09-20',
            'events' => [],
            'stories' => [],
            'banks' => [],
            'settings' => ['rsvp' => 1, 'wishes' => 1],
        ];
    }
}
