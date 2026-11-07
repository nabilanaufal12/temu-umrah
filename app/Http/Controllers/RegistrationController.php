<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $userId = Auth::id() ?? $request->session()->get('user_id');
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu.',
                'redirect' => route('login'),
            ], 401);
        }

        if (!User::whereKey($userId)->where('role', 'mahasiswa')->where('status', 'aktif')->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya akun mahasiswa aktif yang dapat mendaftar event.',
            ], 403);
        }

        $result = DB::transaction(function () use ($slug, $userId) {
            $event = Event::query()
                ->where('slug', $slug)
                ->where('status', 'published')
                ->lockForUpdate()
                ->firstOrFail();

            if ($event->kuota_terisi >= $event->kuota_maks) {
                return ['error' => 'Kuota event sudah penuh.'];
            }

            if (Registration::where('user_id', $userId)->where('event_id', $event->id)->exists()) {
                return ['error' => 'Anda sudah terdaftar pada event ini.'];
            }

            $isPaid = $event->tipe_biaya === 'berbayar' && (float) $event->harga > 0;
            $registration = Registration::create([
                'user_id' => $userId,
                'event_id' => $event->id,
                'nomor_tiket' => 'TIK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'status' => $isPaid ? 'menunggu_pembayaran' : 'terkonfirmasi',
                'token_presensi' => strtoupper(Str::random(6)),
            ]);

            if ($isPaid) {
                Payment::create([
                    'registration_id' => $registration->id,
                    'order_id' => 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                    'jumlah' => $event->harga,
                    'metode_bayar' => 'qris',
                    'channel_detail' => 'QRIS Dinamis',
                    'status' => 'pending',
                ]);
            }

            $event->increment('kuota_terisi');

            return [
                'registration' => $registration,
                'paid' => $isPaid,
            ];
        });

        if (isset($result['error'])) {
            return response()->json(['status' => 'error', 'message' => $result['error']], 422);
        }

        $registration = $result['registration'];
        return response()->json([
            'status' => 'success',
            'message' => $result['paid']
                ? 'Pendaftaran berhasil dibuat. Silakan lanjutkan pembayaran.'
                : 'Pendaftaran event berhasil. E-tiket telah diterbitkan.',
            'ticket' => $registration->nomor_tiket,
            'redirect' => $result['paid']
                ? route('pembayaran.show', ['invoice' => $registration->nomor_tiket])
                : route('peserta.events'),
        ]);
    }
}
