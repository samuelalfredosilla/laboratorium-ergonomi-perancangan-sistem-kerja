<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Mencegah web dibungkus iframe oleh situs lain (Anti-Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Mencegah browser menebak MIME type file (Anti-MIME Sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Batasi informasi referer saat berpindah halaman
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 4. Batasi akses fitur perangkat keras browser yang tidak diperlukan
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // 5. Jangan simpan cache untuk halaman dashboard admin (mencegah akses via tombol Back browser setelah logout)
        if ($request->is('admin*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
