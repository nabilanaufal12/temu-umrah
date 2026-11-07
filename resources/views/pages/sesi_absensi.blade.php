<!-- resources/views/pages/sesi_absensi.blade.php -->
@extends('layouts.app')

@section('title', 'Layar Absensi Proyektor QR Dinamis - TEMU UMRAH')
@section('meta_desc', 'Mode auditorium layar penuh dengan QR code dinamis berganti tiap 30 detik untuk absensi massal.')
@section('body_class', 'projection-body proyektor-body')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/sesi_absensi.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_SESSION_STATS = @json($sessionStats);
  window.TEMU_EVENT_ID = {{ $eventId }};
  window.TEMU_SESSION_TOKEN = "{{ $sessionToken }}";
  window.csrfToken = "{{ csrf_token() }}";
</script>

  <!-- Top Gateway Bar -->
  <header class="proj-gateway-bar">
    <div class="proj-gateway-brand">
      <span class="proj-gateway-logo">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polygon points="12 8 8 12 12 16 16 12 12 8"/></svg>
      </span>
      <span>TEMU UMRAH &bull; Layar Presensi Auditorium</span>
      <span class="proj-gateway-badge">LIVE PROJEKTOR</span>
    </div>
    <div style="display:flex; align-items:center; gap:0.75rem;">
      <a href="{{ url('/panitia/event/' . $eventId . '/peserta') }}" class="proj-btn-esc" style="background: rgba(2, 132, 199, 0.2); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Tabel Peserta</span>
      </a>
      <a href="{{ url('/panitia/dashboard') }}" class="proj-btn-esc">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Dashboard</span>
      </a>
    </div>
  </header>

  <!-- Event Title & Status Banner -->
  <section class="proj-header">
    <h1>{{ strtoupper($event->nama_kegiatan) }}</h1>
    <div class="proj-header-meta">
      <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> {{ $event->lokasi }}</span>
      <span>&bull;</span>
      <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> Penyelenggara: {{ $event->organisasi }}</span>
      <span>&bull;</span>
      <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> {{ $event->tanggal }}</span>
    </div>
    <div>
      <span class="proj-pulse-badge">
        <span class="pulse-dot"></span>
        <span>SESI PRESENSI AKTIF &amp; TERBUKA</span>
      </span>
    </div>
  </section>

  <!-- 2-Columns Layout: Dynamic QR Code vs Backup Token & Live Stats -->
  <main class="proj-main">
    
    <!-- KOLOM KIRI: METODE 1 - QR CODE DINAMIS 30 DETIK -->
    <div class="proj-card">
      <div class="proj-card-header">
        <h2>
          <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg></span>
          <span>METODE 1: PINDAI QR CODE DENGAN KAMERA</span>
        </h2>
        <span style="font-size:0.75rem; color:#38BDF8; font-weight:700;">TOTP v2.4</span>
      </div>

      <div class="proj-card-body">
        <!-- Big QR Container -->
        <div class="qr-stage-wrapper">
          <div class="qr-scan-corner corner-tl"></div>
          <div class="qr-scan-corner corner-tr"></div>
          <div class="qr-scan-corner corner-bl"></div>
          <div class="qr-scan-corner corner-br"></div>
          
          <div id="qrcodeWrapper" class="qr-code-wrapper-real" style="width:260px; height:260px; display:flex; justify-content:center; align-items:center; background:#ffffff; border-radius:8px; padding:10px;"></div>
        </div>

        <!-- Countdown Bar -->
        <div class="countdown-container">
          <div class="countdown-bar-bg">
            <div id="qrTimerFill" class="countdown-bar-fill"></div>
          </div>
          <div class="countdown-text">
            <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> QR otomatis berganti dalam:</span>
            <span id="qrTimerText">30 Detik</span>
          </div>
        </div>

        <div class="qr-hash-display" id="qrHashDisplay">
           Hash Dinamis: #QR-UMR-994182
        </div>

        <p class="anti-cheat-note">
          * QR code dirotasi secara otomatis tiap 30 detik untuk mencegah kecurangan titip absen atau foto barcode dari luar gedung auditorium.
        </p>
      </div>
    </div>

    <!-- KOLOM KANAN: METODE 2 - TOKEN CADANGAN & LIVE STATISTIK REAL-TIME ( & ) -->
    <div class="proj-card">
      <div class="proj-card-header">
        <h2>
          <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg></span>
          <span>METODE 2: TOKEN CADANGAN &amp; LIVE LOG KEHADIRAN</span>
        </h2>
        <button class="btn-regen-token" onclick="regenerateToken()" title="Acak Token Cadangan Baru">
          <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg> Ganti Token
        </button>
      </div>

      <div class="proj-card-body">
        <p style="font-size:0.85rem; color:#94A3B8; margin-top:0; margin-bottom:0.75rem;">
          Gunakan kode token 6 digit jika kamera ponsel mahasiswa mengalami kendala:
        </p>

        <!-- Big Token Display -->
        <div class="token-boxes-row" id="tokenBoxesContainer">
          <!-- Rendered via JS -->
          <div class="token-box-char">A</div>
          <div class="token-box-char">I</div>
          <div class="token-box-char">2</div>
          <div class="token-box-char">0</div>
          <div class="token-box-char">2</div>
          <div class="token-box-char">6</div>
        </div>

        <!-- Statistik Realtime -->
        <div class="stats-card-mini">
          <div class="stats-count-header">
            <div>
              <div style="font-size:0.75rem; color:#94A3B8; text-transform:uppercase; font-weight:700;">Peserta Telah Presensi</div>
           <div class="stats-count-num" id="statHadirCount">{{ $sessionStats['present'] }} / {{ $sessionStats['registered'] }}</div>
             </div>
             <div class="stats-count-pct" id="statHadirPct">{{ $sessionStats['registered'] ? number_format(($sessionStats['present'] / $sessionStats['registered']) * 100, 1) : '0.0' }}% Terisi</div>
          </div>

          <div class="stats-prog-bg">
             <div id="statProgFill" class="stats-prog-fill" style="width: {{ $sessionStats['registered'] ? min(100, ($sessionStats['present'] / $sessionStats['registered']) * 100) : 0 }}%;"></div>
          </div>
        </div>

        <!-- Live Feed Mahasiswa Masuk -->
        <div class="live-feed-card">
          <div class="feed-header-mini">
            <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.93 4.93a10 10 0 0 1 14.14 0"></path><path d="M7.76 7.76a6 6 0 0 1 8.48 0"></path><circle cx="12" cy="12" r="2"></circle><path d="M12 14v8"></path></svg> Stream Presensi Terkini</span>
            <span style="color:#34D399;">● Real-time</span>
          </div>

          <div class="feed-items-stream" id="liveFeedStream">
            @forelse($recentPresensi as $item)
              <div class="feed-item-row">
                <div>
                   <span class="feed-item-name">{{ $item['nama'] }}</span>
                   <span class="feed-item-nim">({{ $item['nim'] }} - {{ $item['prodi'] }})</span>
                </div>
                <span class="feed-item-time"><svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ $item['metode'] }} ({{ $item['time'] }})</span>
              </div>
            @empty
              <div class="feed-item-row" id="feedEmptyNotice">
                <span style="color:#94a3b8; font-size:0.85rem;">Belum ada peserta yang melakukan presensi.</span>
              </div>
            @endforelse
          </div>
        </div>

      </div>
    </div>

  </main>

  <!-- Bottom Projection Control Deck -->
  <footer class="proj-deck-footer">
    <div style="display:flex; align-items:center; gap:0.75rem;">
      <button class="btn-manual-input" onclick="manualCheckinPrompt()">
        <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg></span> Presensi Manual (Input NIM)
      </button>
      <a href="{{ url('/panitia/event/' . $event->id . '/peserta') }}" class="btn-manual-input" style="text-decoration:none;">
        <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg></span> Buka Tabel Peserta
      </a>
    </div>

    <div>
      <button class="btn-close-session" onclick="closeSessionAndProceed()">
        <span><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
        <span>Tutup Sesi Presensi &amp; Terbitkan Sertifikat &rarr;</span>
      </button>
    </div>
  </footer>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
<script src="{{ asset('assets/js/sesi_absensi.js') }}"></script>
@endpush
