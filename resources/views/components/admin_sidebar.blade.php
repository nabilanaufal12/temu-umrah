<!-- resources/views/components/admin_sidebar.blade.php -->
@php
  $totalPendingAdmin = (isset($stats) ? ($stats['pendingOrmawa'] ?? 0) + ($stats['pendingEvents'] ?? 0) : 0);
@endphp

<!-- Mobile Backdrop -->
<div class="sipa-backdrop" onclick="toggleSipaSidebar()"></div>

<!-- Left Sidebar Admin Navigation (SIPA Style Clean White) -->
<aside class="sipa-sidebar admin-sidebar" id="sipaSidebar">

  <!-- Header Sidebar: Label Ruang Kendali -->
  <div class="sipa-sidebar-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.85rem 1.25rem;">
    <div class="sipa-sidebar-subtext" style="color: #475569; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase;">
      Super Admin &bull; BAAK
    </div>
  </div>

  <!-- Menu Navigasi Admin -->
  <ul class="sipa-menu-list" style="padding-top: 0.5rem;">

    <!-- 1. Ruang Kendali / Dashboard Utama -->
    <li class="sipa-menu-item" id="adminSideItem_dashboard">
      <a href="{{ url('/admin/panel') }}" onclick="switchAdminTab('dashboard'); return false;" class="sipa-menu-link" title="Ruang Kendali & Ringkasan Platform">
        <span class="sipa-menu-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </span>
        <span class="sipa-menu-text">Ruang Kendali</span>
      </a>
    </li>

    <!-- Section Divider -->
    <div class="sipa-sidebar-section-title" style="padding-top: 0.65rem;">Moderasi & Verifikasi</div>

    <!-- 2. Verifikasi Ormawa -->
    <li class="sipa-menu-item" id="adminSideItem_ormawa">
      <a href="{{ url('/admin/panel') }}#ormawa" onclick="switchAdminTab('ormawa'); return false;" class="sipa-menu-link" title="Verifikasi SK & Legalitas Ormawa">
        <span class="sipa-menu-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        </span>
        <span class="sipa-menu-text">Verifikasi Ormawa</span>
        @if(isset($stats) && ($stats['pendingOrmawa'] ?? 0) > 0)
          <span class="sipa-menu-badge sipa-badge-danger">{{ $stats['pendingOrmawa'] }}</span>
        @endif
      </a>
    </li>

    <!-- 3. Moderasi Event Kampus -->
    <li class="sipa-menu-item" id="adminSideItem_events">
      <a href="{{ url('/admin/panel') }}#events" onclick="switchAdminTab('events'); return false;" class="sipa-menu-link" title="Kurasi & Moderasi Kegiatan Kampus">
        <span class="sipa-menu-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        <span class="sipa-menu-text">Moderasi Event</span>
        @if(isset($stats) && ($stats['pendingEvents'] ?? 0) > 0)
          <span class="sipa-menu-badge sipa-badge-warning">{{ $stats['pendingEvents'] }}</span>
        @endif
      </a>
    </li>

    <!-- Section Divider -->
    <div class="sipa-sidebar-section-title">Data & Master</div>

    <!-- 4. Kelola Mahasiswa -->
    <li class="sipa-menu-item" id="adminSideItem_users">
      <a href="{{ url('/admin/panel') }}#users" onclick="switchAdminTab('users'); return false;" class="sipa-menu-link" title="Kontrol Hak Akses Akun Mahasiswa">
        <span class="sipa-menu-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
        <span class="sipa-menu-text">Kelola Mahasiswa</span>
      </a>
    </li>

    <!-- 5. Audit Log Sistem -->
    <li class="sipa-menu-item" id="adminSideItem_logs">
      <a href="{{ url('/admin/panel') }}#logs" onclick="switchAdminTab('logs'); return false;" class="sipa-menu-link" title="Audit Trail & Webhook Platform">
        <span class="sipa-menu-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </span>
        <span class="sipa-menu-text">Audit Log Sistem</span>
      </a>
    </li>

    <!-- Section Divider -->
    <div class="sipa-sidebar-section-title">Akun & Otoritas</div>

    <!-- 6. Profil & Keamanan Admin -->
    <li class="sipa-menu-item" id="adminSideItem_profil">
      <a href="{{ url('/admin/panel') }}#profil" onclick="switchAdminTab('profil'); return false;" class="sipa-menu-link" title="Profil Dinas & Keamanan Super Admin BAAK">
        <span class="sipa-menu-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="sipa-menu-text">Pengaturan Akun</span>
      </a>
    </li>

    <!-- Divider -->
    <div class="sipa-menu-divider"></div>

    <!-- Section Divider -->
    <div class="sipa-sidebar-section-title">Navigasi Peran</div>

    <!-- Beranda Utama / Katalog Publik -->
    <li class="sipa-menu-item">
      <a href="{{ url('/') }}" class="sipa-menu-link" title="Buka Beranda Utama / Katalog Kegiatan">
        <span class="sipa-menu-icon" style="color: #059669;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <span class="sipa-menu-text">Beranda Utama</span>
      </a>
    </li>

    <!-- Keluar -->
    <li class="sipa-menu-item">
      <form action="{{ route('logout') }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" class="sipa-menu-link" style="width:100%; border:none; background:transparent; cursor:pointer; text-align:left; color:#dc2626;" title="Keluar dari Akun Admin">
          <span class="sipa-menu-icon" style="color:#dc2626;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </span>
          <span class="sipa-menu-text">Keluar</span>
        </button>
      </form>
    </li>

  </ul>

  <!-- Footer Sidebar Admin -->
  <div class="sipa-sidebar-footer" style="padding: 0.85rem 1.25rem; border-top: 1px solid #e2e8f0; margin-top: auto; font-size: 0.72rem; color: #64748b; line-height: 1.4; background: #f8fafc;">
    <div style="font-weight: 700; color: #334155;">TEMU UMRAH &bull; Admin</div>
    <div>Sistem aktif &bull; <span id="adminLiveClock">{{ now()->format('H:i') }}</span> WIB</div>
  </div>

</aside>

<script>
  // Live clock for admin sidebar footer
  (function() {
    function updateClock() {
      const el = document.getElementById('adminLiveClock');
      if (!el) return;
      const now = new Date();
      const h = String(now.getHours()).padStart(2,'0');
      const m = String(now.getMinutes()).padStart(2,'0');
      el.textContent = `${h}:${m}`;
    }
    setInterval(updateClock, 10000);
  })();
</script>
