<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- SITE META --}}
    <meta name="keywords"
        content="Daycare , DAYCARE , daycare , Al-Jannah, Al Jannah, al jannah , daycare al jannah, paud al jannah ">
    <meta name="description"
        content="Kami adalah lembaga pendidikan anak usia dini dengan visi menjadi pusat tumbuh kembang anak, mewujudkan generasi sehat jasmani, rohani, beriman, dan berkarakter unggul. Daycare Al-Jannah menawarkan layanan pendidikan anak, kegiatan menyenangkan, serta perawatan personal untuk setiap anak.">
    <meta property="og:title" content="Daycare Al-Jannah">
    <meta property="og:image" content="images/logo.svg">


    <title>{{ config('app.name', 'Laravel') }}</title>

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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="antialiased bg-white dark:bg-gray-900 mt-16">
    <x-loading></x-loading>
    <x-navbar></x-navbar>
    {{ $slot }}
    <x-footer></x-footer>

    <script>
        // Import the functions you need from the SDKs you need
        import {
            initializeApp
        } from "firebase/app";
        import {
            getAnalytics
        } from "firebase/analytics";
        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
            apiKey: "AIzaSyDDFNKBTw5f65Z6J34vuxAhIC-nNQgGYaI",
            authDomain: "daycare-7ac78.firebaseapp.com",
            projectId: "daycare-7ac78",
            storageBucket: "daycare-7ac78.firebasestorage.app",
            messagingSenderId: "620135697732",
            appId: "1:620135697732:web:f0bf5cc34818a9439d5b11",
            measurementId: "G-Q8LDNYMNDX"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const analytics = getAnalytics(app);
    </script>
</body>

</html>
