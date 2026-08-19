@php
    $recentNotifications = \App\Models\Notification::latest()->take(8)->get();
    $unreadNotifCount = \App\Models\Notification::where('is_read', false)->count();
    $notifColorMap = [
        'success' => 'bg-emerald-50 text-emerald-600',
        'warning' => 'bg-amber-50 text-amber-600',
        'danger'  => 'bg-red-50 text-red-600',
        'maroon'  => 'bg-maroon-50 text-maroon-600',
    ];
@endphp
<header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
    <!-- Mobile hamburger -->
    <button @click="sidebarOpen = true" class="text-slate-500 hover:text-maroon-600 lg:hidden">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <!-- Global Search -->
    <div
        class="relative hidden max-w-md flex-1 sm:block"
        x-data="{
            open: false,
            query: '',
            loading: false,
            results: { lecturers: [], news: [], menu: [] },
            get hasResults() { return this.results.lecturers.length || this.results.news.length || this.results.menu.length; },
            search() {
                if (this.query.trim().length < 2) { this.results = { lecturers: [], news: [], menu: [] }; this.open = false; return; }
                this.loading = true;
                fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(this.query))
                    .then(r => r.json())
                    .then(data => { this.results = data; this.open = true; this.loading = false; })
                    .catch(() => { this.loading = false; });
            },
        }"
        @click.outside="open = false"
    >
        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
        <input
            type="text"
            x-model="query"
            @input.debounce.300ms="search()"
            @focus="if (query.trim().length >= 2) open = true"
            placeholder="Cari dosen, berita, atau menu..."
            autocomplete="off"
            class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-maroon-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-100"
        >

        <div x-cloak x-show="open" x-transition
            class="absolute left-0 right-0 z-30 mt-2 max-h-96 overflow-y-auto rounded-xl border border-slate-200 bg-white py-2 shadow-xl">

            <template x-if="results.menu.length">
                <div class="mb-1">
                    <p class="px-4 pb-1 pt-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Menu</p>
                    <template x-for="item in results.menu" :key="'m' + item.label">
                        <a :href="item.url" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                            <i class="fa-solid w-4 text-center text-slate-400" :class="item.icon"></i>
                            <span x-text="item.label"></span>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="results.lecturers.length">
                <div class="mb-1 border-t border-slate-100 pt-1">
                    <p class="px-4 pb-1 pt-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Dosen</p>
                    <template x-for="item in results.lecturers" :key="'l' + item.id">
                        <a :href="item.url" class="flex items-center gap-3 px-4 py-2 hover:bg-maroon-50">
                            <img :src="item.photo" class="h-7 w-7 shrink-0 rounded-full object-cover">
                            <div class="min-w-0">
                                <p class="truncate text-sm text-slate-700" x-text="item.name"></p>
                                <p class="truncate text-xs text-slate-400" x-text="item.subtitle"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="results.news.length">
                <div class="border-t border-slate-100 pt-1">
                    <p class="px-4 pb-1 pt-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Berita</p>
                    <template x-for="item in results.news" :key="'n' + item.id">
                        <a :href="item.url" class="flex items-center gap-3 px-4 py-2 hover:bg-maroon-50">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-maroon-50 text-maroon-500"><i class="fa-solid fa-newspaper text-xs"></i></span>
                            <div class="min-w-0">
                                <p class="truncate text-sm text-slate-700" x-text="item.title"></p>
                                <p class="truncate text-xs text-slate-400" x-text="item.subtitle"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </template>

            <p x-show="!hasResults && !loading" class="px-4 py-6 text-center text-xs text-slate-400">Tidak ada hasil untuk pencarian ini.</p>
            <p x-show="loading" class="px-4 py-6 text-center text-xs text-slate-400">Mencari...</p>
        </div>
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
                <a href="{{ route('admin.assistants.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-user-check w-4 text-slate-400"></i> Tambah Asisten
                </a>
                <a href="{{ route('admin.news.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-newspaper w-4 text-slate-400"></i> Tulis Berita
                </a>
                <a href="{{ route('admin.sliders.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-maroon-50 hover:text-maroon-600">
                    <i class="fa-solid fa-image w-4 text-slate-400"></i> Upload Slider
                </a>
            </div>
        </div>

        <!-- Notification Bell -->
        <div
            class="relative"
            x-data="{
                open: false,
                unread: {{ $unreadNotifCount }},
                toggle() {
                    this.open = !this.open;
                    if (this.open && this.unread > 0) {
                        fetch('{{ route('admin.notifications.read-all') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                        }).then(() => { this.unread = 0; });
                    }
                },
            }"
        >
            <button @click="toggle()" @click.outside="open = false"
                class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-maroon-600">
                <i class="fa-regular fa-bell text-lg"></i>
                <span x-cloak x-show="unread > 0" class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>

            <div x-cloak x-show="open" x-transition
                class="absolute right-0 z-30 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-semibold text-slate-700">Notifikasi</p>
                    <div class="flex items-center gap-2">
                        <span x-cloak x-show="unread > 0" class="rounded-full bg-maroon-50 px-2 py-0.5 text-[11px] font-semibold text-maroon-600" x-text="unread + ' baru'"></span>
                        <button @click="open = false" type="button" class="flex h-6 w-6 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </div>
                <ul class="max-h-72 divide-y divide-slate-100 overflow-y-auto">
                    @forelse ($recentNotifications as $notif)
                        <li>
                            <a href="{{ $notif->url ?? '#' }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $notifColorMap[$notif->color] ?? $notifColorMap['maroon'] }}">
                                    <i class="fa-solid {{ $notif->icon }} text-xs"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm text-slate-700">{{ $notif->message }}</p>
                                    <p class="text-xs text-slate-400">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-xs text-slate-400">Belum ada notifikasi.</li>
                    @endforelse
                </ul>
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
