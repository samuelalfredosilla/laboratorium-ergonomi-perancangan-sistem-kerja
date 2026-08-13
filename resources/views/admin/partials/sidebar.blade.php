@php
    // Grouped navigation. `route` = named route to link; null = not built yet (shows "Soon" badge).
    $navGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Dashboard Overview', 'icon' => 'fa-gauge-high', 'route' => 'admin.dashboard'],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                ['label' => 'Lecturers & Staff', 'icon' => 'fa-users', 'route' => null],
                ['label' => 'News & Articles', 'icon' => 'fa-newspaper', 'route' => null],
                ['label' => 'Categories', 'icon' => 'fa-tags', 'route' => null],
                ['label' => 'Home Sliders', 'icon' => 'fa-images', 'route' => null],
            ],
        ],
        [
            'label' => 'Lab Assets & Services',
            'items' => [
                ['label' => 'EPSIKERS / Assistants', 'icon' => 'fa-user-check', 'route' => null],
                ['label' => 'Facilities & Tools', 'icon' => 'fa-boxes-stacked', 'route' => null],
                ['label' => 'Lab Requests / Services', 'icon' => 'fa-clipboard-list', 'route' => null],
            ],
        ],
        [
            'label' => 'System',
            'items' => [
                ['label' => 'Site Settings', 'icon' => 'fa-gear', 'route' => null],
            ],
        ],
    ];
@endphp

<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 flex w-72 shrink-0 flex-col border-r border-slate-200 bg-white transition-transform duration-200 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
>
    <!-- Brand Header -->
    <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-5">
        <img src="{{ asset('images/Logo.png') }}" alt="Logo Lab EPSK" class="h-9 w-9 rounded-lg object-contain">
        <div class="min-w-0 leading-tight">
            <p class="truncate text-sm font-bold text-maroon-600">EPSK Admin Panel</p>
            <p class="truncate text-[11px] text-slate-400">Lab EPSK &middot; UTM</p>
        </div>
        <button @click="sidebarOpen = false" class="ml-auto text-slate-400 hover:text-slate-600 lg:hidden">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        @foreach ($navGroups as $group)
            <div>
                @if ($group['label'])
                    <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $group['label'] }}</p>
                @endif
                <ul class="space-y-1">
                    @foreach ($group['items'] as $item)
                        @php $active = $item['route'] && request()->routeIs($item['route']); @endphp
                        <li>
                            <a
                                href="{{ $item['route'] ? route($item['route']) : '#' }}"
                                class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                                    {{ $active
                                        ? 'bg-maroon-600 text-white shadow-card'
                                        : 'text-slate-600 hover:bg-maroon-50 hover:text-maroon-600' }}"
                            >
                                <i class="fa-solid {{ $item['icon'] }} w-4 text-center {{ $active ? 'text-white' : 'text-slate-400 group-hover:text-maroon-500' }}"></i>
                                <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                @unless ($item['route'])
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-400 group-hover:bg-maroon-100 group-hover:text-maroon-500">Soon</span>
                                @endunless
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <!-- Footer mini profile -->
    <div class="border-t border-slate-200 p-4">
        <div class="flex items-center gap-3 rounded-lg bg-maroon-50 px-3 py-2.5">
            <img src="https://ui-avatars.com/api/?name=Admin+EPSK&background=6B1C1C&color=fff&bold=true" alt="Avatar" class="h-8 w-8 rounded-full">
            <div class="min-w-0 leading-tight">
                <p class="truncate text-xs font-semibold text-slate-700">Admin EPSK</p>
                <p class="truncate text-[11px] text-maroon-500">Super Admin</p>
            </div>
        </div>
    </div>
</aside>
