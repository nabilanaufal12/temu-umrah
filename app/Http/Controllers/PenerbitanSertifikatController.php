<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Presensi;
use App\Models\Certificate;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller Penerbitan Sertifikat (Opsi Hybrid: Template Ormawa & Upload Berkas Jadi)
 */
class PenerbitanSertifikatController extends Controller
{
    /**
     * Menampilkan formulir konfigurasi, upload template ormawa, dan daftar peserta hadir (UC-11).
     */
    public function index($eventId = 1)
    {
        $event = $this->ownedEvent($eventId);

        // Ambil seluruh peserta dengan presensi sah (Hadir)
        $pesertaHadirList = Registration::with(['user', 'presensi', 'certificate'])
            ->where('event_id', $eventId)
            ->whereHas('presensi', fn ($query) => $query->where('status', 'hadir'))
            ->get();

        $hadirCount = $pesertaHadirList->count();
        $terbitCount = $pesertaHadirList->filter(function($p) {
            return $p->certificate && $p->certificate->file_sertifikat_path && Storage::disk('public')->exists($p->certificate->file_sertifikat_path);
        })->count();
        $belumCount = max(0, $hadirCount - $terbitCount);

        $participantsData = $pesertaHadirList->map(function ($p) {
            return [
                'id'       => $p->id,
                'nama'     => $p->user->nama ?? '',
                'nim'      => trim($p->user->nim ?? ''),
                'has_file' => (bool)($p->certificate && $p->certificate->file_sertifikat_path && Storage::disk('public')->exists($p->certificate->file_sertifikat_path)),
            ];
        })->values();

        return view('pages.penerbitan_sertifikat', [
            'eventId'          => $eventId,
            'event'            => $event,
            'hadirCount'       => $hadirCount,
            'terbitCount'      => $terbitCount,
            'belumCount'       => $belumCount,
            'pesertaHadirList' => $pesertaHadirList,
            'participantsJson' => $participantsData->toJson(),
        ]);
    }

    /**
     * Metode 1: Mengunggah Berkas Blanko Desain Template Karya Ormawa (Canva / Photoshop)
     */
    public function uploadTemplate(Request $request, $eventId)
    {
        $event = $this->ownedEvent($eventId);

        $request->validate([
            'template_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // Maks 5 MB
        ]);

        if ($request->hasFile('template_file')) {
            // Hapus file template lama jika ada
            if ($event->certificate_template_path && Storage::disk('public')->exists($event->certificate_template_path)) {
                Storage::disk('public')->delete($event->certificate_template_path);
            }

            $file = $request->file('template_file');
            $ext = $file->getClientOriginalExtension();
            $filename = 'template_' . Str::slug($event->judul) . '_' . time() . '.' . $ext;
            $path = $file->storeAs('certificate_templates', $filename, 'public');

            $event->certificate_template_path = $path;
            $event->save();

            return redirect()->back()->with('success', 'Template sertifikat karya divisi desain Ormawa berhasil diunggah dan diterapkan pada kegiatan ini!');
        }

        return redirect()->back()->with('error', 'Gagal memproses file template sertifikat.');
    }

    /**
     * Menghapus template custom Ormawa dan kembali ke background default
     */
    public function deleteTemplate($eventId)
    {
        $event = $this->ownedEvent($eventId);

        if ($event->certificate_template_path && Storage::disk('public')->exists($event->certificate_template_path)) {
            Storage::disk('public')->delete($event->certificate_template_path);
        }

        $event->certificate_template_path = null;
        $event->save();

        return redirect()->back()->with('success', 'Template custom telah dihapus. Sistem kembali ke format standar.');
    }

    /**
     * Metode Utama: Mengunggah Berkas Sertifikat Jadi Karya Canva (Format: NIM_Nama.pdf)
     * Mendukung multi-upload banyak berkas PDF sekaligus maupun berkas ZIP.
     */
    public function uploadBulkCanva(Request $request, $eventId)
    {
        $event = $this->ownedEvent($eventId);

        $request->validate([
            'files'    => 'nullable|array',
            'files.*'  => 'file|mimes:pdf,png,jpg,jpeg|max:20480',
            'zip_file' => 'nullable|file|mimes:zip|max:51200',
        ]);

        if (!$request->hasFile('files') && !$request->hasFile('zip_file')) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu berkas e-sertifikat PDF atau berkas ZIP.');
        }

        // Ambil daftar peserta dengan presensi sah (hadir)
        $registrations = Registration::with(['user', 'certificate'])
            ->where('event_id', $eventId)
            ->whereHas('presensi', fn ($query) => $query->where('status', 'hadir'))
            ->get();

        // Buat mapping NIM peserta untuk pencocokan cepat
        $regByNim = [];
        foreach ($registrations as $reg) {
            if ($reg->user && $reg->user->nim) {
                $regByNim[trim($reg->user->nim)] = $reg;
            }
        }

        $matched = [];
        $unmatched = [];

        // 1. Proses berkas PDF / Gambar langsung (Multi-Upload)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $origName = $file->getClientOriginalName();
                $targetReg = $this->findMatchingRegistration($origName, $regByNim);

                if ($targetReg) {
                    $user = $targetReg->user;
                    $ext = $file->getClientOriginalExtension();
                    $filename = 'canva_' . $eventId . '_' . ($user->nim ?: $user->id) . '_' . time() . '_' . Str::random(4) . '.' . $ext;
                    $path = $file->storeAs('certificates', $filename, 'public');

                    $nomor = $targetReg->certificate?->nomor_sertifikat ?: ('SERT/FTTK-UMRAH/' . strtoupper($event->kategori) . '/2026/' . ($user->nim ?: $user->id));

                    Certificate::updateOrCreate(
                        ['registration_id' => $targetReg->id],
                        [
                            'nomor_sertifikat'     => $nomor,
                            'tanggal_terbit'       => now()->toDateString(),
                            'file_sertifikat_path' => $path,
                            'status_klaim'         => 'siap_unduh',
                        ]
                    );

                    Notification::create([
                        'user_id'     => $user->id,
                        'judul'       => 'E-Sertifikat Canva Resmi Telah Diterbitkan',
                        'pesan'       => "Panitia {$event->ormawa?->nama_ormawa} telah mengunggah e-sertifikat resmi untuk Anda pada kegiatan '{$event->judul}'. Silakan unduh di menu Sertifikat.",
                        'kategori'    => 'sertifikat',
                        'dibaca'      => false,
                    ]);

                    $matched[] = "{$user->nama} ({$user->nim})";
                } else {
                    $unmatched[] = $origName;
                }
            }
        }

        // 2. Proses Berkas ZIP (jika ada)
        if ($request->hasFile('zip_file')) {
            $zip = new \ZipArchive();
            $zipFile = $request->file('zip_file');
            if ($zip->open($zipFile->getRealPath()) === true) {
                $tempDir = storage_path('app/temp_zip_' . time() . '_' . Str::random(6));
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $zip->extractTo($tempDir);
                $zip->close();

                // Scan berkas di folder hasil ekstraksi
                $scanned = glob($tempDir . '/*');
                foreach ($scanned as $extractedPath) {
                    if (is_file($extractedPath)) {
                        $ext = strtolower(pathinfo($extractedPath, PATHINFO_EXTENSION));
                        if (in_array($ext, ['pdf', 'png', 'jpg', 'jpeg'])) {
                            $baseName = basename($extractedPath);
                            $targetReg = $this->findMatchingRegistration($baseName, $regByNim);

                            if ($targetReg) {
                                $user = $targetReg->user;
                                $newFilename = 'canva_' . $eventId . '_' . ($user->nim ?: $user->id) . '_' . time() . '_' . Str::random(4) . '.' . $ext;
                                $targetRelativePath = 'certificates/' . $newFilename;
                                Storage::disk('public')->put($targetRelativePath, file_get_contents($extractedPath));

                                $nomor = $targetReg->certificate?->nomor_sertifikat ?: ('SERT/FTTK-UMRAH/' . strtoupper($event->kategori) . '/2026/' . ($user->nim ?: $user->id));

                                Certificate::updateOrCreate(
                                    ['registration_id' => $targetReg->id],
                                    [
                                        'nomor_sertifikat'     => $nomor,
                                        'tanggal_terbit'       => now()->toDateString(),
                                        'file_sertifikat_path' => $targetRelativePath,
                                        'status_klaim'         => 'siap_unduh',
                                    ]
                                );

                                Notification::create([
                                    'user_id'     => $user->id,
                                    'judul'       => 'E-Sertifikat Canva Resmi Telah Diterbitkan',
                                    'pesan'       => "Panitia {$event->ormawa?->nama_ormawa} telah mengunggah e-sertifikat resmi untuk Anda pada kegiatan '{$event->judul}'. Silakan unduh di menu Sertifikat.",
                                    'kategori'    => 'sertifikat',
                                    'dibaca'      => false,
                                ]);

                                $matched[] = "{$user->nama} ({$user->nim})";
                            } else {
                                $unmatched[] = $baseName;
                            }
                        }
                    }
                }

                // Bersihkan folder temp
                foreach (glob($tempDir . '/*') as $f) { if (is_file($f)) unlink($f); }
                @rmdir($tempDir);
            } else {
                $unmatched[] = 'Arsip ZIP tidak dapat diekstrak atau corrupt.';
            }
        }

        $matchedCount = count($matched);
        $unmatchedCount = count($unmatched);

        if ($matchedCount > 0) {
            $msg = "{$matchedCount} e-sertifikat Canva berhasil diunggah dan disinkronkan ke akun mahasiswa!";
            if ($unmatchedCount > 0) {
                $msg .= " ({$unmatchedCount} berkas dilewati karena NIM tidak terdaftar: " . implode(', ', array_slice($unmatched, 0, 3)) . ").";
            }
            return redirect()->back()->with('success', $msg);
        } else {
            $errMsg = "Tidak ada berkas yang berhasil dicocokkan dengan NIM peserta hadir.";
            if ($unmatchedCount > 0) {
                $errMsg .= " Contoh berkas: " . implode(', ', array_slice($unmatched, 0, 3)) . ". Pastikan nama berkas memuat NIM peserta (contoh: 2401020073_Nabil.pdf).";
            }
            return redirect()->back()->with('error', $errMsg);
        }
    }

    /**
     * Helper untuk mencocokkan nama file dengan peserta terdaftar berdasarkan NIM
     */
    private function findMatchingRegistration(string $filename, array $regByNim): ?Registration
    {
        // 1. Coba regex pola NIM (8 - 15 digit)
        if (preg_match('/(\d{8,15})/', $filename, $matches)) {
            $nimCandidate = $matches[1];
            if (isset($regByNim[$nimCandidate])) {
                return $regByNim[$nimCandidate];
            }
        }

        // 2. Cek apakah ada NIM peserta hadir yang terkandung di dalam nama file
        foreach ($regByNim as $nim => $reg) {
            if (str_contains($filename, (string)$nim)) {
                return $reg;
            }
        }

        return null;
    }

    /**
     * Metode 2: Mengunggah Berkas Sertifikat Jadi (PDF / JPG) untuk Peserta Tertentu
     */
    public function uploadFilePeserta(Request $request, $eventId)
    {
        $event = $this->ownedEvent($eventId);

        $request->validate([
            'registration_id'  => 'required|exists:registrations,id',
            'file_sertifikat'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Maks 10 MB
            'nomor_sertifikat' => 'nullable|string|max:120',
        ]);

        $reg = Registration::where('id', $request->registration_id)
            ->where('event_id', $eventId)
            ->firstOrFail();

        $user = $reg->user;
        $file = $request->file('file_sertifikat');
        $ext = $file->getClientOriginalExtension();
        $filename = 'sertif_' . Str::slug($event->judul) . '_' . ($user->nim ?: $user->id) . '_' . time() . '.' . $ext;
        $path = $file->storeAs('certificates', $filename, 'public');

        // Nomor seri resmi
        $nomor = $request->nomor_sertifikat ?: ($reg->certificate?->nomor_sertifikat ?: 'SERT/FTTK-UMRAH/' . strtoupper($event->kategori) . '/2026/' . ($user->nim ?: str_pad($user->id, 4, '0', STR_PAD_LEFT)));

        $cert = Certificate::updateOrCreate(
            ['registration_id' => $reg->id],
            [
                'nomor_sertifikat'     => $nomor,
                'tanggal_terbit'       => now()->toDateString(),
                'file_sertifikat_path' => $path,
                'status_klaim'         => 'siap_unduh',
            ]
        );

        // Kirimkan notifikasi in-app ke mahasiswa bersangkutan
        Notification::create([
            'user_id'     => $user->id,
            'judul'       => 'Sertifikat Resmi Telah Diunggah Panitia',
            'pesan'       => "Panitia {$event->ormawa?->nama_ormawa} telah mengunggah berkas e-sertifikat resmi Anda untuk kegiatan '{$event->judul}'. Silakan unduh di menu Sertifikat.",
            'kategori'    => 'sertifikat',
            'dibaca'      => false,
        ]);

        return redirect()->back()->with('success', "Berkas sertifikat resmi untuk {$user->nama} ({$user->nim}) berhasil diunggah!");
    }

    /**
     * Memproses penerbitan sertifikat massal ke seluruh peserta berhak (UC-11).
     */
    public function publishBatch(Request $request, $eventId)
    {
        $event = $this->ownedEvent($eventId);

        $request->validate([
            'format_nomor'   => 'required|string',
            'tanggal_terbit' => 'required|date',
            'signer_1'       => 'required|string',
            'signer_2'       => 'required|string'
        ]);

        $registrations = Registration::with(['user', 'certificate'])
            ->where('event_id', $eventId)
            ->whereHas('presensi', fn ($query) => $query->where('status', 'hadir'))
            ->get();

        $generated = 0;
        $idx = 1;

        foreach ($registrations as $reg) {
            $user = $reg->user;
            $noStr = str_pad($idx, 3, '0', STR_PAD_LEFT);
            $seriNo = str_replace('{NO}', $noStr, $request->format_nomor);
            $seriNo = str_replace('{NIM}', $user->nim ?: $user->id, $seriNo);

            if (!$reg->certificate) {
                $cert = Certificate::create([
                    'registration_id'      => $reg->id,
                    'nomor_sertifikat'     => $seriNo,
                    'tanggal_terbit'       => $request->tanggal_terbit,
                    'file_sertifikat_path' => $event->certificate_template_path ?: 'certificates/default.pdf',
                    'status_klaim'         => 'siap_unduh',
                ]);

                Notification::create([
                    'user_id'     => $user->id,
                    'judul'       => 'E-Sertifikat Telah Diterbitkan',
                    'pesan'       => "Selamat! E-Sertifikat kegiatan '{$event->judul}' resmi diterbitkan dengan nomor seri {$cert->nomor_sertifikat}.",
                    'kategori'    => 'sertifikat',
                    'dibaca'      => false,
                ]);

                $generated++;
            }
            $idx++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status'          => 'success',
                'message'         => "{$registrations->count()} E-Sertifikat berhasil diterbitkan & disinkronkan ke akun mahasiswa.",
                'total_generated' => $registrations->count()
            ]);
        }

        return redirect()->back()->with('success', "{$registrations->count()} E-Sertifikat resmi berhasil diterbitkan!");
    }

    private function ownedEvent($eventId): Event
    {
        return Event::query()
            ->whereKey($eventId)
            ->where('ormawa_id', Auth::user()?->ormawa?->id)
            ->firstOrFail();
    }
}
