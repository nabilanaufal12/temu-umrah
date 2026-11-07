<!-- resources/views/components/footer.blade.php -->
<footer class="footer-campus-unified">
  <div class="container">
    <div class="footer-campus-grid">
      <!-- Kolom 1: Brand & Identitas Kampus -->
      <div class="footer-campus-brand">
        <a href="{{ url('/') }}" class="footer-brand-header" style="text-decoration: none;">
          <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" width="44" height="44" alt="Logo TEMU UMRAH" class="footer-brand-logo">
          <div class="footer-brand-titles">
            <h3 class="footer-brand-name">TEMU UMRAH</h3>
            <span class="footer-brand-tagline">Portal Event &amp; Prestasi Mahasiswa</span>
          </div>
        </a>
        <p class="footer-brand-desc">
          Sistem Informasi Pengelolaan &amp; Partisipasi Event Mahasiswa terintegrasi di lingkungan Universitas Maritim Raja Ali Haji (UMRAH), Tanjungpinang, Kepulauan Riau.
        </p>
        <div class="footer-badge-pill">
          <span class="footer-pulse-dot"></span>
          <span>Sistem Resmi Sivitas Akademika UMRAH</span>
        </div>
        <div class="footer-social-row">
          <a href="https://umrah.ac.id" target="_blank" rel="noopener" class="footer-social-btn" title="Website Resmi UMRAH" aria-label="Website UMRAH">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          </a>
          <a href="https://instagram.com/universitasmaritimrajaalihaji" target="_blank" rel="noopener" class="footer-social-btn" title="Instagram Resmi UMRAH" aria-label="Instagram UMRAH">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
          <a href="mailto:ftk@umrah.ac.id" class="footer-social-btn" title="Email Resmi Fakultas" aria-label="Email Fakultas">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </a>
        </div>
      </div>

      <!-- Kolom 2: Kategori Event -->
      <div class="footer-campus-col">
        <h4 class="footer-col-title">Kategori Event</h4>
        <ul class="footer-campus-links">
          <li><a href="{{ url('/jelajah-event?category=SEMINAR') }}">Seminar &amp; Konferensi</a></li>
          <li><a href="{{ url('/jelajah-event?category=WORKSHOP') }}">Workshop &amp; Pelatihan</a></li>
          <li><a href="{{ url('/jelajah-event?category=LOMBA') }}">Kompetisi &amp; Lomba</a></li>
          <li><a href="{{ url('/jelajah-event?category=BAZAR') }}">Bazar &amp; Pameran Seni</a></li>
          <li><a href="{{ url('/jelajah-event?q=Maritim') }}">Kemaritiman &amp; Riset</a></li>
        </ul>
      </div>

      <!-- Kolom 3: Akses Cepat Pengguna -->
      <div class="footer-campus-col">
        <h4 class="footer-col-title">Akses Pengguna</h4>
        <ul class="footer-campus-links">
          <li><a href="{{ url('/jelajah-event') }}">Jelajah Katalog Event</a></li>
          <li><a href="{{ url('/event-saya') }}">Tiket &amp; Event Saya</a></li>
          <li><a href="{{ url('/presensi') }}">Presensi Cepat QR</a></li>
          <li><a href="{{ url('/sertifikat') }}">Koleksi E-Sertifikat</a></li>
          <li><a href="{{ url('/profil') }}">Pengaturan Akun</a></li>
          <li><a href="{{ url('/notifikasi') }}">Pusat Notifikasi</a></li>
        </ul>
      </div>

      <!-- Kolom 4: Kontak & Kampus -->
      <div class="footer-campus-col">
        <h4 class="footer-col-title">Kontak &amp; Kampus</h4>
        <ul class="footer-campus-links footer-campus-contact">
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>Kampus Utama Dompak, Tanjungpinang, Kepulauan Riau</span>
          </li>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Fakultas Teknik &amp; Teknologi Kemaritiman (FTTK)</span>
          </li>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <span>Email: ftk@umrah.ac.id</span>
          </li>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Pelayanan: Senin – Jumat (08:00 – 16:00 WIB)</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Baris Bawah Footer yang Rapi & Seimbang -->
    <div class="footer-campus-bottom">
      <div class="footer-bottom-copy">
        <span>&copy; {{ date('Y') }} Universitas Maritim Raja Ali Haji (UMRAH). Seluruh Hak Cipta Dilindungi.</span>
      </div>
      <div class="footer-bottom-credits">
        <span>Sistem Informasi Event &amp; Partisipasi Kemahasiswaan &bull; FTTK UMRAH</span>
      </div>
    </div>
  </div>
</footer>

<!-- Tombol Mengambang Bulat Kembali ke Atas (Floating Circular Back to Top) -->
<button type="button" id="floatingBackToTop" class="floating-back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'});" aria-label="Kembali ke atas" title="Kembali ke atas">
  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="18 15 12 9 6 15"></polyline>
  </svg>
</button>

<script>
  (function() {
    const btn = document.getElementById('floatingBackToTop');
    if (!btn) return;
    function toggleBackToTop() {
      if (window.scrollY > 280) {
        btn.classList.add('visible');
      } else {
        btn.classList.remove('visible');
      }
    }
    window.addEventListener('scroll', toggleBackToTop, { passive: true });
    toggleBackToTop();
  })();
</script>
