<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA · EPSK Admin Panel</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
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
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="flex min-h-screen items-center justify-center bg-[#F8FAFC] px-4 py-12">

    <div class="w-full max-w-sm">
        <div class="mb-7 flex flex-col items-center text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-maroon-50 text-maroon-600 shadow-sm border border-maroon-100">
                <i class="fa-solid fa-shield-halved text-2xl"></i>
            </div>
            <h1 class="mt-4 text-lg font-bold text-slate-800">Verifikasi Dua Langkah</h1>
            <p class="mt-1 text-xs text-slate-500">
                Kode 6 digit telah dikirimkan ke email<br>
                <span class="font-semibold text-slate-700">{{ Str::mask($user->email ?? 'admin@utm.ac.id', '*', 3, -4) }}</span>
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-7 shadow-[0_10px_24px_-12px_rgba(107,28,28,0.18)]">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
                    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.2fa.verify') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-center text-xs font-semibold text-slate-600">Masukkan 6 Digit Kode OTP</label>
                    <input type="text" name="code" maxlength="6" autofocus required placeholder="000000"
                        class="w-full text-center text-2xl tracking-[0.35em] font-bold text-slate-800 rounded-lg border border-slate-200 px-3.5 py-2.5 focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                </div>

                <button type="submit" class="w-full rounded-lg bg-maroon-600 px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-maroon-700 transition-colors">
                    Verifikasi &amp; Masuk Dashboard
                </button>
            </form>

            <div class="mt-5 border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-slate-500 mb-1.5">Tidak menerima kode?</p>
                <form method="POST" action="{{ route('login.2fa.resend') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-maroon-600 hover:text-maroon-800 hover:underline">
                        <i class="fa-solid fa-rotate-right mr-1"></i> Kirim ulang kode OTP
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            <a href="{{ route('login') }}" class="hover:text-maroon-600">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke halaman login
            </a>
        </p>
    </div>

</body>
</html>
