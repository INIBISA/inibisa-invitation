<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GuestManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_customer_cannot_read_another_customers_guests(): void
    {
        $owner = User::factory()->active()->create();
        $other = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($owner)->create();
        Guest::factory()->for($invitation)->create();

        $this->actingAs($other)->get(route('invitations.guests.index', $invitation))->assertForbidden();
    }
}
