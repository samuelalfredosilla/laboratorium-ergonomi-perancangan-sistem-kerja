<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · EPSK Admin Panel</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        maroon: {
                            50: '#FBF1F1', 100: '#F5DEDE', 200: '#EBDADA', 300: '#D9A9A9',
                            400: '#B45A5A', 500: '#8D2B2B', 600: '#6B1C1C', 700: '#591717',
                            800: '#471212', 900: '#380E0E',
                        },
                    },
                },
            },
        };
    </script>

    <style>body { font-family: 'Plus Jakarta Sans', 'Inter', ui-sans-serif, system-ui, sans-serif; }</style>
</head>
<body class="flex min-h-screen items-center justify-center bg-[#F8FAFC] px-4 py-12">

    <div class="w-full max-w-sm">
        <div class="mb-7 flex flex-col items-center text-center">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo Lab EPSK" class="h-14 w-14 rounded-xl object-contain shadow-soft">
            <h1 class="mt-4 text-lg font-bold text-maroon-600">EPSK Admin Panel</h1>
            <p class="mt-1 text-xs text-slate-500">Laboratorium Ergonomi &amp; Perancangan Sistem Kerja<br>Universitas Trunojoyo Madura</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-7 shadow-[0_10px_24px_-12px_rgba(107,28,28,0.18)]">
            <h2 class="text-base font-bold text-slate-800">Masuk ke Akun Anda</h2>
            <p class="mt-1 text-xs text-slate-500">Khusus untuk admin &amp; asisten laboratorium.</p>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="admin"
                        class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-600">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-10 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-maroon-500">
                            <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700">
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-maroon-600"><i class="fa-solid fa-arrow-left mr-1"></i>Kembali ke situs utama</a>
        </p>
    </div>

    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
</body>
</html>
