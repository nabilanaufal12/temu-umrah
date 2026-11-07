-- =====================================================================
-- SKEMA BASIS DATA SISTEM INFORMASI EVENT MAHASISWA (TEMU UMRAH)
-- Relasi Antar Tabel (RAT) Lengkap - FTTK UMRAH
-- =====================================================================

CREATE DATABASE IF NOT EXISTS temu_umrah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE temu_umrah;

-- 1. Tabel USERS (Pengguna sistem: Mahasiswa, Panitia, Admin)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) UNIQUE NULL COMMENT 'NIM Mahasiswa jika role mahasiswa',
    nama VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    no_wa VARCHAR(25) NULL COMMENT 'Nomor WhatsApp aktif untuk tiket & notif',
    password VARCHAR(255) NOT NULL,
    role ENUM('mahasiswa', 'panitia', 'admin') NOT NULL DEFAULT 'mahasiswa',
    status ENUM('aktif', 'nonaktif', 'banned') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel ORMAWA (Organisasi Mahasiswa Penyelenggara)
CREATE TABLE IF NOT EXISTS ormawa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_ormawa VARCHAR(150) NOT NULL,
    tingkat ENUM('universitas', 'fakultas', 'himpunan', 'ukm') NOT NULL,
    ketua VARCHAR(150) NULL,
    pembina VARCHAR(150) NULL,
    no_sk VARCHAR(100) NULL,
    dokumen_sk VARCHAR(255) NULL,
    deskripsi TEXT NULL,
    logo_path VARCHAR(255) NULL,
    status_verifikasi ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    kuota_panitia_maks INT NOT NULL DEFAULT 2,
    jumlah_panitia_aktif INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Tabel EVENTS (Master Data Kegiatan Mahasiswa)
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ormawa_id INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    kategori ENUM('SEMINAR', 'WORKSHOP', 'LOMBA', 'BAZAR') NOT NULL,
    tipe_biaya ENUM('gratis', 'berbayar') NOT NULL DEFAULT 'gratis',
    harga DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    kuota_maks INT NOT NULL DEFAULT 100,
    kuota_terisi INT NOT NULL DEFAULT 0,
    tanggal_kegiatan DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    manfaat_fasilitas TEXT NULL,
    banner_path VARCHAR(255) NULL,
    status ENUM('draft', 'pending_approval', 'published', 'ongoing', 'selesai', 'dibatalkan', 'ditolak') NOT NULL DEFAULT 'pending_approval',
    catatan_admin TEXT NULL,
    disetujui_oleh INT NULL,
    disetujui_pada TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ormawa_id) REFERENCES ormawa(id) ON DELETE CASCADE,
    FOREIGN KEY (disetujui_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Tabel REGISTRATIONS (Pendaftaran Mahasiswa ke Event)
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    nomor_tiket VARCHAR(50) UNIQUE NOT NULL,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('menunggu_pembayaran', 'terkonfirmasi', 'batal') NOT NULL DEFAULT 'terkonfirmasi',
    token_presensi VARCHAR(10) NULL COMMENT 'Kode unik presensi 6 digit',
    qr_code_hash VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_event (user_id, event_id)
) ENGINE=InnoDB;

-- 5. Tabel PAYMENTS (Transaksi Pembayaran Event Berbayar)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    order_id VARCHAR(64) UNIQUE NOT NULL,
    transaction_id VARCHAR(128) UNIQUE NULL,
    jumlah DECIMAL(12, 2) NOT NULL,
    metode_bayar ENUM('qris', 'virtual_account', 'ewallet') NOT NULL DEFAULT 'qris',
    channel_detail VARCHAR(64) NULL,
    status ENUM('pending', 'settlement', 'expired', 'failed') NOT NULL DEFAULT 'pending',
    gateway_response JSON NULL,
    settlement_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabel PRESENSI (Catatan Kehadiran Mahasiswa)
CREATE TABLE IF NOT EXISTS presensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    waktu_hadir TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    metode ENUM('scan_qr', 'token_input', 'manual_panitia') NOT NULL DEFAULT 'token_input',
    status ENUM('hadir', 'tidak_hadir', 'izin') NOT NULL DEFAULT 'hadir',
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    UNIQUE KEY uq_presensi_reg (registration_id)
) ENGINE=InnoDB;

-- 7. Tabel CERTIFICATES (Penerbitan E-Sertifikat Resmi)
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    nomor_sertifikat VARCHAR(100) UNIQUE NOT NULL,
    tanggal_terbit DATE NOT NULL,
    file_sertifikat_path VARCHAR(255) NOT NULL,
    status_klaim ENUM('siap_unduh', 'terunduh') NOT NULL DEFAULT 'siap_unduh',
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Tabel NOTIFICATIONS (Pusat Pemberitahuan Sistem)
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    pesan TEXT NOT NULL,
    kategori VARCHAR(50) NOT NULL DEFAULT 'SISTEM',
    dibaca TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
