<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Controller Dashboard Mahasiswa (Pendaftaran Saya & E-Tiket)
 * Mengacu pada UC-06 & UC-08
 * Sesuai kurikulum Praktikum Pemrograman Web (Modul 4 & 7)
 */
class PesertaController extends Controller
{
    /**
     * Menampilkan riwayat pendaftaran & status kehadiran (WF5)
     */
    public function index(Request $request)
    {
        $userId = Auth::id() ?? session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melihat event Anda.');
        }

        // Query riwayat event mahasiswa via JOIN tabel registrations, events, payments, presensi
        $registrations = DB::select("
            SELECT 
                r.id AS reg_id,
                r.nomor_tiket,
                r.tanggal_daftar,
                r.status AS reg_status,
                r.token_presensi,
                r.qr_code_hash,
                e.id AS event_id,
                e.slug AS event_slug,
                e.judul AS event_judul,
                e.kategori AS event_kategori,
                e.tanggal_kegiatan,
                e.waktu_mulai,
                e.waktu_selesai,
                e.lokasi,
                e.tipe_biaya,
                e.harga,
                e.banner_path,
                o.nama_ormawa,
                p.status AS payment_status,
                pr.status AS presensi_status,
                c.nomor_sertifikat
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            JOIN ormawa o ON e.ormawa_id = o.id
            LEFT JOIN payments p ON r.id = p.registration_id
            LEFT JOIN presensi pr ON r.id = pr.registration_id
            LEFT JOIN certificates c ON r.id = c.registration_id
            WHERE r.user_id = ?
            ORDER BY r.id DESC
        ", [$userId]);

        $registrations = collect($registrations)->map(function ($registration) {
            $paymentStatus = $registration->payment_status;
            $isPaid = in_array($paymentStatus, ['settlement', 'verified'], true);
            $isPresent = $registration->presensi_status === 'hadir';
            $isPendingPayment = $registration->reg_status === 'menunggu_pembayaran' && !$isPaid;
            $kategori = strtoupper($registration->event_kategori ?? 'SEMINAR');
            $tglKegiatan = Carbon::parse($registration->tanggal_kegiatan);

            $gradients = [
                'SEMINAR' => 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%)',
                'WORKSHOP' => 'linear-gradient(135deg, #1e1b4b 0%, #3730a3 100%)',
                'KOMPETISI' => 'linear-gradient(135deg, #451a03 0%, #9a3412 100%)',
                'LOMBA' => 'linear-gradient(135deg, #451a03 0%, #9a3412 100%)',
                'BAZAR' => 'linear-gradient(135deg, #064e3b 0%, #047857 100%)',
            ];

            return [
                'id' => $registration->reg_id,
                'eventId' => $registration->event_id,
                'eventSlug' => $registration->event_slug,
                'eventTitle' => $registration->event_judul,
                'category' => $kategori,
                'bannerPath' => $registration->banner_path ? asset($registration->banner_path) : null,
                'qrCodeHash' => $registration->qr_code_hash,
                'ormawa' => $registration->nama_ormawa,
                'date' => $tglKegiatan->translatedFormat('l, d F Y'),
                'dateShort' => $tglKegiatan->translatedFormat('d M Y'),
                'eventDay' => $tglKegiatan->translatedFormat('d'),
                'eventMonth' => $tglKegiatan->translatedFormat('M'),
                'time' => substr((string) $registration->waktu_mulai, 0, 5) . ' - ' . substr((string) $registration->waktu_selesai, 0, 5) . ' WIB',
                'location' => $registration->lokasi,
                'ticketNo' => $registration->nomor_tiket,
                'registeredAt' => Carbon::parse($registration->tanggal_daftar)->translatedFormat('d F Y'),
                'isPending' => $isPendingPayment,
                'statusPay' => $isPendingPayment ? 'MENUNGGU_VERIFIKASI' : ($isPaid || $registration->tipe_biaya === 'gratis' ? 'TERDAFTAR' : 'MENUNGGU_VERIFIKASI'),
                'statusPayLabel' => $isPendingPayment ? 'MENUNGGU PEMBAYARAN' : ($isPaid ? 'LUNAS (GATEWAY)' : 'TERDAFTAR (GRATIS)'),
                'statusPresensi' => $isPresent ? 'HADIR PENUH' : 'BELUM HADIR',
                'isPresent' => $isPresent,
                'hasCert' => (bool) $registration->nomor_sertifikat,
                'certNo' => $registration->nomor_sertifikat,
                'price' => (float) $registration->harga,
                'gradient' => $gradients[$kategori] ?? 'linear-gradient(135deg, #0f172a 0%, #2563eb 100%)',
                'icon' => $kategori,
            ];
        })->values();

        $totalEvents = $registrations->count();
        $totalPending = $registrations->where('isPending', true)->count();
        $totalHadir = $registrations->where('isPresent', true)->count();
        $totalActive = $registrations->where('isPending', false)->where('isPresent', false)->count();
        $totalSertifikat = $registrations->where('hasCert', true)->count();

        return view('pages.dashboard_peserta', compact('registrations', 'totalEvents', 'totalPending', 'totalActive', 'totalHadir', 'totalSertifikat'));
    }

    /**
     * API detail tiket untuk QR Code modal
     */
    public function tiketDetail($tiketNo)
    {
        $userId = Auth::id() ?? session('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $tiket = DB::selectOne("
            SELECT r.*, e.judul, e.tanggal_kegiatan, e.lokasi, u.nama, u.nim
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            JOIN users u ON r.user_id = u.id
            WHERE r.nomor_tiket = ? AND r.user_id = ?
            LIMIT 1
        ", [$tiketNo, $userId]);

        if (!$tiket) {
            return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $tiket]);
    }
}
