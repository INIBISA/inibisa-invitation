<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_customer_without_paid_payment_cannot_create_an_invitation(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();

        $this->actingAs($customer)->get(route('invitations.create'))->assertRedirect(route('templates.index'));
        $this->actingAs($customer)->post(route('invitations.store'), ['template_id' => $template->id])->assertForbidden();
    }

    public function test_admin_approves_manual_payment(): void
    {
        $admin = User::factory()->admin()->create();
        $payment = Payment::factory()->create(['status' => Payment::STATUS_WAITING_APPROVAL, 'proof_path' => 'payment-proofs/test.png']);

        $this->actingAs($admin)->patch(route('admin.payments.update', $payment), ['status' => Payment::STATUS_PAID])->assertRedirect();
        $this->assertSame(Payment::STATUS_PAID, $payment->refresh()->status);
        $this->assertSame($admin->id, $payment->approved_by);
    }

    public function test_checkout_uses_the_single_payment_method_enabled_by_admin(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        Payment::factory()->for($customer)->for($template)->create([
            'status' => Payment::STATUS_PAID,
            'invitation_id' => null,
        ]);
        Setting::query()->create(['key' => 'manual_transfer', 'value' => 'BCA 1234567890 a.n. Admin']);
        Setting::query()->create(['key' => Setting::KEY_ACTIVE_PAYMENT_METHOD, 'value' => Payment::METHOD_MANUAL_TRANSFER]);

        $this->actingAs($customer)->get(route('payments.create', $template))
            ->assertOk()
            ->assertSee('Transfer Manual')
            ->assertSee('Ringkasan Pesanan')
            ->assertSee('Salin')
            ->assertDontSee('name="payment_method"', false);
    }

    public function test_customer_cannot_override_the_active_payment_method(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        Setting::query()->create(['key' => 'manual_transfer', 'value' => 'BCA 1234567890 a.n. Admin']);
        Setting::query()->create(['key' => Setting::KEY_ACTIVE_PAYMENT_METHOD, 'value' => Payment::METHOD_MANUAL_TRANSFER]);

        $this->actingAs($customer)->post(route('payments.store'), [
            'template_id' => $template->id,
            'payment_method' => Payment::METHOD_MIDTRANS,
        ])->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_new_payment_uses_the_method_enabled_by_admin(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        Setting::query()->create(['key' => 'manual_transfer', 'value' => 'BCA 1234567890 a.n. Admin']);
        Setting::query()->create(['key' => Setting::KEY_ACTIVE_PAYMENT_METHOD, 'value' => Payment::METHOD_MANUAL_TRANSFER]);

        $this->actingAs($customer)->post(route('payments.store'), ['template_id' => $template->id])->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'user_id' => $customer->id,
            'template_id' => $template->id,
            'payment_method' => Payment::METHOD_MANUAL_TRANSFER,
        ]);
    }

    public function test_changing_active_method_does_not_change_an_existing_payment(): void
    {
        $payment = Payment::factory()->create(['payment_method' => Payment::METHOD_MANUAL_TRANSFER]);

        Setting::query()->create(['key' => Setting::KEY_ACTIVE_PAYMENT_METHOD, 'value' => Payment::METHOD_MIDTRANS]);

        $this->assertSame(Payment::METHOD_MANUAL_TRANSFER, $payment->refresh()->payment_method);
    }
}
