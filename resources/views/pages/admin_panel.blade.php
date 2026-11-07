@extends('layouts.app')

@section('title', 'Super Admin Panel & Moderasi BAAK - TEMU UMRAH')
@section('meta_desc', 'Tata kelola verifikasi legalitas SK Ormawa, moderasi kegiatan kampus, manajemen akun mahasiswa SIPA, dan audit trail webhook platform.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/profil.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_PROFILE_USER = @json(auth()->user());
  window.TEMU_ADMIN_BACKEND = true;
  window.TEMU_ADMIN_ALL_ORMAWA = @json($allOrmawa);
  window.TEMU_ADMIN_ORMAWA = @json($pendingOrmawa);
  window.TEMU_ADMIN_ALL_EVENTS = @json($allEvents);
  window.TEMU_ADMIN_EVENTS = @json($pendingEvents);
  window.TEMU_ADMIN_STUDENTS = @json($students);
  window.TEMU_ADMIN_ROUTES = {
    approveOrmawa: @json(route('admin.ormawa.approve', ['id' => '__ID__'])),
    rejectOrmawa: @json(route('admin.ormawa.reject', ['id' => '__ID__'])),
    approveEvent: @json(route('admin.event.approve', ['id' => '__ID__'])),
    rejectEvent: @json(route('admin.event.reject', ['id' => '__ID__'])),
    toggleStudent: @json(route('admin.student.toggle_status', ['id' => '__ID__']))
  };
</script>

<div class="container panitia-main-container">

  <!-- Breadcrumb -->
  <nav class="breadcrumb-wrapper">
    <a href="{{ url('/admin/panel') }}" onclick="switchAdminTab('dashboard'); return false;">Admin BAAK</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current" id="adminBreadcrumbCurrent">Ruang Kendali</span>
  </nav>

  <!-- Alert Flash -->
  @if(session('success'))
    <div style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.25rem; border-radius: 0.75rem; background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #065f46;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <span style="font-weight: 600; font-size: 0.88rem;">{{ session('success') }}</span>
    </div>
  @endif
  @if(session('error'))
    <div style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.25rem; border-radius: 0.75rem; background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span style="font-weight: 600; font-size: 0.88rem;">{{ session('error') }}</span>
    </div>
  @endif

  <!-- TAB PANE 0: RUANG KENDALI / DASHBOARD OVERVIEW -->
  <div class="admin-tab-pane" id="tabPane_dashboard">
    <!-- Page Header -->
    <div class="sipa-dashboard-header" style="margin-bottom: 1.5rem;">
      <div>
        <h1 class="sipa-page-title">Ruang Kendali &amp; Moderasi Platform</h1>
        <p class="sipa-page-subtitle">Pantau legalitas SK Ormawa, kurasi pengajuan kegiatan, dan kelola akun mahasiswa SIPA UMRAH secara terpusat.</p>
      </div>
      <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
        @php $totalPending = $stats['pendingOrmawa'] + $stats['pendingEvents']; @endphp
        @if($totalPending > 0)
          <div style="display:inline-flex; align-items:center; gap:0.45rem; background:#fef2f2; color:#dc2626; font-size:0.78rem; font-weight:800; padding:0.5rem 1rem; border-radius:999px; border:1.5px solid #fecaca; box-shadow: 0 2px 8px rgba(220,38,38,0.12);">
            <span class="pulse-dot" style="background:#ef4444;"></span>
            <span>{{ $totalPending }} Antrean Butuh Tindakan</span>
          </div>
        @else
          <div style="display:inline-flex; align-items:center; gap:0.45rem; background:#ecfdf5; color:#059669; font-size:0.78rem; font-weight:800; padding:0.5rem 1rem; border-radius:999px; border:1.5px solid #a7f3d0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Semua Antrean Bersih</span>
          </div>
        @endif
        <div style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:#64748b; padding:0.4rem 0.75rem; background:#f8fafc; border-radius:999px; border:1px solid #e2e8f0;">
          <span class="pulse-dot" style="width:7px; height:7px;"></span>
          <span>Sistem BAAK Online</span>
        </div>
      </div>
    </div>

    <!-- KPI Stats Grid (4 Kartu) -->
    <div class="sipa-kpi-grid" style="margin-bottom: 1.75rem;">
      <div class="sipa-kpi-card" onclick="switchAdminTab('users')" style="cursor:pointer;" title="Buka Kelola Mahasiswa">
        <div class="sipa-kpi-icon" style="background: #e0f2fe; color: #0284c7;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value">{{ number_format($stats['students']) }}</div>
          <div class="sipa-kpi-label">Total Mahasiswa SIPA</div>
        </div>
      </div>

      <div class="sipa-kpi-card" onclick="jumpToAdminFilter('ormawa', 'verified')" style="cursor:pointer;" title="Buka Ormawa Terverifikasi">
        <div class="sipa-kpi-icon" style="background: #dcfce7; color: #16a34a;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value">{{ $stats['activeOrmawa'] }}</div>
          <div class="sipa-kpi-label">Ormawa SK Terverifikasi</div>
        </div>
      </div>

      <div class="sipa-kpi-card" onclick="jumpToAdminFilter('ormawa', 'pending')" style="cursor:pointer; {{ $stats['pendingOrmawa'] > 0 ? 'border-color:#fecaca;' : '' }}" title="Verifikasi SK Ormawa">
        <div class="sipa-kpi-icon" style="background: {{ $stats['pendingOrmawa'] > 0 ? '#fee2e2; color: #dc2626' : '#e0f2fe; color: #0284c7' }};">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value" id="badgeOrmawaCount" style="{{ $stats['pendingOrmawa'] > 0 ? 'color:#dc2626;' : '' }}">{{ $stats['pendingOrmawa'] }}</div>
          <div class="sipa-kpi-label">{{ $stats['pendingOrmawa'] > 0 ? 'SK Ormawa Butuh Tindakan' : 'Verifikasi SK Ormawa' }}</div>
        </div>
      </div>

      <div class="sipa-kpi-card" onclick="jumpToAdminFilter('events', 'pending_approval')" style="cursor:pointer; {{ $stats['pendingEvents'] > 0 ? 'border-color:#fde68a;' : '' }}" title="Moderasi Event Kampus">
        <div class="sipa-kpi-icon" style="background: {{ $stats['pendingEvents'] > 0 ? '#fef3c7; color: #d97706' : '#e0f2fe; color: #0284c7' }};">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value" id="statPendingEvents" style="{{ $stats['pendingEvents'] > 0 ? 'color:#d97706;' : '' }}">{{ $stats['pendingEvents'] }}</div>
          <div class="sipa-kpi-label">{{ $stats['pendingEvents'] > 0 ? 'Event Butuh Review BAAK' : 'Moderasi Event Kampus' }}</div>
        </div>
      </div>
    </div>

    <!-- Ringkasan Modul & Tindakan Cepat BAAK -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
      <div class="admin-card-box" style="padding: 1.5rem;">
        <h4 style="margin: 0 0 0.5rem; font-size: 1rem; color: var(--umrah-navy); display:flex; align-items:center; gap:0.5rem;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Verifikasi Legalitas SK Ormawa
        </h4>
        <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem; line-height: 1.5;">
          Periksa keabsahan SK kepengurusan ormawa dari Dekanat/Rektorat serta pastikan kepatuhan batas maksimal 2 akun panitia per ormawa.
        </p>
        @if($stats['pendingOrmawa'] > 0)
          <div style="padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; font-size: 0.82rem; color: #991b1b; margin-bottom: 1rem;">
            Terdapat <strong>{{ $stats['pendingOrmawa'] }} permohonan SK</strong> menunggu verifikasi Admin.
          </div>
        @else
          <div style="padding: 0.75rem 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; font-size: 0.82rem; color: #065f46; margin-bottom: 1rem;">
            Semua dokumen SK ormawa telah terverifikasi sah.
          </div>
        @endif
        <button type="button" class="btn btn-sm btn-primary" onclick="switchAdminTab('ormawa')">
          Buka Verifikasi Ormawa &rarr;
        </button>
      </div>

      <div class="admin-card-box" style="padding: 1.5rem;">
        <h4 style="margin: 0 0 0.5rem; font-size: 1rem; color: var(--umrah-navy); display:flex; align-items:center; gap:0.5rem;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Moderasi Kegiatan Kampus
        </h4>
        <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem; line-height: 1.5;">
          Tinjau pengajuan kegiatan ormawa (jadwal, kuota, biaya, proposal) sebelum disetujui tampil di katalog publik mahasiswa.
        </p>
        @if($stats['pendingEvents'] > 0)
          <div style="padding: 0.75rem 1rem; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; font-size: 0.82rem; color: #92400e; margin-bottom: 1rem;">
            Terdapat <strong>{{ $stats['pendingEvents'] }} kegiatan</strong> menunggu persetujuan moderasi.
          </div>
        @else
          <div style="padding: 0.75rem 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; font-size: 0.82rem; color: #065f46; margin-bottom: 1rem;">
            Tidak ada kegiatan kampus yang tertahan antrean.
          </div>
        @endif
        <button type="button" class="btn btn-sm btn-primary" onclick="switchAdminTab('events')">
          Buka Moderasi Event &rarr;
        </button>
      </div>
    </div>
  </div>

  <!-- TAB PANE 1: VERIFIKASI ORMAWA -->
  <div class="admin-tab-pane" id="tabPane_ormawa" style="display:none;">
    <div class="admin-card-box">
      <div class="admin-card-header" style="flex-wrap:wrap; gap:1rem; align-items:flex-end;">
        <div>
          <h3>Daftar Organisasi Mahasiswa &amp; Verifikasi Legalitas SK</h3>
          <p style="margin:0; font-size:0.85rem; color:var(--text-muted);">
            Verifikasi Surat Keputusan (SK) legalitas dari Dekanat atau Rektorat serta kontrol kuota maksimal 2 panitia per ormawa.
          </p>
        </div>
        <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
          <div class="admin-search-wrapper">
            <span class="admin-search-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="searchOrmawaInput" class="admin-search-input" oninput="filterOrmawaTable()" placeholder="Cari nama ormawa, nomor SK, ketua...">
          </div>
          <div class="admin-filter-pills" id="ormawaFilterPills" style="display:flex; gap:0.35rem;">
            <button type="button" class="btn btn-sm btn-primary ormawa-pill active" data-filter="ALL" onclick="setOrmawaStatusFilter('ALL')">Semua ({{ $allOrmawa->count() }})</button>
            <button type="button" class="btn btn-sm btn-outline ormawa-pill" data-filter="pending" onclick="setOrmawaStatusFilter('pending')">Menunggu ({{ $stats['pendingOrmawa'] }})</button>
            <button type="button" class="btn btn-sm btn-outline ormawa-pill" data-filter="verified" onclick="setOrmawaStatusFilter('verified')">Terverifikasi ({{ $stats['activeOrmawa'] }})</button>
          </div>
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width: 50px;">No</th>
              <th>Nama Organisasi &amp; Kategori</th>
              <th>Pengurus &amp; Pembina Dosen</th>
              <th>Dokumen SK Legalitas</th>
              <th>Status Akun</th>
              <th style="width: 170px;">Aksi Verifikasi</th>
            </tr>
          </thead>
          <tbody id="ormawaQueueBody">
            <!-- Rendered via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB PANE 2: MODERASI EVENT KAMPUS -->
  <div class="admin-tab-pane" id="tabPane_events" style="display:none;">
    <div class="admin-card-box">
      <div class="admin-card-header" style="flex-wrap:wrap; gap:1rem; align-items:flex-end;">
        <div>
          <h3>Antrean Verifikasi &amp; Moderasi Kegiatan Kampus</h3>
          <p style="margin:0; font-size:0.85rem; color:var(--text-muted);">
            Sesuai kebijakan tata kelola BAAK UMRAH, setiap kegiatan yang diajukan panitia ormawa wajib ditinjau dan disetujui Admin sebelum tampil di katalog publik mahasiswa.
          </p>
        </div>
        <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
          <div class="admin-search-wrapper">
            <span class="admin-search-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="searchEventInput" class="admin-search-input" oninput="filterEventsTable()" placeholder="Cari judul kegiatan, ormawa, lokasi...">
          </div>
          <div class="admin-filter-pills" id="eventFilterPills" style="display:flex; gap:0.35rem;">
            <button type="button" class="btn btn-sm btn-primary event-pill active" data-filter="ALL" onclick="setEventStatusFilter('ALL')">Semua ({{ $allEvents->count() }})</button>
            <button type="button" class="btn btn-sm btn-outline event-pill" data-filter="pending_approval" onclick="setEventStatusFilter('pending_approval')">Menunggu Review ({{ $stats['pendingEvents'] }})</button>
            <button type="button" class="btn btn-sm btn-outline event-pill" data-filter="published" onclick="setEventStatusFilter('published')">Dipublikasikan ({{ $allEvents->where('status', 'published')->count() }})</button>
          </div>
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width: 50px;">No</th>
              <th>Nama Kegiatan &amp; Ormawa Penyelenggara</th>
              <th>Kategori &amp; Biaya</th>
              <th>Jadwal &amp; Lokasi</th>
              <th>Kuota</th>
              <th>Status Moderasi</th>
              <th style="width: 170px;">Aksi Verifikasi</th>
            </tr>
          </thead>
          <tbody id="eventsQueueBody">
            <!-- Rendered via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB PANE 3: KELOLA PENGGUNA MAHASISWA -->
  <div class="admin-tab-pane" id="tabPane_users" style="display:none;">
    <div class="admin-card-box">
      <div class="admin-card-header" style="flex-wrap:wrap; gap:1rem; align-items:flex-end;">
        <div>
          <h3>Manajemen Akun Mahasiswa SIPA UMRAH</h3>
          <p style="margin:0; font-size:0.85rem; color:var(--text-muted);">
            Verifikasi status pangkalan data mahasiswa, kontrol hak akses login, dan proteksi akun kampus secara real-time.
          </p>
        </div>
        <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
          <div class="admin-search-wrapper">
            <span class="admin-search-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="searchUserInput" class="admin-search-input" oninput="filterUsersTable()" placeholder="Cari NIM, nama mahasiswa, prodi..." style="min-width:240px;">
          </div>
          <div class="admin-filter-pills" id="userFilterPills" style="display:flex; gap:0.35rem;">
            <button type="button" class="btn btn-sm btn-primary user-pill active" data-filter="ALL" onclick="setUserStatusFilter('ALL')">Semua (<span id="userCountAll">{{ count($students) }}</span>)</button>
            <button type="button" class="btn btn-sm btn-outline user-pill" data-filter="aktif" onclick="setUserStatusFilter('aktif')">Aktif (<span id="userCountActive">{{ collect($students)->where('status', 'Aktif')->count() }}</span>)</button>
            <button type="button" class="btn btn-sm btn-outline user-pill" data-filter="nonaktif" onclick="setUserStatusFilter('nonaktif')">Ditangguhkan (<span id="userCountSuspended">{{ collect($students)->where('status', 'Ditangguhkan')->count() }}</span>)</button>
          </div>
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width: 120px;">NIM</th>
              <th>Nama Mahasiswa</th>
              <th>Program Studi</th>
              <th>Fakultas</th>
              <th>Status Akun</th>
              <th style="width: 140px; text-align:center;">Aksi Kontrol</th>
            </tr>
          </thead>
          <tbody id="userTableBody">
            <!-- Rendered via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB PANE 4: LOG AUDIT SISTEM -->
  <div class="admin-tab-pane" id="tabPane_logs" style="display:none;">
    <div class="admin-card-box">
      <div class="admin-card-header">
        <div>
          <h3>Pencatatan Audit Trail &amp; Webhook Transaksi Platform</h3>
          <p style="margin:0; font-size:0.85rem; color:var(--text-muted);">
            Pencatatan aktivitas sistem, autentikasi, settlement webhook payment gateway, dan audit presensi kampus secara real-time.
          </p>
        </div>
        <div style="display:flex; gap:0.5rem; align-items:center;">
          <button class="btn btn-outline btn-sm" onclick="exportAuditLogs()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Unduh Log (.txt)
          </button>
        </div>
      </div>
      <div style="padding:1.5rem;">
        <div style="display:flex; gap:0.5rem; margin-bottom:1rem; flex-wrap:wrap;">
          <button class="btn btn-sm btn-primary log-filter-btn" data-cat="ALL" onclick="filterAuditLogs('ALL')">Semua Kategori</button>
          <button class="btn btn-sm btn-outline log-filter-btn" data-cat="AUTH" onclick="filterAuditLogs('AUTH')">Autentikasi</button>
          <button class="btn btn-sm btn-outline log-filter-btn" data-cat="GATEWAY" onclick="filterAuditLogs('GATEWAY')">Payment Gateway</button>
          <button class="btn btn-sm btn-outline log-filter-btn" data-cat="ATTENDANCE" onclick="filterAuditLogs('ATTENDANCE')">Presensi QR</button>
          <button class="btn btn-sm btn-outline log-filter-btn" data-cat="CERTIFICATE" onclick="filterAuditLogs('CERTIFICATE')">Sertifikat</button>
          <button class="btn btn-sm btn-outline log-filter-btn" data-cat="MODERATION" onclick="filterAuditLogs('MODERATION')">Moderasi Event</button>
        </div>
        <div id="auditLogContainer" style="font-family:monospace; font-size:0.82rem; line-height:1.9; color:#334155; background:#F8FAFC; padding:1.25rem; border-radius:8px; border:1px solid #E2E8F0; max-height:360px; overflow-y:auto;">
          <!-- Rendered via JS -->
        </div>
      </div>
    </div>
  </div>

  <!-- TAB PANE 5: PROFIL & KEAMANAN AKUN SUPER ADMIN BAAK -->
  <div class="admin-tab-pane" id="tabPane_profil" style="display:none;">
    
    <!-- Header Halaman Profil Admin -->
    <div class="sipa-dashboard-header" style="margin-bottom:1.5rem;">
      <div>
        <div style="display:inline-flex; align-items:center; gap:0.45rem; background:#fef3c7; color:#92400e; font-size:0.75rem; font-weight:800; padding:0.25rem 0.75rem; border-radius:999px; margin-bottom:0.5rem; border:1px solid #fde68a;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <span>Super Admin BAAK &bull; Ruang Kendali Otoritas</span>
        </div>
        <h1 class="sipa-page-title">Profil &amp; Keamanan Akun Admin BAAK</h1>
        <p class="sipa-page-subtitle">Kelola data unit kerja biro, narahubung dinas resmi, dan kredensial akses Ruang Kendali Admin BAAK secara terpusat.</p>
      </div>
    </div>

    <!-- KARTU TUNGGAL TERSATUKAN ALA SIPA UMRAH (ADMIN BAAK) -->
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

              <!-- Form Input Nomor WhatsApp / Kontak Dinas Aktif -->
              <form id="formUpdateKontak" onsubmit="simpanProfilKontak(event)">
                @csrf
                <div class="sipa-form-row">
                  <span class="sipa-row-label">Nomor Kontak Dinas <span style="color:#e11d48;">*</span></span>
                  <div class="sipa-row-field">
                    <input type="tel" id="inputHp" name="no_hp" class="sipa-input-editable" value="{{ auth()->user()?->no_wa ?? '' }}" placeholder="Contoh: 081234567890" required>
                    <span style="font-size:0.75rem; color:#64748b; margin-top:0.3rem; display:block;">
                      Nomor kontak dinas digunakan untuk koordinasi penting antar-unit kerja kampus.
                    </span>
                  </div>
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid #e5e7eb;">
                  <button type="submit" id="btnSubmitKontak" class="sipa-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4l2 3h6l2-3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span>Simpan Kontak Dinas</span>
                  </button>
                </div>
              </form>

            </div>

            <!-- Kolom Pas Foto Kanan (Ratio 3x4 Resmi SIPA) -->
            <div class="sipa-photo-column">
              <div class="sipa-pasfoto-box" id="sipaPasfotoBox">
                <img src="{{ auth()->user()?->avatar_url ?? '' }}" alt="Foto Profil Resmi BAAK" class="sipa-pasfoto-img" id="avatarLargeImg" style="{{ auth()->user()?->avatar_url ? '' : 'display:none;' }}">
                <div class="sipa-pasfoto-placeholder" id="avatarLargeInitial" style="{{ auth()->user()?->avatar_url ? 'display:none;' : '' }}">
                  {{ strtoupper(substr(auth()->user()?->nama ?? 'B', 0, 1)) }}
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

        <!-- PANE 2: UBAH KATA SANDI ADMIN -->
        <div class="profil-tab-pane" id="tabPane_sandi" style="display:none;">
          <div class="profile-alert-box" style="background:#f0f9ff; border-color:rgba(2, 132, 199, 0.2); color:#0369a1; margin-bottom:1.5rem;">
            <svg class="profile-alert-icon" style="color:#0284c7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <div>
              <strong style="color:#0369a1; font-size:0.9rem;">Kata Sandi Akun Super Admin BAAK</strong>
              <p style="margin:0.25rem 0 0; color:#0c4a6e; font-size:0.83rem;">
                Kata sandi ini digunakan untuk mengakses Ruang Kendali Admin BAAK TEMU UMRAH. Gunakan kombinasi kata sandi yang kuat dan ubah secara berkala demi keamanan platform universitas.
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

</div>

  <!-- MODAL DETAIL MODERASI EVENT KAMPUS -->
  <div class="modal-sk-backdrop" id="modalEventDetailAdmin">
    <div class="modal-sk-box" style="max-width:680px;">
      <div class="modal-sk-header">
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; display:flex; align-items:center; gap:0.5rem;">
          <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          REVIEW DETAIL PENGAJUAN EVENT PANITIA
        </h3>
        <button type="button" class="modal-sk-close" onclick="closeEventDetailModal()" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <div class="modal-sk-body">
        <div style="background:#F8FAFC; padding:1.25rem; border-radius:8px; border:1px solid var(--border-color); margin-bottom:1rem;">
          <h4 id="medJudul" style="margin:0 0 0.5rem; color:var(--umrah-navy); font-size:1.05rem;">Judul Event</h4>
          <p id="medDesc" style="margin:0 0 1rem; font-size:0.85rem; color:#475569; line-height:1.5;">Deskripsi kegiatan...</p>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.75rem; font-size:0.85rem;">
            <div>
              <span style="color:var(--text-muted);">Ormawa Pengusul:</span><br>
              <strong id="medOrmawa" style="color:var(--umrah-navy);">-</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Kategori &amp; Biaya:</span><br>
              <strong id="medKategoriBiaya">-</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Jadwal Kegiatan:</span><br>
              <strong id="medJadwal">-</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Lokasi Kampus:</span><br>
              <strong id="medLokasi">-</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Target Kuota:</span><br>
              <strong id="medKuota">-</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">E-Sertifikat SKP:</span><br>
              <strong id="medCert" style="color:#059669;">Tersedia (Aktif)</strong>
            </div>
          </div>
        </div>

        <div class="sk-doc-preview" style="padding:1rem; margin-bottom:1.25rem; text-align:left; border-radius:10px;">
          <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:0.75rem; min-width:0; flex:1 1 260px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#003E6D" stroke-width="2" style="flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <div style="min-width:0; flex:1;">
                <strong id="medDocName" style="font-size:0.85rem; display:block; word-break:break-all; overflow-wrap:anywhere; color:var(--umrah-navy); line-height:1.4;">Proposal_Kegiatan_2026.pdf</strong>
                <div style="font-size:0.75rem; color:#64748B; margin-top:0.15rem;">Surat Pengantar &amp; Rencana Anggaran Biaya</div>
              </div>
            </div>
            <a href="javascript:void(0)" class="btn btn-outline btn-sm" style="flex-shrink:0; white-space:nowrap; padding:0.4rem 0.85rem; font-size:0.8rem; display:inline-flex; align-items:center; gap:0.35rem;" onclick="showAdminToast('Berkas proposal terverifikasi dalam arsip BAAK.', 'info');">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <span>Cek PDF</span>
            </a>
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
          <button class="btn btn-outline" onclick="closeEventDetailModal()">Tutup</button>
          <button class="btn btn-danger" onclick="rejectFromDetailModal()">Tolak Pengajuan</button>
          <button class="btn btn-success" style="background:#059669; color:white;" onclick="approveFromDetailModal()">Setujui &amp; Publikasikan</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL TINJAU SK ORMAWA -->
  <div class="modal-sk-backdrop" id="modalSkVerif">
    <div class="modal-sk-box">
      <div class="modal-sk-header">
        <h3 style="margin:0; font-size:1.15rem; font-weight:800;">
          <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> TINJAUAN LEGALITAS &amp; SK PENGESAHAN ORMAWA
        </h3>
        <button type="button" class="modal-sk-close" onclick="closeSkModal()" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <div class="modal-sk-body">
        <div style="background:#F8FAFC; padding:1.25rem; border-radius:8px; border:1px solid var(--border-color); margin-bottom:1rem;">
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.75rem; font-size:0.85rem;">
            <div>
              <span style="color:var(--text-muted);">Nama Organisasi:</span><br>
              <strong id="modalSkNama" style="color:var(--umrah-navy);">HMTM</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Kategori Badan:</span><br>
              <strong id="modalSkKategori">Himpunan Jurusan</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Nomor SK Terdaftar:</span><br>
              <strong id="modalSkNo" style="font-family:monospace;">SK-DEK/012</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Pengurus Pemohon:</span><br>
              <strong id="modalSkKetua">Farhan</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Dosen Pembina:</span><br>
              <strong id="modalSkPembina">-</strong>
            </div>
            <div>
              <span style="color:var(--text-muted);">Kontak / Surel:</span><br>
              <strong id="modalSkEmail">-</strong>
            </div>
            <div style="grid-column: span 2; padding-top:0.6rem; border-top:1px dashed var(--border-color); display:flex; justify-content:space-between; align-items:center;">
              <span style="color:var(--text-muted); font-size:0.82rem;">Batas Kuota Panitia BAAK:</span>
              <strong id="modalSkKuotaPanitia" style="color:var(--umrah-navy); font-size:0.82rem; background:#EFF6FF; padding:0.25rem 0.6rem; border-radius:4px; border:1px solid #BFDBFE;">1 / 2 Panitia Aktif (Maksimal 2 Akun per Ormawa)</strong>
            </div>
          </div>
        </div>

        <div class="sk-doc-preview">
          <div style="font-size:2.5rem; margin-bottom:0.5rem;"><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
          <strong id="modalSkDocName" style="display:block; word-break:break-all; overflow-wrap:anywhere;">SK_Pengesahan_2026.pdf</strong>
          <p style="font-size:0.8rem; color:#64748B; margin:0.35rem 0 0.75rem;">
            Surat Keputusan Dekan FTTK UMRAH tentang Struktur Kepengurusan Periode 2026/2027
          </p>
          <a href="javascript:void(0)" class="btn btn-outline btn-sm" onclick="window.showToast('Berkas SK resmi ormawa SK_Pengesahan_2026.pdf terverifikasi sah.', 'success', 'Unduh SK Ormawa'); return false;">
            <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Buka &amp; Unduh Berkas PDF
          </a>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1.5rem;">
          <button class="btn btn-outline" onclick="closeSkModal()">Tutup</button>
          <button class="btn btn-danger" onclick="rejectModalCurrent()"><svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tolak Pendaftaran</button>
          <button class="btn btn-success" style="background:#059669; color:white;" onclick="approveModalCurrent()"><svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Setujui &amp; Terbitkan Akun Ormawa</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL IN-APP ALASAN PENOLAKAN RESMI (ORMAWA & EVENT) -->
  <div class="modal-sk-backdrop" id="modalAdminReject" style="z-index: 1060;">
    <div class="modal-sk-box" style="max-width: 520px;">
      <div class="modal-sk-header" style="background: linear-gradient(135deg, #7f1d1d, #991b1b) !important;">
        <h3 style="margin:0; font-size:1.05rem; font-weight:800; display:flex; align-items:center; gap:0.5rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span id="adminRejectModalTitle">Alasan Penolakan Pengajuan</span>
        </h3>
        <button type="button" class="modal-sk-close" onclick="closeAdminRejectModal()" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <div class="modal-sk-body" style="padding: 1.5rem;">
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
          <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #991b1b; margin-bottom: 0.2rem;">Sasaran Pengajuan:</div>
          <div id="adminRejectTargetName" style="font-weight: 700; color: #7f1d1d; font-size: 0.95rem;">-</div>
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display:block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">
            Tuliskan Catatan Resmi BAAK <span style="color:#ef4444;">*</span>
          </label>
          <textarea id="adminRejectReasonInput" rows="3" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 0.75rem; font-size: 0.85rem; font-family: inherit; resize: vertical; box-sizing: border-box;" placeholder="Tuliskan catatan atau revisi yang harus dipenuhi..."></textarea>
        </div>

        <!-- Preset Chips -->
        <div style="margin-bottom: 1.5rem;">
          <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem;">Pilihan Cepat Alasan Umum:</div>
          <div id="adminRejectChipsContainer" style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
            <!-- Populated via JS -->
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.65rem;">
          <button type="button" class="btn btn-outline" onclick="closeAdminRejectModal()" style="font-size:0.85rem; padding:0.55rem 1.1rem;">Batal</button>
          <button type="button" class="btn btn-danger" id="adminRejectConfirmBtn" onclick="submitAdminRejectAction()" style="font-size:0.85rem; padding:0.55rem 1.25rem; background:#dc2626; color:#fff; border:none; font-weight:700;">Tolak &amp; Simpan Catatan</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL IN-APP KONFIRMASI TINDAKAN (STATUS MAHASISWA DLL) -->
  <div class="modal-sk-backdrop" id="modalAdminConfirm" style="z-index: 1060;">
    <div class="modal-sk-box" style="max-width: 460px;">
      <div class="modal-sk-header" style="background: linear-gradient(135deg, #002B49, #003E6D) !important;">
        <h3 style="margin:0; font-size:1.05rem; font-weight:800; display:flex; align-items:center; gap:0.5rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <span id="adminConfirmModalTitle">Konfirmasi Tindakan Admin</span>
        </h3>
        <button type="button" class="modal-sk-close" onclick="closeAdminConfirmModal()" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <div class="modal-sk-body" style="padding: 1.5rem;">
        <p id="adminConfirmModalMessage" style="font-size: 0.9rem; color: #334155; line-height: 1.6; margin: 0 0 1rem;">
          Apakah Anda yakin ingin memproses tindakan ini?
        </p>

        <div id="adminConfirmNoteBox" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.8rem; color: #64748b; margin-bottom: 1.5rem; line-height: 1.5;">
          Catatan akan disimpan ke audit log BAAK.
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.65rem;">
          <button type="button" class="btn btn-outline" onclick="closeAdminConfirmModal()" style="font-size:0.85rem; padding:0.55rem 1.1rem;">Batal</button>
          <button type="button" class="btn btn-primary" id="adminConfirmExecuteBtn" onclick="executeAdminConfirmedAction()" style="font-size:0.85rem; padding:0.55rem 1.25rem; font-weight:700;">Ya, Lanjutkan</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/profil.js') }}"></script>
<script src="{{ asset('assets/js/admin.js') }}"></script>
@endpush
