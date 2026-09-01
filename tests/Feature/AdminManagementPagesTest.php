<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementPagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_view_and_filter_all_rsvps(): void
    {
        $admin = User::factory()->admin()->create();
        $invitation = Invitation::factory()->create(['title' => 'Haikal & Fitria']);
        Rsvp::factory()->for($invitation)->create(['guest_name' => 'Rizky Special', 'attendance' => 'attending']);
        Rsvp::factory()->for($invitation)->create(['guest_name' => 'Other Guest', 'attendance' => 'not_attending']);

        $response = $this->actingAs($admin)->get(route('admin.rsvps.index', [
            'search' => 'Rizky',
            'attendance' => 'attending',
        ]));

        $response->assertOk()->assertSee('Rizky Special')->assertSee('Haikal &amp; Fitria', false)->assertDontSee('Other Guest');
    }

    public function test_admin_can_search_all_wishes(): void
    {
        $admin = User::factory()->admin()->create();
        $invitation = Invitation::factory()->create();
        Wish::factory()->for($invitation)->create(['guest_name' => 'Anisa', 'message' => 'Semoga selalu bahagia']);
        Wish::factory()->for($invitation)->create(['guest_name' => 'Budi', 'message' => 'Selamat menikah']);

        $response = $this->actingAs($admin)->get(route('admin.wishes.index', ['search' => 'bahagia']));

        $response->assertOk()->assertSee('Anisa')->assertSee('Semoga selalu bahagia')->assertDontSee('Budi');
    }

    public function test_customer_is_forbidden_from_admin_management_pages(): void
    {
        $customer = User::factory()->active()->create();

        $this->actingAs($customer)->get(route('admin.rsvps.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.wishes.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.settings.edit'))->assertForbidden();
    }

    public function test_admin_can_update_profile_and_password(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'old-admin@example.com']);

        $response = $this->actingAs($admin)->patch(route('admin.settings.update'), [
            'name' => 'Wedding Administrator',
            'email' => 'new-admin@example.com',
            'current_password' => 'password',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ]);

        $response->assertRedirect()->assertSessionHas('success');
        $admin->refresh();
        $this->assertSame('Wedding Administrator', $admin->name);
        $this->assertSame('new-admin@example.com', $admin->email);
        $this->assertTrue(Hash::check('newsecret123', $admin->password));
    }

    public function test_admin_cannot_change_password_without_valid_current_password(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->from(route('admin.settings.edit'))->patch(route('admin.settings.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'current_password' => 'incorrect-password',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ]);

        $response->assertRedirect(route('admin.settings.edit'))->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password', $admin->refresh()->password));
    }
}
