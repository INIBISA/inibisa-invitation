<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Rsvp;
use App\Models\Template;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DataTableTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_customer_table_returns_paginated_search_results(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->active()->create(['name' => 'Pelanggan Dicari', 'email' => 'dicari@example.com']);
        User::factory()->active()->create(['name' => 'Pelanggan Lain']);

        $response = $this->actingAs($admin)->getJson(route('admin.customers.data', $this->dataTableQuery('Pelanggan Dicari', [
            ['data' => 'name', 'name' => 'name', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
            ['data' => 'email', 'name' => 'email', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '', 'regex' => 'false']],
        ])));

        $response
            ->assertOk()
            ->assertJsonPath('recordsTotal', 2)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.name', 'Pelanggan Dicari');
    }

    public function test_admin_customer_table_rejects_unsafe_sort_column(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->active()->create();

        $response = $this->actingAs($admin)->getJson(route('admin.customers.data', [
            ...$this->dataTableQuery('', [[
                'data' => 'name',
                'name' => 'name desc; drop table users',
                'searchable' => 'true',
                'orderable' => 'true',
                'search' => ['value' => '', 'regex' => 'false'],
            ]]),
            'order' => [['column' => 0, 'dir' => 'asc']],
        ]));

        $response->assertOk();
        $this->assertDatabaseCount('users', 2);
    }

    public function test_admin_invitation_table_escapes_customer_content(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->active()->create(['name' => '<script>alert(1)</script>']);
        Invitation::factory()->for($customer)->create(['title' => '<b>Judul</b>']);

        $response = $this->actingAs($admin)->getJson(route('admin.invitations.data', $this->dataTableQuery()));

        $response
            ->assertOk()
            ->assertJsonMissing(['customer' => '<script>alert(1)</script>'])
            ->assertJsonPath('data.0.customer', '<strong>&lt;script&gt;alert(1)&lt;/script&gt;</strong><small>'.e($customer->email).'</small>')
            ->assertJsonPath('data.0.invitation', '<strong>&lt;b&gt;Judul&lt;/b&gt;</strong><small>'.$response->json('data.0.slug').'</small>');
    }

    public function test_admin_guest_table_applies_customer_filter(): void
    {
        $admin = User::factory()->admin()->create();
        $firstCustomer = User::factory()->active()->create(['name' => 'Nadia Utama']);
        $secondCustomer = User::factory()->active()->create(['name' => 'Raka Lain']);
        Guest::factory()->for(Invitation::factory()->for($firstCustomer))->create(['name' => 'Tamu Nadia']);
        Guest::factory()->for(Invitation::factory()->for($secondCustomer))->create(['name' => 'Tamu Raka']);

        $response = $this->actingAs($admin)->getJson(route('admin.guests.data', [
            ...$this->dataTableQuery(),
            'customer' => 'Nadia',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.name', 'Tamu Nadia');
    }

    public function test_admin_rsvp_table_applies_attendance_filter(): void
    {
        $admin = User::factory()->admin()->create();
        Rsvp::factory()->create(['guest_name' => 'Tamu Hadir', 'attendance' => 'attending']);
        Rsvp::factory()->create(['guest_name' => 'Tamu Absen', 'attendance' => 'not_attending']);

        $response = $this->actingAs($admin)->getJson(route('admin.rsvps.data', [
            ...$this->dataTableQuery(),
            'attendance' => 'attending',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.guest_name', 'Tamu Hadir');
    }

    public function test_admin_payment_table_validates_and_applies_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->active()->create(['name' => 'Pembayar Tepat']);
        $template = Template::factory()->create();
        Payment::factory()->for($customer)->for($template)->create([
            'payment_method' => Payment::METHOD_MANUAL_TRANSFER,
            'status' => Payment::STATUS_PAID,
        ]);
        Payment::factory()->create(['payment_method' => Payment::METHOD_MIDTRANS, 'status' => Payment::STATUS_PENDING]);

        $this->actingAs($admin)->getJson(route('admin.payments.data', [
            ...$this->dataTableQuery(),
            'method' => Payment::METHOD_MANUAL_TRANSFER,
            'status' => Payment::STATUS_PAID,
            'customer' => 'Pembayar Tepat',
        ]))->assertOk()->assertJsonPath('recordsTotal', 1);

        $this->actingAs($admin)->getJson(route('admin.payments.data', [
            ...$this->dataTableQuery(),
            'status' => 'invalid',
        ]))->assertUnprocessable()->assertJsonValidationErrors('status');
    }

    public function test_customer_guest_table_is_tenant_scoped_and_filters_delivery(): void
    {
        $owner = User::factory()->active()->create();
        $other = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($owner)->create();
        Guest::factory()->for($invitation)->create(['name' => 'Sudah Dikirim', 'sent_at' => now()]);
        Guest::factory()->for($invitation)->create(['name' => 'Belum Dikirim', 'sent_at' => null]);

        $response = $this->actingAs($owner)->getJson(route('invitations.guests.data', [
            'invitation' => $invitation,
            ...$this->dataTableQuery(),
            'delivery' => 'sent',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('data.0.guest', fn (string $value): bool => str_contains($value, 'Sudah Dikirim'));
        $this->actingAs($other)->getJson(route('invitations.guests.data', $invitation))->assertForbidden();
    }

    public function test_customer_response_tables_return_only_owned_invitation_data(): void
    {
        $owner = User::factory()->active()->create();
        $other = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($owner)->create();
        Rsvp::factory()->for($invitation)->create(['guest_name' => 'RSVP Milik Owner']);
        Wish::factory()->for($invitation)->create(['guest_name' => 'Ucapan Milik Owner']);

        $this->actingAs($owner)->getJson(route('invitations.rsvps.data', [
            'invitation' => $invitation,
            ...$this->dataTableQuery(),
        ]))->assertOk()->assertJsonPath('data.0.guest_name', 'RSVP Milik Owner');
        $this->actingAs($owner)->getJson(route('invitations.wishes.data', [
            'invitation' => $invitation,
            ...$this->dataTableQuery(),
        ]))->assertOk()->assertJsonPath('data.0.guest_name', 'Ucapan Milik Owner');

        $this->actingAs($other)->getJson(route('invitations.rsvps.data', $invitation))->assertForbidden();
        $this->actingAs($other)->getJson(route('invitations.wishes.data', $invitation))->assertForbidden();
    }

    #[DataProvider('adminDataRoutes')]
    public function test_customer_is_forbidden_from_admin_data_routes(string $route): void
    {
        $customer = User::factory()->active()->create();

        $this->actingAs($customer)->getJson(route($route, $this->dataTableQuery()))->assertForbidden();
    }

    public static function adminDataRoutes(): array
    {
        return [
            'customers' => ['admin.customers.data'],
            'invitations' => ['admin.invitations.data'],
            'guests' => ['admin.guests.data'],
            'rsvps' => ['admin.rsvps.data'],
            'payments' => ['admin.payments.data'],
        ];
    }

    /** @return array<string, mixed> */
    private function dataTableQuery(string $search = '', array $columns = []): array
    {
        return [
            'draw' => 1,
            'start' => 0,
            'length' => 25,
            'search' => ['value' => $search, 'regex' => false],
            'columns' => $columns,
            'order' => [],
        ];
    }
}
