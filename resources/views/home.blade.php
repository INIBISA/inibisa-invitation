<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', 'Undangan Digital') }} — Undangan Pernikahan Digital yang Elegan</title>
    <meta name="description" content="Buat undangan pernikahan digital yang elegan, bagikan dengan satu tautan, dan kelola RSVP serta ucapan tamu dalam satu workspace yang rapi.">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script src="{{ asset('js/landing.js') }}" defer></script>
</head>
<body class="landing-body">
<header class="landing-header" data-landing-header>
    <div class="landing-container header-inner">
        <a class="landing-brand" href="/" aria-label="{{ config('app.name', 'Undangan Digital') }}">
            <span class="brand-mark"><span>U</span></span>
            <span><strong>Undangan</strong><small>Digital Studio</small></span>
        </a>
        <nav class="landing-nav" aria-label="Navigasi utama">
            <a href="#fitur">Fitur</a>
            <a href="#cara-kerja">Cara Kerja</a>
            <a href="#template">Template</a>
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
        <a href="#fitur">Fitur</a>
        <a href="#cara-kerja">Cara Kerja</a>
        <a href="#template">Template</a>
        <a href="#faq">FAQ</a>
        <div class="landing-mobile-actions">
            <a href="{{ route('login') }}">Masuk</a>
            <a class="landing-cta" href="{{ route('register') }}">Buat Undangan</a>
        </div>
    </div>
</header>

<main>
    <section class="landing-hero">
        <img class="landing-hero-floral landing-hero-floral--left" src="{{ asset('images/templates/eternal-ivory/floral-corner-left.webp') }}" alt="" width="720" height="720" decoding="async" data-parallax="0.12">
        <img class="landing-hero-floral landing-hero-floral--right" src="{{ asset('images/templates/eternal-ivory/floral-corner-right.webp') }}" alt="" width="720" height="720" decoding="async" data-parallax="0.08">
        <div class="landing-container hero-grid">
            <div class="hero-copy" data-reveal>
                <p class="hero-kicker">Website undangan pernikahan premium</p>
                <h1>Undangan digital yang terasa personal, elegan, dan mudah dibagikan.</h1>
                <p class="hero-lead">Buat undangan pernikahan yang rapi dan berkesan tanpa proses yang rumit. Satu tautan untuk dibagikan, satu dashboard untuk mengelola RSVP, ucapan, dan detail acara.</p>
                <div class="hero-cta">
                    <a class="primary-button hero-primary" href="{{ route('register') }}">Buat Undangan Sekarang <svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></svg></a>
                    <a class="secondary-button" href="{{ route('login') }}">Masuk ke Workspace</a>
                </div>
                <p class="hero-note">Tanpa instalasi. Undangan langsung siap dibagikan setelah disimpan dan dipublikasikan.</p>
                <div class="hero-proof">
                    <span><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg> Editor sederhana</span>
                    <span><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg> Mobile-friendly</span>
                    <span><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg> RSVP real-time</span>
                </div>
            </div>
            <div class="hero-preview" data-reveal>
                <div class="device">
                    <div class="device-top"><span></span><span></span><span></span><small>undangan.digital/haikal-fitria</small></div>
                    <div class="device-screen">
                        <img class="preview-wreath" src="{{ asset('images/templates/eternal-ivory/floral-wreath.webp') }}" alt="" width="520" height="520" decoding="async">
                        <p class="preview-kicker">The Wedding Of</p>
                        <h2>Haikal &amp; Fitria</h2>
                        <p class="preview-date">Sabtu, 14 September 2026</p>
                        <img class="preview-divider" src="{{ asset('images/templates/eternal-ivory/floral-divider.webp') }}" alt="" width="320" height="60" decoding="async">
                        <div class="preview-cards">
                            <article><strong>Akad Nikah</strong><span>09.00 WIB</span><small>Gedung Serbaguna</small></article>
                            <article><strong>Resepsi</strong><span>11.00 WIB</span><small>Gedung Serbaguna</small></article>
                        </div>
                        <div class="preview-actions"><span>Konfirmasi Kehadiran</span><span class="ghost">Kirim Ucapan</span></div>
                        <p class="preview-foot">Bagikan satu tautan, tamu dapat membuka undangan di semua perangkat.</p>
                    </div>
                </div>
                <div class="device-caption">
                    <strong>Preview undangan publik</strong>
                    <span>Tamu membuka undangan tanpa perlu login, langsung RSVP dan mengirim ucapan.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-section" id="fitur">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">Apa yang kamu dapatkan</p>
                <h2>Semua kebutuhan undangan dalam satu tempat.</h2>
                <p>Dirancang untuk pasangan yang ingin hasil premium tanpa proses yang melelahkan.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card wide" data-reveal><span class="feature-icon gold"><svg viewBox="0 0 24 24"><path d="M4 4h16v16H4zM8 8h8M8 12h5M8 16h7"/></svg></span><h3>Editor yang rapi dan mudah</h3><p>Atur detail pasangan, acara, galeri, dan kutipan pernikahan dalam form yang terstruktur. Perubahan langsung tercermin pada preview undangan.</p></article>
                <article class="feature-card" data-reveal><span class="feature-icon rose"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span><h3>Link undangan publik</h3><p>Setiap undangan memiliki tautan unik yang bisa dibagikan lewat WhatsApp atau media lain.</p></article>
                <article class="feature-card" data-reveal><span class="feature-icon green"><svg viewBox="0 0 24 24"><path d="M5 3v3M19 3v3M4 8h16M5 5h14a2 2 0 0 1 2 2v13H3V7a2 2 0 0 1 2-2Z"/><path d="m8 14 2 2 5-5"/></svg></span><h3>RSVP yang tertata otomatis</h3><p>Kehadiran dan jumlah tamu tercatat langsung di dashboard, memudahkan persiapan acara.</p></article>
                <article class="feature-card" data-reveal><span class="feature-icon blue"><svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z"/></svg></span><h3>Ucapan tamu yang hangat</h3><p>Tamu dapat meninggalkan pesan dan doa yang tampil cantik di halaman undangan.</p></article>
                <article class="feature-card" data-reveal><span class="feature-icon gold"><svg viewBox="0 0 24 24"><path d="M12 2 15 6l4 1-3 3 1 4-5-2-5 2 1-4-3-3 4-1z"/><circle cx="12" cy="14" r="5"/></svg></span><h3>Tampil premium di semua perangkat</h3><p>Template dirancang editorial, ringan, dan nyaman dibaca di ponsel maupun desktop.</p></article>
                <article class="feature-card wide" data-reveal><span class="feature-icon muted"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM4 7l8 6 8-6"/><path d="M12 13a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/></svg></span><h3>Publikasi yang fleksibel</h3><p>Simpan sebagai draft, publikasikan saat siap, atau nonaktifkan kembali tanpa menghapus data undangan.</p></article>
            </div>
        </div>
    </section>

    <section class="landing-story">
        <div class="landing-container story-grid">
            <div class="story-copy" data-reveal>
                <p class="overline">Preview pengalaman tamu</p>
                <h2>Tamu merasakan undangan yang hidup, bukan sekadar halaman statis.</h2>
                <p>Bagian cover yang menyambut nama tamu, timeline acara yang jelas, galeri, countdown, sampai RSVP dan wishes dibuat mengalir dalam satu cerita yang utuh.</p>
                <ul class="story-list">
                    <li><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg><span><strong>Alur tamu yang halus</strong><small>Dari opening, profil pasangan, jadwal acara, sampai konfirmasi kehadiran.</small></span></li>
                    <li><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg><span><strong>Informasi yang mudah dipahami</strong><small>Tanggal, lokasi, dan detail acara tampil jelas tanpa membuat tamu bingung.</small></span></li>
                    <li><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg><span><strong>Interaksi yang natural</strong><small>Tamu bisa RSVP, mengirim ucapan, dan melihat undangan secara ringan tanpa loading berat.</small></span></li>
                </ul>
                <a class="text-link" href="{{ route('register') }}">Buat preview undanganmu <svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></svg></a>
            </div>
            <div class="story-preview" data-reveal>
                <img class="story-bouquet" src="{{ asset('images/templates/eternal-ivory/floral-bouquet.webp') }}" alt="" width="520" height="520" decoding="async">
                <article class="story-card"><span>Undangan Aktif</span><h3>Eternal Ivory</h3><p>Nuansa ivory yang tenang dengan ornamen floral klasik. Cocok untuk pernikahan yang ingin tampil timeless dan berkelas.</p><div class="story-meta"><span><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM4 7l8 6 8-6"/></svg> RSVP</span><span><svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z"/></svg> Wishes</span><span><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1 1.55V21h-4v-.08A1.7 1.7 0 0 0 9 19.37a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.63 15a1.7 1.7 0 0 0-1.55-1H3v-4h.08A1.7 1.7 0 0 0 4.63 9a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.63a1.7 1.7 0 0 0 1-1.55V3h4v.08A1.7 1.7 0 0 0 15 4.63a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.37 9a1.7 1.7 0 0 0 1.55 1H21v4h-.08a1.7 1.7 0 0 0-1.52 1Z"/></svg> Gift</span></div></article>
            </div>
        </div>
    </section>

    <section class="landing-section" id="cara-kerja">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">Cara kerja</p>
                <h2>Tiga langkah menuju undangan yang siap dibagikan.</h2>
            </div>
            <div class="steps-grid">
                <article class="step-card" data-reveal><span class="step-number">01</span><h3>Daftar dan verifikasi akun</h3><p>Buat akun customer, lalu akun akan diaktifkan oleh admin agar workspace undanganmu siap digunakan.</p></article>
                <article class="step-card" data-reveal><span class="step-number">02</span><h3>Isi detail undangan</h3><p>Lengkapi nama pasangan, jadwal, lokasi, cerita, foto, dan pesan. Semua tersimpan rapi dan bisa diubah kapan pun.</p></article>
                <article class="step-card" data-reveal><span class="step-number">03</span><h3>Publikasikan dan bagikan</h3><p>Terbitkan undangan, bagikan tautannya, lalu pantau RSVP dan ucapan tamu langsung dari dashboard.</p></article>
            </div>
        </div>
    </section>

    <section class="landing-section soft" id="template">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">Template</p>
                <h2>Desain yang matang, bukan pilihan yang membingungkan.</h2>
                <p>Kami fokus pada template yang benar-benar siap pakai dan terasa premium.</p>
            </div>
            <div class="template-showcase" data-reveal>
                <img src="{{ asset('images/templates/eternal-ivory/thumbnail.svg') }}" alt="Preview template Eternal Ivory" width="640" height="480" decoding="async">
                <div>
                    <span class="template-badge">Aktif sekarang</span>
                    <h3>Eternal Ivory</h3>
                    <p>Template editorial yang memadukan nuansa ivory, serif yang anggun, dan ornamen floral yang halus. Cocok untuk pernikahan modern yang ingin tetap klasik.</p>
                    <ul>
                        <li><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg> Cover personal untuk setiap tamu</li>
                        <li><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg> Galeri, timeline, countdown, dan gift section</li>
                        <li><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg> RSVP dan wishes yang sudah terintegrasi</li>
                    </ul>
                    <a class="landing-cta" href="{{ route('register') }}">Buat Undangan dengan Template Ini</a>
                    <small class="template-note">Template baru akan ditambahkan secara bertahap agar kualitas tetap terjaga.</small>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-section" id="benefit">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">Kenapa pasangan memilih kami</p>
                <h2>Lebih hemat, lebih praktis, dan tetap berkesan.</h2>
            </div>
            <div class="benefit-grid">
                <article data-reveal><h3>Lebih praktis dari undangan fisik</h3><p>Tidak perlu cetak ulang saat ada perubahan. Update detail acara kapan pun dan tamu langsung melihat versi terbaru.</p></article>
                <article data-reveal><h3>Pengelolaan tamu yang rapi</h3><p>Semua konfirmasi kehadiran dan ucapan tercatat dalam satu workspace yang mudah dipantau.</p></article>
                <article data-reveal><h3>Pengalaman tamu yang lebih baik</h3><p>Undangan dapat dibuka di ponsel dalam hitungan detik, tanpa perlu aplikasi tambahan.</p></article>
            </div>
        </div>
    </section>

    <section class="landing-section soft" id="faq">
        <div class="landing-container">
            <div class="section-head" data-reveal>
                <p class="overline">FAQ</p>
                <h2>Pertanyaan yang paling sering ditanyakan.</h2>
            </div>
            <div class="faq-list" data-reveal>
                <details open><summary>Apakah undangan bisa langsung dibagikan setelah dibuat? <svg viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></summary><p>Ya. Setelah kamu menyimpan dan mempublikasikan undangan, tautan publik langsung aktif dan bisa dibagikan ke tamu.</p></details>
                <details><summary>Bagaimana proses aktivasi akun customer? <svg viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></summary><p>Setelah mendaftar, akun perlu disetujui oleh admin agar kamu bisa masuk ke dashboard. Ini menjaga workspace tetap aman dan terkontrol.</p></details>
                <details><summary>Apakah tamu harus login untuk RSVP atau mengirim ucapan? <svg viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></summary><p>Tidak. Tamu cukup membuka tautan undangan dan langsung bisa mengisi kehadiran serta meninggalkan ucapan tanpa membuat akun.</p></details>
                <details><summary>Bisakah undangan diperbarui setelah dibagikan? <svg viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></summary><p>Bisa. Kamu dapat mengedit undangan kapan pun. Perubahan akan langsung terlihat pada tautan yang sama tanpa perlu membagikan ulang.</p></details>
            </div>
        </div>
    </section>

    <section class="landing-cta-section">
        <div class="landing-container">
            <div class="cta-panel" data-reveal>
                <img class="cta-floral" src="{{ asset('images/templates/eternal-ivory/floral-accent.webp') }}" alt="" width="260" height="260" decoding="async">
                <p class="overline light">Mulai sekarang</p>
                <h2>Buat undangan yang benar-benar mewakili cerita kalian.</h2>
                <p>Dari detail kecil sampai momen besar, semuanya dirangkai dalam undangan digital yang rapi dan mudah dibagikan.</p>
                <div class="cta-actions">
                    <a class="primary-button" href="{{ route('register') }}">Buat Undangan <svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></svg></a>
                    <a class="ghost-button" href="{{ route('login') }}">Masuk ke Akun</a>
                </div>
                <small>Sudah punya akun dan disetujui admin? Langsung masuk dan lanjutkan undanganmu.</small>
            </div>
        </div>
    </section>
</main>

<footer class="landing-footer">
    <div class="landing-container footer-grid">
        <div>
            <a class="landing-brand" href="/">
                <span class="brand-mark light"><span>U</span></span>
                <span><strong>Undangan</strong><small>Digital Studio</small></span>
            </a>
            <p>Undangan pernikahan digital yang dirancang untuk tampil premium, mudah dikelola, dan nyaman dibuka di semua perangkat.</p>
        </div>
        <div>
            <strong>Menu</strong>
            <a href="#fitur">Fitur</a>
            <a href="#cara-kerja">Cara Kerja</a>
            <a href="#template">Template</a>
            <a href="#faq">FAQ</a>
        </div>
        <div>
            <strong>Akses</strong>
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}">Daftar</a>
            <a href="{{ route('login') }}">Bantuan akun</a>
        </div>
    </div>
    <div class="landing-container footer-bottom">
        <span>© {{ now()->year }} {{ config('app.name', 'Undangan Digital') }}. Crafted with care.</span>
        <span>Beautiful moments, thoughtfully shared.</span>
    </div>
</footer>
</body>
</html>
