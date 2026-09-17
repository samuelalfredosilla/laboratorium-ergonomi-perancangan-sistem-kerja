<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Dynamic Title & SEO Dasar -->
    <title>@yield('title', 'EPSK Laboratory - Universitas Trunojoyo Madura')</title>
    <meta name="description" content="@yield('meta_description', 'Portal resmi Laboratorium Ergonomi dan Perancangan Sistem Kerja (EPSK) Universitas Trunojoyo Madura. Informasi praktikum, fasilitas, dan riset ergonomi.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Lab EPSK, Ergonomi UTM, Teknik Industri UTM, Praktikum Ergonomi, Lab EPSK UTM')">
    <meta name="author" content="Laboratorium EPSK UTM">

    <!-- Canonical URL (Mencegah duplikasi konten di mata Google) -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph (Agar rapi saat link web di-share ke WhatsApp/Sosmed) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'EPSK Laboratory - Universitas Trunojoyo Madura')">
    <meta property="og:description" content="@yield('meta_description', 'Portal resmi Laboratorium Ergonomi dan Perancangan Sistem Kerja (EPSK) Universitas Trunojoyo Madura.')">
    <meta property="og:image" content="@yield('meta_image', asset('images/Logo.png'))">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Favicon Utama -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/Logo.png') }}">

    <!-- Untuk Perangkat Apple / Shortcut HP -->
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-gray-50">

    @include('partials.navbar')

    <!-- PENTING: Baris ini yang menampilkan isi dari home.blade.php -->
    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Schema.org JSON-LD untuk Identitas Institusi -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "Laboratorium EPSK Universitas Trunojoyo Madura",
      "alternateName": "Lab EPSK UTM",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/Logo.png') }}",
      "description": "Portal resmi Laboratorium Ergonomi dan Perancangan Sistem Kerja (EPSK) Teknik Industri UTM.",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Bangkalan",
        "addressRegion": "Jawa Timur",
        "addressCountry": "ID"
      }
    }
    </script>
</body>
</html>
