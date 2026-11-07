<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <meta name="description" content="@yield('meta_desc', 'TEMU UMRAH - Sistem Informasi Komunitas & Event Mahasiswa Universitas Maritim Raja Ali Haji.')">
  <title>@yield('title', 'TEMU UMRAH - Portal Event Mahasiswa')</title>
  
  <!-- Google Fonts: Plus Jakarta Sans & Manrope (Harmonisasi Tipografi Beranda) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Design System CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/notifikasi.css') }}">
  @if(request()->is('panitia*') && !request()->is('*sesi-absensi*'))
    <link rel="stylesheet" href="{{ asset('assets/css/sipa_layout.css') }}">
  @elseif(request()->is('admin*'))
    <link rel="stylesheet" href="{{ asset('assets/css/sipa_layout.css') }}">
  @endif
  @stack('styles')
</head>
<body class="portal-shell @if((request()->is('panitia*') && !request()->is('*sesi-absensi*')) || request()->is('admin*')) sipa-body @endif @yield('body_class')">
  <script>
    window.TEMU_LARAVEL_APP = true;
    window.TEMU_AUTHENTICATED = @json(auth()->check());
    window.TEMU_NOTIFICATIONS_URL = @json(route('notifikasi.index'));
    window.TEMU_NOTIFICATION_READ_URL = @json(url('/api/notifikasi/__ID__/read'));
    window.TEMU_NOTIFICATION_READ_ALL_URL = @json(route('notifikasi.readAll'));
  </script>

  @if(request()->is('panitia*') && !request()->is('*sesi-absensi*'))
    <!-- ==================== TATA LETAK MENU SAMPING (SIPA UMRAH STYLE) ==================== -->
    @include('components.sipa_topbar')
    @include('components.sipa_sidebar')

    <main class="sipa-main-wrapper">
      @yield('content')
    </main>

    @include('components.sipa_footer')
    @include('components.notification_drawer')

  @elseif(request()->is('admin*'))
    <!-- ==================== TATA LETAK ADMIN BAAK (SIPA ADMIN STYLE) ==================== -->
    @include('components.admin_topbar')
    @include('components.admin_sidebar')

    <main class="sipa-main-wrapper">
      @yield('content')
    </main>

    @include('components.sipa_footer')
    @include('components.notification_drawer')

  @elseif(request()->is('*sesi-absensi*'))
    <!-- Mode Auditorium Layar Penuh (Proyektor) -->
    @yield('content')

  @else
    <!-- ==================== TATA LETAK MAHASISWA / UMUM ==================== -->
    @unless(request()->is('login*') || request()->is('register*') || request()->is('auth*'))
      @include('components.navbar')
      @unless(request()->is('admin*'))
        @include('components.portal_context')
      @endunless
    @endunless

    <!-- Main Content Body -->
    <div class="portal-content-shell">
      @yield('content')
    </div>

    <!-- In-App Notification Drawer Component -->
    @unless(request()->is('notifikasi') || request()->is('login*') || request()->is('register*') || request()->is('auth*'))
      @include('components.notification_drawer')
      @include('components.footer')
    @endunless
  @endif

  <!-- Core JavaScript Assets -->
  <script src="{{ asset('assets/js/app.js') }}"></script>
  <script src="{{ asset('assets/js/toast.js') }}"></script>
  @stack('scripts')
</body>
</html>
