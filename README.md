# TEMU UMRAH

Platform pengelolaan dan pendaftaran kegiatan mahasiswa Universitas Maritim Raja Ali Haji (UMRAH).

## Teknologi

- **Laravel 11** sebagai satu-satunya framework dan runtime aplikasi.
- PHP 8.2+ dan Composer.
- Blade untuk template server-side.
- MySQL untuk deployment; SQLite dapat digunakan untuk pengujian lokal.
- JavaScript dan CSS browser biasa sebagai asset antarmuka.

Tidak ada server Node.js, Express, atau framework frontend lain di dalam alur aplikasi.

## Menjalankan aplikasi

### Terminal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Buka http://127.0.0.1:8000.

### Windows

Klik dua kali `jalankan.bat`. Script tersebut menjalankan `php artisan serve`.

## Akun Seeder Lokal

Data mahasiswa yang tersedia pada seeder lokal:

| Nama | NIM | Email UMRAH |
| --- | --- | --- |
| Nabil | `2401020073` | `2401020073@student.umrah.ac.id` |
| Alman | `2401020110` | `2401020110@student.umrah.ac.id` |
| Balqis | `2401020069` | `2401020069@student.umrah.ac.id` |

Akun panitia dan Admin tetap diisi oleh `DatabaseSeeder` untuk kebutuhan
pengelolaan aplikasi. Pada instalasi lokal baru, kata sandi awal seeder adalah
`password123` dan sebaiknya segera diganti melalui alur akun yang tersedia.

## Struktur utama

```text
app/
├── Http/Controllers/       # Controller aplikasi
├── Http/Middleware/        # Middleware autentikasi dan role
└── Models/                 # Model Eloquent
bootstrap/                  # Bootstrap Laravel
config/                     # Konfigurasi Laravel
database/
├── migrations/             # Skema database
└── seeders/                # Data awal aplikasi
public/assets/              # Asset CSS, JavaScript, dan gambar
resources/views/            # Template Blade
routes/web.php              # Route aplikasi
artisan                     # CLI Laravel
jalankan.bat                # Launcher Laravel untuk Windows
```

## Fitur backend yang tersedia

- Autentikasi Laravel dan aktivasi akun mahasiswa.
- Katalog dan detail event dari database.
- Pendaftaran event dengan transaksi, validasi role, anti-duplikasi, dan penguncian kuota.
- Dashboard peserta berbasis data pendaftaran.
- Dashboard panitia berbasis data event milik Ormawa.
- Moderasi Admin untuk Ormawa dan event.
- Proteksi route berdasarkan role dan ownership event panitia.
- Pembayaran event berbayar melalui record payment gateway `pending`.
- Presensi, notifikasi, dan sertifikat dengan route privat sesuai role.

## Pemeriksaan

```bash
php artisan test
```

Pemeriksaan tambahan: lint PHP, lint JavaScript, migrasi + seeder SQLite, dan smoke test HTTP Laravel.
