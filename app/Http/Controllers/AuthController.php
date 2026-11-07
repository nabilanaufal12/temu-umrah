<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * Controller Autentikasi Pengguna & Aktivasi Akun Mahasiswa
 * Mengacu pada UC-01 (Aktivasi) & UC-02 (Login)
 * Sesuai kurikulum Praktikum Pemrograman Web (Modul 3, 4, 7)
 */
class AuthController extends Controller
{
    /** Menampilkan halaman login. */
    public function showLogin()
    {
        return view('pages.auth_login');
    }

    /** Menampilkan halaman aktivasi mahasiswa dan pendaftaran ormawa. */
    public function showRegister()
    {
        return view('pages.auth_register');
    }

    /**
     * Memproses login mahasiswa / panitia / admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string|min:8',
        ]);

        $id = $request->input('identifier');
        $pwd = $request->input('password');

        $user = User::query()
            ->where('status', 'aktif')
            ->where(function ($query) use ($id) {
                $query->where('nim', $id)->orWhere('email', $id);
            })
            ->first();

        if ($user && Hash::check($pwd, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            session([
                'user_id'   => $user->id,
                'user_nama' => $user->nama,
                'user_role' => $user->role,
                'user_nim'  => $user->nim,
                'ormawa_id' => optional($user->ormawa)->id,
            ]);

            // Redirect berdasarkan role (sesuai Skenario UC-02)
            if ($user->role === 'admin') {
                return redirect()->route('admin.panel')->with('success', 'Selamat datang Admin Sistem!');
            } elseif ($user->role === 'panitia') {
                return redirect()->route('panitia.dashboard')->with('success', 'Selamat datang Panitia Ormawa!');
            } else {
                return redirect()->route('katalog.index')->with('success', 'Berhasil masuk ke portal TEMU UMRAH!');
            }
        }

        return back()->withInput()->with('error', 'NIM/Email atau kata sandi yang Anda masukkan tidak sesuai.');
    }

    /**
     * Memproses aktivasi kata sandi mahasiswa resmi SIPA (UC-01)
     */
    public function activateMahasiswa(Request $request)
    {
        $request->validate([
            'nim'      => 'required|string',
            'no_wa'    => 'required|string|min:10',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $nim = $request->input('nim');
        $hashed = Hash::make($request->input('password'));

        // Update kata sandi mahasiswa di database
        $updated = User::query()
            ->where('nim', $nim)
            ->where('role', 'mahasiswa')
            ->update([
                'password' => $hashed,
                'no_wa' => $request->input('no_wa'),
                'status' => 'aktif',
                'updated_at' => now(),
            ]);

        if ($updated) {
            return redirect()->route('login')->with('success', "Aktivasi Akun NIM {$nim} berhasil! Silakan masuk dengan kata sandi baru Anda.");
        }

        return back()->with('error', 'Gagal mengaktivasi akun. Pastikan NIM terdaftar resmi di kampus.');
    }

    /**
     * Memproses pendaftaran Ormawa baru
     */
    public function registerOrmawa(Request $request)
    {
        $request->validate([
            'nama_ormawa' => 'required|string|max:150',
            'tingkat'     => 'required|in:universitas,fakultas,himpunan,ukm',
            'ketua'       => 'nullable|string|max:150',
            'pembina'     => 'nullable|string|max:150',
            'no_sk'       => 'nullable|string|max:100',
            'dokumen_sk'  => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'email'       => 'required|email|max:150',
            'no_wa'       => 'required|string',
            'password'    => 'required|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat user panitia
            $userId = DB::table('users')->insertGetId([
                'nama'       => $request->input('nama_ormawa'),
                'email'      => $request->input('email'),
                'no_wa'      => $request->input('no_wa'),
                'password'   => Hash::make($request->input('password')),
                'role'       => 'panitia',
                    'status'     => 'nonaktif',
                'created_at' => now(),
            ]);

            // Handle berkas SK upload
            $dokumenSkPath = null;
            if ($request->hasFile('dokumen_sk')) {
                $dokumenSkPath = $request->file('dokumen_sk')->store('dokumen_sk', 'public');
            }

            // 2. Buat data ormawa dengan status verifikasi pending
            DB::table('ormawa')->insert([
                'user_id'           => $userId,
                'nama_ormawa'       => $request->input('nama_ormawa'),
                'tingkat'           => $request->input('tingkat'),
                'ketua'             => $request->input('ketua'),
                'pembina'           => $request->input('pembina'),
                'no_sk'             => $request->input('no_sk'),
                'dokumen_sk'        => $dokumenSkPath,
                'status_verifikasi' => 'pending',
                'created_at'        => now(),
            ]);

            DB::commit();
            return redirect()->route('login')->with('success', 'Pendaftaran akun Ormawa dan berkas SK berhasil diajukan! Menunggu peninjauan & verifikasi oleh Tim Admin BAAK / Kemahasiswaan FTTK.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat mendaftarkan Ormawa: ' . $e->getMessage());
        }
    }

    /**
     * Logout pengguna (UC-16)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->flush();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
