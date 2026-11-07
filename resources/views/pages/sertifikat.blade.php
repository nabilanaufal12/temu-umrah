<!-- resources/views/pages/sertifikat.blade.php -->
@extends('layouts.app')

@section('title', 'Pratinjau & Unduh E-Sertifikat - TEMU UMRAH')
@section('meta_desc', 'Cetak dan unduh e-sertifikat resmi ber-SK Dekanat FTTK Universitas Maritim Raja Ali Haji.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/sertifikat.css') }}">
@endpush

@section('content')
<main class="cert-page-wrapper">
  <div class="container">

    @if(session('success'))
      <div class="alert-popup alert-success" id="alertSuccessBanner" style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 1rem; background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #065f46;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="document.getElementById('alertSuccessBanner').remove()" style="background: none; border: none; color: #065f46; cursor: pointer; padding: 0.25rem; display: inline-flex; align-items: center; opacity: 0.8;" aria-label="Tutup notifikasi">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert-popup alert-danger" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 1rem; background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span style="font-weight: 600;">{{ session('error') }}</span>
      </div>
    @endif

    <!-- Header Strip -->
    <section class="cert-header-strip">
      <div class="cert-header-left">
        <div class="cert-eyebrow">
          <span class="cert-pulse-dot"></span>
          <span>E-SERTIFIKAT DIGITAL MAHASISWA</span>
        </div>
        <h1 class="cert-page-title">Pratinjau &amp; Unduh E-Sertifikat Resmi</h1>
        <p class="cert-page-desc">
          Dokumen pengakuan kelulusan dan perolehan poin SKP kemahasiswaan dengan tanda tangan elektronik terverifikasi Dekanat FTTK Universitas Maritim Raja Ali Haji.
        </p>
      </div>

      @if(isset($allCertificates) && count($allCertificates) > 1)
        <!-- Certificate Switcher jika punya lebih dari 1 sertifikat -->
        <div class="cert-header-switcher">
          <label for="certSelectDropdown" class="switcher-label">Pilih Dokumen Sertifikat:</label>
          <div class="switcher-select-wrap">
            <svg class="switcher-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <select id="certSelectDropdown" class="switcher-select" onchange="window.location.href = this.value;">
              @foreach($allCertificates as $c)
                <option value="{{ url('/sertifikat/' . $c->nomor_sertifikat) }}" {{ ($certificate?->id === $c->id) ? 'selected' : '' }}>
                  {{ Str::limit($c->registration?->event?->judul ?? 'Sertifikat Event', 42) }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      @endif
    </section>

    @if($certificate && $event)
      <!-- Zoom & Mode Toolbar -->
      <section class="cert-toolbar">
        <div class="zoom-controls">
          <span class="zoom-label">Zoom Dokumen:</span>
          <button type="button" class="zoom-btn" onclick="zoomCert(-10)" title="Perkecil Ukuran">-</button>
          <span id="zoomText" class="zoom-level-text">100%</span>
          <button type="button" class="zoom-btn" onclick="zoomCert(10)" title="Perbesar Ukuran">+</button>
          <button type="button" class="zoom-btn-reset" onclick="resetZoom()" title="Kembali ke Ukuran Normal">Reset (100%)</button>
        </div>

        <div class="cert-mode-badge">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="3" width="20" height="18" rx="2"/>
            <line x1="2" y1="9" x2="22" y2="9"/>
          </svg>
          @if($hasUploadedFile)
            <span>Berkas Resmi Khusus Panitia: {{ strtoupper(pathinfo($certificate->file_sertifikat_path, PATHINFO_EXTENSION)) }}</span>
          @elseif($hasCustomTemplate)
            <span>Desain Khusus: Template Ormawa ({{ $event->ormawa?->nama_ormawa ?? 'Pubdok' }})</span>
          @else
            <span>Rasio Baku: A4 Landscape (297 mm &times; 210 mm)</span>
          @endif
        </div>
      </section>

      <!-- Informasi Integritas 3-Kolom Strip -->
      <section class="certificate-info-strip" aria-label="Informasi sertifikat digital">
        <div class="certificate-info-item">
          <div class="cert-info-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="cert-info-body">
            <strong>Nomor Seri Unik Terverifikasi</strong>
            <span>Nomor dokumen sah terdaftar di pangkalan data kampus.</span>
          </div>
        </div>

        <div class="certificate-info-item">
          <div class="cert-info-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          </div>
          <div class="cert-info-body">
            <strong>Validasi Kriptografi QR Publik</strong>
            <span>Pindai QR pada dokumen untuk memeriksa keaslian publik.</span>
          </div>
        </div>

        <div class="certificate-info-item">
          <div class="cert-info-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div class="cert-info-body">
            <strong>Sinkronisasi Poin SKP Resmi</strong>
            <span>Poin {{ $event->skp ?? 4 }} SKP telah terhubung ke profil akun Anda.</span>
          </div>
        </div>
      </section>

      <!-- 2-Column Certificate Layout -->
      <main class="cert-layout">
        
        <!-- ==================== KOLOM KIRI: CANVAS SERTIFIKAT ==================== -->
        <section class="cert-viewer-container">
          
          @if($hasUploadedFile && $isImageFile)
            <!-- Skenario B1: Berkas Gambar Asli Jadi yang Diunggah Panitia -->
            <div id="canvasWrapper" class="cert-canvas-wrapper" style="text-align: center;">
              <div style="background:#ffffff; border-radius:1rem; padding:1rem; box-shadow:0 15px 35px rgba(0,34,68,0.12); border:1px solid #e2e8f0; display:inline-block;">
                <img src="{{ $uploadedFileUrl }}" alt="Sertifikat Resmi Panitia" style="width:100%; max-width:920px; height:auto; border-radius:0.75rem; display:block;">
              </div>
            </div>

          @elseif($hasUploadedFile && $isPdfFile)
            <!-- Skenario B2: Berkas Dokumen PDF Resmi Canva yang Diunggah Panitia -->
            <div id="canvasWrapper" class="cert-canvas-wrapper" style="text-align: center;">
              <div style="background:#ffffff; border-radius:1rem; padding:0.75rem; box-shadow:0 15px 35px rgba(0,34,68,0.12); border:1px solid #e2e8f0; width:100%; max-width:920px; margin:0 auto;">
                <object data="{{ $uploadedFileUrl }}#toolbar=0" type="application/pdf" style="width:100%; height:580px; border-radius:0.5rem; display:block;">
                  <div style="padding: 2.5rem; color: #64748b; font-size: 0.9rem;">
                    <p>Pratinjau langsung berkas PDF resmi panitia.</p>
                    <a href="{{ route('sertifikat.download_file', $certificate->nomor_sertifikat) }}" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:0.4rem;">
                      Unduh E-Sertifikat PDF Asli
                    </a>
                  </div>
                </object>
              </div>
            </div>

          @else
            <!-- Skenario A & Default: Kanvas Sertifikat (Dengan Background Template Ormawa jika ada) -->
            <div id="canvasWrapper" class="cert-canvas-wrapper">
              <article class="cert-canvas" id="printableCert" style="{{ $hasCustomTemplate ? 'background-image: url(' . $templateUrl . '); background-size: cover; background-position: center; border:none; box-shadow:0 15px 35px rgba(0,34,68,0.12);' : '' }}">
                
                @if(!$hasCustomTemplate)
                  <!-- Ornate Double Borders & Filigrees (Hanya tampil jika tidak ada background template ormawa) -->
                  <div class="cert-outer-border"></div>
                  <div class="cert-inner-border"></div>
                  <div class="corner-decor corner-tl"></div>
                  <div class="corner-decor corner-tr"></div>
                  <div class="corner-decor corner-bl"></div>
                  <div class="corner-decor corner-br"></div>

                  <!-- Watermark Kemaritiman -->
                  <div class="cert-watermark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#003E6D" stroke-width="1.2">
                      <circle cx="12" cy="5" r="3"/>
                      <line x1="12" y1="22" x2="12" y2="8"/>
                      <path d="M5 12H2a10 10 0 0 0 20 0h-3"/>
                    </svg>
                  </div>
                @endif

                <!-- Header Sertifikat -->
                <header class="cert-header">
                  @if(!$hasCustomTemplate)
                    <div class="cert-logos-row">
                      <div class="cert-logo-circle" title="Logo TEMU UMRAH">
                        <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" width="38" height="38" alt="Logo TEMU">
                      </div>
                      <div class="cert-univ-name">
                        <span class="univ-line-1">UNIVERSITAS MARITIM RAJA ALI HAJI</span>
                        <span class="univ-line-2">FAKULTAS TEKNIK &amp; TEKNOLOGI KEMARITIMAN</span>
                      </div>
                      <div class="cert-logo-circle cert-faculty-badge" title="Fakultas Teknik & Teknologi Kemaritiman">
                        <span>FTTK</span>
                      </div>
                    </div>
                    <h2 class="cert-title-huge">SERTIFIKAT PENGHARGAAN</h2>
                  @else
                    <div style="height: 65px;"></div>
                  @endif

                  <div id="certSerial" class="cert-serial-num" style="{{ $hasCustomTemplate ? 'background: rgba(255,255,255,0.92); display: inline-block; padding: 0.35rem 1rem; border-radius: 999px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);' : '' }}">
                    NOMOR SERI RESMI: {{ $certificate->nomor_sertifikat }}
                  </div>
                </header>

                <!-- Body Tengah Sertifikat -->
                <div class="cert-body-center">
                  <p class="cert-given-text">Diberikan dengan penuh apresiasi dan kehormatan kepada:</p>
                  <div id="certName" class="cert-recipient-name">
                    {{ strtoupper($user->nama ?? 'Mahasiswa UMRAH') }}
                  </div>
                  <p id="certNim" class="cert-recipient-meta">
                    NIM: {{ $user->nim ?? '-' }} — Program Studi {{ $user->prodi ?? 'Teknik Informatika' }} UMRAH
                  </p>
                  
                  <div class="cert-role-wrap">
                    <span class="cert-role-highlight">PESERTA ({{ $event->skp ?? 4 }} SKP KEMAHASISWAAN)</span>
                  </div>

                  <h3 id="certEvent" class="cert-event-title">
                    {{ $event->judul }}
                  </h3>
                  <p class="cert-event-date">
                    Diselenggarakan oleh {{ $event->ormawa?->nama_ormawa ?? 'Ormawa UMRAH' }} pada {{ \Carbon\Carbon::parse($event->tanggal_kegiatan)->translatedFormat('d F Y') }}
                  </p>
                </div>

                <!-- Footer Tanda Tangan & QR Validasi -->
                <footer class="cert-footer-row">
                  <!-- TTD 1: Dekan / Dosen Pembina -->
                  <div class="sig-box">
                    <div class="sig-svg-wrap">
                      <svg class="sig-svg-image" viewBox="0 0 120 40" fill="none" stroke="#003E6D" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 25 C20 10, 35 35, 50 15 C60 5, 75 30, 90 20 C100 15, 110 25, 115 10"/>
                      </svg>
                    </div>
                    <span class="sig-line">Prof. Dr. Ir. H. M. Zainuri, M.T.</span>
                    <span class="sig-role">Dekan FTTK UMRAH</span>
                  </div>

                  <!-- QR Code Tengah -->
                  <div class="cert-qr-center">
                    <a href="{{ route('sertifikat.verifikasi', $certificate->nomor_sertifikat) }}" target="_blank" rel="noopener noreferrer" class="cert-qr-small" title="Klik atau pindai QR untuk verifikasi keaslian publik" style="display:inline-block; text-decoration:none;">
                      <svg width="68" height="68" viewBox="0 0 100 100" fill="#003E6D">
                        <rect width="100" height="100" fill="#FFFFFF"/>
                        <rect x="5" y="5" width="28" height="28" fill="#003E6D"/>
                        <rect x="9" y="9" width="20" height="20" fill="#FFFFFF"/>
                        <rect x="13" y="13" width="12" height="12" fill="#003E6D"/>
                        <rect x="67" y="5" width="28" height="28" fill="#003E6D"/>
                        <rect x="71" y="9" width="20" height="20" fill="#FFFFFF"/>
                        <rect x="75" y="13" width="12" height="12" fill="#003E6D"/>
                        <rect x="5" y="67" width="28" height="28" fill="#003E6D"/>
                        <rect x="9" y="71" width="20" height="20" fill="#FFFFFF"/>
                        <rect x="13" y="75" width="12" height="12" fill="#003E6D"/>
                        <rect x="42" y="42" width="16" height="16" fill="#003E6D"/>
                        <rect x="42" y="15" width="8" height="8" fill="#003E6D"/>
                        <rect x="15" y="42" width="8" height="8" fill="#003E6D"/>
                        <rect x="42" y="68" width="8" height="8" fill="#003E6D"/>
                        <rect x="68" y="42" width="8" height="8" fill="#003E6D"/>
                        <rect x="68" y="68" width="8" height="8" fill="#003E6D"/>
                      </svg>
                    </a>
                    <span class="cert-qr-caption">Klik / Pindai Verifikasi</span>
                  </div>

                  <!-- TTD 2: Ketua Pelaksana -->
                  <div class="sig-box">
                    <div class="sig-svg-wrap">
                      <svg class="sig-svg-image" viewBox="0 0 120 40" fill="none" stroke="#003E6D" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 20 C30 5, 40 30, 60 10 C75 0, 85 35, 105 18"/>
                      </svg>
                    </div>
                    <span class="sig-line">Ahmad Fauzi</span>
                    <span class="sig-role">Ketua Pelaksana Kegiatan</span>
                  </div>
                </footer>

              </article>
            </div>
          @endif

        </section>

        <!-- ==================== KOLOM KANAN: ACTIONS SIDEBAR ==================== -->
        <aside class="cert-sidebar-card">
          <div class="sidebar-header">
            <h3 class="sidebar-title">Aksi &amp; Opsi Unduh</h3>
            <p class="sidebar-desc">Pilih format dokumen resmi yang ingin Anda simpan atau gunakan untuk portofolio Anda.</p>
          </div>

          <!-- Notice Berkas Unggahan Panitia (Jika Ada) -->
          @if($hasUploadedFile)
            <div style="background:#ecfdf5; border:1.5px solid #a7f3d0; border-radius:1rem; padding:1.15rem; margin-bottom:1.25rem;">
              <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.35rem; color:#065f46; font-weight:800; font-size:0.88rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Berkas Asli Panitia Terunggah</span>
              </div>
              <p style="font-size:0.78rem; color:#047857; margin:0 0 0.85rem; line-height:1.45;">
                Panitia <strong>{{ $event->ormawa?->nama_ormawa ?? 'Ormawa UMRAH' }}</strong> telah mengunggah berkas sertifikat resmi karya desain khusus kepanitiaan.
              </p>
              <a href="{{ route('sertifikat.download_file', $certificate->nomor_sertifikat) }}" class="btn btn-primary btn-action-full" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Unduh Berkas Asli Panitia ({{ strtoupper(pathinfo($certificate->file_sertifikat_path, PATHINFO_EXTENSION)) }})</span>
              </a>
            </div>
          @elseif($hasCustomTemplate)
            <div style="background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:1rem; padding:1rem; margin-bottom:1.25rem;">
              <div style="display:flex; align-items:center; gap:0.45rem; margin-bottom:0.25rem; color:#1e40af; font-weight:800; font-size:0.85rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Desain Resmi Karya Ormawa</span>
              </div>
              <p style="font-size:0.76rem; color:#1d4ed8; margin:0; line-height:1.4;">
                Dokumen menggunakan template karya divisi Pubdok <strong>{{ $event->ormawa?->nama_ormawa ?? 'Ormawa' }}</strong>.
              </p>
            </div>
          @endif

          <div class="cert-actions-list">
            @if(!$hasUploadedFile)
              <button type="button" class="btn btn-primary btn-action-full" onclick="downloadPdf()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Simpan PDF / Cetak Sertifikat (A4)</span>
              </button>
            @else
              <button type="button" class="btn btn-outline btn-action-full" onclick="printCertificate()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>Cetak Dokumen Fisik (Print)</span>
              </button>
            @endif
          </div>

          <button type="button" class="btn-linkedin-share" onclick="shareLinkedIn()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.64 1.64 0 1 0 0-3.28 1.64 1.64 0 0 0 0 3.28m1.4 9.74v-8.37H5.06v8.37z"/></svg>
            <span>Tambahkan ke Profil LinkedIn</span>
          </button>

          <a href="{{ url('/event-saya') }}" class="btn-back-events">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <span>Kembali ke Dashboard Event</span>
          </a>
        </aside>

      </main>
    @else
      <!-- Empty State jika belum ada sertifikat -->
      <section class="cert-empty-card">
        <div class="cert-empty-icon">
          <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <h3 class="empty-title">Belum Ada E-Sertifikat Diterbitkan</h3>
        <p class="empty-desc">
          E-sertifikat akan otomatis diterbitkan panitia setelah Anda menyelesaikan pendaftaran dan melakukan presensi kehadiran pada kegiatan akademik yang Anda ikuti.
        </p>
        <div class="empty-actions">
          <a href="{{ url('/event-saya') }}" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
            <span>Periksa Riwayat Event Saya</span>
          </a>
          <a href="{{ url('/presensi') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Buka Sesi Presensi</span>
          </a>
        </div>
      </section>
    @endif

  </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/sertifikat.js') }}"></script>
@endpush
