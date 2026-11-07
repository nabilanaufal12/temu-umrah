<!-- resources/views/pages/kelola_peserta.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Peserta & Verifikasi Pembayaran - TEMU UMRAH')
@section('meta_desc', 'Verifikasi bukti transfer pembayaran dan kelola status kehadiran peserta kegiatan.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/panitia.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/kelola_peserta.css') }}">
@endpush

@section('content')
<main class="peserta-wrapper">
    <div class="container">
      
      <!-- Breadcrumb Navigasi SIPA UMRAH -->
      <nav class="breadcrumb-wrapper">
        <a href="{{ url('/panitia/dashboard') }}">Home</a>
        <span class="breadcrumb-separator">/</span>
        <span>{{ $eventName }}</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Kelola Data Peserta &amp; Presensi</span>
      </nav>

      <!-- Header Halaman Bersih & Tombol Aksi Cepat -->
      <div class="sipa-dashboard-header">
        <div>
          <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom: 0.35rem;">
            <span class="badge badge-gold" style="font-size:0.75rem;">{{ $event->tipe_biaya === 'gratis' ? 'Bebas Biaya (Gratis)' : 'Biaya: Rp ' . number_format($event->harga, 0, ',', '.') . ' / Peserta' }}</span>
            <span class="badge badge-primary" style="font-size:0.75rem;">Kuota: {{ $event->kuota_maks }} Kursi</span>
          </div>
          <h1 class="sipa-page-title">Data Pendaftar Kegiatan: {{ $eventName }}</h1>
          <p class="sipa-page-subtitle">Penyelenggara: {{ optional($event->ormawa)->nama_ormawa ?? 'Panitia Penyelenggara' }} &bull; Pelaksanaan: {{ optional($event->tanggal_kegiatan)->translatedFormat('d F Y') }}</p>
        </div>
        <div style="display:flex; align-items:center; gap:0.6rem; flex-wrap:wrap;">
          <button class="btn btn-outline btn-sm" onclick="exportDataPeserta()" style="display:flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; font-weight:700;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            <span>Ekspor CSV</span>
          </button>
          <a href="{{ url('/panitia/event/' . $eventId . '/sesi-absensi') }}" class="sipa-btn-create" style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1.15rem; font-size:0.84rem; text-decoration:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Layar Absensi Proyektor &rarr;</span>
          </a>
        </div>
      </div>

      <!-- 5 Metric Counter Grid (Compact) -->
      <div class="sipa-kpi-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 1.5rem;">
        <div class="sipa-kpi-card">
          <div class="sipa-kpi-icon" style="background:#e0f2fe; color:#0284c7;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div>
            <div class="sipa-kpi-value" id="statTotal">{{ $stats['total'] }}</div>
            <div class="sipa-kpi-label">Total Pendaftar</div>
          </div>
        </div>

        <div class="sipa-kpi-card">
          <div class="sipa-kpi-icon" style="background:#dcfce7; color:#16a34a;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div>
            <div class="sipa-kpi-value text-success" id="statLunas">{{ $stats['lunas'] }}</div>
            <div class="sipa-kpi-label">Terdaftar Lunas</div>
          </div>
        </div>

        <div class="sipa-kpi-card">
          <div class="sipa-kpi-icon" style="background:#fef3c7; color:#d97706;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div>
            <div class="sipa-kpi-value text-warning" id="statPending">{{ $stats['pending'] }}</div>
            <div class="sipa-kpi-label">Menunggu Bayar</div>
          </div>
        </div>

        <div class="sipa-kpi-card">
          <div class="sipa-kpi-icon" style="background:#fee2e2; color:#dc2626;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          </div>
          <div>
            <div class="sipa-kpi-value text-danger" id="statDitolak">{{ $stats['ditolak'] }}</div>
            <div class="sipa-kpi-label">Kedaluwarsa</div>
          </div>
        </div>

        <div class="sipa-kpi-card">
          <div class="sipa-kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div>
            <div class="sipa-kpi-value text-blue" id="statHadir">{{ $stats['hadir'] }}</div>
            <div class="sipa-kpi-label">Telah Hadir Sah</div>
          </div>
        </div>
      </div>

      <!-- Filter & Search Toolbar (SIPA UMRAH Style) -->
      <div class="sipa-table-toolbar">
        <div class="sipa-filter-group">
          <!-- Dropdown Status Bayar -->
          <select id="filterBayar" class="sipa-filter-select">
            <option value="all" selected>Status Bayar: Semua ({{ $stats['total'] }})</option>
            <option value="pending">Menunggu Bayar ({{ $stats['pending'] }})</option>
            <option value="lunas">Terdaftar Lunas ({{ $stats['lunas'] }})</option>
            <option value="ditolak">Kedaluwarsa / Batal ({{ $stats['ditolak'] }})</option>
          </select>

          <!-- Dropdown Status Hadir -->
          <select id="filterHadir" class="sipa-filter-select">
            <option value="all">Kehadiran: Semua</option>
            <option value="hadir">Sudah Hadir Sah</option>
            <option value="belum">Belum Hadir</option>
          </select>

          <!-- Input Search Peserta -->
          <input type="text" id="searchPeserta" class="sipa-filter-input" placeholder="Cari nama, NIM, atau invoice..." style="min-width: 250px;">
        </div>

        <div>
          <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('searchPeserta').value=''; document.getElementById('filterBayar').value='all'; document.getElementById('filterHadir').value='all'; renderPesertaTable();" style="font-size:0.8rem; padding:0.45rem 0.85rem;">
            Reset Filter
          </button>
        </div>
      </div>

      <!-- Tabel Data Peserta (SIPA UMRAH Style) -->
      <div class="sipa-table-container">
        <table class="sipa-data-table peserta-table">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">No</th>
              <th>Mahasiswa Peserta</th>
              <th>No Invoice</th>
              <th>Status Pembayaran</th>
              <th>Kanal &amp; Referensi Gateway</th>
              <th>Status Presensi</th>
              <th style="width: 170px; text-align: center;">Aksi &amp; Presensi</th>
            </tr>
          </thead>
          <tbody id="pesertaTableBody">
            <!-- Rendered via JavaScript -->
          </tbody>
        </table>

        <div class="sipa-table-footer">
          <span id="footerCounter">Menampilkan {{ $stats['total'] }} dari {{ $stats['total'] }} peserta terdaftar</span>
          <div style="display:flex; gap:0.5rem; align-items:center;">
            <a href="{{ url('/panitia/event/' . $eventId . '/terbitkan-sertifikat') }}" class="btn btn-outline btn-sm" style="font-size:0.8rem; font-weight:700;">
              Lanjut ke Penerbitan Sertifikat &rarr;
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>
  <!-- Official UMRAH Footer -->
    <!-- Campus Standard Unified Footer -->
  



  <!-- MODAL OVERLAY: LOG AUDIT PAYMENT GATEWAY (READ-ONLY SESUAI ARAHAN DOSEN) -->
  <div class="modal-verif-backdrop" id="modalVerifikasi">
    <div class="modal-verif-box">
      <div class="modal-verif-header">
        <div style="display:flex; align-items:center; gap:0.75rem;">
          <span style="font-size:1.25rem;"><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 2 4 22 7 20 10 22 13 20 16 22 19 20 19 2"></polyline><line x1="8" y1="7" x2="15" y2="7"></line><line x1="8" y1="11" x2="15" y2="11"></line><line x1="8" y1="15" x2="13" y2="15"></line></svg></span>
          <h3>LOG AUDIT TRANSAKSI PAYMENT GATEWAY</h3>
        </div>
        <button onclick="closeModalVerif()" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer; line-height:1;">&times;</button>
      </div>

      <div class="modal-verif-body">
        
        <!-- Kolom Kiri: Respons Webhook Gateway Digital -->
        <div>
          <div style="font-size:0.85rem; font-weight:700; color:var(--umrah-navy); margin-bottom:0.5rem; display:flex; justify-content:space-between; align-items:center;">
            <span>STATUS WEBHOOK GATEWAY RESMI</span>
            <span style="font-size:0.75rem; color:#10B981; font-weight:700;" id="strukStatusGateway">SETTLEMENT (200 OK)</span>
          </div>

          <div class="struk-card">
            <div class="struk-ticket">
              <div class="struk-badge-bank">
                <span style="background:#003E6D; color:white; padding:0.2rem 0.5rem; border-radius:4px; font-size:0.75rem;">GATEWAY</span>
                <span id="strukBankName">MIDTRANS SNAP (ASPI)</span>
              </div>

              <div class="struk-row">
                <span class="lbl">ID Referensi Gateway:</span>
                <span class="val" id="strukRefNum">SETTLED-QRIS-994182</span>
              </div>
              <div class="struk-row">
                <span class="lbl">Waktu Dibuat:</span>
                <span class="val" id="strukWaktu">05 Okt 2026, 16.12 WIB</span>
              </div>
              <div class="struk-row">
                <span class="lbl">Waktu Settlement:</span>
                <span class="val" id="strukSettlement">05 Okt 2026, 16.12.08 WIB</span>
              </div>
              <div class="struk-row">
                <span class="lbl">Kanal Pembayaran:</span>
                <span class="val" id="strukPengirim">QRIS Dinamis ASPI</span>
              </div>
              <div class="struk-row">
                <span class="lbl">Merchant Akun:</span>
                <span class="val" id="strukPenerima">Rekening Penampungan BAAK UMRAH</span>
              </div>
              <div class="struk-row">
                <span class="lbl">Keamanan Payload:</span>
                <span class="val" style="color:#059669; font-weight:700;">SHA512 Signature Valid</span>
              </div>

              <div class="struk-amount-highlight">
                <div style="font-size:0.75rem; color:#065F46; font-weight:700; text-transform:uppercase;">Gross Amount (Lunas)</div>
                <div class="amount" id="strukNominal">Rp 35.000</div>
              </div>

              <div style="margin-top:0.75rem; font-size:0.72rem; color:#64748B; text-align:center;">
                * Terverifikasi otomatis melalui Webhook Payment Gateway. E-Tiket diterbitkan langsung ke akun mahasiswa.
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: Rincian Peserta & Status E-Tiket -->
        <div>
          <div style="font-size:0.85rem; font-weight:700; color:var(--umrah-navy); margin-bottom:0.5rem;">
            DATA PESERTA &amp; STATUS E-TIKET
          </div>

          <div class="verif-detail-box">
            <div class="verif-detail-item">
              <span class="lbl">Nama Mahasiswa:</span>
               <span class="val" id="modalPesertaNama">Nabil</span>
            </div>
            <div class="verif-detail-item">
              <span class="lbl">NIM &amp; Program Studi:</span>
               <span class="val" id="modalPesertaNim">2401020073 (Mahasiswa UMRAH)</span>
            </div>
            <div class="verif-detail-item">
              <span class="lbl">Alamat Surel:</span>
              <span class="val" id="modalPesertaEmail">ahmad.nabil@mhs.umrah.ac.id</span>
            </div>
            <div class="verif-detail-item">
              <span class="lbl">Nomor Invoice:</span>
              <span class="val" id="modalPesertaInvoice">#INV-2026-0891</span>
            </div>
            <div class="verif-detail-item">
              <span class="lbl">Metode Pembayaran:</span>
              <span class="val" id="modalBankPengirim">QRIS Dinamis ASPI</span>
            </div>
            <div class="verif-detail-item">
              <span class="lbl">Total Tagihan:</span>
              <span class="val" style="color:var(--umrah-navy);" id="modalNominalWajib">Rp 35.000</span>
            </div>
          </div>

          <div style="margin-top:1rem; background:#ECFDF5; border:1px solid #A7F3D0; border-radius:8px; padding:1rem;">
            <div style="display:flex; align-items:center; gap:0.5rem; font-weight:700; color:#065F46; font-size:0.85rem; margin-bottom:0.35rem;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>E-Tiket &amp; Kode QR Presensi Aktif</span>
            </div>
            <p style="margin:0; font-size:0.78rem; color:#047857; line-height:1.5;">
              Peserta ini telah terdaftar resmi dan memiliki hak akses ke sesi absensi serta penerbitan sertifikat resmi kegiatan.
            </p>
          </div>
        </div>

      </div>

      <div class="modal-verif-footer" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
        <div id="modalActionButtons" style="display:flex; gap:0.5rem;">
          <button type="button" class="btn btn-outline" style="color:#DC2626; border-color:#F87171;" onclick="rejectPendingPaymentPrompt()" id="btnRejectPayment">
            Tolak Pembayaran
          </button>
          <button type="button" class="btn btn-primary" style="background:#059669; border-color:#059669; color:#fff;" onclick="approvePendingPayment()" id="btnApprovePayment">
            Setujui Pembayaran
          </button>
        </div>
        <div style="display:flex; gap:0.5rem;">
          <button type="button" class="btn btn-outline" onclick="closeModalVerif()">
            Tutup Audit Log
          </button>
          <button type="button" class="btn btn-primary" onclick="copyGatewayPayload()" style="display:flex; align-items:center; gap:0.4rem;">
            <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            <span>Salin JSON Payload</span>
          </button>
        </div>
      </div>

    </div>
  </div>

  @endsection

@push('scripts')
<script>
  window.initialPesertaData = @json($pesertaList);
  window.kelolaPesertaEventId = {{ $eventId }};
  window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('assets/js/kelola_peserta.js') }}"></script>
@endpush
