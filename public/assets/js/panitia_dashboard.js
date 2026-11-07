// ==========================================================================
// TEMU UMRAH - PANITIA DASHBOARD CLIENT LOGIC
// CRUD Kegiatan Ormawa, Alur Pengajuan Event ke Admin BAAK, & Modul Terpadu
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  // Dashboard Laravel memakai data server; prototype tetap memakai localStorage.
  const backendDashboard = window.TEMU_PANITIA_BACKEND === true;
  let panitiaEvents = backendDashboard && Array.isArray(window.TEMU_PANITIA_EVENTS)
    ? window.TEMU_PANITIA_EVENTS
    : JSON.parse(localStorage.getItem('temu_panitia_events') || 'null');
  if (!backendDashboard && !panitiaEvents) {
    panitiaEvents = [
      {
        id: 1,
        title: 'Seminar Nasional AI: Peluang Karir Generative AI 2026',
        category: 'Seminar',
        date: '18 Okt 2026',
        time: '08:30 WIB',
        location: 'Auditorium Gedung E Lt. 3',
        quotaFilled: 168,
        quotaMax: 200,
        price: 0,
        priceLabel: 'Gratis',
        hasCert: true,
        status: 'Publik',
        gradient: 'linear-gradient(135deg, #003E6D, #005696)',
        icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a5 5 0 0 1 5 5v1a5 5 0 0 1-10 0V7a5 5 0 0 1 5-5z"></path><path d="M19 11v1a7 7 0 0 1-14 0v-1"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>'
      },
      {
        id: 2,
        title: 'Workshop UI/UX Product Design with Figma',
        category: 'Workshop',
        date: '25 Okt 2026',
        time: '09:00 WIB',
        location: 'Lab Komputer RPL FTTK',
        quotaFilled: 48,
        quotaMax: 50,
        price: 35000,
        priceLabel: 'Rp 35.000',
        hasCert: true,
        status: 'Publik',
        gradient: 'linear-gradient(135deg, #0F172A, #334155)',
        icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>'
      },
      {
        id: 3,
        title: 'Campus Competitive Programming Championship 2026',
        category: 'Lomba',
        date: '05 Sep 2026',
        time: '13:00 WIB',
        location: 'Daring (HackerRank & Zoom)',
        quotaFilled: 50,
        quotaMax: 50,
        price: 50000,
        priceLabel: 'Rp 50.000',
        hasCert: true,
        status: 'Selesai',
        gradient: 'linear-gradient(135deg, #064E3B, #047857)',
        icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>'
      },
      {
        id: 4,
        title: 'Bazar Kreatif Mahasiswa & Expo Inovasi Startup 2026',
        category: 'Bazar',
        date: '12 Nov 2026',
        time: '08:00 WIB',
        location: 'Plaza Rektorat Kampus Dompak',
        quotaFilled: 0,
        quotaMax: 350,
        price: 0,
        priceLabel: 'Gratis',
        hasCert: false,
        status: 'Menunggu Persetujuan',
        gradient: 'linear-gradient(135deg, #78350F, #B45309)',
        icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>'
      }
    ];
    localStorage.setItem('temu_panitia_events', JSON.stringify(panitiaEvents));
  }

  function saveEvents() {
    localStorage.setItem('temu_panitia_events', JSON.stringify(panitiaEvents));
  }

  // Reactive Cross-Tab Listener via TemuStore
  if (typeof window.TemuStore !== 'undefined' && typeof window.TemuStore.onEvent === 'function') {
    window.TemuStore.onEvent((evt) => {
      if (evt.type === 'EVENT_APPROVED' || evt.type === 'EVENT_REJECTED') {
        const stored = localStorage.getItem('temu_panitia_events');
        if (stored) {
          panitiaEvents = JSON.parse(stored);
          renderTable();
        }
        if (evt.type === 'EVENT_APPROVED' && evt.payload) {
          if (typeof window.showToast === 'function') {
            window.showToast(`Kegiatan "${evt.payload.event?.title || 'Event'}" telah DISETUJUI oleh Super Admin dan kini tampil di katalog publik!`, 'success', 'Event Disetujui Admin');
          }
        } else if (evt.type === 'EVENT_REJECTED' && evt.payload) {
          if (typeof window.showToast === 'function') {
            window.showToast(`Pengajuan "${evt.payload.event?.title || 'Event'}" ditolak: ${evt.payload.alasan}`, 'error', 'Pemberitahuan BAAK');
          }
        }
      }
    });
  }

  // DOM Elements
  const tableBody = document.getElementById('panitiaEventsTableBody');
  const totalEventsCount = document.getElementById('totalEventsCount');
  const totalParticipantsCount = document.getElementById('totalParticipantsCount');
  const btnOpenCreateModal = document.getElementById('btnOpenCreateModal');
  const eventCrudModal = document.getElementById('eventCrudModal');
  const modalCloseBtn = document.getElementById('modalCrudClose');
  const modalCancelBtn = document.getElementById('modalCrudCancel');
  const eventForm = document.getElementById('eventCrudForm');

  // SIPA Table Filter Elements
  const filterCategory = document.getElementById('filterCategory');
  const filterStatus = document.getElementById('filterStatus');
  const filterSearch = document.getElementById('filterSearch');
  const sipaTableInfo = document.getElementById('sipaTableInfo');

  window.resetFilters = function() {
    if (filterCategory) filterCategory.value = '';
    if (filterStatus) filterStatus.value = '';
    if (filterSearch) filterSearch.value = '';
    renderTable();
  };

  if (filterCategory) filterCategory.addEventListener('change', renderTable);
  if (filterStatus) filterStatus.addEventListener('change', renderTable);
  if (filterSearch) filterSearch.addEventListener('input', renderTable);

  // Radio Biaya Toggles
  const radioPaid = document.getElementById('feePaidRadio');
  const radioFree = document.getElementById('feeFreeRadio');
  const paidInputsBox = document.getElementById('paidInputsBox');

  if (radioPaid && radioFree && paidInputsBox) {
    radioPaid.addEventListener('change', () => {
      paidInputsBox.style.display = radioPaid.checked ? 'block' : 'none';
    });
    radioFree.addEventListener('change', () => {
      paidInputsBox.style.display = 'none';
    });
  }

  function updateKpi() {
    if (totalEventsCount) totalEventsCount.textContent = `${panitiaEvents.length} Kegiatan`;
    const sumParticipants = panitiaEvents.reduce((acc, ev) => acc + (ev.quotaFilled || 0), 0);
    if (totalParticipantsCount) totalParticipantsCount.textContent = `${sumParticipants} Mahasiswa`;
  }

  function renderTable() {
    updateKpi();
    if (!tableBody) return;

    let filtered = panitiaEvents.filter(ev => {
      if (filterCategory && filterCategory.value && ev.category.toUpperCase() !== filterCategory.value.toUpperCase()) {
        return false;
      }
      if (filterStatus && filterStatus.value) {
        const st = (ev.status || '').toLowerCase();
        const val = filterStatus.value.toLowerCase();
        if (!st.includes(val) && !val.includes(st)) {
          return false;
        }
      }
      if (filterSearch && filterSearch.value.trim()) {
        const q = filterSearch.value.trim().toLowerCase();
        const matchTitle = (ev.title || '').toLowerCase().includes(q);
        const matchLoc = (ev.location || '').toLowerCase().includes(q);
        if (!matchTitle && !matchLoc) {
          return false;
        }
      }
      return true;
    });

    if (sipaTableInfo) {
      sipaTableInfo.textContent = `Menampilkan ${filtered.length} dari ${panitiaEvents.length} kegiatan ormawa`;
    }

    if (filtered.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="8" style="text-align:center; padding:2.5rem 1rem; color:#64748b;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="display:block; margin:0 auto 0.5rem;"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
            <strong style="color:#0f172a; display:block; margin-bottom:0.25rem;">Tidak Ada Kegiatan yang Cocok</strong>
            <span>Silakan ubah filter kategori, status, atau kata kunci pencarian Anda.</span>
          </td>
        </tr>
      `;
      return;
    }

    tableBody.innerHTML = filtered.map((ev, idx) => {
      let statusBadgeHtml = '<span class="badge badge-category">Draft</span>';
      if (ev.status === 'Publik' || ev.status === 'published') {
        statusBadgeHtml = '<span class="badge badge-free" style="display:inline-flex; align-items:center; gap:0.25rem;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Disetujui (Publik)</span>';
      } else if (ev.status === 'Menunggu Persetujuan' || ev.status === 'pending_approval') {
        statusBadgeHtml = '<span class="badge badge-warning" style="background:#FFFBEB; color:#B45309; border:1px solid #FCD34D; display:inline-flex; align-items:center; gap:0.25rem;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Menunggu BAAK</span>';
      } else if (ev.status === 'Ditolak' || ev.status === 'rejected') {
        statusBadgeHtml = `<span class="badge badge-danger" style="background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; display:inline-flex; align-items:center; gap:0.25rem;" title="${ev.catatanAdmin || 'Belum memenuhi syarat'}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Ditolak / Revisi</span>`;
      } else if (ev.status === 'Selesai') {
        statusBadgeHtml = '<span class="badge badge-paid">Selesai</span>';
      }

      return `
        <tr data-id="${ev.id}">
          <td style="text-align: center; color: #64748b; font-weight: 700;">${idx + 1}</td>
          <td>
            <div class="event-cell-info">
              <div class="event-cell-icon" style="background: ${ev.gradient};">
                ${ev.icon}
              </div>
              <div class="event-cell-text">
                <h4 style="font-size:0.92rem; margin:0 0 0.2rem 0; color:#0f172a; font-weight:700;">${ev.title}</h4>
                <p style="margin:0; font-size:0.75rem; color:var(--text-muted);">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> ${ev.location}
                </p>
                ${ev.catatanAdmin ? `<p style="margin:0.2rem 0 0; font-size:0.72rem; color:#DC2626; font-weight:600;">Catatan Admin: ${ev.catatanAdmin}</p>` : ''}
              </div>
            </div>
          </td>
          <td><span class="badge badge-category">${ev.category}</span></td>
          <td><strong>${ev.date}</strong><br><span style="font-size:0.75rem; color:var(--text-muted);">${ev.time}</span></td>
          <td><strong>${ev.quotaFilled} / ${ev.quotaMax}</strong></td>
          <td><span class="badge ${ev.price === 0 ? 'badge-free' : 'badge-paid'}">${ev.priceLabel}</span></td>
          <td>${statusBadgeHtml}</td>
          <td style="text-align: center; white-space: nowrap;">
            <div class="sipa-table-actions">
              <a href="/panitia/event/${ev.id}/peserta" class="sipa-btn-kelola-row" title="Buka Ruang Kerja &amp; Kelola Kegiatan Ini">
                <span>Kelola Event</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
              </a>
              <button type="button" class="sipa-btn-edit-row" onclick="openEditEventModal(${ev.id})" title="Ubah Rincian Kegiatan">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                <span>Edit</span>
              </button>
              <button type="button" class="sipa-btn-delete-row" onclick="deleteEvent(${ev.id})" title="Hapus Kegiatan">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Modal Open & Close
  if (btnOpenCreateModal) {
    btnOpenCreateModal.addEventListener('click', () => {
      eventForm.reset();
      const crudMethod = document.getElementById('crudMethod');
      const crudEventId = document.getElementById('crudEventId');
      const modalTitleText = document.getElementById('modalTitleText');
      const modalSubText = document.getElementById('modalSubText');
      const btnSubmitText = document.getElementById('btnSubmitText');

      if (crudMethod) crudMethod.value = 'POST';
      if (crudEventId) crudEventId.value = '';
      if (modalTitleText) modalTitleText.textContent = 'Buat Kegiatan Ormawa Baru';
      if (modalSubText) modalSubText.textContent = 'Formulir Pengajuan Kegiatan Resmi ke Admin BAAK';
      if (btnSubmitText) btnSubmitText.textContent = 'Ajukan Kegiatan ke Admin BAAK';
      if (eventForm) eventForm.action = '/panitia/event/store';

      if (radioFree) radioFree.checked = true;
      if (paidInputsBox) paidInputsBox.style.display = 'none';

      eventCrudModal.classList.add('open');
    });
  }

  // Edit Event Modal Opener
  window.openEditEventModal = function(id) {
    const ev = panitiaEvents.find(e => e.id == id);
    if (!ev) return;

    const inputTitle = document.getElementById('inputEventTitle');
    const inputCategory = document.getElementById('inputCategory');
    const inputQuota = document.getElementById('inputQuota');
    const inputDate = document.getElementById('inputDate');
    const inputLocation = document.getElementById('inputLocation');
    const inputPrice = document.getElementById('inputPrice');
    const crudMethod = document.getElementById('crudMethod');
    const crudEventId = document.getElementById('crudEventId');
    const modalTitleText = document.getElementById('modalTitleText');
    const modalSubText = document.getElementById('modalSubText');
    const btnSubmitText = document.getElementById('btnSubmitText');

    if (inputTitle) inputTitle.value = ev.title || '';
    if (inputCategory) inputCategory.value = (ev.category || 'SEMINAR').toUpperCase();
    if (inputQuota) inputQuota.value = ev.quotaMax || 100;
    if (inputDate) inputDate.value = ev.rawDate || '';
    if (inputLocation) inputLocation.value = ev.location || '';

    const isPaid = ev.tipeBiaya === 'berbayar' || (ev.price && ev.price > 0);
    if (isPaid) {
      if (radioPaid) radioPaid.checked = true;
      if (paidInputsBox) paidInputsBox.style.display = 'block';
      if (inputPrice) inputPrice.value = ev.rawHarga || ev.price || 0;
    } else {
      if (radioFree) radioFree.checked = true;
      if (paidInputsBox) paidInputsBox.style.display = 'none';
      if (inputPrice) inputPrice.value = '';
    }

    if (crudMethod) crudMethod.value = 'PUT';
    if (crudEventId) crudEventId.value = ev.id;
    if (modalTitleText) modalTitleText.textContent = 'Ubah Rincian Kegiatan';
    if (modalSubText) modalSubText.textContent = 'Perbarui informasi kegiatan ormawa Anda';
    if (btnSubmitText) btnSubmitText.textContent = 'Simpan Perubahan Kegiatan';
    if (eventForm) eventForm.action = `/panitia/event/${ev.id}`;

    if (eventCrudModal) eventCrudModal.classList.add('open');
  };

  function closeModal() {
    if (eventCrudModal) eventCrudModal.classList.remove('open');
  }

  if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
  if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeModal);
  if (eventCrudModal) {
    eventCrudModal.addEventListener('click', (e) => {
      if (e.target === eventCrudModal) closeModal();
    });
  }

  // Form Submit (Alur Pengajuan & Update Event ke Admin BAAK)
  if (eventForm) {
    eventForm.addEventListener('submit', (e) => {
      if (eventForm.dataset.backendForm === 'true') return;
      e.preventDefault();
      const title = document.getElementById('inputEventTitle').value.trim();
      const category = document.getElementById('inputCategory').value;
      const date = document.getElementById('inputDate').value;
      const location = document.getElementById('inputLocation').value.trim();
      const quota = parseInt(document.getElementById('inputQuota').value) || 100;
      const isPaid = document.getElementById('feePaidRadio').checked;
      const priceVal = isPaid ? parseInt(document.getElementById('inputPrice').value) || 25000 : 0;
      const hasCert = document.getElementById('certYesRadio').checked;

      const method = document.getElementById('crudMethod')?.value || 'POST';
      const eventIdToEdit = document.getElementById('crudEventId')?.value;

      if (method === 'PUT' && eventIdToEdit) {
        const evIdx = panitiaEvents.findIndex(e => e.id == eventIdToEdit);
        if (evIdx !== -1) {
          panitiaEvents[evIdx].title = title;
          panitiaEvents[evIdx].category = category;
          panitiaEvents[evIdx].date = date;
          panitiaEvents[evIdx].location = location;
          panitiaEvents[evIdx].quotaMax = quota;
          panitiaEvents[evIdx].price = priceVal;
          panitiaEvents[evIdx].priceLabel = priceVal === 0 ? 'Gratis' : `Rp ${priceVal.toLocaleString('id-ID')}`;
          panitiaEvents[evIdx].hasCert = hasCert;
          saveEvents();
          renderTable();
          closeModal();
          if (typeof window.showToast === 'function') {
            window.showToast(`Perubahan kegiatan "${title}" berhasil disimpan!`, 'success', 'Perubahan Disimpan');
          }
          return;
        }
      }

      const newId = Date.now();
      const newEvent = {
        id: newId,
        title: title,
        category: category,
        date: date,
        time: '09:00 WIB',
        location: location,
        quotaFilled: 0,
        quotaMax: quota,
        price: priceVal,
        priceLabel: priceVal === 0 ? 'Gratis' : `Rp ${priceVal.toLocaleString('id-ID')}`,
        hasCert: hasCert,
        status: 'Menunggu Persetujuan',
        statusLabel: 'Menunggu Persetujuan',
        gradient: 'linear-gradient(135deg, #003E6D, #005696)',
        icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>',
        ormawa: 'Himpunan Mahasiswa Teknik Informatika (HMTI)',
        desc: `Kegiatan ${category} yang diselenggarakan oleh panitia ormawa kampus FTTK UMRAH.`
      };

      // Simpan ke antrean moderasi Admin via TemuStore
      if (typeof window.TemuStore !== 'undefined' && typeof window.TemuStore.submitEventForApproval === 'function') {
        window.TemuStore.submitEventForApproval(newEvent);
      } else {
        panitiaEvents.unshift(newEvent);
        saveEvents();
      }

      panitiaEvents = JSON.parse(localStorage.getItem('temu_panitia_events') || JSON.stringify(panitiaEvents));
      renderTable();
      closeModal();

      if (typeof window.showToast === 'function') {
        window.showToast(`Pengajuan "${title}" BERHASIL DIKIRIM ke BAAK Kemahasiswaan! Menunggu verifikasi admin sebelum terbit di katalog.`, 'success', 'Pengajuan Terkirim');
      } else {
        alert(`Pengajuan kegiatan "${title}" BERHASIL DIKIRIM ke BAAK Kemahasiswaan!\n\nStatus: Menunggu Persetujuan Admin sebelum dipublikasikan ke katalog mahasiswa.`);
      }
    });
  }

  // ==================== DELETE EVENT (ELEGANT MODAL UX) ====================
  const deleteConfirmModal = document.getElementById('deleteConfirmModal');
  const deleteEventTitleText = document.getElementById('deleteEventTitleText');
  const btnCancelDelete = document.getElementById('btnCancelDelete');
  const btnConfirmDelete = document.getElementById('btnConfirmDelete');
  let eventToDeleteId = null;

  function closeDeleteModal() {
    if (deleteConfirmModal) deleteConfirmModal.classList.remove('open');
    eventToDeleteId = null;
  }

  if (btnCancelDelete) btnCancelDelete.addEventListener('click', closeDeleteModal);
  if (deleteConfirmModal) {
    deleteConfirmModal.addEventListener('click', (e) => {
      if (e.target === deleteConfirmModal) closeDeleteModal();
    });
  }

  window.deleteEvent = function(id) {
    const ev = panitiaEvents.find(e => e.id == id);
    if (!ev) return;
    eventToDeleteId = id;

    if (deleteEventTitleText) {
      deleteEventTitleText.innerHTML = `Apakah Anda yakin ingin menghapus kegiatan <strong>"${ev.title}"</strong>?<br><span style="font-size:0.8rem; color:#94a3b8; display:block; margin-top:0.4rem;">Seluruh data pendaftar, tiket, dan presensi untuk kegiatan ini akan dihapus secara permanen.</span>`;
    }

    if (deleteConfirmModal) {
      deleteConfirmModal.classList.add('open');
    }
  };

  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener('click', () => {
      if (!eventToDeleteId) return;
      const id = eventToDeleteId;
      const ev = panitiaEvents.find(e => e.id == id);

      if (backendDashboard) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        btnConfirmDelete.disabled = true;
        btnConfirmDelete.textContent = 'Menghapus...';

        fetch(`/panitia/event/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          }
        })
        .then(res => {
          btnConfirmDelete.disabled = false;
          btnConfirmDelete.textContent = 'Ya, Hapus';
          if (res.ok) {
            panitiaEvents = panitiaEvents.filter(e => e.id != id);
            renderTable();
            closeDeleteModal();
            if (typeof window.showToast === 'function') {
              window.showToast(`Kegiatan "${ev ? ev.title : 'Event'}" berhasil dihapus.`, 'success', 'Kegiatan Dihapus');
            } else {
              alert(`Kegiatan "${ev ? ev.title : 'Event'}" berhasil dihapus.`);
            }
          } else {
            alert('Gagal menghapus kegiatan dari server.');
          }
        })
        .catch(err => {
          btnConfirmDelete.disabled = false;
          btnConfirmDelete.textContent = 'Ya, Hapus';
          console.error(err);
          alert('Terjadi kesalahan koneksi.');
        });
      } else {
        panitiaEvents = panitiaEvents.filter(e => e.id != id);
        saveEvents();
        renderTable();
        closeDeleteModal();
        if (typeof window.showToast === 'function') {
          window.showToast(`Kegiatan "${ev ? ev.title : 'Event'}" berhasil dihapus.`, 'success', 'Kegiatan Dihapus');
        } else {
          alert(`Kegiatan "${ev ? ev.title : 'Event'}" berhasil dihapus.`);
        }
      }
    });
  }

  // Initial Run
  renderTable();
});
