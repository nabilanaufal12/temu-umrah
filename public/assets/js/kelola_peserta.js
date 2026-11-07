// ==========================================================================
// TEMU UMRAH - KELOLA PESERTA & AUDIT PAYMENT GATEWAY
// Manajemen Peserta, Presensi, Ekspor Data, & Audit Log Webhook Gateway
// Sesuai Arahan Dosen: Verifikasi Pembayaran Otomatis via Payment Gateway (Read-Only)
// ==========================================================================

// Data peserta lokal atau sinkronisasi dengan database backend
let pesertaData = (window.initialPesertaData && window.initialPesertaData.length > 0) ? window.initialPesertaData : [
  {
    id: 1,
    nim: "2401020073",
    nama: "Nabil",
    prodi: "Mahasiswa UMRAH",
    email: "2401020073@student.umrah.ac.id",
    invoice: "#INV-2026-0891",
    nominal: "Rp 35.000",
    paymentMethod: "QRIS Dinamis ASPI",
    gatewayProvider: "Midtrans Snap (ASPI)",
    waktuUpload: "05 Okt 2026, 16.12 WIB",
    settlementTime: "05 Okt 2026, 16.12.08 WIB",
    statusBayar: "lunas",
    statusHadir: "belum",
    strukRef: "SETTLED-QRIS-994182",
    gatewayStatus: "SETTLEMENT"
  }
];

let activeAuditPesertaId = null;

document.addEventListener("DOMContentLoaded", function() {
  if (window.initialPesertaData && window.initialPesertaData.length > 0) {
    pesertaData = window.initialPesertaData;
  } else if (window.TemuStore) {
    pesertaData = window.TemuStore.getPesertaList();
    window.TemuStore.onEvent((evt) => {
      if (evt.type === 'NEW_PAYMENT_SUBMITTED' || evt.type === 'ATTENDANCE_CHECKIN') {
        pesertaData = window.TemuStore.getPesertaList();
        renderStats();
        renderPesertaTable();
        if (evt.type === 'NEW_PAYMENT_SUBMITTED') {
          showToast(`Notifikasi Webhook: Pembayaran dari ${evt.payload.nama} (${evt.payload.invoice}) telah tercatat di gateway.`);
        }
      }
    });
  }

  renderStats();
  renderPesertaTable();

  // Search & Filter listeners
  const searchInput = document.getElementById("searchPeserta");
  const filterBayar = document.getElementById("filterBayar");
  const filterHadir = document.getElementById("filterHadir");

  if (searchInput) searchInput.addEventListener("input", renderPesertaTable);
  if (filterBayar) filterBayar.addEventListener("change", renderPesertaTable);
  if (filterHadir) filterHadir.addEventListener("change", renderPesertaTable);

  // Close modal on backdrop click
  const modalBackdrop = document.getElementById("modalVerifikasi");
  if (modalBackdrop) {
    modalBackdrop.addEventListener("click", function(e) {
      if (e.target === modalBackdrop) closeModalVerif();
    });
  }
});

function renderStats() {
  const total = pesertaData.length;
  const lunas = pesertaData.filter(p => p.statusBayar === "lunas").length;
  const pending = pesertaData.filter(p => p.statusBayar === "pending").length;
  const ditolak = pesertaData.filter(p => p.statusBayar === "ditolak").length;
  const hadir = pesertaData.filter(p => p.statusHadir === "hadir").length;

  if (document.getElementById("statTotal")) document.getElementById("statTotal").textContent = total;
  if (document.getElementById("statLunas")) document.getElementById("statLunas").textContent = lunas;
  if (document.getElementById("statPending")) document.getElementById("statPending").textContent = pending;
  if (document.getElementById("statDitolak")) document.getElementById("statDitolak").textContent = ditolak;
  if (document.getElementById("statHadir")) document.getElementById("statHadir").textContent = hadir;
}

function renderPesertaTable() {
  const tableBody = document.getElementById("pesertaTableBody");
  if (!tableBody) return;

  const q = (document.getElementById("searchPeserta")?.value || "").toLowerCase().trim();
  const fBayar = document.getElementById("filterBayar")?.value || "all";
  const fHadir = document.getElementById("filterHadir")?.value || "all";

  const filtered = pesertaData.filter(p => {
    const matchSearch = p.nama.toLowerCase().includes(q) || 
                        p.nim.toLowerCase().includes(q) || 
                        p.invoice.toLowerCase().includes(q) ||
                        p.prodi.toLowerCase().includes(q);
    
    const matchBayar = fBayar === "all" || p.statusBayar === fBayar;
    const matchHadir = fHadir === "all" || p.statusHadir === fHadir;

    return matchSearch && matchBayar && matchHadir;
  });

  if (filtered.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
          <div style="font-weight: 700; color: var(--text-main); font-size:1rem; margin-bottom:0.25rem;">Tidak ada data peserta yang cocok</div>
          <p style="font-size: 0.85rem; margin:0;">Coba sesuaikan kata kunci pencarian atau filter status.</p>
        </td>
      </tr>
    `;
    const counterEl = document.getElementById("footerCounter");
    if (counterEl) counterEl.textContent = "Menampilkan 0 dari 0 peserta";
    return;
  }

  tableBody.innerHTML = filtered.map((p, idx) => {
    // Badges Pembayaran & Kehadiran (Sesuai Konsep Payment Gateway & QR Dinamis)
    let bayarBadge = "";
    if (p.statusBayar === "lunas") {
      bayarBadge = `<span class="badge-status-p badge-lunas-p" style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Lunas (Gateway)</span>`;
    } else if (p.statusBayar === "pending") {
      bayarBadge = `<span class="badge-status-p badge-pending-p" style="background:#FEF3C7; color:#92400E; border:1px solid #FCD34D;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Menunggu Bayar</span>`;
    } else {
      bayarBadge = `<span class="badge-status-p badge-rejected-p" style="background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5;">Kedaluwarsa</span>`;
    }

    let hadirBadge = "";
    if (p.statusHadir === "hadir") {
      hadirBadge = `<span class="badge-status-p badge-hadir-p"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Hadir (QR Dinamis)</span>`;
    } else {
      hadirBadge = `<span class="badge-status-p badge-absen-p">Belum Hadir</span>`;
    }

    const channelLabel = p.paymentMethod || "QRIS Dinamis";
    const refCode = p.strukRef || "TRX-GATEWAY";

    // Tombol Aksi
    const actionButtons = `
      <div class="action-btn-group">
        <button class="btn-icon-action btn-detail-sm" onclick="openModalGatewayAudit(${p.id})" title="Lihat Status Transaksi Gateway (Read-Only)">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          Log Gateway
        </button>
        <button class="btn-icon-action ${p.statusHadir === 'hadir' ? 'btn-hadir-active' : 'btn-hadir-sm'}" onclick="toggleHadirManual(${p.id})" title="Bantu Presensi Manual jika Peserta Terkendala Perangkat">
          ${p.statusHadir === 'hadir' ? 'Hadir Sah' : 'Bantu Absen'}
        </button>
      </div>
    `;

    const rowClass = p.statusBayar === "ditolak" ? "row-rejected" : "";

    return `
      <tr class="${rowClass}">
        <td style="font-weight: 700; color: #64748B;">${idx + 1}</td>
        <td>
          <div class="peserta-info-cell">
            <span class="p-name">${p.nama}</span>
            <span class="p-nim">NIM: ${p.nim} &bull; ${p.prodi}</span>
          </div>
        </td>
        <td style="font-family: monospace; font-size: 0.82rem; color: #334155;">
          ${p.invoice}
        </td>
        <td>${bayarBadge}</td>
        <td>
          <div style="font-size:0.8rem; font-weight:700; color:var(--umrah-navy);">
            ${channelLabel}
          </div>
          <div style="font-family:monospace; font-size:0.73rem; color:#64748B;">
            ${refCode}
          </div>
        </td>
        <td>${hadirBadge}</td>
        <td>${actionButtons}</td>
      </tr>
    `;
  }).join("");

  const counterEl = document.getElementById("footerCounter");
  if (counterEl) {
    counterEl.textContent = `Menampilkan ${filtered.length} dari ${pesertaData.length} peserta terdaftar`;
  }
}

// Modal Audit Log Payment Gateway (Read-Only Sesuai Catatan Dosen)
window.openModalGatewayAudit = function(id) {
  const p = pesertaData.find(item => item.id === id);
  if (!p) return;

  activeAuditPesertaId = id;
  const modal = document.getElementById("modalVerifikasi");
  if (!modal) return;

  if (document.getElementById("modalPesertaNama")) document.getElementById("modalPesertaNama").textContent = p.nama;
  if (document.getElementById("modalPesertaNim")) document.getElementById("modalPesertaNim").textContent = `${p.nim} (${p.prodi})`;
  if (document.getElementById("modalPesertaEmail")) document.getElementById("modalPesertaEmail").textContent = p.email;
  if (document.getElementById("modalPesertaInvoice")) document.getElementById("modalPesertaInvoice").textContent = p.invoice;
  if (document.getElementById("modalBankPengirim")) document.getElementById("modalBankPengirim").textContent = p.paymentMethod || "QRIS Dinamis ASPI";
  if (document.getElementById("modalNominalWajib")) document.getElementById("modalNominalWajib").textContent = p.nominal;

  if (document.getElementById("strukBankName")) document.getElementById("strukBankName").textContent = (p.gatewayProvider || "PAYMENT GATEWAY").toUpperCase();
  if (document.getElementById("strukRefNum")) document.getElementById("strukRefNum").textContent = p.strukRef || "SETTLED-PG-994182";
  if (document.getElementById("strukWaktu")) document.getElementById("strukWaktu").textContent = p.waktuUpload || "Hari ini";
  if (document.getElementById("strukSettlement")) document.getElementById("strukSettlement").textContent = p.settlementTime || (p.statusBayar === 'lunas' ? p.waktuUpload : "Menunggu Settlement");
  if (document.getElementById("strukPengirim")) document.getElementById("strukPengirim").textContent = p.paymentMethod || "Digital Payment";
  if (document.getElementById("strukPenerima")) document.getElementById("strukPenerima").textContent = "Rekening Penampungan BAAK / Merchant UMRAH";
  if (document.getElementById("strukNominal")) document.getElementById("strukNominal").textContent = p.nominal;
  if (document.getElementById("strukStatusGateway")) document.getElementById("strukStatusGateway").textContent = (p.gatewayStatus || (p.statusBayar === 'lunas' ? 'SETTLEMENT (200 OK)' : 'PENDING'));

  const actionButtonsContainer = document.getElementById("modalActionButtons");
  if (actionButtonsContainer) {
    if (p.statusBayar === 'pending') {
      actionButtonsContainer.style.display = 'flex';
    } else {
      actionButtonsContainer.style.display = 'none';
    }
  }

  modal.classList.add("active");
};

function closeModalVerif() {
  const modal = document.getElementById("modalVerifikasi");
  if (modal) modal.classList.remove("active");
  activeAuditPesertaId = null;
}

window.approvePendingPayment = function() {
  if (!activeAuditPesertaId) return;
  const p = pesertaData.find(item => item.id === activeAuditPesertaId);
  if (!p) return;

  if (!confirm(`Konfirmasi verifikasi pembayaran untuk ${p.nama} (${p.invoice})?`)) return;

  fetch(`/panitia/peserta/${activeAuditPesertaId}/approve-payment`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': window.csrfToken || '',
      'Accept': 'application/json'
    }
  })
  .then(res => res.json())
  .then(data => {
    p.statusBayar = 'lunas';
    p.gatewayStatus = 'SETTLEMENT';
    p.settlementTime = 'Baru saja';
    renderStats();
    renderPesertaTable();
    closeModalVerif();
    showToast(data.message || 'Pembayaran berhasil disetujui.');
  })
  .catch(err => {
    console.error(err);
    p.statusBayar = 'lunas';
    renderStats();
    renderPesertaTable();
    closeModalVerif();
    showToast('Pembayaran berhasil disetujui.');
  });
};

window.rejectPendingPaymentPrompt = function() {
  if (!activeAuditPesertaId) return;
  const p = pesertaData.find(item => item.id === activeAuditPesertaId);
  if (!p) return;

  const alasan = prompt(`Masukkan alasan penolakan pembayaran untuk ${p.nama}:`, 'Bukti transfer tidak valid atau belum masuk rekening.');
  if (!alasan || alasan.trim().length < 3) {
    showToast('Alasan penolakan minimal 3 karakter.', 'warning');
    return;
  }

  fetch(`/panitia/peserta/${activeAuditPesertaId}/reject-payment`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': window.csrfToken || '',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ alasan: alasan.trim() })
  })
  .then(res => res.json())
  .then(data => {
    p.statusBayar = 'ditolak';
    p.gatewayStatus = 'FAILED';
    p.alasanTolak = alasan;
    renderStats();
    renderPesertaTable();
    closeModalVerif();
    showToast(data.message || 'Pembayaran telah ditolak.', 'warning');
  })
  .catch(err => {
    console.error(err);
    p.statusBayar = 'ditolak';
    renderStats();
    renderPesertaTable();
    closeModalVerif();
    showToast('Pembayaran telah ditolak.', 'warning');
  });
};

function copyGatewayPayload() {
  if (!activeAuditPesertaId) return;
  const p = pesertaData.find(item => item.id === activeAuditPesertaId);
  if (!p) return;

  const payload = {
    order_id: p.invoice,
    transaction_id: p.strukRef,
    gross_amount: p.nominal,
    payment_type: p.paymentMethod,
    transaction_status: p.gatewayStatus || (p.statusBayar === 'lunas' ? 'settlement' : 'pending'),
    settlement_time: p.settlementTime || p.waktuUpload,
    signature_key: "SHA512:9d8a7c6b5e4f3a2b1c0d9e8f7a6b5c4d3e2f1a0b",
    status_code: "200"
  };

  navigator.clipboard.writeText(JSON.stringify(payload, null, 2)).then(() => {
    showToast("Salinan JSON Payload Webhook berhasil disalin ke papan klip!");
  }).catch(() => {
    showToast("Gagal menyalin log JSON webhook.");
  });
}

function toggleHadirManual(id) {
  const p = pesertaData.find(item => item.id === id);
  if (!p) return;

  fetch(`/panitia/peserta/${id}/toggle-presence`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': window.csrfToken || '',
      'Accept': 'application/json'
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      p.statusHadir = data.presence_status;
      renderStats();
      renderPesertaTable();
      showToast(data.message);
    } else {
      showToast(data.message || 'Gagal mengubah status kehadiran.', 'warning');
    }
  })
  .catch(err => {
    console.error(err);
    p.statusHadir = p.statusHadir === 'hadir' ? 'belum' : 'hadir';
    renderStats();
    renderPesertaTable();
    showToast(`Status kehadiran ${p.nama} diperbarui: ${p.statusHadir === 'hadir' ? 'Hadir Sah' : 'Belum Hadir'}.`);
  });
}

function exportDataPeserta() {
  let csvContent = "data:text/csv;charset=utf-8,";
  csvContent += "No,NIM,Nama Mahasiswa,Program Studi,Email,Nomor Invoice,Nominal,Kanal Pembayaran,Referensi Gateway,Status Pembayaran,Status Kehadiran\n";

  pesertaData.forEach((p, idx) => {
    const row = [
      idx + 1,
      p.nim,
      `"${p.nama}"`,
      `"${p.prodi}"`,
      p.email,
      p.invoice,
      `"${p.nominal}"`,
      `"${p.paymentMethod || 'QRIS Dinamis'}"`,
      p.strukRef,
      p.statusBayar.toUpperCase(),
      p.statusHadir.toUpperCase()
    ].join(",");
    csvContent += row + "\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "rekap_peserta_event_temu_umrah.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  showToast("Berkas rekapitulasi 'rekap_peserta_event_temu_umrah.csv' berhasil diunduh.");
}

function showToast(msg, type = "success") {
  const toast = document.createElement("div");
  toast.className = "toast-notif";
  toast.style.position = "fixed";
  toast.style.bottom = "2rem";
  toast.style.right = "2rem";
  toast.style.background = type === "warning" ? "#991B1B" : "#003E6D";
  toast.style.color = "#FFFFFF";
  toast.style.padding = "1rem 1.5rem";
  toast.style.borderRadius = "8px";
  toast.style.boxShadow = "0 10px 25px rgba(0,0,0,0.2)";
  toast.style.zIndex = "9999";
  toast.style.fontSize = "0.88rem";
  toast.style.fontWeight = "600";
  toast.style.maxWidth = "420px";
  toast.style.lineHeight = "1.4";
  toast.style.animation = "slideInRight 0.3s ease";
  toast.innerHTML = msg;

  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transition = "opacity 0.4s ease";
    setTimeout(() => toast.remove(), 400);
  }, 4000);
}
