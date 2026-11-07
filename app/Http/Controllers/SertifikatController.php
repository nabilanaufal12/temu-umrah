<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller E-Sertifikat Digital Mahasiswa (UC-09 & UC-11)
 * Mendukung Opsi Hybrid: Template Khusus Karya Ormawa & Unduh File Fisik Jadi
 */
class SertifikatController extends Controller
{
    /**
     * Menampilkan pratinjau sertifikat mahasiswa (WF7)
     */
    public function show($nomorSertifikat = null)
    {
        $userId = Auth::id();

        // Ambil semua sertifikat yang dimiliki pengguna
        $allCertificates = Certificate::with(['registration.user', 'registration.event.ormawa'])
            ->whereHas('registration', fn ($query) => $query->where('user_id', $userId))
            ->orderByDesc('id')
            ->get();

        $certificate = null;
        if ($nomorSertifikat) {
            $certificate = $allCertificates->firstWhere('nomor_sertifikat', $nomorSertifikat);
            if (!$certificate) {
                $certificate = Certificate::with(['registration.user', 'registration.event.ormawa'])
                    ->where('nomor_sertifikat', $nomorSertifikat)
                    ->whereHas('registration', fn ($query) => $query->where('user_id', $userId))
                    ->first();
            }
        }

        // Jika nomor tidak dispesifikasikan, pilih sertifikat terbaru milik mahasiswa
        if (!$certificate) {
            $certificate = $allCertificates->first();
        }

        $event = $certificate?->registration?->event;
        $user  = $certificate?->registration?->user ?? Auth::user();

        // 1. Cek apakah ada file fisik sertifikat jadi yang diunggah panitia (Opsi 2)
        $hasUploadedFile = false;
        $uploadedFileUrl = null;
        $isImageFile = false;
        $isPdfFile = false;

        if ($certificate && $certificate->file_sertifikat_path) {
            $path = $certificate->file_sertifikat_path;
            if (Storage::disk('public')->exists($path)) {
                $hasUploadedFile = true;
                $uploadedFileUrl = asset('storage/' . $path);
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $isImageFile = in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
                $isPdfFile = ($ext === 'pdf');
            }
        }

        // 2. Cek apakah panitia mengunggah template desain blanko karya ormawa (Opsi 1)
        $hasCustomTemplate = false;
        $templateUrl = null;
        if ($event && $event->certificate_template_path) {
            if (Storage::disk('public')->exists($event->certificate_template_path)) {
                $hasCustomTemplate = true;
                $templateUrl = asset('storage/' . $event->certificate_template_path);
            }
        }

        return view('pages.sertifikat', [
            'nomor'              => $certificate?->nomor_sertifikat ?? $nomorSertifikat,
            'certificate'        => $certificate,
            'allCertificates'    => $allCertificates,
            'event'              => $event,
            'user'               => $user,
            'hasUploadedFile'    => $hasUploadedFile,
            'uploadedFileUrl'    => $uploadedFileUrl,
            'isImageFile'        => $isImageFile,
            'isPdfFile'          => $isPdfFile,
            'hasCustomTemplate'  => $hasCustomTemplate,
            'templateUrl'        => $templateUrl,
        ]);
    }

    /**
     * Mengunduh berkas fisik asli yang diunggah panitia (PDF / Gambar)
     */
    public function downloadFile($nomorSertifikat)
    {
        $userId = Auth::id();
        $cert = Certificate::with(['registration.event', 'registration.user'])
            ->where('nomor_sertifikat', $nomorSertifikat)
            ->whereHas('registration', fn ($q) => $q->where('user_id', $userId))
            ->firstOrFail();

        if (!$cert->file_sertifikat_path || !Storage::disk('public')->exists($cert->file_sertifikat_path)) {
            return redirect()->back()->with('error', 'Berkas sertifikat fisik belum diunggah oleh panitia.');
        }

        // Update status klaim
        $cert->status_klaim = 'terunduh';
        $cert->save();

        $ext = pathinfo($cert->file_sertifikat_path, PATHINFO_EXTENSION);
        $user = $cert->registration->user;
        $cleanTitle = Str::slug($cert->registration->event->judul ?? 'Event');
        $nim = $user->nim ?: $user->id;
        $downloadName = "Sertifikat_{$cleanTitle}_{$nim}.{$ext}";

        return Storage::disk('public')->download($cert->file_sertifikat_path, $downloadName);
    }

    /**
     * Verifikasi publik sertifikat via scan QR Code
     */
    public function verifikasi($nomorSertifikat)
    {
        $cert = DB::selectOne("
            SELECT c.*, u.nama, u.nim, e.judul AS event_judul, e.tanggal_kegiatan, o.nama_ormawa
            FROM certificates c
            JOIN registrations r ON c.registration_id = r.id
            JOIN users u ON r.user_id = u.id
            JOIN events e ON r.event_id = e.id
            JOIN ormawa o ON e.ormawa_id = o.id
            WHERE c.nomor_sertifikat = ?
            LIMIT 1
        ", [$nomorSertifikat]);

        if (!$cert) {
            return response()->json([
                'status'  => 'invalid',
                'message' => 'Sertifikat tidak terdaftar atau telah dicabut.'
            ], 404);
        }

        return response()->json([
            'status' => 'valid',
            'data'   => $cert
        ]);
    }
}
