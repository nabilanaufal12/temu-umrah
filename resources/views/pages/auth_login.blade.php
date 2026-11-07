<!-- resources/views/pages/auth_login.blade.php -->
@extends('layouts.app')

@section('title', 'Masuk ke TEMU UMRAH - Portal Kegiatan Mahasiswa')
@section('meta_desc', 'Masuk ke portal kegiatan mahasiswa Universitas Maritim Raja Ali Haji menggunakan Google Workspace kampus atau NIM.')
@section('body_class', 'auth-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('content')
<main class="auth-modern-wrapper">
  <!-- Minimalist Brand Header (No Distracting Public Navbar) -->
  <div class="auth-brand-center">
    <a href="{{ url('/') }}" class="auth-brand-link" title="TEMU UMRAH">
      <img src="{{ asset('assets/brand/temu-umrah-mark.svg') }}" width="44" height="44" alt="Logo TEMU UMRAH" class="auth-brand-logo">
      <span class="auth-brand-name">TEMU UMRAH</span>
    </a>
  </div>

  <!-- Header Strip & Eyebrow -->
  <header class="auth-header-strip">
    <div class="auth-eyebrow">
      <span class="auth-pulse-dot"></span>
      <span>Akses Portal Sivitas Akademika</span>
    </div>
    <h1 class="auth-page-title">Masuk ke TEMU UMRAH</h1>
    <p class="auth-page-desc">
      Gunakan akun Google Workspace resmi kampus (@student.umrah.ac.id) atau masuk dengan NIM dan kata sandi Anda.
    </p>
  </header>

  <!-- Main Card -->
  <div class="auth-card-modern">
    @if(session('info'))
      <div class="alert-popup" style="background:#eff6ff; border:1.5px solid #bfdbfe; color:#1e40af; margin-bottom:1.25rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <span>{{ session('info') }}</span>
      </div>
    @endif
    @if(session('success'))
      <div class="alert-popup alert-success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="alert-popup alert-danger">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ session('error') }}</span>
      </div>
    @endif
    @if($errors->any())
      <div class="alert-popup alert-danger">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif
    <div id="loginAlert" class="alert-popup" style="display: none;"></div>

    <!-- Google Workspace for Education SSO (OAuth 2.0) -->
    <a href="{{ route('auth.google') }}" class="btn-google-sso" id="btnGoogleSso">
      <svg class="btn-google-icon" viewBox="0 0 24 24" width="20" height="20">
        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
      </svg>
      <span class="btn-google-text">Masuk dengan Akun Google UMRAH</span>
      <span class="btn-google-badge">@student.umrah.ac.id</span>
    </a>

    <!-- Divider -->
    <div class="auth-divider">
      <span>atau masuk dengan NIM & kata sandi</span>
    </div>

    <!-- Login Form -->
    <form id="loginForm" action="{{ route('login.post') }}" method="POST" data-backend-auth="true">
      @csrf
      <div class="form-group">
        <label class="form-label" for="loginUser">NIM atau Email Kampus *</label>
        <div class="input-with-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <input type="text" name="identifier" id="loginUser" class="form-input" placeholder="2401020073 atau email kampus" value="{{ old('identifier') }}" required autocomplete="username">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="loginPass">Kata Sandi *</label>
        <div class="input-with-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" name="password" id="loginPass" class="form-input" placeholder="Masukkan kata sandi" required autocomplete="current-password" style="padding-right: 2.75rem;">
          <button type="button" class="btn-toggle-pass" id="btnTogglePass" title="Lihat/Sembunyikan kata sandi" aria-label="Lihat kata sandi">
            <svg id="eyeOpenIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg id="eyeClosedIcon" style="display: none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
      </div>

      <div class="form-row-between">
        <label class="form-checkbox-label">
          <input type="checkbox" name="remember" value="1">
          <span>Ingat sesi saya</span>
        </label>
        <a href="mailto:helpdesk@umrah.ac.id" class="forgot-link">Butuh bantuan?</a>
      </div>

      <button type="submit" class="btn-auth-primary">
        <span>Masuk ke Portal</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </form>

    <!-- Switch to Register -->
    <div class="auth-switch-card">
      <div class="auth-switch-info">
        <strong>Belum punya akun?</strong>
        <span>Aktivasi akun mahasiswa atau daftarkan ormawa baru.</span>
      </div>
      <a href="{{ route('register') }}" class="btn-switch-action">
        <span>Buka Registrasi &rarr;</span>
      </a>
    </div>
  </div>

  <!-- Micro Features / Trust Badges -->
  <div class="auth-trust-strip" aria-label="Jaminan keamanan platform">
    <div class="auth-trust-item">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      <span>SSO Google Workspace Kampus</span>
    </div>
    <div class="auth-trust-item">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span>E-Tiket &amp; Presensi QR Dinamis</span>
    </div>
    <div class="auth-trust-item">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
      <span>E-Sertifikat Terverifikasi SKP</span>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
