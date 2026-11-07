<!-- resources/views/pages/presensi_mahasiswa.blade.php -->
@extends('layouts.app')

@section('title', 'Presensi Kehadiran Mahasiswa - TEMU UMRAH')
@section('meta_desc', 'Konfirmasi presensi kegiatan mahasiswa UMRAH melalui pemindai kamera QR code dinamis atau verifikasi kode token alfanumerik resmi.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/presensi.css') }}">
@endpush

@section('content')
<main class="presensi-wrapper">
  <div class="container">

    <!-- Header Strip (Tanpa Breadcrumb Sesuai Panduan) -->
    <section class="presensi-header-strip">
      <div class="presensi-header-left">
        <div class="presensi-eyebrow">
          <span class="presensi-pulse-dot"></span>
          <span>SISTEM PRESENSI &amp; VALIDASI TIKET RESMI</span>
        </div>
        <h1 class="presensi-page-title">Presensi Digital Mahasiswa</h1>
        <p class="presensi-page-desc">
          Konfirmasi kehadiran mandiri untuk kegiatan akademik sivitas akademika UMRAH. Gunakan pemindai kamera QR Code dinamis panitia atau masukkan kode token unik resmi.
        </p>
      </div>

      @if(isset($registrations) && count($registrations) > 1)
        <!-- Event Switcher Dropdown jika mahasiswa punya beberapa event aktif -->
        <div class="presensi-header-switcher">
          <label for="eventSelectDropdown" class="switcher-label">Pilih Sesi Kegiatan:</label>
          <div class="switcher-select-wrap">
            <svg class="switcher-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <select id="eventSelectDropdown" class="switcher-select" onchange="window.location.href = this.value;">
              @foreach($registrations as $r)
                <option value="{{ url('/presensi/' . ($r->event?->slug ?? '')) }}" {{ ($activeRegistration?->id === $r->id) ? 'selected' : '' }}>
                  {{ Str::limit($r->event?->judul ?? 'Event UMRAH', 40) }} ({{ $r->presensi ? 'Hadir' : 'Belum Hadir' }})
                </option>
              @endforeach
            </select>
          </div>
        </div>
      @endif
    </section>

    @if($activeEvent && $activeRegistration)
      <!-- Active Attendance Session Banner -->
      <section class="session-info-card {{ $hasPresensi ? 'session-info-attended' : '' }}">
        <div class="session-meta-left">
          <div class="session-status-badge {{ $hasPresensi ? 'badge-attended' : 'badge-active' }}">
            @if($hasPresensi)
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>KEHADIRAN SUDAH TERCATAT</span>
            @else
              <span class="session-live-pulse"></span>
              <span>SESI PRESENSI AKTIF</span>
            @endif
          </div>

          <h2 class="session-event-title" id="sessionEventTitle">
            {{ $activeEvent->judul }}
          </h2>

          <div class="session-chips-row">
            <div class="session-chip">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span>{{ \Carbon\Carbon::parse($activeEvent->tanggal_kegiatan)->translatedFormat('d F Y') }}</span>
            </div>
            <div class="session-chip">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span id="sessionLocation">{{ $activeEvent->lokasi ?? 'Auditorium Kampus UMRAH Dompak' }}</span>
            </div>
            <div class="session-chip">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              <span>{{ $activeEvent->ormawa?->nama_ormawa ?? 'BEM FTTK UMRAH' }}</span>
            </div>
            <div class="session-chip session-chip-reg">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="12" y2="16"/></svg>
              <span>#REG-{{ str_pad($activeRegistration->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
          </div>
        </div>

        <div class="session-meta-right">
          <div class="session-countdown-box">
            <span class="countdown-label">STATUS SESI:</span>
            <div class="countdown-value">
              @if($hasPresensi)
                <span class="text-success-strong">Terverifikasi</span>
              @else
                <span>Sesi Terbuka</span>
              @endif
            </div>
            <span class="countdown-sub">
              @if($hasPresensi)
                Dicatat: {{ $presensiData?->waktu_hadir ? $presensiData->waktu_hadir->translatedFormat('H:i') . ' WIB' : 'Hari ini' }}
              @else
                Pindai sebelum sesi ditutup
              @endif
            </span>
          </div>
        </div>
      </section>

      @if(!$hasPresensi)
        <!-- Panduan Alur Presensi -->
        <section class="attendance-steps" id="attendanceStepsSection" aria-label="Panduan presensi">
          <div class="attendance-step-card">
            <div class="step-badge-wrap">
              <span class="step-num-pill">01</span>
              <div class="step-icon-circle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/></svg>
              </div>
            </div>
            <div class="step-body">
              <strong>Siapkan E-Tiket Mahasiswa</strong>
              <p>Pastikan Anda login menggunakan akun aktif yang telah terdaftar pada kegiatan ini.</p>
            </div>
          </div>

          <div class="attendance-step-card">
            <div class="step-badge-wrap">
              <span class="step-num-pill">02</span>
              <div class="step-icon-circle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
              </div>
            </div>
            <div class="step-body">
              <strong>Gunakan Salah Satu Metode</strong>
              <p>Scan QR dinamis di layar proyektor panggung atau masukkan kode token dari panitia.</p>
            </div>
          </div>

          <div class="attendance-step-card">
            <div class="step-badge-wrap">
              <span class="step-num-pill">03</span>
              <div class="step-icon-circle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
            </div>
            <div class="step-body">
              <strong>Klaim Hadir &amp; Poin SKP</strong>
              <p>Status kehadiran tercatat otomatis di pangkalan data untuk penerbitan e-sertifikat.</p>
            </div>
          </div>
        </section>
      @endif

      <!-- 2-Column Attendance Methods Grid -->
      <section id="presensiGridSection" class="presensi-grid" style="display: {{ $hasPresensi ? 'none' : 'grid' }};">
        
        <!-- ==================== METODE A: SCAN QR CODE ==================== -->
        <article class="presensi-method-card">
          <header class="method-title-header">
            <div class="method-badge-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
              </svg>
            </div>
            <div>
              <h3 class="method-heading">Metode A: Scan QR Code Panitia</h3>
              <p class="method-sub">Arahkan kamera perangkat Anda ke QR Code dinamis yang ditampilkan di layar proyektor ruangan.</p>
            </div>
          </header>

          <!-- Camera Scanner Viewfinder -->
          <div class="camera-scanner-wrapper" id="cameraScannerWrapper">
            <div id="interactiveQrReader" style="width: 100%; height: 100%; position: absolute; inset: 0; z-index: 1;"></div>
            <div class="scanner-reticle-box" id="scannerReticleBox" style="position: relative; z-index: 2; pointer-events: none;">
              <div class="scanner-laser-line"></div>
              <div class="reticle-caption" id="reticleCaption">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/></svg>
                <span id="scannerStatusText">Arahkan ke QR Code di Layar Proyektor Panitia</span>
              </div>
            </div>
          </div>

          <button type="button" id="btnToggleCamera" class="btn btn-primary btn-scan-action" onclick="toggleRealCameraScan()" style="margin-bottom: 1.25rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <span id="btnToggleCameraText">Aktifkan Kamera &amp; Pindai QR</span>
          </button>

          <div class="anti-fraud-note">
            <div class="anti-fraud-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div class="anti-fraud-text">
              <strong>Aturan Anti-Kecurangan Presensi QR:</strong>
              <span>QR Code di proyektor panitia berganti otomatis tiap 30 detik. Sistem memblokir foto atau tangkapan layar tiruan di luar ruangan.</span>
            </div>
          </div>
        </article>

        <!-- ==================== METODE B: INPUT KODE TOKEN ==================== -->
        <article class="presensi-method-card">
          <header class="method-title-header">
            <div class="method-badge-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="4" width="20" height="16" rx="2"/>
                <line x1="6" y1="8" x2="6.01" y2="8"/>
                <line x1="10" y1="8" x2="10.01" y2="8"/>
                <line x1="14" y1="8" x2="14.01" y2="8"/>
                <line x1="18" y1="8" x2="18.01" y2="8"/>
              </svg>
            </div>
            <div>
              <h3 class="method-heading">Metode B: Input Kode Token Presensi</h3>
              <p class="method-sub">Gunakan opsi cadangan resmi jika kamera Anda mengalami kendala atau untuk kegiatan hybrid / daring.</p>
            </div>
          </header>

          <form id="tokenPresensiForm" onsubmit="event.preventDefault(); submitTokenPresence();">
            <input type="hidden" id="presensiRegistrationId" value="{{ $activeRegistration->id }}">
            <input type="hidden" id="presensiCsrfToken" value="{{ csrf_token() }}">

            <div class="token-input-group">
              <label for="tokenBox0" class="token-label">KODE TOKEN RESMI DARI PANITIA:</label>
              <div class="token-boxes-row" id="tokenBoxesContainer">
                <input type="text" id="tokenBox0" maxlength="1" class="token-box-input" autocomplete="off">
                <input type="text" id="tokenBox1" maxlength="1" class="token-box-input" autocomplete="off">
                <input type="text" id="tokenBox2" maxlength="1" class="token-box-input" autocomplete="off">
                <input type="text" id="tokenBox3" maxlength="1" class="token-box-input" autocomplete="off">
                <input type="text" id="tokenBox4" maxlength="1" class="token-box-input" autocomplete="off">
                <input type="text" id="tokenBox5" maxlength="1" class="token-box-input" autocomplete="off">
              </div>
              <p class="token-hint-text">
                Masukkan 5 hingga 6 karakter token yang dibacakan MC atau ditampilkan panitia kegiatan.
              </p>
            </div>

            <div id="tokenErrorMessage" class="token-error-alert" style="display: none;"></div>

            <button type="submit" id="submitTokenBtn" class="btn btn-primary btn-token-submit">
              <span>Konfirmasi Kehadiran Sekarang</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </form>

          <div class="token-info-callout">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Kode token diumumkan resmi oleh pembawa acara di panggung utama atau disampaikan melalui kolom obrolan ruang virtual pada sesi presensi.</span>
          </div>
        </article>

      </section>

      <!-- ==================== STATUS SUKSES PRESENSI ==================== -->
      <section id="attendanceSuccessCard" class="attendance-success-card" style="display: {{ $hasPresensi ? 'block' : 'none' }};">
        <div class="attendance-success-icon">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>

        <h2 class="success-title">
          Presensi Kehadiran Berhasil Diverifikasi!
        </h2>
        <p class="success-desc">
          Terima kasih, data kehadiran Anda telah tercatat sah di pangkalan data kampus UMRAH dan tersinkronisasi ke sistem poin SKP.
        </p>

        <div class="attendance-details-list">
          <div class="att-row">
            <span>Nama Mahasiswa</span>
            <span id="attName">{{ auth()->user()->nama ?? 'Mahasiswa UMRAH' }}</span>
          </div>
          <div class="att-row">
            <span>NIM Resmi</span>
            <span id="attNim">{{ auth()->user()->nim ?? '-' }}</span>
          </div>
          <div class="att-row">
            <span>Kegiatan Kampus</span>
            <span id="attEvent">{{ $activeEvent->judul }}</span>
          </div>
          <div class="att-row">
            <span>Waktu Presensi</span>
            <span id="attTime">
              {{ $presensiData?->waktu_hadir ? $presensiData->waktu_hadir->translatedFormat('d F Y, H:i') . ' WIB' : now()->translatedFormat('d F Y, H:i') . ' WIB' }}
            </span>
          </div>
          <div class="att-row">
            <span>Metode Verifikasi</span>
            <span id="attMethod" class="text-brand-strong">
              {{ ($presensiData?->metode === 'scan_qr') ? 'Pemindaian Kamera QR Code Dinamis' : 'Input Kode Token Alfanumerik Resmi' }}
            </span>
          </div>
          <div class="att-row">
            <span>Status E-Sertifikat &amp; SKP</span>
            <span class="text-success-badge">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Siap Diunduh / Terhubung ke SIPA</span>
            </span>
          </div>
        </div>

        <div class="attendance-actions-row">
          <a href="{{ url('/event-saya') }}" class="btn btn-outline btn-lg-action">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <span>Kembali ke Event Saya</span>
          </a>
          <a href="{{ url('/sertifikat') }}" class="btn btn-primary btn-lg-action">
            <span>Buka E-Sertifikat Digital</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        </div>
      </section>

    @else
      <!-- Empty State jika mahasiswa belum punya pendaftaran event -->
      <section class="presensi-empty-card">
        <div class="presensi-empty-icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <h3 class="empty-title">Tidak Ada Sesi Presensi Aktif</h3>
        <p class="empty-desc">
          Anda belum terdaftar pada kegiatan kampus yang sedang membuka sesi presensi kehadiran saat ini. Silakan daftar kegiatan terlebih dahulu pada katalog event.
        </p>
        <a href="{{ route('katalog.jelajah') }}" class="btn btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <span>Jelajah Katalog Event Kampus</span>
        </a>
      </section>
    @endif

  </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
<script src="{{ asset('assets/js/presensi.js') }}"></script>
@endpush
