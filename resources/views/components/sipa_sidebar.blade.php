<!-- resources/views/components/sipa_sidebar.blade.php -->
@php
  $routeEventId = request()->route('id') ?? request()->route('event');
  $isEventContext = request()->is('panitia/event/*');
  
  $activeEvent = null;
  if ($routeEventId) {
      $activeEvent = \App\Models\Event::find($routeEventId);
  }
  
  $userOrmawa = auth()->user()?->ormawa;
  $userEvents = $userOrmawa ? $userOrmawa->events()->orderByDesc('id')->get() : collect();
@endphp

<!-- Mobile Backdrop -->
<div class="sipa-backdrop" onclick="toggleSipaSidebar()"></div>

<!-- Left Sidebar Navigation ("Menyamping") -->
<aside class="sipa-sidebar" id="sipaSidebar">
  <!-- Subtitle Header: Tahun Ajaran (Exact Match SIPA Screenshot) -->
  <div class="sipa-sidebar-header">
    <div class="sipa-sidebar-subtext">Tahun Ajaran : 2026/2027 Ganjil</div>
  </div>

  @if($isEventContext && $activeEvent)
    <!-- ==================== MODE PENGELOLAAN KEGIATAN SPESIFIK ==================== -->
    <a href="{{ url('/panitia/dashboard') }}" class="sipa-sidebar-btn-back" title="Kembali ke Dashboard Utama">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Kembali ke Dashboard</span>
    </a>

    <div class="sipa-sidebar-section-title">Kegiatan Dikelola</div>
    <div class="sipa-sidebar-active-event-box">
      <div style="font-size:0.65rem; color:#15803d; font-weight:800; margin-bottom:0.2rem; text-transform:uppercase;">Kegiatan Aktif:</div>
      <div class="sipa-sidebar-active-event-name" title="{{ $activeEvent->judul }}">{{ $activeEvent->judul }}</div>
    </div>

    <!-- Menu Khusus Kegiatan Aktif -->
    <ul class="sipa-menu-list">
      <!-- 1. Kelola Peserta & Presensi -->
      <li class="sipa-menu-item {{ request()->is('panitia/*/peserta*') ? 'active' : '' }}">
        <a href="{{ url('/panitia/event/' . $activeEvent->id . '/peserta') }}" class="sipa-menu-link {{ request()->is('panitia/*/peserta*') ? 'active' : '' }}" title="Kelola Peserta & Verifikasi Pembayaran">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </span>
          <span class="sipa-menu-text">Kelola Peserta</span>
        </a>
      </li>

      <!-- 2. Sesi Absensi Proyektor -->
      <li class="sipa-menu-item {{ request()->is('panitia/*/sesi-absensi*') ? 'active' : '' }}">
        <a href="{{ url('/panitia/event/' . $activeEvent->id . '/sesi-absensi') }}" class="sipa-menu-link {{ request()->is('panitia/*/sesi-absensi*') ? 'active' : '' }}" title="Layar Sesi Absensi Proyektor">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          </span>
          <span class="sipa-menu-text">Sesi Absensi (QR)</span>
        </a>
      </li>

      <!-- 3. Penerbitan Sertifikat Canva -->
      <li class="sipa-menu-item {{ request()->is('panitia/*/terbitkan-sertifikat*') ? 'active' : '' }}">
        <a href="{{ url('/panitia/event/' . $activeEvent->id . '/terbitkan-sertifikat') }}" class="sipa-menu-link {{ request()->is('panitia/*/terbitkan-sertifikat*') ? 'active' : '' }}" title="Penerbitan E-Sertifikat Canva">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
          </span>
          <span class="sipa-menu-text">Penerbitan Sertifikat</span>
        </a>
      </li>

      <div class="sipa-menu-divider"></div>

      <!-- Profil & SK Ormawa -->
      <li class="sipa-menu-item {{ request()->is('panitia/profil*') ? 'active' : '' }}">
        <a href="{{ url('/panitia/profil') }}" class="sipa-menu-link {{ request()->is('panitia/profil*') ? 'active' : '' }}" title="Pengaturan Akun &amp; Legalitas Ormawa">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <span class="sipa-menu-text">Pengaturan Akun</span>
        </a>
      </li>

      <!-- Portal Mahasiswa -->
      <li class="sipa-menu-item">
        <a href="{{ url('/') }}" class="sipa-menu-link" title="Beralih ke Portal Mahasiswa">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          </span>
          <span class="sipa-menu-text">Portal Mahasiswa</span>
        </a>
      </li>

      <!-- Keluar (Logout) -->
      <li class="sipa-menu-item">
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="sipa-menu-link" style="width:100%; border:none; background:transparent; cursor:pointer; text-align:left;" title="Keluar dari Akun">
            <span class="sipa-menu-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </span>
            <span class="sipa-menu-text">Keluar</span>
          </button>
        </form>
      </li>
    </ul>

  @else
    <!-- ==================== MODE DASHBOARD UTAMA ==================== -->
    <ul class="sipa-menu-list">
      <!-- 1. Halaman Utama / Dashboard -->
      <li class="sipa-menu-item {{ request()->is('panitia/dashboard') ? 'active' : '' }}">
        <a href="{{ url('/panitia/dashboard') }}" class="sipa-menu-link {{ request()->is('panitia/dashboard') ? 'active' : '' }}" title="Halaman Utama Panitia">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span>
          <span class="sipa-menu-text">Halaman Utama</span>
        </a>
      </li>

      <!-- Subheader: Daftar Kegiatan Anda -->
      <div class="sipa-sidebar-section-title">Pilih Kegiatan Ormawa</div>

      @forelse($userEvents as $ev)
        <li class="sipa-menu-item">
          <a href="{{ url('/panitia/event/' . $ev->id . '/peserta') }}" class="sipa-menu-link" title="Kelola {{ $ev->judul }}">
            <span class="sipa-menu-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span class="sipa-menu-text" title="{{ $ev->judul }}">{{ $ev->judul }}</span>
          </a>
        </li>
      @empty
        <li style="padding:0.5rem 1.25rem; font-size:0.75rem; color:#94a3b8;">Belum ada kegiatan.</li>
      @endforelse

      <div class="sipa-menu-divider"></div>

      <!-- Section: Akun & Legalitas Ormawa -->
      <div class="sipa-sidebar-section-title">Akun & Legalitas</div>
      <li class="sipa-menu-item {{ request()->is('panitia/profil*') ? 'active' : '' }}">
        <a href="{{ url('/panitia/profil') }}" class="sipa-menu-link {{ request()->is('panitia/profil*') ? 'active' : '' }}" title="Pengaturan Akun &amp; Legalitas Ormawa">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <span class="sipa-menu-text">Pengaturan Akun</span>
        </a>
      </li>

      <div class="sipa-menu-divider"></div>

      <!-- Portal Mahasiswa -->
      <li class="sipa-menu-item">
        <a href="{{ url('/') }}" class="sipa-menu-link" title="Beralih ke Portal Mahasiswa">
          <span class="sipa-menu-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          </span>
          <span class="sipa-menu-text">Portal Mahasiswa</span>
        </a>
      </li>

      <!-- Keluar (Logout) -->
      <li class="sipa-menu-item">
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="sipa-menu-link" style="width:100%; border:none; background:transparent; cursor:pointer; text-align:left;" title="Keluar dari Akun">
            <span class="sipa-menu-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </span>
            <span class="sipa-menu-text">Keluar</span>
          </button>
        </form>
      </li>
    </ul>
  @endif
</aside>
