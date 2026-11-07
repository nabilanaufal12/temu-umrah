/* ===================================================
   TEMU UMRAH - PENERBITAN SERTIFIKAT MASSAL 
   Fitur:
   1. Live preview update dari formulir
   2. Generator massal berdasarkan jumlah peserta hadir dengan visual progress bar
   3. Notifikasi sukses dan tombol pratinjau hasil sertifikat mahasiswa
=================================================== */

const recipients = [
  { no: "001", nim: "2401020073", nama: "Nabil", status: "Hadir Sah (QR)" },
  { no: "002", nim: "2401020110", nama: "Alman", status: "Hadir Sah (QR)" },
  { no: "003", nim: "2401020069", nama: "Balqis", status: "Hadir Sah (QR)" }
];
const certificateCount = Number(window.TEMU_CERTIFICATE_COUNT || recipients.length);

document.addEventListener("DOMContentLoaded", function() {
  renderRecipientsList();

  // Input listeners for live certificate preview
  const formatInput = document.getElementById("inputFormatNo");
  const signer1Input = document.getElementById("inputSigner1");
  const signer2Input = document.getElementById("inputSigner2");

  if (formatInput) {
    formatInput.addEventListener("input", function() {
      const formatted = this.value.replace("{NO}", "001");
      document.getElementById("miniCertNo").textContent = "No: " + formatted;
    });
  }

  if (signer1Input) {
    signer1Input.addEventListener("input", function() {
      document.getElementById("miniSigner1").textContent = this.value;
    });
  }

  if (signer2Input) {
    signer2Input.addEventListener("input", function() {
      document.getElementById("miniSigner2").textContent = this.value;
    });
  }
});

function renderRecipientsList() {
  const container = document.getElementById("recipientsListContainer");
  if (!container) return;

  container.innerHTML = recipients.map(r => `
    <div class="recipient-mini-row">
      <div>
        <strong style="color:var(--umrah-navy);">${r.nama}</strong>
        <span style="color:#64748B; font-size:0.75rem; margin-left:0.35rem;">(${r.nim})</span>
      </div>
      <div style="display:flex; align-items:center; gap:0.5rem;">
        <span style="font-family:monospace; font-size:0.75rem; background:#E2E8F0; padding:0.1rem 0.35rem; border-radius:3px;">#${r.no}</span>
        <span style="color:#059669; font-weight:700; font-size:0.75rem;">✔ ${r.status}</span>
      </div>
    </div>
  `).join("") + `
    <div style="text-align:center; padding:0.5rem; font-size:0.75rem; color:#64748B;">
       Data penerima lainnya mengikuti hasil presensi event.
    </div>
  `;
}

function startBatchGeneration() {
  const modal = document.getElementById("modalBatchGen");
  const progressBar = document.getElementById("genProgressBar");
  const progressText = document.getElementById("genProgressText");
  const stepText = document.getElementById("genStepText");
  const successBox = document.getElementById("genSuccessState");
  const inProgressBox = document.getElementById("genInProgressState");

  modal.classList.add("active");
  inProgressBox.style.display = "block";
  successBox.style.display = "none";
  progressBar.style.width = "0%";

  let progress = 0;
  const steps = [
     { at: 15, msg: `Memvalidasi ${certificateCount} data kehadiran & nomor seri...` },
    { at: 40, msg: "Mengompilasi layout grafis E-Sertifikat A4..." },
    { at: 70, msg: "Membubuhkan Tanda Tangan Elektronik & Segel Hash..." },
    { at: 90, msg: "Mengunggah arsip PDF & menyinkronkan ke akun mahasiswa..." },
     { at: 100, msg: `Penerbitan ${certificateCount} Sertifikat Selesai!` }
  ];

  const interval = setInterval(() => {
    progress += 5;
    if (progress > 100) progress = 100;

    progressBar.style.width = progress + "%";
    progressText.textContent = progress + "%";

    const currentStep = steps.find(s => progress <= s.at);
    if (currentStep) {
      stepText.textContent = currentStep.msg;
    }

    if (progress >= 100) {
      clearInterval(interval);
      setTimeout(() => {
        inProgressBox.style.display = "none";
        successBox.style.display = "block";

        // Simpan status penerbitan agar mahasiswa langsung dapat mengunduh e-sertifikat
        localStorage.setItem('temu_cert_published_2', 'true');
        localStorage.setItem('temu_cert_published_1', 'true');

        if (window.TemuStore && typeof window.TemuStore.broadcast === 'function') {
          window.TemuStore.broadcast('CERTIFICATE_BATCH_PUBLISHED', {
            eventId: 2,
           count: certificateCount,
            format: document.getElementById('inputFormatNo')?.value || 'FTTK-UMRAH/SRT/2026/{NO}',
            publishedAt: new Date().toISOString()
          });
        }
      }, 500);
    }
  }, 100);
}

function closeBatchModal() {
  document.getElementById("modalBatchGen").classList.remove("active");
}
