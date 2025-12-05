<div :class="sidebarOpen ? 'fixed lg:fixed' : 'hidden lg:block lg:fixed lg:translate-x-0'"
    x-show="sidebarOpen || window.innerWidth >= 1024" @click.away="if (window.innerWidth < 1024) sidebarOpen = false"
    x-transition:enter="transition transform ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-85 lg:opacity-100"
    x-transition:leave="transition transform ease-in-out duration-300"
    x-transition:leave-start="translate-x-0 opacity-85 lg:opacity-100"
    x-transition:leave-end="-translate-x-full opacity-0"
    class="bg-white dark:bg-gray-900 h-lvh w-56 shadow-xl fixed inset-0 lg:static z-30">
    <div class="p-4 text-start justify-start">
        <div class="flex mb-6 gap-4 items-center">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="w-40 lg:w-52">
            <button>
                <span @click="sidebarOpen = false"
                    class="material-symbols-outlined lg:hidden text-gray-800 dark:text-gray-400">close</span>
            </button>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 h-[calc(100vh-10rem)] overflow-y-auto scrollbar-hide">
            <h1 class="py-2">Menu Utama</h1>
            {{-- MENU ADMIN --}}
            {{-- MENU DASHBOARD --}}
            <div
                class="flex {{ request()->is('dashboard', 'dashboard_user') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                <div
                    class="flex h-full w-1 {{ request()->is('dashboard', 'dashboard_user') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                </div>
                <a href="{{ url('dashboard') }}" class="flex">
                    <div
                        class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('', 'dashboard') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg ">dashboard</span>
                        <h1>Dashboard</h1>
                    </div>
                </a>
            </div>
            <h1 class="py-2">Master laporan</h1>
            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'admin')
            <div
                class="flex {{ request()->is('absensi', 'absensi/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                <div
                    class="flex h-full w-1 {{ request()->is('absensi', 'absensi/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                </div>
                <a href="{{ route('attendance.index') }}" class="flex">
                    <div
                        class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('absensi', 'absensi/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg ">checklist_rtl</span>
                        <h1>Absensi Harian</h1>
                    </div>
                </a>
            </div>
            {{-- MENU LAPORAN HARIAN --}}
            <div
                class="flex {{ request()->is('laporan-harian', 'laporan-harian/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                <div
                    class="flex h-full w-1 {{ request()->is('laporan-harian', 'laporan-harian/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                </div>
                <a href="{{ url('laporan-harian') }}" class="flex">
                    <div
                        class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('laporan-harian', 'laporan-harian/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg ">edit_note</span>
                        <h1>Laporan Harian</h1>
                    </div>
                </a>
            </div>
            {{-- MENU RAPORT --}}
            <div x-data="{ dropdownOpen: {{ request()->is('add-book', 'books-list', 'books/*') ? 'true' : 'false' }} }" class="relative">
                <div @click="dropdownOpen = !dropdownOpen"
                    class="flex {{ request()->is('add-book', 'books-list', 'books/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                    <div
                        class="flex {{ request()->is('add-book', 'books-list', 'books/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                    </div>
                    <div
                        class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('add-book', 'books-list', 'books/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg">description</span>
                        <h1>Raport</h1>
                        <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                        <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                    </div>
                </div>
                <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-48 bg-white dark:bg-gray-900">
                    <a href="{{ url('add-book') }}"
                        class="block {{ request()->is('add-book') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">tambah
                        raport</a>
                    <a href="{{ url('books-list') }}"
                        class="block {{ request()->is('books-list', 'books/*') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">daftar
                        raport</a>
                </div>
            </div>

            {{-- MENU MASTER KURIKULUM --}}
            <h1>Master Kurikulum</h1>
            <div x-data="{ dropdownOpen: {{ request()->is('themes', 'themes/*', 'subthemes', 'subthemes/*') ? 'true' : 'false' }} }" class="relative">
                <div @click="dropdownOpen = !dropdownOpen"
                    class="flex {{ request()->is('themes', 'themes/*', 'subthemes', 'subthemes/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                    <div
                        class="flex {{ request()->is('themes', 'themes/*', 'subthemes', 'subthemes/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                    </div>
                    <div
                        class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('themes', 'themes/*', 'subthemes', 'subthemes/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg">library_books</span>
                        <h1>Master Tema</h1>
                        <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                        <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                    </div>
                </div>
                <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-48 bg-white dark:bg-gray-900">
                    <a href="{{ route('themes.create') }}"
                        class="block {{ request()->is('themes', 'themes/*') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Tema</a>
                    <a href="{{ route('subthemes.create') }}"
                        class="block {{ request()->is('books-list', 'subtheme', 'subtheme/*') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Sub
                        Tema</a>
                    <a href="{{ route('material.create') }}"
                        class="block {{ request()->is('books-list', 'books/*') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Materi</a>
                </div>
            </div>
            {{-- <div
                    class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
            <div
                class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
            </div>
            <a href="{{ url('category') }}" class="flex">
                <div
                    class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('category', 'edit-category/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg">library_books</span>
                    <h1>Master Tema</h1>
                </div>
            </a>
        </div> --}}
        <div
            class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
            <div
                class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
            </div>
            <a href="{{ url('category') }}" class="flex">
                <div
                    class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('category', 'edit-category/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg">smart_toy</span>
                    <h1>Master Permainan</h1>
                </div>
            </a>
        </div>

        {{-- MENU LAYANAN --}}
        <h1>Menu Layanan</h1>
        {{-- MENU PENGUKURAN --}}
        <div
            class="flex {{ request()->is('pengukuran', 'edit-pengukuran/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
            <div
                class="flex {{ request()->is('pengukuran', 'edit-pengukuran/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
            </div>
            <a href="{{ url('pengukuran') }}" class="flex">
                <div
                    class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('category', 'edit-category/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg">home_health</span>
                    <h1>Pengukuran Anak</h1>
                </div>
            </a>
        </div>
        {{-- MENU LAYANAN MASUK --}}
        <div
            class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
            <div
                class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
            </div>
            <a href="{{ url('category') }}" class="flex">
                <div
                    class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('category', 'edit-category/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg">concierge</span>
                    <h1>Layanan Masuk</h1>
                </div>
            </a>
        </div>
        {{-- MENU TAGIHAN --}}
        <div x-data="{ dropdownOpen: {{ request()->is('add-book', 'books-list', 'books/*') ? 'true' : 'false' }} }" class="relative">
            <div @click="dropdownOpen = !dropdownOpen"
                class="flex {{ request()->is('add-book', 'books-list', 'books/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                <div
                    class="flex {{ request()->is('add-book', 'books-list', 'books/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                </div>
                <div
                    class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('add-book', 'books-list', 'books/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg">payments</span>
                    <h1>Tagihan</h1>
                    <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                    <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                </div>
            </div>
            <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-48 bg-white dark:bg-gray-900">
                <a href="{{ url('add-book') }}"
                    class="block {{ request()->is('add-book') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">buat
                    tagihan</a>
                <a href="{{ url('books-list') }}"
                    class="block {{ request()->is('books-list', 'books/*') ? 'bg-gray-200' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">pembayaran
                    masuk</a>
            </div>
        </div>

        {{-- MASTER BLOGS --}}
        <h1 class="py-2">Master Blogs</h1>
        {{-- MENU GALERI AKTIVITAS --}}
        <div
            class="flex {{ request()->is('galeri-aktivitas') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
            <div
                class="flex h-full w-1 {{ request()->is('galeri-aktivitas') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
            </div>
            <a href="{{ url('galeri-aktivitas') }}" class="flex">
                <div
                    class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('', 'galeri-aktivitas') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg ">photo_library</span>
                    <h1>Galeri Aktivitas</h1>
                </div>
            </a>
        </div>
        {{-- MENU BLOGS --}}
        <div
            class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
            <div
                class="flex {{ request()->is('category', 'edit-category/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
            </div>
            <a href="{{ url('category') }}" class="flex">
                <div
                    class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('category', 'edit-category/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg">newspaper</span>
                    <h1>Blogs</h1>
                </div>
            </a>
        </div>
        {{-- MENU USERS --}}
        <h1 class="py-2">Master Pengguna</h1>
        <div x-data="{ dropdownOpen: {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*') ? 'true' : 'false' }} }" class="relative mb-8">
            <div @click="dropdownOpen = !dropdownOpen"
                class="flex {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                <div
                    class="flex {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                </div>
                <div
                    class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*', 'books/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                    <span class="material-symbols-outlined text-lg">supervisor_account</span>
                    <h1>Users</h1>
                    <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                    <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                </div>
            </div>
            <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-48 bg-white dark:bg-gray-900">
                <a href="{{ url('siswa') }}"
                    class="block {{ request()->is('siswa', 'siswa/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">siswa</a>
                <a href="{{ url('pengajar') }}"
                    class="block {{ request()->is('pengajar', 'pengajar/*', 'books/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">pengajar</a>
                <a href="{{ url('books-list') }}"
                    class="block {{ request()->is('books-list', 'books/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">admin</a>
            </div>
        </div>
        @endif
    </div>
    <form action="/logout" method="post">
        @csrf
        <button>
            <div
                class="absolute flex gap-4 items-center text-gray-500 dark:text-gray-400 hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 duration-300 bottom-8 text-xs p-2 h-14 w-48 bg-white dark:bg-gray-900">
                <span class="material-symbols-outlined text-xs">logout</span>
                <h1>Log Out</h1>
            </div>
        </button>
    </form>
</div>
</div>

{{-- NAVBAR MOBILE --}}
<div class="lg:hidden block w-screen h-16 content-center shadow-md bg-white dark:bg-gray-900">
    <div class="flex justify-between items-center h-full px-4">
        <div class="flex items-center space-x-2 gap-4">
            <span @click="sidebarOpen = true" class="material-symbols-outlined text-pink-500 cursor-pointer">
                menu
            </span>
            <h1 class="text-gray-600 text-xs font-semibold"></h1>
        </div>
    </div>
</div>

{{-- NAVBAR --}}
<div class="hidden lg:block w-lvw h-16 content-center bg-white dark:bg-gray-900 drop-shadow-md">
    <div class="flex justify-between items-center h-full px-4">
        <h1 class="text-gray-600 ml-60 text-2xl font-semibold"></h1>
        <div class="flex items-center space-x-2">
            <h1 class="text-gray-500 dark:text-gray-400">
                @if (Auth::guard('web')->check())
                Hi, {{ Auth::guard('web')->user()->user_name }}
                @elseif(Auth::guard('student')->check())
                Hi, {{ Auth::guard('student')->user()->student_nama }}
                @else
                Hi, Guest
                @endif
            </h1>
            <img src="@if (Auth::guard('web')->check()) {{ Auth::guard('web')->user()->foto_user ? asset('foto_user/' . Auth::guard('web')->user()->foto_user) : asset('images/profile-1.png') }}
        @elseif(Auth::guard('student')->check())
            {{ Auth::guard('student')->user()->foto_user ? asset('foto_user/' . Auth::guard('student')->user()->foto_user) : asset('images/profile-1.png') }}
        @else
            {{ asset('images/profile-1.png') }} @endif"
                alt="Profil Pengguna" class="profile-img h-10 w-10 rounded-full border-2 border-gray-300">
        </div>
    </div>
</div>
