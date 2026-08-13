<header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
    <!-- Mobile hamburger -->
    <button @click="sidebarOpen = true" class="text-slate-500 hover:text-maroon-600 lg:hidden">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <!-- Global Search -->
    <div class="relative hidden max-w-md flex-1 sm:block">
        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
        <input
            type="text"
            placeholder="Cari dosen, berita, atau menu..."
            class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100"
        >
    </div>

    <!-- Mobile search trigger -->
    <button class="ml-auto text-slate-500 hover:text-maroon-600 sm:hidden">
        <i class="fa-solid fa-magnifying-glass text-lg"></i>
    </button>

    <div class="ml-auto flex items-center gap-2 sm:ml-3 sm:gap-3">

        <!-- Quick Add Shortcut -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false"
                class="hidden items-center gap-2 rounded-lg bg-maroon-600 px-3.5 py-2 text-sm font-semibold text-white shadow-soft transition-colors hover:bg-maroon-700 sm:flex">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add New</span>
                <i class="fa-solid fa-chevron-down text-[10px] opacity-70"></i>
            </button>
            <button @click="open = !open" class="flex h-9 w-9 items-center justify-center rounded-lg bg-maroon-600 text-white shadow-soft hover:bg-maroon-700 sm:hidden">
                <i class="fa-solid fa-plus text-sm"></i>
            </button>

            <div x-cloak x-show="open" x-transition
                class="absolute right-0 z-30 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1.5 shadow-xl">
                <a href="{{ route('admin.lecturers.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-user-plus w-4 text-slate-400"></i> Tambah Dosen
                </a>
                <a href="{{ route('admin.news.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-pen-nib w-4 text-slate-400"></i> Buat Berita
                </a>
                <a href="{{ route('admin.sliders.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-image w-4 text-slate-400"></i> Upload Slider
                </a>
            </div>
        </div>

        <!-- Notification Bell -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false"
                class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-maroon-600">
                <i class="fa-regular fa-bell text-lg"></i>
                <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>

            <div x-cloak x-show="open" x-transition
                class="absolute right-0 z-30 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-semibold text-slate-700">Notifikasi</p>
                    <span class="rounded-full bg-maroon-50 px-2 py-0.5 text-[11px] font-semibold text-maroon-600">3 baru</span>
                </div>
                <ul class="max-h-72 divide-y divide-slate-100 overflow-y-auto">
                    <li class="flex gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-maroon-50 text-maroon-600"><i class="fa-solid fa-newspaper text-xs"></i></span>
                        <div class="min-w-0">
                            <p class="truncate text-sm text-slate-700">Berita <b>"Seminar Ergonomi 2026"</b> menunggu review.</p>
                            <p class="text-xs text-slate-400">5 menit lalu</p>
                        </div>
                    </li>
                    <li class="flex gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"><i class="fa-solid fa-user-plus text-xs"></i></span>
                        <div class="min-w-0">
                            <p class="truncate text-sm text-slate-700">Profil dosen <b>Dr. Anisa R.</b> berhasil diperbarui.</p>
                            <p class="text-xs text-slate-400">2 jam lalu</p>
                        </div>
                    </li>
                    <li class="flex gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600"><i class="fa-solid fa-image text-xs"></i></span>
                        <div class="min-w-0">
                            <p class="truncate text-sm text-slate-700">Slider baru menunggu persetujuan tayang.</p>
                            <p class="text-xs text-slate-400">1 hari lalu</p>
                        </div>
                    </li>
                </ul>
                <a href="#" class="block border-t border-slate-100 px-4 py-2.5 text-center text-xs font-semibold text-maroon-600 hover:bg-maroon-50">Lihat semua notifikasi</a>
            </div>
        </div>

        <div class="h-8 w-px bg-slate-200"></div>

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2.5 rounded-lg py-1 pl-1 pr-2 hover:bg-slate-100">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6B1C1C&color=fff&bold=true" alt="Avatar" class="h-8 w-8 rounded-full">
                <span class="hidden text-left leading-tight sm:block">
                    <span class="block text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</span>
                    <span class="inline-flex items-center rounded-full bg-maroon-50 px-1.5 py-0.5 text-[10px] font-semibold text-maroon-600">{{ ucfirst(auth()->user()->role) }}</span>
                </span>
                <i class="fa-solid fa-chevron-down hidden text-[10px] text-slate-400 sm:block"></i>
            </button>

            <div x-cloak x-show="open" x-transition
                class="absolute right-0 z-30 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1.5 shadow-xl">
                <div class="border-b border-slate-100 px-4 py-2.5">
                    <p class="truncate text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ '@' . auth()->user()->username }}</p>
                </div>
                <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-user w-4 text-slate-400"></i> Edit Profil
                </a>
                <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-gear w-4 text-slate-400"></i> Pengaturan
                </a>
                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
