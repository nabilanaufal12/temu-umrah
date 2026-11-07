// ==========================================================================
// TEMU UMRAH - EVENT DETAIL & REGISTRATION CLIENT LOGIC
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  // 1. Get Event ID from URL query (?id=X)
  const urlParams = new URLSearchParams(window.location.search);
  const eventId = parseInt(urlParams.get('id') || '1');

  // Event detail is supplied by the Laravel controller.
  const currentEvent = window.TEMU_BACKEND_EVENT || {
    id: eventId,
    title: 'Kegiatan mahasiswa UMRAH',
    category: 'EVENT',
    price: 0,
    priceLabel: 'Informasi tersedia di portal',
    bannerGradient: 'linear-gradient(135deg, #087fc5, #62cee1)',
    ormawa: 'Organisasi Mahasiswa UMRAH',
    speaker: 'Narasumber kegiatan mahasiswa UMRAH',
    desc: 'Detail kegiatan akan dimuat dari database Laravel.',
    quotaFilled: 0,
    quotaMax: 1,
    date: 'Segera diumumkan',
    time: '-',
    location: 'Kampus UMRAH',
    benefits: []
  };

  // DOM Elements to Bind
  const eventTitle = document.getElementById('eventTitle');
  const breadcrumbTitle = document.getElementById('breadcrumbTitle');
  const breadcrumbCategory = document.getElementById('breadcrumbCategory');
  const bannerHero = document.getElementById('bannerHero');
  const heroBadgeCategory = document.getElementById('heroBadgeCategory');
  const heroBadgePrice = document.getElementById('heroBadgePrice');
  const organizerName = document.getElementById('organizerName');
  const speakerName = document.getElementById('speakerName');
  const eventDescription = document.getElementById('eventDescription');

  // Sidebar Elements
  const sidebarPriceAmount = document.getElementById('sidebarPriceAmount');
  const sidebarQuotaNumbers = document.getElementById('sidebarQuotaNumbers');
  const sidebarQuotaPercent = document.getElementById('sidebarQuotaPercent');
  const sidebarProgressBar = document.getElementById('sidebarProgressBar');
  const sidebarDate = document.getElementById('sidebarDate');
  const sidebarTime = document.getElementById('sidebarTime');
  const sidebarLocation = document.getElementById('sidebarLocation');
  const registerCtaBtn = document.getElementById('registerCtaBtn');

  // Populate Event Data
  if (eventTitle) eventTitle.textContent = currentEvent.title;
  if (breadcrumbTitle) breadcrumbTitle.textContent = currentEvent.title;
  if (breadcrumbCategory) breadcrumbCategory.textContent = currentEvent.category;
  if (heroBadgeCategory) heroBadgeCategory.textContent = currentEvent.category;
  if (heroBadgePrice) {
    heroBadgePrice.textContent = currentEvent.priceLabel;
    heroBadgePrice.className = `badge ${currentEvent.price === 0 ? 'badge-free' : 'badge-paid'}`;
  }
  if (bannerHero) {
    if (currentEvent.bannerPath) {
      bannerHero.style.backgroundImage = `linear-gradient(to top, rgba(0, 43, 73, 0.92) 0%, rgba(0, 43, 73, 0.4) 60%, rgba(0, 43, 73, 0.2) 100%), url(${currentEvent.bannerPath})`;
      bannerHero.style.backgroundSize = 'cover';
      bannerHero.style.backgroundPosition = 'center';
    } else {
      bannerHero.style.background = currentEvent.bannerGradient;
    }
  }
  if (organizerName) organizerName.textContent = currentEvent.ormawa;
  if (speakerName && currentEvent.speaker && !currentEvent.speaker.includes('Narasumber kegiatan mahasiswa UMRAH')) {
    speakerName.textContent = currentEvent.speaker;
  }
  if (eventDescription) eventDescription.textContent = currentEvent.desc;

  // Sidebar Populating
  const quotaPct = Math.min(100, Math.round((currentEvent.quotaFilled / currentEvent.quotaMax) * 100));
  const seatsLeft = Math.max(0, currentEvent.quotaMax - currentEvent.quotaFilled);

  if (sidebarPriceAmount) {
    sidebarPriceAmount.textContent = currentEvent.priceLabel;
    if (currentEvent.price === 0) sidebarPriceAmount.classList.add('free');
  }
  if (sidebarQuotaNumbers) {
    if (seatsLeft <= 0) {
      sidebarQuotaNumbers.innerHTML = `<span style="color:#dc2626; font-weight:700;">${currentEvent.quotaFilled} / ${currentEvent.quotaMax} (Kuota Penuh)</span>`;
    } else {
      sidebarQuotaNumbers.textContent = `${currentEvent.quotaFilled} / ${currentEvent.quotaMax} (Sisa ${seatsLeft} Kursi)`;
    }
  }
  if (sidebarQuotaPercent) sidebarQuotaPercent.textContent = `${quotaPct}%`;
  if (sidebarProgressBar) {
    sidebarProgressBar.style.width = `${quotaPct}%`;
    if (quotaPct >= 85) sidebarProgressBar.classList.add('almost-full');
  }
  if (sidebarDate) sidebarDate.textContent = currentEvent.date;
  if (sidebarTime) sidebarTime.textContent = currentEvent.time;
  if (sidebarLocation) sidebarLocation.textContent = currentEvent.location;

  // Render Facilities
  const facilitiesContainer = document.getElementById('facilitiesContainer');
  if (facilitiesContainer && currentEvent.benefits) {
    facilitiesContainer.innerHTML = currentEvent.benefits.map(b => `
      <div class="facility-pill">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        <span>${b}</span>
      </div>
    `).join('');
  }

  // 2. Tab Navigation Handling
  const tabBtns = document.querySelectorAll('.tab-nav-btn');
  const tabPanes = document.querySelectorAll('.tab-pane');

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      tabBtns.forEach(b => b.classList.remove('active'));
      tabPanes.forEach(p => p.classList.remove('active'));

      btn.classList.add('active');
      const targetPane = document.getElementById(btn.getAttribute('data-target'));
      if (targetPane) targetPane.classList.add('active');
    });
  });

  // 3. Modal Registration Handling
  const regModal = document.getElementById('regConfirmModal');
  const modalClose = document.getElementById('modalConfirmClose');
  const modalCancel = document.getElementById('modalConfirmCancel');
  const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

  // Registration Summary Fields
  const sumEventTitle = document.getElementById('sumEventTitle');
  const sumStudentName = document.getElementById('sumStudentName');
  const sumStudentNim = document.getElementById('sumStudentNim');
  const sumStudentEmail = document.getElementById('sumStudentEmail');
  const sumStudentWa = document.getElementById('sumStudentWa');
  const sumPrice = document.getElementById('sumPrice');

  function openConfirmModal() {
    const loggedUser = window.TEMU_CURRENT_USER;
    if (!loggedUser || !loggedUser.id) {
      if (confirm('Silakan masuk dengan akun mahasiswa UMRAH terlebih dahulu untuk mendaftar kegiatan ini.\n\nKlik OK untuk menuju ke halaman login.')) {
        window.location.href = '/login';
      }
      return;
    }
    if (loggedUser.role !== 'mahasiswa') {
      alert(`Anda saat ini masuk sebagai ${loggedUser.role}. Pendaftaran kegiatan hanya diperuntukkan bagi mahasiswa aktif UMRAH.`);
      return;
    }
    if (window.TEMU_IS_REGISTERED) {
      window.location.href = '/event-saya';
      return;
    }
    if (seatsLeft <= 0) {
      alert('Mohon maaf, kuota kursi pendaftaran untuk kegiatan ini telah penuh.');
      return;
    }

    if (sumEventTitle) sumEventTitle.textContent = currentEvent.title;
    if (sumStudentName && loggedUser.nama) sumStudentName.textContent = loggedUser.nama;
    if (sumStudentNim && loggedUser.nim) sumStudentNim.textContent = loggedUser.nim;
    if (sumStudentEmail && loggedUser.email) sumStudentEmail.textContent = loggedUser.email;
    if (sumStudentWa && loggedUser.no_wa) sumStudentWa.textContent = loggedUser.no_wa;
    if (sumPrice) sumPrice.textContent = currentEvent.priceLabel;

    regModal.classList.add('open');
  }

  function closeConfirmModal() {
    regModal.classList.remove('open');
  }

  if (registerCtaBtn) registerCtaBtn.addEventListener('click', openConfirmModal);
  if (modalClose) modalClose.addEventListener('click', closeConfirmModal);
  if (modalCancel) modalCancel.addEventListener('click', closeConfirmModal);

  // 4. Submit Registration (Business Flow Branching)
  if (confirmSubmitBtn) {
    confirmSubmitBtn.addEventListener('click', () => {
      if (window.TEMU_BACKEND_EVENT && window.TEMU_REGISTRATION_URL) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        confirmSubmitBtn.disabled = true;

        fetch(window.TEMU_REGISTRATION_URL, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({})
        })
          .then(async response => {
            const payload = await response.json();
            if (!response.ok) throw new Error(payload.message || 'Pendaftaran gagal diproses.');
            return payload;
          })
          .then(payload => {
            window.location.href = payload.redirect;
          })
          .catch(error => {
            alert(error.message);
            confirmSubmitBtn.disabled = false;
          });
        return;
      }

      const isPaid = currentEvent.price > 0;

      if (isPaid) {
        // Event Berbayar -> Arahkan ke WF4 (Halaman Instruksi Pembayaran & Upload Bukti)
        alert(`Event "${currentEvent.title}" adalah kegiatan berbayar (${currentEvent.priceLabel}).\\n\\nAnda akan dialihkan ke Halaman Instruksi Pembayaran & Upload Bukti.`);
         window.location.href = `/pembayaran?event=${currentEvent.id}`;
      } else {
        // Event Gratis -> Langsung Terdaftar (Status: Terdaftar)
        const ticketNo = `TIK-UMRAH-${currentEvent.id}0${Math.floor(100 + Math.random() * 900)}`;
        
        // Simpan pendaftaran ke localStorage
        const regs = JSON.parse(localStorage.getItem('temu_registrations') || '[]');
        regs.push({
          eventId: currentEvent.id,
          eventTitle: currentEvent.title,
          ticketNo: ticketNo,
          date: currentEvent.date,
          location: currentEvent.location,
          status: 'TERDAFTAR',
          price: 0
        });
        localStorage.setItem('temu_registrations', JSON.stringify(regs));

        // Tampilkan Tampilan Sukses Tiket
        const modalBody = document.querySelector('.modal-confirm-body');
        const modalFooter = document.querySelector('.modal-confirm-footer');
        
        modalBody.innerHTML = `
          <div class="success-ticket-box fade-in">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--success-bg); color: var(--success-text); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--umrah-navy); margin-bottom: 0.35rem;">Pendaftaran Berhasil!</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); max-width: 420px; margin: 0 auto;">Selamat, tiket pendaftaran Anda telah diterbitkan secara resmi oleh sistem TEMU UMRAH.</p>
            
            <div class="ticket-badge-number">${ticketNo}</div>
            
            <p style="font-size: 0.82rem; color: var(--text-body);">QR Code & Token Presensi dapat Anda akses pada menu <strong>Event Saya</strong>.</p>
          </div>
        `;

        modalFooter.innerHTML = `
          <a href="/" class="btn btn-outline btn-sm">Kembali ke Katalog</a>
          <a href="/event-saya" class="btn btn-primary btn-sm" style="text-decoration:none;">Lihat Tiket di Event Saya &rarr;</a>
        `;
      }
    });
  }

  // 5. Share Button
  window.shareEvent = function() {
    const currentUrl = window.location.href;
    navigator.clipboard?.writeText(currentUrl).then(() => {
      alert('Tautan informasi event berhasil disalin ke clipboard!');
    }).catch(() => {
      alert(`Salin tautan ini: ${currentUrl}`);
    });
  };
});
