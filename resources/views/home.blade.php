<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', 'Undangan Digital') }} - Undangan Pernikahan Digital yang Elegan</title>
    <meta name="description" content="Buat undangan pernikahan digital yang elegan, bagikan dengan satu tautan, dan kelola RSVP serta ucapan tamu dalam satu dashboard.">
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
    <script src="{{ asset('js/landing.js') }}" defer></script>
</head>
<body class="landing-body">
<!-- HEADER / NAVBAR -->
<header class="landing-header" data-landing-header>
    <div class="landing-container header-inner">
        <a class="landing-brand" href="/" aria-label="{{ config('app.name', 'Undangan Digital') }}">
            <img class="brand-icon" src="{{ asset('logo.webp') }}" alt="" width="42" height="42">
            <span><strong>Undangan</strong><small>Digital Studio</small></span>
        </a>
        <nav class="landing-nav" aria-label="Navigasi utama">
            <a href="#cara-kerja">Alur Pembuatan</a>
            <a href="#template">Template</a>
            <a href="#fitur">Fitur</a>
            <a href="#faq">FAQ</a>
        </nav>
        <div class="landing-actions">
            <a class="landing-link" href="{{ route('login') }}">Masuk</a>
            <a class="landing-cta" href="{{ route('register') }}">Buat Undangan</a>
        </div>
        <button class="landing-burger" type="button" data-landing-menu-open aria-expanded="false" aria-controls="landing-mobile-menu" aria-label="Buka menu">
            <span></span><span></span><span></span>
        </button>
    </div>
    <div class="landing-mobile" id="landing-mobile-menu" data-landing-mobile hidden>
        <a href="#cara-kerja">Alur Pembuatan</a>
        <a href="#template">Template</a>
        <a href="#fitur">Fitur</a>
        <a href="#faq">FAQ</a>
        <div class="landing-mobile-actions">
            <a href="{{ route('login') }}">Masuk</a>
            <a class="landing-cta" href="{{ route('register') }}">Buat Undangan</a>
        </div>
    </div>
</header>

<main>
    <!-- HERO -->
    <section class="landing-hero">
        <img class="landing-hero-floral landing-hero-floral--left" src="{{ asset('images/templates/eternal-ivory/floral-corner-left.webp') }}" alt="" width="720" height="720" decoding="async" data-parallax="0.12">
        <img class="landing-hero-floral landing-hero-floral--right" src="{{ asset('images/templates/eternal-ivory/floral-corner-right.webp') }}" alt="" width="720" height="720" decoding="async" data-parallax="0.08">
        <div class="landing-container hero-grid">
            <div class="hero-copy" data-reveal>
                <p class="hero-kicker">Website undangan pernikahan</p>
                <h1>Bikin Undangan Mewah Tanpa Ribet, Cukup Dalam Hitungan Menit.</h1>
                <p class="hero-lead">Persiapan pernikahan jadi lebih tenang. Cukup sebar satu tautan cantik, lalu biarkan sistem mengelola konfirmasi hadir, lokasi, hingga pesan dari para tamu.</p>
                <div class="hero-cta">
                    <a class="primary-button hero-primary" href="{{ route('register') }}">Buat Undangan Gratis <svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></svg></a>
                    <a class="secondary-button" href="#template">Lihat Template</a>
                </div>
            </div>
            <div class="hero-preview" data-reveal>
                <div class="device">
                    <div class="device-top"><span></span><span></span><span></span><small>undangan.digital/alexander-cyntia</small></div>
                    <div class="device-screen">
                        <img class="preview-wreath" src="{{ asset('images/templates/eternal-ivory/floral-wreath.webp') }}" alt="" width="520" height="520" decoding="async">
                        <p class="preview-kicker">The Wedding Of</p>
                        <h2>Alexander &amp; Cyntia</h2>
                        <p class="preview-date">Sabtu, 14 September 2026</p>
                        <img class="preview-divider" src="{{ asset('images/templates/eternal-ivory/floral-divider.webp') }}" alt="" width="320" height="60" decoding="async">
                        <div class="preview-cards">
                            <article><strong>Akad Nikah</strong><span>09.00 WIB</span><small>Gedung Serbaguna</small></article>
                            <article><strong>Resepsi</strong><span>11.00 WIB</span><small>Gedung Serbaguna</small></article>
                        </div>
                        <div class="preview-actions"><span>Konfirmasi Kehadiran</span><span class="ghost">Kirim Ucapan</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <!-- CARA KERJA -->
    <section class="landing-section" id="cara-kerja">
        <div class="landing-container">

            <div class="section-head" data-reveal>
                <p class="overline">Alur Pembuatan</p>
                <h2>Tiga langkah menuju undangan siap dibagikan.</h2>
            </div>

            <div class="steps-grid">

                <!-- STEP 01 -->
                <article class="step-card" data-reveal>
                    <div class="step-card-header">
                        <span class="step-number">01</span>

                        <div class="step-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="22" height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                style="width:22px;height:22px;display:block;flex:none;">
                                <path d="M12 3l2.1 2.1 3-.4.4 3L20 10l-2.5 2.3-.4 3-3-.4L12 17l-2.1-2.1-3 .4-.4-3L4 10l2.5-2.3.4-3 3 .4L12 3Z"/>
                                <path d="m9.5 10 1.6 1.6 3.4-3.4"/>
                            </svg>
                        </div>
                    </div>

                    <h3>Daftar akun</h3>
                    <p>Buat akun, admin akan mengaktifkannya untukmu.</p>
                </article>

                <!-- STEP 02 -->
                <article class="step-card" data-reveal>
                    <div class="step-card-header">
                        <span class="step-number">02</span>

                        <div class="step-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="22" height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                style="width:22px;height:22px;display:block;flex:none;">
                                <rect x="4" y="5" width="16" height="14" rx="1.5"/>
                                <path d="M4 7.5 12 13l8-5.5"/>
                                <path d="M8 9.5h8"/>
                            </svg>
                        </div>
                    </div>

                    <h3>Isi detail undangan</h3>
                    <p>Lengkapi nama, jadwal, lokasi, foto, dan pesan.</p>
                </article>

                <!-- STEP 03 -->
                <article class="step-card" data-reveal>
                    <div class="step-card-header">
                        <span class="step-number">03</span>

                        <div class="step-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="22" height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                style="width:22px;height:22px;display:block;flex:none;">
                                <path d="M10 13a5 5 0 0 0 7.1.1l1.8-1.8a5 5 0 0 0-7.1-7.1L10.8 5"/>
                                <path d="M14 11a5 5 0 0 0-7.1-.1l-1.8 1.8a5 5 0 0 0 7.1 7.1l1-1"/>
                            </svg>
                        </div>
                    </div>

                    <h3>Publikasikan &amp; bagikan</h3>
                    <p>Terbitkan tautan, lalu pantau RSVP dari dashboard.</p>
                </article>

            </div>
        </div>
    </section>


    <!-- TEMPLATE -->
    <section class="landing-section soft" id="template">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">Template</p>
                <h2>Pilih desain favoritmu.</h2>
                <p>Buka demonya dulu sebelum memutuskan.</p>
            </div>
            <div class="template-grid">
                @foreach($templates as $template)
                @php($demo = ($demos ?? collect())->get($template->id))
                <article class="template-card" data-reveal>
                    <img src="{{ asset($template->thumbnail) }}" alt="Preview template {{ $template->name }}" loading="lazy" decoding="async">
                    <div class="template-card-body">
                        <h3>{{ $template->name }}</h3>
                        <p>{{ $descriptions[$template->key] ?? 'Desain premium yang siap pakai untuk hari bahagiamu.' }}</p>
                        <div class="template-card-actions">
                            @if($demo)
                            <a class="secondary-button" href="{{ route('templates.show', $template) }}" target="_blank" rel="noopener">Lihat Demo</a>
                            @endif
                            <a class="landing-cta" href="{{ route('register') }}">Buat Undangan</a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FITUR -->
    <section class="landing-section" id="fitur">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">Fitur</p>
                <h2>Detail kecil hingga momen besar, semua ada di sini!</h2>
            </div>
            <div class="feature-grid">
                <article class="feature-card" data-reveal><span class="feature-icon gold"><svg viewBox="0 0 24 24"><path d="M4 4h16v16H4zM8 8h8M8 12h5M8 16h7"/></svg></span><h3>Mudah disiapkan</h3><p>Isi detail pasangan, acara, dan galeri lewat form terstruktur.</p></article>
                <article class="feature-card" data-reveal><span class="feature-icon green"><svg viewBox="0 0 24 24"><path d="M5 3v3M19 3v3M4 8h16M5 5h14a2 2 0 0 1 2 2v13H3V7a2 2 0 0 1 2-2Z"/><path d="m8 14 2 2 5-5"/></svg></span><h3>Mudah memantau kehadiran</h3><p>Pantau kehadiran dan ucapan hangat dari orang terdekat.</p></article>
                <article class="feature-card" data-reveal><span class="feature-icon rose"><svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z"/></svg></span><h3>Elegan dari layar mana pun</h3><p>Dari ponsel hingga desktop, undangan tetap tampil elegan.</p></article>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="landing-section" id="faq">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">FAQ</p>
                <h2>Pertanyaan yang sering ditanyakan.</h2>
            </div>
            <div class="faq-list" data-reveal>
                <details open><summary>Apakah undangan bisa langsung dibagikan? <svg viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></summary><p>Ya. Setelah dipublikasikan, tautan langsung aktif dan bisa dibagikan.</p></details>
                <details><summary>Apakah tamu harus login untuk RSVP? <svg viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></summary><p>Tidak. Tamu cukup membuka tautan undangan.</p></details>
                <details><summary>Bisakah undangan diubah setelah dibagikan? <svg viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></summary><p>Bisa. Perubahan langsung terlihat di tautan yang sama.</p></details>
            </div>
        </div>
    </section>

    <!-- CTA PENUTUP -->
    <section class="landing-cta-section">
        <div class="landing-container">
            <div class="cta-panel" data-reveal>
                <img class="cta-floral" src="{{ asset('images/templates/eternal-ivory/floral-accent.webp') }}" alt="" width="260" height="260" decoding="async">
                <p class="overline light">Mulai sekarang</p>
                <h2>Ceritakan momen bahagiamu lewat undangan digital.</h2>
                <div class="cta-actions">
                    <a class="primary-button" href="{{ route('register') }}">Buat Undangan <svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></svg></a>
                    <a class="ghost-button" href="{{ route('login') }}">Masuk ke Akun</a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer class="landing-footer">
    <div class="landing-container footer-grid">
        <div>
            <a class="landing-brand" href="/" aria-label="Undangan Digital Studio">
                <img class="brand-wordmark" src="{{ asset('logotext.webp') }}" alt="" width="230" height="102" loading="lazy">
            </a>
            <p>Undangan pernikahan digital yang premium dan mudah dikelola.</p>
        </div>
        <div>
            <strong>Menu</strong>
            <a href="#cara-kerja">Alur Pembuatan</a>
            <a href="#template">Template</a>
            <a href="#fitur">Fitur</a>
            <a href="#faq">FAQ</a>
        </div>
        <div>
            <strong>Akses</strong>
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}">Daftar</a>
        </div>
    </div>
    <div class="landing-container footer-bottom">
        <span>&copy; {{ now()->year }} {{ config('app.name', 'Undangan Digital') }}.</span>
        <span><a href="{{ route('terms') }}">Syarat dan Ketentuan</a> &middot; <a href="{{ route('privacy') }}">Kebijakan Privasi</a></span>
    </div>
</footer>
</body>
</html>
