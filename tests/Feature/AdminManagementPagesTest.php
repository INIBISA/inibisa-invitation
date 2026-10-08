<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Rsvp;
use App\Models\Setting;
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

        $this->actingAs($admin)->get(route('admin.rsvps.index'))
            ->assertOk()
            ->assertSee(route('admin.rsvps.data'));

        $response = $this->actingAs($admin)->getJson(route('admin.rsvps.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 25,
            'attendance' => 'attending',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.guest_name', 'Rizky Special')
            ->assertJsonPath('data.0.invitation_title', 'Haikal &amp; Fitria');
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

    public function test_admin_cannot_enable_midtrans_without_credentials(): void
    {
        config()->set('payments.midtrans.server_key');
        config()->set('payments.midtrans.client_key');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.settings.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'active_payment_method' => Payment::METHOD_MIDTRANS,
        ])->assertSessionHasErrors('active_payment_method');

        $this->assertDatabaseMissing('settings', ['key' => Setting::KEY_ACTIVE_PAYMENT_METHOD]);
    }
}
