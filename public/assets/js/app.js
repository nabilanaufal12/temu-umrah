// ==========================================================================
// TEMU UMRAH - CLIENT-SIDE APPLICATION LOGIC
// Realtime Filtering, Search, Modal Detail, Notification Drawer
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  // Load public events from reactive store or fallback
  const initialEvents = Array.isArray(window.TEMU_SERVER_EVENTS)
    ? window.TEMU_SERVER_EVENTS
    : (typeof window.TemuStore !== 'undefined' && typeof window.TemuStore.getPublicEvents === 'function')
      ? window.TemuStore.getPublicEvents()
      : [];

  // Application State
  const state = {
    searchQuery: '',
    selectedCategory: 'SEMUA',
    selectedFee: 'ALL',
    sortBy: 'newest',
    notifications: [],
    events: initialEvents
  };

  // Reactive Cross-Tab Listener for Newly Approved Events from Admin BAAK
  if (typeof window.TemuStore !== 'undefined' && typeof window.TemuStore.onEvent === 'function') {
    window.TemuStore.onEvent((evt) => {
      if (evt.type === 'EVENT_APPROVED') {
        state.events = window.TemuStore.getPublicEvents();
        renderEvents();
        if (typeof window.showToast === 'function') {
          window.showToast(`Kegiatan baru telah dibuka pendaftarannya: "${evt.payload.event?.title || 'Event'}"!`, 'info', 'Katalog Diperbarui');
        }
      }
    });
  }

  // DOM Elements
  const gridContainer = document.getElementById('eventsGrid');
  const searchInput = document.getElementById('heroSearchInput');
  const searchCategorySelect = document.getElementById('heroCategorySelect');
  const searchFeeSelect = document.getElementById('heroFeeSelect');
  const searchBtn = document.getElementById('heroSearchBtn');
  const sortSelect = document.getElementById('sortSelect');
  const categoryChips = document.querySelectorAll('.chip-btn');
  const eventCounterText = document.getElementById('eventCounterText');

  // Notification Drawer Elements
  const notifBtn = document.getElementById('notifBtn');
  const drawerBackdrop = document.getElementById('drawerBackdrop');
  const drawerCloseBtn = document.getElementById('drawerCloseBtn');
  const notifListContainer = document.getElementById('notifListContainer') || document.getElementById('notifListBody');
  const notifBadgeCounter = document.getElementById('notifBadgeCounter');
  const markAllReadBtn = document.getElementById('markAllReadBtn') || document.getElementById('btnMarkAllRead');

  function normalizeServerNotification(item) {
    return {
      id: Number(item.id),
      tag: String(item.kategori || 'SISTEM').toUpperCase(),
      title: item.judul || 'Pembaruan TEMU UMRAH',
      desc: item.pesan || '',
      time: item.created_at || 'Baru saja',
      unread: !Boolean(item.dibaca)
    };
  }

  async function loadServerNotifications() {
    if (!window.TEMU_AUTHENTICATED || !window.TEMU_NOTIFICATIONS_URL) return;

    try {
      const response = await fetch(window.TEMU_NOTIFICATIONS_URL, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin'
      });
      if (!response.ok) return;
      const payload = await response.json();
      if (payload.status === 'success' && Array.isArray(payload.data)) {
        state.notifications = payload.data.map(normalizeServerNotification);
        renderNotifications();
      }
    } catch (error) {
      // Drawer tetap menampilkan empty state bila API sedang tidak tersedia.
    }
  }

  // Detail Modal Elements
  const modalBackdrop = document.getElementById('modalBackdrop');
  const modalCloseBtn = document.getElementById('modalCloseBtn');
  const modalContentBody = document.getElementById('modalContentBody');

  // Icons Helper
  function getCategorySvg(iconCode) {
    const svgs = {
      brain: '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.04z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.04z"/></svg>',
      layout: '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>',
      code: '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
      'shopping-bag': '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
      anchor: '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="5" r="3"/><line x1="12" y1="22" x2="12" y2="8"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/></svg>',
      server: '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>'
    };
    return svgs[iconCode] || svgs.layout;
  }

  // Render Event Cards Function
  function renderEvents() {
    if (!gridContainer) return;
    let filtered = state.events.filter(event => {
      // Text Match
      const q = state.searchQuery.toLowerCase().trim();
      const matchQuery = !q || 
        event.title.toLowerCase().includes(q) ||
        event.ormawa.toLowerCase().includes(q) ||
        event.desc.toLowerCase().includes(q) ||
        event.location.toLowerCase().includes(q);

      // Category Match
      const matchCategory = state.selectedCategory === 'SEMUA' || 
        event.category.toUpperCase() === state.selectedCategory.toUpperCase();

      // Fee Match
      let matchFee = true;
      if (state.selectedFee === 'FREE') {
        matchFee = event.price === 0;
      } else if (state.selectedFee === 'PAID') {
        matchFee = event.price > 0;
      }

      return matchQuery && matchCategory && matchFee;
    });

    // Sort Logic
    if (state.sortBy === 'quota') {
      filtered.sort((a, b) => (b.quotaFilled / b.quotaMax) - (a.quotaFilled / a.quotaMax));
    } else if (state.sortBy === 'price_asc') {
      filtered.sort((a, b) => a.price - b.price);
    } else if (state.sortBy === 'title_asc') {
      filtered.sort((a, b) => a.title.localeCompare(b.title));
    } else {
      // Default newest
      filtered.sort((a, b) => b.id - a.id);
    }

    // Update Counter
    if (eventCounterText) {
      eventCounterText.textContent = `Menampilkan ${filtered.length} kegiatan aktif`;
    }

    // Empty State Check
    if (filtered.length === 0) {
      gridContainer.innerHTML = `
        <div class="empty-state fade-in">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            <line x1="8" y1="11" x2="14" y2="11"/>
          </svg>
          <h3>Tidak Ada Kegiatan Ditemukan</h3>
          <p>Coba gunakan kata kunci lain atau ubah filter kategori dan biaya.</p>
        </div>
      `;
      return;
    }

    // Render Cards
    gridContainer.innerHTML = filtered.map(ev => {
      const quotaPct = Math.round((ev.quotaFilled / ev.quotaMax) * 100);
      const isAlmostFull = quotaPct >= 85;
      const isFree = ev.price === 0;

      return `
        <div class="event-card fade-in" data-id="${ev.id}">
          <div class="card-banner-wrapper" style="background: ${ev.bannerGradient}; position: relative; overflow: hidden;">
            ${ev.bannerPath ? `<img src="${ev.bannerPath}" alt="${ev.title}" class="card-banner-img" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit: cover;">` : `
            <div class="card-banner-art" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; opacity: 0.28;">
              ${getCategorySvg(ev.iconCode)}
            </div>`}
            <div class="card-banner-badges">
              <span class="badge badge-category">${ev.category}</span>
              <span class="badge ${isFree ? 'badge-free' : 'badge-paid'}">${ev.priceLabel}</span>
            </div>
          </div>
          
          <div class="card-content">
            <div class="card-ormawa">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
              </svg>
              <span>${ev.ormawa}</span>
            </div>
            
            <h3 class="card-title" title="${ev.title}">${ev.title}</h3>
            
            <ul class="card-meta-list">
              <li class="card-meta-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>${ev.date} • ${ev.time}</span>
              </li>
              <li class="card-meta-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
                <span>${ev.location}</span>
              </li>
            </ul>

            <div class="card-quota-bar">
              <div class="quota-label-row">
                <span>Kuota Terisi</span>
                <span>${ev.quotaFilled} / ${ev.quotaMax} Peserta (${quotaPct}%)</span>
              </div>
              <div class="progress-track">
                <div class="progress-fill ${isAlmostFull ? 'almost-full' : ''}" style="width: ${quotaPct}%"></div>
              </div>
            </div>

            <div class="card-footer-cta">
              <a href="/event/${encodeURIComponent(ev.slug || ev.id)}" class="btn btn-outline btn-sm btn-detail" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">Lihat Detail</a>
              <a href="/event/${encodeURIComponent(ev.slug || ev.id)}" class="btn btn-primary btn-sm btn-register" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">Daftar</a>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  // Update Category Counts in Chips
  function updateChipCounts() {
    categoryChips.forEach(chip => {
      const cat = chip.getAttribute('data-category');
      let count = 0;
      if (cat === 'SEMUA') {
        count = state.events.length;
      } else {
        count = state.events.filter(e => e.category.toUpperCase() === cat.toUpperCase()).length;
      }
      const countSpan = chip.querySelector('.chip-count');
      if (countSpan) countSpan.textContent = count;
    });
  }

  // Event Listeners for Filters
  categoryChips.forEach(chip => {
    chip.addEventListener('click', () => {
      categoryChips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      state.selectedCategory = chip.getAttribute('data-category');
      if (searchCategorySelect) {
        searchCategorySelect.value = state.selectedCategory;
      }
      renderEvents();
    });
  });

  if (searchBtn) {
    searchBtn.addEventListener('click', (e) => {
      e.preventDefault();
      state.searchQuery = searchInput ? searchInput.value : '';
      state.selectedCategory = searchCategorySelect ? searchCategorySelect.value : state.selectedCategory;
      state.selectedFee = searchFeeSelect ? searchFeeSelect.value : 'ALL';

      // Sync chips
      categoryChips.forEach(c => {
        if (c.getAttribute('data-category') === state.selectedCategory) {
          c.classList.add('active');
        } else {
          c.classList.remove('active');
        }
      });

      renderEvents();
      // Smooth scroll to catalog toolbar
      document.getElementById('katalogArea').scrollIntoView({ behavior: 'smooth' });
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      state.searchQuery = e.target.value;
      renderEvents();
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', (e) => {
      state.sortBy = e.target.value;
      renderEvents();
    });
  }

  // Notification Drawer Handling
  let currentNotifFilter = 'ALL';
  const tabNotifAllBtn = document.getElementById('tabNotifAll');
  const tabNotifUnreadBtn = document.getElementById('tabNotifUnread');
  const tabAllCountEl = document.getElementById('tabNotifAllCount');
  const tabUnreadCountEl = document.getElementById('tabNotifUnreadCount');

  if (tabNotifAllBtn && tabNotifUnreadBtn) {
    tabNotifAllBtn.addEventListener('click', () => {
      currentNotifFilter = 'ALL';
      tabNotifAllBtn.classList.add('active');
      tabNotifUnreadBtn.classList.remove('active');
      renderNotifications();
    });
    tabNotifUnreadBtn.addEventListener('click', () => {
      currentNotifFilter = 'UNREAD';
      tabNotifUnreadBtn.classList.add('active');
      tabNotifAllBtn.classList.remove('active');
      renderNotifications();
    });
  }

  function renderNotifications() {
    const unreadCount = state.notifications.filter(n => n.unread).length;
    const totalCount = state.notifications.length;

    if (notifBadgeCounter) {
      notifBadgeCounter.textContent = unreadCount;
      notifBadgeCounter.style.display = unreadCount > 0 ? 'flex' : 'none';
    }

    const sipaBadge = document.getElementById('sipaNotifBadge') || document.querySelector('.sipa-notif-badge');
    if (sipaBadge) {
      sipaBadge.style.display = unreadCount > 0 ? 'block' : 'none';
    }

    if (tabAllCountEl) tabAllCountEl.textContent = totalCount;
    if (tabUnreadCountEl) tabUnreadCountEl.textContent = unreadCount;

    if (notifListContainer) {
      let filtered = state.notifications;
      if (currentNotifFilter === 'UNREAD') {
        filtered = state.notifications.filter(n => n.unread);
      }

      if (filtered.length === 0) {
        notifListContainer.innerHTML = `
          <div class="notif-empty-state" style="padding: 2.5rem 1.5rem; text-align: center; color: var(--text-muted);">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" style="margin: 0 auto 0.75rem; display: block;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <strong style="color: var(--text-main); display: block; margin-bottom: 0.25rem;">${currentNotifFilter === 'UNREAD' ? 'Semua Notifikasi Telah Dibaca' : 'Belum Ada Notifikasi Baru'}</strong>
            <p style="font-size: 0.8rem; margin: 0;">Pengumuman kegiatan, verifikasi tiket, dan sertifikat akan muncul di sini.</p>
          </div>
        `;
      } else {
        notifListContainer.innerHTML = filtered.map(n => `
          <div class="notif-item ${n.unread ? 'unread' : ''}" onclick="markNotificationRead(${n.id})">
            <div class="notif-top">
              <span class="badge ${n.tag === 'SERTIFIKAT' ? 'notif-tag-purple' : (n.tag === 'PRESENSI' ? 'notif-tag-blue' : 'notif-tag-green')}" style="font-size:0.68rem; padding:0.15rem 0.5rem;">${n.tag}</span>
              ${n.unread ? '<span class="unread-indicator-dot" title="Belum dibaca"></span>' : ''}
            </div>
            <h4 class="notif-title" style="margin: 0.35rem 0 0.2rem 0; font-size: 0.88rem; font-weight: 700; color: var(--text-main);">${n.title}</h4>
            <p class="notif-desc" style="margin: 0 0 0.35rem 0; font-size: 0.78rem; color: var(--text-muted); line-height: 1.4;">${n.desc}</p>
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span class="notif-time" style="font-size: 0.72rem; color: #94a3b8;">${n.time}</span>
              <span style="font-size: 0.72rem; font-weight: 700; color: var(--umrah-blue);">Tandai Dibaca &rarr;</span>
            </div>
          </div>
        `).join('');
      }
    }
  }

  window.openNotificationDrawer = function() {
    const backdrop = document.getElementById('drawerBackdrop');
    if (backdrop) backdrop.classList.add('open');
  };

  window.closeNotificationDrawer = function() {
    const backdrop = document.getElementById('drawerBackdrop');
    if (backdrop) backdrop.classList.remove('open');
  };

  window.markNotificationRead = function(id) {
    const notif = state.notifications.find(n => n.id === id);
    if (notif) {
      notif.unread = false;
      renderNotifications();
      if (window.TEMU_NOTIFICATION_READ_URL) {
        fetch(window.TEMU_NOTIFICATION_READ_URL.replace('__ID__', id), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            Accept: 'application/json'
          },
          credentials: 'same-origin'
        }).catch(() => {});
      }
    }
  };

  if (notifBtn) {
    notifBtn.addEventListener('click', () => {
      window.openNotificationDrawer();
    });
  }

  const sipaNotifBtn = document.getElementById('sipaNotifBtn');
  if (sipaNotifBtn) {
    sipaNotifBtn.addEventListener('click', () => {
      window.openNotificationDrawer();
    });
  }

  if (drawerCloseBtn) {
    drawerCloseBtn.addEventListener('click', () => {
      window.closeNotificationDrawer();
    });
  }

  const drawerFooterClose = document.getElementById('drawerFooterClose');
  if (drawerFooterClose) {
    drawerFooterClose.addEventListener('click', () => {
      window.closeNotificationDrawer();
    });
  }

  if (drawerBackdrop) {
    drawerBackdrop.addEventListener('click', (e) => {
      if (e.target === drawerBackdrop) {
        window.closeNotificationDrawer();
      }
    });
  }

  if (markAllReadBtn) {
    markAllReadBtn.addEventListener('click', () => {
      state.notifications.forEach(n => n.unread = false);
      renderNotifications();
      if (window.TEMU_NOTIFICATION_READ_ALL_URL) {
        fetch(window.TEMU_NOTIFICATION_READ_ALL_URL, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            Accept: 'application/json'
          },
          credentials: 'same-origin'
        }).catch(() => {});
      }
    });
  }

  // Quick Detail Modal 
  window.openDetailModal = function(id) {
    const ev = state.events.find(e => e.id === id);
    if (!ev) return;

    modalContentBody.innerHTML = `
      <div class="modal-hero-cover" style="background: ${ev.bannerGradient}; position: relative; overflow: hidden;">
        ${ev.bannerPath ? `<img src="${ev.bannerPath}" alt="${ev.title}" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit: cover; opacity: 0.35;">` : ''}
        <div style="position: relative; z-index: 2;">
          <span class="badge ${ev.price === 0 ? 'badge-free' : 'badge-paid'}" style="margin-bottom: 0.5rem;">${ev.priceLabel}</span>
          <h2 style="color: #fff; font-size: 1.35rem; line-height: 1.25;">${ev.title}</h2>
        </div>
      </div>
      <div class="modal-body">
        <div style="margin-bottom: 1.25rem;">
          <p style="font-size: 0.85rem; color: var(--umrah-blue); font-weight: 700;">Diselenggarakan oleh: ${ev.ormawa}</p>
          <p style="font-size: 0.85rem; color: var(--text-body); margin-top: 0.25rem;"><strong>Narasumber:</strong> ${ev.speaker}</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; background: var(--bg-surface-alt); padding: 0.9rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.82rem;">
          <div><strong>Tanggal:</strong> ${ev.date}</div>
          <div><strong>Waktu:</strong> ${ev.time}</div>
          <div style="grid-column: 1 / -1;"><strong>Lokasi:</strong> ${ev.location}</div>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <h4 style="font-size: 0.95rem; margin-bottom: 0.4rem;">Deskripsi Kegiatan</h4>
          <p style="font-size: 0.85rem; color: var(--text-body); line-height: 1.5;">${ev.desc}</p>
        </div>

        <div style="margin-bottom: 1.5rem;">
          <h4 style="font-size: 0.95rem; margin-bottom: 0.4rem;">Fasilitas & Manfaat Peserta</h4>
          <ul style="padding-left: 1.25rem; font-size: 0.82rem; color: var(--text-body);">
            ${ev.benefits.map(b => `<li style="margin-bottom: 0.25rem;">${b}</li>`).join('')}
          </ul>
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end; border-top: 1px solid var(--border-light); padding-top: 1rem;">
          <button class="btn btn-outline btn-sm" onclick="closeModal()">Tutup</button>
          <button class="btn btn-primary btn-sm" onclick="handleRegisterClick(${ev.id})">Lanjut Mendaftar</button>
        </div>
      </div>
    `;

    modalBackdrop.classList.add('open');
  };

  window.closeModal = function() {
    modalBackdrop.classList.remove('open');
  };

  if (modalCloseBtn) {
    modalCloseBtn.addEventListener('click', closeModal);
  }
  if (modalBackdrop) {
    modalBackdrop.addEventListener('click', (e) => {
      if (e.target === modalBackdrop) closeModal();
    });
  }

  // Registration Handler
  window.handleRegisterClick = function(id) {
    const event = state.events.find(item => item.id === id) || {};
    const target = event.slug ? `/event/${encodeURIComponent(event.slug)}` : `/event/${id}`;
    window.location.href = target;
  };

  // Initial Run
  
  // User Session Checker (Integrated with Universal Session Manager UC-02, UC-16)
  function checkUserSession() {
    if (window.syncSessionNav) {
      window.syncSessionNav();
    }
  }

  checkUserSession();

  updateChipCounts();
  renderEvents();
  renderNotifications();
  loadServerNotifications();
});

// ==================== GITHUB-STYLE PROFILE DROPDOWN (NAVBAR) ====================
window.toggleNavUserDropdown = function(event) {
  if (event) {
    event.stopPropagation();
  }
  const wrap = document.getElementById('navUserDropdownWrap');
  const menu = document.getElementById('navUserDropdownMenu');
  const btn = document.getElementById('navProfilePillBtn');
  if (!wrap || !menu) return;

  const isOpen = wrap.classList.contains('open');
  if (isOpen) {
    wrap.classList.remove('open');
    menu.classList.remove('open');
    if (btn) btn.setAttribute('aria-expanded', 'false');
  } else {
    wrap.classList.add('open');
    menu.classList.add('open');
    if (btn) btn.setAttribute('aria-expanded', 'true');
  }
};

window.closeNavUserDropdown = function() {
  const wrap = document.getElementById('navUserDropdownWrap');
  const menu = document.getElementById('navUserDropdownMenu');
  const btn = document.getElementById('navProfilePillBtn');
  if (wrap) wrap.classList.remove('open');
  if (menu) menu.classList.remove('open');
  if (btn) btn.setAttribute('aria-expanded', 'false');
};

document.addEventListener('click', function(e) {
  const wrap = document.getElementById('navUserDropdownWrap');
  if (wrap && wrap.classList.contains('open')) {
    if (!wrap.contains(e.target)) {
      window.closeNavUserDropdown();
    }
  }
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    window.closeNavUserDropdown();
    if (window.closeSipaUserDropdown) window.closeSipaUserDropdown();
    if (window.closeNotificationDrawer) window.closeNotificationDrawer();
  }
});

