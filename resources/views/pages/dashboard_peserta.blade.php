<!-- resources/views/pages/dashboard_peserta.blade.php -->
@extends('layouts.app')

@section('title', 'Event Saya & E-Tiket - TEMU UMRAH')
@section('meta_desc', 'Kelola riwayat pendaftaran kegiatan kampus, akses tiket QR presensi resmi, dan unduh e-sertifikat sivitas akademika UMRAH.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/dashboard_peserta.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_PROFILE_USER = @json(auth()->user());
  window.TEMU_BACKEND_REGISTRATIONS = @json($registrations);
</script>

<main class="peserta-wrapper">
  <div class="container">

    @if(session('success'))
      <div class="alert-popup alert-success" id="alertSuccessBanner" style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 1rem; background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #065f46;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span style="font-weight: 600; font-size: 0.92rem;">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="document.getElementById('alertSuccessBanner').remove()" style="background: none; border: none; color: #065f46; cursor: pointer; padding: 0.25rem; display: inline-flex; align-items: center; opacity: 0.8;" aria-label="Tutup notifikasi">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
    @endif

    <!-- Header Strip -->
    <section class="peserta-header-strip">
      <div>
        <div class="peserta-eyebrow">
          <span class="peserta-pulse-dot"></span>
          <span>PORTAL KEPESERTAAN &amp; E-TIKET</span>
        </div>
        <h1 class="peserta-page-title">Event Saya &amp; E-Tiket</h1>
        <p class="peserta-page-desc">
          Pantau status pendaftaran, akses tiket QR presensi kegiatan, dan unduh e-sertifikat resmi Anda dalam satu tempat.
        </p>
      </div>
      <div class="peserta-header-actions">
        <a href="{{ route('katalog.jelajah') }}" class="btn-peserta-cta">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <span>Jelajah Event</span>
        </a>
      </div>
    </section>

    <!-- Student Welcome Banner (Ramping & Bebas Duplikasi Shortcut) -->
    <section class="peserta-welcome-banner" aria-label="Ringkasan identitas mahasiswa">
      <div class="welcome-info-left">
        <div class="welcome-avatar-chip">
          @if(auth()->user()?->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="welcome-avatar-img">
          @else
            {{ strtoupper(substr(auth()->user()?->nama ?? 'M', 0, 1)) }}
          @endif
        </div>
        <div class="welcome-titles">
          <h2>Halo, {{ auth()->user()?->nama ?? 'Mahasiswa UMRAH' }}!</h2>
          <p>
            NIM: <strong>{{ auth()->user()?->nim ?? '-' }}</strong> &bull; Sivitas Akademika Universitas Maritim Raja Ali Haji
          </p>
        </div>
      </div>
      <div class="welcome-stats-summary">
        <div class="summary-pill">
          <span class="summary-num" id="sumTotal">{{ $totalEvents ?? 0 }}</span>
          <span class="summary-label">Total Terdaftar</span>
        </div>
        <div class="summary-pill">
          <span class="summary-num text-emerald" id="sumHadir">{{ $totalHadir ?? 0 }}</span>
          <span class="summary-label">Hadir Resmi</span>
        </div>
        <div class="summary-pill">
          <span class="summary-num text-blue" id="sumCert">{{ $totalSertifikat ?? 0 }}</span>
          <span class="summary-label">E-Sertifikat</span>
        </div>
      </div>
    </section>

    <!-- Segmented Filter Toolbar (Pusat Navigasi & Filter Interaktif) -->
    <section class="peserta-filter-toolbar">
      <div class="segmented-filter-bar" role="tablist">
        <button type="button" class="peserta-tab-btn active" data-filter="SEMUA" role="tab" aria-selected="true">
          <span>Semua Event</span>
          <span id="countSemua" class="tab-counter-badge">{{ $totalEvents ?? 0 }}</span>
        </button>
        <button type="button" class="peserta-tab-btn" data-filter="PENDING" role="tab" aria-selected="false">
          <span>Menunggu Pembayaran</span>
          <span id="countPending" class="tab-counter-badge">{{ $totalPending ?? 0 }}</span>
        </button>
        <button type="button" class="peserta-tab-btn" data-filter="TERDAFTAR" role="tab" aria-selected="false">
          <span>Tiket Aktif / Siap Hadir</span>
          <span id="countTerdaftar" class="tab-counter-badge">{{ $totalActive ?? 0 }}</span>
        </button>
        <button type="button" class="peserta-tab-btn" data-filter="SELESAI" role="tab" aria-selected="false">
          <span>Hadir &amp; Selesai</span>
          <span id="countSelesai" class="tab-counter-badge">{{ $totalHadir ?? 0 }}</span>
        </button>
      </div>

      <!-- Quick Search Box -->
      <div class="peserta-search-box">
        <svg class="search-box-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="inputSearchEventSaya" class="peserta-search-input" placeholder="Cari kegiatan atau ormawa..." aria-label="Cari tiket kegiatan">
        <button type="button" id="btnClearSearch" class="btn-clear-search" style="display:none;" title="Bersihkan pencarian" aria-label="Bersihkan pencarian">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
    </section>

    <!-- Registration Cards List (Rendered dynamically via dashboard_peserta.js) -->
    <section id="regListContainer" class="registrations-list">
      <!-- Injected via JavaScript -->
    </section>

  </div>
</main>

<!-- ==================== MODAL E-TIKET & QR CODE ==================== -->
<div id="ticketModal" class="ticket-modal-backdrop" onclick="if(event.target===this) closeTicketModal()">
  <div class="ticket-modal-box" role="dialog" aria-modal="true" aria-labelledby="ticketEventTitle">
    
    <div class="modal-pass-header">
      <div class="modal-brand-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <span>E-Tiket Resmi Sivitas Akademika UMRAH</span>
      </div>
      <h3 id="ticketEventTitle" class="modal-pass-title">Seminar Nasional AI</h3>
      <p class="modal-pass-subtitle" id="ticketOrganizer">Penyelenggara: Ormawa UMRAH</p>
    </div>

    <div class="modal-qr-zone">
      <div class="modal-qr-frame">
        <svg id="ticketQrGraphic" width="160" height="160" viewBox="0 0 100 100" fill="#0f172a">
          <rect width="100" height="100" fill="#ffffff"/>
          <!-- Position Marker Top-Left -->
          <rect x="6" y="6" width="26" height="26" rx="4" fill="#0f172a"/>
          <rect x="10" y="10" width="18" height="18" rx="2" fill="#ffffff"/>
          <rect x="14" y="14" width="10" height="10" rx="1" fill="#1d4ed8"/>
          <!-- Position Marker Top-Right -->
          <rect x="68" y="6" width="26" height="26" rx="4" fill="#0f172a"/>
          <rect x="72" y="10" width="18" height="18" rx="2" fill="#ffffff"/>
          <rect x="76" y="14" width="10" height="10" rx="1" fill="#1d4ed8"/>
          <!-- Position Marker Bottom-Left -->
          <rect x="6" y="68" width="26" height="26" rx="4" fill="#0f172a"/>
          <rect x="10" y="72" width="18" height="18" rx="2" fill="#ffffff"/>
          <rect x="14" y="76" width="10" height="10" rx="1" fill="#1d4ed8"/>
          <!-- Data Matrix Patterns -->
          <rect x="40" y="10" width="8" height="8" rx="1" fill="#0f172a"/>
          <rect x="52" y="10" width="8" height="8" rx="1" fill="#0f172a"/>
          <rect x="40" y="24" width="14" height="8" rx="1" fill="#0f172a"/>
          <rect x="10" y="40" width="10" height="10" rx="1" fill="#0f172a"/>
          <rect x="26" y="40" width="8" height="12" rx="1" fill="#0f172a"/>
          <rect x="40" y="40" width="20" height="20" rx="2" fill="#1d4ed8"/>
          <rect x="44" y="44" width="12" height="12" rx="1" fill="#ffffff"/>
          <rect x="47" y="47" width="6" height="6" fill="#1d4ed8"/>
          <rect x="66" y="40" width="12" height="8" rx="1" fill="#0f172a"/>
          <rect x="82" y="40" width="8" height="14" rx="1" fill="#0f172a"/>
          <rect x="66" y="54" width="14" height="8" rx="1" fill="#0f172a"/>
          <rect x="40" y="68" width="10" height="10" rx="1" fill="#0f172a"/>
          <rect x="56" y="68" width="8" height="16" rx="1" fill="#0f172a"/>
          <rect x="68" y="68" width="24" height="10" rx="1" fill="#0f172a"/>
          <rect x="40" y="82" width="10" height="8" rx="1" fill="#0f172a"/>
          <rect x="72" y="82" width="20" height="8" rx="1" fill="#0f172a"/>
        </svg>
      </div>
      <div id="ticketNumber" class="modal-ticket-id">#TIK-UMRAH-10012</div>
      <span class="modal-qr-hint">Tunjukkan kode QR ini ke panitia di meja registrasi saat tiba di lokasi</span>
    </div>

    <div class="modal-pass-details">
      <div class="pass-row">
        <span class="pass-row-label">Nama Mahasiswa:</span>
        <span id="ticketUserName" class="pass-row-val">{{ auth()->user()?->nama ?? 'Mahasiswa UMRAH' }}</span>
      </div>
      <div class="pass-row">
        <span class="pass-row-label">NIM Mahasiswa:</span>
        <span id="ticketUserNim" class="pass-row-val">{{ auth()->user()?->nim ?? '-' }}</span>
      </div>
      <div class="pass-row">
        <span class="pass-row-label">Jadwal Pelaksanaan:</span>
        <span id="ticketDate" class="pass-row-val">Sabtu, 18 Oktober 2026</span>
      </div>
      <div class="pass-row">
        <span class="pass-row-label">Lokasi Kegiatan:</span>
        <span id="ticketLocation" class="pass-row-val">Auditorium Kampus Dompak</span>
      </div>
      <div class="pass-row">
        <span class="pass-row-label">Status Verifikasi:</span>
        <span class="pass-row-val" style="color:#1d4ed8; font-weight:700;">Terverifikasi SIPA UMRAH</span>
      </div>
    </div>

    <div class="modal-pass-actions">
      <button type="button" class="btn-modal-print" onclick="window.print()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        <span>Cetak / Simpan PDF</span>
      </button>
      <button type="button" class="btn-modal-close" onclick="closeTicketModal()">
        <span>Tutup</span>
      </button>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dashboard_peserta.js') }}"></script>
@endpush
