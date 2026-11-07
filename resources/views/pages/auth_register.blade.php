<!-- resources/views/pages/auth_register.blade.php -->
@extends('layouts.app')

@section('title', 'Registrasi & Aktivasi Akun - TEMU UMRAH')
@section('meta_desc', 'Aktivasi akun mahasiswa dan pendaftaran organisasi mahasiswa di portal TEMU UMRAH.')
@section('body_class', 'auth-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('content')
<main class="auth-modern-wrapper">
  <!-- Minimalist Brand Header (No Distracting Public Navbar) -->
  <div class="auth-brand-center">
    <a href="{{ url('/') }}" class="auth-brand-link" title="TEMU UMRAH">
      <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" width="44" height="44" alt="Logo TEMU UMRAH" class="auth-brand-logo">
      <span class="auth-brand-name">TEMU UMRAH</span>
    </a>
  </div>

  <!-- Header Strip & Eyebrow -->
  <header class="auth-header-strip">
    <div class="auth-eyebrow">
      <span class="auth-pulse-dot"></span>
      <span>Registrasi &amp; Aktivasi Portal</span>
    </div>
    <h1 class="auth-page-title">Aktivasi &amp; Registrasi Akun</h1>
    <p class="auth-page-desc">
      Pilih jalur yang sesuai. Mahasiswa mengaktifkan akun resmi dengan verifikasi NIM, sedangkan organisasi mahasiswa mengajukan legalitas kepengurusan.
    </p>
  </header>

  <!-- Main Registration Card -->
  <div class="auth-card-modern auth-card-register">
    @if(session('success'))
      <div class="alert-popup alert-success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="alert-popup alert-danger">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ session('error') }}</span>
      </div>
    @endif
    @if($errors->any())
      <div class="alert-popup alert-danger">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <!-- Tab Switcher -->
    <div class="auth-tabs" role="tablist">
      <button type="button" id="tabMahasiswa" class="auth-tab-btn active" role="tab" aria-selected="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        <span>Aktivasi Mahasiswa</span>
      </button>
      <button type="button" id="tabOrmawa" class="auth-tab-btn" role="tab" aria-selected="false">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Daftar Ormawa</span>
      </button>
    </div>

    <!-- Section 1: Mahasiswa Activation -->
    <div id="formSectionMahasiswa">
      <!-- Step 1: Verification -->
      <div class="step-card active" id="step1Card">
        <div class="step-header">
          <div class="step-badge">1</div>
          <h3 class="step-title">Verifikasi Data Mahasiswa</h3>
        </div>
        <div class="auth-form-grid-2">
          <div class="form-group">
            <label class="form-label" for="nimInput">NIM Mahasiswa *</label>
            <div class="input-with-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" id="nimInput" class="form-input" placeholder="Masukkan NIM (contoh: 2401020073)" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="birthdateInput">Tanggal Lahir (opsional)</label>
            <div class="input-with-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <input type="text" id="birthdateInput" class="form-input" placeholder="DD/MM/YYYY">
            </div>
          </div>
        </div>
        <div class="auth-form-action-row">
          <span>Gunakan NIM aktif yang terdaftar di pangkalan data akademik UMRAH.</span>
          <button type="button" id="checkNimBtn" class="btn-outline">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <span>Cek Data Mahasiswa</span>
          </button>
        </div>
        <div id="studentResultBox" class="student-found-box" style="display:none;"></div>
      </div>

      <!-- Step 2: Set Password & WhatsApp -->
      <form id="activationForm" action="{{ route('activate.post') }}" method="POST" data-backend-auth="true">
        @csrf
        <input type="hidden" name="nim" id="activationNimHidden">
        <div id="step2Card" class="step-card" style="opacity:0.5; pointer-events:none;">
          <div class="step-header">
            <div class="step-badge">2</div>
            <h3 class="step-title">Buat Kata Sandi &amp; Kontak</h3>
          </div>
          <div class="form-group">
            <label class="form-label">Email Kampus Resmi (Otomatis dari Sistem)</label>
            <div class="input-with-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <input type="email" id="lockedEmailInput" class="form-input input-locked" readonly placeholder="Terisi otomatis setelah NIM valid">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="waInput">Nomor WhatsApp Aktif *</label>
            <div class="input-with-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              <input type="tel" name="no_wa" id="waInput" class="form-input" placeholder="08xxxxxxxxxx (untuk kirim tiket &amp; token presensi)" required>
            </div>
          </div>
          <div class="auth-form-grid-2">
            <div class="form-group">
              <label class="form-label" for="newPassInput">Kata Sandi Baru *</label>
              <div class="input-with-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" name="password" id="newPassInput" class="form-input" placeholder="Minimal 8 karakter" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="confirmPassInput">Ulangi Kata Sandi *</label>
              <div class="input-with-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <input type="password" name="password_confirmation" id="confirmPassInput" class="form-input" placeholder="Ketik ulang kata sandi" required>
              </div>
            </div>
          </div>
          <label class="form-checkbox-label auth-terms">
            <input type="checkbox" id="termsAgree" required checked>
            <span>Saya menyetujui syarat &amp; ketentuan penggunaan portal kegiatan TEMU UMRAH.</span>
          </label>
          <button type="submit" id="activateBtn" class="btn-auth-primary" disabled>
            <span>Aktifkan Akun &amp; Simpan Kata Sandi</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>
        </div>
      </form>
    </div>

    <!-- Section 2: Ormawa Registration -->
    <div id="formSectionOrmawa" style="display:none;">
      <div class="auth-document-note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <span>Format legalitas yang diterima: PDF, DOC, atau DOCX (Maks 5 MB). Pengajuan akan diverifikasi oleh Admin Kemahasiswaan (BAAK) UMRAH.</span>
      </div>

      <form id="ormawaRegisterForm" action="{{ route('register.ormawa.post') }}" method="POST" enctype="multipart/form-data" data-backend-auth="true">
        @csrf
        <div class="form-group">
          <label class="form-label" for="ormawaNamaInput">Nama Lembaga / Ormawa *</label>
          <input type="text" name="nama_ormawa" id="ormawaNamaInput" class="form-input" placeholder="Contoh: Himpunan Mahasiswa Teknik Informatika (HMTI)" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="ormawaTingkatSelect">Tingkat Organisasi *</label>
          <select name="tingkat" id="ormawaTingkatSelect" class="form-input" required>
            <option value="himpunan">Himpunan Program Studi</option>
            <option value="fakultas">Tingkat Fakultas</option>
            <option value="ukm">Unit Kegiatan Mahasiswa (UKM)</option>
            <option value="universitas">Tingkat Universitas (BEM / DPM UMRAH)</option>
          </select>
        </div>

        <div class="auth-form-grid-2">
          <div class="form-group">
            <label class="form-label" for="ormawaKetuaInput">Nama Ketua / Pemohon</label>
            <input type="text" name="ketua" id="ormawaKetuaInput" class="form-input" placeholder="Nama lengkap ketua">
          </div>
          <div class="form-group">
            <label class="form-label" for="ormawaPembinaInput">Dosen Pembina</label>
            <input type="text" name="pembina" id="ormawaPembinaInput" class="form-input" placeholder="Nama dosen pembina">
          </div>
        </div>

        <div class="auth-form-grid-2">
          <div class="form-group">
            <label class="form-label" for="ormawaEmailInput">Email Narahubung Ormawa *</label>
            <input type="email" name="email" id="ormawaEmailInput" class="form-input" placeholder="ormawa@umrah.ac.id" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="ormawaWaInput">No. WhatsApp Resmi *</label>
            <input type="tel" name="no_wa" id="ormawaWaInput" class="form-input" placeholder="08xxxxxxxxxx" required>
          </div>
        </div>

        <div class="sk-upload-box">
          <strong>Legalitas &amp; Dokumen SK Kepengurusan Resmi</strong>
          <div class="form-group">
            <label class="form-label" for="ormawaNoSkInput">Nomor SK Pengesahan Dekan / Rektor</label>
            <input type="text" name="no_sk" id="ormawaNoSkInput" class="form-input" placeholder="Nomor surat keputusan pengesahan">
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="ormawaSkFileInput">Unggah Berkas SK Legalitas *</label>
            <input type="file" name="dokumen_sk" id="ormawaSkFileInput" class="sk-file-input" accept=".pdf,.doc,.docx,application/pdf" required>
            <span class="auth-field-hint">Maksimal ukuran file 5 MB.</span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="ormawaPassInput">Kata Sandi Akun Ormawa *</label>
          <input type="password" name="password" id="ormawaPassInput" class="form-input" placeholder="Minimal 8 karakter" required>
        </div>

        <div class="auth-document-note auth-warning-note">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <span>Akun kepanitiaan akan otomatis aktif setelah dokumen SK diverifikasi oleh Admin BAAK.</span>
        </div>

        <button type="submit" id="ormawaSubmitBtn" class="btn-auth-primary">
          <span>Daftarkan Ormawa &amp; Kirim Berkas</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>
    </div>

    <!-- Switch to Login -->
    <div class="auth-switch-card">
      <div class="auth-switch-info">
        <strong>Sudah memiliki akun?</strong>
        <span>Masuk menggunakan NIM atau akun Google resmi kampus Anda.</span>
      </div>
      <a href="{{ route('login') }}" class="btn-switch-action">
        <span>Masuk ke Portal &rarr;</span>
      </a>
    </div>
  </div>

  <!-- Trust Badges Strip -->
  <div class="auth-trust-strip" aria-label="Jaminan keamanan platform">
    <div class="auth-trust-item">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      <span>Verifikasi Pangkalan Data Akademik</span>
    </div>
    <div class="auth-trust-item">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
      <span>Khusus Mahasiswa &amp; Ormawa UMRAH</span>
    </div>
    <div class="auth-trust-item">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
      <span>Akses Cepat 24/7</span>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
