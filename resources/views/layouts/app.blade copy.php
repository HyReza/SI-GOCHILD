<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    {{-- SWEET ALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- FONTS --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- CART JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



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

    {{-- SITE META --}}
    <meta name="keywords"
        content="Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah ">
    <meta name="description"
        content="Kami adalah lembaga pendidikan anak usia dini dengan visi menjadi pusat tumbuh kembang anak, mewujudkan generasi sehat jasmani, rohani, beriman, dan berkarakter unggul. Daycare Al-Jannah menawarkan layanan pendidikan anak, kegiatan menyenangkan, serta perawatan personal untuk setiap anak.">
    <meta property="og:title" content="Daycare Al-Jannah">
    <meta property="og:image" content="images/logo.png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="/images/logo2.png">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    {{-- Quill Editor --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">




    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ sidebarOpen: false }" class="antialiased bg-slate-100 dark:bg-gray-800">
    <x-loading></x-loading>
    <x-sidebar></x-sidebar>
    <div class="px-4 py-4 lg:pl-64 lg:py-6">
        {{ $slot }}
    </div>
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
