<!-- resources/views/pages/jelajah_event.blade.php -->
@extends('layouts.app')

@section('title', 'Jelajah Event - Katalog Kegiatan Mahasiswa TEMU UMRAH')
@section('meta_desc', 'Eksplorasi seluruh kegiatan seminar, workshop, kompetisi, dan festival seni ormawa di Universitas Maritim Raja Ali Haji.')
@section('body_class', 'home-body jelajah-body')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
<style>
  .jelajah-page-header {
    background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 7.5rem 0 2.25rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
  }
  .jelajah-page-header::before {
    content: '';
    position: absolute;
    top: -50px;
    right: 5%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(2, 132, 199, 0.08) 0%, transparent 70%);
    pointer-events: none;
  }
  .jelajah-hero-title {
    font-size: 2.35rem;
    font-weight: 850;
    color: #002B49;
    letter-spacing: -0.03em;
    line-height: 1.2;
    margin: 0.4rem 0 0.75rem;
  }
  .jelajah-hero-copy {
    font-size: 1.05rem;
    color: #475569;
    max-width: 680px;
    line-height: 1.55;
    margin: 0;
  }
  .jelajah-badge-count {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: #002B49;
    color: #38bdf8;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    margin-bottom: 0.8rem;
  }
  .jelajah-header-meta {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
  }
  .jelajah-live-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 0.3rem 0.85rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #15803d;
  }
  .jelajah-live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
  }
  .jelajah-unified-search {
    margin-bottom: 1.25rem;
  }
  .jelajah-counter-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.15rem;
    margin-bottom: 0.35rem;
  }
  .jelajah-counter-text {
    font-size: 0.88rem;
    color: #475569;
    font-weight: 700;
  }
  .chip-btn-reset {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #fff1f2 !important;
    color: #e11d48 !important;
    border-color: #fecdd3 !important;
    font-weight: 700 !important;
    text-decoration: none;
    padding: 0.4rem 0.85rem;
    border-radius: 999px;
    font-size: 0.78rem;
    transition: all 0.2s ease;
  }
  .chip-btn-reset:hover {
    background: #ffe4e6 !important;
    color: #be123c !important;
    transform: translateY(-1px);
  }
  .empty-search-state {
    grid-column: 1 / -1;
    background: #ffffff;
    border: 1.5px dashed #cbd5e1;
    border-radius: 24px;
    padding: 4rem 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
  }
  .empty-search-icon {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
@endpush

@section('content')
<script>
  window.TEMU_SERVER_EVENTS = @json($events);
</script>

<main class="temu-jelajah">
  <!-- Header Page Hero (Satu Header Utama yang Bersih) -->
  <section class="jelajah-page-header">
    <div class="container">
      <div>
        <div class="jelajah-badge-count">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span>Portal Eksplorasi Resmi UMRAH</span>
        </div>
        <h1 class="jelajah-hero-title">Jelajah Semua Kegiatan Kampus</h1>
        <p class="jelajah-hero-copy">
          Temukan dan ikuti seminar, workshop teknologi, kompetisi kemaritiman, dan bazar ormawa resmi di lingkungan Universitas Maritim Raja Ali Haji.
        </p>
        <div class="jelajah-header-meta">
          <span class="jelajah-live-pill">
            <span class="jelajah-live-dot"></span>
            <span id="headerTotalEventsText">{{ $categoryCounts['SEMUA'] ?? count($events) }} Kegiatan Aktif &bull; Terverifikasi SIPA</span>
          </span>
        </div>
      </div>
    </div>
  </section>

  <!-- Katalog & Filter Section -->
  <section id="katalogArea" class="home-section" style="padding-top: 0; padding-bottom: 4rem;">
    <div class="container">
      
      <!-- Unified Search & Filter Panel (Satu Baris Rapi & Efisien) -->
      <form class="home-search-panel jelajah-unified-search" action="{{ route('katalog.jelajah') }}" method="GET">
        <div class="home-search-input">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="11" cy="11" r="7.5"/><path d="m20 20-3.5-3.5"/></svg>
          <input type="text" id="heroSearchInput" name="q" placeholder="Cari seminar, kompetisi, ormawa, atau topik..." value="{{ $query ?? '' }}" autocomplete="off" aria-label="Cari event">
        </div>

        <div class="home-search-select" title="Filter Biaya">
          <select id="heroFeeSelect" name="fee">
            <option value="ALL" {{ ($fee ?? 'ALL') === 'ALL' ? 'selected' : '' }}>Semua Biaya</option>
            <option value="FREE" {{ ($fee ?? '') === 'FREE' ? 'selected' : '' }}>Gratis (Free)</option>
            <option value="PAID" {{ ($fee ?? '') === 'PAID' ? 'selected' : '' }}>Berbayar</option>
          </select>
        </div>

        <div class="home-search-select" title="Urutkan Kegiatan">
          <select id="sortSelect" name="sort">
            <option value="newest" {{ ($sort ?? '') === 'newest' ? 'selected' : '' }}>Paling Baru</option>
            <option value="quota" {{ ($sort ?? '') === 'quota' ? 'selected' : '' }}>Kuota Hampir Penuh</option>
            <option value="title_asc" {{ ($sort ?? '') === 'title_asc' ? 'selected' : '' }}>Judul A–Z</option>
            <option value="price_asc" {{ ($sort ?? '') === 'price_asc' ? 'selected' : '' }}>Biaya Terendah</option>
          </select>
        </div>

        <button type="submit" id="heroSearchBtn" class="home-button home-button-ink">Cari Event</button>
      </form>

      <!-- Category Filter Chips dengan Ikon Visual & Counter Real-Time -->
      <div class="home-category-toolbar">
        <div class="home-category-row" aria-label="Filter kategori event">
          <button type="button" class="chip-btn {{ ($category ?? 'SEMUA') === 'SEMUA' ? 'active' : '' }}" data-category="SEMUA">
            Semua <span class="chip-count">{{ $categoryCounts['SEMUA'] ?? count($events) }}</span>
          </button>
          <button type="button" class="chip-btn {{ ($category ?? '') === 'SEMINAR' ? 'active' : '' }}" data-category="SEMINAR">
            Seminar <span class="chip-count">{{ $categoryCounts['SEMINAR'] ?? 2 }}</span>
          </button>
          <button type="button" class="chip-btn {{ ($category ?? '') === 'WORKSHOP' ? 'active' : '' }}" data-category="WORKSHOP">
            Workshop <span class="chip-count">{{ $categoryCounts['WORKSHOP'] ?? 2 }}</span>
          </button>
          <button type="button" class="chip-btn {{ ($category ?? '') === 'LOMBA' ? 'active' : '' }}" data-category="LOMBA">
            Lomba <span class="chip-count">{{ $categoryCounts['LOMBA'] ?? 1 }}</span>
          </button>
          <button type="button" class="chip-btn {{ ($category ?? '') === 'BAZAR' ? 'active' : '' }}" data-category="BAZAR">
            Bazar <span class="chip-count">{{ $categoryCounts['BAZAR'] ?? 1 }}</span>
          </button>

          @if(!empty($query) || (!empty($category) && $category !== 'SEMUA') || (!empty($fee) && $fee !== 'ALL'))
            <a href="{{ route('katalog.jelajah') }}" class="chip-btn-reset" title="Hapus semua filter aktif">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              <span>Reset Filter</span>
            </a>
          @endif
        </div>

        <!-- Result Info Strip -->
        <div class="jelajah-counter-strip">
          <span id="eventCounterText" class="jelajah-counter-text">Menampilkan {{ count($events) }} kegiatan aktif</span>
        </div>
      </div>

      <!-- Events Grid Container -->
      <div id="eventsGrid" class="events-grid home-events-grid">
        @forelse($events as $ev)
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
        @empty
          <div class="empty-search-state">
            <div class="empty-search-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><line x1="8" y1="8" x2="14" y2="14"/><line x1="14" y1="8" x2="8" y2="14"/></svg>
            </div>
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #002B49;">Tidak Ada Kegiatan yang Cocok</h3>
            <p style="margin: 0; color: #64748b; font-size: 0.95rem; max-width: 480px;">
              Tidak ditemukan kegiatan kampus yang sesuai dengan kata kunci atau filter yang Anda pilih. Silakan atur ulang filter pencarian Anda.
            </p>
            <a href="{{ route('katalog.jelajah') }}" class="home-button home-button-primary" style="margin-top: 0.5rem;">
              <span>Reset Filter Pencarian</span>
            </a>
          </div>
        @endforelse
      </div>
    </div>
  </section>
</main>
@endsection
