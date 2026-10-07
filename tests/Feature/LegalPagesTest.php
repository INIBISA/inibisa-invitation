<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_terms_page_renders_without_login(): void
    {
        $this->get(route('terms'))
            ->assertOk()
            ->assertSee('Syarat dan Ketentuan')
            ->assertSee('Pengembalian Dana')
            ->assertSee('https://wa.me/'.config('legal.whatsapp'), false);
    }

    public function test_privacy_page_renders_without_login(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Kebijakan Privasi')
            ->assertSee('Data yang Dikumpulkan')
            ->assertSee('https://wa.me/'.config('legal.whatsapp'), false);
    }

    public function test_legal_pages_are_not_captured_as_invitation_slug(): void
    {
        $this->get('/syarat-ketentuan')->assertOk();
        $this->get('/kebijakan-privasi')->assertOk();
    }
}
