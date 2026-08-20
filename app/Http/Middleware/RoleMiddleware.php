<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memeriksa apakah user yang sedang login memiliki hak akses yang sesuai.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah terotentikasi/login
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors([
                'username' => 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.',
            ]);
        }

        $user = Auth::user();

        // 2. Cek apakah role user cocok dengan salah satu role yang diizinkan
        if (! empty($roles) && ! $user->hasRole($roles)) {
            abort(403, 'Akses ditolak. Akun Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
