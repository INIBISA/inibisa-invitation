<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminApprovalTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_approves_pending_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.customers.update', $customer), ['status' => User::STATUS_ACTIVE]);

        $response->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $customer->refresh()->status);
    }

    public function test_customer_is_forbidden_from_admin_pages(): void
    {
        $customer = User::factory()->active()->create();

        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_rejected_customer_loses_dashboard_access(): void
    {
        $customer = User::factory()->active()->create();
        $customer->update(['status' => User::STATUS_REJECTED]);

        $this->actingAs($customer)->get(route('dashboard'))->assertRedirect(route('approval.pending'));
    }

    public function test_admin_pages_render_successfully(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Recent Invitations');
        $this->actingAs($admin)->get(route('admin.customers.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.invitations.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.templates.index'))->assertOk();
    }
}
