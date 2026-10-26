@php
    $design = $invitation->template->designSettings();
    $fonts = [
        'serif' => 'Georgia, "Times New Roman", serif',
        'sans' => 'Arial, sans-serif',
        'monospace' => '"Courier New", monospace',
    ];
@endphp
<style data-template-design>
    :root {
        --template-primary: {{ $design['primary_color'] }};
        --template-text: {{ $design['text_color'] }};
        --template-background: {{ $design['background_color'] }};
        --template-heading-font: {{ $fonts[$design['heading_font']] }};
        --template-body-font: {{ $fonts[$design['body_font']] }};
        --template-card-radius: {{ $design['card_radius'] }}px;
        --template-overlay: {{ $design['hero_overlay'] }}%;
        --template-overlay-opacity: {{ $design['hero_overlay'] / 100 }};
        --gold: var(--template-primary);
        --charcoal: var(--template-text);
        --ivory: var(--template-background);
        --paper: var(--template-background);
        --serif: var(--template-heading-font);
        --mn-gold: var(--template-primary);
        --mn-gold-bright: var(--template-primary);
        --mn-cream: var(--template-text);
        --mn-night: var(--template-background);
        --mn-serif: var(--template-heading-font);
        --mn-sans: var(--template-body-font);
        --yellow: var(--template-primary);
        --ink: var(--template-text);
        --cream: var(--template-background);
        --mono: var(--template-heading-font);
        --sans: var(--template-body-font);
    }

    body { font-family: var(--template-body-font); }
    h1, h2, h3 { font-family: var(--template-heading-font); }
    .opening-shade, .mn-gate-wash { background: rgb(0 0 0 / var(--template-overlay-opacity)); }
    .vv-gate::after { position: absolute; z-index: 2; inset: 0; content: ""; pointer-events: none; background: rgb(0 0 0 / var(--template-overlay-opacity)); }
    .vv-gate-photo { opacity: 1; }
    .vv-gate-card { position: relative; z-index: 3; }
    .card, .event-card, .wish-list article, .rsvp-form, .gift,
    .bank-card, .date-panel, .mn-event, .mn-story, .mn-gate-panel,
    .mn-profile figure, .mn-gallery-grid figure, .mn-desk-panel, .mn-bank,
    .vv-gate-card, .vv-console, .vv-date, .vv-quote, .vv-countdown,
    .vv-title-tile, .vv-player, .vv-mission, .vv-route-stop, .vv-bank,
    .vv-gallery figure, .rf-gate-copy, .rf-hero figure, .rf-couple article figure,
    .rf-gallery-grid figure, .rf-event-list article, .rf-bank-grid article,
    .rf-wishes form, .mm-gate-copy, .mm-people article, .mm-people figure,
    .mm-events article, .mm-story article, .mm-gallery figure, .mm-gift article,
    .mm-wishes form {
        border-radius: var(--template-card-radius);
        overflow: hidden;
    }
</style>
