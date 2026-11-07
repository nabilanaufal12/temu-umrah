{{-- resources/views/pages/pembayaran.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment Gateway & Checkout Tiket - TEMU UMRAH')
@section('meta_desc', 'Gerbang pembayaran otomatis terintegrasi QRIS dan Virtual Account untuk pendaftaran kegiatan mahasiswa.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pembayaran.css') }}">
@endpush

@section('content')
<script>
  window.TEMU_PAYMENT_EVENT = @json($eventData ?? null);
  window.TEMU_PAYMENT_USER = @json(auth()->user());
  window.TEMU_PAYMENT_SETTLE_URL = @json(route('pembayaran.settle'));
</script>
<main class="pembayaran-wrapper">
  <div class="container">
    <!-- Countdown Urgent Timer Banner -->
    <div class="countdown-banner">
      <div class="countdown-left">
        <div class="countdown-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="countdown-text">
          <h4>Batas Waktu Transaksi Pembayaran</h4>
          <p>Selesaikan transaksi sebelum sesi kedaluwarsa untuk mengamankan kuota pendaftaran Anda.</p>
        </div>
      </div>

      <div class="countdown-timer-boxes">
        <div id="timerHours" class="timer-box">00</div>
        <span class="timer-colon">:</span>
        <div id="timerMins" class="timer-box">14</div>
        <span class="timer-colon">:</span>
        <div id="timerSecs" class="timer-box">59</div>
      </div>
    </div>

    <section class="payment-steps" aria-label="Tahapan pembayaran">
      <div class="payment-step"><span class="portal-step-number">01</span><strong>Pilih kanal</strong><span>QRIS, Virtual Account, atau e-wallet.</span></div>
      <div class="payment-step"><span class="portal-step-number">02</span><strong>Selesaikan transaksi</strong><span>Periksa nama merchant dan nominal tagihan.</span></div>
      <div class="payment-step"><span class="portal-step-number">03</span><strong>Tiket terbit otomatis</strong><span>Status settlement mengaktifkan akses event.</span></div>
    </section>

    <!-- 2-Column Payment Gateway Layout -->
    <main class="payment-grid">
      
      <!-- ==================== KOLOM KIRI: RINCIAN TAGIHAN & ORDER ==================== -->
      <section class="payment-card-left">
        
        <!-- Invoice Summary Box -->
        <div class="invoice-summary-box">
          <div class="invoice-header-row">
            <div class="invoice-title-group">
              <span class="invoice-number-tag" id="invOrderTag">ORDER ID: #ORD-2026-0891</span>
              <h3>Rincian Tagihan Tiket</h3>
            </div>
            <span id="invStatusBadge" class="invoice-status-badge">Menunggu Pembayaran</span>
          </div>

          <div class="invoice-meta-list">
            <div class="meta-row">
              <span>Kegiatan Mahasiswa:</span>
              <span id="invEventTitle">Workshop UI/UX Product Design with Figma</span>
            </div>
            <div class="meta-row">
              <span>Penyelenggara:</span>
              <span id="invOrganizer">HMTI FTTK UMRAH</span>
            </div>
            <div class="meta-row">
              <span>Nama Peserta (SIPA):</span>
               <span id="invStudentName">{{ auth()->user()->nama ?? 'Mahasiswa UMRAH' }} (NIM: {{ auth()->user()->nim ?? '-' }})</span>
            </div>
            <div class="meta-row">
              <span>Email Notifikasi:</span>
               <span id="invStudentEmail">{{ auth()->user()->email ?? '-' }}</span>
            </div>
            <div class="meta-row">
              <span>Biaya Pendaftaran:</span>
              <span id="invTicketPrice">Rp 35.000,-</span>
            </div>
            <div class="meta-row">
              <span>Biaya Layanan Gateway:</span>
              <span style="color: #059669; font-weight: 800;">Gratis (Subsidi UMRAH)</span>
            </div>
          </div>

          <!-- Total Highlight -->
          <div class="total-transfer-highlight">
            <span>Total Tagihan Resmi:</span>
            <h2 id="invTotalAmount">Rp 35.000,-</h2>
          </div>

          <p style="font-size: 0.78rem; color: var(--text-muted); text-align: center; margin: 0;">
            *Status pembayaran akan diverifikasi secara otomatis oleh sistem payment gateway tanpa perlu mengirim bukti transfer manual.
          </p>
        </div>

        <!-- Security & Compliance Card -->
        <div class="gateway-security-card">
          <h5>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#005696" stroke-width="2.2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Jaminan Transaksi Resmi & Aman
          </h5>
          <p>
            Sistem pembayaran terintegrasi dengan Payment Gateway standar Bank Indonesia (QRIS) dan perbankan nasional. Enkripsi SSL 256-bit menjamin keamanan data dan validasi transaksi seketika.
          </p>
        </div>

      </section>

      <!-- ==================== KOLOM KANAN: PAYMENT GATEWAY CHANNELS ==================== -->
      <section class="gateway-portal-card">
        <div class="gateway-portal-header">
          <div class="gateway-badge-live">
            <span class="pulse-dot"></span>
            <span>TEMU UMRAH PAYMENT GATEWAY</span>
          </div>
          <h3>Pilih Kanal Pembayaran Digital</h3>
          <p>Pilih salah satu metode pembayaran di bawah ini untuk menyelesaikan pendaftaran.</p>
        </div>

        <!-- Channel Navigation Tabs -->
        <div class="payment-channel-nav">
          <button type="button" class="channel-tab-btn active" onclick="switchChannel('qris', this)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span>QRIS Dinamis</span>
          </button>
          
          <button type="button" class="channel-tab-btn" onclick="switchChannel('va', this)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="5" width="20" height="14" rx="2"/>
              <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
            <span>Virtual Account</span>
          </button>

          <button type="button" class="channel-tab-btn" onclick="switchChannel('ewallet', this)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="5" y="2" width="14" height="20" rx="2"/>
              <line x1="12" y1="18" x2="12.01" y2="18"/>
            </svg>
            <span>E-Wallet</span>
          </button>
        </div>

        <!-- PANEL 1: QRIS DINAMIS (DEFAULT) -->
        <div id="panel-qris" class="channel-panel active">
          <div class="qris-display-card">
            <span class="qris-merchant-badge">MERCHANT RESMI: TEMU UMRAH KEMAHASISWAAN</span>
            <span class="qris-nmid">NMID: ID102026UMRAH01 • STANDAR ASPI / BANK INDONESIA</span>
            
            <div class="qris-qr-container">
              <svg width="180" height="180" viewBox="0 0 120 120" fill="#002847">
                <rect width="120" height="120" fill="#FFFFFF"/>
                <rect x="8" y="8" width="30" height="30" rx="4" fill="#002847"/>
                <rect x="12" y="12" width="22" height="22" rx="2" fill="#FFFFFF"/>
                <rect x="16" y="16" width="14" height="14" rx="2" fill="#002847"/>
                
                <rect x="82" y="8" width="30" height="30" rx="4" fill="#002847"/>
                <rect x="86" y="12" width="22" height="22" rx="2" fill="#FFFFFF"/>
                <rect x="90" y="16" width="14" height="14" rx="2" fill="#002847"/>
                
                <rect x="8" y="82" width="30" height="30" rx="4" fill="#002847"/>
                <rect x="12" y="86" width="22" height="22" rx="2" fill="#FFFFFF"/>
                <rect x="16" y="90" width="14" height="14" rx="2" fill="#002847"/>

                <rect x="46" y="10" width="8" height="8" fill="#002847"/>
                <rect x="58" y="14" width="10" height="8" fill="#002847"/>
                <rect x="72" y="10" width="6" height="12" fill="#002847"/>
                <rect x="44" y="24" width="12" height="6" fill="#002847"/>
                <rect x="62" y="26" width="14" height="6" fill="#002847"/>

                <rect x="10" y="44" width="8" height="12" fill="#002847"/>
                <rect x="24" y="46" width="14" height="8" fill="#002847"/>
                <rect x="10" y="62" width="12" height="8" fill="#002847"/>
                <rect x="28" y="60" width="10" height="14" fill="#002847"/>

                <rect x="46" y="46" width="28" height="28" rx="6" fill="#005696"/>
                <circle cx="60" cy="60" r="10" fill="#F1A80A"/>
                <circle cx="60" cy="60" r="5" fill="#FFFFFF"/>

                <rect x="44" y="82" width="8" height="14" fill="#002847"/>
                <rect x="56" y="86" width="14" height="8" fill="#002847"/>
                <rect x="46" y="102" width="18" height="8" fill="#002847"/>
                <rect x="82" y="44" width="14" height="8" fill="#002847"/>
                <rect x="102" y="46" width="8" height="14" fill="#002847"/>
                <rect x="84" y="60" width="10" height="12" fill="#002847"/>
                <rect x="100" y="64" width="10" height="10" fill="#002847"/>
                <rect x="82" y="82" width="12" height="8" fill="#002847"/>
                <rect x="98" y="86" width="12" height="8" fill="#002847"/>
                <rect x="84" y="98" width="26" height="12" fill="#002847"/>
              </svg>
            </div>

            <div style="font-size: 0.85rem; font-weight: 700; color: var(--umrah-navy);">
              Total Tagihan: <span id="qrisAmountLabel" style="color: var(--umrah-blue);">Rp 35.000,-</span>
            </div>

            <div class="qris-supported-logos">
              <span>BCA Mobile</span>
              <span>Livin' Mandiri</span>
              <span>BRImo</span>
              <span>BNI Mobile</span>
              <span>GoPay</span>
              <span>OVO</span>
              <span>Dana</span>
              <span>ShopeePay</span>
            </div>
          </div>

          <ol style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.6; margin: 0 0 1.5rem 1.25rem; padding: 0;">
            <li>Buka aplikasi Mobile Banking atau E-Wallet pilihan Anda.</li>
            <li>Pilih menu <strong>Bayar / Scan QRIS</strong> dan arahkan kamera ke kode di atas.</li>
            <li>Periksa nama merchant (<strong>TEMU UMRAH</strong>) dan konfirmasi pembayaran.</li>
          </ol>

          <button type="button" class="btn-gateway-trigger" id="btnPayQRIS" onclick="executeGatewayPayment('qris')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
            </svg>
            <span id="labelPayQRIS">Konfirmasi Pelunasan &amp; Terbitkan E-Tiket</span>
          </button>
        </div>

        <!-- PANEL 2: VIRTUAL ACCOUNT BANK -->
        <div id="panel-va" class="channel-panel">
          <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
            Pilih bank tujuan untuk mendapatkan nomor Virtual Account resmi pendaftaran Anda:
          </p>

          <div class="va-selector-list">
             <div class="va-item-option selected" onclick="selectVA('BCA', '8801200000000', this)">
              <div>
                <div class="va-bank-title">BCA Virtual Account</div>
                 <div class="va-number-display">88012 0000000</div>
              </div>
               <button type="button" class="va-copy-btn" onclick="copyVAText('8801200000000', event)">Salin Nomor</button>
            </div>

             <div class="va-item-option" onclick="selectVA('Mandiri', '8930100000000', this)">
              <div>
                <div class="va-bank-title">Mandiri Virtual Account (Bill)</div>
                 <div class="va-number-display">89301 0000000</div>
              </div>
               <button type="button" class="va-copy-btn" onclick="copyVAText('8930100000000', event)">Salin Nomor</button>
            </div>

             <div class="va-item-option" onclick="selectVA('BNI', '9880100000000', this)">
              <div>
                <div class="va-bank-title">BNI Virtual Account</div>
                 <div class="va-number-display">98801 0000000</div>
              </div>
               <button type="button" class="va-copy-btn" onclick="copyVAText('9880100000000', event)">Salin Nomor</button>
            </div>

             <div class="va-item-option" onclick="selectVA('BRI', '1020100000000', this)">
              <div>
                <div class="va-bank-title">BRI Virtual Account (BRIVA)</div>
                 <div class="va-number-display">10201 0000000</div>
              </div>
               <button type="button" class="va-copy-btn" onclick="copyVAText('1020100000000', event)">Salin Nomor</button>
            </div>
          </div>

          <ol style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.6; margin: 0 0 1.5rem 1.25rem; padding: 0;">
            <li>Masukkan kode Virtual Account di atas pada menu transfer virtual account m-banking/ATM.</li>
            <li>Nominal tagihan (<span id="vaAmountLabel" style="font-weight:700; color:var(--umrah-navy);">Rp 35.000,-</span>) akan otomatis terkunci.</li>
            <li>Sistem payment gateway akan memvalidasi pelunasan dalam hitungan detik.</li>
          </ol>

          <button type="button" class="btn-gateway-trigger" id="btnPayVA" onclick="executeGatewayPayment('virtual_account')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
            </svg>
            <span id="labelPayVA">Konfirmasi Pelunasan Virtual Account</span>
          </button>
        </div>

        <!-- PANEL 3: E-WALLET DIRECT -->
        <div id="panel-ewallet" class="channel-panel">
          <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
            Pembayaran langsung via aplikasi e-wallet terdaftar:
          </p>

          <div style="background: #FAFBFD; border: 1px solid var(--border-light); border-radius: var(--radius-lg, 12px); padding: 1.25rem; margin-bottom: 1.5rem;">
            <div class="form-group" style="margin-bottom: 1rem;">
              <label class="form-label" for="ewalletChoice">Pilih Penyedia E-Wallet:</label>
              <select id="ewalletChoice" class="form-input" style="padding-left: 0.75rem;">
                <option value="GoPay">GoPay (GoTo Financial)</option>
                <option value="ShopeePay">ShopeePay</option>
                <option value="OVO">OVO</option>
                <option value="Dana">DANA Indonesia</option>
              </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="ewalletPhone">Nomor WhatsApp / E-Wallet Terdaftar:</label>
               <input type="text" id="ewalletPhone" class="form-input" style="padding-left: 0.75rem;" value="{{ auth()->user()?->no_wa ?? '' }}" placeholder="08xxxxxxxxxx">
            </div>
          </div>

          <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.5rem;">
            Permintaan pembayaran akan dikirimkan langsung ke aplikasi e-wallet pada nomor yang Anda cantumkan di atas.
          </p>

          <button type="button" class="btn-gateway-trigger" id="btnPayEwallet" onclick="executeGatewayPayment('ewallet')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
            </svg>
            <span id="labelPayEwallet">Konfirmasi Pelunasan E-Wallet</span>
          </button>
        </div>

      </section>

    </main>
  </div>
</main>

<!-- Digital Receipt Modal -->
<div id="receiptModal" class="receipt-modal-backdrop">
  <div class="receipt-modal-card">
    <div class="receipt-success-badge">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>

    <h3>Pembayaran Berhasil Terverifikasi!</h3>
    <p class="subtitle">
      Payment Gateway telah mengonfirmasi pelunasan transaksi Anda secara realtime. E-Tiket kepesertaan Anda telah resmi terbit.
    </p>

    <div class="receipt-details-box">
      <div class="receipt-row">
        <span>Order ID:</span>
        <span id="rcptOrderId">#ORD-2026-0891</span>
      </div>
      <div class="receipt-row">
        <span>No. Transaksi Gateway:</span>
        <span id="rcptTrxId" style="color: var(--umrah-blue); font-family: monospace;">TRX-MIDTRANS-89211</span>
      </div>
      <div class="receipt-row">
        <span>Kegiatan:</span>
        <span id="rcptEventTitle">Workshop UI/UX Product Design</span>
      </div>
      <div class="receipt-row">
        <span>Metode Pembayaran:</span>
        <span id="rcptChannel">QRIS Dinamis (Bank Indonesia)</span>
      </div>
      <div class="receipt-row">
        <span>Jumlah Pelunasan:</span>
        <span id="rcptAmount" style="color: #059669;">Rp 35.000,-</span>
      </div>
      <div class="receipt-row">
        <span>Status Gateway:</span>
        <span style="color: #059669; font-weight: 800;">SETTLEMENT / LUNAS</span>
      </div>
      <div class="receipt-row">
        <span>Waktu Penyelesaian:</span>
        <span id="rcptTimestamp">19 Oktober 2026, 20:45 WIB</span>
      </div>
    </div>

    <div style="display: flex; gap: 0.85rem; flex-direction: column;">
      <a href="{{ url('/event-saya') }}" class="btn btn-primary btn-block" style="padding: 0.85rem 1.25rem; font-weight: 800; text-align: center; text-decoration: none;">
        <span>Buka E-Tiket di Halaman Event Saya &rarr;</span>
      </a>
      <a href="{{ url('/') }}" class="btn btn-outline btn-block" style="padding: 0.75rem 1.25rem; font-weight: 700; text-align: center; text-decoration: none;">
        <span>Kembali ke Katalog Event</span>
      </a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/pembayaran.js') }}"></script>
@endpush
