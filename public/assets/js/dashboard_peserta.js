/* ==========================================================================
   TEMU UMRAH - DASHBOARD PESERTA & E-TIKET CLIENT LOGIC
   Harmonized with AgroPulse UI/UX Design System (Streamlined & Clean)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  // SVG Icon Templates for Categories (Zero Emojis)
  const CATEGORY_ICONS = {
    SEMINAR: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
    WORKSHOP: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
    KOMPETISI: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H8c-.55 0-1 .45-1 1v1h10v-1c0-.55-.45-1-1-1h-1c-.55 0-1-.45-1-1v-2.34"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>',
    LOMBA: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H8c-.55 0-1 .45-1 1v1h10v-1c0-.55-.45-1-1-1h-1c-.55 0-1-.45-1-1v-2.34"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>',
    BAZAR: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
    DEFAULT: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'
  };

  // Prioritize real backend registrations
  const serverRegs = Array.isArray(window.TEMU_BACKEND_REGISTRATIONS)
    ? window.TEMU_BACKEND_REGISTRATIONS
    : null;

  let allRegistrations = serverRegs && serverRegs.length > 0 ? [...serverRegs] : [];

  // Filter State
  let currentFilter = 'SEMUA';
  let searchQuery = '';

  // DOM Elements
  const regListContainer = document.getElementById('regListContainer');
  const countSemua = document.getElementById('countSemua');
  const countPending = document.getElementById('countPending');
  const countTerdaftar = document.getElementById('countTerdaftar');
  const countSelesai = document.getElementById('countSelesai');
  const filterTabs = document.querySelectorAll('.peserta-tab-btn');
  const searchInput = document.getElementById('inputSearchEventSaya');
  const clearSearchBtn = document.getElementById('btnClearSearch');

  // Welcome stats summary
  const sumTotal = document.getElementById('sumTotal');
  const sumHadir = document.getElementById('sumHadir');
  const sumCert = document.getElementById('sumCert');

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
  }

  function updateCounts() {
    const total = allRegistrations.length;
    const pending = allRegistrations.filter(r => r.isPending || r.statusPay === 'MENUNGGU_VERIFIKASI' || r.statusPay === 'MENUNGGU_PEMBAYARAN').length;
    const finished = allRegistrations.filter(r => r.hasCert || r.statusPresensi === 'HADIR PENUH').length;
    const active = allRegistrations.filter(r => !r.isPending && r.statusPresensi !== 'HADIR PENUH').length;
    const cert = allRegistrations.filter(r => r.hasCert).length;

    if (countSemua) countSemua.textContent = total;
    if (countPending) countPending.textContent = pending;
    if (countTerdaftar) countTerdaftar.textContent = active;
    if (countSelesai) countSelesai.textContent = finished;

    if (sumTotal) sumTotal.textContent = total;
    if (sumHadir) sumHadir.textContent = finished;
    if (sumCert) sumCert.textContent = cert;
  }

  function renderRegistrations() {
    if (!regListContainer) return;

    let filtered = allRegistrations;

    if (currentFilter === 'PENDING') {
      filtered = allRegistrations.filter(r => r.isPending || r.statusPay === 'MENUNGGU_VERIFIKASI' || r.statusPay === 'MENUNGGU_PEMBAYARAN');
    } else if (currentFilter === 'TERDAFTAR') {
      filtered = allRegistrations.filter(r => !r.isPending && r.statusPresensi !== 'HADIR PENUH');
    } else if (currentFilter === 'SELESAI') {
      filtered = allRegistrations.filter(r => r.hasCert || r.statusPresensi === 'HADIR PENUH');
    }

    if (searchQuery) {
      filtered = filtered.filter(r => {
        const title = (r.eventTitle || '').toLowerCase();
        const ormawa = (r.ormawa || '').toLowerCase();
        const ticketNo = (r.ticketNo || '').toLowerCase();
        const loc = (r.location || '').toLowerCase();
        return title.includes(searchQuery) || ormawa.includes(searchQuery) || ticketNo.includes(searchQuery) || loc.includes(searchQuery);
      });
    }

    if (filtered.length === 0) {
      if (searchQuery) {
        regListContainer.innerHTML = `
          <div class="empty-state-card">
            <div class="empty-icon-wrap">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <h3 class="empty-title">Tidak Ada Kegiatan yang Cocok</h3>
            <p class="empty-desc">
              Tidak ditemukan tiket kegiatan dengan kata kunci "<strong>${escapeHtml(searchQuery)}</strong>".
            </p>
            <button type="button" class="btn-peserta-cta" onclick="document.getElementById('btnClearSearch').click()" style="margin: 0 auto;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              <span>Bersihkan Pencarian</span>
            </button>
          </div>
        `;
        return;
      }

      regListContainer.innerHTML = `
        <div class="empty-state-card">
          <div class="empty-icon-wrap">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          </div>
          <h3 class="empty-title">Tidak Ada Kegiatan pada Kategori Ini</h3>
          <p class="empty-desc">
            Anda belum memiliki kegiatan dalam filter ini. Silakan jelajahi katalog event kampus UMRAH untuk mendaftar kegiatan baru.
          </p>
          <a href="/jelajah-event" class="btn-peserta-cta" style="margin: 0 auto;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Jelajah Event Sekarang</span>
          </a>
        </div>
      `;
      return;
    }

    regListContainer.innerHTML = filtered.map(reg => {
      const isPending = reg.isPending || reg.statusPay === 'MENUNGGU_VERIFIKASI' || reg.statusPay === 'MENUNGGU_PEMBAYARAN';
      const isCompleted = reg.hasCert || reg.statusPresensi === 'HADIR PENUH';
      const catKey = (reg.category || 'SEMINAR').toUpperCase();
      const catIcon = CATEGORY_ICONS[catKey] || CATEGORY_ICONS.DEFAULT;

      // Status Badge
      let statusBadge = '';
      if (isPending) {
        statusBadge = `
          <span class="ticket-status-badge badge-amber">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Menunggu Pembayaran</span>
          </span>
        `;
      } else if (reg.hasCert) {
        statusBadge = `
          <span class="ticket-status-badge badge-purple">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
            <span>E-Sertifikat Terbit</span>
          </span>
        `;
      } else if (reg.statusPresensi === 'HADIR PENUH') {
        statusBadge = `
          <span class="ticket-status-badge badge-emerald">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Hadir Resmi</span>
          </span>
        `;
      } else {
        statusBadge = `
          <span class="ticket-status-badge badge-blue">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Tiket Aktif &bull; Siap Hadir</span>
          </span>
        `;
      }

      // Action Buttons (Clean & Contextual)
      let actionButtons = '';
      if (isPending) {
        actionButtons = `
          <a href="/pembayaran?event=${reg.eventId}" class="btn-ticket-primary btn-amber">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span>Bayar Tiket</span>
          </a>
        `;
      } else if (isCompleted) {
        if (reg.hasCert && reg.certNo) {
          actionButtons = `
            <a href="/sertifikat/${reg.certNo}" class="btn-ticket-primary btn-emerald">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span>Unduh E-Sertifikat</span>
            </a>
            <button type="button" class="btn-ticket-secondary" onclick="openTicketModal(${reg.id})">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10"/><path d="M7 12h10"/></svg>
              <span>Detail Tiket</span>
            </button>
          `;
        } else {
          // Hadir Resmi tapi sertifikat belum terbit dari panitia
          actionButtons = `
            <button type="button" class="btn-ticket-primary btn-blue" onclick="openTicketModal(${reg.id})">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10"/><path d="M7 12h10"/><path d="M7 16h6"/></svg>
              <span>Lihat E-Tiket</span>
            </button>
            <div class="ticket-cert-pending-hint" title="Sertifikat akan otomatis muncul begitu panitia memvalidasi dan mengunggahnya">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <span>Sertifikat sedang disiapkan panitia</span>
            </div>
          `;
        }
      } else {
        // Tiket Aktif & Siap Hadir
        actionButtons = `
          <button type="button" class="btn-ticket-primary btn-blue" onclick="openTicketModal(${reg.id})">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Tampilkan QR Tiket</span>
          </button>
          <a href="/presensi/${encodeURIComponent(reg.eventSlug || '')}" class="btn-ticket-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Presensi Acara</span>
          </a>
        `;
      }

      // Event Date Formatting for Left Stamp
      const eventDateDisplay = reg.dateShort || reg.date || 'Event UMRAH';

      return `
        <article class="peserta-ticket-card">
          <!-- Left Cover Column: Focused on Event Schedule -->
          <div class="ticket-cover" style="background: ${reg.gradient};">
            ${reg.bannerPath ? `<img class="ticket-cover-bg" src="${reg.bannerPath}" alt="${reg.eventTitle}">` : ''}
            <div class="ticket-cat-badge">
              <span>${catKey}</span>
            </div>
            <div class="ticket-event-date-chip">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span>${eventDateDisplay}</span>
            </div>
          </div>

          <!-- Middle Body Column -->
          <div class="ticket-body">
            <div class="ticket-status-row">
              ${statusBadge}
              <span class="ticket-org-tag">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>${reg.ormawa}</span>
              </span>
            </div>

            <h3 class="ticket-title">
              <a href="/event/${encodeURIComponent(reg.eventSlug || reg.eventId)}" class="ticket-title-link" title="Buka rincian kegiatan">
                ${reg.eventTitle}
              </a>
            </h3>

            <div class="ticket-meta-grid">
              <div class="ticket-meta-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>${reg.date} &bull; ${reg.time}</span>
              </div>
              <div class="ticket-meta-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>${reg.location}</span>
              </div>
            </div>

            <div class="ticket-footer-strip">
              <div class="ticket-id-group">
                <span class="ticket-no-badge">#${reg.ticketNo}</span>
                <button type="button" class="btn-copy-ticket" onclick="copyTicketNumber('${reg.ticketNo}', this)" title="Salin Nomor Tiket">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
              </div>

              <!-- Reassuring Payment Status (No Confusing Outstanding Bills) -->
              <div class="ticket-payment-status-wrap">
                ${isPending 
                  ? `<span class="ticket-pay-status tag-pending" title="Segera lakukan pembayaran untuk mengonfirmasi tiket"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Tagihan: Rp ${Number(reg.price).toLocaleString('id-ID')}</span>`
                  : (reg.price > 0 
                      ? `<span class="ticket-pay-status tag-paid" title="Pembayaran lunas terverifikasi SIPA"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Lunas (Rp ${Number(reg.price).toLocaleString('id-ID')})</span>` 
                      : `<span class="ticket-pay-status tag-free" title="Kegiatan gratis sivitas akademika UMRAH"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Gratis (Mahasiswa)</span>`
                    )
                }
              </div>
            </div>
          </div>

          <!-- Right Action Stub Column -->
          <div class="ticket-stub-actions">
            ${actionButtons}
          </div>
        </article>
      `;
    }).join('');
  }

  // Filter Tabs Event Listeners
  filterTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      filterTabs.forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');
      currentFilter = tab.getAttribute('data-filter');
      renderRegistrations();
    });
  });

  // Search Input Event Listener (Instant Realtime Filter)
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value.trim().toLowerCase();
      if (clearSearchBtn) {
        clearSearchBtn.style.display = searchQuery ? 'inline-flex' : 'none';
      }
      renderRegistrations();
    });
  }

  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', () => {
      if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
      }
      searchQuery = '';
      clearSearchBtn.style.display = 'none';
      renderRegistrations();
    });
  }

  // Ticket Modal Handling
  const ticketModal = document.getElementById('ticketModal');

  window.openTicketModal = function(id) {
    const reg = allRegistrations.find(r => r.id === id);
    if (!reg) return;

    const user = window.TEMU_PROFILE_USER || JSON.parse(localStorage.getItem('temu_user') || 'null') || {
      nama: 'Mahasiswa UMRAH',
      nim: '-'
    };

    const titleEl = document.getElementById('ticketEventTitle');
    const orgEl = document.getElementById('ticketOrganizer');
    const noEl = document.getElementById('ticketNumber');
    const userEl = document.getElementById('ticketUserName');
    const nimEl = document.getElementById('ticketUserNim');
    const dateEl = document.getElementById('ticketDate');
    const locEl = document.getElementById('ticketLocation');

    if (titleEl) titleEl.textContent = reg.eventTitle;
    if (orgEl) orgEl.textContent = 'Penyelenggara: ' + (reg.ormawa || 'Ormawa UMRAH');
    if (noEl) noEl.textContent = '#' + reg.ticketNo;
    if (userEl) userEl.textContent = user.nama || user.name || 'Mahasiswa UMRAH';
    if (nimEl) nimEl.textContent = user.nim || '-';
    if (dateEl) dateEl.textContent = reg.date + ' (' + reg.time + ')';
    if (locEl) locEl.textContent = reg.location;

    if (ticketModal) {
      ticketModal.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeTicketModal = function() {
    if (ticketModal) {
      ticketModal.classList.remove('open');
      document.body.style.overflow = '';
    }
  };

  // Keyboard accessibility: ESC closes modal
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      window.closeTicketModal();
    }
  });

  // Copy Ticket Number Helper
  window.copyTicketNumber = function(no, btnEl) {
    navigator.clipboard.writeText(no).then(() => {
      showTicketToast(`Nomor tiket #${no} berhasil disalin!`, 'success');
      if (btnEl) {
        const origSvg = btnEl.innerHTML;
        btnEl.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
        setTimeout(() => btnEl.innerHTML = origSvg, 1800);
      }
    }).catch(() => {
      showTicketToast(`Nomor tiket: #${no}`, 'info');
    });
  };

  // Toast Helper
  window.showTicketToast = function(msg, type) {
    type = type || 'info';
    if (typeof window.showToast === 'function') {
      const title = type === 'success' ? 'Berhasil' : (type === 'error' ? 'Peringatan' : 'Informasi E-Tiket');
      window.showToast(msg, type, title);
      return;
    }

    const toast = document.createElement('div');
    const bg = type === 'success' ? '#0f172a' : '#1d4ed8';
    toast.style.cssText = `position:fixed;bottom:2rem;right:2rem;background:${bg};color:#ffffff;padding:0.85rem 1.4rem;border-radius:999px;box-shadow:0 14px 35px rgba(15,23,42,0.25);z-index:10000;font-size:0.88rem;font-weight:700;display:inline-flex;align-items:center;gap:0.65rem;max-width:440px;transform:translateY(20px);opacity:0;transition:all 0.3s cubic-bezier(0.16,1,0.3,1);`;
    toast.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span>${msg}</span>`;
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
      toast.style.transform = 'translateY(0)';
      toast.style.opacity = '1';
    });

    setTimeout(() => {
      toast.style.transform = 'translateY(20px)';
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  };

  // Initial Run
  updateCounts();
  renderRegistrations();
});
