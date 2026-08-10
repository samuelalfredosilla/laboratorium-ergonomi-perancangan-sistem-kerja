<!-- NAVBAR HTML -->
<nav id="navbar" class="custom-nav">
    <div class="nav-container">
        <!-- Logo -->
        <div>
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo EWDPI LAB" class="nav-logo-img">
            </a>
        </div>

        <!-- Desktop Menu -->
        <div class="nav-menu-desktop">
            <a href="{{ route('home') }}" class="nav-link active-link">HOME</a>
            <a href="#about" class="nav-link">ABOUT US</a>
            <a href="#activities" class="nav-link">ACTIVITIES</a>
            <a href="#facilities" class="nav-link">FACILITIES</a>
            <a href="#mini-thesis" class="nav-link">ACHIEVEMENT</a>
            <a href="https://www2.trunojoyo.ac.id/" target="_blank" class="btn-utm">UTM OFFICIAL</a>
        </div>

        <!-- Mobile Button -->
        <button id="hamburgerBtn" class="mobile-btn">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobileDrawer" class="mobile-drawer">
        <a href="{{ route('home') }}" class="nav-link active-link">HOME</a>
        <a href="#about" class="nav-link">ABOUT US</a>
        <a href="#activities" class="nav-link">ACTIVITIES</a>
        <a href="#facilities" class="nav-link">FACILITIES</a>
        <a href="#mini-thesis" class="nav-link">ACHIEVEMENT</a>
        <a href="https://www.trunojoyo.ac.id" target="_blank" class="nav-link">UTM OFFICIAL</a>
    </div>
</nav>
