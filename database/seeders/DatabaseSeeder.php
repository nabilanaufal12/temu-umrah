<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with UMRAH production/mock dataset.
     */
    public function run(): void
    {
        // Nonaktifkan foreign keys sementara untuk pembersihan data lama
        DB::statement('PRAGMA foreign_keys = OFF;');
        DB::table('notifications')->truncate();
        DB::table('certificates')->truncate();
        DB::table('presensi')->truncate();
        DB::table('payments')->truncate();
        DB::table('registrations')->truncate();
        DB::table('events')->truncate();
        DB::table('ormawa')->truncate();
        DB::table('users')->truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // 1. SEED USERS (Admin, Panitia Ormawa, & Mahasiswa Anggota Kelompok)
        $passwordHash = Hash::make('password123');

        $users = [
            // Admin BAAK / Kemahasiswaan
            [
                'id' => 1,
                'nim' => null,
                'nama' => 'Biro Kemahasiswaan & Alumni UMRAH',
                'email' => 'admin@umrah.ac.id',
                'no_wa' => '081270001000',
                'password' => $passwordHash,
                'role' => 'admin',
                'status' => 'aktif',
            ],
            // Panitia Ormawa Kampus
            [
                'id' => 2,
                'nim' => '22050101',
                'nama' => 'Panitia HMTI UMRAH',
                'email' => 'hmti@umrah.ac.id',
                'no_wa' => '081270002001',
                'password' => $passwordHash,
                'role' => 'panitia',
                'status' => 'aktif',
            ],
            [
                'id' => 3,
                'nim' => '22050102',
                'nama' => 'BEM FTTK UMRAH',
                'email' => 'bem.fttk@umrah.ac.id',
                'no_wa' => '081270002002',
                'password' => $passwordHash,
                'role' => 'panitia',
                'status' => 'aktif',
            ],
            [
                'id' => 4,
                'nim' => '22050103',
                'nama' => 'HIMASIKKA UMRAH',
                'email' => 'himasikka@umrah.ac.id',
                'no_wa' => '081270002003',
                'password' => $passwordHash,
                'role' => 'panitia',
                'status' => 'aktif',
            ],
            [
                'id' => 5,
                'nim' => '22050104',
                'nama' => 'Kemenparekraf BEM UMRAH',
                'email' => 'kemenparekraf@umrah.ac.id',
                'no_wa' => '081270002004',
                'password' => $passwordHash,
                'role' => 'panitia',
                'status' => 'aktif',
            ],
            [
                'id' => 6,
                'nim' => '22050105',
                'nama' => 'Laboratorium Terpadu FTTK',
                'email' => 'lab.fttk@umrah.ac.id',
                'no_wa' => '081270002005',
                'password' => $passwordHash,
                'role' => 'panitia',
                'status' => 'aktif',
            ],
            // 4 ANGGOTA KELOMPOK (MAHASISWA PESERTA AKTIF FTTK UMRAH)
            [
                'id' => 7,
                'nim' => '2401020073',
                'nama' => 'Nabil',
                'email' => '2401020073@student.umrah.ac.id',
                'no_wa' => '081298765432',
                'password' => $passwordHash,
                'role' => 'mahasiswa',
                'status' => 'aktif',
            ],
            [
                'id' => 8,
                'nim' => '2401020069',
                'nama' => 'Balqis Manisa',
                'email' => '2401020069@student.umrah.ac.id',
                'no_wa' => '081270112233',
                'password' => $passwordHash,
                'role' => 'mahasiswa',
                'status' => 'aktif',
            ],
            [
                'id' => 9,
                'nim' => '2401020087',
                'nama' => 'Annisa Darmawan',
                'email' => '2401020087@student.umrah.ac.id',
                'no_wa' => '081270223344',
                'password' => $passwordHash,
                'role' => 'mahasiswa',
                'status' => 'aktif',
            ],
            [
                'id' => 10,
                'nim' => '2401020110',
                'nama' => 'Yehezkiel Alman Siambaton',
                'email' => '2401020110@student.umrah.ac.id',
                'no_wa' => '081270334455',
                'password' => $passwordHash,
                'role' => 'mahasiswa',
                'status' => 'aktif',
            ],
        ];

        foreach ($users as $u) {
            DB::table('users')->updateOrInsert(['id' => $u['id']], $u);
        }

        // 2. SEED ORMAWA
        $ormawas = [
            [
                'id' => 1,
                'user_id' => 2,
                'nama_ormawa' => 'Himpunan Mahasiswa Teknik Informatika (HMTI)',
                'tingkat' => 'himpunan',
                'ketua' => 'Muhammad Rizky (NIM: 23050110)',
                'pembina' => 'Novi Haryani, S.T., M.Kom.',
                'no_sk' => 'SK-DEK/FTTK-UMRAH/2026/001',
                'dokumen_sk' => 'SK_Pengesahan_HMTI_2026.pdf',
                'deskripsi' => 'Organisasi kemahasiswaan Program Studi Teknik Informatika FTTK UMRAH.',
                'logo_path' => 'assets/images/logo_hmti.png',
                'status_verifikasi' => 'verified',
                'kuota_panitia_maks' => 2,
                'jumlah_panitia_aktif' => 1,
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'nama_ormawa' => 'Badan Eksekutif Mahasiswa FTTK (BEM FTTK)',
                'tingkat' => 'fakultas',
                'ketua' => 'Dharma Saputra (NIM: 22050215)',
                'pembina' => 'Dr. Ir. Hendra S., M.T.',
                'no_sk' => 'SK-DEK/FTTK-UMRAH/2026/002',
                'dokumen_sk' => 'SK_Pengesahan_BEM_FTTK_2026.pdf',
                'deskripsi' => 'Lembaga eksekutif mahasiswa tingkat Fakultas Teknik dan Teknologi Kemaritiman UMRAH.',
                'logo_path' => 'assets/images/logo_bem.png',
                'status_verifikasi' => 'verified',
                'kuota_panitia_maks' => 2,
                'jumlah_panitia_aktif' => 1,
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'nama_ormawa' => 'HIMASIKKA (Teknologi Hasil Perikanan & Kelautan)',
                'tingkat' => 'himpunan',
                'ketua' => 'Fajar Nugraha (NIM: 220102045)',
                'pembina' => 'Dr. Dedi Suryadi, S.Pi., M.Si.',
                'no_sk' => 'SK-DEK/FIKP-UMRAH/2026/007',
                'dokumen_sk' => 'SK_Pengesahan_HIMASIKKA_2026.pdf',
                'deskripsi' => 'Himpunan Mahasiswa Ilmu Kelautan & Kemaritiman UMRAH.',
                'logo_path' => 'assets/images/logo_himasikka.png',
                'status_verifikasi' => 'verified',
                'kuota_panitia_maks' => 2,
                'jumlah_panitia_aktif' => 1,
            ],
            [
                'id' => 4,
                'user_id' => 5,
                'nama_ormawa' => 'Kementerian Pariwisata & Ekonomi Kreatif BEM',
                'tingkat' => 'universitas',
                'ketua' => 'Anisa Rahmawati (NIM: 23040108)',
                'pembina' => 'Dra. Nurhayati, M.Hum.',
                'no_sk' => 'SK-REK/UMRAH/KM/2026/012',
                'dokumen_sk' => 'SK_Rektor_Kemenparekraf_2026.pdf',
                'deskripsi' => 'Inkubator kewirausahaan dan ekspresi seni budaya mahasiswa UMRAH.',
                'logo_path' => 'assets/images/logo_kemenparekraf.png',
                'status_verifikasi' => 'verified',
                'kuota_panitia_maks' => 2,
                'jumlah_panitia_aktif' => 1,
            ],
            [
                'id' => 5,
                'user_id' => 6,
                'nama_ormawa' => 'Laboratorium Terpadu Komputasi & Rekayasa Perangkat Lunak',
                'tingkat' => 'fakultas',
                'ketua' => 'Muhamad Radzi Rathomi, M.Cs.',
                'pembina' => 'Dekan FTTK UMRAH',
                'no_sk' => 'SK-DEK/FTTK-UMRAH/2026/018',
                'dokumen_sk' => 'SK_Lab_Komputasi_FTTK_2026.pdf',
                'deskripsi' => 'Pusat riset dan praktikum komputasi Fakultas Teknik FTTK UMRAH.',
                'logo_path' => 'assets/images/logo_lab.png',
                'status_verifikasi' => 'verified',
                'kuota_panitia_maks' => 2,
                'jumlah_panitia_aktif' => 1,
            ],
        ];

        foreach ($ormawas as $o) {
            DB::table('ormawa')->updateOrInsert(['id' => $o['id']], $o);
        }

        // 3. SEED EVENTS (6 Master Kegiatan Resmi Kampus UMRAH)
        $events = [
            [
                'id' => 1,
                'ormawa_id' => 2,
                'judul' => 'Seminar Nasional: AI & Future of Maritime Technology',
                'slug' => 'seminar-nasional-ai-maritime-2026',
                'kategori' => 'SEMINAR',
                'tipe_biaya' => 'gratis',
                'harga' => 0.00,
                'kuota_maks' => 120,
                'kuota_terisi' => 108,
                'tanggal_kegiatan' => '2026-10-18',
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '12:30:00',
                'lokasi' => 'Auditorium Kampus Dompak Lt. 3, UMRAH',
                'deskripsi' => 'Eksplorasi kecerdasan artifisial, sistem navigasi otonom kapal, dan komputasi maritim terkini bersama akademisi serta praktisi industri perkapalan Kepulauan Riau.',
                'manfaat_fasilitas' => json_encode(['E-Sertifikat Nasional', 'Snack & Coffee Break', 'Materi Presentasi PDF', 'Poin SKPI']),
                'banner_path' => 'assets/img/events/banner-ai-maritime.jpg',
                'status' => 'published',
            ],
            [
                'id' => 2,
                'ormawa_id' => 1,
                'judul' => 'Workshop UI/UX Design: Dari Riset Pengguna ke Figma Prototype',
                'slug' => 'workshop-uiux-design-figma-prototype',
                'kategori' => 'WORKSHOP',
                'tipe_biaya' => 'berbayar',
                'harga' => 35000.00,
                'kuota_maks' => 45,
                'kuota_terisi' => 42,
                'tanggal_kegiatan' => '2026-10-22',
                'waktu_mulai' => '13:30:00',
                'waktu_selesai' => '17:00:00',
                'lokasi' => 'Lab Komputer 2 FTTK UMRAH',
                'deskripsi' => 'Pelatihan langsung dari wireframe ke visual design high-fidelity menggunakan auto layout, component variants, interactive prototyping, dan usability testing.',
                'manfaat_fasilitas' => json_encode(['E-Sertifikat Keterampilan', 'Starter Kit UI Component', 'Studi Kasus Nyata', 'Mentoring 1-on-1']),
                'banner_path' => 'assets/img/events/banner-uiux-design.jpg',
                'status' => 'published',
            ],
            [
                'id' => 3,
                'ormawa_id' => 2,
                'judul' => 'UMRAH Web Development Competition 2026',
                'slug' => 'umrah-web-development-competition-2026',
                'kategori' => 'LOMBA',
                'tipe_biaya' => 'berbayar',
                'harga' => 50000.00,
                'kuota_maks' => 20,
                'kuota_terisi' => 16,
                'tanggal_kegiatan' => '2026-11-05',
                'waktu_mulai' => '08:30:00',
                'waktu_selesai' => '16:00:00',
                'lokasi' => 'Gedung Rektorat UMRAH Dompak',
                'deskripsi' => 'Kompetisi perancangan aplikasi web interaktif bagi mahasiswa se-Kepri dengan fokus inovasi digital kemaritiman, pariwisata bahari, dan smart governance.',
                'manfaat_fasilitas' => json_encode(['Total Hadiah Rp 7.500.000', 'Sertifikat Juara & Finalis', 'Plakat Penghargaan', 'Networking Praktisi']),
                'banner_path' => 'assets/img/events/banner-web-competition.jpg',
                'status' => 'published',
            ],
            [
                'id' => 4,
                'ormawa_id' => 3,
                'judul' => 'Seminar Kemaritiman: Potensi Blue Economy Kepulauan Riau',
                'slug' => 'seminar-kemaritiman-blue-economy-kepri',
                'kategori' => 'SEMINAR',
                'tipe_biaya' => 'gratis',
                'harga' => 0.00,
                'kuota_maks' => 90,
                'kuota_terisi' => 45,
                'tanggal_kegiatan' => '2026-11-12',
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '12:00:00',
                'lokasi' => 'Ruang Seminar Bersama Kampus Senggarang',
                'deskripsi' => 'Diskusi komprehensif bersama pakar oseanografi mengenai optimalisasi potensi kelautan dan industri hilir perikanan berbasis kelestarian ekosistem pesisir.',
                'manfaat_fasilitas' => json_encode(['E-Sertifikat Kemaritiman', 'Konsumsi', 'Buku Panduan Blue Economy', 'Poin SKPI']),
                'banner_path' => 'assets/img/events/banner-blue-economy.jpg',
                'status' => 'published',
            ],
            [
                'id' => 5,
                'ormawa_id' => 4,
                'judul' => 'Bazar Kewirausahaan & Festival Seni Mahasiswa',
                'slug' => 'bazar-kewirausahaan-festival-seni-umrah',
                'kategori' => 'BAZAR',
                'tipe_biaya' => 'gratis',
                'harga' => 0.00,
                'kuota_maks' => 60,
                'kuota_terisi' => 28,
                'tanggal_kegiatan' => '2026-11-20',
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '21:00:00',
                'lokasi' => 'Plaza Terbuka Rektorat UMRAH Dompak',
                'deskripsi' => 'Pameran produk inovasi rintisan usaha mahasiswa, kuliner khas pesisir Kepri, live acoustic music, dan bazar merchandise kreatif karya ormawa.',
                'manfaat_fasilitas' => json_encode(['Stand Pameran Gratis', 'Voucher Belanja Bazar', 'Piagam Partisipasi', 'Liputan Media Kampus']),
                'banner_path' => 'assets/img/events/banner-bazar-festival.jpg',
                'status' => 'published',
            ],
            [
                'id' => 6,
                'ormawa_id' => 5,
                'judul' => 'Workshop Pemrograman Web Modern: MVC & REST API',
                'slug' => 'workshop-pemrograman-web-modern-mvc-rest',
                'kategori' => 'WORKSHOP',
                'tipe_biaya' => 'berbayar',
                'harga' => 25000.00,
                'kuota_maks' => 35,
                'kuota_terisi' => 33,
                'tanggal_kegiatan' => '2026-11-28',
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '15:30:00',
                'lokasi' => 'Lab Software Engineering FTTK UMRAH',
                'deskripsi' => 'Pendalaman arsitektur MVC, Blade templating, query prepared statements, autentikasi CSRF, dan integrasi frontend ke backend.',
                'manfaat_fasilitas' => json_encode(['E-Sertifikat Laboratorium', 'Source Code Starter Kit', 'Modul Praktikum Lengkap', 'Konsultasi Proyek']),
                'banner_path' => 'assets/img/events/banner-web-modern.jpg',
                'status' => 'published',
            ],
        ];

        foreach ($events as $ev) {
            DB::table('events')->updateOrInsert(['id' => $ev['id']], $ev);
        }

        // 4. SEED REGISTRATIONS (Pendaftaran untuk 4 Anggota Kelompok)
        $registrations = [
            // NABIL (User ID 7)
            [
                'id' => 1,
                'user_id' => 7,
                'event_id' => 1, // Seminar AI
                'nomor_tiket' => 'TIK-20261018-0073',
                'tanggal_daftar' => '2026-10-02 10:15:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'UMR88',
                'qr_code_hash' => 'QR-EVT1-USR7-UMR88',
            ],
            [
                'id' => 2,
                'user_id' => 7,
                'event_id' => 2, // Workshop UI/UX
                'nomor_tiket' => 'TIK-20261022-0073',
                'tanggal_daftar' => '2026-10-04 14:20:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'FIGMA',
                'qr_code_hash' => 'QR-EVT2-USR7-FIGMA',
            ],
            [
                'id' => 3,
                'user_id' => 7,
                'event_id' => 3, // Web Dev Competition
                'nomor_tiket' => 'TIK-20261105-0073',
                'tanggal_daftar' => '2026-10-15 09:30:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'WEBDEV',
                'qr_code_hash' => 'QR-EVT3-USR7-WEBDEV',
            ],

            // BALQIS MANISA (User ID 8)
            [
                'id' => 4,
                'user_id' => 8,
                'event_id' => 1, // Seminar AI
                'nomor_tiket' => 'TIK-20261018-0069',
                'tanggal_daftar' => '2026-10-02 11:00:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'AI69',
                'qr_code_hash' => 'QR-EVT1-USR8-AI69',
            ],
            [
                'id' => 5,
                'user_id' => 8,
                'event_id' => 2, // Workshop UI/UX
                'nomor_tiket' => 'TIK-20261022-0069',
                'tanggal_daftar' => '2026-10-05 13:10:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'UI69',
                'qr_code_hash' => 'QR-EVT2-USR8-UI69',
            ],
            [
                'id' => 6,
                'user_id' => 8,
                'event_id' => 5, // Bazar Kewirausahaan
                'nomor_tiket' => 'TIK-20261120-0069',
                'tanggal_daftar' => '2026-10-18 16:45:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'BAZAR69',
                'qr_code_hash' => 'QR-EVT5-USR8-BAZAR69',
            ],

            // ANNISA DARMAWAN (User ID 9)
            [
                'id' => 7,
                'user_id' => 9,
                'event_id' => 1, // Seminar AI
                'nomor_tiket' => 'TIK-20261018-0087',
                'tanggal_daftar' => '2026-10-02 11:20:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'AI87',
                'qr_code_hash' => 'QR-EVT1-USR9-AI87',
            ],
            [
                'id' => 8,
                'user_id' => 9,
                'event_id' => 4, // Seminar Blue Economy
                'nomor_tiket' => 'TIK-20261112-0087',
                'tanggal_daftar' => '2026-10-10 09:15:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'MARITIM87',
                'qr_code_hash' => 'QR-EVT4-USR9-MARITIM87',
            ],
            [
                'id' => 9,
                'user_id' => 9,
                'event_id' => 6, // Workshop Web Modern
                'nomor_tiket' => 'TIK-20261128-0087',
                'tanggal_daftar' => '2026-10-20 14:00:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'REST87',
                'qr_code_hash' => 'QR-EVT6-USR9-REST87',
            ],

            // YEHEZKIEL ALMAN SIAMBATON (User ID 10)
            [
                'id' => 10,
                'user_id' => 10,
                'event_id' => 1, // Seminar AI
                'nomor_tiket' => 'TIK-20261018-0110',
                'tanggal_daftar' => '2026-10-03 10:45:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'AI110',
                'qr_code_hash' => 'QR-EVT1-USR10-AI110',
            ],
            [
                'id' => 11,
                'user_id' => 10,
                'event_id' => 3, // Web Dev Competition
                'nomor_tiket' => 'TIK-20261105-0110',
                'tanggal_daftar' => '2026-10-12 11:30:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'CODE110',
                'qr_code_hash' => 'QR-EVT3-USR10-CODE110',
            ],
            [
                'id' => 12,
                'user_id' => 10,
                'event_id' => 6, // Workshop Web Modern
                'nomor_tiket' => 'TIK-20261128-0110',
                'tanggal_daftar' => '2026-10-21 15:10:00',
                'status' => 'terkonfirmasi',
                'token_presensi' => 'MVC110',
                'qr_code_hash' => 'QR-EVT6-USR10-MVC110',
            ],
        ];

        foreach ($registrations as $reg) {
            DB::table('registrations')->updateOrInsert(['id' => $reg['id']], $reg);
        }

        // 5. SEED PAYMENTS (Transaksi Pembayaran Terverifikasi / Settlement)
        $payments = [
            // Pembayaran Nabil (Workshop UI/UX & Lomba Web Dev)
            [
                'id' => 1,
                'registration_id' => 2,
                'order_id' => 'ORD-2026-0073-01',
                'transaction_id' => 'TRX-QRIS-0073-01',
                'jumlah' => 35000.00,
                'metode_bayar' => 'qris',
                'channel_detail' => 'QRIS Dinamis Bank Mandiri',
                'status' => 'settlement',
                'gateway_response' => json_encode(['status' => 'settlement', 'issuer' => 'QRIS']),
                'settlement_time' => '2026-10-04 15:30:00',
            ],
            [
                'id' => 2,
                'registration_id' => 3,
                'order_id' => 'ORD-2026-0073-02',
                'transaction_id' => 'TRX-VA-0073-02',
                'jumlah' => 50000.00,
                'metode_bayar' => 'virtual_account',
                'channel_detail' => 'BNI Virtual Account',
                'status' => 'settlement',
                'gateway_response' => json_encode(['status' => 'settlement', 'bank' => 'BNI']),
                'settlement_time' => '2026-10-15 10:00:00',
            ],
            // Pembayaran Balqis Manisa (Workshop UI/UX)
            [
                'id' => 3,
                'registration_id' => 5,
                'order_id' => 'ORD-2026-0069-01',
                'transaction_id' => 'TRX-QRIS-0069-01',
                'jumlah' => 35000.00,
                'metode_bayar' => 'qris',
                'channel_detail' => 'QRIS Dinamis BCA',
                'status' => 'settlement',
                'gateway_response' => json_encode(['status' => 'settlement', 'issuer' => 'QRIS']),
                'settlement_time' => '2026-10-05 14:00:00',
            ],
            // Pembayaran Annisa Darmawan (Workshop Web Modern)
            [
                'id' => 4,
                'registration_id' => 9,
                'order_id' => 'ORD-2026-0087-01',
                'transaction_id' => 'TRX-QRIS-0087-01',
                'jumlah' => 25000.00,
                'metode_bayar' => 'qris',
                'channel_detail' => 'QRIS Dinamis Bank Riau Kepri Syariah',
                'status' => 'settlement',
                'gateway_response' => json_encode(['status' => 'settlement', 'issuer' => 'BRKS']),
                'settlement_time' => '2026-10-20 14:30:00',
            ],
            // Pembayaran Yehezkiel Alman (Competition & Workshop Web Modern)
            [
                'id' => 5,
                'registration_id' => 11,
                'order_id' => 'ORD-2026-0110-01',
                'transaction_id' => 'TRX-VA-0110-01',
                'jumlah' => 50000.00,
                'metode_bayar' => 'virtual_account',
                'channel_detail' => 'Mandiri Virtual Account',
                'status' => 'settlement',
                'gateway_response' => json_encode(['status' => 'settlement', 'bank' => 'Mandiri']),
                'settlement_time' => '2026-10-12 12:00:00',
            ],
            [
                'id' => 6,
                'registration_id' => 12,
                'order_id' => 'ORD-2026-0110-02',
                'transaction_id' => 'TRX-QRIS-0110-02',
                'jumlah' => 25000.00,
                'metode_bayar' => 'qris',
                'channel_detail' => 'QRIS Dinamis Mandiri',
                'status' => 'settlement',
                'gateway_response' => json_encode(['status' => 'settlement', 'issuer' => 'QRIS']),
                'settlement_time' => '2026-10-21 15:45:00',
            ],
        ];

        foreach ($payments as $pay) {
            DB::table('payments')->updateOrInsert(['id' => $pay['id']], $pay);
        }

        // 6. SEED PRESENSI (Kehadiran Sah Terverifikasi Sistem)
        $presensi = [
            // Nabil: Hadir Seminar AI & Hadir Workshop UI/UX
            [
                'id' => 1,
                'registration_id' => 1,
                'waktu_hadir' => '2026-10-18 08:42:00',
                'metode' => 'scan_qr',
                'status' => 'hadir',
            ],
            [
                'id' => 2,
                'registration_id' => 2,
                'waktu_hadir' => '2026-10-22 13:20:00',
                'metode' => 'token_input',
                'status' => 'hadir',
            ],
            // Balqis Manisa: Hadir Seminar AI & Hadir Workshop UI/UX
            [
                'id' => 3,
                'registration_id' => 4,
                'waktu_hadir' => '2026-10-18 08:45:00',
                'metode' => 'scan_qr',
                'status' => 'hadir',
            ],
            [
                'id' => 4,
                'registration_id' => 5,
                'waktu_hadir' => '2026-10-22 13:25:00',
                'metode' => 'scan_qr',
                'status' => 'hadir',
            ],
            // Annisa Darmawan: Hadir Seminar AI & Hadir Seminar Blue Economy
            [
                'id' => 5,
                'registration_id' => 7,
                'waktu_hadir' => '2026-10-18 08:50:00',
                'metode' => 'scan_qr',
                'status' => 'hadir',
            ],
            [
                'id' => 6,
                'registration_id' => 8,
                'waktu_hadir' => '2026-11-12 08:40:00',
                'metode' => 'token_input',
                'status' => 'hadir',
            ],
            // Yehezkiel Alman: Hadir Seminar AI
            [
                'id' => 7,
                'registration_id' => 10,
                'waktu_hadir' => '2026-10-18 08:38:00',
                'metode' => 'scan_qr',
                'status' => 'hadir',
            ],
        ];

        foreach ($presensi as $pres) {
            DB::table('presensi')->updateOrInsert(['id' => $pres['id']], $pres);
        }

        // 7. SEED CERTIFICATES (E-Sertifikat Resmi Terbit Ber-Nomor Registrasi Dekanat)
        $certificates = [
            // Sertifikat Nabil
            [
                'id' => 1,
                'registration_id' => 1,
                'nomor_sertifikat' => 'SERT/FTTK-UMRAH/AI/2026/0073',
                'tanggal_terbit' => '2026-10-19',
                'file_sertifikat_path' => 'certificates/SERT-AI-2026-0073.pdf',
                'status_klaim' => 'siap_unduh',
            ],
            [
                'id' => 2,
                'registration_id' => 2,
                'nomor_sertifikat' => 'SERT/FTTK-UMRAH/UIUX/2026/0073',
                'tanggal_terbit' => '2026-10-23',
                'file_sertifikat_path' => 'certificates/SERT-UIUX-2026-0073.pdf',
                'status_klaim' => 'siap_unduh',
            ],
            // Sertifikat Balqis Manisa
            [
                'id' => 3,
                'registration_id' => 4,
                'nomor_sertifikat' => 'SERT/FTTK-UMRAH/AI/2026/0069',
                'tanggal_terbit' => '2026-10-19',
                'file_sertifikat_path' => 'certificates/SERT-AI-2026-0069.pdf',
                'status_klaim' => 'siap_unduh',
            ],
            [
                'id' => 4,
                'registration_id' => 5,
                'nomor_sertifikat' => 'SERT/FTTK-UMRAH/UIUX/2026/0069',
                'tanggal_terbit' => '2026-10-23',
                'file_sertifikat_path' => 'certificates/SERT-UIUX-2026-0069.pdf',
                'status_klaim' => 'siap_unduh',
            ],
            // Sertifikat Annisa Darmawan
            [
                'id' => 5,
                'registration_id' => 7,
                'nomor_sertifikat' => 'SERT/FTTK-UMRAH/AI/2026/0087',
                'tanggal_terbit' => '2026-10-19',
                'file_sertifikat_path' => 'certificates/SERT-AI-2026-0087.pdf',
                'status_klaim' => 'siap_unduh',
            ],
            // Sertifikat Yehezkiel Alman Siambaton
            [
                'id' => 6,
                'registration_id' => 10,
                'nomor_sertifikat' => 'SERT/FTTK-UMRAH/AI/2026/0110',
                'tanggal_terbit' => '2026-10-19',
                'file_sertifikat_path' => 'certificates/SERT-AI-2026-0110.pdf',
                'status_klaim' => 'siap_unduh',
            ],
        ];

        foreach ($certificates as $cert) {
            DB::table('certificates')->updateOrInsert(['id' => $cert['id']], $cert);
        }

        // 8. SEED NOTIFICATIONS (Notifikasi untuk 4 Mahasiswa Kelompok)
        $notifications = [
            // Notifikasi Nabil (User 7)
            [
                'id' => 1,
                'user_id' => 7,
                'judul' => 'Pembayaran Workshop UI/UX Telah Diverifikasi!',
                'pesan' => 'Bukti pembayaran Rp 35.000 telah disetujui panitia HMTI. Status kepesertaan Anda telah Terkonfirmasi.',
                'kategori' => 'PEMBAYARAN',
                'dibaca' => 0,
            ],
            [
                'id' => 2,
                'user_id' => 7,
                'judul' => 'E-Sertifikat Seminar Nasional AI Telah Diterbitkan',
                'pesan' => 'Selamat! Sertifikat kelulusan nomor SERT/FTTK-UMRAH/AI/2026/0073 Anda telah siap diunduh.',
                'kategori' => 'SERTIFIKAT',
                'dibaca' => 1,
            ],
            // Notifikasi Balqis Manisa (User 8)
            [
                'id' => 3,
                'user_id' => 8,
                'judul' => 'Presensi Seminar Nasional AI Berhasil Tercatat!',
                'pesan' => 'Data kehadiran Balqis Manisa telah terekam secara sah melalui pemindai QR Code di Auditorium Dompak.',
                'kategori' => 'PRESENSI',
                'dibaca' => 0,
            ],
            [
                'id' => 4,
                'user_id' => 8,
                'judul' => 'E-Sertifikat Seminar Nasional AI Telah Diterbitkan',
                'pesan' => 'Sertifikat resmi nomor SERT/FTTK-UMRAH/AI/2026/0069 siap diunduh di menu E-Sertifikat Anda.',
                'kategori' => 'SERTIFIKAT',
                'dibaca' => 1,
            ],
            // Notifikasi Annisa Darmawan (User 9)
            [
                'id' => 5,
                'user_id' => 9,
                'judul' => 'Tiket Kegiatan Seminar Blue Economy Aktif',
                'pesan' => 'Pendaftaran kegiatan Seminar Kemaritiman telah sah dengan nomor tiket TIK-20261112-0087.',
                'kategori' => 'PENDAFTARAN',
                'dibaca' => 0,
            ],
            [
                'id' => 6,
                'user_id' => 9,
                'judul' => 'E-Sertifikat Seminar AI Telah Tersedia',
                'pesan' => 'Selamat Annisa Darmawan! Dokumen sertifikat resmi bernomor SERT/FTTK-UMRAH/AI/2026/0087 telah diterbitkan.',
                'kategori' => 'SERTIFIKAT',
                'dibaca' => 1,
            ],
            // Notifikasi Yehezkiel Alman (User 10)
            [
                'id' => 7,
                'user_id' => 10,
                'judul' => 'Konfirmasi Pendaftaran UMRAH Web Dev Competition',
                'pesan' => 'Pendaftaran kompetisi web Anda telah diverifikasi oleh BEM FTTK. Nomor registrasi: TIK-20261105-0110.',
                'kategori' => 'LOMBA',
                'dibaca' => 0,
            ],
            [
                'id' => 8,
                'user_id' => 10,
                'judul' => 'E-Sertifikat Seminar Nasional AI Telah Diterbitkan',
                'pesan' => 'Dokumen sertifikat kelulusan nomor SERT/FTTK-UMRAH/AI/2026/0110 siap diunduh di dashboard sertifikat Anda.',
                'kategori' => 'SERTIFIKAT',
                'dibaca' => 1,
            ],
        ];

        foreach ($notifications as $notif) {
            DB::table('notifications')->updateOrInsert(['id' => $notif['id']], $notif);
        }

        echo ">>> DATABASE SEEDING COMPLETED FOR ALL 4 GROUP MEMBERS & SYSTEM ENTITIES! <<<\n";
    }
}
