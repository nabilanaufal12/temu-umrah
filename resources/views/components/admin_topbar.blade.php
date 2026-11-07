<!-- resources/views/components/admin_topbar.blade.php -->
<header class="sipa-topbar admin-topbar">
  <div class="sipa-topbar-left">
    <!-- Brand Admin BAAK -->
    <a href="{{ url('/admin/panel') }}" class="sipa-brand-link" title="Super Admin Panel BAAK - TEMU UMRAH">
      <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" class="sipa-brand-logo" alt="Logo UMRAH">
      <div class="sipa-brand-text">
        <span class="sipa-brand-title">Panel Admin BAAK</span>
        <span class="sipa-brand-sub">Moderasi & Kendali Platform UMRAH</span>
      </div>
    </a>

    <!-- Hamburger Toggle Sidebar -->
    <button type="button" class="sipa-hamburger-btn" id="sipaHamburgerBtn" onclick="toggleSipaSidebar()" title="Buka / Ciutkan Menu Samping">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
  </div>

  <div class="sipa-topbar-right">
    <!-- Pending Alert Pill -->
    @php
      $totalPendingAdmin = (isset($stats) ? ($stats['pendingOrmawa'] ?? 0) + ($stats['pendingEvents'] ?? 0) : 0);
    @endphp
    @if($totalPendingAdmin > 0)
      <div style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(239,68,68,0.22); color:#fca5a5; font-size:0.74rem; font-weight:800; padding:0.3rem 0.75rem; border-radius:999px; border:1px solid rgba(239,68,68,0.3); margin-right:0.5rem;">
        <span class="pulse-dot" style="background:#f87171; width:7px; height:7px;"></span>
        <span>{{ $totalPendingAdmin }} Antrean</span>
      </div>
    @endif

    <!-- User Dropdown Admin -->
    <div class="sipa-user-dropdown-wrap" id="sipaUserDropdownWrap">
      <button type="button" class="sipa-user-chip" id="sipaUserChipBtn" onclick="toggleSipaUserDropdown(event)" aria-expanded="false" aria-haspopup="true" title="Menu Akun Admin ({{ auth()->user()?->nama ?? 'Admin' }})">
        <span class="sipa-user-name" style="font-size:0.8rem;">
          {{ strtoupper(auth()->user()?->nama ?? 'ADMIN BAAK') }}, Admin
        </span>
        <div class="sipa-user-avatar" title="{{ auth()->user()?->nama ?? 'Admin' }}">
          @if(auth()->user()?->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
          @else
            <span>{{ strtoupper(substr(auth()->user()?->nama ?? 'A', 0, 1)) }}</span>
          @endif
        </div>
        <svg class="sipa-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </button>

      <div class="sipa-user-dropdown-menu" id="sipaUserDropdownMenu" role="menu" aria-label="Menu Admin BAAK">
        <div class="sipa-dropdown-header">
          <div class="sipa-dropdown-user-title">{{ auth()->user()?->nama ?? 'Super Admin' }}</div>
          <div class="sipa-dropdown-user-role">
            <span class="sipa-role-badge" style="background: linear-gradient(135deg, #F1A80A, #d97706); color:#fff;">Super Admin BAAK</span>
            <span class="sipa-user-email">{{ auth()->user()?->email ?? '-' }}</span>
          </div>
        </div>

        <div class="sipa-dropdown-divider"></div>

        <a href="{{ url('/admin/panel#profil') }}" onclick="switchAdminTab('profil'); document.getElementById('sipaUserDropdownWrap')?.classList.remove('open'); return false;" class="sipa-dropdown-item" role="menuitem">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span>Pengaturan Akun</span>
        </a>

        <a href="{{ url('/admin/panel#profil') }}" onclick="switchAdminTab('profil'); switchAdminProfilSubTab('sandi'); document.getElementById('sipaUserDropdownWrap')?.classList.remove('open'); return false;" class="sipa-dropdown-item" role="menuitem">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>Ubah Kata Sandi</span>
        </a>

        <div class="sipa-dropdown-divider"></div>

        <a href="{{ url('/') }}" class="sipa-dropdown-item" role="menuitem">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <span>Buka Beranda Utama</span>
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
