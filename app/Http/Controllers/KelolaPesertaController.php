<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Presensi;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelolaPesertaController extends Controller
{
    /**
     * Menampilkan halaman kelola peserta & verifikasi pembayaran event (UC-12).
     */
    public function index($eventId = 1)
    {
        $event = $this->ownedEvent($eventId);
        $event->load('ormawa');

        $registrations = Registration::query()
            ->with(['user', 'payment', 'presensi'])
            ->where('event_id', $event->id)
            ->orderBy('id', 'asc')
            ->get();

        // Hitung metrik statistik secara dinamis dari database
        $totalPendaftar = $registrations->count();
        $totalLunas = $registrations->filter(function ($r) {
            return $r->status === 'terkonfirmasi' || optional($r->payment)->status === 'settlement';
        })->count();
        $totalPending = $registrations->filter(function ($r) {
            return $r->status === 'menunggu_pembayaran' || optional($r->payment)->status === 'pending';
        })->count();
        $totalDitolak = $registrations->filter(function ($r) {
            return in_array($r->status, ['batal', 'dibatalkan', 'ditolak']) || in_array(optional($r->payment)->status, ['expired', 'failed', 'expire', 'deny', 'cancel']);
        })->count();
        $totalHadir = $registrations->filter(function ($r) {
            return optional($r->presensi)->status === 'hadir';
        })->count();

        // Format data peserta untuk konsumsi view & JavaScript
        $pesertaList = $registrations->map(function ($r) use ($event) {
            $user = $r->user;
            $payment = $r->payment;
            $presensi = $r->presensi;

            // Status pembayaran
            $statusBayar = 'pending';
            if ($r->status === 'terkonfirmasi' || optional($payment)->status === 'settlement') {
                $statusBayar = 'lunas';
            } elseif (in_array($r->status, ['batal', 'dibatalkan', 'ditolak']) || in_array(optional($payment)->status, ['expired', 'failed', 'expire', 'deny', 'cancel'])) {
                $statusBayar = 'ditolak';
            }

            // Status kehadiran
            $statusHadir = optional($presensi)->status === 'hadir' ? 'hadir' : 'belum';

            // Detail pembayaran
            $channelDetail = optional($payment)->channel_detail ?? ($event->tipe_biaya === 'gratis' ? 'Pendaftaran Bebas Biaya' : 'QRIS Dinamis ASPI');
            $refNumber = optional($payment)->transaction_id ?? ($statusBayar === 'lunas' ? 'SETTLED-' . $r->nomor_tiket : 'PENDING-' . $r->nomor_tiket);
            $gatewayStatus = strtoupper(optional($payment)->status ?? ($statusBayar === 'lunas' ? 'SETTLEMENT' : 'PENDING'));

            return [
                'id' => $r->id,
                'nim' => $user->nim ?? '-',
                'nama' => $user->nama ?? 'Mahasiswa UMRAH',
                'prodi' => 'Mahasiswa UMRAH',
                'email' => $user->email ?? '-',
                'invoice' => $r->nomor_tiket,
                'nominal' => $event->tipe_biaya === 'gratis' ? 'Gratis' : 'Rp ' . number_format($event->harga, 0, ',', '.'),
                'paymentMethod' => $channelDetail,
                'gatewayProvider' => str_contains(strtolower($channelDetail), 'va') || str_contains(strtolower($channelDetail), 'virtual') ? 'Virtual Account Biller' : 'Midtrans Snap (ASPI)',
                'waktuUpload' => optional($r->tanggal_daftar)->translatedFormat('d M Y, H:i') ? optional($r->tanggal_daftar)->translatedFormat('d M Y, H:i') . ' WIB' : 'Hari ini',
                'settlementTime' => optional(optional($payment)->settlement_time)->translatedFormat('d M Y, H:i:s') ? optional(optional($payment)->settlement_time)->translatedFormat('d M Y, H:i:s') . ' WIB' : '-',
                'statusBayar' => $statusBayar,
                'statusHadir' => $statusHadir,
                'strukRef' => $refNumber,
                'gatewayStatus' => $gatewayStatus,
                'alasanTolak' => '',
            ];
        })->values();

        return view('pages.kelola_peserta', [
            'event' => $event,
            'eventId' => $event->id,
            'eventName' => $event->judul,
            'stats' => [
                'total' => $totalPendaftar,
                'lunas' => $totalLunas,
                'pending' => $totalPending,
                'ditolak' => $totalDitolak,
                'hadir' => $totalHadir,
            ],
            'pesertaList' => $pesertaList,
        ]);
    }

    /**
     * Menyetujui bukti pembayaran pendaftar (UC-12).
     */
    public function approvePayment(Request $request, $pendaftaranId)
    {
        $registration = $this->ownedRegistration($pendaftaranId);

        $registration->status = 'terkonfirmasi';
        $registration->save();

        if ($registration->payment) {
            $registration->payment->status = 'settlement';
            $registration->payment->settlement_time = now();
            $registration->payment->save();
        } else {
            Payment::create([
                'registration_id' => $registration->id,
                'order_id' => $registration->nomor_tiket,
                'transaction_id' => 'SETTLED-PANITIA-' . rand(100000, 999999),
                'jumlah' => $registration->event->harga ?? 0,
                'metode_bayar' => 'qris',
                'channel_detail' => 'Verifikasi Panitia',
                'status' => 'settlement',
                'settlement_time' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran ' . ($registration->user->nama ?? 'peserta') . ' berhasil disetujui. Tiket telah diterbitkan.'
        ]);
    }

    /**
     * Menolak bukti pembayaran pendaftar beserta alasan (Alt 12A).
     */
    public function rejectPayment(Request $request, $pendaftaranId)
    {
        $request->validate([
            'alasan' => 'required|string|min:3'
        ]);

        $registration = $this->ownedRegistration($pendaftaranId);
        $registration->status = 'batal';
        $registration->save();

        if ($registration->payment) {
            $registration->payment->status = 'failed';
            $registration->payment->save();
        }

        return response()->json([
            'status' => 'rejected',
            'message' => 'Pendaftaran/pembayaran ' . ($registration->user->nama ?? 'peserta') . ' ditolak: ' . $request->alasan
        ]);
    }

    /**
     * Mengubah kehadiran peserta secara manual oleh panitia.
     */
    public function togglePresence(Request $request, $pendaftaranId)
    {
        $registration = $this->ownedRegistration($pendaftaranId);

        $presensi = Presensi::where('registration_id', $registration->id)->first();
        if ($presensi && $presensi->status === 'hadir') {
            $presensi->status = 'tidak_hadir';
            $presensi->save();
            $newStatus = 'belum';
            $msg = 'Status kehadiran ' . ($registration->user->nama ?? 'peserta') . ' diubah menjadi Belum Hadir.';
        } else {
            if (!$presensi) {
                $presensi = new Presensi();
                $presensi->registration_id = $registration->id;
            }
            $presensi->waktu_hadir = now();
            $presensi->metode = 'manual_panitia';
            $presensi->status = 'hadir';
            $presensi->save();
            $newStatus = 'hadir';
            $msg = 'Status kehadiran ' . ($registration->user->nama ?? 'peserta') . ' berhasil diverifikasi sebagai Hadir.';
        }

        return response()->json([
            'status' => 'success',
            'presence_status' => $newStatus,
            'message' => $msg
        ]);
    }

    private function ownedEvent($eventId): Event
    {
        $ormawaId = Auth::user()?->ormawa?->id;

        return Event::query()
            ->whereKey($eventId)
            ->where('ormawa_id', $ormawaId)
            ->firstOrFail();
    }

    private function ownedRegistration($registrationId): Registration
    {
        $ormawaId = Auth::user()?->ormawa?->id;

        return Registration::query()
            ->whereKey($registrationId)
            ->whereHas('event', fn ($query) => $query->where('ormawa_id', $ormawaId))
            ->firstOrFail();
    }
}

