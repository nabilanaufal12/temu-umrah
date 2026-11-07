<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Instruksi Pembayaran & Upload Bukti Transfer (UC-05)
 * Sesuai kurikulum Praktikum Pemrograman Web (Modul 4, 6, 7)
 */
class PembayaranController extends Controller
{
    /**
     * Menampilkan halaman instruksi pembayaran & tagihan (WF4)
     */
    public function show($invoice = null)
    {
        $registration = Registration::query()
            ->with(['event.ormawa', 'payment'])
            ->where('user_id', Auth::id())
            ->when($invoice, fn ($query) => $query->where('nomor_tiket', $invoice))
            ->when(!$invoice, fn ($query) => $query->where('status', 'menunggu_pembayaran')->latest('id'))
            ->first();

        if (!$registration?->event) {
            return redirect()->route('peserta.events')->with('info', 'Tidak ada tagihan pembayaran tertunda untuk akun Anda.');
        }

        $eventData = [
            'id' => $registration->event->id,
            'title' => $registration->event->judul,
            'category' => $registration->event->kategori,
            'organizer' => optional($registration->event->ormawa)->nama_ormawa ?? 'Organisasi Mahasiswa UMRAH',
            'date' => optional($registration->event->tanggal_kegiatan)->translatedFormat('d F Y'),
            'time' => substr((string) $registration->event->waktu_mulai, 0, 5) . ' - ' . substr((string) $registration->event->waktu_selesai, 0, 5) . ' WIB',
            'location' => $registration->event->lokasi,
            'price' => (float) $registration->event->harga,
            'priceLabel' => 'Rp ' . number_format((float) $registration->event->harga, 0, ',', '.') . ',-',
            'invoice' => $registration->nomor_tiket,
            'registration_id' => $registration->id,
        ];

        return view('pages.pembayaran', [
            'invoice' => $invoice ?? $registration->nomor_tiket,
            'eventData' => $eventData,
        ]);
    }

    /**
     * Menyelesaikan transaksi pembayaran & mengonfirmasi tiket secara otomatis (UC-05)
     */
    public function settle(Request $request)
    {
        $request->validate([
            'invoice' => 'required|string',
            'channel' => 'nullable|string',
        ]);

        $registration = Registration::query()
            ->with('event')
            ->where('nomor_tiket', $request->invoice)
            ->where('user_id', Auth::id())
            ->first();

        if (!$registration) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tagihan pendaftaran tidak ditemukan.'
            ], 404);
        }

        // Tentukan enum metode_bayar yang sesuai (qris, virtual_account, ewallet)
        $rawChannel = strtolower((string) $request->channel);
        if (str_contains($rawChannel, 'va') || str_contains($rawChannel, 'virtual')) {
            $metodeBayar = 'virtual_account';
        } elseif (str_contains($rawChannel, 'wallet') || str_contains($rawChannel, 'gopay') || str_contains($rawChannel, 'dana') || str_contains($rawChannel, 'ovo') || str_contains($rawChannel, 'shopeepay')) {
            $metodeBayar = 'ewallet';
        } else {
            $metodeBayar = 'qris';
        }

        // 1. Update status registrasi menjadi terkonfirmasi
        $registration->status = 'terkonfirmasi';
        $registration->save();

        // 2. Buat atau update catatan transaksi di tabel payments
        $trxId = 'TRX-MIDTRANS-' . date('Ymd') . '-' . rand(10000, 99999);
        \App\Models\Payment::updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'order_id' => $registration->payment->order_id ?? $registration->nomor_tiket,
                'transaction_id' => $trxId,
                'jumlah' => $registration->event->harga ?? 25000,
                'metode_bayar' => $metodeBayar,
                'channel_detail' => $request->channel ?? 'QRIS Dinamis (Bank Indonesia)',
                'status' => 'settlement',
                'settlement_time' => now(),
                'gateway_response' => [
                    'status_code' => '200',
                    'transaction_status' => 'settlement',
                    'fraud_status' => 'accept',
                    'transaction_id' => $trxId,
                    'gross_amount' => (float) ($registration->event->harga ?? 25000),
                ]
            ]
        );

        // 3. Tambahkan notifikasi real-time di tabel notifications
        \Illuminate\Support\Facades\DB::table('notifications')->insert([
            'user_id' => Auth::id(),
            'judul' => 'Pembayaran ' . ($registration->event->judul ?? 'Event') . ' Terverifikasi!',
            'pesan' => 'Pembayaran tagihan sebesar Rp ' . number_format((float) ($registration->event->harga ?? 25000), 0, ',', '.') . ' telah disetujui otomatis oleh Payment Gateway. E-Tiket Anda telah terbit.',
            'kategori' => 'PEMBAYARAN',
            'dibaca' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran berhasil diselesaikan dan diverifikasi oleh Payment Gateway.',
            'transaction_id' => $trxId,
            'order_id' => $registration->nomor_tiket,
            'settlement_time' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ]);
    }
}
