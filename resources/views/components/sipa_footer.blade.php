<!-- resources/views/components/sipa_footer.blade.php -->
<footer class="sipa-footer">
  <span class="sipa-footer-copy">Copyright &copy; UPT.PTIK UMRAH &bull; TEMU UMRAH {{ date('Y') }}.</span>
</footer>

<script>
  // 1. Sidebar Toggle (Responsive: Mobile Drawer vs Desktop Collapse)
  function toggleSipaSidebar() {
    if (window.innerWidth <= 992) {
      document.body.classList.toggle('sipa-mobile-open');
    } else {
      toggleSipaSidebarCollapse();
    }
  }

  function toggleSipaSidebarCollapse() {
    document.body.classList.toggle('sipa-collapsed');
    const isCollapsed = document.body.classList.contains('sipa-collapsed');
    localStorage.setItem('sipa_sidebar_collapsed', isCollapsed ? '1' : '0');
    const btn = document.getElementById('sipaHamburgerBtn');
    if (btn) {
      btn.setAttribute('title', isCollapsed ? 'Lebarkan Menu Samping (Tampilkan Teks)' : 'Ciutkan Menu Samping (Mode Ikon)');
    }
  }

  // 2. User Profile Dropdown Toggle
  window.toggleSipaUserDropdown = function(event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    const wrap = document.getElementById('sipaUserDropdownWrap');
    const menu = document.getElementById('sipaUserDropdownMenu');
    const btn = document.getElementById('sipaUserChipBtn');
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

  window.closeSipaUserDropdown = function() {
    const wrap = document.getElementById('sipaUserDropdownWrap');
    const menu = document.getElementById('sipaUserDropdownMenu');
    const btn = document.getElementById('sipaUserChipBtn');
    if (wrap) wrap.classList.remove('open');
    if (menu) menu.classList.remove('open');
    if (btn) btn.setAttribute('aria-expanded', 'false');
  };

  // 3. Notification Drawer Global Controller
  window.openNotificationDrawer = function() {
    const drawer = document.getElementById('drawerBackdrop');
    if (drawer) {
      drawer.classList.add('open');
    }
  };

  window.closeNotificationDrawer = function() {
    const drawer = document.getElementById('drawerBackdrop');
    if (drawer) {
      drawer.classList.remove('open');
    }
  };

  // Close dropdown on outside click
  document.addEventListener('click', function(e) {
    const wrap = document.getElementById('sipaUserDropdownWrap');
    if (wrap && wrap.classList.contains('open') && !wrap.contains(e.target)) {
      window.closeSipaUserDropdown();
    }
  });

  // Close dropdown & notification drawer on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      window.closeSipaUserDropdown();
      window.closeNotificationDrawer();
    }
  });

  // Restore collapsed preference on load
  document.addEventListener('DOMContentLoaded', function() {
    const isCollapsed = localStorage.getItem('sipa_sidebar_collapsed') === '1' && window.innerWidth > 992;
    if (isCollapsed) {
      document.body.classList.add('sipa-collapsed');
    }
    const btn = document.getElementById('sipaHamburgerBtn');
    if (btn) {
      btn.setAttribute('title', isCollapsed ? 'Lebarkan Menu Samping (Tampilkan Teks)' : 'Ciutkan Menu Samping (Mode Ikon)');
    }
  });
</script>
