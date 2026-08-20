<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthenticationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Langkah 1: Validasi Username & Password, Log Percobaan, lalu buat & kirim OTP
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $username = $request->input('username');
        $throttleKey = Str::transliterate(Str::lower($username) . '|' . $request->ip());

        // 1. Cek jika akun sedang terkunci karena brute force
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            // Catat log kejadian terkunci
            AuthenticationLog::create([
                'username'   => $username,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status'     => 'LOCKED_OUT',
                'message'    => "Percobaan login ditolak. Akun terkunci sementara ({$seconds}s tersisa).",
            ]);

            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login yang gagal. Akun dikunci sementara. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        // 2. Cek Kredensial User di Database
        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 300);
            $remaining = RateLimiter::remaining($throttleKey, 5);

            // Catat log login gagal
            AuthenticationLog::create([
                'username'   => $username,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status'     => 'FAILED',
                'message'    => "Kredensial tidak valid. Sisa percobaan: {$remaining}x.",
            ]);

            $errorMessage = 'Username atau password yang Anda masukkan salah.';
            if ($remaining > 0) {
                $errorMessage .= " (Sisa percobaan: {$remaining}x)";
            }

            return back()
                ->withErrors(['username' => $errorMessage])
                ->onlyInput('username');
        }

        // 3. Generate Kode OTP 6 Digit & Set Kedaluwarsa 5 Menit
        $otp = (string) random_int(100000, 999999);
        $user->update([
            'two_factor_code'       => $otp,
            'two_factor_expires_at' => now()->addMinutes(5),
        ]);

        // Simpan sesi sementara untuk proses verifikasi 2FA
        session([
            'auth.2fa.user_id'  => $user->id,
            'auth.2fa.remember' => $request->boolean('remember'),
            'auth.2fa.password' => $request->input('password'),
        ]);

        // Kirim email OTP
        try {
            Mail::raw("Halo {$user->name},\n\nKode verifikasi login (OTP) Admin Panel Anda adalah: {$otp}\n\nKode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun.", function ($message) use ($user) {
                $message->to($user->email)->subject('Kode OTP Verifikasi Login - EPSK Admin Panel');
            });
        } catch (\Exception $e) {
            // Error pengiriman email tidak memutus proses (berguna saat offline/testing)
        }

        return redirect()->route('login.2fa');
    }

    /**
     * Tampilkan Halaman Input Kode OTP
     */
    public function show2FaForm()
    {
        if (! session()->has('auth.2fa.user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('auth.2fa.user_id'));
        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.verify-2fa', compact('user'));
    }

    /**
     * Langkah 2: Verifikasi Kode OTP & Selesaikan Login Resmi
     */
    public function verify2Fa(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.size'     => 'Kode OTP harus berjumlah 6 digit.',
        ]);

        $userId = session('auth.2fa.user_id');
        $user = User::find($userId);

        if (! $user || ! $userId) {
            return redirect()->route('login')->withErrors(['username' => 'Sesi verifikasi telah berakhir. Silakan login kembali.']);
        }

        // Cek kecocokan kode OTP dan batas waktu (5 menit)
        if ($user->two_factor_code !== $request->input('code') || now()->gt($user->two_factor_expires_at)) {
            return back()->withErrors(['code' => 'Kode OTP salah atau telah kadaluarsa. Silakan gunakan tombol kirim ulang.']);
        }

        // Reset kolom OTP setelah berhasil dipakai
        $user->update([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
        ]);

        // Bersihkan hitungan rate limiter karena login sukses
        $throttleKey = Str::transliterate(Str::lower($user->username) . '|' . $request->ip());
        RateLimiter::clear($throttleKey);

        // Login resmi ke aplikasi
        Auth::login($user, session('auth.2fa.remember', false));

        // Auto-rehash password jika standar enkripsi dinaikkan
        $rawPassword = session('auth.2fa.password');
        if ($rawPassword && Hash::needsRehash($user->password)) {
            $user->password = $rawPassword;
            $user->save();
        }

        // Catat log autentikasi berhasil
        AuthenticationLog::create([
            'user_id'    => $user->id,
            'username'   => $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'SUCCESS',
            'message'    => 'Login dan verifikasi 2FA berhasil.',
        ]);

        $request->session()->regenerate();

        if ($rawPassword) {
            Auth::logoutOtherDevices($rawPassword);
        }

        // Bersihkan sesi sementara 2FA
        session()->forget(['auth.2fa.user_id', 'auth.2fa.remember', 'auth.2fa.password']);

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Kirim Ulang Kode OTP Baru
     */
    public function resend2Fa()
    {
        $userId = session('auth.2fa.user_id');
        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        $otp = (string) random_int(100000, 999999);
        $user->update([
            'two_factor_code'       => $otp,
            'two_factor_expires_at' => now()->addMinutes(5),
        ]);

        try {
            Mail::raw("Halo {$user->name},\n\nKode verifikasi OTP baru Anda adalah: {$otp}\n\nKode ini berlaku selama 5 menit.", function ($message) use ($user) {
                $message->to($user->email)->subject('Kode OTP Baru - EPSK Admin Panel');
            });
        } catch (\Exception $e) {
            //
        }

        return back()->with('status', 'Kode OTP baru berhasil dikirimkan ke email Anda.');
    }

    /**
     * Logout
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
