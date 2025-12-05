<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- SITE META --}}
    <meta name="keywords" content="{{ $seo_key ?? '' }}">
    <meta name="description" content="{{ $seo_description ?? '' }}">
    <meta property="og:title" content="{{ $seo_meta_title ?? '' }}">
    <meta property="og:image" content="images/logo.svg">
    <meta property="og:url" content="https://aljannah.sch.id">
    <meta property="og:type" content="website">
    <meta name="author" content="Al Jannah Preschool and Day Care">


    <title>{{ $seo_title ?? config('app.name', 'Al Jannah') }}</title>

    <link rel="icon" type="image/x-icon" href="/images/logo2.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- GOOGLE FONT --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    {{-- GOOGLE ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    {{-- PARTICLES JS --}}
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    {{-- SEO ANALYTIC --}}
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-NZZ4SF9Q');
    </script>
    <!-- End Google Tag Manager -->

    {{-- Quill Editor --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="antialiased bg-slate-50 dark:bg-gray-900 mt-16">
    <x-loading></x-loading>
    <x-navbar></x-navbar>
    {{ $slot }}
    <x-footer></x-footer>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NZZ4SF9Q" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <script type="module">
        // Mengimpor Firebase dari CDN
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/9.0.2/firebase-app.js";
        import {
            getAnalytics
        } from "https://www.gstatic.com/firebasejs/9.0.2/firebase-analytics.js";

        // Konfigurasi aplikasi Firebase
        const firebaseConfig = {
            apiKey: "AIzaSyDDFNKBTw5f65Z6J34vuxAhIC-nNQgGYaI",
            authDomain: "daycare-7ac78.firebaseapp.com",
            projectId: "daycare-7ac78",
            storageBucket: "daycare-7ac78.firebasestorage.app",
            messagingSenderId: "620135697732",
            appId: "1:620135697732:web:f0bf5cc34818a9439d5b11",
            measurementId: "G-Q8LDNYMNDX"
        };

        // Inisialisasi Firebase
        const app = initializeApp(firebaseConfig);
        const analytics = getAnalytics(app);
    </script>
</body>

</html>
