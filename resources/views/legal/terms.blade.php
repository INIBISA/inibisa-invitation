<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Syarat dan Ketentuan · {{ config('app.name', 'Undangan Digital') }}</title>
    <meta name="description" content="Syarat dan Ketentuan penggunaan layanan {{ config('legal.operator', 'Undangan Digital') }}.">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="landing-body">
<header class="landing-header">
    <div class="landing-container header-inner">
        <a class="landing-brand" href="/" aria-label="{{ config('app.name', 'Undangan Digital') }}">
            <span class="brand-mark"><span>U</span></span>
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
        <a href="#tentang">Tentang Layanan</a>
        <a href="#akun">Akun &amp; Keamanan</a>
        <a href="#penggunaan">Penggunaan Layanan</a>
        <a href="#konten">Konten Pengguna</a>
        <a href="#pembayaran">Pembayaran</a>
        <a href="#pengembalian">Pengembalian Dana</a>
        <a href="#pihak-ketiga">Layanan Pihak Ketiga</a>
        <a href="#ketersediaan">Ketersediaan Layanan</a>
        <a href="#penangguhan">Penangguhan Akun</a>
        <a href="#tanggung-jawab">Batas Tanggung Jawab</a>
        <a href="#perubahan">Perubahan Ketentuan</a>
        <a href="#kontak">Kontak</a>
    </aside>
    <article class="legal-article">
        <p class="overline">Dokumen Legal</p>
        <h1>Syarat dan Ketentuan</h1>
        <p class="legal-meta">Berlaku mulai {{ \Carbon\Carbon::parse(config('legal.terms_version', '2026-10-07'))->translatedFormat('d F Y') }} · Versi {{ config('legal.terms_version', '2026-10-07') }} · Pengelola: {{ config('legal.operator', 'Undangan Digital') }}</p>
        <p>Dengan mendaftar dan menggunakan layanan {{ config('legal.operator', 'Undangan Digital') }}, Anda dianggap telah membaca, memahami, dan menyetujui seluruh Syarat dan Ketentuan ini beserta <a href="{{ route('privacy') }}">Kebijakan Privasi</a>. Jika Anda tidak setuju, jangan gunakan layanan ini.</p>

        <h2 id="tentang">1. Tentang Layanan</h2>
        <p>{{ config('legal.operator', 'Undangan Digital') }} menyediakan layanan pembuatan undangan pernikahan digital, termasuk pemilihan templat, pengisian data acara, pengelolaan tamu, RSVP, ucapan, dan berbagi tautan undangan.</p>

        <h2 id="akun">2. Akun &amp; Keamanan</h2>
        <ul>
            <li>Anda wajib memberikan nama dan alamat email yang benar saat mendaftar.</li>
            <li>Anda bertanggung jawab menjaga kerahasiaan kata sandi dan seluruh aktivitas yang terjadi pada akun Anda.</li>
            <li>Segera beri tahu kami melalui WhatsApp bila menduga akun Anda disalahgunakan.</li>
            <li>Satu akun digunakan oleh pemiliknya; jangan membagikan akses akun kepada pihak yang tidak berhak.</li>
        </ul>

        <h2 id="penggunaan">3. Penggunaan Layanan</h2>
        <ul>
            <li>Gunakan layanan hanya untuk keperluan yang sah dan tidak melanggar hukum yang berlaku di Indonesia.</li>
            <li>Dilarang mengunggah konten yang melanggar hukum, pornografi, ujaran kebencian, penipuan, atau melanggar hak pihak lain.</li>
            <li>Dilarang merusak, membebani, atau mencoba mengakses sistem secara tidak sah.</li>
            <li>Kami dapat menghapus atau menonaktifkan konten yang melanggar ketentuan ini.</li>
        </ul>

        <h2 id="konten">4. Konten Pengguna</h2>
        <ul>
            <li>Hak atas foto, teks, dan data undangan tetap milik Anda.</li>
            <li>Anda memberikan izin teknis kepada kami untuk menyimpan, memproses, dan menampilkan konten tersebut agar undangan dapat berfungsi dan dibagikan melalui tautan.</li>
            <li>Anda menjamin konten yang diunggah adalah milik Anda atau Anda berhak menggunakannya.</li>
        </ul>

        <h2 id="pembayaran">5. Pembayaran</h2>
        <ul>
            <li>Harga setiap templat tercantum pada halaman pemilihan templat dan dibayar sebelum undangan dapat dibuat.</li>
            <li>Metode pembayaran yang tersedia ditentukan oleh admin dan ditampilkan pada halaman pembayaran.</li>
            <li>Pembayaran melalui transfer manual diverifikasi oleh admin setelah Anda mengunggah bukti pembayaran.</li>
            <li>Pembayaran melalui Midtrans diproses otomatis oleh Midtrans sesuai status transaksi.</li>
        </ul>

        <h2 id="pengembalian">6. Pengembalian Dana</h2>
        <ul>
            <li>Pengembalian dana dapat diajukan <strong>sebelum</strong> templat digunakan untuk membuat undangan.</li>
            <li>Setelah templat digunakan untuk membuat undangan, pembayaran <strong>tidak dapat dikembalikan</strong>.</li>
            <li>Pengecualian berlaku untuk kegagalan sistem dari pihak kami atau pembayaran ganda yang terbukti.</li>
            <li>Pengajuan dilakukan melalui WhatsApp layanan dengan menyertakan nama, email akun, dan bukti pembayaran.</li>
        </ul>

        <h2 id="pihak-ketiga">7. Layanan Pihak Ketiga</h2>
        <p>Layanan ini menggunakan pihak ketiga seperti Midtrans untuk pembayaran dan YouTube untuk pemutaran musik. Penggunaan layanan pihak ketiga tunduk pada ketentuan masing-masing penyedia.</p>

        <h2 id="ketersediaan">8. Ketersediaan Layanan</h2>
        <p>Kami berupaya menjaga layanan tersedia setiap saat, tetapi tidak menjamin layanan bebas gangguan. Pemeliharaan terjadwal atau gangguan teknis dapat menyebabkan layanan tidak dapat diakses sementara waktu.</p>

        <h2 id="penangguhan">9. Penangguhan Akun</h2>
        <p>Kami dapat menangguhkan atau menghapus akun dan konten yang melanggar Syarat dan Ketentuan ini, melanggar hukum, atau merugikan pihak lain, dengan atau tanpa pemberitahuan terlebih dahulu sesuai tingkat pelanggaran.</p>

        <h2 id="tanggung-jawab">10. Batas Tanggung Jawab</h2>
        <p>Sejauh diizinkan hukum, kami tidak bertanggung jawab atas kerugian tidak langsung akibat penggunaan layanan, termasuk kehilangan data yang disebabkan kelalaian pengguna atau gangguan pihak ketiga. Tanggung jawab kami terbatas pada nilai pembayaran yang Anda lakukan untuk layanan terkait.</p>

        <h2 id="perubahan">11. Perubahan Ketentuan</h2>
        <p>Ketentuan ini dapat diperbarui sewaktu-waktu. Versi terbaru selalu tersedia pada halaman ini beserta tanggal berlakunya. Penggunaan layanan setelah perubahan dianggap sebagai persetujuan terhadap versi terbaru.</p>

        <h2 id="kontak">12. Kontak</h2>
        <p>Pertanyaan mengenai Syarat dan Ketentuan ini dapat disampaikan melalui WhatsApp layanan:</p>
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
