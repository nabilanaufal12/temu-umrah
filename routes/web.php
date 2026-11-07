<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PanitiaController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\KelolaPesertaController;
use App\Http\Controllers\SesiAbsensiController;
use App\Http\Controllers\PenerbitanSertifikatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\GoogleAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - TEMU UMRAH
|--------------------------------------------------------------------------
| Alur rute bertahap mengacu pada Wireframe WF1 - WF14
*/

// WF1 - Beranda (Landing & Spotlight, wajib login)
Route::get('/', [KatalogController::class, 'index'])
    ->middleware('auth')
    ->name('katalog.index');
Route::get('/beranda', fn () => redirect()->route('katalog.index'));
Route::get('/jelajah-event', [KatalogController::class, 'jelajah'])
    ->middleware('auth')
    ->name('katalog.jelajah');
Route::get('/event/{slug}', [KatalogController::class, 'show'])
    ->middleware('auth')
    ->name('event.detail');
Route::post('/event/{slug}/register', [RegistrationController::class, 'store'])
    ->middleware(['auth', 'role:mahasiswa'])
    ->name('event.register');

// WF2 - Login dan registrasi dipisahkan
Route::get('/auth', fn () => redirect()->route('login'))->name('auth.legacy');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    // Google Workspace for Education SSO (OAuth 2.0)
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.post');
Route::post('/aktivasi-mahasiswa', [AuthController::class, 'activateMahasiswa'])->middleware('guest')->name('activate.post');
Route::post('/daftar-ormawa', [AuthController::class, 'registerOrmawa'])->middleware('guest')->name('register.ormawa.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// WF4 - Instruksi & Verifikasi Pembayaran Event (UC-05)
Route::get('/pembayaran/{invoice?}', [PembayaranController::class, 'show'])
    ->middleware(['auth', 'role:mahasiswa'])
    ->name('pembayaran.show');
Route::post('/pembayaran/settle', [PembayaranController::class, 'settle'])
    ->middleware(['auth', 'role:mahasiswa'])
    ->name('pembayaran.settle');

// WF5 - Dashboard Mahasiswa (Pendaftaran Saya & Tiket Saya - UC-06 & UC-08)
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/event-saya', [PesertaController::class, 'index'])->name('peserta.events');
    Route::get('/api/tiket/{tiketNo}', [PesertaController::class, 'tiketDetail'])->name('peserta.tiket');

    // WF6 - Modul Presensi Mahasiswa
    Route::get('/presensi/{slug?}', [PresensiController::class, 'show'])->name('presensi.show');
    Route::post('/presensi/submit', [PresensiController::class, 'submitPresensi'])->name('presensi.submit.post');

    // WF7 - Pratinjau & Unduh E-Sertifikat Mahasiswa
    Route::get('/sertifikat-download/{nomor}', [SertifikatController::class, 'downloadFile'])->where('nomor', '.*')->name('sertifikat.download_file');
    Route::get('/sertifikat/{nomor?}', [SertifikatController::class, 'show'])->where('nomor', '.*')->name('sertifikat.show');
});


// Verifikasi sertifikat bersifat publik agar pihak luar dapat memeriksa nomor sertifikat.
Route::get('/verifikasi/sertifikat/{nomor}', [SertifikatController::class, 'verifikasi'])->where('nomor', '.*')->name('sertifikat.verifikasi');


// WF9 - Dashboard Panitia & CRUD Event Ormawa (UC-10)
Route::middleware(['auth', 'role:panitia'])->group(function () {
    Route::get('/panitia/dashboard', [PanitiaController::class, 'dashboard'])->name('panitia.dashboard');
    Route::get('/panitia/profil', [ProfilController::class, 'index'])->name('panitia.profil');
    Route::post('/panitia/event/store', [PanitiaController::class, 'storeEvent'])->name('panitia.event.store');
    Route::put('/panitia/event/{id}', [PanitiaController::class, 'updateEvent'])->name('panitia.event.update');
    Route::delete('/panitia/event/{id}', [PanitiaController::class, 'destroyEvent'])->name('panitia.event.destroy');
});

// Standalone & dynamic routes for WF10 - WF14 are defined below

// WF8 - Pusat Pemberitahuan / Drawer Notifikasi (UC-14)
Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'showPage'])->name('notifikasi.page');
    Route::get('/api/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/api/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/api/notifikasi/read-all', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.readAll');
});

// WF10: Kelola Peserta & Verifikasi Pembayaran (UC-12)
Route::middleware(['auth', 'role:panitia'])->group(function () {
    Route::get('/panitia/event/{id}/peserta', [KelolaPesertaController::class, 'index'])->name('panitia.peserta');
    Route::post('/panitia/peserta/{id}/approve-payment', [KelolaPesertaController::class, 'approvePayment'])->name('panitia.peserta.approve');
    Route::post('/panitia/peserta/{id}/reject-payment', [KelolaPesertaController::class, 'rejectPayment'])->name('panitia.peserta.reject');
    Route::post('/panitia/peserta/{id}/toggle-presence', [KelolaPesertaController::class, 'togglePresence'])->name('panitia.peserta.toggle_presence');

    // WF11 - Sesi Absensi Panitia
    Route::get('/panitia/event/{id}/sesi-absensi', [SesiAbsensiController::class, 'index'])->name('panitia.sesi_absensi');
    Route::get('/panitia/event/{id}/active-qr', [SesiAbsensiController::class, 'getActiveQrHash'])->name('panitia.active_qr');
    Route::post('/panitia/event/{id}/manual-checkin', [SesiAbsensiController::class, 'manualCheckin'])->name('panitia.manual_checkin');
    Route::post('/panitia/event/{id}/close-session', [SesiAbsensiController::class, 'closeSession'])->name('panitia.close_session');

    // WF12 - Penerbitan Sertifikat Massal & Upload Sertifikat Karya Ormawa (Hybrid / Canva)
    Route::get('/panitia/event/{id}/terbitkan-sertifikat', [PenerbitanSertifikatController::class, 'index'])->name('panitia.terbitkan_sertifikat');
    Route::post('/panitia/event/{id}/upload-bulk-canva', [PenerbitanSertifikatController::class, 'uploadBulkCanva'])->name('panitia.upload_bulk_canva');
    Route::post('/panitia/event/{id}/publish-batch-sertifikat', [PenerbitanSertifikatController::class, 'publishBatch'])->name('panitia.publish_batch');
    Route::post('/panitia/event/{id}/upload-template-sertifikat', [PenerbitanSertifikatController::class, 'uploadTemplate'])->name('panitia.upload_template_sertifikat');
    Route::delete('/panitia/event/{id}/hapus-template-sertifikat', [PenerbitanSertifikatController::class, 'deleteTemplate'])->name('panitia.delete_template_sertifikat');
    Route::post('/panitia/event/{id}/upload-file-sertifikat', [PenerbitanSertifikatController::class, 'uploadFilePeserta'])->name('panitia.upload_file_sertifikat');
});

// WF13: Admin Panel & Verifikasi Ormawa (UC-13)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/panel', [AdminController::class, 'index'])->name('admin.panel');
    Route::get('/admin/profil', fn () => redirect('/admin/panel#profil'))->name('admin.profil');
    Route::post('/admin/ormawa/{id}/approve', [AdminController::class, 'approveOrmawa'])->name('admin.ormawa.approve');
    Route::post('/admin/ormawa/{id}/reject', [AdminController::class, 'rejectOrmawa'])->name('admin.ormawa.reject');
    Route::post('/admin/event/{id}/approve', [AdminController::class, 'approveEvent'])->name('admin.event.approve');
    Route::post('/admin/event/{id}/reject', [AdminController::class, 'rejectEvent'])->name('admin.event.reject');
    Route::post('/admin/student/{id}/toggle-status', [AdminController::class, 'toggleStudentStatus'])->name('admin.student.toggle_status');
});

// WF14: Profil Pengguna & Keamanan (UC-15 & UC-16)
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil/update-kontak', [ProfilController::class, 'updateContact'])->name('profil.update_kontak');
    Route::post('/profil/update-password', [ProfilController::class, 'updatePassword'])->name('profil.update_password');
    Route::post('/profil/update-foto', [ProfilController::class, 'updateFoto'])->name('profil.update_foto');
    Route::post('/profil/delete-foto', [ProfilController::class, 'deleteFoto'])->name('profil.delete_foto');
});
