<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\User;
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
}
