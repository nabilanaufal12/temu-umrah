<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman profil & data akademik pengguna (UC-15).
     */
    public function index()
    {
        $user = auth()->user();
        $totalEvent = 0;
        $totalSertifikat = 0;
        $stats = [];
        $ormawa = null;

        if ($user) {
            // Jika admin mengakses route /profil umum, arahkan ke tab profil di dalam panel admin
            if ($user->role === 'admin' && !request()->is('admin*')) {
                return redirect('/admin/panel#profil');
            }
            // Jika panitia mengakses route /profil umum, arahkan ke route profil di dalam panel panitia
            if ($user->role === 'panitia' && !request()->is('panitia*')) {
                return redirect()->route('panitia.profil');
            }

            if ($user->role === 'admin') {
                $stats = [
                    'verifiedOrmawa' => \App\Models\Ormawa::where('status_verifikasi', 'verified')->count(),
                    'totalEvents' => \App\Models\Event::count(),
                    'totalStudents' => \App\Models\User::where('role', 'mahasiswa')->count(),
                ];
            } elseif ($user->role === 'panitia') {
                $ormawa = $user->ormawa;
                $ormawaId = $ormawa?->id;
                $stats = [
                    'ormawaEvents' => $ormawa ? $ormawa->events()->count() : 0,
                    'ormawaParticipants' => $ormawaId ? \Illuminate\Support\Facades\DB::table('registrations')
                        ->join('events', 'events.id', '=', 'registrations.event_id')
                        ->where('events.ormawa_id', $ormawaId)
                        ->count() : 0,
                ];
            } else {
                $totalEvent = \App\Models\Registration::where('user_id', $user->id)->count();
                $totalSertifikat = \Illuminate\Support\Facades\DB::table('certificates')
                    ->join('registrations', 'certificates.registration_id', '=', 'registrations.id')
                    ->where('registrations.user_id', $user->id)
                    ->count();
            }
        }

        return view('pages.profil', compact('user', 'totalEvent', 'totalSertifikat', 'stats', 'ormawa'));
    }

    /**
     * Memperbarui kontak mahasiswa (UC-15).
     */
    public function updateContact(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string|min:10|max:18',
            'bio' => 'nullable|string|max:500',
            'username' => 'nullable|string|max:100',
            'github_username' => 'nullable|string|max:100'
        ]);

        $user = auth()->user();
        if ($user) {
            $user->no_wa = $request->no_hp;
            if ($request->has('bio')) {
                $user->bio = $request->bio;
            }
            if ($request->filled('username')) {
                $cleanedUser = ltrim(trim($request->username), '@');
                $user->username = $cleanedUser;
                $user->github_username = $cleanedUser;
            } elseif ($request->filled('github_username')) {
                $cleanedUser = ltrim(trim($request->github_username), '@');
                $user->username = $cleanedUser;
                $user->github_username = $cleanedUser;
            }
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor WhatsApp berhasil diperbarui.',
            'no_wa' => $user?->no_wa
        ]);
    }

    /**
     * Mengubah kata sandi akun (UC-15).
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8'
        ]);

        $user = auth()->user();
        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.'
            ], 422);
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Kata sandi akun berhasil diperbarui. Silakan gunakan kata sandi baru untuk masuk selanjutnya.'
        ]);
    }

    /**
     * Mengunggah / memperbarui foto profil pengguna.
     */
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072'
        ], [
            'foto.required' => 'Silakan pilih berkas foto terlebih dahulu.',
            'foto.image' => 'Berkas harus berupa gambar valid (JPEG, PNG, JPG, WEBP).',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal adalah 3 MB.'
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi login telah berakhir.'
            ], 401);
        }

        // Hapus foto lama jika tersimpan secara lokal
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $path = $request->file('foto')->store('avatars', 'public');
        $user->foto = $path;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Foto profil berhasil diperbarui.',
            'avatar_url' => $user->avatar_url
        ]);
    }

    /**
     * Menghapus foto profil pengguna dan kembali ke inisial nama.
     */
    public function deleteFoto(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi login telah berakhir.'
            ], 401);
        }

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->foto = null;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Foto profil berhasil dihapus dan kembali ke inisial nama.',
            'initial' => strtoupper(substr($user->nama ?? 'U', 0, 1))
        ]);
    }
}
