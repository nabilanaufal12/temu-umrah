# 📚 PANDUAN BASIS DATA & MIGRASI LARAVEL - TEMU UMRAH
**Sistem Informasi Pengelolaan & Partisipasi Event Mahasiswa**  
*Fakultas Teknik dan Teknologi Kemaritiman (FTTK) - Universitas Maritim Raja Ali Haji (UMRAH)*

---

## 1. Konfigurasi Lingkungan (`.env`)

Pastikan berkas `.env` telah disesuaikan dengan konfigurasi MySQL lokal Anda (misal: XAMPP, Laragon, atau native MySQL service).

```env
APP_NAME="TEMU UMRAH"
APP_ENV=local
APP_KEY=base64:G67Jz9vXv5p0wXwZq7V9Y0xL2b6mZ5qP8eR1wY3uO0s=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=temu_umrah
DB_USERNAME=root
DB_PASSWORD=
```

> **Tips:** Pastikan service MySQL di XAMPP / Laragon Anda dalam status **RUNNING** dan database bernama `temu_umrah` telah dibuat (atau biarkan Laravel membuatnya secara otomatis saat migrasi).

---

## 2. Menjalankan Migrasi & Database Seeder

Buka terminal pada direktori proyek `temu-umrah`, kemudian jalankan perintah berikut:

### Opsi A: Jalankan Migrasi Sekaligus Mengisi Data Awal (Direkomendasikan)
```bash
php artisan migrate:fresh --seed
```

### Opsi B: Jalankan Secara Terpisah
1. **Jalankan Migrasi Skema Tabel:**
   ```bash
   php artisan migrate
   ```
2. **Jalankan Seeder Dataset Awal:**
   ```bash
   php artisan db:seed
   ```

---

## 3. Struktur Berkas yang Telah Dibangun

### A. Berkas Migrasi (`database/migrations/`)
1. `2026_01_01_000001_create_users_table.php`  
   *Menyimpan akun pengguna dengan peran: `mahasiswa`, `panitia`, `admin`.*
2. `2026_01_01_000002_create_ormawa_table.php`  
   *Menyimpan profil Organisasi Mahasiswa penyelenggara (HMTI, BEM FTTK, HIMASIKKA, dsb.).*
3. `2026_01_01_000003_create_events_table.php`  
   *Master data kegiatan: judul, slug, kategori, kuota, tanggal, lokasi, biaya, dan fasilitas.*
4. `2026_01_01_000004_create_registrations_table.php`  
   *Pendaftaran kegiatan, nomor tiket unik (`TIK-...`), token OTP presensi, dan hash QR Code.*
5. `2026_01_01_000005_create_payments_table.php`  
   *Pencatatan pembayaran kegiatan berbayar, bukti transfer, dan status verifikasi bendahara.*
6. `2026_01_01_000006_create_presensi_table.php`  
   *Log kehadiran presensi mahasiswa (metode scan QR dinamis / token input).*
7. `2026_01_01_000007_create_certificates_table.php`  
   *Penerbitan E-Sertifikat resmi terverifikasi dengan nomor SK dan link unduh PDF.*
8. `2026_01_01_000008_create_notifications_table.php`  
   *Pusat pemberitahuan sistem real-time untuk akun mahasiswa.*

### B. Berkas Eloquent Models (`app/Models/`)
* [User.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/User.php) — Relasi `ormawa()`, `registrations()`, `notifications()`
* [Ormawa.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/Ormawa.php) — Relasi `user()`, `events()`
* [Event.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/Event.php) — Relasi `ormawa()`, `registrations()`
* [Registration.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/Registration.php) — Relasi `user()`, `event()`, `payment()`, `presensi()`, `certificate()`
* [Payment.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/Payment.php) — Relasi `registration()`, `verifier()`
* [Presensi.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/Presensi.php) — Relasi `registration()`
* [Certificate.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/Certificate.php) — Relasi `registration()`
* [Notification.php](file:///C:/Users/wwwww/Downloads/3_Kuliah%20&%20Perkuliahan/Pemrograman%20Web/temu-umrah/app/Models/Notification.php) — Relasi `user()`

### C. Database Seeder (`database/seeders/DatabaseSeeder.php`)
Secara otomatis mengisi data master:
* 6 Kegiatan resmi UMRAH (Seminar AI Maritim, Workshop UI/UX Figma, Lomba Web Dev, dsb.).
* 5 Ormawa resmi terverifikasi FTTK.
* Data awal pengguna, Ormawa, dan event untuk instalasi aplikasi.

---

## 4. Data Pengguna Seeder Lokal

Seluruh password default adalah: **`password123`**

| Peran (Role) | Nama Pengguna | Email Login | NIM / Identitas |
| :--- | :--- | :--- | :--- |
| **Super Admin** | Biro Kemahasiswaan & Alumni UMRAH | `admin@umrah.ac.id` | Pengelola Pusat |
| **Panitia Ormawa** | Panitia HMTI UMRAH | `hmti@umrah.ac.id` | Ormawa TI |
| **Panitia Ormawa** | BEM FTTK UMRAH | `bem.fttk@umrah.ac.id` | Eksekutif Fakultas |
| **Panitia Ormawa** | HIMASIKKA UMRAH | `himasikka@umrah.ac.id` | Kemaritiman |
| **Mahasiswa** | Nabil | `2401020073@student.umrah.ac.id` | NIM: `2401020073` |
| **Mahasiswa** | Alman | `2401020110@student.umrah.ac.id` | NIM: `2401020110` |
| **Mahasiswa** | Balqis | `2401020069@student.umrah.ac.id` | NIM: `2401020069` |

---

## 5. Alternatif Eksekusi Skrip SQL Langsung

Jika pengujian dilakukan langsung melalui phpMyAdmin, DBeaver, HeidiSQL, atau MySQL CLI tanpa menggunakan `php artisan`:
Berkas SQL mentah telah disediakan di:
📂 **`database/sql/temu_umrah_schema.sql`**
