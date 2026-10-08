<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Kebijakan Privasi · {{ config('app.name', 'Undangan Digital') }}</title>
    <meta name="description" content="Kebijakan Privasi layanan {{ config('legal.operator', 'Undangan Digital') }}.">
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
</head>
<body class="landing-body">
<header class="landing-header">
    <div class="landing-container header-inner">
        <a class="landing-brand" href="/" aria-label="{{ config('app.name', 'Undangan Digital') }}">
            <img class="brand-icon" src="{{ asset('logo.webp') }}" alt="" width="42" height="42">
            <span><strong>Undangan</strong><small>Digital Studio</small></span>
        </a>
        <div class="landing-actions">
            <a class="landing-link" href="{{ route('login') }}">Masuk</a>
            <a class="landing-cta" href="{{ route('register') }}">Buat Undangan</a>
        </div>
    </div>
</header>
<main class="landing-container legal-layout">
    <aside class="legal-toc" aria-label="Daftar isi">
        <strong>Daftar Isi</strong>
        <a href="#data">Data yang Dikumpulkan</a>
        <a href="#tujuan">Tujuan Penggunaan</a>
        <a href="#penyimpanan">Penyimpanan &amp; Keamanan</a>
        <a href="#pihak-ketiga">Pihak Ketiga</a>
        <a href="#cookie">Cookie &amp; Sesi</a>
        <a href="#hak">Hak Pengguna</a>
        <a href="#retensi">Retensi Data</a>
        <a href="#tamu">Data Tamu Undangan</a>
        <a href="#perubahan">Perubahan Kebijakan</a>
        <a href="#kontak">Kontak Privasi</a>
    </aside>
    <article class="legal-article">
        <p class="overline">Dokumen Legal</p>
        <h1>Kebijakan Privasi</h1>
        <p class="legal-meta">Berlaku mulai {{ \Carbon\Carbon::parse(config('legal.terms_version', '2026-10-07'))->translatedFormat('d F Y') }} · Versi {{ config('legal.terms_version', '2026-10-07') }} · Pengelola: {{ config('legal.operator', 'Undangan Digital') }}</p>
        <p>Kebijakan ini menjelaskan data yang kami kumpulkan, cara menggunakannya, dan hak Anda. Kebijakan ini merupakan bagian dari <a href="{{ route('terms') }}">Syarat dan Ketentuan</a>.</p>

        <h2 id="data">1. Data yang Dikumpulkan</h2>
        <ul>
            <li><strong>Data akun:</strong> nama, alamat email, kata sandi (tersimpan dalam bentuk terenkripsi), serta waktu dan versi persetujuan Syarat dan Ketentuan.</li>
            <li><strong>Data undangan:</strong> nama mempelai dan keluarga, tanggal dan detail acara, kisah cinta, rekening hadiah, kutipan, dan pengaturan tampilan.</li>
            <li><strong>Data tamu:</strong> nama tamu, nomor WhatsApp, konfirmasi kehadiran (RSVP), dan ucapan yang dikirim melalui undangan.</li>
            <li><strong>Media:</strong> foto sampul, foto mempelai, galeri, QRIS, foto cerita, tautan musik YouTube, dan bukti pembayaran transfer manual.</li>
        </ul>

        <h2 id="tujuan">2. Tujuan Penggunaan</h2>
        <ul>
            <li>Menyediakan, mengoperasikan, dan memelihara layanan undangan digital.</li>
            <li>Memverifikasi pembayaran dan mencegah penyalahgunaan layanan.</li>
            <li>Menampilkan undangan, RSVP, dan ucapan kepada tamu yang Anda undang.</li>
            <li>Menghubungi Anda terkait akun, pembayaran, atau permintaan bantuan.</li>
        </ul>

        <h2 id="penyimpanan">3. Penyimpanan &amp; Keamanan</h2>
        <ul>
            <li>Data disimpan pada server aplikasi dan dilindungi dengan kontrol akses berbasis peran (admin dan pelanggan).</li>
            <li>Kata sandi tidak disimpan dalam bentuk teks biasa.</li>
            <li>Kami menerapkan langkah keamanan yang wajar, tetapi tidak ada sistem yang sepenuhnya kebal terhadap gangguan.</li>
        </ul>

        <h2 id="pihak-ketiga">4. Pihak Ketiga</h2>
        <ul>
            <li><strong>Midtrans:</strong> memproses pembayaran otomatis. Data transaksi tunduk pada kebijakan privasi Midtrans.</li>
            <li><strong>YouTube:</strong> memutar musik undangan bila Anda menambahkan tautan video. Interaksi dengan pemutar tunduk pada kebijakan YouTube/Google.</li>
            <li>Kami tidak menjual data pribadi Anda kepada pihak lain.</li>
        </ul>

        <h2 id="cookie">5. Cookie &amp; Sesi</h2>
        <p>Kami menggunakan cookie dan data sesi untuk menjaga status masuk, preferensi dasar, dan keamanan formulir. Menonaktifkan cookie dapat menyebabkan sebagian fitur tidak berfungsi.</p>

        <h2 id="hak">6. Hak Pengguna</h2>
        <ul>
            <li>Meminta salinan, koreksi, atau penghapusan data akun Anda.</li>
            <li>Menonaktifkan undangan yang dipublikasikan melalui dasbor Anda.</li>
            <li>Menghapus media atau data undangan yang tidak lagi diperlukan.</li>
            <li>Mengajukan permintaan melalui WhatsApp layanan dengan menyertakan email akun Anda.</li>
        </ul>

        <h2 id="retensi">7. Retensi Data</h2>
        <p>Data akun dan undangan disimpan selama akun Anda aktif. Bila akun dihapus, data terkait akan dihapus atau dianonimkan, kecuali wajib disimpan untuk memenuhi kewajiban hukum atau penyelesaian sengketa.</p>

        <h2 id="tamu">8. Data Tamu Undangan</h2>
        <p>Anda bertanggung jawab memastikan tamu mengetahui bahwa nama, konfirmasi kehadiran, dan ucapan mereka akan tampil pada undangan sesuai pengaturan yang Anda pilih. Kami memproses data tamu hanya untuk menyediakan fitur undangan Anda.</p>

        <h2 id="perubahan">9. Perubahan Kebijakan</h2>
        <p>Kebijakan ini dapat diperbarui sewaktu-waktu. Versi terbaru selalu tersedia pada halaman ini beserta tanggal berlakunya.</p>

        <h2 id="kontak">10. Kontak Privasi</h2>
        <p>Permintaan akses, koreksi, atau penghapusan data dapat disampaikan melalui WhatsApp layanan:</p>
        <p><a class="landing-cta" href="https://wa.me/{{ config('legal.whatsapp', '62882006381163') }}" target="_blank" rel="noopener">Hubungi 0882006381163</a></p>
    </article>
</main>
<footer class="landing-footer">
    <div class="landing-container footer-bottom">
        <span>© {{ now()->year }} {{ config('app.name', 'Undangan Digital') }}.</span>
        <span><a href="{{ route('terms') }}">Syarat dan Ketentuan</a> · <a href="{{ route('privacy') }}">Kebijakan Privasi</a></span>
    </div>
</footer>
</body>
</html>
