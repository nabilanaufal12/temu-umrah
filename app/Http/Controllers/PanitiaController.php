<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Event;

/**
 * Controller Pengelolaan Event Panitia / Ormawa (UC-10)
 * Sesuai kurikulum Praktikum Pemrograman Web (Modul 4 & 7)
 */
class PanitiaController extends Controller
{
    /**
     * Dashboard daftar kegiatan panitia (WF9)
     */
    public function dashboard()
    {
        $ormawa = Auth::user()?->ormawa;
        if (!$ormawa || $ormawa->status_verifikasi !== 'verified') {
            return redirect()->route('login')->with('error', 'Akun Anda belum terhubung dengan Ormawa resmi yang terverifikasi.');
        }
        $ormawaId = $ormawa->id;

        // Prepared statement query event ormawa
        $events = collect(DB::select("
            SELECT e.*, 
                   COUNT(r.id) AS peserta_terdaftar
            FROM events e
            LEFT JOIN registrations r ON e.id = r.event_id
            WHERE e.ormawa_id = ?
            GROUP BY e.id
            ORDER BY e.id DESC
        ", [$ormawaId]))->map(function ($event) {
            $price = (float) $event->harga;
            return [
                'id' => (int) $event->id,
                'title' => $event->judul,
                'category' => $event->kategori,
                'date' => date('d M Y', strtotime($event->tanggal_kegiatan)),
                'rawDate' => date('Y-m-d', strtotime($event->tanggal_kegiatan)),
                'time' => substr((string) $event->waktu_mulai, 0, 5) . ' - ' . substr((string) $event->waktu_selesai, 0, 5) . ' WIB',
                'location' => $event->lokasi,
                'quotaFilled' => (int) $event->kuota_terisi,
                'quotaMax' => (int) $event->kuota_maks,
                'price' => $price,
                'rawHarga' => (int) $price,
                'tipeBiaya' => $event->tipe_biaya ?? ($price > 0 ? 'berbayar' : 'gratis'),
                'priceLabel' => $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Gratis',
                'hasCert' => true,
                'status' => $event->status,
                'catatanAdmin' => $event->catatan_admin,
                'gradient' => 'linear-gradient(135deg, #003E6D, #005696)',
                'icon' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
            ];
        })->values();

        $pendingPayments = DB::table('payments')
            ->join('registrations', 'registrations.id', '=', 'payments.registration_id')
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->where('events.ormawa_id', $ormawaId)
            ->where('payments.status', 'pending')
            ->count();

        $totalCertificates = DB::table('certificates')
            ->join('registrations', 'registrations.id', '=', 'certificates.registration_id')
            ->join('events', 'events.id', '=', 'registrations.event_id')
            ->where('events.ormawa_id', $ormawaId)
            ->count();

        $stats = [
            'events' => $events->count(),
            'participants' => $events->sum('quotaFilled'),
            'pendingPayments' => $pendingPayments,
            'certificates' => $totalCertificates,
        ];

        return view('pages.dashboard_panitia', compact('events', 'stats', 'ormawa'));
    }

    /**
     * Simpan event baru (Create UC-10)
     */
    public function storeEvent(Request $request)
    {
        $request->validate([
            'judul'            => 'required|string|max:255',
            'kategori'         => 'required|in:SEMINAR,WORKSHOP,LOMBA,BAZAR',
            'kuota_maks'       => 'required|integer|min:1',
            'tanggal_kegiatan' => 'required|date',
            'lokasi'           => 'required|string',
            'tipe_biaya'       => 'required|in:gratis,berbayar',
            'harga'            => 'nullable|numeric',
        ]);

        $ormawa = Auth::user()?->ormawa;
        if (!$ormawa || $ormawa->status_verifikasi !== 'verified') {
            return redirect()->route('login')->with('error', 'Akun Anda belum terhubung dengan Ormawa resmi yang terverifikasi.');
        }
        $ormawaId = $ormawa->id;
        $slug = Str::slug($request->input('judul')) . '-' . Str::lower(Str::random(6));

        DB::insert("
            INSERT INTO events 
            (ormawa_id, judul, slug, kategori, tipe_biaya, harga, kuota_maks, tanggal_kegiatan, waktu_mulai, waktu_selesai, lokasi, deskripsi, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, '09:00:00', '12:00:00', ?, 'Kegiatan resmi ormawa mahasiswa.', 'pending_approval')
        ", [
            $ormawaId,
            $request->input('judul'),
            $slug,
            $request->input('kategori'),
            $request->input('tipe_biaya'),
            $request->input('tipe_biaya') === 'berbayar' ? $request->input('harga', 0) : 0,
            $request->input('kuota_maks'),
            $request->input('tanggal_kegiatan'),
            $request->input('lokasi')
        ]);

        return redirect()->route('panitia.dashboard')->with('success', 'Kegiatan berhasil diajukan dan menunggu persetujuan Admin BAAK.');
    }

    /**
     * Perbarui rincian kegiatan ormawa (Update UC-10)
     */
    public function updateEvent(Request $request, $id)
    {
        $request->validate([
            'judul'            => 'required|string|max:255',
            'kategori'         => 'required|in:SEMINAR,WORKSHOP,LOMBA,BAZAR',
            'kuota_maks'       => 'required|integer|min:1',
            'tanggal_kegiatan' => 'required|date',
            'lokasi'           => 'required|string',
            'tipe_biaya'       => 'required|in:gratis,berbayar',
            'harga'            => 'nullable|numeric',
        ]);

        $ormawa = Auth::user()?->ormawa;
        if (!$ormawa || $ormawa->status_verifikasi !== 'verified') {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Silakan login sebagai panitia.'], 403);
            }
            return redirect()->route('login')->with('error', 'Silakan login sebagai panitia.');
        }

        $event = DB::table('events')->where('id', $id)->where('ormawa_id', $ormawa->id)->first();
        if (!$event) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Kegiatan tidak ditemukan atau bukan milik ormawa Anda.'], 404);
            }
            return redirect()->route('panitia.dashboard')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $harga = $request->input('tipe_biaya') === 'berbayar' ? $request->input('harga', 0) : 0;

        DB::table('events')->where('id', $id)->update([
            'judul'            => $request->input('judul'),
            'kategori'         => $request->input('kategori'),
            'tipe_biaya'       => $request->input('tipe_biaya'),
            'harga'            => $harga,
            'kuota_maks'       => $request->input('kuota_maks'),
            'tanggal_kegiatan' => $request->input('tanggal_kegiatan'),
            'lokasi'           => $request->input('lokasi'),
            'updated_at'       => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Perubahan informasi kegiatan berhasil disimpan.'
            ]);
        }

        return redirect()->route('panitia.dashboard')->with('success', 'Perubahan informasi kegiatan berhasil disimpan.');
    }

    /**
     * Hapus event (Delete UC-10)
     */
    public function destroyEvent($id)
    {
        $ormawa = Auth::user()?->ormawa;
        if (!$ormawa || $ormawa->status_verifikasi !== 'verified') {
            return redirect()->route('login')->with('error', 'Akun Anda belum terhubung dengan Ormawa resmi yang terverifikasi.');
        }
        $ormawaId = $ormawa->id;

        DB::delete("DELETE FROM events WHERE id = ? AND ormawa_id = ?", [$id, $ormawaId]);

        return redirect()->route('panitia.dashboard')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
