<!-- resources/views/pages/event_detail.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Kegiatan Kemahasiswaan - TEMU UMRAH')
@section('meta_desc', 'Informasi lengkap kegiatan kemahasiswaan, narasumber, manfaat, dan formulir pendaftaran.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/detail.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_BACKEND_EVENT = @json($eventData);
  window.TEMU_REGISTRATION_URL = @json(route('event.register', ['slug' => $eventData['slug']]));
  window.TEMU_CURRENT_USER = @json(auth()->user());
  window.TEMU_IS_REGISTERED = @json($isRegistered ?? false);
  window.TEMU_USER_REGISTRATION = @json($userRegistration ?? null);
</script>
  <!-- Main 2-Column Detail Layout -->
  <main class="container detail-layout">
    
    <!-- ==================== KOLOM KIRI: KONTEN LENGKAP EVENT ==================== -->
    <div class="detail-main">
      
      <!-- Graphic Banner (16:9) -->
      <div id="bannerHero" class="detail-hero-banner" style="{{ !empty($eventData['bannerPath']) ? 'background-image: linear-gradient(to top, rgba(0, 43, 73, 0.92) 0%, rgba(0, 43, 73, 0.35) 60%, rgba(0, 43, 73, 0.2) 100%), url(' . $eventData['bannerPath'] . '); background-size: cover; background-position: center;' : '' }}">
        
        <div class="detail-hero-content">
          <div class="detail-badges-row">
            <span id="heroBadgeCategory" class="badge badge-category">{{ $eventData['category'] ?? 'EVENT' }}</span>
            @if($isRegistered ?? false)
              <span id="heroBadgeStatus" class="badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; display:inline-flex; align-items:center; gap:0.3rem;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> ANDA TELAH TERDAFTAR</span>
            @elseif(($eventData['quotaFilled'] ?? 0) >= ($eventData['quotaMax'] ?? 1))
              <span id="heroBadgeStatus" class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; display:inline-flex; align-items:center; gap:0.3rem;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> KUOTA PENUH</span>
            @else
              <span id="heroBadgeStatus" class="badge badge-status-open">PENDAFTARAN DIBUKA</span>
            @endif
            <span id="heroBadgePrice" class="badge {{ ($eventData['price'] ?? 0) == 0 ? 'badge-free' : 'badge-paid' }}">{{ $eventData['priceLabel'] ?? 'GRATIS' }}</span>
          </div>
          <p style="font-size: 0.95rem; letter-spacing: 0.05em; font-weight: 700; opacity: 0.95; margin-top: 0.75rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"><svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:0.4rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>Kegiatan Resmi Terverifikasi &bull; Sivitas Akademika FTTK UMRAH</p>
        </div>
      </div>

      <!-- Header Information Card -->
      <div class="detail-header-card">
        <h1 id="eventTitle" class="detail-title">Seminar Nasional AI: Peluang Karir Generative AI & Ekosistem Digital 2026</h1>
        
        <div class="detail-organizer-row">
          <div class="organizer-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <div>
              <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Penyelenggara Resmi:</span>
              <strong id="organizerName">BEM Fakultas Teknik & Teknologi Kemaritiman</strong>
            </div>
          </div>

          <div class="organizer-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <div>
              <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Layanan &amp; Bantuan:</span>
              <strong>Sekretariat Ormawa &amp; Helpdesk BAAK</strong>
            </div>
          </div>
        </div>
      </div>

      <div class="detail-insight-strip" aria-label="Ringkasan event">
        <div class="detail-insight-item">
          <strong>Format Kegiatan</strong>
          <span>Luring &bull; Tatap Muka di Kampus Dompak</span>
        </div>
        <div class="detail-insight-item">
          <strong>Bobot Poin SKP</strong>
          <span>4 Poin SKP &bull; Terhubung Otomatis ke SIPA</span>
        </div>
        <div class="detail-insight-item">
          <strong>Bukti Kelulusan</strong>
          <span>E-Sertifikat Resmi Ber-SK Dekanat FTTK</span>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div>
        <div class="detail-tabs-nav">
          <button class="tab-nav-btn active" data-target="tabDeskripsi">Deskripsi Lengkap</button>
          <button class="tab-nav-btn" data-target="tabRundown">Rundown Acara</button>
          <button class="tab-nav-btn" data-target="tabPembicara">Pembicara</button>
          <button class="tab-nav-btn" data-target="tabFasilitas">Ketentuan & Fasilitas</button>
        </div>

        <!-- Tab 1: Deskripsi -->
        <div id="tabDeskripsi" class="tab-pane active content-box">
          <h3>Tentang Kegiatan Ini</h3>
          <p id="eventDescription">
            Seminar ini mengupas tuntas transformasi industri perangkat lunak dengan hadirnya teknologi Artificial Intelligence dan Large Language Models. Mahasiswa akan mendapatkan pemahaman mendalam tentang bagaimana mempersiapkan skill masa depan yang relevan dengan kebutuhan industri digital global dan kemaritiman.
          </p>
          <p>
            Kegiatan ini sangat direkomendasikan bagi mahasiswa Program Studi Teknik Informatika, Sistem Informasi, Teknik Elektro, dan seluruh mahasiswa UMRAH yang tertarik memperluas wawasan karir di era revolusi kecerdasan buatan.
          </p>
        </div>

        <!-- Tab 2: Rundown -->
        <div id="tabRundown" class="tab-pane content-box">
          <h3>Jadwal & Susunan Acara (Rundown)</h3>
          <div class="rundown-timeline">
            <div class="rundown-item">
              <div class="rundown-time">08.30 - 09.00 WIB</div>
              <div class="rundown-desc">
                <h4>Registrasi Peserta & Presensi Masuk (Token QR)</h4>
                <p>Peserta melakukan verifikasi kehadiran melalui modul presensi TEMU UMRAH.</p>
              </div>
            </div>
            <div class="rundown-item">
              <div class="rundown-time">09.00 - 09.30 WIB</div>
              <div class="rundown-desc">
                <h4>Pembukaan & Sambutan Dekan FTTK UMRAH</h4>
                <p>Menyanyikan lagu Indonesia Raya dan sambutan pembuka kegiatan.</p>
              </div>
            </div>
            <div class="rundown-item">
              <div class="rundown-time">09.30 - 11.30 WIB</div>
              <div class="rundown-desc">
                <h4>Sesi Pemaparan Materi Inti & Studi Kasus</h4>
                <p>Paparan komprehensif implementasi teknologi dan peluang magang industri.</p>
              </div>
            </div>
            <div class="rundown-item">
              <div class="rundown-time">11.30 - 12.15 WIB</div>
              <div class="rundown-desc">
                <h4>Sesi Diskusi Interaktif & Tanya Jawab (Q&A)</h4>
                <p>Diskusi langsung antara mahasiswa dan narasumber pakar.</p>
              </div>
            </div>
            <div class="rundown-item">
              <div class="rundown-time">12.15 - 12.30 WIB</div>
              <div class="rundown-desc">
                <h4>Penutupan & Info Penerbitan E-Sertifikat</h4>
                <p>Pengumuman sertifikat digital yang dapat diunduh langsung di akun mahasiswa.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab 3: Pembicara / Instruktur -->
        <div id="tabPembicara" class="tab-pane content-box">
          <h3 id="speakerHeading">{{ ($eventData['category'] ?? '') === 'LOMBA' ? 'Dewan Juri & Tim Penilai' : (($eventData['category'] ?? '') === 'WORKSHOP' ? 'Narasumber & Instruktur Workshop' : 'Narasumber & Pembicara') }}</h3>
          <div class="speaker-card">
            <div class="speaker-avatar" id="speakerAvatar">{{ strtoupper(substr($eventData['category'] ?? 'EV', 0, 2)) }}</div>
            <div class="speaker-info">
              <h4 id="speakerName">{{ ($eventData['category'] ?? '') === 'WORKSHOP' ? 'Tim Instruktur Laboratorium Rekayasa Perangkat Lunak' : (($eventData['category'] ?? '') === 'LOMBA' ? 'Dewan Juri & Tim Penilai FTTK UMRAH' : 'Dr. Ir. Budi Santoso, M.Kom') }}</h4>
              <p id="speakerRole">{{ ($eventData['category'] ?? '') === 'WORKSHOP' ? 'Laboratorium Komputasi Terpadu FTTK Universitas Maritim Raja Ali Haji' : (($eventData['category'] ?? '') === 'LOMBA' ? 'Dosen Pembina & Pakar Pengembangan Sistem Perangkat Lunak' : 'Lead AI Researcher & Tim Konsultan Digital Kemaritiman') }}</p>
            </div>
          </div>
        </div>

        <!-- Tab 4: Fasilitas & Ketentuan -->
        <div id="tabFasilitas" class="tab-pane content-box">
          <h3>Fasilitas & Manfaat Peserta</h3>
          <div id="facilitiesContainer" class="facility-grid">
            <!-- Populated via detail.js -->
          </div>
        </div>
      </div>

    </div>

    <!-- ==================== KOLOM KANAN: STICKY REGISTRATION PANEL ==================== -->
    <aside class="detail-sidebar">
      <div class="sidebar-header">
        <h3>Ringkasan & Pendaftaran</h3>
      </div>
      
      <div class="sidebar-body">
        <!-- Price Display -->
        <div class="price-display-group">
          <span class="price-label-text">Biaya Pendaftaran:</span>
          <div id="sidebarPriceAmount" class="price-amount-text">GRATIS</div>
        </div>

        <!-- Quota Progress Box -->
        <div class="quota-box">
          <div class="quota-row">
            <span>Kuota Pendaftaran</span>
            <span id="sidebarQuotaPercent">72%</span>
          </div>
          <div class="progress-track" style="margin-bottom: 0.5rem;">
            <div id="sidebarProgressBar" class="progress-fill" style="width: 72%;"></div>
          </div>
          <div id="sidebarQuotaNumbers" style="font-size: 0.78rem; color: var(--text-muted); text-align: right;">
            145 / 200 (Sisa 55 Kursi)
          </div>
        </div>

        <!-- Key Event Meta -->
        <div class="sidebar-meta-list">
          <div class="sidebar-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <div>
              <strong style="display:block;">Tanggal Kegiatan:</strong>
              <span id="sidebarDate">Sabtu, 18 Oktober 2026</span>
            </div>
          </div>

          <div class="sidebar-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
            <div>
              <strong style="display:block;">Waktu:</strong>
              <span id="sidebarTime">08:30 - 12:30 WIB</span>
            </div>
          </div>

          <div class="sidebar-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <div>
              <strong style="display:block;">Lokasi Kampus:</strong>
              <span id="sidebarLocation">Auditorium Gedung E Lt.3, Kampus Dompak</span>
            </div>
          </div>
        </div>

        <!-- CTA & Registration Status Area -->
        @if($isRegistered ?? false)
          <div class="reg-status-box" style="background:#f0fdf4; border:1.5px solid #a7f3d0; border-radius:14px; padding:0.9rem 1.1rem; text-align:left;">
            <div style="display:flex; align-items:center; gap:0.45rem; color:#166534; font-weight:800; font-size:0.88rem; margin-bottom:0.25rem;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Anda Telah Terdaftar</span>
            </div>
            <p style="margin:0; font-size:0.78rem; color:#15803d; line-height:1.45;">
              @if(!empty($userRegistration?->nomor_tiket))
                Nomor Tiket: <strong>{{ $userRegistration->nomor_tiket }}</strong><br>
              @endif
              QR Code dan token presensi resmi dapat Anda akses pada ruang tiket mahasiswa.
            </p>
          </div>

          <a href="{{ route('peserta.events') }}" class="btn sidebar-cta-btn" style="text-decoration:none; display:flex; align-items:center; justify-content:center; gap:0.5rem; background:#10b981; border-color:#059669; color:#fff; box-shadow:0 4px 14px rgba(16, 185, 129, 0.35);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
            <span>Lihat E-Tiket Saya &rarr;</span>
          </a>
        @elseif(($eventData['quotaFilled'] ?? 0) >= ($eventData['quotaMax'] ?? 1))
          <div class="reg-status-box" style="background:#fef2f2; border:1.5px solid #fecaca; border-radius:14px; padding:0.9rem 1.1rem; text-align:left;">
            <div style="display:flex; align-items:center; gap:0.45rem; color:#991b1b; font-weight:800; font-size:0.88rem; margin-bottom:0.25rem;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span>Pendaftaran Ditutup</span>
            </div>
            <p style="margin:0; font-size:0.78rem; color:#b91c1c; line-height:1.45;">
              Kuota kursi peserta untuk kegiatan ini telah mencapai batas maksimal (penuh).
            </p>
          </div>

          <button id="registerCtaBtn" class="btn sidebar-cta-btn" disabled style="background:#94a3b8; border-color:#94a3b8; color:#ffffff; cursor:not-allowed; opacity:0.85;">
            <span>Kuota Event Penuh</span>
          </button>
        @elseif(auth()->check() && auth()->user()->role !== 'mahasiswa')
          <div class="reg-status-box" style="background:#fefce8; border:1.5px solid #fde047; border-radius:14px; padding:0.9rem 1.1rem; text-align:left;">
            <div style="display:flex; align-items:center; gap:0.45rem; color:#854d0e; font-weight:800; font-size:0.88rem; margin-bottom:0.25rem;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"/></svg>
              <span>Akun {{ ucfirst(auth()->user()->role) }}</span>
            </div>
            <p style="margin:0; font-size:0.78rem; color:#a16207; line-height:1.45;">
              Pendaftaran peserta kegiatan ini khusus untuk akun mahasiswa aktif UMRAH.
            </p>
          </div>

          <a href="{{ auth()->user()->role === 'admin' ? url('/admin/panel') : url('/panitia/dashboard') }}" class="btn btn-outline sidebar-cta-btn" style="text-decoration:none; display:flex; align-items:center; justify-content:center; gap:0.5rem;">
            <span>Buka Panel {{ ucfirst(auth()->user()->role) }} &rarr;</span>
          </a>
        @else
          <button id="registerCtaBtn" class="btn btn-gold sidebar-cta-btn">
            <span>Daftar Event Sekarang &rarr;</span>
          </button>
        @endif

        <button onclick="shareEvent()" class="btn btn-outline sidebar-share-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="18" cy="5" r="3"/>
            <circle cx="6" cy="12" r="3"/>
            <circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
          </svg>
          <span>Bagikan Informasi Event</span>
        </button>
      </div>
    </aside>

  </main>

  <!-- ==================== MODAL KONFIRMASI PENDAFTARAN ==================== -->
  <div id="regConfirmModal" class="modal-backdrop">
    <div class="modal-dialog modal-confirm-dialog">
      <div class="modal-confirm-header">
        <h3>Konfirmasi Pendaftaran Event</h3>
        <button id="modalConfirmClose" class="drawer-close-btn">&times;</button>
      </div>

      <div class="modal-confirm-body">
        <p style="font-size: 0.88rem; color: var(--text-body); margin-bottom: 1.25rem;">
          Pastikan data identitas kepesertaan Anda di bawah ini sudah sesuai sebelum menyelesaikan pendaftaran:
        </p>

        <div class="student-summary-card">
          <div class="summary-row">
            <span class="summary-label">Kegiatan</span>
            <span id="sumEventTitle" class="summary-value" style="max-width: 280px; text-align: right;">-</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Mahasiswa</span>
             <span id="sumStudentName" class="summary-value">{{ auth()->user()?->nama ?? 'Mahasiswa UMRAH' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">NIM Resmi</span>
             <span id="sumStudentNim" class="summary-value">{{ auth()->user()?->nim ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Email Kampus</span>
             <span id="sumStudentEmail" class="summary-value">{{ auth()->user()?->email ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">No. WhatsApp</span>
             <span id="sumStudentWa" class="summary-value">{{ auth()->user()?->no_wa ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Biaya Pendaftaran</span>
            <span id="sumPrice" class="summary-value" style="color: var(--umrah-blue); font-size: 0.95rem;">GRATIS</span>
          </div>
        </div>

        <label class="form-checkbox-label" style="font-size: 0.82rem; margin-bottom: 0.5rem;">
          <input type="checkbox" checked required>
          <span>Saya berkomitmen untuk hadir dan mematuhi tata tertib kegiatan panitia.</span>
        </label>
      </div>

      <div class="modal-confirm-footer">
        <button id="modalConfirmCancel" class="btn btn-outline btn-sm">Batal</button>
        <button id="confirmSubmitBtn" class="btn btn-primary btn-sm">
          <span>Ya, Daftarkan Saya Sekarang &rarr;</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Official UMRAH Footer -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/detail.js') }}"></script>
@endpush
