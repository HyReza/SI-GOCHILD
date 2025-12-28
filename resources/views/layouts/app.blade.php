<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Daycare Al-Jannah') }}</title>
    <link rel="icon" type="image/x-icon" href="/images/logo2.png">

    {{-- FONTS & ICONS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    {{-- LIBRARIES --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

    {{-- META --}}
    <meta name="description" content="Daycare Al-Jannah - Pusat tumbuh kembang anak.">
    <meta property="og:title" content="Daycare Al-Jannah">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes swing {
            0% {
                transform: rotate(0deg);
            }

            20% {
                transform: rotate(15deg);
            }

            40% {
                transform: rotate(-10deg);
            }

            60% {
                transform: rotate(5deg);
            }

            80% {
                transform: rotate(-5deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .hover\:animate-swing:hover {
            animation: swing 0.5s ease-in-out;
        }
    </style>

    {{-- ⚡️ PENTING: TEMPAT INJECT SCRIPT DARI VIEW --}}
    @stack('head')
</head>

<body x-data="{ sidebarOpen: false }" class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
    @php
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\Storage; // Tambahkan ini

        $segment = Request::segment(1) ?? 'dashboard';

        // --- Mapping Judul ---
        $pageTitles = [
            'dashboard' => 'Dashboard',
            'service-catalog' => 'Order Layanan',
            'reports' => 'Raport Siswa',
            'student-daily-report' => 'Laporan Harian Siswa',
            'measurements' => 'Laporan Pertumbuhan',
            // ... (lanjutan mapping titles Anda) ...
        ];
        $currentTitle = $pageTitles[$segment] ?? ucwords(str_replace('-', ' ', $segment));

        // --- Logika Penentuan Foto Profil ---
        $defaultImage = asset('images/profile-1.png');
        $photoUrl = $defaultImage;

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            // Asumsi: foto_user disimpan di storage/app/public/foto_user/
            if ($user->foto_user && Storage::disk('public')->exists('foto_user/' . $user->foto_user)) {
                $photoUrl = asset('storage/foto_user/' . $user->foto_user);
            }
        } elseif (Auth::guard('student')->check()) {
            $user = Auth::guard('student')->user();
            // Asumsi: student juga menggunakan kolom foto_user di tabel students
            if ($user->foto_user && Storage::disk('public')->exists('foto_user/' . $user->foto_user)) {
                $photoUrl = asset('storage/foto_user/' . $user->foto_user);
            } else {
                // Fallback ke UI Avatars jika tidak ada foto sama sekali untuk student
                $photoUrl =
                    'https://ui-avatars.com/api/?name=' .
                    urlencode($user->student_name ?? 'Student') .
                    '&background=fbcfe8&color=9d174d&bold=true';
            }
        }

    @endphp
    {{-- ... (Bagian body ke bawah TETAP SAMA seperti kode Anda sebelumnya) ... --}}
    @php
        $segment = Request::segment(1) ?? 'dashboard';
        // ... array titles ...
        $pageTitles = [
            'dashboard' => 'Dashboard',
            'service-catalog' => 'Order Layanan',
            'themes' => 'Master Tema',
            'subthemes' => 'Sub Tema',
            'material' => 'Materi Pembelajaran',
            'growth-standards' => 'Standar Pertumbuhan',
            'category-parameter' => 'Kategori MMDST',
            'mmdst-parameter' => 'Parameter MMDST',
            'catalog-service' => 'Katalog Layanan',
            'catalog-programs' => 'Katalog Program',
            'extra-services' => 'Layanan Tambahan',
            'gallery-activity' => 'Galeri Aktivitas',
            'articles' => 'Artikel & Berita',
            'categories' => 'Kategori Blog',
            'siswa' => 'Data Siswa',
            'pengajar' => 'Data Pengajar',
            'admin' => 'Data Admin',
            'attendance' => 'Absensi Harian',
            'daily-report' => 'Laporan Harian',
            'measurement' => 'Laporan Pertumbuhan',
            'mmdst' => 'Laporan Perkembangan',
            'reports' => 'Raport Siswa',
            'coming-soon' => 'Segera Hadir',
            'profile' => 'Profil Saya',
            'student-daily-report' => 'Laporan Harian Siswa',
            'measurements' => 'Laporan Pertumbuhan',
            'development-reports' => 'Laporan Perkembangan',
        ];
        $currentTitle = $pageTitles[$segment] ?? ucwords(str_replace('-', ' ', $segment));
    @endphp
    <x-loading></x-loading>

    <div class="flex h-screen overflow-hidden">

        <x-sidebar />

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

            <header
                class="sticky top-0 z-20 w-full bg-white/80 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 shadow-sm transition-all">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 text-gray-600 bg-gray-100 rounded-lg lg:hidden hover:text-emerald-600 hover:bg-emerald-50 transition focus:outline-none">
                            <span class="material-symbols-outlined text-2xl">menu</span>
                        </button>

                        <h1
                            class="text-lg font-bold text-gray-800 dark:text-white capitalize tracking-tight line-clamp-1">
                            {{ $currentTitle }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-4">

                        <div class="relative" x-data="{ notifOpen: false }">
                            <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false"
                                class="relative p-2 text-gray-500 hover:text-emerald-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-all duration-200 focus:outline-none hover:animate-swing">
                                <span class="material-symbols-outlined text-[26px]">notifications</span>
                                {{-- Mockup Badge --}}
                                {{-- <span
                                    class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900 text-[9px] font-bold text-white">3</span> --}}
                            </button>
                            {{-- Dropdown Content (Mockup) --}}
                            <div x-show="notifOpen" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-2" /* Responsive Positioning:
                                Fixed di mobile, Absolute di Desktop */
                                class="fixed md:absolute inset-x-4 md:inset-x-auto md:right-0 mt-3 md:w-96 bg-white dark:bg-gray-900 rounded-[2rem] shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden z-50"
                                style="display: none;">

                                <div
                                    class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                                    <h3
                                        class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-widest">
                                        Notifikasi</h3>
                                    <span
                                        class="text-[10px] bg-emerald-500/10 text-emerald-600 px-2 py-0.5 rounded-full font-bold">Terbaru</span>
                                </div>

                                <div class="p-8 text-center space-y-4">
                                    <div
                                        class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto transition-transform hover:scale-110">
                                        <span class="material-symbols-outlined text-gray-400">notifications_off</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Belum ada
                                            notifikasi baru</p>
                                    </div>
                                </div>

                                <div
                                    class="px-4 py-3 bg-gray-50 dark:bg-gray-800/30 text-center border-t border-gray-100 dark:border-gray-800">
                                    <button @click="notifOpen = false"
                                        class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-emerald-500 transition-colors">
                                        Tutup Panel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                                class="flex items-center gap-3 focus:outline-none group pl-2 pr-1 py-1 rounded-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-300 border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                                <div class="hidden md:block text-right">
                                    <p
                                        class="text-sm font-bold text-gray-700 dark:text-gray-200 leading-tight group-hover:text-emerald-600 transition-colors">
                                        @if (Auth::guard('web')->check())
                                            {{ Auth::guard('web')->user()->user_name }}
                                        @elseif(Auth::guard('student')->check())
                                            {{ Auth::guard('student')->user()->student_name }}
                                        @else
                                            Guest
                                        @endif
                                    </p>
                                    <p
                                        class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        @if (Auth::guard('web')->check())
                                            {{ Auth::guard('web')->user()->role->role_name == 'teacher' ? 'PENGAJAR' : Auth::guard('web')->user()->role->role_name ?? 'User' }}
                                        @elseif(Auth::guard('student')->check())
                                            Siswa
                                        @endif
                                    </p>
                                </div>
                                {{-- <div class="relative">
                                    <img src="@if (Auth::guard('web')->check()) {{ Auth::guard('web')->user()->foto_user ? asset('foto_user/' . Auth::guard('web')->user()->foto_user) : asset('images/profile-1.png') }}
                                              @elseif(Auth::guard('student')->check()) {{ Auth::guard('student')->user()->foto_user ? asset('foto_user/' . Auth::guard('student')->user()->foto_user) : asset('images/profile-1.png') }}
                                              @else {{ asset('images/profile-1.png') }} @endif"
                                        alt="Profile"
                                        class="h-9 w-9 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow-sm group-hover:border-emerald-500 transition-colors">
                                </div> --}}
                                <div class="relative">
                                    <img src="{{ $photoUrl }}" alt="Profile"
                                        class="h-9 w-9 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow-sm group-hover:border-emerald-500 transition-colors">
                                </div>
                                <span
                                    class="material-symbols-outlined text-gray-400 text-xl transition-transform duration-300 hidden sm:block"
                                    :class="profileOpen ? 'rotate-180 text-emerald-500' : ''">expand_more</span>
                            </button>

                            <div x-show="profileOpen" x-transition
                                class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50 transform origin-top-right"
                                style="display: none;">
                                <div class="p-1">
                                    {{-- <a href="{{ route('profile.edit') }}"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-gray-800 rounded-xl transition-all group">
                                        <span
                                            class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-emerald-500">person</span>
                                        <span class="font-medium">Profil Saya</span>
                                    </a> --}}
                                    <div class="h-px bg-gray-100 dark:bg-gray-700 my-1 mx-2"></div>
                                    <form action="{{ Auth::guard('student')->check() ? '/logout-student' : '/logout' }}"
                                        method="post">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 dark:text-red-400 rounded-xl transition-all group">
                                            <span
                                                class="material-symbols-outlined text-[20px] group-hover:rotate-180 transition-transform duration-300">logout</span>
                                            <span class="font-semibold">Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-grow p-4 lg:p-8">
                {{ $slot }}
            </main>

            <footer class="px-8 py-6 text-center text-xs text-gray-400 border-t border-gray-100 dark:border-gray-800">
                &copy; {{ date('Y') }} Daycare Al-Jannah. All rights reserved.
            </footer>
        </div>
    </div>

    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NZZ4SF9Q" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/9.0.2/firebase-app.js";
        import {
            getAnalytics
        } from "https://www.gstatic.com/firebasejs/9.0.2/firebase-analytics.js";
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key', 'AIzaSyDDFNKBTw5f65Z6J34vuxAhIC-nNQgGYaI') }}",
            authDomain: "daycare-7ac78.firebaseapp.com",
            projectId: "daycare-7ac78",
            storageBucket: "daycare-7ac78.firebasestorage.app",
            messagingSenderId: "620135697732",
            appId: "1:620135697732:web:f0bf5cc34818a9439d5b11",
            measurementId: "G-Q8LDNYMNDX"
        };
        const app = initializeApp(firebaseConfig);
        const analytics = getAnalytics(app);
    </script>
</body>

</html>
