<!-- NAVBAR HTML -->
<nav id="navbar" class="custom-nav">
    <div class="nav-container">
        <!-- Logo -->
        <div>
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo EPSK LAB" class="nav-logo-img">
            </a>
        </div>

        <!-- Desktop Menu (TIDAK DIUBAH) -->
        <div class="nav-menu-desktop">
            <!-- 1. HOME -->
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active-link' : '' }}">HOME</a>

            <!-- 2. ACTIVITIES -->
            <div class="nav-dropdown">
                <a href="javascript:void(0)" class="nav-link dropdown-toggle {{ request()->routeIs('activities.*') ? 'active-link' : '' }}">
                    ACTIVITIES <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="dropdown-menu">
                    <a href="{{ route('activities.practicum') }}" class="dropdown-item {{ request()->routeIs('activities.practicum*') ? 'active-item' : '' }}">PRACTICUM</a>
                    <a href="{{ route('activities.events') }}" class="dropdown-item {{ request()->routeIs('activities.events*') ? 'active-item' : '' }}">LABORATORY EVENTS</a>
                </div>
            </div>

            <!-- 3. FACILITIES -->
            <div class="nav-dropdown">
                <a href="javascript:void(0)" class="nav-link dropdown-toggle {{ request()->routeIs('facilities.*') ? 'active-link' : '' }}">
                    FACILITIES <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="dropdown-menu">
                    <a href="{{ route('facilities.equipment') }}" class="dropdown-item {{ request()->routeIs('facilities.equipment*') ? 'active-item' : '' }}">EQUIPMENT</a>
                </div>
            </div>

            <!-- 4. ABOUT US -->
            <div class="nav-dropdown">
                <a href="javascript:void(0)" class="nav-link dropdown-toggle {{ request()->routeIs('about.*') ? 'active-link' : '' }}">
                    ABOUT US <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="dropdown-menu">
                    <a href="{{ route('about.overview') }}" class="dropdown-item {{ request()->routeIs('about.overview') ? 'active-item' : '' }}">OVERVIEW</a>
                    <a href="{{ route('about.lecturer') }}" class="dropdown-item {{ request()->routeIs('about.lecturer') ? 'active-item' : '' }}">PROFILE LECTURER</a>
                    <a href="{{ route('about.epsikers') }}" class="dropdown-item {{ request()->routeIs('about.epsikers') ? 'active-item' : '' }}">EPSIKERS</a>
                    <a href="{{ route('about.structure') }}" class="dropdown-item {{ request()->routeIs('about.structure') ? 'active-item' : '' }}">STRUCTURE ORGANIZATION</a>
                </div>
            </div>

            <!-- 5. ACHIEVEMENT -->
            <a href="{{ route('achievement.index')}}" class="nav-link {{ request()->routeIs('achievement.*') ? 'active-link' : '' }}">ACHIEVEMENT</a>

            <!-- 6. LAYANAN LAB -->
            <a href="{{ route('procedures.index') }}" class="nav-link {{ request()->routeIs('procedures.*') ? 'active-link' : '' }}">LAB PROCEDURES</a>
        </div>

        <!-- Mobile Button (Hamburger) -->
        <button id="hamburgerBtn" class="mobile-btn">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <!-- Overlay Gelap saat Sidebar Terbuka -->
    <div id="mobileOverlay" class="mobile-overlay"></div>

    <!-- Mobile Drawer (Sidebar Menyamping) -->
    <div id="mobileDrawer" class="mobile-sidebar">
        <!-- Header Sidebar -->
        <div class="mobile-sidebar-header">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo EPSK" class="sidebar-logo">
            <button id="closeSidebarBtn" class="close-sidebar-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Konten Menu Sidebar -->
        <div class="mobile-sidebar-content">
            <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">HOME</a>

            <!-- ACTIVITIES Accordion -->
            <div class="sidebar-dropdown">
                <button class="sidebar-dropdown-btn {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                    ACTIVITIES <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="sidebar-submenu">
                    <a href="{{ route('activities.practicum') }}" class="{{ request()->routeIs('activities.practicum*') ? 'active-sub' : '' }}">PRACTICUM</a>
                    <a href="{{ route('activities.events') }}" class="{{ request()->routeIs('activities.events*') ? 'active-sub' : '' }}">LABORATORY EVENTS</a>
                </div>
            </div>

            <!-- FACILITIES Accordion -->
            <div class="sidebar-dropdown">
                <button class="sidebar-dropdown-btn {{ request()->routeIs('facilities.*') ? 'active' : '' }}">
                    FACILITIES <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="sidebar-submenu">
                    <a href="{{ route('facilities.equipment') }}" class="{{ request()->routeIs('facilities.equipment*') ? 'active-sub' : '' }}">EQUIPMENT</a>
                </div>
            </div>

            <!-- ABOUT US Accordion -->
            <div class="sidebar-dropdown">
                <button class="sidebar-dropdown-btn {{ request()->routeIs('about.*') ? 'active' : '' }}">
                    ABOUT US <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="sidebar-submenu">
                    <a href="{{ route('about.overview') }}" class="{{ request()->routeIs('about.overview') ? 'active-sub' : '' }}">OVERVIEW</a>
                    <a href="{{ route('about.lecturer') }}" class="{{ request()->routeIs('about.lecturer') ? 'active-sub' : '' }}">PROFILE LECTURER</a>
                    <a href="{{ route('about.epsikers') }}" class="{{ request()->routeIs('about.epsikers') ? 'active-sub' : '' }}">EPSIKERS</a>
                    <a href="{{ route('about.structure') }}" class="{{ request()->routeIs('about.structure') ? 'active-sub' : '' }}">STRUCTURE ORGANIZATION</a>
                </div>
            </div>

            <a href="{{ route('achievement.index') }}" class="sidebar-link {{ request()->routeIs('achievement.*') ? 'active' : '' }}">ACHIEVEMENT</a>

            <a href="{{ route('procedures.index') }}" class="sidebar-link {{ request()->routeIs('procedures.*') ? 'active' : '' }}">LAB PROCEDURES</a>
        </div>
    </div>
</nav>

<style>
    /* =========================================
       CSS KHUSUS MOBILE SIDEBAR
       ========================================= */

    /* Sembunyikan elemen sidebar di Desktop */
    .mobile-overlay, .mobile-sidebar {
        display: none;
    }

    @media (max-width: 992px) {
        /* Memastikan Desktop Menu hilang di HP */
        .nav-menu-desktop {
            display: none !important;
        }

        /* Overlay Gelap */
        .mobile-overlay {
            display: block;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Sidebar Container */
        .mobile-sidebar {
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            right: -100%; /* Sembunyi di sebelah kanan layar */
            width: 280px;
            max-width: 80%;
            height: 100vh;
            background-color: #7b152e; /* Warna Maroon Navbar */
            z-index: 999;
            box-shadow: -5px 0 25px rgba(0,0,0,0.2);
            transition: right 0.3s ease-in-out;
            overflow-y: auto;
        }
        .mobile-sidebar.show {
            right: 0; /* Muncul ke layar */
        }

        /* Header Sidebar */
        .mobile-sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-logo {
            height: 40px;
        }
        .close-sidebar-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .close-sidebar-btn:active {
            transform: scale(0.9);
        }

        /* Konten Menu */
        .mobile-sidebar-content {
            display: flex;
            flex-direction: column;
            padding: 1rem 0;
        }

        /* Link Utama & Tombol Accordion */
        .sidebar-link, .sidebar-dropdown-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            color: #ffffff;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 700;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.2s;
            cursor: pointer;
        }
        .sidebar-link:hover, .sidebar-dropdown-btn:hover,
        .sidebar-link.active, .sidebar-dropdown-btn.active {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Animasi Panah Dropdown */
        .sidebar-dropdown-btn i {
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }
        .sidebar-dropdown-btn.open i {
            transform: rotate(180deg);
        }

        /* Submenu Accordion */
        .sidebar-submenu {
            display: none; /* Disembunyikan secara default */
            flex-direction: column;
            background: rgba(0, 0, 0, 0.15); /* Lebih gelap sedikit untuk submenu */
        }
        .sidebar-submenu.show {
            display: flex;
        }
        .sidebar-submenu a {
            padding: 0.85rem 1.5rem 0.85rem 2.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            transition: color 0.2s;
        }
        .sidebar-submenu a:hover, .sidebar-submenu a.active-sub {
            color: #ffffff;
            font-weight: 700;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        const mobileDrawer = document.getElementById('mobileDrawer');
        const mobileOverlay = document.getElementById('mobileOverlay');

        // Fungsi Buka Tutup Sidebar
        function toggleSidebar() {
            mobileDrawer.classList.toggle('show');
            mobileOverlay.classList.toggle('show');
        }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
        if (mobileOverlay) mobileOverlay.addEventListener('click', toggleSidebar);

        // Fungsi Accordion untuk Dropdown Menu di Mobile
        const dropdownBtns = document.querySelectorAll('.sidebar-dropdown-btn');
        dropdownBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Toggle rotasi panah
                this.classList.toggle('open');

                // Cari submenu yang ada di bawah tombol yang diklik
                const submenu = this.nextElementSibling;
                submenu.classList.toggle('show');
            });
        });
    });
</script>
