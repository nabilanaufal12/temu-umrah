// ==========================================================================
// TEMU UMRAH - NOTIFICATION DRAWER CLIENT LOGIC
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  // Laravel menjadi sumber data utama; cache lokal hanya dipakai ketika API
  // sedang tidak tersedia.
  let notifications = JSON.parse(localStorage.getItem('temu_notifs') || 'null') || [];

  let currentTab = 'ALL'; // 'ALL' or 'UNREAD'

  // DOM Elements
  const listContainer = document.getElementById('notifListBody');
  const badgeCounter = document.getElementById('notifBadgeCounter');
  const tabAllBtn = document.getElementById('tabNotifAll');
  const tabUnreadBtn = document.getElementById('tabNotifUnread');
  const tabAllCount = document.getElementById('tabNotifAllCount');
  const tabUnreadCount = document.getElementById('tabNotifUnreadCount');
  const btnMarkAll = document.getElementById('btnMarkAllRead');
  const drawerCloseBtn = document.getElementById('drawerCloseBtn');
  const drawerBackdrop = document.getElementById('drawerBackdrop');
  const notifBtn = document.getElementById('notifBtn');

  function saveState() {
    localStorage.setItem('temu_notifs', JSON.stringify(notifications));
  }

  function normalizeNotification(item) {
    const category = String(item.kategori || item.tag || 'SISTEM').toUpperCase();
    const colorMap = {
      PEMBAYARAN: 'green',
      PRESENSI: 'blue',
      SERTIFIKAT: 'purple',
      PENGINGAT: 'gold'
    };

    // Auto map actionUrl based on notification category if not explicitly provided
    let targetUrl = item.actionUrl || null;
    if (!targetUrl) {
      if (category === 'SERTIFIKAT') targetUrl = '/sertifikat';
      else if (category === 'PRESENSI') targetUrl = '/presensi';
      else if (category === 'PEMBAYARAN' || category === 'TIKET') targetUrl = '/event-saya';
      else targetUrl = '/event-saya';
    }

    return {
      id: Number(item.id),
      tag: category,
      tagColor: colorMap[category] || 'blue',
      title: item.judul || item.title || 'Pembaruan TEMU UMRAH',
      desc: item.pesan || item.desc || '',
      time: item.created_at || item.time || 'Baru saja',
      unread: item.dibaca === undefined ? Boolean(item.unread) : !Boolean(item.dibaca),
      actionUrl: targetUrl
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
        notifications = payload.data.map(normalizeNotification);
        saveState();
        renderList();
      }
    } catch (error) {
      // Cache lokal tetap ditampilkan saat API tidak dapat dijangkau.
    }
  }

  function getTagClass(color) {
    if (color === 'green') return 'notif-tag-green';
    if (color === 'blue') return 'notif-tag-blue';
    if (color === 'gold') return 'notif-tag-gold';
    if (color === 'purple') return 'notif-tag-purple';
    return 'notif-tag-blue';
  }

  function renderList() {
    const unreadCount = notifications.filter(n => n.unread).length;
    const totalCount = notifications.length;

    // Update Badges
    if (badgeCounter) {
      badgeCounter.textContent = unreadCount;
      badgeCounter.style.display = unreadCount > 0 ? 'flex' : 'none';
    }
    if (tabAllCount) tabAllCount.textContent = totalCount;
    if (tabUnreadCount) tabUnreadCount.textContent = unreadCount;

    // Filter Items
    let displayItems = notifications;
    if (currentTab === 'UNREAD') {
      displayItems = notifications.filter(n => n.unread);
    }

    // Empty State (14A)
    if (displayItems.length === 0) {
      listContainer.innerHTML = `
        <div class="notif-empty-state fade-in">
          <div class="notif-empty-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
          </div>
          <h4>Belum Ada Notifikasi Baru untuk Anda</h4>
          <p>Semua pengumuman pendaftaran, pembayaran, absensi, dan sertifikat akan muncul secara otomatis di sini.</p>
        </div>
      `;
      return;
    }

    listContainer.innerHTML = displayItems.map(n => `
      <div class="notif-item ${n.unread ? 'unread' : ''} fade-in" onclick="handleNotificationClick(${n.id})">
        <div class="notif-top">
          <span class="badge ${getTagClass(n.tagColor)}" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">${n.tag}</span>
          ${n.unread ? '<span class="unread-indicator-dot" title="Belum dibaca"></span>' : ''}
        </div>
        <h4 class="notif-title" style="margin-top: 0.35rem;">${n.title}</h4>
        <p class="notif-desc">${n.desc}</p>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
          <span class="notif-time">${n.time}</span>
          <span style="font-size: 0.72rem; font-weight: 700; color: var(--umrah-blue);">Buka Menu &rarr;</span>
        </div>
      </div>
    `).join('');
  }

  // Handle Click on Specific Notification (Mark as read & Deep Link)
  window.handleNotificationClick = function(id) {
    const item = notifications.find(n => n.id === id);
    if (item) {
      item.unread = false;
      saveState();
      renderList();
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
      if (item.actionUrl) {
        window.location.href = item.actionUrl;
      }
    }
  };

  // Tab Switching
  if (tabAllBtn && tabUnreadBtn) {
    tabAllBtn.addEventListener('click', () => {
      currentTab = 'ALL';
      tabAllBtn.classList.add('active');
      tabUnreadBtn.classList.remove('active');
      renderList();
    });

    tabUnreadBtn.addEventListener('click', () => {
      currentTab = 'UNREAD';
      tabUnreadBtn.classList.add('active');
      tabAllBtn.classList.remove('active');
      renderList();
    });
  }

  // Mark All Read (14B)
  if (btnMarkAll) {
    btnMarkAll.addEventListener('click', () => {
      notifications.forEach(n => n.unread = false);
      saveState();
      renderList();
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

  // Drawer Toggle
  if (drawerCloseBtn) {
    drawerCloseBtn.addEventListener('click', () => {
      if (drawerBackdrop) drawerBackdrop.classList.remove('open');
    });
  }
  if (notifBtn) {
    notifBtn.addEventListener('click', () => {
      if (drawerBackdrop) drawerBackdrop.classList.add('open');
    });
  }

  // Initial Run
  renderList();
  loadServerNotifications();
});
