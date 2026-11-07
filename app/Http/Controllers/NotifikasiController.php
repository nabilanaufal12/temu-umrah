<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Pusat Notifikasi Mahasiswa (UC-14)
 * Sesuai kurikulum Praktikum Pemrograman Web (Modul 4 & 7)
 */
class NotifikasiController extends Controller
{
    /**
     * Halaman Pusat Notifikasi Mahasiswa
     */
    public function showPage()
    {
        $userId = Auth::id() ?? session('user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melihat notifikasi.');
        }

        $totalCount = DB::table('notifications')->where('user_id', $userId)->count();
        $unreadCount = DB::table('notifications')->where('user_id', $userId)->where('dibaca', 0)->count();

        return view('pages.notifikasi', compact('totalCount', 'unreadCount'));
    }


    /**
     * API Ambil Notifikasi Real-time
     */
    public function index()
    {
        $userId = Auth::id() ?? session('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $notifs = DB::select("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY id DESC LIMIT 20
        ", [$userId]);

        return response()->json([
            'status' => 'success',
            'data'   => $notifs
        ]);
    }

    /**
     * Tandai Notifikasi Telah Dibaca
     */
    public function markAsRead($id)
    {
        $userId = Auth::id() ?? session('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        DB::update("
            UPDATE notifications 
            SET dibaca = 1 
            WHERE id = ? AND user_id = ?
        ", [$id, $userId]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Tandai Semua Notifikasi Dibaca (UC-14B)
     */
    public function markAllRead()
    {
        $userId = Auth::id() ?? session('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        DB::update("
            UPDATE notifications 
            SET dibaca = 1 
            WHERE user_id = ?
        ", [$userId]);

        return response()->json(['status' => 'success']);
    }
}
