<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BHUMI — Sistem Deteksi Penyakit Tanaman Jagung berbasis CNN. Upload foto daun jagung untuk identifikasi penyakit secara cepat dan akurat.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BHUMI — Deteksi Penyakit Tanaman Jagung')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- AOS - Animate on Scroll --}}
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- AOS Init --}}
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: false,
            offset: 80,
        });
    </script>
</body>
</html>
