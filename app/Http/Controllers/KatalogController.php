<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KatalogController extends Controller
{
    /**
     * Menampilkan Beranda kegiatan mahasiswa (Spotlight / Pilihan)
     */
    public function index()
    {
        $allEvents = Event::query()
            ->with('ormawa')
            ->where('status', 'published')
            ->orderByDesc('id')
            ->get();

        $totalEvents = $allEvents->count();

        // Ambil 3 event pilihan / terdekat untuk Beranda
        $featuredEvents = $allEvents->take(3)->map(fn (Event $event) => $this->toFrontendEvent($event))->values();
        $events = $allEvents->map(fn (Event $event) => $this->toFrontendEvent($event))->values();

        return view('pages.katalog', compact('featuredEvents', 'totalEvents', 'events'));
    }

    /**
     * Menampilkan Halaman Lengkap Jelajah Event dengan Filter & Pencarian Penuh
     */
    public function jelajah(Request $request)
    {
        $query = $request->input('q');
        $category = $request->input('category', 'SEMUA');
        $fee = $request->input('fee', 'ALL');
        $sort = $request->input('sort', 'newest');

        $eventsQuery = Event::query()
            ->with('ormawa')
            ->where('status', 'published');

        if ($query) {
            $eventsQuery->where(function ($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('deskripsi', 'like', "%{$query}%")
                  ->orWhere('lokasi', 'like', "%{$query}%")
                  ->orWhereHas('ormawa', function ($sub) use ($query) {
                      $sub->where('nama_ormawa', 'like', "%{$query}%");
                  });
            });
        }

        if ($category && strtoupper($category) !== 'SEMUA') {
            $eventsQuery->where('kategori', strtoupper($category));
        }

        if ($fee && strtoupper($fee) === 'FREE') {
            $eventsQuery->where('tipe_biaya', 'gratis');
        } elseif ($fee && strtoupper($fee) === 'PAID') {
            $eventsQuery->where('tipe_biaya', 'berbayar');
        }

        if ($sort === 'title_asc') {
            $eventsQuery->orderBy('judul', 'asc');
        } elseif ($sort === 'price_asc') {
            $eventsQuery->orderBy('harga', 'asc');
        } elseif ($sort === 'quota') {
            $eventsQuery->orderByDesc('kuota_terisi');
        } else {
            $eventsQuery->orderByDesc('id');
        }

        $allPublishedEvents = Event::query()->where('status', 'published')->get();
        $categoryCounts = [
            'SEMUA' => $allPublishedEvents->count(),
            'SEMINAR' => $allPublishedEvents->where('kategori', 'SEMINAR')->count(),
            'WORKSHOP' => $allPublishedEvents->where('kategori', 'WORKSHOP')->count(),
            'LOMBA' => $allPublishedEvents->where('kategori', 'LOMBA')->count(),
            'BAZAR' => $allPublishedEvents->where('kategori', 'BAZAR')->count(),
        ];

        $events = $eventsQuery->get()->map(fn (Event $event) => $this->toFrontendEvent($event))->values();

        return view('pages.jelajah_event', compact('events', 'query', 'category', 'fee', 'sort', 'categoryCounts'));
    }

    /**
     * Detail event spesifik (WF3 / UC-04)
     */
    public function show($slug)
    {
        if ($slug === 'maritime-ai-hackathon-2026') {
            return redirect()->route('event.detail', ['slug' => 'seminar-nasional-ai-maritime-2026']);
        }

        $event = Event::query()
            ->with('ormawa')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$event) {
            throw new NotFoundHttpException('Event tidak ditemukan.');
        }

        $isRegistered = false;
        $userRegistration = null;
        if (Auth::check() && Auth::user()->role === 'mahasiswa') {
            $userRegistration = Registration::where('user_id', Auth::id())
                ->where('event_id', $event->id)
                ->first();
            $isRegistered = $userRegistration !== null;
        }

        return view('pages.event_detail', [
            'slug' => $slug,
            'eventData' => $this->toFrontendEvent($event),
            'isRegistered' => $isRegistered,
            'userRegistration' => $userRegistration,
        ]);
    }

    private function toFrontendEvent(Event $event): array
    {
        $quotaMax = max((int) $event->kuota_maks, 1);
        $benefits = is_array($event->manfaat_fasilitas)
            ? $event->manfaat_fasilitas
            : (json_decode($event->manfaat_fasilitas ?? '[]', true) ?: []);

        return [
            'id' => $event->id,
            'title' => $event->judul,
            'slug' => $event->slug,
            'category' => $event->kategori,
            'price' => (float) $event->harga,
            'priceLabel' => $event->tipe_biaya === 'gratis'
                ? 'GRATIS'
                : 'Rp ' . number_format((float) $event->harga, 0, ',', '.'),
            'ormawa' => optional($event->ormawa)->nama_ormawa ?? 'Organisasi Mahasiswa UMRAH',
            'speaker' => 'Narasumber kegiatan mahasiswa UMRAH',
            'date' => optional($event->tanggal_kegiatan)->translatedFormat('d F Y'),
            'time' => substr((string) $event->waktu_mulai, 0, 5) . ' - ' . substr((string) $event->waktu_selesai, 0, 5) . ' WIB',
            'location' => $event->lokasi,
            'quotaMax' => $quotaMax,
            'quotaFilled' => (int) $event->kuota_terisi,
            'bannerPath' => $event->banner_path ? asset($event->banner_path) : null,
            'bannerGradient' => 'linear-gradient(135deg, #003E6D, #005696)',
            'iconCode' => 'layout',
            'desc' => $event->deskripsi,
            'benefits' => $benefits,
        ];
    }
}
