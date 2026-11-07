<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ormawa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard Super Admin dengan antrean yang tersimpan di database.
     */
    public function index()
    {
        $allOrmawa = Ormawa::query()
            ->with('user')
            ->latest('id')
            ->get()
            ->map(fn (Ormawa $ormawa) => [
                'id' => $ormawa->id,
                'nama' => $ormawa->nama_ormawa,
                'kategori' => ucfirst($ormawa->tingkat ?? 'Universitas'),
                'email' => optional($ormawa->user)->email ?: '-',
                'ketua' => $ormawa->ketua ?: '-',
                'pembina' => $ormawa->pembina ?: '-',
                'noSk' => $ormawa->no_sk ?: '-',
                'dokumen' => $ormawa->dokumen_sk ?: 'SK_Pengesahan.pdf',
                'tglDaftar' => optional($ormawa->created_at)->format('d M Y H:i'),
                'status' => $ormawa->status_verifikasi, // 'pending', 'verified', 'rejected'
                'panitiaCount' => (int) ($ormawa->jumlah_panitia_aktif ?: 1),
                'panitiaList' => array_fill(0, max(1, (int) $ormawa->jumlah_panitia_aktif), true),
                'kuotaPanitiaMaks' => (int) ($ormawa->kuota_panitia_maks ?: 2),
            ])
            ->values();

        $pendingOrmawa = $allOrmawa->where('status', 'pending')->values();

        $allEvents = Event::query()
            ->with('ormawa')
            ->latest('id')
            ->get()
            ->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->judul,
                'category' => $event->kategori,
                'price' => (float) $event->harga,
                'priceLabel' => $event->tipe_biaya === 'gratis'
                    ? 'Gratis'
                    : 'Rp ' . number_format((float) $event->harga, 0, ',', '.'),
                'ormawa' => optional($event->ormawa)->nama_ormawa ?: 'Ormawa Mahasiswa',
                'date' => optional($event->tanggal_kegiatan)->format('d M Y'),
                'time' => substr((string) $event->waktu_mulai, 0, 5) . ' - ' . substr((string) $event->waktu_selesai, 0, 5) . ' WIB',
                'location' => $event->lokasi,
                'quotaMax' => (int) $event->kuota_maks,
                'quotaFilled' => (int) $event->kuota_terisi,
                'hasCert' => true,
                'desc' => $event->deskripsi,
                'catatanAdmin' => $event->catatan_admin,
                'submittedAt' => optional($event->created_at)->format('d M Y H:i'),
                'status' => $event->status, // 'pending_approval', 'published', 'ditolak', 'selesai'
                'gradient' => 'linear-gradient(135deg, #003E6D, #005696)',
            ])
            ->values();

        $pendingEvents = $allEvents->where('status', 'pending_approval')->values();

        $students = User::query()
            ->where('role', 'mahasiswa')
            ->orderBy('nim')
            ->get()
            ->map(function (User $student) {
                $nim = (string) ($student->nim ?? '');
                $kodeProdi = strlen($nim) >= 6 ? substr($nim, 2, 4) : '';

                $prodiInfo = match ($kodeProdi) {
                    '0102' => ['prodi' => 'Teknik Informatika', 'fakultas' => 'FTTK UMRAH'],
                    '0101' => ['prodi' => 'Teknik Elektro', 'fakultas' => 'FTTK UMRAH'],
                    '0103' => ['prodi' => 'Sistem Informasi', 'fakultas' => 'FTTK UMRAH'],
                    '0201' => ['prodi' => 'Ilmu Kelautan', 'fakultas' => 'FIKP UMRAH'],
                    '0202' => ['prodi' => 'Manajemen Sumberdaya Perairan', 'fakultas' => 'FIKP UMRAH'],
                    '0301' => ['prodi' => 'Manajemen', 'fakultas' => 'FEB UMRAH'],
                    '0302' => ['prodi' => 'Akuntansi', 'fakultas' => 'FEB UMRAH'],
                    '0401' => ['prodi' => 'Ilmu Pemerintahan', 'fakultas' => 'FISIP UMRAH'],
                    '0501' => ['prodi' => 'Pendidikan Bahasa Inggris', 'fakultas' => 'FKIP UMRAH'],
                    default => ['prodi' => 'Teknik Informatika', 'fakultas' => 'FTTK UMRAH'],
                };

                return [
                    'id' => $student->id,
                    'nim' => $student->nim ?: '-',
                    'nama' => $student->nama,
                    'email' => $student->email ?: '-',
                    'prodi' => $prodiInfo['prodi'],
                    'fakultas' => $prodiInfo['fakultas'],
                    'status' => $student->status === 'aktif' ? 'Aktif' : 'Ditangguhkan',
                    'statusRaw' => $student->status,
                    'statusLabel' => $student->status === 'aktif' ? 'Aktif Terverifikasi' : 'Akun Ditangguhkan',
                    'badgeClass' => $student->status === 'aktif' ? 'badge-success' : 'badge-danger',
                ];
            })
            ->values();

        $stats = [
            'students' => User::where('role', 'mahasiswa')->count(),
            'activeOrmawa' => Ormawa::where('status_verifikasi', 'verified')->count(),
            'verifiedOrmawa' => Ormawa::where('status_verifikasi', 'verified')->count(),
            'pendingOrmawa' => $pendingOrmawa->count(),
            'pendingEvents' => $pendingEvents->count(),
            'publishedEvents' => Event::where('status', 'published')->count(),
            'totalEvents' => Event::count(),
        ];

        return view('pages.admin_panel', compact('allOrmawa', 'pendingOrmawa', 'allEvents', 'pendingEvents', 'students', 'stats'));
    }

    /** Mengubah status aktif / tangguhkan akun mahasiswa. */
    public function toggleStudentStatus($id)
    {
        $student = User::query()->where('role', 'mahasiswa')->whereKey($id)->firstOrFail();
        $newStatus = $student->status === 'aktif' ? 'nonaktif' : 'aktif';
        $student->update(['status' => $newStatus]);

        return response()->json([
            'status' => 'success',
            'new_status' => $newStatus,
            'message' => 'Status akun ' . $student->nama . ' (' . ($student->nim ?? '-') . ') berhasil diubah menjadi ' . ($newStatus === 'aktif' ? 'Aktif Terverifikasi' : 'Ditangguhkan') . '.',
        ]);
    }

    /** Menyetujui legalitas Ormawa dan mengaktifkan akun panitianya. */
    public function approveOrmawa($id)
    {
        $ormawa = Ormawa::query()->with('user')->whereKey($id)->firstOrFail();

        DB::transaction(function () use ($ormawa) {
            $ormawa->update(['status_verifikasi' => 'verified']);
            if ($ormawa->user) {
                $ormawa->user->update(['status' => 'aktif']);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Akun ormawa berhasil diverifikasi dan diaktifkan.',
        ]);
    }

    /** Menolak legalitas Ormawa dan menonaktifkan akun panitianya. */
    public function rejectOrmawa(Request $request, $id)
    {
        $request->validate(['alasan' => 'nullable|string|max:500']);
        $ormawa = Ormawa::query()->with('user')->whereKey($id)->firstOrFail();

        DB::transaction(function () use ($ormawa) {
            $ormawa->update(['status_verifikasi' => 'rejected']);
            if ($ormawa->user) {
                $ormawa->user->update(['status' => 'nonaktif']);
            }
        });

        return response()->json([
            'status' => 'rejected',
            'message' => 'Permohonan pendaftaran ormawa ditolak.',
        ]);
    }

    /** Mempublikasikan event yang sudah lolos moderasi Admin. */
    public function approveEvent($id)
    {
        $event = Event::query()->where('status', 'pending_approval')->whereKey($id)->firstOrFail();
        $event->update([
            'status' => 'published',
            'catatan_admin' => null,
            'disetujui_oleh' => Auth::id(),
            'disetujui_pada' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil disetujui dan dipublikasikan.',
        ]);
    }

    /** Menolak event dengan catatan yang dapat ditindaklanjuti panitia. */
    public function rejectEvent(Request $request, $id)
    {
        $data = $request->validate([
            'alasan' => 'required|string|min:5|max:1000',
        ]);

        $event = Event::query()->where('status', 'pending_approval')->whereKey($id)->firstOrFail();
        $event->update([
            'status' => 'ditolak',
            'catatan_admin' => $data['alasan'],
            'disetujui_oleh' => Auth::id(),
            'disetujui_pada' => now(),
        ]);

        return response()->json([
            'status' => 'rejected',
            'message' => 'Pengajuan event ditolak dan catatan revisi tersimpan.',
        ]);
    }
}
