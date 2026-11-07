/**
 * TEMU UMRAH - Sistem Toast & In-App Notification Resmi (FTTK UMRAH)
 * Serta Manajemen Sesi Pengguna Terpusat (UC-02, UC-15, UC-16, Sequence & Activity Diagrams)
 */

(function() {
  // =========================================================================
  // 1. SISTEM NOTIFIKASI TOAST RESMI
  // =========================================================================
  window.showToast = function(msg, type = 'info', title = '') {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      document.body.appendChild(container);
    }

    const icons = {
      success: '<svg class="icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>',
      danger: '<svg class="icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      warning: '<svg class="icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
      info: '<svg class="icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#005696" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };

    const defaultTitles = {
      success: 'Berhasil',
      danger: 'Perhatian',
      warning: 'Peringatan',
      info: 'Informasi Sistem'
    };

    const item = document.createElement('div');
    item.className = `toast-item toast-${type}`;
    item.innerHTML = `
      <div class="toast-icon">${icons[type] || icons.info}</div>
      <div class="toast-content">
        <div class="toast-title">${title || defaultTitles[type]}</div>
        <div>${msg}</div>
      </div>
      <button class="toast-close" title="Tutup" onclick="this.parentElement.remove()">&times;</button>
    `;

    container.appendChild(item);

    setTimeout(() => {
      item.classList.add('toast-hiding');
      setTimeout(() => item.remove(), 350);
    }, 4000);
  };

  // Intercept window.alert agar TIDAK PERNAH muncul kotak dialog abu-abu browser
  window.alert = function(msg) {
    if (!msg) return;
    const lower = msg.toLowerCase();
    let type = 'info';
    let title = 'Pemberitahuan Sistem';

    if (lower.includes('berhasil') || lower.includes('sukses') || lower.includes('sah') || lower.includes('selesai') || lower.includes('disalin')) {
      type = 'success';
      title = 'Aksi Berhasil';
    } else if (lower.includes('gagal') || lower.includes('tolak') || lower.includes('salah') || lower.includes('kadaluwarsa') || lower.includes('kedaluwarsa')) {
      type = 'danger';
      title = 'Gagal Diproses';
    } else if (lower.includes('mohon') || lower.includes('wajib') || lower.includes('harus') || lower.includes('peringatan') || lower.includes('antrean')) {
      type = 'warning';
      title = 'Peringatan Formulir';
    }

    window.showToast(msg, type, title);
  };

  // Helper salin teks
  window.copyToClipboard = function(text, label = 'Teks') {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(() => {
        window.showToast(`${label} berhasil disalin ke clipboard!`, 'success');
      }).catch(() => {
        window.showToast(`Salin: ${text}`, 'info');
      });
    } else {
      window.showToast(`Salin: ${text}`, 'info');
    }
  };

  // =========================================================================
  // 2. MANAJEMEN SESI PENGGUNA TERPADU (UC-02, UC-15, UC-16)
  // =========================================================================
  
  // Ambil data session saat ini
  window.getCurrentUser = function() {
    try {
      const raw = localStorage.getItem('temu_user') || localStorage.getItem('temu_umrah_session');
      if (!raw) return null;
      return JSON.parse(raw);
    } catch(e) {
      return null;
    }
  };

  // Dialog Modal Konfirmasi Logout (Activity Diagram UC-16 & Sequence Diagram UC-16)
  window.confirmLogoutModal = function() {
    let modal = document.getElementById('modalLogoutUniversal');
    if (!modal) {
      modal = document.createElement('div');
      modal.id = 'modalLogoutUniversal';
      modal.className = 'modal-universal-backdrop';
      modal.innerHTML = `
        <div class="modal-universal-box">
          <div style="width: 58px; height: 58px; margin: 0 auto 1.25rem; border-radius: 50%; background: #FEE2E2; display: flex; align-items: center; justify-content: center; color: #DC2626;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
          </div>
          <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--umrah-navy, #002847); margin: 0 0 0.5rem 0;">Konfirmasi Keluar Akun</h3>
          <p style="font-size: 0.92rem; color: var(--text-muted, #64748B); line-height: 1.5; margin: 0 0 1.75rem 0;">
            Apakah Anda yakin ingin mengakhiri sesi aktif dan keluar dari portal <strong>TEMU UMRAH</strong>?
          </p>
          <div style="display: flex; gap: 0.85rem; justify-content: center;">
            <button type="button" class="btn btn-outline" style="flex: 1; padding: 0.65rem 1rem; border-radius: var(--radius-md, 8px); font-weight: 600; cursor: pointer;" onclick="window.closeLogoutModal()">Batal</button>
            <button type="button" class="btn btn-danger" style="flex: 1; padding: 0.65rem 1rem; border-radius: var(--radius-md, 8px); font-weight: 700; background: #DC2626; color: #fff; border: none; cursor: pointer;" onclick="window.executeLogoutUniversal()">Ya, Keluar</button>
          </div>
        </div>
      `;
      document.body.appendChild(modal);
    }
    modal.classList.add('active');
  };

  // Tutup Modal Logout
  window.closeLogoutModal = function() {
    const modal = document.getElementById('modalLogoutUniversal');
    if (modal) modal.classList.remove('active');
    const modalProfil = document.getElementById('modalLogout');
    if (modalProfil) modalProfil.classList.remove('active');
  };

  // Terminasi Sesi & Render Tampilan Tamu (UC-16 / Sequence Step 4-9)
  window.executeLogoutUniversal = function() {
    if (window.TEMU_LARAVEL_APP) {
      const logoutForm = document.querySelector('form[action$="/logout"]');
      if (logoutForm) {
        logoutForm.submit();
      } else {
        window.location.href = '/login';
      }
      return;
    }

    window.closeLogoutModal();
    localStorage.removeItem('temu_user');
    localStorage.removeItem('temu_umrah_session');

    window.showToast('Logout Berhasil! Sesi Anda telah diakhiri.', 'success', 'Keluar Akun');

    // Segera render ulang navigasi menjadi status Pengunjung Tamu
    window.syncSessionNav();

    // Cek apakah halaman saat ini adalah halaman privat yang membutuhkan login
    const path = window.location.pathname.toLowerCase();
    const privatePages = ['/profil', '/event-saya', '/panitia/', '/admin/', '/pembayaran'];
    const isPrivate = privatePages.some(p => path.endsWith(p));

    if (isPrivate || path !== '/') {
      setTimeout(() => {
        window.location.href = '/';
      }, 500);
    }
  };

  // Sinkronisasi status navbar secara dinamis
  window.syncSessionNav = function() {
    if (window.TEMU_LARAVEL_APP) return;
    const user = window.getCurrentUser();
    const navActions = document.querySelector('.nav-actions');
    if (!navActions) return;

    // Cari atau buat wadah dinamis
    let userArea = document.getElementById('userNavArea');
    if (!userArea) {
      // Periksa apakah ada user-profile-pill statis lama
      const oldPill = navActions.querySelector('.user-profile-pill');
      if (oldPill) {
        userArea = document.createElement('div');
        userArea.id = 'userNavArea';
        userArea.style.display = 'inline-flex';
        userArea.style.alignItems = 'center';
        oldPill.replaceWith(userArea);
      } else {
        // Cek login button lama
        const oldBtn = navActions.querySelector('#navLoginBtn, .btn-nav-login');
        if (oldBtn) {
          userArea = document.createElement('div');
          userArea.id = 'userNavArea';
          userArea.style.display = 'inline-flex';
          userArea.style.alignItems = 'center';
          oldBtn.replaceWith(userArea);
        }
      }
    }

    if (!userArea) return;

    if (!user) {
      // =====================================================================
      // STATE 1: PENGUNJUNG TAMU (GUEST / BELUM LOGIN / SETELAH LOGOUT)
      // Navigasi tamu hanya memuat akses masuk dan aktivasi akun.
      // =====================================================================
      userArea.innerHTML = `
        <a href="/login" class="btn-nav-login" id="navLoginBtn" title="Masuk ke Portal TEMU UMRAH">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          <span>Masuk / Aktivasi</span>
        </a>
      `;

      // Jika ada lonceng notifikasi, saat diklik oleh tamu berikan info ramah
      const notifBtn = document.getElementById('notifBtn');
      if (notifBtn) {
        notifBtn.onclick = function(e) {
          if (!window.getCurrentUser()) {
            e.preventDefault();
            e.stopPropagation();
            window.showToast('Silakan masuk ke akun Anda terlebih dahulu untuk mengakses pemberitahuan pribadi.', 'info', 'Akses Masuk Diperlukan');
          }
        };
      }
    } else {
      // =====================================================================
      // STATE 2: PENGGUNA TERAUTENTIKASI (MAHASISWA / PANITIA / ADMIN)
      // =====================================================================
      const rawName = user.name || user.nama || 'Pengguna';
      const firstName = rawName.split(' ')[0];
      const initial = rawName.charAt(0).toUpperCase();
      const role = (user.role || 'mahasiswa').toLowerCase();
      const roleDisplay = role === 'admin' ? 'Super Admin' : (role === 'panitia' ? 'Panitia' : 'Mahasiswa');
      const profileLink = role === 'admin' ? '/admin/panel' : (role === 'panitia' ? '/panitia/dashboard' : '/profil');

      userArea.innerHTML = `
        <div style="display:inline-flex; align-items:center; gap:0.45rem;">
          <a href="${profileLink}" class="user-profile-pill" title="Profil ${rawName}">
            <div class="user-avatar">${initial}</div>
            <div class="user-meta">
              <span class="user-name">${firstName}</span>
              <span class="user-role">${roleDisplay}</span>
            </div>
          </a>
          <button type="button" class="btn-nav-logout" onclick="window.confirmLogoutModal()" title="Keluar dari Akun (Logout)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </button>
        </div>
      `;
    }
  };

  // Jalankan inisialisasi saat DOM siap
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.syncSessionNav);
  } else {
    window.syncSessionNav();
  }

  // Sinkronisasi lintas tab browser
  window.addEventListener('storage', function(e) {
    if (e.key === 'temu_user' || e.key === 'temu_umrah_session') {
      window.syncSessionNav();
    }
  });

})();
