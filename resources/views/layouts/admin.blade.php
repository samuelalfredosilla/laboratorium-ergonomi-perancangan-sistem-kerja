<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Mencegah Google mengindeks halaman Admin ini -->
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · EPSK Admin Panel</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (CDN, matching the public site setup) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        maroon: {
                            50:  '#FBF1F1',
                            100: '#F5DEDE',
                            200: '#EBDADA',
                            300: '#D9A9A9',
                            400: '#B45A5A',
                            500: '#8D2B2B',
                            600: '#6B1C1C',
                            700: '#591717',
                            800: '#471212',
                            900: '#380E0E',
                        },
                    },
                    boxShadow: {
                        soft: '0 1px 2px 0 rgba(16, 24, 40, 0.04), 0 1px 3px 0 rgba(16, 24, 40, 0.06)',
                        card: '0 4px 12px -2px rgba(107, 28, 28, 0.06), 0 2px 4px -2px rgba(16, 24, 40, 0.04)',
                    },
                },
            },
        };
    </script>

    <!-- Alpine.js (interactivity: sidebar, dropdowns, tabs, modals, toasts) -->
    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D9A9A9; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #B45A5A; }
        body { font-family: 'Plus Jakarta Sans', 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>

    @stack('styles')
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')

        <!-- Mobile sidebar overlay -->
        <div x-cloak x-show="sidebarOpen" x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

        <div class="flex min-w-0 flex-1 flex-col">
            @include('admin.partials.topbar')

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white px-4 py-4 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
                &copy; {{ date('Y') }} Laboratorium Ergonomi &amp; Perancangan Sistem Kerja &middot; Universitas Trunojoyo Madura
            </footer>
        </div>
    </div>

    <!-- Toast Notification Container -->
    @include('admin.partials.toast')

    @if (session('success') || session('error'))
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: '{{ session('success') ? 'success' : 'error' }}',
                        message: @json(session('success') ?? session('error')),
                    },
                }));
            });
        </script>
    @endif

    @stack('modals')
    @stack('scripts')
</body>
</html>
