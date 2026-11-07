<!-- resources/views/pages/penerbitan_sertifikat.blade.php -->
@extends('layouts.app')

@section('title', 'Penerbitan E-Sertifikat Canva - TEMU UMRAH')
@section('meta_desc', 'Unggah berkas e-sertifikat karya desain Canva (format NIM_Nama.pdf) untuk peserta hadir sah.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/panitia.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/penerbitan_sertifikat.css') }}">
@endpush

@section('content')
<main class="terbit-wrapper">
  <div class="container">
    
    <!-- Breadcrumb Navigasi SIPA UMRAH -->
    <nav class="breadcrumb-wrapper">
      <a href="{{ url('/panitia/dashboard') }}">Home</a>
      <span class="breadcrumb-separator">/</span>
      <span>{{ $event->judul }}</span>
      <span class="breadcrumb-separator">/</span>
      <span class="breadcrumb-current">Penerbitan E-Sertifikat Canva</span>
    </nav>

    <!-- Header Halaman Bersih & Tombol Aksi Cepat (SIPA UMRAH Style) -->
    <div class="sipa-dashboard-header">
      <div>
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom: 0.35rem; flex-wrap:wrap;">
          <span class="badge badge-gold" style="font-size:0.75rem;">Format Berkas: PDF / Canva (NIM_Nama.pdf)</span>
          <span class="badge badge-primary" style="font-size:0.75rem;">Peserta Hadir Sah: {{ $hadirCount }} Mahasiswa</span>
        </div>
        <h1 class="sipa-page-title">Penerbitan E-Sertifikat: {{ $event->judul }}</h1>
        <p class="sipa-page-subtitle">Penyelenggara: {{ optional($event->ormawa)->nama_ormawa ?? 'Panitia Penyelenggara' }} &bull; Pelaksanaan: {{ optional($event->tanggal_kegiatan)->translatedFormat('d F Y') }}</p>
      </div>
      <div style="display:flex; align-items:center; gap:0.6rem; flex-wrap:wrap;">
        <a href="{{ url('/panitia/event/' . $eventId . '/peserta') }}" class="btn btn-outline btn-sm" style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; font-weight:700;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
          <span>Data Peserta &amp; Presensi</span>
        </a>
      </div>
    </div>

    <!-- 4 KPI Overview Cards (SIPA UMRAH Style) -->
    <div class="sipa-kpi-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background:#e0f2fe; color:#0284c7;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value">{{ $hadirCount }}</div>
          <div class="sipa-kpi-label">Peserta Hadir Sah</div>
        </div>
      </div>

      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background:#dcfce7; color:#16a34a;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value text-success">{{ $terbitCount }}</div>
          <div class="sipa-kpi-label">Sertifikat Terbit</div>
        </div>
      </div>

      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background:#fef3c7; color:#d97706;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value text-warning">{{ $belumCount }}</div>
          <div class="sipa-kpi-label">Belum Ada Berkas</div>
        </div>
      </div>

      <div class="sipa-kpi-card">
        <div class="sipa-kpi-icon" style="background:#eff6ff; color:#2563eb;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
        </div>
        <div>
          <div class="sipa-kpi-value" style="font-size:1.15rem; font-weight:800; color:#1e40af;">NIM_Nama.pdf</div>
          <div class="sipa-kpi-label">Standar Berkas Canva</div>
        </div>
      </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
      <div class="alert-popup alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 0.85rem; background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #065f46;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span style="font-weight: 600;">{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="alert-popup alert-danger" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 0.85rem; background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span style="font-weight: 600;">{{ session('error') }}</span>
      </div>
    @endif

    <!-- Panduan Praktis Alur Canva (3 Langkah Cepat) -->
    <div class="canva-guide-strip" style="margin-top: 1rem;">
        <div class="canva-guide-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
          <span>Alur Penerbitan Sertifikat Desain Canva ke Portal TEMU UMRAH:</span>
        </div>
        <div class="canva-guide-steps">
          <div class="canva-step-card">
            <div class="canva-step-num">1</div>
            <h4>Desain Sertifikat di Canva</h4>
            <p>Divisi Pubdok mendesain sertifikat lengkap dengan nama, NIM, dan tanda tangan peserta (dapat menggunakan fitur <em>Bulk Create</em> Canva).</p>
          </div>
          <div class="canva-step-card">
            <div class="canva-step-num">2</div>
            <h4>Ekspor Format NIM_Nama.pdf</h4>
            <p>Unduh file PDF dengan format penamaan memuat NIM peserta, contoh: <strong><code>2401020073_Nabil.pdf</code></strong> atau <strong><code>2401020110_Alman.pdf</code></strong>.</p>
          </div>
          <div class="canva-step-card">
            <div class="canva-step-num">3</div>
            <h4>Unggah &amp; Sinkronkan Otomatis</h4>
            <p>Pilih atau seret seluruh berkas PDF di bawah ini (bisa banyak berkas sekaligus atau arsip .zip). Sistem otomatis mendeteksi NIM dan menerbitkan ke akun mahasiswa.</p>
          </div>
        </div>
      </div>

      <!-- Area Unggah Berkas Canva (Dropzone Multi-Upload) -->
      <div class="terbit-card" style="margin-bottom: 2rem;">
        <div class="terbit-card-header">
          <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span>FORMULIR UNGGAH BERKAS E-SERTIFIKAT CANVA (MASSAL / ZIP)</span>
          </h2>
          <span style="font-size:0.75rem; background:rgba(255,255,255,0.2); padding:0.25rem 0.6rem; border-radius:999px;">Multi-Upload Aktif</span>
        </div>

        <div class="terbit-card-body">
          <form action="{{ route('panitia.upload_bulk_canva', $event->id) }}" method="POST" enctype="multipart/form-data" id="formCanvaBulk">
            @csrf

            <!-- Dashed Dropzone -->
            <div class="canva-dropzone" id="canvaDropzone" onclick="document.getElementById('canvaFilesInput').click();">
              <div class="canva-dropzone-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              </div>
              <div class="canva-dropzone-title">Tarik &amp; Lepaskan Berkas PDF Canva ke Sini, atau Klik untuk Memilih</div>
              <div class="canva-dropzone-subtitle">Dapat memilih banyak berkas PDF sekaligus (Multi-Select) atau unggah satu file arsip .ZIP</div>
              <div class="canva-dropzone-formats">Format yang didukung: .PDF, .ZIP, .PNG, .JPG (Maksimal 20 MB per berkas)</div>

              <input type="file" name="files[]" id="canvaFilesInput" multiple accept=".pdf,.png,.jpg,.jpeg,.zip" style="display:none;" onchange="handleCanvaFilesSelected(this.files)">
            </div>

            <!-- Live Selected Files Matcher Preview Box -->
            <div class="canva-selected-box" id="canvaSelectedBox">
              <div class="canva-selected-header">
                <div>
                  <strong style="color:#0f172a; font-size:0.88rem;">Berkas Terdeteksi: <span id="canvaSelectedCount">0</span> File</strong>
                  <div style="font-size:0.75rem; color:#64748b;">Sistem telah mencocokkan NIM pada nama berkas dengan peserta hadir di bawah ini:</div>
                </div>
                <button type="button" class="btn btn-outline btn-sm" onclick="clearCanvaSelection()" style="font-size:0.75rem; padding:0.25rem 0.6rem;">
                  Batal / Pilih Ulang
                </button>
              </div>

              <div class="canva-file-list" id="canvaFileList">
                <!-- Rendered dynamically by JS -->
              </div>
            </div>

            <!-- Tombol Submit Massal -->
            <button type="submit" class="btn-submit-canva" id="btnSubmitCanva">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>UNGGAH &amp; TERBITKAN E-SERTIFIKAT CANVA SEKARANG</span>
            </button>
          </form>
        </div>
      </div>

      <!-- Tabel Status Rekapitulasi Sertifikat Peserta Hadir -->
      <div class="sipa-table-container">
        <div style="padding:1rem 1.5rem; background:#ffffff; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
          <div>
            <h3 style="font-size:0.95rem; font-weight:800; color:#0f172a; margin:0 0 0.2rem;">Daftar Peserta Hadir Sah &amp; Berkas Sertifikat</h3>
            <p style="font-size:0.78rem; color:#64748b; margin:0;">Memastikan setiap mahasiswa yang hadir sah menerima berkas sertifikat resmi dari panitia.</p>
          </div>
          <div style="display:flex; align-items:center; gap:0.5rem;">
            <span class="badge badge-success" style="font-size:0.75rem;">{{ $terbitCount }} dari {{ $hadirCount }} Sudah Ada Berkas</span>
          </div>
        </div>

        <table class="sipa-data-table">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">No</th>
              <th>Mahasiswa Peserta</th>
              <th>Status Presensi</th>
              <th>Status E-Sertifikat</th>
              <th>Berkas Tersimpan</th>
              <th style="width: 170px; text-align: center;">Aksi Berkas</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pesertaHadirList as $idx => $p)
              @php
                $cert = $p->certificate;
                $hasFile = $cert && $cert->file_sertifikat_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($cert->file_sertifikat_path);
                $ext = $hasFile ? strtoupper(pathinfo($cert->file_sertifikat_path, PATHINFO_EXTENSION)) : null;
              @endphp
              <tr>
                <td style="text-align: center; color: #64748b; font-weight: 600;">{{ $idx + 1 }}</td>
                <td>
                  <strong style="color: #0f172a; display: block; font-size: 0.88rem;">{{ $p->user->nama ?? 'Peserta' }}</strong>
                  <span style="font-size: 0.75rem; color: #64748b;">NIM: {{ $p->user->nim ?? '-' }}</span>
                </td>
                <td>
                  <span class="badge badge-success" style="font-size: 0.75rem;">
                    Hadir Sah
                  </span>
                </td>
                <td>
                  @if($hasFile)
                    <span class="canva-match-badge-success">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                      <span>File Canva Terpasang ({{ $ext }})</span>
                    </span>
                  @else
                    <span class="canva-match-badge-warning">
                      <span>Belum Ada Berkas</span>
                    </span>
                  @endif
                </td>
                <td>
                  @if($hasFile)
                    <a href="{{ asset('storage/' . $cert->file_sertifikat_path) }}" target="_blank" style="font-size:0.78rem; color:#0284c7; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:0.3rem;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      <span>Lihat / Unduh Dokumen</span>
                    </a>
                  @else
                    <span style="color:#94a3b8; font-size:0.78rem;">-</span>
                  @endif
                </td>
                <td style="text-align: center;">
                  <!-- Form Unggah Individu jika panitia hanya ingin revisi 1 mahasiswa -->
                  <form action="{{ route('panitia.upload_file_sertifikat', $event->id) }}" method="POST" enctype="multipart/form-data" style="margin:0;">
                    @csrf
                    <input type="hidden" name="registration_id" value="{{ $p->id }}">
                    <label class="btn btn-outline btn-sm" style="cursor:pointer; font-size:0.75rem; padding:0.35rem 0.65rem; display:inline-flex; align-items:center; gap:0.3rem;" title="Unggah / ganti berkas Canva untuk {{ $p->user->nama }}">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                      <span>{{ $hasFile ? 'Ganti File' : 'Unggah File' }}</span>
                      <input type="file" name="file_sertifikat" accept=".pdf,.png,.jpg,.jpeg" style="display:none;" onchange="this.form.submit();">
                    </label>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">
                  Belum ada peserta dengan status kehadiran 'Hadir Sah'.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="sipa-table-footer">
          <span id="footerCounter">Menampilkan {{ $hadirCount }} peserta hadir sah</span>
          <div style="display:flex; gap:0.5rem; align-items:center;">
            <a href="{{ url('/sertifikat') }}" target="_blank" class="btn btn-outline btn-sm" style="font-size:0.8rem; font-weight:700;">
              Pratinjau di Sisi Mahasiswa &rarr;
            </a>
          </div>
        </div>
      </div>

  </div>
</main>
@endsection

@push('scripts')
<script>
  // Data Peserta Hadir untuk Pencocokan Realtime di Sisi Klien
  window.REGISTERED_PARTICIPANTS = {!! $participantsJson !!};

  // Drag and Drop Handling untuk Canva Dropzone
  const dropzone = document.getElementById('canvaDropzone');
  const filesInput = document.getElementById('canvaFilesInput');

  if (dropzone) {
    ['dragenter', 'dragover'].forEach(eventName => {
      dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('dragover');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('dragover');
      }, false);
    });

    dropzone.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;
      if (files && files.length > 0) {
        filesInput.files = files;
        handleCanvaFilesSelected(files);
      }
    }, false);
  }

  // Handler Pencocokan Berkas Canva (Format NIM_Nama.pdf)
  function handleCanvaFilesSelected(files) {
    const box = document.getElementById('canvaSelectedBox');
    const list = document.getElementById('canvaFileList');
    const countEl = document.getElementById('canvaSelectedCount');

    if (!files || files.length === 0) {
      box.style.display = 'none';
      return;
    }

    list.innerHTML = '';
    countEl.textContent = files.length;
    box.style.display = 'block';

    const participants = window.REGISTERED_PARTICIPANTS || [];

    Array.from(files).forEach((file, idx) => {
      const filename = file.name;
      let matchedParticipant = null;

      // 1. Ekstrak NIM pola angka 8-15 digit
      const numMatch = filename.match(/(\d{8,15})/);
      if (numMatch) {
        const nimCand = numMatch[1];
        matchedParticipant = participants.find(p => p.nim === nimCand);
      }

      // 2. Fallback cek apakah ada NIM peserta yang terkandung di nama file
      if (!matchedParticipant) {
        matchedParticipant = participants.find(p => p.nim && filename.includes(p.nim));
      }

      const item = document.createElement('div');
      item.className = 'canva-file-item';

      if (matchedParticipant) {
        item.innerHTML = `
          <div style="display:flex; align-items:center; gap:0.5rem;">
            <span style="font-weight:700; color:#0f172a;">${idx + 1}. ${escapeHtml(filename)}</span>
            <span style="font-size:0.72rem; color:#64748b;">(${(file.size / 1024).toFixed(1)} KB)</span>
          </div>
          <div>
            <span class="canva-match-badge-success">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Cocok: ${escapeHtml(matchedParticipant.nama)} (${matchedParticipant.nim})</span>
            </span>
          </div>
        `;
      } else {
        item.innerHTML = `
          <div style="display:flex; align-items:center; gap:0.5rem;">
            <span style="font-weight:600; color:#475569;">${idx + 1}. ${escapeHtml(filename)}</span>
            <span style="font-size:0.72rem; color:#64748b;">(${(file.size / 1024).toFixed(1)} KB)</span>
          </div>
          <div>
            <span class="canva-match-badge-warning">
              <span>NIM Tidak Ditemukan Pada Peserta Hadir</span>
            </span>
          </div>
        `;
      }

      list.appendChild(item);
    });
  }

  function clearCanvaSelection() {
    const filesInput = document.getElementById('canvaFilesInput');
    const box = document.getElementById('canvaSelectedBox');
    if (filesInput) filesInput.value = '';
    if (box) box.style.display = 'none';
  }

  function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  }
</script>
@endpush
