/* ==========================================================================
   TEMU UMRAH - LAYAR SESI ABSENSI PANITIA (MODE PROYEKTOR AUDITORIUM)
   1. Dynamic TOTP QR Code (Standar mesin dapat dipindai, rotasi tiap 30 detik)
   2. Hitung mundur bar sinkron dengan backend
   3. Live stream kehadiran mahasiswa terkoneksi basis data riil (polling 5 detik)
   4. Presensi manual langsung simpan ke basis data
   5. Penutupan sesi & alur penerbitan e-sertifikat massal
   ========================================================================== */

let qrCountdown = 30;
let qrTimerInterval = null;
let currentHash = "QR-EVT2-INIT";
let currentToken = window.TEMU_SESSION_TOKEN || "FIGM26";
let eventId = window.TEMU_EVENT_ID || 2;
let totalPeserta = Number(window.TEMU_SESSION_STATS?.registered || 0);
let hadirCount = Number(window.TEMU_SESSION_STATS?.present || 0);

document.addEventListener("DOMContentLoaded", function() {
  renderTokenBoxes(currentToken);
  updateStatsDisplay(hadirCount, totalPeserta);

  // Ambil hash QR resmi pertama kali dari backend
  fetchActiveQrData();

  // Inisialisasi timer countdown per detik
  initQrCountdown();

  // Polling sinkronisasi kehadiran live setiap 5 detik
  setInterval(fetchActiveQrData, 5000);
});

function initQrCountdown() {
  clearInterval(qrTimerInterval);
  updateTimerUI();

  qrTimerInterval = setInterval(() => {
    qrCountdown--;
    if (qrCountdown <= 0) {
      fetchActiveQrData();
      qrCountdown = 30;
    }
    updateTimerUI();
  }, 1000);
}

function updateTimerUI() {
  const bar = document.getElementById("qrTimerFill");
  const text = document.getElementById("qrTimerText");
  if (bar) {
    const pct = Math.max(0, Math.min(100, (qrCountdown / 30) * 100));
    bar.style.width = pct + "%";
  }
  if (text) {
    text.textContent = `${Math.max(0, qrCountdown)} Detik`;
  }
}

async function fetchActiveQrData() {
  try {
    const res = await fetch(`/panitia/event/${eventId}/active-qr`);
    if (!res.ok) return;

    const data = await res.json();
    if (data.status === 'success') {
      currentHash = data.hash;

      const hashDisplay = document.getElementById("qrHashDisplay");
      if (hashDisplay) hashDisplay.textContent = `Hash Dinamis: #${currentHash}`;

      renderQrVisual(data.payload);

      if (typeof data.seconds_remaining === 'number') {
        qrCountdown = data.seconds_remaining;
        updateTimerUI();
      }

      if (data.stats) {
        hadirCount = data.stats.present;
        totalPeserta = data.stats.registered;
        updateStatsDisplay(data.stats.present, data.stats.registered, data.stats.percent);
      }

      if (data.recent && Array.isArray(data.recent)) {
        renderLiveFeed(data.recent);
      }
    }
  } catch (err) {
    console.warn("Polling active QR error:", err);
  }
}

function renderQrVisual(payload) {
  const wrapper = document.getElementById("qrcodeWrapper");
  if (!wrapper) return;

  if (typeof QRCode !== 'undefined') {
    wrapper.innerHTML = "";
    new QRCode(wrapper, {
      text: payload,
      width: 240,
      height: 240,
      colorDark: "#003E6D",
      colorLight: "#FFFFFF",
      correctLevel: QRCode.CorrectLevel.M
    });
  } else {
    wrapper.innerHTML = `<div style="color:#003E6D; font-weight:700; text-align:center; padding:1rem;">${payload}</div>`;
  }
}

function renderTokenBoxes(tokenStr) {
  const container = document.getElementById("tokenBoxesContainer");
  if (!container) return;

  container.innerHTML = tokenStr.split("").map(char => {
    return `<div class="token-box-char">${char}</div>`;
  }).join("");
}

function regenerateToken() {
  const chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
  let newToken = "";
  for (let i = 0; i < 6; i++) {
    newToken += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  currentToken = newToken;
  renderTokenBoxes(currentToken);
  showProjToast(`Token presensi cadangan diperbarui: ${currentToken}`);
}

function updateStatsDisplay(present, registered, percent) {
  const countEl = document.getElementById("statHadirCount");
  const pctEl = document.getElementById("statHadirPct");
  const progFill = document.getElementById("statProgFill");

  const reg = typeof registered !== 'undefined' ? registered : totalPeserta;
  const pres = typeof present !== 'undefined' ? present : hadirCount;
  const pct = percent ? percent : (reg > 0 ? ((pres / reg) * 100).toFixed(1) : '0.0');

  if (countEl) countEl.textContent = `${pres} / ${reg}`;
  if (pctEl) pctEl.textContent = `${pct}% Terisi`;
  if (progFill) progFill.style.width = `${Math.min(100, Number(pct))}%`;
}

function renderLiveFeed(recentList) {
  const feedContainer = document.getElementById("liveFeedStream");
  if (!feedContainer || !Array.isArray(recentList)) return;

  if (recentList.length === 0) {
    feedContainer.innerHTML = `
      <div class="feed-item-row" id="feedEmptyNotice">
        <span style="color:#94a3b8; font-size:0.85rem;">Belum ada peserta yang melakukan presensi.</span>
      </div>
    `;
    return;
  }

  feedContainer.innerHTML = recentList.map(item => `
    <div class="feed-item-row">
      <div>
        <span class="feed-item-name">${item.nama}</span>
        <span class="feed-item-nim">(${item.nim} - ${item.prodi})</span>
      </div>
      <span class="feed-item-time">
        <svg class="icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        ${item.metode} (${item.time})
      </span>
    </div>
  `).join("");
}

async function manualCheckinPrompt() {
  const nim = prompt("Masukkan NIM Mahasiswa untuk Verifikasi Kehadiran Manual:");
  if (!nim || nim.trim().length < 4) return;

  try {
    const res = await fetch(`/panitia/event/${eventId}/manual-checkin`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.csrfToken || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ nim: nim.trim() })
    });

    const data = await res.json();
    if (res.ok && data.status === 'success') {
      showProjToast(data.message);
      if (data.stats) {
        hadirCount = data.stats.present;
        totalPeserta = data.stats.registered;
        updateStatsDisplay(data.stats.present, data.stats.registered, data.stats.percent);
      }
      fetchActiveQrData();
    } else {
      showProjToast(data.message || 'Gagal melakukan verifikasi presensi manual.', 'warning');
    }
  } catch (err) {
    console.error(err);
    showProjToast('Gagal menghubungi server.', 'warning');
  }
}

async function closeSessionAndProceed() {
  const confirmClose = confirm(
    "Apakah Anda yakin ingin MENUTUP SESI PRESENSI?\n\n" +
    "Setelah ditutup, total " + hadirCount + " mahasiswa berhak mendapatkan E-Sertifikat Digital.\n" +
    "Sistem akan mengarahkan ke Halaman Penerbitan E-Sertifikat Massal."
  );

  if (!confirmClose) return;

  try {
    const res = await fetch(`/panitia/event/${eventId}/close-session`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.csrfToken || '',
        'Accept': 'application/json'
      }
    });

    const data = await res.json();
    if (res.ok && data.status === 'closed') {
      showProjToast(data.message);
      setTimeout(() => {
        window.location.href = data.redirect_url || `/panitia/event/${eventId}/terbitkan-sertifikat`;
      }, 1200);
    } else {
      window.location.href = `/panitia/event/${eventId}/terbitkan-sertifikat`;
    }
  } catch (err) {
    console.error(err);
    window.location.href = `/panitia/event/${eventId}/terbitkan-sertifikat`;
  }
}

function showProjToast(msg, type = 'info') {
  const toast = document.createElement("div");
  toast.style.position = "fixed";
  toast.style.top = "1.5rem";
  toast.style.left = "50%";
  toast.style.transform = "translateX(-50%)";
  toast.style.background = type === 'warning' ? '#DC2626' : '#0284C7';
  toast.style.color = "#FFFFFF";
  toast.style.padding = "0.75rem 1.75rem";
  toast.style.borderRadius = "8px";
  toast.style.boxShadow = "0 8px 25px rgba(0,0,0,0.4)";
  toast.style.zIndex = "9999";
  toast.style.fontSize = "0.9rem";
  toast.style.fontWeight = "700";
  toast.textContent = msg;

  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transition = "opacity 0.4s ease";
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}
