<!-- NAVBAR HTML -->
<nav id="navbar" class="custom-nav">
    <div class="nav-container">
        <!-- Logo -->
        <div>
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo EPSK LAB" class="nav-logo-img">
            </a>
        </div>

        <!-- Desktop Menu -->
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
            <a href="#facilities" class="nav-link">FACILITIES</a>

            <!-- 4. ABOUT US (Dropdown) -->
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
            <a href="#mini-thesis" class="nav-link">ACHIEVEMENT</a>

            <!-- 6. LAYANAN LAB (Sudah disamakan dengan menu lain) -->
            <a href="#services" class="nav-link">LAB PROCEDURES</a>
        </div>

        <!-- Mobile Button -->
        <button id="hamburgerBtn" class="mobile-btn">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobileDrawer" class="mobile-drawer">
        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active-link' : '' }}">HOME</a>
        <a href="#activities" class="nav-link">ACTIVITIES</a>
        <a href="#facilities" class="nav-link">FACILITIES</a>
        <a href="{{ route('about.overview') }}" class="nav-link {{ request()->routeIs('about.*') ? 'active-link' : '' }}">ABOUT US</a>
        <a href="#mini-thesis" class="nav-link">ACHIEVEMENT</a>
        <a href="#services" class="nav-link">LAYANAN LAB</a>
    </div>
</nav>
