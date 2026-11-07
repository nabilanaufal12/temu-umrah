<!-- resources/views/components/notification_drawer.blade.php -->
<div id="drawerBackdrop" class="drawer-backdrop" onclick="if(event.target === this) closeNotificationDrawer();">
  <aside class="notif-drawer">
    <div class="drawer-header">
      <div>
        <h3>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          Pemberitahuan &amp; Pengingat
        </h3>
        <span style="font-size: 0.72rem; color: var(--text-muted);">Sistem Realtime TEMU UMRAH</span>
      </div>
      <button id="drawerCloseBtn" type="button" class="drawer-close-btn" onclick="closeNotificationDrawer()" title="Tutup Panel">&times;</button>
    </div>

    <!-- Tab Filter -->
    <div class="notif-filter-tabs">
      <button id="tabNotifAll" type="button" class="notif-tab-btn active">
        Semua <span id="tabNotifAllCount" class="notif-tab-badge">0</span>
      </button>
      <button id="tabNotifUnread" type="button" class="notif-tab-btn">
        Belum Dibaca <span id="tabNotifUnreadCount" class="notif-tab-badge">0</span>
      </button>
    </div>

    <div style="padding: 0.65rem 1.25rem 0.25rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-size: 0.74rem; color: var(--text-muted);">Klik notifikasi untuk aksi langsung</span>
      <button id="btnMarkAllRead" type="button" style="background: none; border: none; font-size: 0.76rem; font-weight: 700; color: var(--umrah-blue); cursor: pointer; text-decoration: underline;">
        Tandai Semua Dibaca
      </button>
    </div>

    <div id="notifListBody" class="drawer-body"></div>

    <div class="drawer-footer">
      <button id="drawerFooterClose" type="button" class="btn btn-outline btn-sm" onclick="closeNotificationDrawer()" style="width: 100%;">
        Tutup Panel Pemberitahuan &times;
      </button>
    </div>
  </aside>
</div>
