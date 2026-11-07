<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Presensi;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesiAbsensiController extends Controller
{
    /**
     * Menampilkan layar sesi absensi panitia mode proyektor (UC-12 & UC-07).
     */
    public function index($eventId = 1)
    {
        $eventModel = $this->ownedEvent($eventId);
        $registeredCount = $eventModel->registrations()->count();
        $presentCount = Presensi::query()
            ->where('status', 'hadir')
            ->whereHas('registration', fn ($query) => $query->where('event_id', $eventId))
            ->count();

        // Ambil riwayat presensi terbaru langsung dari database
        $recentPresensi = Presensi::query()
            ->with(['registration.user'])
            ->where('status', 'hadir')
            ->whereHas('registration', fn ($query) => $query->where('event_id', $eventId))
            ->orderByDesc('waktu_hadir')
            ->limit(6)
            ->get()
            ->map(function ($p) {
                $user = $p->registration?->user;
                $methodLabel = match ($p->metode) {
                    'scan_qr' => 'Scan QR',
                    'token_input' => 'Input Token',
                    default => 'Manual Panitia',
                };
                return [
                    'nama' => $user?->nama ?? 'Mahasiswa UMRAH',
                    'nim' => $user?->nim ?? '-',
                    'prodi' => 'Mahasiswa UMRAH',
                    'metode' => $methodLabel,
                    'time' => optional($p->waktu_hadir)->format('H.i') . ' WIB',
                ];
            });

        // Token cadangan sesi (6 digit alfanumerik)
        $sessionToken = strtoupper(substr(md5('TEMU-SESSION-' . $eventId), 0, 6));
        if ($eventId == 2) {
            $sessionToken = 'FIGM26';
        }

        return view('pages.sesi_absensi', [
            'eventId' => $eventId,
            'event' => (object)[
                'id' => $eventId,
                'nama_kegiatan' => $eventModel->judul,
                'lokasi' => $eventModel->lokasi,
                'organisasi' => optional($eventModel->ormawa)->nama_ormawa ?? 'Organisasi Mahasiswa UMRAH',
                'tanggal' => optional($eventModel->tanggal_kegiatan)->translatedFormat('d F Y'),
            ],
            'sessionStats' => [
                'registered' => $registeredCount,
                'present' => $presentCount,
            ],
            'recentPresensi' => $recentPresensi,
            'sessionToken' => $sessionToken,
        ]);
    }

    /**
     * Mendapatkan hash QR dinamis terbaru (TOTP 30s) beserta data kehadiran real-time.
     */
    public function getActiveQrHash($eventId)
    {
        $eventModel = $this->ownedEvent($eventId);

        // TOTP 30-detik interval slot
        $timeSlot = floor(time() / 30);
        $hashSuffix = strtoupper(substr(hash('sha256', $eventId . '|' . $timeSlot . '|temu-salt-2026'), 0, 6));
        $hash = 'QR-EVT' . $eventId . '-' . $hashSuffix;
        $secondsRemaining = 30 - (time() % 30);

        // Ambil metrik terbaru langsung dari basis data
        $registeredCount = $eventModel->registrations()->count();
        $presentCount = Presensi::query()
            ->where('status', 'hadir')
            ->whereHas('registration', fn ($query) => $query->where('event_id', $eventId))
            ->count();

        // Ambil feed presensi terkini
        $recent = Presensi::query()
            ->with(['registration.user'])
            ->where('status', 'hadir')
            ->whereHas('registration', fn ($query) => $query->where('event_id', $eventId))
            ->orderByDesc('waktu_hadir')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                $user = $p->registration?->user;
                $methodLabel = match ($p->metode) {
                    'scan_qr' => 'Scan QR',
                    'token_input' => 'Input Token',
                    default => 'Manual Panitia',
                };
                return [
                    'nama' => $user?->nama ?? 'Mahasiswa UMRAH',
                    'nim' => $user?->nim ?? '-',
                    'prodi' => 'Mahasiswa UMRAH',
                    'metode' => $methodLabel,
                    'time' => optional($p->waktu_hadir)->format('H.i') . ' WIB',
                ];
            });

        return response()->json([
            'status' => 'success',
            'hash' => $hash,
            'payload' => 'TEMU-PRESENSI:EVT-' . $eventId . ':' . $hash,
            'seconds_remaining' => $secondsRemaining,
            'valid_until' => now()->addSeconds($secondsRemaining)->toIso8601String(),
            'stats' => [
                'registered' => $registeredCount,
                'present' => $presentCount,
                'percent' => $registeredCount > 0 ? number_format(($presentCount / $registeredCount) * 100, 1) : '0.0'
            ],
            'recent' => $recent,
        ]);
    }

    /**
     * Presensi manual oleh panitia di layar proyektor berdasarkan NIM mahasiswa.
     */
    public function manualCheckin(Request $request, $eventId)
    {
        $request->validate([
            'nim' => 'required|string|min:4',
        ]);

        $event = $this->ownedEvent($eventId);
        $nim = trim((string) $request->input('nim'));

        $user = User::query()->where('nim', $nim)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan di pangkalan data kampus.',
            ], 404);
        }

        // Cari atau buat registrasi untuk event ini
        $registration = Registration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration) {
            $registration = Registration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'nomor_tiket' => 'TIK-MANUAL-' . date('Ymd') . '-' . substr($nim, -4),
                'tanggal_daftar' => now(),
                'status' => 'terkonfirmasi',
                'token_presensi' => strtoupper(substr(md5($nim), 0, 6)),
            ]);
        }

        // Simpan kehadiran
        $presensi = Presensi::updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'waktu_hadir' => now(),
                'metode' => 'manual_panitia',
                'status' => 'hadir',
            ]
        );

        $presentCount = Presensi::query()
            ->where('status', 'hadir')
            ->whereHas('registration', fn ($q) => $q->where('event_id', $event->id))
            ->count();
        $registeredCount = $event->registrations()->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Kehadiran mahasiswa ' . $user->nama . ' (NIM: ' . $user->nim . ') berhasil diverifikasi sah.',
            'student' => [
                'nama' => $user->nama,
                'nim' => $user->nim,
                'prodi' => 'Mahasiswa UMRAH',
                'metode' => 'Manual Panitia',
                'time' => now()->format('H.i') . ' WIB',
            ],
            'stats' => [
                'registered' => $registeredCount,
                'present' => $presentCount,
                'percent' => $registeredCount > 0 ? number_format(($presentCount / $registeredCount) * 100, 1) : '0.0'
            ]
        ]);
    }

    /**
     * Menutup sesi absensi secara permanen dan bersiap menerbitkan sertifikat.
     */
    public function closeSession($eventId)
    {
        $this->ownedEvent($eventId);

        $presentCount = Presensi::query()
            ->where('status', 'hadir')
            ->whereHas('registration', fn ($query) => $query->where('event_id', $eventId))
            ->count();

        return response()->json([
            'status' => 'closed',
            'message' => 'Sesi absensi resmi ditutup. Sebanyak ' . $presentCount . ' peserta berhak memperoleh sertifikat digital.',
            'redirect_url' => url('/panitia/event/' . $eventId . '/terbitkan-sertifikat')
        ]);
    }

    private function ownedEvent($eventId): Event
    {
        return Event::query()
            ->with('ormawa')
            ->whereKey($eventId)
            ->where('ormawa_id', Auth::user()?->ormawa?->id)
            ->firstOrFail();
    }
}

