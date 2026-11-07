<!-- resources/views/components/sipa_topbar.blade.php -->
<header class="sipa-topbar">
  <div class="sipa-topbar-left">
    <!-- Brand Title & Logo UMRAH -->
    <a href="{{ url('/panitia/dashboard') }}" class="sipa-brand-link" title="Sistem Informasi Event Kampus - TEMU UMRAH">
      <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" class="sipa-brand-logo" alt="Logo UMRAH">
      <div class="sipa-brand-text">
        <span class="sipa-brand-title">Sistem Informasi</span>
        <span class="sipa-brand-sub">Pengelolaan Event Mahasiswa</span>
      </div>
    </a>

    <!-- Hamburger Toggle Sidebar Button -->
    <button type="button" class="sipa-hamburger-btn" id="sipaHamburgerBtn" onclick="toggleSipaSidebar()" title="Buka / Ciutkan Menu Samping">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
  </div>

  <div class="sipa-topbar-right">
    <!-- Notification Icon -->
    <button type="button" id="sipaNotifBtn" class="sipa-notif-btn" onclick="openNotificationDrawer()" title="Pemberitahuan &amp; Pengingat">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      <span class="sipa-notif-badge" id="sipaNotifBadge" style="display:none;"></span>
    </button>

    <!-- User Profile Dropdown (SIPA UMRAH Style) -->
    <div class="sipa-user-dropdown-wrap" id="sipaUserDropdownWrap">
      <button type="button" class="sipa-user-chip" id="sipaUserChipBtn" onclick="toggleSipaUserDropdown(event)" aria-expanded="false" aria-haspopup="true" title="Menu Akun Panitia ({{ auth()->user()?->nama ?? 'Panitia' }})">
        <span class="sipa-user-name">
          {{ strtoupper(auth()->user()?->nama ?? 'PANITIA HMTI') }}, Panitia
        </span>
        <div class="sipa-user-avatar" title="{{ auth()->user()?->nama ?? 'Panitia' }}">
          @if(auth()->user()?->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
          @else
            <span>{{ strtoupper(substr(auth()->user()?->nama ?? 'P', 0, 1)) }}</span>
          @endif
        </div>
        <svg class="sipa-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </button>

      <!-- Dropdown Menu -->
      <div class="sipa-user-dropdown-menu" id="sipaUserDropdownMenu" role="menu" aria-label="Menu Pengguna Panitia">
        <div class="sipa-dropdown-header">
          <div class="sipa-dropdown-user-title">{{ auth()->user()?->nama ?? 'Panitia Ormawa' }}</div>
          <div class="sipa-dropdown-user-role">
            <span class="sipa-role-badge">Panitia Ormawa</span>
            <span class="sipa-user-email">{{ auth()->user()?->email ?? '-' }}</span>
          </div>
        </div>

        <div class="sipa-dropdown-divider"></div>

        <a href="{{ url('/panitia/profil') }}" class="sipa-dropdown-item" role="menuitem">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span>Pengaturan Akun</span>
        </a>

        <a href="{{ url('/panitia/profil?tab=sandi') }}" class="sipa-dropdown-item" role="menuitem">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>Ubah Kata Sandi</span>
        </a>

        <a href="javascript:void(0)" onclick="openNotificationDrawer()" class="sipa-dropdown-item" role="menuitem">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <span>Pusat Notifikasi</span>
        </a>

        <div class="sipa-dropdown-divider"></div>

        <a href="{{ url('/') }}" class="sipa-dropdown-item" role="menuitem">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          <span>Beralih ke Portal Mahasiswa</span>
        </a>

        <div class="sipa-dropdown-divider"></div>

        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="sipa-dropdown-item sipa-dropdown-danger" role="menuitem">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Keluar dari Akun (Logout)</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</header>
