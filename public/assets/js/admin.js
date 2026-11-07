// ==========================================================================
// TEMU UMRAH - SUPER ADMIN PANEL CLIENT LOGIC (BAAK KEMAHASISWAAN)
// Modul Verifikasi Ormawa (SK & Kuota 2 Panitia), Moderasi Event Kampus,
// Manajemen Mahasiswa SIPA, dan Audit Trail Webhook Gateway
// ==========================================================================

let allOrmawaData = [];
let ormawaQueue = [];
let allEventsData = [];
let eventsQueue = [];
let studentsData = [];
let activeOrmawaId = null;
let activeReviewEventId = null;
let activeOrmawaFilter = 'ALL';
let activeEventFilter = 'ALL';
let activeUserFilter = 'ALL';
const backendAdmin = window.TEMU_ADMIN_BACKEND === true;

function adminRoute(name, id) {
  return (window.TEMU_ADMIN_ROUTES?.[name] || '').replace('__ID__', String(id));
}

async function adminRequest(name, id, data = {}) {
  const response = await fetch(adminRoute(name, id), {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    body: JSON.stringify(data)
  });
  const payload = await response.json();
  if (!response.ok) throw new Error(payload.message || 'Aksi Admin gagal diproses.');
  return payload;
}

// ============================================================================
// DATA MAHASISWA SIPA UMRAH
// ============================================================================
if (backendAdmin && Array.isArray(window.TEMU_ADMIN_STUDENTS)) {
  studentsData = window.TEMU_ADMIN_STUDENTS;
} else {
  studentsData = [
    { id: 1, nim: '2401020073', nama: 'Nabil', email: '2401020073@student.umrah.ac.id', prodi: 'Teknik Informatika', fakultas: 'FTTK UMRAH', status: 'Aktif', statusRaw: 'aktif', statusLabel: 'Aktif Terverifikasi', badgeClass: 'badge-success' },
    { id: 2, nim: '2401020110', nama: 'Yehezkiel Alman', email: '2401020110@student.umrah.ac.id', prodi: 'Teknik Informatika', fakultas: 'FTTK UMRAH', status: 'Aktif', statusRaw: 'aktif', statusLabel: 'Aktif Terverifikasi', badgeClass: 'badge-success' },
    { id: 3, nim: '2401020069', nama: 'Balqis Manisa', email: '2401020069@student.umrah.ac.id', prodi: 'Teknik Informatika', fakultas: 'FTTK UMRAH', status: 'Aktif', statusRaw: 'aktif', statusLabel: 'Aktif Terverifikasi', badgeClass: 'badge-success' }
  ];
}

// ============================================================================
// LOG AUDIT TRAIL PLATFORM & WEBHOOK GATEWAY
// ============================================================================
let auditLogsData = [
  { time: '2026-11-06 20:30:10', cat: 'MODERATION', text: 'Admin BAAK memeriksa antrean pengajuan event & ormawa: status=SYNCED' },
  { time: '2026-11-06 18:35:45', cat: 'GATEWAY', text: 'Webhook Settlement: order_id=ORD-2026-0902 channel=QRIS_DINAMIS amount=Rp25.000 signature=VALID status=settlement' },
  { time: '2026-11-06 17:20:45', cat: 'ATTENDANCE', text: 'Presensi QR TOTP terverifikasi: nim=2401020073 status=HADIR event_id=7' },
  { time: '2026-11-06 16:15:02', cat: 'AUTH', text: 'Login SSO Mahasiswa: nim=2401020073 (Nabil) status=SUCCESS ip=10.10.2.14' },
  { time: '2026-11-06 14:26:30', cat: 'CERTIFICATE', text: 'Penerbitan batch sertifikat digital Canva: event_id=7 status=COMPLETED' },
  { time: '2026-11-06 11:38:12', cat: 'AUTH', text: 'Autentikasi Panitia Ormawa: ormawa=HMTI status=VERIFIED' },
  { time: '2026-11-06 09:12:00', cat: 'MODERATION', text: 'Verifikasi SK Ormawa: ormawa="HMTI" no_sk="SK-DEK/FTTK-UMRAH/2026/001" status=VERIFIED' }
];

let activeLogCat = 'ALL';

function loadAdminData() {
  if (backendAdmin) {
    allOrmawaData = Array.isArray(window.TEMU_ADMIN_ALL_ORMAWA) ? window.TEMU_ADMIN_ALL_ORMAWA : [];
    ormawaQueue = Array.isArray(window.TEMU_ADMIN_ORMAWA) ? window.TEMU_ADMIN_ORMAWA : [];
    allEventsData = Array.isArray(window.TEMU_ADMIN_ALL_EVENTS) ? window.TEMU_ADMIN_ALL_EVENTS : [];
    eventsQueue = Array.isArray(window.TEMU_ADMIN_EVENTS) ? window.TEMU_ADMIN_EVENTS : [];
    return;
  }

  allOrmawaData = JSON.parse(localStorage.getItem('temu_all_ormawa') || '[]');
  ormawaQueue = JSON.parse(localStorage.getItem('temu_ormawa_queue') || '[]');
  allEventsData = JSON.parse(localStorage.getItem('temu_all_events') || '[]');
  eventsQueue = JSON.parse(localStorage.getItem('temu_events_moderation') || '[]');
}

loadAdminData();

function switchAdminTab(tabName) {
  const validTabs = ['dashboard', 'ormawa', 'events', 'users', 'logs', 'profil'];
  if (!validTabs.includes(tabName)) tabName = 'dashboard';

  // 1. Update sidebar menu items
  document.querySelectorAll('.admin-sidebar .sipa-menu-item').forEach(item => item.classList.remove('active'));
  document.querySelectorAll('.admin-sidebar .sipa-menu-link').forEach(link => link.classList.remove('active'));

  const activeSideItem = document.getElementById(`adminSideItem_${tabName}`);
  if (activeSideItem) {
    activeSideItem.classList.add('active');
    const activeSideLink = activeSideItem.querySelector('.sipa-menu-link');
    if (activeSideLink) activeSideLink.classList.add('active');
  }

  // 2. Show selected tab pane
  document.querySelectorAll('.admin-tab-pane').forEach(pane => pane.style.display = 'none');
  const activePane = document.getElementById(`tabPane_${tabName}`);
  if (activePane) activePane.style.display = 'block';

  // 3. Update URL hash without page reload or jumping
  if (history.replaceState) {
    history.replaceState(null, '', '#' + tabName);
  } else {
    location.hash = '#' + tabName;
  }

  // 4. Update breadcrumb
  const breadcrumbEl = document.getElementById('adminBreadcrumbCurrent');
  if (breadcrumbEl) {
    const titles = {
      'dashboard': 'Ruang Kendali',
      'ormawa': 'Verifikasi Ormawa',
      'events': 'Moderasi Event',
      'users': 'Kelola Mahasiswa',
      'logs': 'Audit Log Sistem',
      'profil': 'Profil & Keamanan Admin'
    };
    breadcrumbEl.textContent = titles[tabName] || 'Ruang Kendali';
  }

  // 5. Scroll smoothly to top
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function switchAdminProfilSubTab(subTab) {
  if (typeof switchProfilTab === 'function') {
    switchProfilTab(subTab);
  }
}

function initAdminFromHash() {
  const hash = (window.location.hash || '').replace('#', '').trim();
  if (hash && ['dashboard', 'ormawa', 'events', 'users', 'logs', 'profil'].includes(hash)) {
    switchAdminTab(hash);
  } else {
    switchAdminTab('dashboard');
  }
}

document.addEventListener('DOMContentLoaded', function() {
  renderOrmawaTable();
  renderEventsTable();
  filterUsersTable();
  renderAuditLogs('ALL');
  initAdminFromHash();
});

window.addEventListener('hashchange', function() {
  initAdminFromHash();
});

window.jumpToAdminFilter = function(tabName, filter) {
  switchAdminTab(tabName);
  if (tabName === 'ormawa') {
    if (filter) setOrmawaStatusFilter(filter);
    const searchInput = document.getElementById('searchOrmawaInput');
    if (searchInput) searchInput.focus();
  } else if (tabName === 'events') {
    if (filter) setEventStatusFilter(filter);
    const searchInput = document.getElementById('searchEventInput');
    if (searchInput) searchInput.focus();
  } else if (tabName === 'users') {
    if (filter) setUserStatusFilter(filter);
    const searchInput = document.getElementById('searchUserInput');
    if (searchInput) searchInput.focus();
  }
};

// ============================================================================
// 1. MODUL VERIFIKASI ORMAWA & BATASAN 2 PANITIA AKTIF
// ============================================================================
function setOrmawaStatusFilter(filter) {
  activeOrmawaFilter = filter;
  document.querySelectorAll('#ormawaFilterPills .ormawa-pill').forEach(btn => {
    if (btn.getAttribute('data-filter') === filter) {
      btn.classList.add('btn-primary', 'active');
      btn.classList.remove('btn-outline');
    } else {
      btn.classList.remove('btn-primary', 'active');
      btn.classList.add('btn-outline');
    }
  });
  filterOrmawaTable();
}

function filterOrmawaTable() {
  const query = (document.getElementById('searchOrmawaInput')?.value || '').toLowerCase().trim();
  let list = allOrmawaData;

  if (activeOrmawaFilter !== 'ALL') {
    list = list.filter(o => o.status === activeOrmawaFilter);
  }

  if (query) {
    list = list.filter(o =>
      (o.nama && o.nama.toLowerCase().includes(query)) ||
      (o.noSk && o.noSk.toLowerCase().includes(query)) ||
      (o.ketua && o.ketua.toLowerCase().includes(query)) ||
      (o.email && o.email.toLowerCase().includes(query))
    );
  }

  renderOrmawaRows(list);
}

function renderOrmawaTable() {
  // Update badges
  const pendingCount = allOrmawaData.filter(o => o.status === 'pending').length;
  const verifiedCount = allOrmawaData.filter(o => o.status === 'verified').length;

  const badgeOrmawa = document.getElementById('badgeOrmawaCount');
  if (badgeOrmawa) {
    badgeOrmawa.textContent = pendingCount;
    if (pendingCount > 0) badgeOrmawa.classList.add('text-danger');
    else badgeOrmawa.classList.remove('text-danger');
  }

  const badgeTab = document.getElementById('badgeTabCount');
  if (badgeTab) {
    badgeTab.textContent = pendingCount;
    if (pendingCount > 0) {
      badgeTab.classList.add('badge-alert');
      badgeTab.classList.remove('badge-neutral');
    } else {
      badgeTab.classList.remove('badge-alert');
      badgeTab.classList.add('badge-neutral');
    }
  }

  filterOrmawaTable();
}

function renderOrmawaRows(list) {
  const tbody = document.getElementById('ormawaQueueBody');
  if (!tbody) return;

  if (!list || list.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748B;">
          <div style="font-size: 1.05rem; margin-bottom: 0.35rem; font-weight:700; color:var(--umrah-navy);">Data Organisasi Tidak Ditemukan</div>
          <div style="font-size: 0.82rem;">Tidak ada organisasi mahasiswa yang sesuai dengan filter atau pencarian saat ini.</div>
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = list.map((o, idx) => {
    const panitiaCount = o.panitiaCount || (o.panitiaList ? o.panitiaList.length : 1);
    const maxPanitia = o.kuotaPanitiaMaks || 2;
    const quotaBadge = panitiaCount >= maxPanitia
      ? '<span class="badge" style="background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; font-size:0.72rem;">Kuota Penuh (2/2)</span>'
      : `<span class="badge" style="background:#EFF6FF; color:#005696; border:1px solid #BFDBFE; font-size:0.72rem;">${panitiaCount}/${maxPanitia} Akun Panitia</span>`;

    let statusBadge = '';
    if (o.status === 'pending') {
      statusBadge = '<span class="badge badge-warning" style="background:#FFFBEB; color:#B45309; border:1px solid #FCD34D; font-size:0.75rem;">Menunggu Verifikasi</span>';
    } else if (o.status === 'verified') {
      statusBadge = '<span class="badge badge-success" style="background:#ECFDF5; color:#047857; border:1px solid #A7F3D0; font-size:0.75rem;">Terverifikasi Sah</span>';
    } else {
      statusBadge = '<span class="badge badge-danger" style="background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; font-size:0.75rem;">Ditolak</span>';
    }

    let actionButtons = '';
    if (o.status === 'pending') {
      actionButtons = `
        <div style="display:flex; gap:0.4rem;">
          <button class="btn btn-sm btn-success" style="background:#059669; color:white; border:none; padding:0.35rem 0.65rem; font-size:0.78rem; display:inline-flex; align-items:center; gap:0.25rem;" onclick="approveOrmawaDirect(${o.id})">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Setujui
          </button>
          <button class="btn btn-sm btn-danger" style="background:#EF4444; color:white; border:none; padding:0.35rem 0.65rem; font-size:0.78rem; display:inline-flex; align-items:center; gap:0.25rem;" onclick="openSkModal(${o.id}, true)">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tolak
          </button>
        </div>
      `;
    } else {
      actionButtons = `
        <button class="btn btn-sm btn-outline" style="padding:0.35rem 0.65rem; font-size:0.78rem; display:inline-flex; align-items:center; gap:0.3rem;" onclick="openSkModal(${o.id})">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
          Tinjau SK
        </button>
      `;
    }

    return `
      <tr>
        <td style="font-weight: 700; color: #64748B;">${idx + 1}</td>
        <td>
          <div style="font-weight: 700; color: var(--umrah-navy); font-size:0.9rem;">${o.nama}</div>
          <div style="font-size: 0.78rem; color: #64748B;">${o.kategori} &bull; ${o.email}</div>
          <div style="margin-top:0.25rem;">${quotaBadge}</div>
        </td>
        <td>
          <div style="font-size: 0.82rem; font-weight: 600;">Ketua: ${o.ketua}</div>
          <div style="font-size: 0.75rem; color: #64748B;">Pembina: ${o.pembina || '-'}</div>
        </td>
        <td>
          <button class="btn btn-outline btn-sm" style="font-size:0.78rem; padding:0.3rem 0.6rem; display:inline-flex; align-items:center; gap:0.25rem;" onclick="openSkModal(${o.id})">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            ${o.noSk}
          </button>
        </td>
        <td>
          ${statusBadge}
          <div style="font-size:0.72rem; color:#64748B; margin-top:0.2rem;">${o.tglDaftar || 'Terdaftar'}</div>
        </td>
        <td>
          ${actionButtons}
        </td>
      </tr>
    `;
  }).join('');
}

function openSkModal(id, focusReject = false) {
  const o = allOrmawaData.find(item => item.id === id) || ormawaQueue.find(item => item.id === id);
  if (!o) return;

  activeOrmawaId = id;
  if (document.getElementById('modalSkNama')) document.getElementById('modalSkNama').textContent = o.nama;
  if (document.getElementById('modalSkKategori')) document.getElementById('modalSkKategori').textContent = o.kategori;
  if (document.getElementById('modalSkNo')) document.getElementById('modalSkNo').textContent = o.noSk;
  if (document.getElementById('modalSkKetua')) document.getElementById('modalSkKetua').textContent = o.ketua;
  if (document.getElementById('modalSkPembina')) document.getElementById('modalSkPembina').textContent = o.pembina || '-';
  if (document.getElementById('modalSkEmail')) document.getElementById('modalSkEmail').textContent = o.email || '-';
  if (document.getElementById('modalSkDocName')) document.getElementById('modalSkDocName').textContent = o.dokumen;

  const panitiaCount = o.panitiaCount || (o.panitiaList ? o.panitiaList.length : 1);
  const kuotaEl = document.getElementById('modalSkKuotaPanitia');
  if (kuotaEl) {
    kuotaEl.textContent = `${panitiaCount} / 2 Akun Panitia Aktif (Maksimal 2 Akun per Ormawa)`;
  }

  const modal = document.getElementById('modalSkVerif');
  if (modal) modal.classList.add('active');

  if (focusReject) {
    setTimeout(() => {
      const rejectBtn = modal.querySelector('.btn-danger');
      if (rejectBtn) rejectBtn.focus();
    }, 100);
  }
}

function closeSkModal() {
  const modal = document.getElementById('modalSkVerif');
  if (modal) modal.classList.remove('active');
  activeOrmawaId = null;
}

function approveOrmawaDirect(id) {
  const o = allOrmawaData.find(item => item.id === id) || ormawaQueue.find(item => item.id === id);
  if (!o) return;

  if (backendAdmin) {
    adminRequest('approveOrmawa', id)
      .then(() => {
        o.status = 'verified';
        renderOrmawaTable();

        const nowStr = new Date().toISOString().replace('T', ' ').substring(0, 19);
        auditLogsData.unshift({
          time: nowStr,
          cat: 'AUTH',
          text: `Verifikasi SK Ormawa Berhasil: ormawa="${o.nama}" no_sk="${o.noSk}" status=VERIFIED oleh=Admin_BAAK`
        });
        renderAuditLogs(activeLogCat);

        showAdminToast(`Organisasi "${o.nama}" BERHASIL DISETUJUI dan akun panitia diaktifkan.`);
      })
      .catch(error => showAdminToast(error.message, 'warning'));
    return;
  }

  o.status = 'verified';
  renderOrmawaTable();
  showAdminToast(`Organisasi "${o.nama}" BERHASIL DISETUJUI! Kredensial akun panitia telah diterbitkan ke ${o.email}.`);
}

function approveModalCurrent() {
  if (!activeOrmawaId) return;
  const id = activeOrmawaId;
  closeSkModal();
  approveOrmawaDirect(id);
}

function rejectModalCurrent() {
  if (!activeOrmawaId) return;
  const o = allOrmawaData.find(item => item.id === activeOrmawaId) || ormawaQueue.find(item => item.id === activeOrmawaId);
  if (!o) return;

  const id = activeOrmawaId;
  closeSkModal();
  openAdminRejectModal('ormawa', id, o.nama, 'Dokumen SK belum ditandatangani Dekan / Rektor.');
}

function executeRejectOrmawa(id, alasan) {
  const o = allOrmawaData.find(item => item.id === id) || ormawaQueue.find(item => item.id === id);
  if (!o) return;

  if (backendAdmin) {
    adminRequest('rejectOrmawa', id, { alasan })
      .then(() => {
        o.status = 'rejected';
        renderOrmawaTable();

        const nowStr = new Date().toISOString().replace('T', ' ').substring(0, 19);
        auditLogsData.unshift({
          time: nowStr,
          cat: 'AUTH',
          text: `Penolakan Pendaftaran Ormawa: ormawa="${o.nama}" alasan="${alasan}" oleh=Admin_BAAK`
        });
        renderAuditLogs(activeLogCat);

        showAdminToast(`Permohonan "${o.nama}" DITOLAK: ${alasan}`, 'warning');
      })
      .catch(error => showAdminToast(error.message, 'warning'));
    return;
  }

  o.status = 'rejected';
  renderOrmawaTable();
  showAdminToast(`Permohonan "${o.nama}" DITOLAK: ${alasan}`, 'warning');
}

// ============================================================================
// 2. MODUL VERIFIKASI & MODERASI EVENT KAMPUS
// ============================================================================
function setEventStatusFilter(filter) {
  activeEventFilter = filter;
  document.querySelectorAll('#eventFilterPills .event-pill').forEach(btn => {
    if (btn.getAttribute('data-filter') === filter) {
      btn.classList.add('btn-primary', 'active');
      btn.classList.remove('btn-outline');
    } else {
      btn.classList.remove('btn-primary', 'active');
      btn.classList.add('btn-outline');
    }
  });
  filterEventsTable();
}

function filterEventsTable() {
  const query = (document.getElementById('searchEventInput')?.value || '').toLowerCase().trim();
  let list = allEventsData;

  if (activeEventFilter !== 'ALL') {
    list = list.filter(e => e.status === activeEventFilter);
  }

  if (query) {
    list = list.filter(e =>
      (e.title && e.title.toLowerCase().includes(query)) ||
      (e.ormawa && e.ormawa.toLowerCase().includes(query)) ||
      (e.category && e.category.toLowerCase().includes(query)) ||
      (e.location && e.location.toLowerCase().includes(query))
    );
  }

  renderEventsRows(list);
}

function renderEventsTable() {
  const pendingCount = allEventsData.filter(e => e.status === 'pending_approval').length;

  const statPending = document.getElementById('statPendingEvents');
  if (statPending) {
    statPending.textContent = pendingCount;
    if (pendingCount > 0) statPending.classList.add('text-danger');
    else statPending.classList.remove('text-danger');
  }

  const badgeEvent = document.getElementById('badgeEventCount');
  if (badgeEvent) {
    badgeEvent.textContent = pendingCount;
    if (pendingCount > 0) {
      badgeEvent.classList.add('badge-alert');
      badgeEvent.classList.remove('badge-neutral');
    } else {
      badgeEvent.classList.remove('badge-alert');
      badgeEvent.classList.add('badge-neutral');
    }
  }

  filterEventsTable();
}

function renderEventsRows(list) {
  const tbody = document.getElementById('eventsQueueBody');
  if (!tbody) return;

  if (!list || list.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748B;">
          <div style="font-size: 1.05rem; margin-bottom: 0.35rem; font-weight:700; color:var(--umrah-navy);">Kegiatan Tidak Ditemukan</div>
          <div style="font-size: 0.82rem;">Tidak ada event yang sesuai dengan filter atau kata kunci pencarian.</div>
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = list.map((ev, idx) => {
    let statusBadge = '';
    if (ev.status === 'pending_approval') {
      statusBadge = '<span class="badge badge-warning" style="background:#FFFBEB; color:#B45309; border:1px solid #FCD34D; font-size:0.75rem;">Menunggu Review</span>';
    } else if (ev.status === 'published') {
      statusBadge = '<span class="badge badge-success" style="background:#ECFDF5; color:#047857; border:1px solid #A7F3D0; font-size:0.75rem;">Dipublikasikan</span>';
    } else if (ev.status === 'ditolak') {
      statusBadge = '<span class="badge badge-danger" style="background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA; font-size:0.75rem;">Perlu Revisi</span>';
    } else {
      statusBadge = '<span class="badge" style="background:#F1F5F9; color:#475569; font-size:0.75rem;">Selesai</span>';
    }

    let actionButtons = '';
    if (ev.status === 'pending_approval') {
      actionButtons = `
        <div style="display:flex; gap:0.35rem; flex-wrap:wrap;">
          <button class="btn btn-sm btn-outline" style="padding:0.35rem 0.55rem; font-size:0.78rem;" onclick="openEventDetailModal(${ev.id})">
            Tinjau
          </button>
          <button class="btn btn-sm btn-success" style="background:#059669; color:white; border:none; padding:0.35rem 0.55rem; font-size:0.78rem; display:inline-flex; align-items:center; gap:0.2rem;" onclick="approveEventDirect(${ev.id})">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Setujui
          </button>
          <button class="btn btn-sm btn-danger" style="background:#EF4444; color:white; border:none; padding:0.35rem 0.55rem; font-size:0.78rem; display:inline-flex; align-items:center; gap:0.2rem;" onclick="rejectEventDirect(${ev.id})">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tolak
          </button>
        </div>
      `;
    } else {
      actionButtons = `
        <div style="display:flex; gap:0.35rem; align-items:center;">
          <button class="btn btn-sm btn-outline" style="padding:0.35rem 0.6rem; font-size:0.78rem;" onclick="openEventDetailModal(${ev.id})">
            Detail
          </button>
          ${ev.status === 'published' ? `<a href="/event/${ev.id}" target="_blank" class="btn btn-sm btn-outline" style="padding:0.35rem 0.6rem; font-size:0.78rem; color:#0284C7; text-decoration:none;" title="Pratinjau Halaman Publik Event di Katalog Mahasiswa">Pratinjau Web &rarr;</a>` : ''}
        </div>
      `;
    }

    return `
      <tr>
        <td style="font-weight: 700; color: #64748B;">${idx + 1}</td>
        <td>
          <div style="font-weight: 700; color: var(--umrah-navy); font-size: 0.9rem;">${ev.title}</div>
          <div style="font-size: 0.78rem; color: #64748B;">
            Penyelenggara: <strong>${ev.ormawa || 'Panitia Mahasiswa'}</strong>
          </div>
        </td>
        <td>
          <span class="badge" style="background:#EFF6FF; color:#005696; border:1px solid #BFDBFE; font-size:0.72rem; margin-bottom:0.25rem;">${ev.category}</span><br>
          <span class="badge ${ev.price === 0 ? 'badge-free' : 'badge-paid'}" style="font-size:0.72rem;">${ev.priceLabel || (ev.price === 0 ? 'Gratis' : 'Rp ' + Number(ev.price).toLocaleString('id-ID'))}</span>
        </td>
        <td>
          <div style="font-size: 0.82rem; font-weight: 600;">${ev.date}</div>
          <div style="font-size: 0.75rem; color: #64748B;">${ev.location}</div>
        </td>
        <td style="font-size: 0.82rem; font-weight: 700;">
          ${ev.quotaMax || 100} Kursi
        </td>
        <td>
          ${statusBadge}
          <div style="font-size:0.72rem; color:#64748B; margin-top:0.2rem;">${ev.submittedAt || 'Tersimpan'}</div>
        </td>
        <td>
          ${actionButtons}
        </td>
      </tr>
    `;
  }).join('');
}

function openEventDetailModal(id) {
  const ev = allEventsData.find(e => e.id === id) || eventsQueue.find(e => e.id === id);
  if (!ev) return;
  activeReviewEventId = id;

  if (document.getElementById('medJudul')) document.getElementById('medJudul').textContent = ev.title;
  if (document.getElementById('medDesc')) document.getElementById('medDesc').textContent = ev.desc || 'Kegiatan pengembangan akademik dan kompetensi mahasiswa maritim UMRAH.';
  if (document.getElementById('medOrmawa')) document.getElementById('medOrmawa').textContent = ev.ormawa || 'Panitia Mahasiswa';
  if (document.getElementById('medKategoriBiaya')) {
    const fee = ev.price === 0 ? 'Gratis' : (ev.priceLabel || `Rp ${Number(ev.price).toLocaleString('id-ID')}`);
    document.getElementById('medKategoriBiaya').textContent = `${ev.category} • ${fee}`;
  }
  if (document.getElementById('medJadwal')) document.getElementById('medJadwal').textContent = `${ev.date} (${ev.time || '08:00 WIB - Selesai'})`;
  if (document.getElementById('medLokasi')) document.getElementById('medLokasi').textContent = ev.location || 'Auditorium UMRAH Dompak';
  if (document.getElementById('medKuota')) document.getElementById('medKuota').textContent = `${ev.quotaMax || 100} Kursi Peserta`;
  if (document.getElementById('medCert')) {
    document.getElementById('medCert').textContent = ev.hasCert !== false ? 'Tersedia (Sertifikat Elektronik SKP)' : 'Tanpa Sertifikat';
  }
  if (document.getElementById('medDocName')) {
    const rawOrmawa = (ev.ormawa || 'Ormawa').trim();
    const docEl = document.getElementById('medDocName');
    docEl.textContent = `Proposal_${rawOrmawa.replace(/\s+/g, '_')}_2026.pdf`;
    docEl.title = `Proposal_${rawOrmawa.replace(/\s+/g, '_')}_2026.pdf`;
  }

  const modal = document.getElementById('modalEventDetailAdmin');
  if (modal) modal.classList.add('active');
}

function closeEventDetailModal() {
  const modal = document.getElementById('modalEventDetailAdmin');
  if (modal) modal.classList.remove('active');
  activeReviewEventId = null;
}

function approveFromDetailModal() {
  if (!activeReviewEventId) return;
  const id = activeReviewEventId;
  closeEventDetailModal();
  approveEventDirect(id);
}

function rejectFromDetailModal() {
  if (!activeReviewEventId) return;
  const id = activeReviewEventId;
  closeEventDetailModal();
  rejectEventDirect(id);
}

function approveEventDirect(id) {
  const ev = allEventsData.find(item => item.id === id) || eventsQueue.find(item => item.id === id);
  if (!ev) return;

  if (backendAdmin) {
    adminRequest('approveEvent', id)
      .then(() => {
        ev.status = 'published';
        renderEventsTable();

        const nowStr = new Date().toISOString().replace('T', ' ').substring(0, 19);
        auditLogsData.unshift({
          time: nowStr,
          cat: 'MODERATION',
          text: `Persetujuan Publikasi Event: id=${ev.id} title="${ev.title}" ormawa="${ev.ormawa}" status=published oleh=Admin_BAAK`
        });
        renderAuditLogs(activeLogCat);

        showAdminToast(`Kegiatan "${ev.title}" BERHASIL DISETUJUI & DIPUBLIKASIKAN! Kegiatan kini aktif di katalog mahasiswa.`);
      })
      .catch(error => showAdminToast(error.message, 'warning'));
    return;
  }

  ev.status = 'published';
  renderEventsTable();
  showAdminToast(`Kegiatan "${ev.title}" BERHASIL DISETUJUI & DIPUBLIKASIKAN!`);
}

function rejectFromDetailModal() {
  if (!activeReviewEventId) return;
  const id = activeReviewEventId;
  closeEventDetailModal();
  rejectEventDirect(id);
}

function rejectEventDirect(id) {
  const ev = allEventsData.find(e => e.id === id) || eventsQueue.find(e => e.id === id);
  if (!ev) return;

  openAdminRejectModal('event', id, ev.title, 'Jadwal berbenturan dengan agenda universitas / kelengkapan proposal belum terpenuhi.');
}

function executeRejectEvent(id, alasan) {
  const ev = allEventsData.find(e => e.id === id) || eventsQueue.find(e => e.id === id);
  if (!ev) return;

  if (backendAdmin) {
    adminRequest('rejectEvent', id, { alasan })
      .then(() => {
        ev.status = 'ditolak';
        ev.catatanAdmin = alasan;
        renderEventsTable();

        const nowStr = new Date().toISOString().replace('T', ' ').substring(0, 19);
        auditLogsData.unshift({
          time: nowStr,
          cat: 'MODERATION',
          text: `Penolakan Event: id=${ev.id} title="${ev.title}" alasan="${alasan}" oleh=Admin_BAAK`
        });
        renderAuditLogs(activeLogCat);

        showAdminToast(`Pengajuan "${ev.title}" DITOLAK. Catatan revisi berhasil diteruskan ke panitia.`, 'warning');
      })
      .catch(error => showAdminToast(error.message, 'warning'));
    return;
  }

  ev.status = 'ditolak';
  ev.catatanAdmin = alasan;
  renderEventsTable();
  showAdminToast(`Pengajuan "${ev.title}" DITOLAK. Catatan revisi diteruskan ke panitia.`, 'warning');
}

// ============================================================================
// 3. MANAJEMEN AKUN MAHASISWA SIPA UMRAH
// ============================================================================
function setUserStatusFilter(filter) {
  activeUserFilter = filter;
  document.querySelectorAll('#userFilterPills .user-pill').forEach(btn => {
    if (btn.getAttribute('data-filter') === filter) {
      btn.classList.add('btn-primary', 'active');
      btn.classList.remove('btn-outline');
    } else {
      btn.classList.remove('btn-primary', 'active');
      btn.classList.add('btn-outline');
    }
  });
  filterUsersTable();
}

function filterUsersTable() {
  const q = (document.getElementById('searchUserInput')?.value || '').toLowerCase().trim();
  let list = studentsData;

  // Update counter badges
  const totalCount = studentsData.length;
  const activeCount = studentsData.filter(s => s.statusRaw === 'aktif' || s.status === 'Aktif').length;
  const suspendedCount = totalCount - activeCount;

  const countAllEl = document.getElementById('userCountAll');
  if (countAllEl) countAllEl.textContent = totalCount;
  const countActiveEl = document.getElementById('userCountActive');
  if (countActiveEl) countActiveEl.textContent = activeCount;
  const countSuspendedEl = document.getElementById('userCountSuspended');
  if (countSuspendedEl) countSuspendedEl.textContent = suspendedCount;

  // Filter status filter pill
  if (activeUserFilter === 'aktif') {
    list = list.filter(s => s.statusRaw === 'aktif' || s.status === 'Aktif');
  } else if (activeUserFilter === 'nonaktif') {
    list = list.filter(s => s.statusRaw !== 'aktif' && s.status !== 'Aktif');
  }

  // Filter text search
  if (q) {
    list = list.filter(s =>
      (s.nim && s.nim.toLowerCase().includes(q)) ||
      (s.nama && s.nama.toLowerCase().includes(q)) ||
      (s.email && s.email.toLowerCase().includes(q)) ||
      (s.prodi && s.prodi.toLowerCase().includes(q)) ||
      (s.fakultas && s.fakultas.toLowerCase().includes(q))
    );
  }

  renderUsersTable(list);
}

function renderUsersTable(list = null) {
  const tbody = document.getElementById('userTableBody');
  if (!tbody) return;

  const data = list !== null ? list : studentsData;
  if (!data || data.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #64748B;">
          <div style="font-size: 1.05rem; margin-bottom: 0.35rem; font-weight:700; color:var(--umrah-navy);">Data Mahasiswa Tidak Ditemukan</div>
          <div style="font-size: 0.82rem;">Tidak ada akun mahasiswa yang sesuai dengan filter atau kata kunci pencarian.</div>
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = data.map(s => {
    const isAktif = s.statusRaw === 'aktif' || s.status === 'Aktif';
    const safeNama = (s.nama || '').replace(/'/g, "\\'");
    const safeNim = (s.nim || '').replace(/'/g, "\\'");

    const statusBadge = isAktif
      ? '<span class="admin-badge admin-badge-success"><span class="admin-badge-dot"></span> Aktif Terverifikasi</span>'
      : '<span class="admin-badge admin-badge-danger"><span class="admin-badge-dot"></span> Ditangguhkan</span>';

    const actionBtn = isAktif
      ? `<button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmToggleUserStatus(${s.id}, '${safeNim}', '${safeNama}', true)" style="font-size:0.78rem; padding:0.35rem 0.75rem;">Tangguhkan</button>`
      : `<button type="button" class="btn btn-success btn-sm" onclick="confirmToggleUserStatus(${s.id}, '${safeNim}', '${safeNama}', false)" style="font-size:0.78rem; padding:0.35rem 0.75rem;">Aktifkan</button>`;

    return `
      <tr>
        <td style="font-family:monospace; font-weight:700; color:var(--umrah-navy); font-size:0.88rem;">${s.nim}</td>
        <td>
          <div style="font-weight:700; color:#0f172a; font-size:0.9rem;">${s.nama}</div>
          <div style="font-size:0.75rem; color:#64748b;">${s.email || '-'}</div>
        </td>
        <td style="font-size:0.85rem; font-weight:600; color:#334155;">${s.prodi}</td>
        <td style="font-size:0.82rem; color:#64748b;">${s.fakultas}</td>
        <td>
          ${statusBadge}
        </td>
        <td style="text-align:center;">
          ${actionBtn}
        </td>
      </tr>
    `;
  }).join('');
}

function confirmToggleUserStatus(id, nim, nama, isCurrentlyActive) {
  const actionTitle = isCurrentlyActive ? 'Tangguhkan Akun Mahasiswa' : 'Aktifkan Kembali Akun Mahasiswa';
  const actionMessage = isCurrentlyActive
    ? `Apakah Anda yakin ingin menangguhkan akses login akun <strong>${nama}</strong> (NIM: <code>${nim}</code>)?`
    : `Apakah Anda yakin ingin mengaktifkan kembali akun <strong>${nama}</strong> (NIM: <code>${nim}</code>)?`;
  const note = isCurrentlyActive
    ? '<span style="color:#b91c1c; font-weight:700;">Dampak:</span> Mahasiswa tidak akan dapat login ke portal TEMU UMRAH sampai diaktifkan kembali oleh Admin BAAK.'
    : '<span style="color:#047857; font-weight:700;">Dampak:</span> Hak akses login mahasiswa ke portal TEMU UMRAH akan langsung dipulihkan.';

  openAdminConfirmModal(actionTitle, actionMessage, note, () => {
    toggleUserStatus(id, nim);
  }, isCurrentlyActive);
}

function toggleUserStatus(id, nim) {
  const user = studentsData.find(s => s.id === id || s.nim === nim);
  if (!user) return;

  if (backendAdmin && id) {
    adminRequest('toggleStudent', id)
      .then(res => {
        const isNowAktif = res.new_status === 'aktif';
        user.statusRaw = res.new_status;
        user.status = isNowAktif ? 'Aktif' : 'Ditangguhkan';
        user.statusLabel = isNowAktif ? 'Aktif Terverifikasi' : 'Akun Ditangguhkan';
        user.badgeClass = isNowAktif ? 'badge-success' : 'badge-danger';
        filterUsersTable();

        const nowStr = new Date().toISOString().replace('T', ' ').substring(0, 19);
        auditLogsData.unshift({
          time: nowStr,
          cat: 'AUTH',
          text: `Status Akun Mahasiswa Diperbarui: nim=${user.nim} nama="${user.nama}" status=${res.new_status.toUpperCase()} oleh=Admin_BAAK`
        });
        renderAuditLogs(activeLogCat);

        showAdminToast(res.message, isNowAktif ? 'success' : 'warning');
      })
      .catch(err => showAdminToast(err.message, 'warning'));
    return;
  }

  // Fallback client-only toggle
  const isAktif = user.statusRaw === 'aktif' || user.status === 'Aktif';
  user.statusRaw = isAktif ? 'nonaktif' : 'aktif';
  user.status = isAktif ? 'Ditangguhkan' : 'Aktif';
  user.statusLabel = isAktif ? 'Akun Ditangguhkan' : 'Aktif Terverifikasi';
  user.badgeClass = isAktif ? 'badge-danger' : 'badge-success';
  filterUsersTable();
  showAdminToast(`Status akun mahasiswa ${user.nama} (${user.nim}) berhasil diubah.`, isAktif ? 'warning' : 'success');
}

// ============================================================================
// 4. LOG AUDIT & WEBHOOK TRANSAKSI PLATFORM
// ============================================================================
function renderAuditLogs(cat = 'ALL') {
  const container = document.getElementById('auditLogContainer');
  if (!container) return;

  activeLogCat = cat;
  const filtered = cat === 'ALL' ? auditLogsData : auditLogsData.filter(l => l.cat === cat);

  if (filtered.length === 0) {
    container.innerHTML = `<div style="text-align:center; padding:1.5rem; color:#94A3B8;">Tidak ada log aktivitas untuk kategori [${cat}].</div>`;
    return;
  }

  container.innerHTML = filtered.map(l => {
    let tagColor = '#003E6D';
    if (l.cat === 'GATEWAY') tagColor = '#059669';
    else if (l.cat === 'ATTENDANCE') tagColor = '#D97706';
    else if (l.cat === 'CERTIFICATE') tagColor = '#7C3AED';
    else if (l.cat === 'MODERATION') tagColor = '#2563EB';

    return `<div><span style="color:#64748B;">[${l.time}]</span> <strong style="color:${tagColor};">[${l.cat}]</strong> ${l.text}</div>`;
  }).join('');
}

function filterAuditLogs(cat) {
  document.querySelectorAll('.log-filter-btn').forEach(btn => {
    if (btn.getAttribute('data-cat') === cat) {
      btn.classList.remove('btn-outline');
      btn.classList.add('btn-primary');
    } else {
      btn.classList.remove('btn-primary');
      btn.classList.add('btn-outline');
    }
  });
  renderAuditLogs(cat);
}

function exportAuditLogs() {
  const content = auditLogsData.map(l => `[${l.time}] [${l.cat}] ${l.text}`).join('\r\n');
  const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `audit_log_temu_umrah_baak_${new Date().toISOString().slice(0, 10)}.txt`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
  showAdminToast('Berkas Audit Trail Log Platform (.txt) berhasil diunduh.');
}

// ============================================================================
// 5. TOAST & NOTIFIKASI ADMIN
// ============================================================================
function showAdminToast(msg, type = 'success') {
  const toast = document.createElement('div');
  toast.style.position = 'fixed';
  toast.style.bottom = '2rem';
  toast.style.right = '2rem';
  toast.style.background = type === 'warning' ? '#991B1B' : (type === 'info' ? '#003E6D' : '#0F172A');
  toast.style.color = '#FFFFFF';
  toast.style.padding = '1rem 1.5rem';
  toast.style.borderRadius = '8px';
  toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.3)';
  toast.style.zIndex = '9999';
  toast.style.fontSize = '0.88rem';
  toast.style.fontWeight = '600';
  toast.style.maxWidth = '440px';
  toast.style.display = 'flex';
  toast.style.alignItems = 'center';
  toast.style.gap = '0.75rem';

  const iconSvg = type === 'warning'
    ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#F87171" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
    : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';

  toast.innerHTML = `<div>${iconSvg}</div><div>${msg}</div>`;

  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.4s ease';
    setTimeout(() => toast.remove(), 400);
  }, 4000);
}

// ============================================================================
// 6. IN-APP MODAL HELPERS (REJECT & CONFIRM)
// ============================================================================
let pendingRejectTarget = null;

const REJECT_PRESETS = {
  ormawa: [
    'Dokumen SK belum ditandatangani Dekan / Rektor.',
    'Masa berlaku SK kepengurusan telah berakhir / kadaluarsa.',
    'Format dan resolusi berkas SK buram atau tidak terbaca.',
    'Data kontak penanggung jawab ormawa tidak valid / tidak aktif.'
  ],
  event: [
    'Jadwal berbenturan dengan agenda utama universitas.',
    'Proposal dan rincian teknis kegiatan belum lengkap.',
    'Ketersediaan sarana dan prasarana kampus belum disetujui BAAK.',
    'Deskripsi atau tema acara memerlukan penyesuaian regulasi kampus.'
  ]
};

function openAdminRejectModal(type, id, targetName, defaultReason = '') {
  pendingRejectTarget = { type, id, targetName };

  const modal = document.getElementById('modalAdminReject');
  const titleEl = document.getElementById('adminRejectModalTitle');
  const targetEl = document.getElementById('adminRejectTargetName');
  const inputEl = document.getElementById('adminRejectReasonInput');
  const chipsContainer = document.getElementById('adminRejectChipsContainer');

  if (titleEl) {
    titleEl.textContent = type === 'ormawa'
      ? 'Catatan Penolakan Pendaftaran Ormawa'
      : 'Catatan Penolakan Pengajuan Event';
  }

  if (targetEl) {
    targetEl.textContent = targetName || '-';
  }

  const initialReason = defaultReason || (REJECT_PRESETS[type] && REJECT_PRESETS[type][0]) || '';
  if (inputEl) {
    inputEl.value = initialReason;
    inputEl.style.borderColor = '#cbd5e1';
  }

  if (chipsContainer) {
    const list = REJECT_PRESETS[type] || [];
    chipsContainer.innerHTML = list.map(chipText => {
      const isSelected = chipText === initialReason;
      const safeEscaped = chipText.replace(/"/g, '&quot;').replace(/'/g, "\\'");
      return `<button type="button" class="btn btn-sm ${isSelected ? 'btn-danger' : 'btn-outline'}" 
        style="font-size:0.75rem; padding:0.25rem 0.65rem; border-radius:9999px; font-weight:500; cursor:pointer;" 
        onclick="selectAdminRejectChip('${safeEscaped}')">${chipText}</button>`;
    }).join('');
  }

  if (modal) {
    modal.classList.add('active');
    setTimeout(() => {
      if (inputEl) {
        inputEl.focus();
        inputEl.setSelectionRange(inputEl.value.length, inputEl.value.length);
      }
    }, 120);
  }
}

function selectAdminRejectChip(text) {
  const inputEl = document.getElementById('adminRejectReasonInput');
  if (inputEl) {
    inputEl.value = text;
    inputEl.focus();
  }

  const chips = document.querySelectorAll('#adminRejectChipsContainer button');
  chips.forEach(btn => {
    if (btn.textContent.trim() === text.trim()) {
      btn.className = 'btn btn-sm btn-danger';
    } else {
      btn.className = 'btn btn-sm btn-outline';
    }
  });
}

function closeAdminRejectModal() {
  const modal = document.getElementById('modalAdminReject');
  if (modal) modal.classList.remove('active');
  pendingRejectTarget = null;
}

function submitAdminRejectAction() {
  if (!pendingRejectTarget) {
    closeAdminRejectModal();
    return;
  }

  const inputEl = document.getElementById('adminRejectReasonInput');
  const alasan = inputEl ? inputEl.value.trim() : '';

  if (!alasan) {
    if (inputEl) {
      inputEl.style.borderColor = '#ef4444';
      inputEl.focus();
    }
    showAdminToast('Mohon isi catatan alasan penolakan secara resmi.', 'warning');
    return;
  }

  const { type, id } = pendingRejectTarget;
  closeAdminRejectModal();

  if (type === 'ormawa') {
    executeRejectOrmawa(id, alasan);
  } else if (type === 'event') {
    executeRejectEvent(id, alasan);
  }
}

let pendingConfirmAction = null;

function openAdminConfirmModal(title, message, note, executeCallback, isDanger = false) {
  pendingConfirmAction = executeCallback;
  const titleEl = document.getElementById('adminConfirmModalTitle');
  if (titleEl) titleEl.textContent = title;

  const msgEl = document.getElementById('adminConfirmModalMessage');
  if (msgEl) msgEl.innerHTML = message;

  const noteEl = document.getElementById('adminConfirmNoteBox');
  if (noteEl) noteEl.innerHTML = note;

  const btn = document.getElementById('adminConfirmExecuteBtn');
  if (btn) {
    if (isDanger) {
      btn.className = 'btn btn-danger';
      btn.style.background = '#dc2626';
      btn.style.borderColor = '#dc2626';
      btn.textContent = 'Ya, Tangguhkan Akun';
    } else {
      btn.className = 'btn btn-success';
      btn.style.background = '#059669';
      btn.style.borderColor = '#059669';
      btn.textContent = 'Ya, Aktifkan Akun';
    }
  }
  const modal = document.getElementById('modalAdminConfirm');
  if (modal) modal.classList.add('active');
}

function closeAdminConfirmModal() {
  const modal = document.getElementById('modalAdminConfirm');
  if (modal) modal.classList.remove('active');
  pendingConfirmAction = null;
}

function executeAdminConfirmedAction() {
  if (typeof pendingConfirmAction === 'function') {
    const fn = pendingConfirmAction;
    closeAdminConfirmModal();
    fn();
  } else {
    closeAdminConfirmModal();
  }
}

// Global modal dismiss handlers (Backdrop & Escape)
document.addEventListener('click', function(e) {
  if (e.target && e.target.classList.contains('modal-sk-backdrop')) {
    if (e.target.id === 'modalAdminConfirm') {
      closeAdminConfirmModal();
    } else if (e.target.id === 'modalAdminReject') {
      closeAdminRejectModal();
    } else if (e.target.id === 'modalSkVerif') {
      closeSkModal();
    } else if (e.target.id === 'modalEventDetailAdmin') {
      closeEventDetailModal();
    }
  }
});

window.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const confirmModal = document.getElementById('modalAdminConfirm');
    if (confirmModal && confirmModal.classList.contains('active')) {
      closeAdminConfirmModal();
      return;
    }
    const rejectModal = document.getElementById('modalAdminReject');
    if (rejectModal && rejectModal.classList.contains('active')) {
      closeAdminRejectModal();
      return;
    }
    closeSkModal();
    closeEventDetailModal();
  }
});
