<!-- resources/views/components/portal_context.blade.php -->
@if(!request()->is('admin*') && request()->is('panitia*'))
  @php
    $contextTitle = 'Ruang kerja panitia ormawa';
    $contextDescription = 'Kelola event, peserta, presensi, dan sertifikat dari satu ruang kerja.';
  @endphp

  <div class="portal-context-bar">
    <div class="container portal-context-inner">
      <div class="portal-context-copy">
        <span class="portal-context-dot" aria-hidden="true"></span>
        <strong>{{ $contextTitle }}</strong>
        <span>{{ $contextDescription }}</span>
      </div>
      <div class="portal-context-meta">
        <span class="portal-context-status"><span aria-hidden="true">✓</span> Sistem UMRAH aktif</span>
        <a href="https://umrah.ac.id/" target="_blank" rel="noopener">umrah.ac.id ↗</a>
      </div>
    </div>
  </div>
@endif
