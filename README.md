# Inibisa Invitation

Aplikasi undangan pernikahan digital berbasis Laravel 12. Customer membuat, mengatur, dan mempublikasikan undangan. Tamu membuka tautan publik untuk melihat detail acara, mengisi RSVP, serta mengirim ucapan.

## Fitur

- Landing page, registrasi, login, dan persetujuan customer oleh admin.
- Dashboard customer untuk membuat dan mengelola undangan.
- Template undangan publik: Eternal Ivory dan Sweet Blossom.
- Detail pasangan, acara, cerita, galeri, musik, hadiah, RSVP, serta ucapan tamu.
- Preview sebelum publikasi dan tautan publik berbasis slug.
- Dashboard admin untuk customer, undangan, template, RSVP, ucapan, serta pengaturan aplikasi.

## Kebutuhan

- PHP 8.3 atau lebih baru dengan ekstensi Imagick.
- Composer.
- MySQL 8 atau kompatibel.

## Instalasi

```bash
git clone https://github.com/INIBISA/inibisa-invitation.git
cd inibisa-invitation
composer install
cp .env.example .env
php artisan key:generate
```

Atur koneksi database dan kredensial admin pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=undangan_digital
DB_USERNAME=root
DB_PASSWORD=

ADMIN_NAME=Administrator
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=password
```

Jalankan migrasi, seeder, lalu server pengembangan:

```bash
php artisan migrate --seed
php artisan serve
```

Buka `http://localhost:8000`. Login admin memakai `ADMIN_EMAIL` dan `ADMIN_PASSWORD` dari `.env`.

## Alur Aplikasi

### Customer

1. Pengunjung mendaftar dari `/register`.
2. Akun baru berstatus `pending`.
3. Admin menyetujui akun melalui `/admin/customers`.
4. Customer login dan membuat undangan dari dashboard.
5. Customer memilih template, mengisi detail pasangan, acara, cerita, rekening hadiah, serta media.
6. Customer membuka preview, kemudian mempublikasikan undangan.
7. Tautan publik tersedia pada `/{slug}` dan dapat dibagikan ke tamu.

### Tamu

1. Tamu membuka `/{slug}`. Nama penerima opsional melalui `?to=Nama+Tamu`.
2. Tamu melihat detail pasangan, jadwal acara, galeri, cerita, dan informasi hadiah sesuai pengaturan undangan.
3. Tamu mengirim RSVP melalui `/{slug}/rsvp`.
4. Tamu mengirim ucapan melalui `/{slug}/wishes`.

### Admin

1. Admin login melalui `/login`.
2. Dashboard `/admin` menampilkan ringkasan customer, undangan, RSVP, dan ucapan.
3. Admin menyetujui atau menolak customer baru.
4. Admin memantau semua undangan, template, RSVP, dan ucapan.
5. Admin mengubah pengaturan aplikasi dari `/admin/settings`.

## Status Undangan

- `draft`: belum tampil untuk umum.
- `published`: dapat dibuka melalui slug publik.
- `inactive`: tidak lagi dapat dibuka publik tanpa menghapus data.

## Perintah Pengembangan

```bash
# Menjalankan server Laravel
php artisan serve

# Menjalankan test
php artisan test --compact

# Memformat kode PHP
vendor/bin/pint --format agent

# Menghapus cache konfigurasi
php artisan optimize:clear
```

## Struktur Penting

- `app/Http/Controllers`: alur customer, admin, dan halaman publik.
- `app/Http/Requests`: validasi form.
- `app/Models`: model undangan, template, media, RSVP, ucapan, dan user.
- `app/Services/InvitationMediaManager.php`: pengelolaan media undangan.
- `app/Support/InvitationData.php`: normalisasi data detail undangan.
- `resources/views/templates`: template halaman undangan publik.
- `database/migrations`: skema database.
- `database/seeders`: akun admin, template, dan data demo.

## Catatan Keamanan

- Jangan commit `.env` atau kredensial produksi.
- Ganti `ADMIN_PASSWORD` sebelum deployment.
- Atur `APP_ENV=production` dan `APP_DEBUG=false` di server produksi.
