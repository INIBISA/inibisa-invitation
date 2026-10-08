<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
use App\Support\WhatsAppInvitationMessage;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    public function test_customer_imports_guests_and_skips_duplicate_whatsapp_numbers(): void
    {
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();
        Guest::factory()->for($invitation)->create(['whatsapp' => '6281234567890']);
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['nama', 'whatsapp'],
            ['Budi', '081234567890'],
            ['Siti', '081234567891'],
            ['', '081234567892'],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'guests').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        $this->actingAs($customer)
            ->post(route('invitations.guests.import', $invitation), ['guest_file' => new UploadedFile($path, 'guests.xlsx', null, null, true)])
            ->assertRedirect()
            ->assertSessionHas('success', 'Import selesai: 1 ditambahkan, 1 duplikat, 1 tidak valid.');

        $this->assertDatabaseHas('guests', ['invitation_id' => $invitation->id, 'name' => 'Siti', 'whatsapp' => '6281234567891']);
        $this->assertDatabaseMissing('guests', ['invitation_id' => $invitation->id, 'name' => 'Budi']);
    }

    public function test_customer_marks_guest_delivery_status_and_cannot_update_another_customers_guest(): void
    {
        $owner = User::factory()->active()->create();
        $other = User::factory()->active()->create();
        $guest = Guest::factory()->for(Invitation::factory()->for($owner))->create(['sent_at' => null]);

        $this->actingAs($owner)->patchJson(route('guests.delivery', $guest), ['sent' => true])
            ->assertOk()
            ->assertJsonPath('message', 'Tamu ditandai sudah dikirim.');
        $this->assertNotNull($guest->refresh()->sent_at);

        $this->actingAs($other)->patchJson(route('guests.delivery', $guest), ['sent' => false])->assertForbidden();
        $this->assertNotNull($guest->refresh()->sent_at);
    }

    public function test_customer_can_download_guest_import_template(): void
    {
        Excel::fake();
        $customer = User::factory()->active()->create();
        $invitation = Invitation::factory()->for($customer)->create();

        $this->actingAs($customer)->get(route('invitations.guests.template', $invitation))->assertOk();

        Excel::assertDownloaded('template-tamu.xlsx');
    }

    public function test_guest_page_uses_default_whatsapp_message_template(): void
    {
        $customer = User::factory()->active()->create(['whatsapp_message_template' => null]);
        $invitation = Invitation::factory()->for($customer)->create();

        $this->actingAs($customer)->get(route('invitations.guests.index', $invitation))
            ->assertOk()
            ->assertSee('Template Pesan WhatsApp')
            ->assertSee('{nama_tamu}')
            ->assertSee(WhatsAppInvitationMessage::defaultTemplate());
    }

    public function test_customer_saves_one_whatsapp_template_for_all_invitations_and_can_reset_it(): void
    {
        $customer = User::factory()->active()->create();
        $firstInvitation = Invitation::factory()->for($customer)->create();
        $secondInvitation = Invitation::factory()->for($customer)->create();
        $template = "Halo *{nama_tamu}*\nUndangan {nama_mempelai}: {tautan_undangan}";

        $this->actingAs($customer)->patch(route('customer.whatsapp-message.update'), ['message_template' => $template])
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertSame($template, $customer->refresh()->whatsapp_message_template);

        $this->actingAs($customer)->get(route('invitations.guests.index', $firstInvitation))->assertSee($template);
        $this->actingAs($customer)->get(route('invitations.guests.index', $secondInvitation))->assertSee($template);

        $this->actingAs($customer)->delete(route('customer.whatsapp-message.destroy'))->assertRedirect();
        $this->assertNull($customer->refresh()->whatsapp_message_template);
    }

    public function test_whatsapp_template_rejects_unknown_placeholder_and_is_isolated_per_customer(): void
    {
        $firstCustomer = User::factory()->active()->create();
        $secondCustomer = User::factory()->active()->create(['whatsapp_message_template' => 'Pesan customer kedua']);

        $this->actingAs($firstCustomer)->patch(route('customer.whatsapp-message.update'), [
            'message_template' => 'Halo {nama_tamu}, lokasi {lokasi_rahasia}',
        ])->assertSessionHasErrors('message_template');

        $this->assertNull($firstCustomer->refresh()->whatsapp_message_template);
        $this->assertSame('Pesan customer kedua', $secondCustomer->refresh()->whatsapp_message_template);
    }

    public function test_admin_cannot_change_customer_whatsapp_template(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('customer.whatsapp-message.update'), [
            'message_template' => 'Pesan admin',
        ])->assertForbidden();
    }

    public function test_whatsapp_template_is_safely_encoded_in_page_json(): void
    {
        $messageTemplate = 'Halo "Ayu & Bima" </script><script>alert(1)</script> {nama_tamu}';
        $customer = User::factory()->active()->create([
            'whatsapp_message_template' => $messageTemplate,
        ]);
        $invitation = Invitation::factory()->for($customer)->create();

        $response = $this->actingAs($customer)->get(route('invitations.guests.index', $invitation))
            ->assertOk()
            ->assertDontSee('</script><script>alert(1)</script>', false)
            ->assertSee('\\u003C\\/script\\u003E', false);

        $this->assertSame(1, preg_match('/<script type="application\/json" data-whatsapp-message-template>(.*?)<\/script>/s', $response->getContent(), $matches));
        $this->assertSame($messageTemplate, json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR));
    }
}
