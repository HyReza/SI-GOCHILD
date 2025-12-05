<!DOCTYPE html>
<html lang="en">

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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="/images/logo2.png">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="bg-gray-100 dark:bg-gray-800">
    <div class="min-h-screen flex flex-col justify-center items-center">
        <img src="https://www.svgrepo.com/show/426192/cogs-settings.svg" alt="Logo" class="mb-8 h-40">
        <h1 class="text-xl md:text-5xl lg:text-6xl font-bold text-center text-gray-700 dark:text-white mb-4">test
            Coming
            Soon </h1>
        <p class="text-center text-gray-500 dark:text-gray-300 text-lg md:text-xl lg:text-2xl mb-8">Harap bersabar ya,
            fitur akan segera Jadi orang sabar disayang tuhan</p>
        <div class="flex space-x-4">
            <button onclick="window.history.back()">
                <a href="#"
                    class="border-2 border-gray-800 text-black font-bold py-3 px-6 rounded dark:text-white dark:border-white">Kembali
                    ke halaman sebelumnya</a>
            </button>
            <a href="#"
                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded dark:bg-gray-700 dark:hover:bg-gray-600">Contact
                Us</a>

        </div>
    </div>
</body>

</html>
