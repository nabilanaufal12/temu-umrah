<!-- resources/views/pages/notifikasi.blade.php -->
@extends('layouts.app')

@section('title', 'Pusat Notifikasi Mahasiswa - TEMU UMRAH')
@section('meta_desc', 'Daftar pemberitahuan real-time mengenai status pembayaran, pembukaan presensi, dan sertifikat.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/notifikasi.css') }}">
@endpush

@section('content')
  <!-- Page Background Content Behind Open Drawer -->
  <main class="container notification-page-intro">
    <div>
      <span class="admin-kicker">PUSAT INFORMASI</span>
      <h1>Pembaruan yang perlu kamu tahu</h1>
      <p>Notifikasi pembayaran, pembukaan presensi, penerbitan sertifikat, dan perubahan status event muncul di panel sebelah kanan.</p>
    </div>
    <div class="notification-guide-grid">
      <span><strong>01</strong> Status pendaftaran</span>
      <span><strong>02</strong> Pengingat kegiatan</span>
      <span><strong>03</strong> Arsip sertifikat</span>
    </div>
  </main>

  <!-- ==================== DRAWER NOTIFIKASI AKTIF ( / ) ==================== -->
  <div id="drawerBackdrop" class="drawer-backdrop open">
    <aside class="notif-drawer">
      
      <!-- Drawer Header -->
      <div class="drawer-header">
        <div>
          <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            Pemberitahuan & Pengingat
          </h3>
          <span style="font-size: 0.72rem; color: var(--text-muted);">Sistem Realtime TEMU UMRAH</span>
        </div>
        <button id="drawerCloseBtn" class="drawer-close-btn" title="Tutup Panel">&times;</button>
      </div>

      <!-- Tab Switcher: Semua vs Belum Dibaca -->
      <div class="notif-filter-tabs">
        <button id="tabNotifAll" class="notif-tab-btn active">
          Semua <span id="tabNotifAllCount" class="notif-tab-badge">{{ $totalCount ?? 0 }}</span>
        </button>
        <button id="tabNotifUnread" class="notif-tab-btn">
          Belum Dibaca <span id="tabNotifUnreadCount" class="notif-tab-badge">{{ $unreadCount ?? 0 }}</span>
        </button>
      </div>

      <!-- Mark All As Read Action Bar (14B) -->
      <div style="padding: 0.65rem 1.25rem 0.25rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.74rem; color: var(--text-muted);">Klik notifikasi untuk aksi langsung</span>
        <button id="btnMarkAllRead" style="background: none; border: none; font-size: 0.76rem; font-weight: 700; color: var(--umrah-blue); cursor: pointer; text-decoration: underline;">
          Tandai Semua Dibaca
        </button>
      </div>

      <!-- Drawer Body: List of Notifications -->
      <div id="notifListBody" class="drawer-body">
        <!-- Injected Dynamically by notifikasi.js -->
      </div>

      <!-- Drawer Footer -->
      <div class="drawer-footer">
        <button onclick="window.location.href='{{ url('/') }}'" class="btn btn-outline btn-sm" style="width: 100%;">
          Tutup Panel Pemberitahuan &times;
        </button>
      </div>

    </aside>
  </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/notifikasi.js') }}"></script>
@endpush
