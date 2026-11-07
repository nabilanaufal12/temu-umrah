<!-- resources/views/pages/profil.blade.php -->
@extends('layouts.app')

@php
  $userRole = auth()->user()?->role ?? 'mahasiswa';
  $isRoleAdmin = $userRole === 'admin';
  $isRolePanitia = $userRole === 'panitia';
  $isRoleMahasiswa = $userRole === 'mahasiswa';

  $pageTitle = $isRoleAdmin ? 'Profil Super Admin BAAK - TEMU UMRAH' : ($isRolePanitia ? 'Profil Panitia & SK Ormawa - TEMU UMRAH' : 'Profil Mahasiswa - SIPA TEMU UMRAH');
@endphp

@section('title', $pageTitle)
@section('meta_desc', 'Sistem Informasi Pengelolaan Akademik & Pengaturan Akun TEMU UMRAH.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profil.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_PROFILE_USER = @json(auth()->user());
</script>

<main class="profil-wrapper">
  <div class="container">

    <!-- Breadcrumb SIPA -->
    <nav class="breadcrumb-wrapper" style="margin-bottom:1.25rem; font-size:0.88rem; color:#64748b; display:flex; align-items:center; gap:0.5rem;">
      <a href="{{ $isRolePanitia ? url('/panitia/dashboard') : ($isRoleAdmin ? url('/admin/panel') : url('/')) }}" style="color:#006aa7; text-decoration:none; font-weight:700;">Home</a>
      <span>/</span>
      <span style="color:#475569; font-weight:600;">
        @if($isRoleAdmin)
          Profil Admin BAAK
        @elseif($isRolePanitia)
          Profil Panitia Ormawa
        @else
          Profil Saya
        @endif
      </span>
    </nav>

    <!-- KARTU TUNGGAL TERSATUKAN ALA SIPA UMRAH -->
    <div class="sipa-unified-card">
      
      <!-- Tab Header Navigasi -->
      <div class="sipa-nav-tabs" role="tablist">
        <button type="button" class="sipa-tab-btn active" id="tabBtn_data" onclick="switchProfilTab('data')" role="tab" aria-selected="true">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span>Profil</span>
        </button>
        <button type="button" class="sipa-tab-btn" id="tabBtn_sandi" onclick="switchProfilTab('sandi')" role="tab" aria-selected="false">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>Ubah Kata Sandi</span>
        </button>
      </div>

      <!-- Tab Content Area -->
      <div class="sipa-tab-content">

        <!-- PANE 1: PROFIL & DATA IDENTITAS + PAS FOTO -->
        <div class="profil-tab-pane" id="tabPane_data">
          <div class="sipa-profile-flex">

            <!-- Kolom Form Data Kiri -->
            <div class="sipa-fields-column">

              @if($isRoleAdmin)
                <!-- Admin BAAK Rows -->
                <div class="sipa-form-row">
                  <span class="sipa-row-label">Nama Lembaga / Biro</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ auth()->user()?->nama ?? 'Biro Kemahasiswaan & Alumni UMRAH' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Kedudukan Instansi</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Biro Akademik, Kemahasiswaan &amp; Alumni (BAAK)" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Tingkat Hak Akses</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Super Admin Platform (Level 1)" readonly style="color:#b45309; font-weight:700;">
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Kantor Operasional</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Gedung Rektorat UMRAH Dompak Lt. 2" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Email Resmi Kampus</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ auth()->user()?->email ?? 'admin@umrah.ac.id' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Status Otoritas</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Aktif &bull; Administrator Resmi Platform" readonly style="color:#15803d; font-weight:700;">
                  </div>
                </div>

              @elseif($isRolePanitia)
                <!-- Panitia Ormawa Rows -->
                <div class="sipa-form-row">
                  <span class="sipa-row-label">Nama Organisasi (Ormawa)</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ $ormawa?->nama_ormawa ?? 'Organisasi Mahasiswa UMRAH' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Tingkat Kepengurusan</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Tingkat {{ ucfirst($ormawa?->tingkat ?? 'Himpunan') }} Mahasiswa" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Ketua Umum Ormawa</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ $ormawa?->ketua ?? '-' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Dosen Pembina</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ $ormawa?->pembina ?? '-' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Nomor SK Pengesahan</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ $ormawa?->no_sk ?? 'SK-BAAK/2026' }}" readonly style="color:#006aa7; font-weight:700;">
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Status Legalitas Ormawa</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Sah &bull; Terdaftar di BAAK UMRAH" readonly style="color:#15803d; font-weight:700;">
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Email Akun Panitia</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ auth()->user()?->email ?? '-' }}" readonly>
                  </div>
                </div>

              @else
                <!-- Mahasiswa Peserta Rows (Persis SIPA UMRAH) -->
                <div class="sipa-form-row">
                  <span class="sipa-row-label">NIM</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ auth()->user()?->nim ?? '-' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Nama Lengkap</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ auth()->user()?->nama ?? '-' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Jalur Pendaftaran</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="SNBP &bull; Sarjana (S1)" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Program Studi</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Teknik Informatika (S1)" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Fakultas</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Fakultas Teknik &amp; Teknologi Kemaritiman (FTTK)" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Email Google Kampus</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="{{ auth()->user()?->email ?? '-' }}" readonly>
                  </div>
                </div>

                <div class="sipa-form-row">
                  <span class="sipa-row-label">Status Perkuliahan</span>
                  <div class="sipa-row-field">
                    <input type="text" class="sipa-input-readonly" value="Mahasiswa Aktif SIPA &bull; Angkatan 20{{ substr(auth()->user()?->nim ?? '24', 0, 2) }}" readonly style="color:#15803d; font-weight:700;">
                  </div>
                </div>
              @endif

              <!-- Form Input Nomor WhatsApp / Handphone Aktif -->
              <form id="formUpdateKontak" onsubmit="simpanProfilKontak(event)">
                @csrf
                <div class="sipa-form-row">
                  <span class="sipa-row-label">
                    @if($isRoleAdmin)
                      Nomor Kontak Dinas <span style="color:#e11d48;">*</span>
                    @elseif($isRolePanitia)
                      Nomor WA Narahubung <span style="color:#e11d48;">*</span>
                    @else
                      Nomor Handphone / WA <span style="color:#e11d48;">*</span>
                    @endif
                  </span>
                  <div class="sipa-row-field">
                    <input type="tel" id="inputHp" name="no_hp" class="sipa-input-editable" value="{{ auth()->user()?->no_wa ?? '' }}" placeholder="Contoh: 081234567890" required>
                    <span style="font-size:0.75rem; color:#64748b; margin-top:0.3rem; display:block;">
                      @if($isRoleAdmin)
                        Nomor kontak dinas digunakan untuk koordinasi penting antar-unit kerja kampus.
                      @elseif($isRolePanitia)
                        Nomor WhatsApp ini ditampilkan ke calon peserta untuk informasi teknis kegiatan.
                      @else
                        Nomor aktif digunakan oleh panitia pelaksana event untuk konfirmasi kehadiran dan pengiriman tiket QR.
                      @endif
                    </span>
                  </div>
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid #e5e7eb;">
                  <button type="submit" id="btnSubmitKontak" class="sipa-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4l2 3h6l2-3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span>Simpan Perubahan</span>
                  </button>
                </div>
              </form>

            </div>

            <!-- Kolom Pas Foto Kanan (Ratio 3x4 Resmi SIPA) -->
            <div class="sipa-photo-column">
              <div class="sipa-pasfoto-box" id="sipaPasfotoBox">
                <img src="{{ auth()->user()?->avatar_url ?? '' }}" alt="Foto Profil Resmi" class="sipa-pasfoto-img" id="avatarLargeImg" style="{{ auth()->user()?->avatar_url ? '' : 'display:none;' }}">
                <div class="sipa-pasfoto-placeholder" id="avatarLargeInitial" style="{{ auth()->user()?->avatar_url ? 'display:none;' : '' }}">
                  {{ strtoupper(substr(auth()->user()?->nama ?? 'U', 0, 1)) }}
                </div>
              </div>

              <!-- Tombol Aksi Foto Pas Foto -->
              <div class="sipa-photo-actions">
                <button type="button" class="sipa-btn-photo-upload" onclick="triggerFotoUpload()">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  <span>Ubah Foto</span>
                </button>
                <button type="button" class="sipa-btn-photo-delete" id="popoverBtnHapus" onclick="triggerFotoDelete()" style="{{ auth()->user()?->avatar_url ? '' : 'display:none;' }}">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  <span>Hapus</span>
                </button>
              </div>

              <input type="file" id="fotoProfilInput" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp" style="display:none;" onchange="handleFotoUpload(this)">
            </div>

          </div>
        </div>

        <!-- PANE 2: UBAH KATA SANDI -->
        <div class="profil-tab-pane" id="tabPane_sandi" style="display:none;">
          <div class="profile-alert-box" style="background:#f0f9ff; border-color:rgba(2, 132, 199, 0.2); color:#0369a1; margin-bottom:1.5rem;">
            <svg class="profile-alert-icon" style="color:#0284c7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <div>
              <strong style="color:#0369a1; font-size:0.9rem;">Keamanan Kredensial Akun TEMU UMRAH</strong>
              <p style="margin:0.25rem 0 0; color:#0c4a6e; font-size:0.83rem;">
                Gunakan kombinasi kata sandi minimal 8 karakter dengan paduan huruf dan angka demi menjaga keamanan akses akun Anda.
              </p>
            </div>
          </div>

          <form id="formGantiPwd" onsubmit="ubahKataSandi(event)" style="max-width: 720px;">
            @csrf
            <div class="sipa-form-row" style="margin-bottom:1.25rem;">
              <span class="sipa-row-label">Kata Sandi Saat Ini <span style="color:#e11d48;">*</span></span>
              <div class="sipa-row-field">
                <div class="input-wrap-p">
                  <input type="password" id="inputPwdLama" class="sipa-input-editable" placeholder="Masukkan kata sandi lama Anda" required>
                  <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('inputPwdLama', this)" title="Tampilkan kata sandi">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="sipa-form-row" style="margin-bottom:1.25rem;">
              <span class="sipa-row-label">Kata Sandi Baru <span style="color:#e11d48;">*</span></span>
              <div class="sipa-row-field">
                <div class="input-wrap-p">
                  <input type="password" id="inputPwdBaru" class="sipa-input-editable" placeholder="Minimal 8 karakter" required minlength="8">
                  <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('inputPwdBaru', this)" title="Tampilkan kata sandi">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                </div>
                <div class="pwd-meter-box" style="margin-top:0.4rem;">
                  <div class="pwd-meter-bg" style="height:5px;">
                    <div class="pwd-meter-fill" id="pwdMeterFill"></div>
                  </div>
                  <span class="pwd-strength-text" id="pwdStrengthText" style="font-size:0.75rem;"></span>
                </div>
              </div>
            </div>

            <div class="sipa-form-row" style="margin-bottom:1.5rem;">
              <span class="sipa-row-label">Konfirmasi Sandi Baru <span style="color:#e11d48;">*</span></span>
              <div class="sipa-row-field">
                <div class="input-wrap-p">
                  <input type="password" id="inputPwdKonfirmasi" class="sipa-input-editable" placeholder="Ulangi kata sandi baru" required minlength="8">
                  <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('inputPwdKonfirmasi', this)" title="Tampilkan kata sandi">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                </div>
              </div>
            </div>

            <div style="display:flex; justify-content:flex-end; padding-top:1.25rem; border-top:1px solid #e5e7eb;">
              <button type="submit" id="btnSubmitPassword" class="sipa-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4l2 3h6l2-3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <span>Perbarui Kata Sandi</span>
              </button>
            </div>
          </form>
        </div>

      </div>

    </div>

  </div>
</main>

<!-- MODAL KONFIRMASI LOGOUT -->
<div class="modal-logout-backdrop" id="modalLogout" onclick="if(event.target===this) closeLogoutModal()">
  <div class="modal-logout-box" role="dialog" aria-modal="true" aria-labelledby="logoutTitle">
    <div class="modal-logout-icon">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </div>
    <h3 class="modal-logout-title" id="logoutTitle">
      Konfirmasi Keluar Sesi
    </h3>
    <p class="modal-logout-desc">
      Apakah Anda yakin ingin keluar dari akun TEMU UMRAH? Anda harus masuk kembali untuk mengakses akun Anda.
    </p>

    <form id="logoutFormModal" method="POST" action="{{ route('logout') }}">
      @csrf
      <div class="modal-logout-actions">
        <button type="button" class="btn-modal-cancel" onclick="closeLogoutModal()">
          Batal
        </button>
        <button type="submit" class="btn-modal-confirm">
          Ya, Keluar Sekarang
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/profil.js') }}"></script>
@endpush
