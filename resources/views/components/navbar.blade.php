<!-- resources/views/components/navbar.blade.php -->
@if(request()->is('admin*'))
  <!-- Varian 3: Navbar Super Admin -->
  <header class="navbar navbar-admin">
    <div class="container nav-container">
      <a href="{{ url('/admin/panel') }}" class="brand" title="Portal Super Admin TEMU UMRAH">
        <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" width="40" height="40" class="brand-logo-svg" alt="Logo TEMU UMRAH">
        <div class="brand-text">
          <span class="brand-title">TEMU UMRAH</span>
          <span class="brand-sub">Super Admin Gateway</span>
        </div>
      </a>

      <div class="nav-actions" style="display:flex; align-items:center; gap:0.85rem;">
        <div class="admin-role-badge" style="display:flex; align-items:center; gap:0.45rem; padding:0.4rem 0.85rem; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:var(--radius-full); font-size:0.8rem; color:#ffffff;">
          <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <span style="font-weight:700;">BAAK UMRAH</span>
          <span style="color:rgba(255,255,255,0.7); font-size:0.75rem;">(Super Admin)</span>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline btn-sm btn-nav-switch" title="Beralih ke Portal Publik Mahasiswa">
          Portal Mahasiswa &rarr;
        </a>
        <form action="{{ route('logout') }}" method="POST" style="margin:0; display:inline;">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger" style="display:flex; align-items:center; gap:0.4rem; font-size:0.8rem; padding:0.45rem 0.85rem; border:1px solid rgba(239, 68, 68, 0.4); color:#fca5a5; background:rgba(239, 68, 68, 0.12); border-radius:6px; cursor:pointer;" title="Keluar dari Akun Super Admin">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span style="font-weight:600;">Keluar</span>
          </button>
        </form>
      </div>
    </div>
  </header>

@elseif(request()->is('panitia*'))
  @php($navEventId = auth()->user()?->ormawa?->events()->value('id'))
  <!-- Varian 2: Navbar Panitia Ormawa -->
  <header class="navbar navbar-panitia">
    <div class="container nav-container">
      <a href="{{ url('/panitia/dashboard') }}" class="brand" title="Portal Panitia TEMU UMRAH">
        <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" width="40" height="40" class="brand-logo-svg" alt="Logo TEMU UMRAH">
        <div class="brand-text">
          <span class="brand-title">TEMU UMRAH</span>
          <span class="brand-sub">Portal Panitia Ormawa</span>
        </div>
      </a>

      <nav>
        <ul class="nav-menu">
          <li>
            <a href="{{ url('/panitia/dashboard') }}" class="nav-link {{ request()->is('panitia/dashboard*') ? 'active' : '' }}">
              Kelola Event
            </a>
          </li>
          <li>
            <a href="{{ $navEventId ? url('/panitia/event/' . $navEventId . '/peserta') : url('/panitia/dashboard') }}" class="nav-link {{ request()->is('panitia/*peserta*') ? 'active' : '' }}">
              Kelola Peserta
            </a>
          </li>
          <li>
            <a href="{{ $navEventId ? url('/panitia/event/' . $navEventId . '/sesi-absensi') : url('/panitia/dashboard') }}" class="nav-link {{ request()->is('panitia/*sesi-absensi*') ? 'active' : '' }}">
              Sesi Absensi
            </a>
          </li>
          <li>
            <a href="{{ $navEventId ? url('/panitia/event/' . $navEventId . '/terbitkan-sertifikat') : url('/panitia/dashboard') }}" class="nav-link {{ request()->is('panitia/*terbitkan-sertifikat*') ? 'active' : '' }}">
              Penerbitan Sertifikat
            </a>
          </li>
        </ul>
      </nav>

      <div class="nav-actions">
        <div class="panitia-role-badge" style="display:flex; align-items:center; gap:0.4rem; padding:0.35rem 0.75rem; background:rgba(255,255,255,0.08); border-radius:var(--radius-full); font-size:0.8rem;">
          <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
           <span>{{ auth()->user()?->ormawa?->nama_ormawa ?? 'Panitia Ormawa' }} (Panitia)</span>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline btn-sm btn-nav-switch" title="Kembali ke Portal Mahasiswa">
          Portal Mahasiswa &rarr;
        </a>
      </div>
    </div>
  </header>

@else
  <!-- Varian 1: Navbar Mahasiswa / Peserta (Floating Pill Sync dengan Beranda) -->
  <header class="navbar navbar-mahasiswa navbar-home">
    <div class="container nav-container">
      <a href="{{ url('/') }}" class="brand" title="Beranda TEMU UMRAH">
        <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" width="36" height="36" class="brand-logo-svg" alt="Logo TEMU UMRAH">
        <div class="brand-text">
          <span class="brand-title">TEMU UMRAH</span>
        </div>
      </a>

      <nav>
        <ul class="nav-menu">
          @if(auth()->check())
            <li>
              <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                Beranda
              </a>
            </li>
            <li>
              <a href="{{ route('katalog.jelajah') }}" class="nav-link {{ (request()->is('jelajah-event*') || request()->is('event/*')) ? 'active' : '' }}">
                Jelajah Event
              </a>
            </li>
            @if(auth()->user()->role === 'mahasiswa')
              <li>
                <a href="{{ url('/event-saya') }}" class="nav-link {{ (request()->is('event-saya*') || request()->is('pembayaran*')) ? 'active' : '' }}">
                  Event Saya
                </a>
              </li>
              <li>
                <a href="{{ url('/presensi') }}" class="nav-link {{ request()->is('presensi*') ? 'active' : '' }}">
                  Presensi
                </a>
              </li>
              <li>
                <a href="{{ url('/sertifikat') }}" class="nav-link {{ request()->is('sertifikat*') ? 'active' : '' }}">
                  E-Sertifikat
                </a>
              </li>
            @elseif(auth()->user()->role === 'panitia')
              <li>
                <a href="{{ url('/panitia/dashboard') }}" class="nav-link" style="color: #0284c7; font-weight: 800;">
                  Panel Panitia
                </a>
              </li>
            @elseif(auth()->user()->role === 'admin')
              <li>
                <a href="{{ url('/admin/panel') }}" class="nav-link" style="color: #0284c7; font-weight: 800;">
                  Panel Admin
                </a>
              </li>
            @endif
          @else
            <li><a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
            <li><a href="{{ url('/#cara-kerja') }}" class="nav-link">Cara Kerja</a></li>
            <li><a href="{{ route('katalog.jelajah') }}" class="nav-link {{ (request()->is('jelajah-event*') || request()->is('event/*')) ? 'active' : '' }}">Jelajah Event</a></li>
            <li><a href="{{ url('/#tentang-temu') }}" class="nav-link">Tentang TEMU</a></li>
          @endif
        </ul>
      </nav>

      <div class="nav-actions">
        <!-- Sesi Pengguna Terautentikasi / Tamu -->
        <div id="userNavArea">
          @if(auth()->check())
            <div class="nav-user-dropdown-wrap" id="navUserDropdownWrap">
              <button type="button" class="user-profile-pill {{ request()->is('profil*') ? 'active' : '' }}" id="navProfilePillBtn" aria-expanded="false" aria-haspopup="true" onclick="toggleNavUserDropdown(event)" title="Menu Akun ({{ auth()->user()->nama ?? 'Mahasiswa' }} - NIM: {{ auth()->user()->nim ?? '-' }})">
                <img src="{{ auth()->user()->avatar_url ?? '' }}" alt="Avatar" class="user-avatar-img {{ auth()->user()->avatar_url ? '' : 'user-avatar-hidden' }}" id="navbarAvatarImg">
                <div class="user-avatar {{ auth()->user()->avatar_url ? 'user-avatar-hidden' : '' }}" id="navbarAvatarInitial">{{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}</div>
                <div class="user-meta">
                  <span class="user-name">{{ auth()->user()->nama ?? 'Mahasiswa' }}</span>
                  <span class="user-role">{{ ucfirst(auth()->user()->role ?? 'Mahasiswa') }}</span>
                </div>
                <svg class="nav-pill-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
              </button>

              <!-- Modern Clean Dropdown Menu -->
              <div class="nav-user-dropdown-menu" id="navUserDropdownMenu" role="menu" aria-label="Menu Pengguna">
                <!-- User Identity Card in Dropdown -->
                <div class="dropdown-user-header">
                  <div class="dropdown-user-title">{{ auth()->user()->nama }}</div>
                  <div class="dropdown-user-identity">
                    @if(auth()->user()->role === 'admin')
                      <span class="dropdown-user-nim" style="color:#d97706; font-weight:700;">Super Admin BAAK</span>
                    @elseif(auth()->user()->role === 'panitia')
                      <span class="dropdown-user-nim" style="color:#0284c7; font-weight:700;">Panitia Ormawa</span>
                    @else
                      <span class="dropdown-user-nim">NIM: {{ auth()->user()->nim ?? '-' }}</span>
                      <span class="dropdown-status-dot"></span>
                      <span class="dropdown-status-text">Mahasiswa Aktif</span>
                    @endif
                  </div>
                  @if(auth()->user()->email)
                    <div class="dropdown-user-email">{{ auth()->user()->email }}</div>
                  @endif
                </div>

                <div class="dropdown-divider"></div>

                <!-- Account Management Links -->
                <a href="{{ url('/profil') }}" class="dropdown-menu-item {{ (request()->is('profil*') && request()->query('tab') !== 'sandi') ? 'active' : '' }}" role="menuitem">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <span>Pengaturan Akun</span>
                </a>

                @if(auth()->user()->role === 'mahasiswa')
                <a href="{{ url('/notifikasi') }}" class="dropdown-menu-item {{ request()->is('notifikasi*') ? 'active' : '' }}" role="menuitem">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                  <span>Pusat Notifikasi</span>
                </a>
                @endif

                <a href="{{ url('/profil?tab=sandi') }}" class="dropdown-menu-item {{ (request()->is('profil*') && request()->query('tab') === 'sandi') ? 'active' : '' }}" role="menuitem">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  <span>Ubah Kata Sandi</span>
                </a>

                @if(in_array(auth()->user()->role, ['panitia', 'admin']))
                  <div class="dropdown-divider"></div>
                  <a href="{{ auth()->user()->role === 'admin' ? url('/admin/panel') : url('/panitia/dashboard') }}" class="dropdown-menu-item dropdown-item-portal" role="menuitem">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Portal {{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Panitia Ormawa' }}</span>
                  </a>
                @endif

                <!-- Mobile Navigation (Hanya Tampil di Layar HP/Tablet <= 900px saat navbar disembunyikan) -->
                <div class="dropdown-mobile-nav">
                  <div class="dropdown-divider"></div>
                  <div class="dropdown-section-label">Aktivitas Kampus</div>

                  <a href="{{ route('katalog.jelajah') }}" class="dropdown-menu-item {{ request()->is('jelajah-event*') ? 'active' : '' }}" role="menuitem">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span>Jelajah Event</span>
                  </a>

                  @if(auth()->user()->role === 'mahasiswa')
                    <a href="{{ url('/event-saya') }}" class="dropdown-menu-item {{ request()->is('event-saya*') ? 'active' : '' }}" role="menuitem">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>
                      <span>Event Saya</span>
                    </a>

                    <a href="{{ url('/presensi') }}" class="dropdown-menu-item {{ request()->is('presensi*') ? 'active' : '' }}" role="menuitem">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                      <span>Presensi Event</span>
                    </a>

                    <a href="{{ url('/sertifikat') }}" class="dropdown-menu-item {{ request()->is('sertifikat*') ? 'active' : '' }}" role="menuitem">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                      <span>E-Sertifikat Digital</span>
                    </a>
                  @elseif(auth()->user()->role === 'panitia')
                    <a href="{{ url('/panitia/dashboard') }}" class="dropdown-menu-item" role="menuitem">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                      <span>Dashboard Panitia Ormawa</span>
                    </a>
                  @elseif(auth()->user()->role === 'admin')
                    <a href="{{ url('/admin/panel') }}" class="dropdown-menu-item" role="menuitem">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                      <span>Panel Super Admin</span>
                    </a>
                  @endif
                </div>

                <div class="dropdown-divider"></div>

                <!-- Form Logout -->
                <form method="POST" action="{{ route('logout') }}" class="dropdown-logout-form">
                  @csrf
                  <button type="submit" class="dropdown-menu-item dropdown-item-danger" role="menuitem" title="Keluar dari sesi akun TEMU UMRAH">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Keluar dari Akun (Logout)</span>
                  </button>
                </form>
              </div>
            </div>
          @else
            <a href="{{ route('register') }}" class="home-nav-signup {{ request()->is('register*') ? 'active' : '' }}">Daftar</a>
            <a href="{{ route('login') }}" class="btn-nav-login {{ request()->is('login*') ? 'active' : '' }}" id="navLoginBtn" title="Masuk ke Portal TEMU UMRAH">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
              <span>Masuk</span>
            </a>
          @endif
        </div>
      </div>
    </div>
  </header>
@endif
