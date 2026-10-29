<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\InvitationMedia;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminTemplateDesignSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_edit_template_description_shown_to_guests_and_customers(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['description' => 'Deskripsi awal', 'price' => 150000]);

        $this->actingAs($admin)->get(route('admin.templates.index'))
            ->assertSee('data-template-settings-open="'.$template->id.'"', false)
            ->assertSee('id="template-settings-'.$template->id.'"', false)
            ->assertSee('Deskripsi awal');

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), [
            'name' => $template->name,
            'category' => 'Wedding',
            'price' => 150000,
            'description' => 'Floral <em>hangat</em> untuk hari istimewa.',
            'is_active' => 1,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame('Floral <em>hangat</em> untuk hari istimewa.', $template->refresh()->description);

        auth()->logout();
        $this->get('/')
            ->assertSee('Floral &lt;em&gt;hangat&lt;/em&gt; untuk hari istimewa.', false)
            ->assertDontSee('Floral <em>hangat</em>', false);

        $this->actingAs($admin)->get(route('templates.index'))
            ->assertSee('Floral &lt;em&gt;hangat&lt;/em&gt; untuk hari istimewa.', false);
    }

    public function test_invalid_description_reopens_the_correct_template_modal_with_old_input(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['description' => 'Deskripsi lama']);

        $this->actingAs($admin)->from(route('admin.templates.index'))->patch(route('admin.templates.update', $template), [
            'template_form_id' => $template->id,
            'name' => $template->name,
            'category' => 'Wedding',
            'price' => 150000,
            'description' => str_repeat('A', 501),
        ])->assertRedirect(route('admin.templates.index'))->assertSessionHasErrors('description');

        $this->assertSame('Deskripsi lama', $template->refresh()->description);
        $this->actingAs($admin)->get(route('admin.templates.index'))
            ->assertSee("document.getElementById('template-settings-{$template->id}')?.showModal();", false)
            ->assertSee(str_repeat('A', 501));
    }

    public function test_admin_can_save_and_publish_template_design_draft(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), [
            'name' => $template->name,
            'category' => 'Wedding',
            'price' => 150000,
            'is_active' => 1,
            'design_settings' => [
                'primary_color' => '#123456',
                'text_color' => '#222222',
                'background_color' => '#fafafa',
                'heading_font' => 'serif',
                'body_font' => 'sans',
                'card_radius' => 18,
                'hero_overlay' => 45,
                'sections' => [
                    'quote' => true,
                    'countdown' => true,
                    'events' => false,
                    'story' => true,
                    'gallery' => true,
                    'rsvp' => true,
                    'gift' => true,
                    'wishes' => true,
                ],
                'section_order' => ['events', 'couple', 'quote', 'countdown', 'story', 'gallery', 'rsvp', 'gift', 'wishes'],
            ],
        ])->assertRedirect()->assertSessionHas('success');

        $template->refresh();
        $this->assertNull($template->design_settings);
        $this->assertSame('#123456', $template->design_draft_settings['primary_color']);

        $this->actingAs($admin)->post(route('admin.templates.design-publication.store', $template))
            ->assertRedirect()
            ->assertSessionHas('success');

        $template->refresh();
        $this->assertSame('#123456', $template->design_settings['primary_color']);
        $this->assertSame(18, $template->design_settings['card_radius']);
        $this->assertFalse($template->design_settings['sections']['events']);
        $this->assertSame('events', $template->design_settings['section_order'][0]);
        $this->assertNull($template->design_draft_settings);
        $this->assertCount(1, $template->designRevisions);
    }

    public function test_design_settings_are_rendered_as_safe_css_variables(): void
    {
        $template = Template::factory()->create([
            'design_settings' => [
                'primary_color' => '#123456',
                'text_color' => '#222222',
                'background_color' => '#fafafa',
                'heading_font' => 'serif',
                'body_font' => 'sans',
                'card_radius' => 18,
                'hero_overlay' => 45,
            ],
        ]);
        $invitation = new class($template)
        {
            public function __construct(public Template $template) {}
        };

        $html = view('templates.partials.design-settings', compact('invitation'))->render();

        $this->assertStringContainsString('--template-primary: #123456', $html);
        $this->assertStringContainsString('--template-card-radius: 18px', $html);
        $this->assertStringContainsString('--template-overlay-opacity: 0.45', $html);
        $this->assertStringContainsString('.vv-gate::after', $html);
        $this->assertStringContainsString('.mn-desk-panel', $html);
        $this->assertStringContainsString('.vv-mission', $html);
    }

    public function test_invalid_or_unexpected_design_settings_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), [
            'name' => $template->name,
            'category' => 'Wedding',
            'price' => 150000,
            'design_settings' => [
                'primary_color' => 'red; background: url(https://example.com)',
                'text_color' => '#222222',
                'background_color' => '#fafafa',
                'heading_font' => 'url',
                'body_font' => 'sans',
                'card_radius' => 100,
                'hero_overlay' => 45,
                'custom_css' => 'body { display: none }',
            ],
        ])->assertInvalid([
            'design_settings',
            'design_settings.primary_color',
            'design_settings.heading_font',
            'design_settings.card_radius',
        ]);

        $template->refresh();
        $this->assertNull($template->design_settings);
        $this->assertNull($template->design_draft_settings);
    }

    public function test_low_contrast_design_colors_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();

        $payload = $this->validDesignPayload($template);
        $payload['design_settings']['text_color'] = '#777777';
        $payload['design_settings']['background_color'] = '#888888';

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), $payload)
            ->assertInvalid(['design_settings.text_color']);

        $this->assertNull($template->refresh()->design_draft_settings);
    }

    public function test_template_without_a_view_cannot_be_activated(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create([
            'view_path' => 'templates.missing.index',
            'is_active' => false,
        ]);
        $payload = $this->validDesignPayload($template);
        $payload['is_active'] = 1;

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), $payload)
            ->assertInvalid(['is_active']);

        $this->assertFalse($template->refresh()->is_active);
    }

    public function test_admin_pages_send_clickjacking_protection_headers(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.templates.index'))
            ->assertOk()
            ->assertHeader('Content-Security-Policy', "frame-ancestors 'self'")
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_admin_template_page_only_loads_five_latest_design_revisions(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();
        $revisionIds = [];

        foreach (range(1, 7) as $index) {
            $revisionIds[] = $template->designRevisions()->create([
                'created_by' => $admin->id,
                'settings' => ['primary_color' => sprintf('#%06d', $index)],
            ])->id;
        }

        $response = $this->actingAs($admin)->get(route('admin.templates.index'))->assertOk();
        $loadedTemplate = $response->viewData('templates')->firstWhere('id', $template->id);

        $this->assertCount(5, $loadedTemplate->designRevisions);
        $this->assertSame(array_reverse(array_slice($revisionIds, -5)), $loadedTemplate->designRevisions->pluck('id')->all());
        $response->assertSee('Riwayat Desain')->assertSee('section')->assertSee('design-swatches', false);
    }

    public function test_admin_template_page_offers_desktop_and_mobile_draft_preview(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();
        $template->demo()->create([
            'slug' => 'responsive-preview',
            'title' => 'Responsive Preview',
            'data' => [],
        ]);

        $this->actingAs($admin)->get(route('admin.templates.index'))
            ->assertOk()
            ->assertSee('Preview Draft Tersimpan')
            ->assertSee('data-preview-size="desktop"', false)
            ->assertSee('data-preview-size="mobile"', false);
    }

    public function test_customer_cannot_customize_template_design(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();

        $this->actingAs($customer)->patch(route('admin.templates.update', $template), [])->assertForbidden();
        $this->actingAs($customer)->get(route('admin.templates.design-preview', $template))->assertForbidden();
        $this->actingAs($customer)->post(route('admin.templates.design-publication.store', $template))->assertForbidden();
    }

    public function test_template_design_cannot_be_published_without_a_draft(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();

        $this->actingAs($admin)->post(route('admin.templates.design-publication.store', $template))
            ->assertUnprocessable();

        $this->assertCount(0, $template->designRevisions);
    }

    public function test_admin_can_discard_a_design_draft_without_changing_published_design(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create([
            'design_settings' => ['primary_color' => '#111111'],
            'design_draft_settings' => ['primary_color' => '#abcdef'],
        ]);

        $this->actingAs($admin)->delete(route('admin.templates.design-draft.destroy', $template))
            ->assertRedirect()
            ->assertSessionHas('success');

        $template->refresh();
        $this->assertSame('#111111', $template->design_settings['primary_color']);
        $this->assertNull($template->design_draft_settings);
        $this->assertCount(0, $template->designRevisions);
    }

    public function test_template_design_draft_cannot_be_discarded_when_none_exists(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create();

        $this->actingAs($admin)->delete(route('admin.templates.design-draft.destroy', $template))
            ->assertUnprocessable();
    }

    public function test_admin_can_reset_design_to_template_defaults_as_a_draft(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create([
            'key' => 'sweet-blossom',
            'design_settings' => ['primary_color' => '#111111'],
        ]);

        $this->actingAs($admin)->post(route('admin.templates.design-reset.store', $template))
            ->assertRedirect()
            ->assertSessionHas('success');

        $template->refresh();
        $this->assertSame('#111111', $template->design_settings['primary_color']);
        $this->assertSame('#c87972', $template->design_draft_settings['primary_color']);
        $this->assertSame(array_keys($template->orderableSections()), $template->design_draft_settings['section_order']);
    }

    public function test_customer_cannot_discard_or_reset_template_design_draft(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create(['design_draft_settings' => ['primary_color' => '#abcdef']]);

        $this->actingAs($customer)->delete(route('admin.templates.design-draft.destroy', $template))->assertForbidden();
        $this->actingAs($customer)->post(route('admin.templates.design-reset.store', $template))->assertForbidden();
    }

    public function test_admin_preview_uses_draft_while_public_demo_uses_published_design(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create([
            'design_settings' => ['primary_color' => '#111111'],
            'design_draft_settings' => ['primary_color' => '#abcdef'],
        ]);
        $template->demo()->create([
            'slug' => 'draft-preview',
            'title' => 'Draft Preview',
            'data' => [
                'groom' => ['nickname' => 'Haikal'],
                'bride' => ['nickname' => 'Fitria'],
                'wedding_date' => '2026-09-20',
                'events' => [], 'stories' => [], 'banks' => [], 'settings' => ['music' => false],
            ],
        ]);

        $this->get(route('templates.show', $template))->assertOk()->assertSee('--template-primary: #111111', false);
        $this->actingAs($admin)->get(route('admin.templates.design-preview', $template))
            ->assertOk()
            ->assertSee('--template-primary: #abcdef', false);
    }

    public function test_admin_can_rollback_to_a_previous_published_design(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['design_settings' => ['primary_color' => '#222222']]);
        $revision = $template->designRevisions()->create([
            'created_by' => $admin->id,
            'settings' => ['primary_color' => '#111111'],
        ]);

        $this->actingAs($admin)->post(route('admin.templates.design-revisions.rollback', [$template, $revision]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('#111111', $template->refresh()->design_settings['primary_color']);
        $this->assertSame('#222222', $template->designRevisions()->first()->settings['primary_color']);
    }

    public function test_revision_from_another_template_cannot_be_used_for_rollback(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['design_settings' => ['primary_color' => '#222222']]);
        $otherTemplate = Template::factory()->create();
        $revision = $otherTemplate->designRevisions()->create([
            'created_by' => $admin->id,
            'settings' => ['primary_color' => '#111111'],
        ]);

        $this->actingAs($admin)->post(route('admin.templates.design-revisions.rollback', [$template, $revision]))
            ->assertNotFound();

        $this->assertSame('#222222', $template->refresh()->design_settings['primary_color']);
    }

    public function test_customer_cannot_rollback_template_design(): void
    {
        $customer = User::factory()->active()->create();
        $template = Template::factory()->create();
        $revision = $template->designRevisions()->create([
            'settings' => ['primary_color' => '#111111'],
        ]);

        $this->actingAs($customer)->post(route('admin.templates.design-revisions.rollback', [$template, $revision]))
            ->assertForbidden();
    }

    public function test_disabled_template_sections_are_hidden_from_public_invitation_and_navigation(): void
    {
        $template = Template::factory()->create([
            'design_settings' => ['sections' => array_fill_keys(['quote', 'countdown', 'events', 'story', 'gallery', 'rsvp', 'gift', 'wishes'], false)],
        ]);
        $invitation = Invitation::factory()->published()->for($template)->create([
            'slug' => 'section-hidden',
            'data' => [
                'groom' => ['nickname' => 'Haikal', 'full_name' => 'Muhammad Haikal'],
                'bride' => ['nickname' => 'Fitria', 'full_name' => 'Fitria Putri'],
                'wedding_date' => '2026-09-20',
                'quote' => 'Quote khusus pengujian',
                'events' => [['name' => 'Akad Khusus', 'date' => '2026-09-20', 'time' => '08:00', 'location' => 'Lokasi Khusus', 'address' => 'Alamat Khusus']],
                'stories' => [['title' => 'Cerita Khusus', 'story' => 'Isi cerita', 'date' => '2025-01-01']],
                'banks' => [['bank_name' => 'Bank Tes', 'account_number' => '123456789', 'account_name' => 'Pemilik Tes']],
                'settings' => ['countdown' => true, 'gallery' => true, 'story' => true, 'gift' => true, 'rsvp' => true, 'wishes' => true, 'music' => false],
            ],
        ]);
        InvitationMedia::factory()->for($invitation)->create();

        $this->get(route('public.invitation', $invitation->slug))
            ->assertOk()
            ->assertDontSee('Quote khusus pengujian')
            ->assertDontSee('Akad Khusus')
            ->assertDontSee('Cerita Khusus')
            ->assertDontSee('Captured Moments')
            ->assertDontSee('Will You')
            ->assertDontSee('Bank Tes')
            ->assertDontSee('Warm Wishes')
            ->assertDontSee('href="#event"', false)
            ->assertDontSee('href="#rsvp"', false);
    }

    public function test_classic_template_renders_sections_and_navigation_in_published_order(): void
    {
        $template = Template::factory()->create([
            'key' => 'eternal-ivory',
            'design_settings' => ['section_order' => ['events', 'couple', 'quote', 'countdown', 'story', 'gallery', 'rsvp', 'gift', 'wishes']],
        ]);
        $invitation = Invitation::factory()->published()->for($template)->create([
            'slug' => 'classic-order',
            'data' => $this->orderedInvitationData(),
        ]);

        $html = $this->get(route('public.invitation', $invitation->slug))->assertOk()->getContent();

        $this->assertLessThan(strpos($html, 'id="couple"'), strpos($html, 'id="event"'));
        $this->assertLessThan(strpos($html, 'data-nav-link="couple"'), strpos($html, 'data-nav-link="event"'));
    }

    public function test_template_theme_color_uses_custom_background_color(): void
    {
        $template = Template::factory()->create([
            'design_settings' => ['background_color' => '#abcdef'],
        ]);
        $invitation = Invitation::factory()->published()->for($template)->create([
            'slug' => 'custom-theme-color',
            'data' => $this->orderedInvitationData(),
        ]);

        $this->get(route('public.invitation', $invitation->slug))
            ->assertOk()
            ->assertSee('<meta name="theme-color" content="#abcdef">', false);
    }

    public function test_restored_templates_have_distinct_customizer_defaults(): void
    {
        $rustic = Template::factory()->make(['key' => 'rustic-forest']);
        $modern = Template::factory()->make(['key' => 'modern-minimalist']);

        $this->assertSame('#9d7b45', $rustic->designSettings()['primary_color']);
        $this->assertSame('#efe7d8', $rustic->designSettings()['background_color']);
        $this->assertSame('#4f6f78', $modern->designSettings()['primary_color']);
        $this->assertSame('#f7f5f1', $modern->designSettings()['background_color']);
    }

    public function test_restored_template_public_invitations_render_interactive_forms(): void
    {
        foreach ([
            'rustic-forest' => ['templates.rustic-forest.index', 'rf-body'],
            'modern-minimalist' => ['templates.modern-minimalist.index', 'mm-body'],
        ] as $key => [$viewPath, $bodyClass]) {
            $template = Template::factory()->create(['key' => $key, 'view_path' => $viewPath]);
            $invitation = Invitation::factory()->published()->for($template)->create([
                'slug' => $key.'-public',
                'data' => $this->orderedInvitationData(),
            ]);

            $this->get(route('public.invitation', $invitation->slug))
                ->assertOk()
                ->assertSee('class="'.$bodyClass.'"', false)
                ->assertSee('data-async-form="rsvp"', false)
                ->assertSee('data-async-form="wish"', false)
                ->assertSee('data-open-invitation', false)
                ->assertSee('js/templates/editorial-invitation.js', false);
        }
    }

    public function test_midnight_template_reorders_sections_and_recalculates_chapters(): void
    {
        $template = Template::factory()->create([
            'key' => 'midnight-nusantara',
            'view_path' => 'templates.midnight-nusantara.index',
            'design_settings' => ['section_order' => ['agenda', 'couple', 'story', 'gallery', 'guest', 'wishes']],
        ]);
        $invitation = Invitation::factory()->published()->for($template)->create([
            'slug' => 'midnight-order',
            'data' => $this->orderedInvitationData(),
        ]);

        $html = $this->get(route('public.invitation', $invitation->slug))->assertOk()->getContent();

        $this->assertLessThan(strpos($html, 'id="pasangan"'), strpos($html, 'id="agenda"'));
        $this->assertMatchesRegularExpression('/id="agenda".*?<strong>02<\/strong>/s', $html);
        $this->assertMatchesRegularExpression('/id="pasangan".*?<strong>03<\/strong>/s', $html);
    }

    public function test_voxel_template_renders_sections_and_hud_in_published_order(): void
    {
        $template = Template::factory()->create([
            'key' => 'voxel-voyage',
            'view_path' => 'templates.voxel-voyage.index',
            'design_settings' => ['section_order' => ['missions', 'partners', 'map', 'inventory', 'station', 'messages']],
        ]);
        $invitation = Invitation::factory()->published()->for($template)->create([
            'slug' => 'voxel-order',
            'data' => $this->orderedInvitationData(),
        ]);

        $html = $this->get(route('public.invitation', $invitation->slug))->assertOk()->getContent();

        $this->assertLessThan(strpos($html, 'id="partners"'), strpos($html, 'id="missions"'));
        $this->assertLessThan(strpos($html, 'data-vv-nav="partners"'), strpos($html, 'data-vv-nav="missions"'));
    }

    public function test_section_order_must_contain_each_allowed_block_once(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['key' => 'eternal-ivory']);

        $this->actingAs($admin)->patch(route('admin.templates.update', $template), [
            'name' => $template->name,
            'category' => 'Wedding',
            'price' => 150000,
            'design_settings' => [
                'primary_color' => '#123456',
                'text_color' => '#222222',
                'background_color' => '#fafafa',
                'heading_font' => 'serif',
                'body_font' => 'sans',
                'card_radius' => 18,
                'hero_overlay' => 45,
                'sections' => [],
                'section_order' => ['events', 'events', 'unknown'],
            ],
        ])->assertInvalid(['design_settings.section_order', 'design_settings.section_order.1', 'design_settings.section_order.2']);

        $this->assertNull($template->refresh()->design_draft_settings);
    }

    private function orderedInvitationData(): array
    {
        return [
            'groom' => ['nickname' => 'Haikal', 'full_name' => 'Muhammad Haikal'],
            'bride' => ['nickname' => 'Fitria', 'full_name' => 'Fitria Putri'],
            'wedding_date' => '2026-09-20',
            'quote' => 'Quote urutan',
            'events' => [['name' => 'Akad Urutan', 'date' => '2026-09-20', 'time' => '08:00', 'location' => 'Lokasi', 'address' => 'Alamat']],
            'stories' => [],
            'banks' => [],
            'settings' => ['countdown' => true, 'gallery' => true, 'story' => true, 'gift' => true, 'rsvp' => true, 'wishes' => true, 'music' => false],
        ];
    }

    private function validDesignPayload(Template $template): array
    {
        return [
            'name' => $template->name,
            'category' => 'Wedding',
            'price' => 150000,
            'design_settings' => [
                'primary_color' => '#123456',
                'text_color' => '#222222',
                'background_color' => '#fafafa',
                'heading_font' => 'serif',
                'body_font' => 'sans',
                'card_radius' => 18,
                'hero_overlay' => 45,
                'sections' => array_fill_keys(['quote', 'countdown', 'events', 'story', 'gallery', 'rsvp', 'gift', 'wishes'], true),
                'section_order' => array_keys($template->orderableSections()),
            ],
        ];
    }
}
