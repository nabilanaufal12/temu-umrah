<!-- resources/views/pages/dashboard_panitia.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Panitia & Pengelolaan Event - TEMU UMRAH')
@section('meta_desc', 'Kelola kegiatan kemahasiswaan, kuota peserta, dan laporan kegiatan ormawa.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/panitia.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_PANITIA_EVENTS = @json($events);
  window.TEMU_PANITIA_BACKEND = true;
</script>
<main class="container panitia-main-container">
    
    <!-- Breadcrumb Navigasi SIPA UMRAH -->
    <nav class="breadcrumb-wrapper">
      <a href="{{ url('/panitia/dashboard') }}">Home</a>
      <span class="breadcrumb-separator">/</span>
      <span class="breadcrumb-current">Halaman Utama</span>
    </nav>

    <!-- Header Halaman Bersih & Tombol Buat Event -->
    <div class="sipa-dashboard-header">
      <div>
        <h1 class="sipa-page-title">Kelola Kegiatan Mahasiswa</h1>
        <p class="sipa-page-subtitle">Daftar publikasi kegiatan, moderasi peserta, dan alur persetujuan Admin BAAK.</p>
      </div>
      <div>
        <button id="btnOpenCreateModal" class="sipa-btn-create">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          <span>Ajukan Kegiatan Baru</span>
        </button>
      </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
      <div class="alert-popup alert-success" style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.25rem; border-radius: 0.75rem; background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #065f46;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span style="font-weight: 600; font-size: 0.88rem;">{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="alert-popup alert-danger" style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.25rem; border-radius: 0.75rem; background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span style="font-weight: 600; font-size: 0.88rem;">{{ session('error') }}</span>
      </div>
    @endif

    <!-- 4 KPI Stats Grid (Compact) -->
    <div class="sipa-kpi-grid">
      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background: #e0f2fe; color: #0284c7;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div>
          <div class="sipa-kpi-value" id="totalEventsCount">{{ $stats['events'] }} Kegiatan</div>
          <div class="sipa-kpi-label">Total Event Dikelola</div>
        </div>
      </div>

      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background: #dcfce7; color: #16a34a;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div>
          <div class="sipa-kpi-value" id="totalParticipantsCount">{{ $stats['participants'] }} Mahasiswa</div>
          <div class="sipa-kpi-label">Peserta Terdaftar</div>
        </div>
      </div>

      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background: #fef3c7; color: #d97706;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
        </div>
        <div>
          <div class="sipa-kpi-value">{{ $stats['pendingPayments'] }} Transaksi</div>
          <div class="sipa-kpi-label">Menunggu Verifikasi</div>
        </div>
      </div>

      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background: #f3e8ff; color: #9333ea;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        </div>
        <div>
          <div class="sipa-kpi-value">{{ $stats['certificates'] }} Terbit</div>
          <div class="sipa-kpi-label">E-Sertifikat Digital Mahasiswa</div>
        </div>
      </div>
    </div>

    <!-- Table Management Card (SIPA UMRAH Style) -->
    <div>
      <div class="sipa-table-toolbar">
        <div class="sipa-filter-group">
          <!-- Dropdown Kategori -->
          <select id="filterCategory" class="sipa-filter-select">
            <option value="">Pilih Kategori: Semua</option>
            <option value="SEMINAR">Seminar Akademik</option>
            <option value="WORKSHOP">Workshop Keterampilan</option>
            <option value="LOMBA">Kompetisi &amp; Lomba</option>
            <option value="BAZAR">Bazar &amp; Pameran</option>
          </select>

          <!-- Dropdown Status Persetujuan BAAK -->
          <select id="filterStatus" class="sipa-filter-select">
            <option value="">Pilih Status: Semua</option>
            <option value="Publik">Disetujui / Publik</option>
            <option value="Menunggu Persetujuan">Menunggu BAAK</option>
            <option value="Ditolak">Perlu Revisi / Ditolak</option>
            <option value="Selesai">Kegiatan Selesai</option>
          </select>

          <!-- Input Search Event -->
          <input type="text" id="filterSearch" class="sipa-filter-input" placeholder="Cari nama kegiatan..." style="min-width: 220px;">
        </div>

        <div>
          <button type="button" class="btn btn-outline btn-sm" onclick="resetFilters()" style="font-size: 0.8rem; padding: 0.45rem 0.85rem;">
            Reset Filter
          </button>
        </div>
      </div>

      <div class="sipa-table-container">
        <table class="sipa-data-table">
          <thead>
            <tr>
              <th style="width: 40px; text-align: center;">No</th>
              <th>Nama Kegiatan &amp; Lokasi</th>
              <th>Kategori</th>
              <th>Jadwal Pelaksanaan</th>
              <th>Kuota</th>
              <th>Biaya</th>
              <th>Status Persetujuan BAAK</th>
              <th style="text-align: center; width: 170px; white-space: nowrap;">Aksi Pengelolaan</th>
            </tr>
          </thead>
          <tbody id="panitiaEventsTableBody">
            <!-- Rendered by panitia_dashboard.js -->
          </tbody>
        </table>
        
        <div class="sipa-table-footer">
          <span id="sipaTableInfo">Menampilkan data kegiatan ormawa</span>
          <span style="font-size: 0.75rem; color: #94a3b8;">Sistem Informasi Event &bull; BAAK UMRAH</span>
        </div>
      </div>
    </div>

  </main>

  <!-- ==================== MODAL FORM BUAT KEGIATAN BARU (CRUD) ==================== -->
  <div id="eventCrudModal" class="modal-backdrop">
    <div class="modal-dialog modal-form-dialog">
      <div class="modal-header-custom">
        <div class="modal-header-left">
          <div class="modal-header-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
              <line x1="12" y1="14" x2="12" y2="18"/>
              <line x1="10" y1="16" x2="14" y2="16"/>
            </svg>
          </div>
          <div>
            <h3 class="modal-header-title" id="modalTitleText">Buat Kegiatan Ormawa Baru</h3>
            <p class="modal-header-sub" id="modalSubText">Formulir Pengajuan Kegiatan Resmi ke Admin BAAK</p>
          </div>
        </div>
        <button type="button" id="modalCrudClose" class="modal-close-btn-custom" aria-label="Tutup modal">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <form id="eventCrudForm" action="{{ route('panitia.event.store') }}" method="POST" data-backend-form="true" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <input type="hidden" name="_method" id="crudMethod" value="POST">
        <input type="hidden" name="event_id" id="crudEventId" value="">
        <div class="modal-scrollable-body">
          <div class="form-group">
            <label class="form-label" for="inputEventTitle">Judul Kegiatan Event <span class="form-label-req">*</span></label>
            <input type="text" name="judul" id="inputEventTitle" class="form-input" placeholder="Contoh: Seminar Nasional AI: Peluang Karir Generative AI 2026" required>
          </div>

          <div class="form-grid-2col">
            <div class="form-group">
              <label class="form-label" for="inputCategory">Kategori Kegiatan <span class="form-label-req">*</span></label>
              <select name="kategori" id="inputCategory" class="form-input" required>
                <option value="SEMINAR">Seminar Akademik</option>
                <option value="WORKSHOP">Workshop Keterampilan</option>
                <option value="LOMBA">Kompetisi &amp; Lomba</option>
                <option value="BAZAR">Bazar &amp; Pameran</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="inputQuota">Kuota Maksimal Peserta <span class="form-label-req">*</span></label>
              <input type="number" name="kuota_maks" id="inputQuota" class="form-input" value="150" required min="1">
            </div>
          </div>

          <div class="form-grid-2col">
            <div class="form-group">
              <label class="form-label" for="inputDate">Tanggal Pelaksanaan <span class="form-label-req">*</span></label>
              <input type="date" name="tanggal_kegiatan" id="inputDate" class="form-input" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="inputLocation">Lokasi / Tautan Kegiatan <span class="form-label-req">*</span></label>
              <input type="text" name="lokasi" id="inputLocation" class="form-input" placeholder="Contoh: Auditorium Gedung E Lt. 3" required>
            </div>
          </div>

          <!-- Skema Biaya -->
          <div class="radio-group-box">
            <div class="radio-group-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
              <span>Skema Biaya Pendaftaran</span>
            </div>
            <label class="radio-card-option">
              <input type="radio" name="tipe_biaya" id="feeFreeRadio" value="gratis" checked>
              <div class="radio-card-text">
                <span class="radio-card-title">Gratis (Rp 0)</span>
                <span class="radio-card-desc">Pendaftaran terbuka bebas biaya bagi seluruh mahasiswa</span>
              </div>
            </label>
            <label class="radio-card-option">
              <input type="radio" name="tipe_biaya" id="feePaidRadio" value="berbayar">
              <div class="radio-card-text">
                <span class="radio-card-title">Berbayar</span>
                <span class="radio-card-desc">Membutuhkan verifikasi bukti transfer pembayaran dari bendahara panitia</span>
              </div>
            </label>
            <div id="paidInputsBox" class="conditional-input-box">
              <div class="form-grid-2col">
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label" for="inputPrice">Nominal Tiket (Rp) <span class="form-label-req">*</span></label>
                  <input type="number" name="harga" id="inputPrice" class="form-input" placeholder="Contoh: 35000" min="0">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label" for="inputRekening">No. Rekening Panitia <span class="form-label-req">*</span></label>
                  <input type="text" id="inputRekening" class="form-input" placeholder="Contoh: BSI 1234-5678-90">
                </div>
              </div>
            </div>
          </div>

          <!-- Fasilitas E-Sertifikat -->
          <div class="radio-group-box">
            <div class="radio-group-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
              <span>Fasilitas E-Sertifikat Digital</span>
            </div>
            <label class="radio-card-option">
              <input type="radio" name="certScheme" id="certYesRadio" value="yes" checked>
              <div class="radio-card-text">
                <span class="radio-card-title">Ya, Sediakan E-Sertifikat</span>
                <span class="radio-card-desc">Aktifkan modul penerbitan sertifikat digital massal ber-QR resmi</span>
              </div>
            </label>
            <label class="radio-card-option">
              <input type="radio" name="certScheme" id="certNoRadio" value="no">
              <div class="radio-card-text">
                <span class="radio-card-title">Tidak Menyediakan</span>
                <span class="radio-card-desc">Kegiatan non-sertifikat (misal: bazar, festival seni, atau pameran bebas)</span>
              </div>
            </label>
          </div>

          <!-- Metode Presensi -->
          <div class="radio-group-box">
            <div class="radio-group-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              <span>Metode Presensi Mahasiswa</span>
            </div>
            <label class="radio-card-option">
              <input type="radio" name="attScheme" value="qr" checked>
              <div class="radio-card-text">
                <span class="radio-card-title">Scan QR Code Dinamis</span>
                <span class="radio-card-desc">Direkomendasikan untuk kegiatan luring menggunakan layar proyektor</span>
              </div>
            </label>
            <label class="radio-card-option">
              <input type="radio" name="attScheme" value="token">
              <div class="radio-card-text">
                <span class="radio-card-title">Kode Token Alfanumerik</span>
                <span class="radio-card-desc">Cocok untuk webinar daring melalui Zoom atau Google Meet</span>
              </div>
            </label>
          </div>
        </div>

        <div class="modal-footer-sticky">
          <button type="button" id="modalCrudCancel" class="btn-modal-cancel">Batal</button>
          <button type="submit" class="btn-modal-submit" id="btnCrudSubmit">
            <span id="btnSubmitText">Ajukan Kegiatan ke Admin BAAK</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==================== MODAL KONFIRMASI HAPUS EVENT (ELEGANT UX) ==================== -->
  <div id="deleteConfirmModal" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 440px; background: #ffffff; border-radius: 20px; padding: 2rem 1.75rem; box-shadow: 0 25px 65px -15px rgba(0, 43, 73, 0.4); border: 1.5px solid #fecaca; text-align: center;">
      <div style="width: 58px; height: 58px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
          <line x1="10" y1="11" x2="10" y2="17"/>
          <line x1="14" y1="11" x2="14" y2="17"/>
        </svg>
      </div>
      <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem; letter-spacing: -0.01em;">Hapus Kegiatan Ormawa?</h3>
      <p id="deleteEventTitleText" style="font-size: 0.88rem; color: #64748b; margin: 0 0 1.5rem; line-height: 1.5;">
        Apakah Anda yakin ingin menghapus kegiatan ini? Seluruh data pendaftaran, tiket, dan presensi untuk kegiatan ini akan dihapus secara permanen.
      </p>
      <div style="display: flex; gap: 0.75rem; justify-content: center;">
        <button type="button" id="btnCancelDelete" class="btn btn-outline" style="flex: 1; border-radius: 999px; font-weight: 700; padding: 0.65rem 1rem; border-color:#cbd5e1; color:#475569;">Batal</button>
        <button type="button" id="btnConfirmDelete" class="btn btn-danger" style="flex: 1; border-radius: 999px; font-weight: 700; padding: 0.65rem 1rem; background: #dc2626; border-color: #dc2626; color: white; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">Ya, Hapus</button>
      </div>
    </div>
  </div>

  <!-- Official UMRAH Footer -->
    <!-- Campus Standard Unified Footer -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/data-store.js') }}"></script>
<script src="{{ asset('assets/js/panitia_dashboard.js') }}"></script>
@endpush
