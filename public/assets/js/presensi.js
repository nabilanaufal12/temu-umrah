// ==========================================================================
// TEMU UMRAH - MODERN PRESENSI MAHASISWA CLIENT LOGIC
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  const tokenInputs = document.querySelectorAll('.token-box-input');
  const tokenContainer = document.getElementById('tokenBoxesContainer');
  const errorAlert = document.getElementById('tokenErrorMessage');
  const submitBtn = document.getElementById('submitTokenBtn');
  const presensiGrid = document.getElementById('presensiGridSection');
  const successCard = document.getElementById('attendanceSuccessCard');

  // 1. Auto-Advance & Backspace Jump for 6 Token Boxes
  tokenInputs.forEach((input, idx) => {
    input.addEventListener('input', (e) => {
      const val = e.target.value.toUpperCase();
      input.value = val;
      if (errorAlert) errorAlert.style.display = 'none';

      if (val && idx < tokenInputs.length - 1) {
        tokenInputs[idx + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && idx > 0) {
        tokenInputs[idx - 1].focus();
      }
    });
  });

  // Handle Paste event across token boxes
  if (tokenContainer) {
    tokenContainer.addEventListener('paste', (e) => {
      e.preventDefault();
      const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim().toUpperCase();
      if (!pasteData) return;

      const chars = pasteData.split('');
      tokenInputs.forEach((input, idx) => {
        if (idx < chars.length) {
          input.value = chars[idx];
        }
      });

      const nextIdx = Math.min(chars.length, tokenInputs.length - 1);
      tokenInputs[nextIdx].focus();
      if (errorAlert) errorAlert.style.display = 'none';
    });
  }

  // 2. Submit Token Logic with Real Backend Verification
  window.submitTokenPresence = async function() {
    let enteredToken = '';
    tokenInputs.forEach(inp => enteredToken += inp.value.trim());

    if (enteredToken.length < 3) {
      showError('Mohon masukkan kode token resmi yang dibacakan panitia.');
      if (tokenInputs[0]) tokenInputs[0].focus();
      return;
    }

    const regId = document.getElementById('presensiRegistrationId')?.value;
    const csrfToken = document.getElementById('presensiCsrfToken')?.value;

    if (!regId) {
      showError('Data pendaftaran event tidak ditemukan.');
      return;
    }

    setSubmitting(true);

    try {
      const res = await fetch('/presensi/submit', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          registration_id: regId,
          token: enteredToken,
          metode: 'token_input'
        })
      });

      const data = await res.json();

      if (res.ok && data.status === 'success') {
        renderSuccess(data.data || {
          nama: 'Mahasiswa UMRAH',
          nim: '-',
          event_judul: 'Kegiatan Mahasiswa UMRAH',
          waktu_hadir: new Date().toLocaleTimeString('id-ID') + ' WIB',
          metode: `Input Kode Token (${enteredToken})`
        });
      } else {
        showError(data.message || 'Token presensi tidak valid atau belum dibuka oleh panitia.');
      }
    } catch (err) {
      console.error(err);
      showError('Gagal menghubungkan ke server presensi kampus. Silakan coba lagi.');
    } finally {
      setSubmitting(false);
    }
  };

  // 3. Real HTML5 Camera Scanner Logic
  let html5QrScannerInstance = null;
  let isCameraScanning = false;

  window.toggleRealCameraScan = async function() {
    if (isCameraScanning) {
      await stopRealCameraScan();
    } else {
      await startRealCameraScan();
    }
  };

  async function startRealCameraScan() {
    const btnText = document.getElementById('btnToggleCameraText');
    const btn = document.getElementById('btnToggleCamera');
    const statusText = document.getElementById('scannerStatusText');
    const reticleCaption = document.getElementById('reticleCaption');

    if (typeof Html5Qrcode === 'undefined') {
      showError('Library pemindai kamera belum selesai dimuat. Silakan muat ulang halaman.');
      return;
    }

    try {
      if (btnText) btnText.textContent = 'Menghubungkan Kamera...';
      if (statusText) statusText.textContent = 'Meminta izin kamera perangkat...';

      if (!html5QrScannerInstance) {
        html5QrScannerInstance = new Html5Qrcode('interactiveQrReader');
      }

      const qrConfig = {
        fps: 10,
        qrbox: { width: 220, height: 220 }
      };

      await html5QrScannerInstance.start(
        { facingMode: 'environment' },
        qrConfig,
        (decodedText) => {
          // Berhasil memindai QR code
          handleQrScanned(decodedText);
        },
        () => {
          // Scanning frame loop
        }
      );

      isCameraScanning = true;
      if (btn) {
        btn.style.backgroundColor = '#dc2626';
        btn.style.borderColor = '#b91c1c';
      }
      if (btnText) btnText.textContent = 'Hentikan Kamera';
      if (statusText) statusText.textContent = 'Kamera aktif! Arahkan ke QR panitia.';
      if (reticleCaption) reticleCaption.style.opacity = '0.35';

    } catch (err) {
      console.warn('Real camera start failed/denied:', err);
      isCameraScanning = false;
      if (btn) {
        btn.style.backgroundColor = '';
        btn.style.borderColor = '';
      }
      if (btnText) btnText.textContent = 'Aktifkan Kamera & Pindai QR';
      if (statusText) statusText.textContent = 'Akses kamera tidak diizinkan / tidak ditemukan.';
      showError('Kamera tidak dapat diakses (izin browser ditolak atau perangkat tidak memiliki webcam). Silakan gunakan opsi Metode B: Input Kode Token.');
    }
  }

  async function stopRealCameraScan() {
    const btnText = document.getElementById('btnToggleCameraText');
    const btn = document.getElementById('btnToggleCamera');
    const statusText = document.getElementById('scannerStatusText');
    const reticleCaption = document.getElementById('reticleCaption');

    if (html5QrScannerInstance && isCameraScanning) {
      try {
        await html5QrScannerInstance.stop();
        html5QrScannerInstance.clear();
      } catch (err) {
        console.warn('Camera stop error:', err);
      }
    }
    isCameraScanning = false;
    if (btn) {
      btn.style.backgroundColor = '';
      btn.style.borderColor = '';
    }
    if (btnText) btnText.textContent = 'Aktifkan Kamera & Pindai QR';
    if (statusText) statusText.textContent = 'Arahkan ke QR Code di Layar Proyektor Panitia';
    if (reticleCaption) reticleCaption.style.opacity = '1';
  }

  async function handleQrScanned(qrContent) {
    await stopRealCameraScan();

    const laser = document.querySelector('.scanner-laser-line');
    if (laser) {
      laser.style.background = 'linear-gradient(90deg, transparent, #10b981, #34d399, transparent)';
      laser.style.boxShadow = '0 0 20px #10b981';
    }

    const regId = document.getElementById('presensiRegistrationId')?.value;
    const csrfToken = document.getElementById('presensiCsrfToken')?.value;

    if (!regId) {
      showError('Data pendaftaran event tidak ditemukan.');
      return;
    }

    try {
      const res = await fetch('/presensi/submit', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          registration_id: regId,
          token: qrContent,
          metode: 'scan_qr'
        })
      });

      const data = await res.json();

      if (res.ok && data.status === 'success') {
        renderSuccess(data.data || {
          nama: 'Mahasiswa UMRAH',
          nim: '-',
          event_judul: 'Kegiatan Mahasiswa UMRAH',
          waktu_hadir: new Date().toLocaleTimeString('id-ID') + ' WIB',
          metode: 'Pemindaian Kamera QR Code Dinamis'
        });
      } else {
        showError(data.message || 'Gagal memverifikasi QR Code sesi kegiatan.');
      }
    } catch (err) {
      console.error(err);
      showError('Terjadi kesalahan jaringan saat memvalidasi presensi.');
    }
  }

  // Helper: Display Success State
  function renderSuccess(payload) {
    if (payload.nama) {
      const el = document.getElementById('attName');
      if (el) el.textContent = payload.nama;
    }
    if (payload.nim) {
      const el = document.getElementById('attNim');
      if (el) el.textContent = payload.nim;
    }
    if (payload.event_judul) {
      const el = document.getElementById('attEvent');
      if (el) el.textContent = payload.event_judul;
    }
    if (payload.waktu_hadir) {
      const el = document.getElementById('attTime');
      if (el) el.textContent = payload.waktu_hadir;
    }
    if (payload.metode) {
      const el = document.getElementById('attMethod');
      if (el) el.textContent = payload.metode;
    }

    if (presensiGrid) presensiGrid.style.display = 'none';
    const stepsSection = document.getElementById('attendanceStepsSection');
    if (stepsSection) stepsSection.style.display = 'none';
    if (successCard) {
      successCard.style.display = 'block';
      successCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    if (window.showToast) {
      window.showToast('Presensi kehadiran Anda berhasil tercatat!', 'success', 'Presensi Sukses');
    }
  }

  // Helper: Show Inline Error
  function showError(msg) {
    if (errorAlert) {
      errorAlert.textContent = msg;
      errorAlert.style.display = 'block';
    } else {
      alert(msg);
    }
  }

  // Helper: Loading Button State
  function setSubmitting(isSubmitting) {
    if (!submitBtn) return;
    if (isSubmitting) {
      submitBtn.disabled = true;
      submitBtn.dataset.origHtml = submitBtn.innerHTML;
      submitBtn.innerHTML = `<span>Memverifikasi Kehadiran...</span>`;
    } else {
      submitBtn.disabled = false;
      if (submitBtn.dataset.origHtml) {
        submitBtn.innerHTML = submitBtn.dataset.origHtml;
      }
    }
  }
});
