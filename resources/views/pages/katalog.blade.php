<!-- resources/views/pages/katalog.blade.php -->
@extends('layouts.app')

@section('title', 'TEMU UMRAH - Katalog & Portal Event Mahasiswa')
@section('meta_desc', 'Temukan seminar, workshop, kompetisi, dan kegiatan kampus terkini di Universitas Maritim Raja Ali Haji.')
@section('body_class', 'home-body')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_SERVER_EVENTS = @json($events);
</script>
@php
  $featuredEvent = $events->first();
  $nextEvent = $events->get(1) ?? $featuredEvent;
  $featuredTitle = $featuredEvent['title'] ?? 'Kegiatan mahasiswa UMRAH';
  $nextTitle = $nextEvent['title'] ?? 'Event mahasiswa UMRAH';
  $featuredDate = $featuredEvent['date'] ?? 'Segera hadir';
  $nextDate = $nextEvent['date'] ?? 'Jadwal tersedia di katalog';
  $featuredLocation = $featuredEvent['location'] ?? 'Kampus UMRAH';
@endphp
<main class="temu-home">
  <section class="home-hero" aria-labelledby="homeHeroTitle">
    <div class="container hero-container">
      <div class="home-hero-panel">
        <!-- Decorative cloud accents -->
        <div class="hero-cloud hero-cloud-1" aria-hidden="true"></div>
        <div class="hero-cloud hero-cloud-2" aria-hidden="true"></div>
        <div class="hero-cloud hero-cloud-3" aria-hidden="true"></div>

        <div class="home-hero-copy">
          <span class="home-eyebrow">Portal Resmi Kegiatan Sivitas UMRAH</span>
          <h1 id="homeHeroTitle" class="home-hero-title">
            Kegiatan Kampus Lebih Simpel, Pintar &amp; Terhubung
          </h1>
          <p class="home-hero-subtitle">
            Satu aplikasi terpadu untuk menjelajah seminar, kompetisi, presensi QR SIPA, hingga e-sertifikat resmi BAAK dalam genggaman.
          </p>

          <!-- Quick Search Bar & Category Pills (Sangat Interaktif untuk Mahasiswa) -->
          <div class="home-hero-search-wrap">
            <form action="{{ route('katalog.jelajah') }}" method="GET" class="home-hero-search-form">
              <div class="hero-search-input-box">
                <svg class="hero-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="q" class="hero-search-input" placeholder="Cari seminar, workshop, ormawa, atau topik..." autocomplete="off">
              </div>
              <button type="submit" class="hero-search-submit">
                <span>Cari Event</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
              </button>
            </form>

            <div class="hero-quick-tags">
              <span class="quick-tags-label">Kategori Populer:</span>
              <a href="{{ route('katalog.jelajah', ['category' => 'WORKSHOP']) }}" class="quick-tag-pill">Workshop</a>
              <a href="{{ route('katalog.jelajah', ['category' => 'SEMINAR']) }}" class="quick-tag-pill">Seminar</a>
              <a href="{{ route('katalog.jelajah', ['category' => 'LOMBA']) }}" class="quick-tag-pill">Lomba</a>
              <a href="{{ route('katalog.jelajah', ['category' => 'BAZAR']) }}" class="quick-tag-pill">Bazar</a>
              <a href="{{ route('katalog.jelajah') }}" class="quick-tag-pill quick-tag-all">Lihat Semua &rarr;</a>
            </div>
          </div>
        </div>

        <!-- UMRAH Campus Panoramic Backdrop (Full Bleed) -->
        <div class="home-hero-backdrop" aria-hidden="true">
          <img class="home-hero-illustration" src="{{ asset('assets/illustrations/umrah-campus-hero.png') }}" alt="Ilustrasi Kampus UMRAH Dompak (Gedung Satu Gurindam & Rektorat)">
        </div>

        <div class="home-hero-stage" aria-label="Pratinjau aplikasi TEMU UMRAH">

          <!-- Left Floating Cards -->
          <div class="home-floating-card home-floating-card--left-top">
            <div class="floating-badge-icon icon-blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m8 15 2 2 5-5"/></svg>
            </div>
            <div class="floating-badge-text">
              <div class="floating-badge-header">
                <strong>E-Tiket &amp; Presensi QR</strong>
                <span class="status-pill status-pill--success">Optimal</span>
              </div>
              <small>Tersimpan aman di akun SIPA</small>
            </div>
          </div>

          <div class="home-floating-card home-floating-card--left-bottom">
            <div class="floating-badge-icon icon-yellow">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="floating-badge-text">
              <div class="floating-badge-header">
                <strong>Pengingat Jadwal</strong>
                <span class="status-pill status-pill--warning">H-1 Event</span>
              </div>
              <small>Notifikasi otomatis masuk ke email</small>
            </div>
          </div>

          <!-- Right Floating Cards -->
          <div class="home-floating-card home-floating-card--right-top">
            <div class="floating-badge-icon icon-green">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 4 7v5c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V7l-8-4Z"/><path d="m8.5 12 2.2 2.2 4.8-5"/></svg>
            </div>
            <div class="floating-badge-text">
              <div class="floating-badge-header">
                <strong>E-Sertifikat BAAK</strong>
                <span class="status-pill status-pill--primary">Resmi</span>
              </div>
              <small>Verifikasi nomor sertifikat digital</small>
            </div>
          </div>

          <div class="home-floating-card home-floating-card--right-bottom">
            <div class="floating-badge-icon icon-coral">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 10v11M15 10v11M4 10l8-6 8 6"/></svg>
            </div>
            <div class="floating-badge-text">
              <div class="floating-badge-header">
                <strong>Legalitas Ormawa</strong>
                <span class="status-pill status-pill--purple">SK Aktif</span>
              </div>
              <small>BEM · HMTI · UKM Terdaftar</small>
            </div>
          </div>

          <!-- Center Smartphone Mockup -->
          <div class="home-phone home-phone--hero">
            <div class="phone-dynamic-island">
              <div class="island-camera"></div>
              <div class="island-sensor"></div>
            </div>
            <div class="home-phone-screen">
              <!-- Phone Status Bar -->
              <div class="phone-status-bar">
                <span class="phone-time">09:41</span>
                <div class="phone-status-icons">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L12 22l7.03-4.39C20.26 16.07 21 14.12 21 12c0-4.97-4.03-9-9-9z"/></svg>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.66-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/></svg>
                  <svg width="15" height="12" viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="7" width="18" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><rect x="4" y="9" width="12" height="6" fill="currentColor"/><path d="M22 11v2"/></svg>
                </div>
              </div>

              <!-- Inside App Content -->
              <div class="phone-app-inner">
                <!-- User greeting row -->
                <div class="phone-profile-row">
                  <div class="phone-user-info">
                    <div class="phone-avatar">{{ strtoupper(substr(auth()->user()?->nama ?? 'M', 0, 1)) }}</div>
                    <div>
                      <div class="phone-user-name">{{ auth()->user()?->nama ?? 'Mahasiswa UMRAH' }}</div>
                      <div class="phone-user-nim">NIM: {{ auth()->user()?->nim ?? '2401020073' }}</div>
                    </div>
                  </div>
                  <div class="phone-notif-bell">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span class="bell-dot"></span>
                  </div>
                </div>

                <!-- Two Metric Chips like AgroPulse -->
                <div class="phone-stat-grid">
                  <div class="phone-mini-stat">
                    <div class="stat-top"><span class="dot-live"></span><small>PRESENSI</small></div>
                    <strong>Hadir SIPA</strong>
                  </div>
                  <div class="phone-mini-stat">
                    <div class="stat-top"><span class="dot-verified"></span><small>SERTIFIKAT</small></div>
                    <strong>Siap Unduh</strong>
                  </div>
                </div>

                <!-- Quick actions section -->
                <div class="phone-section-label">Aksi Cepat</div>
                <div class="phone-action-card">
                  <div class="action-card-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m8 15 2 2 5-5"/></svg>
                  </div>
                  <div class="action-card-text">
                    <strong>Tiket Event Terdaftar</strong>
                    <small>QR Code siap di lokasi</small>
                  </div>
                  <span class="action-pill">Aktif</span>
                </div>

                <!-- Featured Event card -->
                <div class="phone-section-label">Event Terdekat</div>
                <div class="phone-event-spotlight">
                  <div class="spotlight-tag">SEMINAR UNGGULAN</div>
                  <div class="spotlight-title">{{ Str::limit($featuredTitle, 32) }}</div>
                  <div class="spotlight-meta">
                    <span style="display:inline-flex;align-items:center;gap:3px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> {{ $featuredDate }}</span>
                    <span style="display:inline-flex;align-items:center;gap:3px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ Str::limit($featuredLocation, 18) }}</span>
                  </div>
                </div>
              </div>

              <!-- Phone Bottom Dock -->
              <div class="phone-bottom-dock">
                <div class="dock-item active"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></div>
                <div class="dock-item"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
                <div class="dock-item"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg></div>
                <div class="dock-item"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Partner Strip -->
  <div class="home-partner-wrapper">
    <div class="container">
      <div class="home-partner-strip" aria-label="Ekosistem TEMU UMRAH">
        <span class="home-partner-intro">DIPERGUNAKAN RESMI OLEH SIVITAS AKADEMIKA:</span>
        <div class="home-partner-logos">
          <span class="home-partner-chip"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg> UMRAH</span>
          <span class="home-partner-chip"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> BAAK</span>
          <span class="home-partner-chip"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> BEM FTTK</span>
          <span class="home-partner-chip"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="4" y="4" width="16" height="16" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg> HMTI</span>
          <span class="home-partner-chip"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> DPM UMRAH</span>
          <span class="home-partner-chip"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> ORMAWA</span>
        </div>
      </div>
    </div>
  </div>

  <section id="cara-kerja" class="home-section home-features-section">
    <div class="container">
      <div class="home-section-heading">
        <span class="feature-section-pill">Fitur Unggulan Mahasiswa</span>
        <h2 class="home-section-title">Semua Kebutuhan Kampus,<br>Terhubung di Satu Portal.</h2>
        <p class="home-section-copy">Dari pencarian kegiatan, pendaftaran instan, presensi QR SIPA, hingga klaim sertifikat resmi BAAK—seluruh aktivitas berjalan otomatis.</p>
      </div>

      <div class="home-feature-grid">
        <!-- Card 1: Presensi Cepat QR SIPA -->
        <article class="home-feature-card home-feature-card--mint">
          <div class="home-feature-visual">
            <svg viewBox="0 0 400 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="feature-svg">
              <defs>
                <linearGradient id="mintGrad" x1="0" y1="0" x2="400" y2="240" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#bbf7d0"/>
                  <stop offset="0.6" stop-color="#86efac"/>
                  <stop offset="1" stop-color="#4ade80"/>
                </linearGradient>
                <linearGradient id="phoneCardGrad" x1="0" y1="0" x2="180" y2="200" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#ffffff"/>
                  <stop offset="1" stop-color="#f8fafc"/>
                </linearGradient>
              </defs>
              <rect width="400" height="240" rx="20" fill="url(#mintGrad)"/>
              <circle cx="80" cy="50" r="60" fill="#ffffff" fill-opacity="0.25"/>
              <circle cx="340" cy="190" r="80" fill="#ffffff" fill-opacity="0.25"/>
              <path d="M0 200c60-15 120-10 180 5s120 15 220-10v45H0z" fill="#15803d" fill-opacity="0.15"/>
              <path d="M0 215c70-10 140-5 210 10s110 5 190-8v23H0z" fill="#166534" fill-opacity="0.25"/>

              <!-- Phone with QR Presensi -->
              <g transform="translate(130, 24)">
                <rect x="5" y="10" width="130" height="195" rx="24" fill="#064e3b" fill-opacity="0.22" filter="blur(6px)"/>
                <rect x="0" y="0" width="140" height="200" rx="24" fill="#0f172a"/>
                <rect x="6" y="6" width="128" height="188" rx="18" fill="url(#phoneCardGrad)"/>
                <rect x="46" y="10" width="48" height="11" rx="5.5" fill="#000000"/>
                <rect x="22" y="32" width="96" height="96" rx="14" fill="#ffffff" stroke="#e2e8f0" stroke-width="1.5"/>
                <g fill="#0f172a">
                  <rect x="32" y="42" width="22" height="22" rx="4"/>
                  <rect x="36" y="46" width="14" height="14" rx="2" fill="#ffffff"/>
                  <rect x="40" y="50" width="6" height="6" rx="1"/>
                  <rect x="86" y="42" width="22" height="22" rx="4"/>
                  <rect x="90" y="46" width="14" height="14" rx="2" fill="#ffffff"/>
                  <rect x="94" y="50" width="6" height="6" rx="1"/>
                  <rect x="32" y="96" width="22" height="22" rx="4"/>
                  <rect x="36" y="100" width="14" height="14" rx="2" fill="#ffffff"/>
                  <rect x="40" y="104" width="6" height="6" rx="1"/>
                  <rect x="62" y="46" width="6" height="12" rx="2"/>
                  <rect x="74" y="46" width="6" height="6" rx="2"/>
                  <rect x="62" y="66" width="16" height="6" rx="2"/>
                  <rect x="84" y="72" width="6" height="14" rx="2"/>
                  <rect x="60" y="80" width="8" height="8" rx="2"/>
                  <rect x="74" y="84" width="14" height="6" rx="2"/>
                  <rect x="64" y="100" width="12" height="14" rx="2"/>
                  <rect x="84" y="98" width="16" height="6" rx="2"/>
                  <rect x="94" y="108" width="6" height="8" rx="2"/>
                </g>
                <line x1="26" y1="80" x2="114" y2="80" stroke="#22c55e" stroke-width="3" stroke-linecap="round"/>
                <rect x="24" y="138" width="92" height="24" rx="12" fill="#dcfce7"/>
                <circle cx="36" cy="150" r="4" fill="#16a34a"/>
                <rect x="46" y="146" width="58" height="8" rx="4" fill="#15803d"/>
                <rect x="36" y="172" width="68" height="6" rx="3" fill="#94a3b8"/>
              </g>

              <!-- Floating Success Badge -->
              <g transform="translate(36, 95)">
                <rect width="86" height="38" rx="14" fill="#ffffff" filter="drop-shadow(0 6px 12px rgba(0,0,0,0.08))"/>
                <circle cx="22" cy="19" r="10" fill="#22c55e"/>
                <path d="M18 19l3 3 6-6" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="38" y="13" width="36" height="5" rx="2.5" fill="#0f172a"/>
                <rect x="38" y="21" width="24" height="4" rx="2" fill="#16a34a"/>
              </g>
            </svg>
          </div>
          <div class="home-feature-content">
            <div class="feature-tag-row">
              <span class="feature-badge feature-badge--mint">Presensi SIPA</span>
            </div>
            <h3>Presensi &amp; E-Tiket Real-Time</h3>
            <p>E-tiket tersimpan otomatis dengan QR Code unik. Lakukan absensi dalam hitungan detik tanpa kertas dan langsung tersinkron ke portal SIPA.</p>
          </div>
        </article>

        <!-- Card 2: Katalog Pintar & Rekomendasi -->
        <article class="home-feature-card home-feature-card--blue">
          <div class="home-feature-visual">
            <svg viewBox="0 0 400 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="feature-svg">
              <defs>
                <linearGradient id="blueGrad" x1="0" y1="0" x2="400" y2="240" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#bae6fd"/>
                  <stop offset="0.6" stop-color="#7dd3fc"/>
                  <stop offset="1" stop-color="#38bdf8"/>
                </linearGradient>
              </defs>
              <rect width="400" height="240" rx="20" fill="url(#blueGrad)"/>
              <circle cx="60" cy="180" r="70" fill="#ffffff" fill-opacity="0.25"/>
              <circle cx="340" cy="60" r="65" fill="#ffffff" fill-opacity="0.25"/>
              <path d="M0 195c80-15 150-10 210 10s100 10 190-12v47H0z" fill="#0284c7" fill-opacity="0.18"/>
              <path d="M0 215c90-8 160-5 230 8s90 5 170-6v23H0z" fill="#0369a1" fill-opacity="0.25"/>

              <!-- Event Discovery Cards Deck -->
              <g transform="translate(105, 26)">
                <rect x="25" y="0" width="160" height="150" rx="18" fill="#e0f2fe" stroke="#bae6fd" stroke-width="1.5" transform="rotate(6 105 75)" opacity="0.85"/>
                <rect x="10" y="5" width="165" height="155" rx="18" fill="#ffffff" opacity="0.92" transform="rotate(-4 92 82)"/>
                
                <!-- Main Top Card -->
                <g filter="drop-shadow(0 14px 28px rgba(3, 105, 161, 0.22))">
                  <rect x="15" y="15" width="170" height="162" rx="18" fill="#ffffff"/>
                  <rect x="25" y="25" width="150" height="66" rx="12" fill="#0284c7"/>
                  <circle cx="150" cy="45" r="24" fill="#38bdf8" opacity="0.5"/>
                  <path d="M25 70l40-25 45 20 45-25v25H25z" fill="#0369a1" opacity="0.6"/>
                  
                  <rect x="33" y="33" width="54" height="15" rx="7.5" fill="#ffffff"/>
                  <rect x="39" y="38" width="42" height="5" rx="2.5" fill="#0284c7"/>
                  
                  <rect x="25" y="102" width="120" height="10" rx="5" fill="#0f172a"/>
                  <rect x="25" y="118" width="85" height="7" rx="3.5" fill="#64748b"/>
                  
                  <g transform="translate(25, 134)">
                    <rect width="68" height="18" rx="9" fill="#f0fdf4"/>
                    <rect x="8" y="6" width="52" height="6" rx="3" fill="#16a34a"/>
                    
                    <rect x="78" y="0" width="66" height="18" rx="9" fill="#eff6ff"/>
                    <rect x="86" y="6" width="50" height="6" rx="3" fill="#2563eb"/>
                  </g>
                </g>
              </g>

              <g transform="translate(32, 60)">
                <circle cx="20" cy="20" r="18" fill="#ffffff" filter="drop-shadow(0 4px 10px rgba(0,0,0,0.08))"/>
                <path d="M20 10l3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z" fill="#eab308"/>
              </g>
              <g transform="translate(315, 115)">
                <circle cx="20" cy="20" r="18" fill="#ffffff" filter="drop-shadow(0 4px 10px rgba(0,0,0,0.08))"/>
                <path d="M20 12a4 4 0 00-4 4v3l-2 2h12l-2-2v-3a4 4 0 00-4-4zM18 23a2 2 0 004 0" stroke="#0284c7" stroke-width="2" stroke-linecap="round"/>
              </g>
            </svg>
          </div>
          <div class="home-feature-content">
            <div class="feature-tag-row">
              <span class="feature-badge feature-badge--blue">Katalog Terpadu</span>
            </div>
            <h3>Jelajah &amp; Rekomendasi Event</h3>
            <p>Temukan seminar, kompetisi, dan workshop akademik sesuai minatmu. Kuota transparan dan pengingat jadwal otomatis sebelum acara dimulai.</p>
          </div>
        </article>

        <!-- Card 3: E-Sertifikat Terverifikasi BAAK -->
        <article class="home-feature-card home-feature-card--amber">
          <div class="home-feature-visual">
            <svg viewBox="0 0 400 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="feature-svg">
              <defs>
                <linearGradient id="amberGrad" x1="0" y1="0" x2="400" y2="240" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#fed7aa"/>
                  <stop offset="0.6" stop-color="#fdba74"/>
                  <stop offset="1" stop-color="#fb923c"/>
                </linearGradient>
              </defs>
              <rect width="400" height="240" rx="20" fill="url(#amberGrad)"/>
              <circle cx="70" cy="60" r="65" fill="#ffffff" fill-opacity="0.25"/>
              <circle cx="340" cy="180" r="85" fill="#ffffff" fill-opacity="0.25"/>
              <path d="M0 200c70-15 140-8 200 8s120 12 200-10v42H0z" fill="#c2410c" fill-opacity="0.15"/>
              <path d="M0 216c80-8 150-4 220 7s100 4 180-6v23H0z" fill="#9a3412" fill-opacity="0.22"/>

              <!-- Certificate Paper with Golden Wax Seal -->
              <g transform="translate(110, 22)">
                <rect x="8" y="10" width="164" height="178" rx="16" fill="#7c2d12" fill-opacity="0.2" filter="blur(6px)"/>
                <rect x="0" y="0" width="180" height="188" rx="16" fill="#ffffff"/>
                <rect x="8" y="8" width="164" height="172" rx="10" fill="none" stroke="#fed7aa" stroke-width="2" stroke-dasharray="4 3"/>
                
                <circle cx="90" cy="30" r="14" fill="#eff6ff"/>
                <path d="M82 30l8-5 8 5-8 5z" fill="#2563eb"/>
                <path d="M90 35v6" stroke="#2563eb" stroke-width="1.5"/>

                <rect x="50" y="52" width="80" height="7" rx="3.5" fill="#0f172a"/>
                <rect x="62" y="64" width="56" height="5" rx="2.5" fill="#94a3b8"/>
                
                <rect x="35" y="80" width="110" height="10" rx="5" fill="#2563eb"/>
                <rect x="42" y="97" width="96" height="5" rx="2.5" fill="#64748b"/>
                <rect x="52" y="107" width="76" height="5" rx="2.5" fill="#94a3b8"/>

                <rect x="25" y="130" width="40" height="4" rx="2" fill="#cbd5e1"/>
                <rect x="115" y="130" width="40" height="4" rx="2" fill="#cbd5e1"/>
                
                <!-- Golden Wax Seal with Ribbon -->
                <g transform="translate(72, 120)">
                  <path d="M12 24l-6 18 12-4 12 4-6-18z" fill="#d97706"/>
                  <circle cx="18" cy="18" r="16" fill="#f59e0b" filter="drop-shadow(0 3px 6px rgba(180, 83, 9, 0.35))"/>
                  <circle cx="18" cy="18" r="12" fill="#fbbf24"/>
                  <path d="M18 11l2 4 4.5.5-3.5 3 1 4.5-4-2.5-4 2.5 1-4.5-3.5-3 4.5-.5z" fill="#ffffff"/>
                </g>

                <rect x="24" y="146" width="22" height="22" rx="4" fill="#f8fafc" stroke="#e2e8f0"/>
                <g fill="#0f172a">
                  <rect x="27" y="149" width="5" height="5"/>
                  <rect x="38" y="149" width="5" height="5"/>
                  <rect x="27" y="160" width="5" height="5"/>
                  <rect x="35" y="157" width="4" height="4"/>
                </g>
              </g>

              <g transform="translate(36, 110)">
                <rect width="86" height="38" rx="14" fill="#ffffff" filter="drop-shadow(0 6px 14px rgba(0,0,0,0.08))"/>
                <circle cx="22" cy="19" r="10" fill="#3b82f6"/>
                <path d="M18 19l3 3 6-6" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="38" y="13" width="36" height="5" rx="2.5" fill="#0f172a"/>
                <rect x="38" y="21" width="26" height="4" rx="2" fill="#2563eb"/>
              </g>
            </svg>
          </div>
          <div class="home-feature-content">
            <div class="feature-tag-row">
              <span class="feature-badge feature-badge--amber">Legalitas BAAK</span>
            </div>
            <h3>E-Sertifikat Terverifikasi</h3>
            <p>Selesai mengikuti kegiatan, sertifikat digital langsung terbit dengan nomor registrasi unik yang dapat divalidasi keabsahannya secara online.</p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section id="katalogArea" class="home-section home-catalog-section">
    <div class="container">
      <div class="home-catalog-header" style="align-items: flex-end; margin-bottom: 2rem;">
        <div class="home-section-heading" style="margin-bottom: 0;">
          <span class="catalog-eyebrow">KEGIATAN UNGGULAN KAMPUS</span>
          <h2 class="home-section-title">Event Pilihan &amp; Mendatang</h2>
          <p class="home-section-copy">Sorotan kegiatan pilihan sivitas akademika UMRAH untuk meningkatkan kompetensi dan SKP Anda.</p>
        </div>
        <div class="home-catalog-action">
          <a href="{{ route('katalog.jelajah') }}" class="home-button home-button-ghost" style="white-space: nowrap;">
            <span>Lihat Semua Event ({{ $totalEvents ?? 6 }})</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
        </div>
      </div>

      <!-- Grid 3 Event Pilihan Saja -->
      <div class="events-grid home-events-grid">
        @foreach($featuredEvents as $ev)
          @php
            $isFree = ($ev['price'] == 0);
            $quotaPct = round(($ev['quotaFilled'] / $ev['quotaMax']) * 100);
            $isAlmostFull = ($quotaPct >= 80);
          @endphp
          <article class="event-card">
            <div class="card-banner-wrapper" style="background: {{ $ev['bannerGradient'] }}; position: relative; overflow: hidden;">
              @if(!empty($ev['bannerPath']))
                <img src="{{ $ev['bannerPath'] }}" alt="{{ $ev['title'] }}" class="card-banner-img" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit: cover;">
              @else
                <div class="card-banner-art" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; opacity: 0.28;">
                  <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                  </svg>
                </div>
              @endif
              <div class="card-banner-badges">
                <span class="badge badge-category">{{ $ev['category'] }}</span>
                <span class="badge {{ $isFree ? 'badge-free' : 'badge-paid' }}">{{ $ev['priceLabel'] }}</span>
              </div>
            </div>

            <div class="card-content">
              <div class="card-ormawa">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span>{{ $ev['ormawa'] }}</span>
              </div>

              <h3 class="card-title" title="{{ $ev['title'] }}">
                <a href="{{ route('event.detail', ['slug' => $ev['slug']]) }}" style="color: inherit; text-decoration: none;">
                  {{ $ev['title'] }}
                </a>
              </h3>

              <ul class="card-meta-list">
                <li class="card-meta-item">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                  <span>{{ $ev['date'] }} &bull; {{ $ev['time'] }}</span>
                </li>
                <li class="card-meta-item">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                  </svg>
                  <span>{{ $ev['location'] }}</span>
                </li>
              </ul>

              <div class="card-quota-bar">
                <div class="quota-label-row">
                  <span>Kuota Terisi</span>
                  <span>{{ $ev['quotaFilled'] }} / {{ $ev['quotaMax'] }} Peserta ({{ $quotaPct }}%)</span>
                </div>
                <div class="progress-track">
                  <div class="progress-fill {{ $isAlmostFull ? 'almost-full' : '' }}" style="width: {{ $quotaPct }}%"></div>
                </div>
              </div>

              <div class="card-footer-cta">
                <a href="{{ route('event.detail', ['slug' => $ev['slug']]) }}" class="btn btn-outline btn-sm btn-detail" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                  Lihat Detail
                </a>
                <a href="{{ route('event.detail', ['slug' => $ev['slug']]) }}" class="btn btn-primary btn-sm btn-register" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                  Daftar
                </a>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="home-faq-section" id="faqSection">
    <div class="container home-faq-layout">
      <div class="home-faq-heading">
        <span class="faq-eyebrow">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          Pertanyaan Umum
        </span>
        <h2 class="faq-title">Punya Pertanyaan Seputar TEMU UMRAH?</h2>
        <p class="faq-desc">Semua jawaban penting seputar alur pendaftaran event, e-tiket QR, verifikasi sertifikat, hingga tata cara pengajuan event ormawa.</p>
        
        <div class="faq-contact-card">
          <div class="faq-contact-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
          </div>
          <div>
            <span class="faq-contact-label">Butuh panduan lebih lanjut?</span>
            <span class="faq-contact-link">Hubungi Helpdesk Kemahasiswaan BAAK</span>
          </div>
        </div>
      </div>

      <div class="home-faq-list">
        <details class="faq-item" open>
          <summary class="faq-summary">
            <span>Siapa saja yang bisa menggunakan layanan TEMU UMRAH?</span>
            <span class="faq-toggle-icon"></span>
          </summary>
          <div class="faq-content">
            <p>Seluruh mahasiswa aktif Universitas Maritim Raja Ali Haji (UMRAH) dapat login langsung menggunakan NIM dan password akun kampus. Organisasi mahasiswa (BEM, DPM, HIMA, dan UKM) yang telah terverifikasi juga dapat mengakses portal panitia untuk mempublikasikan dan mengelola kegiatan resmi kampus.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">
            <span>Apakah semua event di TEMU UMRAH dapat diikuti secara gratis?</span>
            <span class="faq-toggle-icon"></span>
          </summary>
          <div class="faq-content">
            <p>Sebagian besar seminar dan kegiatan ormawa bersifat <strong>Gratis</strong> bagi mahasiswa. Namun, untuk beberapa workshop bersertifikasi kompetensi atau lomba tertentu yang berbayar, informasi biaya pendaftaran ditampilkan secara transparan beserta instruksi pembayaran resminya.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">
            <span>Bagaimana cara mengakses E-Tiket dan presensi QR di lokasi acara?</span>
            <span class="faq-toggle-icon"></span>
          </summary>
          <div class="faq-content">
            <p>Setelah berhasil mendaftar, e-tiket digital otomatis tersedia di menu <strong>Event Saya</strong>. Di hari kegiatan, cukup tunjukkan barcode/QR code dari layar smartphone Anda kepada panitia untuk dipindai secara real-time tanpa perlu cetak kertas.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">
            <span>Kapan dan di mana saya bisa mengunduh E-Sertifikat kegiatan?</span>
            <span class="faq-toggle-icon"></span>
          </summary>
          <div class="faq-content">
            <p>E-Sertifikat resmi bertanda tangan digital dan nomor verifikasi unik akan diterbitkan oleh panitia setelah presensi selesai diverifikasi. Anda dapat langsung mengunduhnya dalam format PDF berkualitas tinggi melalui tab <strong>E-Sertifikat</strong> di profil Anda.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">
            <span>Bagaimana cara organisasi mahasiswa mengajukan dan menerbitkan event?</span>
            <span class="faq-toggle-icon"></span>
          </summary>
          <div class="faq-content">
            <p>Pengurus ormawa dapat masuk ke dashboard <strong>Panitia Ormawa</strong>, mengisi detail kegiatan (deskripsi, pemateri, kuota, tanggal, dan poster), lalu mengirimkannya untuk ditinjau oleh Admin BAAK sebelum secara resmi ditampilkan pada katalog publik.</p>
          </div>
        </details>
      </div>
    </div>
  </section>
</main>
@endsection
