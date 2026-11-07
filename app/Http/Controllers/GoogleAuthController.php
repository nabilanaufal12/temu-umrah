<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * Controller Autentikasi Google Workspace for Education SSO (OAuth 2.0)
 * Khusus Sivitas Akademika Universitas Maritim Raja Ali Haji (@student.umrah.ac.id)
 */
class GoogleAuthController extends Controller
{
    /**
     * Mengarahkan pengguna ke Google OAuth 2.0 Consent Screen resmi.
     */
    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        if (empty($clientId) || $clientId === 'dummy') {
            return redirect()->route('login')->with('info', 'Layanan SSO Google Workspace resmi memerlukan GOOGLE_CLIENT_ID di file .env. Silakan masuk menggunakan NIM dan Kata Sandi Anda.');
        }

        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $query = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'hd'            => 'student.umrah.ac.id', // Restriksi domain Google Workspace UMRAH
            'prompt'        => 'select_account',
            'access_type'   => 'online',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    /**
     * Menangani callback resmi dari Google Cloud OAuth 2.0
     */
    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Otentikasi Google dibatalkan atau terjadi kesalahan: ' . $request->error);
        }

        $state = session('oauth_state');
        if (empty($state) || $request->state !== $state) {
            return redirect()->route('login')->with('error', 'Sesi otentikasi Google kedaluwarsa. Silakan coba kembali.');
        }

        $code = $request->code;
        if (!$code) {
            return redirect()->route('login')->with('error', 'Kode otorisasi Google tidak ditemukan.');
        }

        try {
            // Tukar authorization code dengan access token
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code'          => $code,
                'client_id'     => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri'  => config('services.google.redirect'),
                'grant_type'    => 'authorization_code',
            ]);

            if (!$tokenResponse->successful()) {
                return redirect()->route('login')->with('error', 'Gagal memverifikasi token ke server Google.');
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? null;

            // Ambil profil data pengguna dari Google
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
            if (!$userResponse->successful()) {
                return redirect()->route('login')->with('error', 'Gagal mengambil data profil dari Google.');
            }

            $googleData = $userResponse->json();
            $email = $googleData['email'] ?? '';
            $name = $googleData['name'] ?? 'Mahasiswa UMRAH';
            $picture = $googleData['picture'] ?? null;

            return $this->loginGoogleUser($email, $name, $picture);

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem saat menghubungkan ke Google: ' . $e->getMessage());
        }
    }

    /**
     * Memvalidasi domain, menyinkronkan data mahasiswa, dan membuat sesi login sah
     */
    protected function loginGoogleUser(string $email, string $name, ?string $picture = null)
    {
        // 1. Validasi Keamanan Domain: Hanya mengizinkan @student.umrah.ac.id
        if (!str_ends_with(strtolower($email), '@student.umrah.ac.id')) {
            return redirect()->route('login')->with('error', 'Akses Ditolak: Hanya akun Google resmi sivitas akademika UMRAH (@student.umrah.ac.id) yang dapat masuk ke portal ini.');
        }

        // 2. Ekstraksi NIM otomatis dari format email kampus: NIM@student.umrah.ac.id
        $nim = explode('@', $email)[0];

        // 3. Sinkronkan dengan database (Cari atau buat akun baru)
        $user = User::query()
            ->where('email', $email)
            ->orWhere('nim', $nim)
            ->first();

        if ($user) {
            // Update nama jika belum lengkap dan pasang foto jika ada
            if ($picture && empty($user->foto)) {
                $user->foto = $picture;
            }
            if ($name && $user->nama !== $name && str_contains($user->nama, 'Mahasiswa')) {
                $user->nama = $name;
            }
            $user->save();
        } else {
            // Auto-Registrasi Mahasiswa Baru dari Google Workspace Kampus
            $user = User::create([
                'nim'      => $nim,
                'nama'     => $name,
                'email'    => $email,
                'no_wa'    => null,
                'password' => Hash::make(Str::random(24)),
                'role'     => 'mahasiswa',
                'status'   => 'aktif',
                'foto'     => $picture,
            ]);
        }

        // 4. Buat Sesi Login Otentikasi Sah Laravel
        Auth::login($user, true);
        request()->session()->regenerate();

        session([
            'user_id'   => $user->id,
            'user_nama' => $user->nama,
            'user_role' => $user->role,
            'user_nim'  => $user->nim,
            'auth_via'  => 'google_workspace_sso',
        ]);

        return redirect()->route('peserta.events')->with('success', "Selamat datang, {$user->nama}! Anda berhasil masuk melalui Google Workspace resmi UMRAH.");
    }
}
