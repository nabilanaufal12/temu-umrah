<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->guest(route('login'));
        }

        if ($user->status !== 'aktif') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda sedang ditangguhkan. Silakan hubungi BAAK UMRAH.');
        }

        if (!in_array($user->role, $roles, true)) {
            if ($user->role === 'admin' && in_array('panitia', $roles, true)) {
                return redirect()->route('admin.panel')->with('error', 'Halaman tersebut khusus untuk akun Panitia Ormawa pelaksana kegiatan.');
            }
            if ($user->role === 'panitia' && in_array('admin', $roles, true)) {
                return redirect()->route('panitia.dashboard')->with('error', 'Anda tidak memiliki hak akses ke Panel Admin BAAK.');
            }
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
