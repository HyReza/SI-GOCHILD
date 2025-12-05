<!-- Sidebar -->
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
            {{-- MENU DASHBOARD --}}
            <div
                class="flex {{ request()->is('dashboard', 'dashboard_user') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
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
            <div
                class="flex {{ request()->is('service-catalog') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                <div
                    class="flex h-full w-1 {{ request()->is('service-catalog') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                </div>
                <a href="{{ route('service-orders.catalog') }}" class="flex">
                    <div
                        class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('', 'service-catalog') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                        <span class="material-symbols-outlined text-lg ">dashboard</span>
                        <h1>Order Layanan</h1>
                    </div>
                </a>
            </div>
            {{-- MENU ADMIN VALIDASI --}}
            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'admin')
                {{-- MASTER LAYANAN --}}
                <h1 class="py-2">Master Harga</h1>
                {{-- MENU KATALOG SERVICE --}}
                <div
                    class="flex {{ request()->is('catalog-service', 'catalog-service/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('catalog-service', 'catalog-service/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="/coming-soon" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('catalog-service', 'catalog-service/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">payment_arrow_down</span>
                            <h1>Pembayaran Masuk</h1>
                        </div>
                    </a>
                </div>
                {{-- KATALOG PROGRAM --}}
                <div
                    class="flex {{ request()->is('catalog-programs', 'catalog-programs/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('catalog-programs', 'catalog-programs/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="/coming-soon" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('catalog-programs', 'catalog-programs/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">add_card</span>
                            <h1>Tagihan</h1>
                        </div>
                    </a>
                </div>
                <h1 class="py-2">Master laporan</h1>
                <div
                    class="flex {{ request()->is('attendance', 'attendance/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('attendance', 'attendance/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('attendance.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('attendance', 'attendance/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">checklist_rtl</span>
                            <h1>Absensi Harian</h1>
                        </div>
                    </a>
                </div>
                {{-- MENU LAPORAN HARIAN --}}
                <div
                    class="flex {{ request()->is('daily-report', 'daily-report/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('daily-report', 'daily-report/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ url('daily-report') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('daily-report', 'daily-report/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">edit_note</span>
                            <h1>Laporan Harian</h1>
                        </div>
                    </a>
                </div>
                <div
                    class="flex {{ request()->is('measurement', 'measurement/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('measurement', 'measurement/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ url('measurement') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('measurement', 'measurement/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">conditions</span>
                            <h1>Laporan Pertumbuhan</h1>
                        </div>
                    </a>
                </div>
                <div
                    class="flex {{ request()->is('mmdst', 'mmdst/*', 'mmdst-assessments', 'mmdst', 'mmdst/*', 'mmdst-assessments/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('mmdst', 'mmdst/*', 'mmdst-assessments', 'mmdst', 'mmdst/*', 'mmdst-assessments/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ url('mmdst') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('mmdst', 'mmdst/*', 'mmdst-assessments', 'mmdst', 'mmdst/*', 'mmdst-assessments/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">cardiology</span>
                            <h1>Laporan Perkembangan</h1>
                        </div>
                    </a>
                </div>
                {{-- MENU RAPORT --}}
                {{-- <div x-data="{ dropdownOpen: {{ request()->is('add-book', 'books-list', 'books/*') ? 'true' : 'false' }} }" class="relative">
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
                        <a href="{{ url('coming-soon') }}"
                            class="block {{ request()->is('add-book') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">tambah
                            raport</a>
                        <a href="{{ url('coming-soon') }}"
                            class="block {{ request()->is('books-list', 'books/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">daftar
                            raport</a>
                    </div>
                </div> --}}

                <div
                    class="flex {{ request()->is('reports', 'reports/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('reports', 'reports/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('reports.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('reports', 'reports/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">description</span>
                            <h1>Raport</h1>
                        </div>
                    </a>
                </div>

                {{-- MASTER KURIKULUM --}}
                <h1>Master Kurikulum</h1>

                <div x-data="{ dropdownOpen: {{ request()->is('themes', 'themes/*', 'subthemes', 'subthemes/*', 'material/', 'material/*') ? 'true' : 'false' }} }" class="relative">
                    <div @click="dropdownOpen = !dropdownOpen"
                        class="flex {{ request()->is('themes', 'themes/*', 'subthemes', 'subthemes/*', 'material/', 'material/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                        <div
                            class="flex {{ request()->is('themes', 'themes/*', 'subthemes', 'subthemes/*', 'material/', 'material/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
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
                            class="block {{ request()->is('themes', 'themes/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Tema</a>
                        <a href="{{ route('subthemes.create') }}"
                            class="block {{ request()->is('subthemes', 'subthemes/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Sub
                            Tema</a>
                        <a href="{{ route('material.create') }}"
                            class="block {{ request()->is('material/', 'material/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Materi</a>
                    </div>
                </div>
                {{-- MASTER PERTUMBUHAN --}}
                <div
                    class="flex {{ request()->is('growth-standards', 'growth-standards/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('growth-standards', 'growth-standards/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('growth-standards.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('growth-standards', 'growth-standards/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">moving</span>
                            <h1>Master Pertumbuhan</h1>
                        </div>
                    </a>
                </div>

                {{-- MASTER PERKEMBANGAN --}}
                <div x-data="{ dropdownOpen: {{ request()->is('category-parameter', 'category-parameter/*', 'mmdst-parameter', 'mmdst-parameter/*') ? 'true' : 'false' }} }" class="relative">
                    <div @click="dropdownOpen = !dropdownOpen"
                        class="flex {{ request()->is('category-parameter', 'category-parameter/*', 'mmdst-parameter', 'mmdst-parameter/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                        <div
                            class="flex {{ request()->is('category-parameter', 'category-parameter/*', 'mmdst-parameter', 'mmdst-parameter/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                        </div>
                        <div
                            class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('category-parameter', 'category-parameter/*, mmdst-parameter', 'mmdst-parameter/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg">sentiment_excited</span>
                            <h1>Master Perkembangan</h1>
                            <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                            <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                        </div>
                    </div>
                    <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-48 bg-white dark:bg-gray-900">
                        <a href="{{ route('category-parameter.index') }}"
                            class="block {{ request()->is('category-parameter', 'category-parameter/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Categori
                            Perkembangan</a>
                        <a href="{{ route('mmdst-parameter.index') }}"
                            class="block {{ request()->is('mmdst-parameter', 'mmdst-parameter/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Perkembangan</a>
                    </div>
                </div>


                {{-- MASTER BLOGS --}}
                <h1 class="py-2">Master Blogs</h1>
                {{-- MENU GALERI AKTIVITAS --}}
                <div
                    class="flex {{ request()->is('gallery-activity', 'gallery-activity/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('gallery-activity', 'gallery-activity/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('gallery-activity.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('gallery-activity', 'gallery-activity/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">photo_library</span>
                            <h1>Galeri Aktivitas</h1>
                        </div>
                    </a>
                </div>
                {{-- MENU BLOGS --}}
                <div x-data="{ dropdownOpen: {{ request()->is('articles', 'articles/*', 'categories', 'categories/*') ? 'true' : 'false' }} }" class="relative">
                    <div @click="dropdownOpen = !dropdownOpen"
                        class="flex {{ request()->is('articles', 'articles/*', 'categories', 'categories/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                        <div
                            class="flex {{ request()->is('articles', 'articles/*', 'categories', 'categories/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                        </div>
                        <div
                            class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('articles', 'articles/*', 'categories', 'categories/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg">newspaper</span>
                            <h1>Master Blogs</h1>
                            <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                            <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                        </div>
                    </div>
                    <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-48 bg-white dark:bg-gray-900">
                        <a href="{{ route('articles.index') }}"
                            class="block {{ request()->is('articles', 'articles/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Blogs</a>
                        <a href="{{ route('categories.index') }}"
                            class="block {{ request()->is('categories', 'categories/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">Category</a>
                    </div>
                </div>

                {{-- MASTER LAYANAN --}}
                <h1 class="py-2">Master Layanan</h1>
                {{-- MENU KATALOG SERVICE --}}
                <div
                    class="flex {{ request()->is('catalog-service', 'catalog-service/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('catalog-service', 'catalog-service/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('catalog-service.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('catalog-service', 'catalog-service/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">child_care</span>
                            <h1>Katalog Service</h1>
                        </div>
                    </a>
                </div>
                {{-- KATALOG PROGRAM --}}
                <div
                    class="flex {{ request()->is('catalog-programs', 'catalog-programs/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('catalog-programs', 'catalog-programs/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('catalog-programs.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('catalog-programs', 'catalog-programs/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">manage_history</span>
                            <h1>Katalog Program</h1>
                        </div>
                    </a>
                </div>
                <div
                    class="flex {{ request()->is('extra-services', 'extra-services/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('extra-services', 'extra-services/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('extra-services.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('extra-services', 'extra-services/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">assignment_add</span>
                            <h1>Layanan Extra</h1>
                        </div>
                    </a>
                </div>
                {{-- MENU USERS --}}
                <h1 class="py-2">Master Pengguna</h1>
                <div x-data="{ dropdownOpen: {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*', 'admin', 'admin/*') ? 'true' : 'false' }} }" class="relative mb-8">
                    <div @click="dropdownOpen = !dropdownOpen"
                        class="flex {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*', 'admin', 'admin/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2 cursor-pointer">
                        <div
                            class="flex {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*', 'admin', 'admin/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} h-full w-1">
                        </div>
                        <div
                            class="flex p-2 gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('siswa', 'siswa/*', 'pengajar', 'pengajar/*', 'admin', 'admin/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg">supervisor_account</span>
                            <h1>Users</h1>
                            <span class="material-symbols-outlined ml-auto" x-show="!dropdownOpen">expand_more</span>
                            <span class="material-symbols-outlined ml-auto" x-show="dropdownOpen">expand_less</span>
                        </div>
                    </div>
                    <div x-show="dropdownOpen" x-transition class="left-0 mt-2 w-48 bg-white dark:bg-gray-900">
                        <a href="{{ route('siswa.index') }}"
                            class="block {{ request()->is('siswa', 'siswa/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">siswa</a>
                        <a href="{{ route('pengajar.index') }}"
                            class="block {{ request()->is('pengajar', 'pengajar/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">pengajar</a>
                        <a href="{{ route('admin.index') }}"
                            class="block {{ request()->is('admin', 'admin/*') ? 'bg-gray-200 dark:bg-gray-800' : 'bg-none' }} px-4 py-2 text-xs text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 border-b-2 border-gray-100 dark:border-gray-600">admin</a>
                    </div>
                </div>
            @endif
            {{-- MENU UNTUK GURU --}}
            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'teacher')
                <h1 class="py-2">Master laporan</h1>
                <div
                    class="flex {{ request()->is('attendance', 'attendance/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('attendance', 'attendance/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('attendance.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('attendance', 'attendance/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">checklist_rtl</span>
                            <h1>Absensi Harian</h1>
                        </div>
                    </a>
                </div>


                {{-- MENU LAPORAN HARIAN --}}
                <div
                    class="flex {{ request()->is('daily-report', 'daily-report/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('daily-report', 'daily-report/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ url('daily-report') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('daily-report', 'daily-report/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">edit_note</span>
                            <h1>Laporan Harian</h1>
                        </div>
                    </a>
                </div>
                <div
                    class="flex {{ request()->is('measurement', 'measurement/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('measurement', 'measurement/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ url('measurement') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('measurement', 'measurement/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">conditions</span>
                            <h1>Laporan Pertumbuhan</h1>
                        </div>
                    </a>
                </div>
                <div
                    class="flex {{ request()->is('mmdst', 'mmdst/*', 'mmdst-assessments', 'mmdst', 'mmdst/*', 'mmdst-assessments/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('mmdst', 'mmdst/*', 'mmdst-assessments', 'mmdst', 'mmdst/*', 'mmdst-assessments/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ url('mmdst') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('mmdst', 'mmdst/*', 'mmdst-assessments', 'mmdst', 'mmdst/*', 'mmdst-assessments/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">cardiology</span>
                            <h1>Laporan Perkembangan</h1>
                        </div>
                    </a>
                </div>

                {{-- MASTER BLOGS --}}
                <h1 class="py-2">Master Blogs</h1>
                {{-- MENU GALERI AKTIVITAS --}}
                <div
                    class="flex {{ request()->is('gallery-activity', 'gallery-activity/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30  h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('gallery-activity', 'gallery-activity/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ url('coming-soon') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('gallery-activity', 'gallery-activity/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">photo_library</span>
                            <h1>Galeri Aktivitas</h1>
                        </div>
                    </a>
                </div>
            @endif
            {{-- MENU ORANG TUA --}}
            @if (Auth::guard('student')->check() || Auth::guard('student')->check())
                <h1 class="py-2">Master laporan</h1>
                <div
                    class="flex {{ request()->is('attendance', 'attendance/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }} bg-opacity-30 dark:bg-opacity-30 h-14 w-48 content-center mb-2">
                    <div
                        class="flex h-full w-1 {{ request()->is('attendance', 'attendance/*') ? 'bg-pink-500 dark:bg-pink-600' : 'bg-none' }}">
                    </div>
                    <a href="{{ route('attendance.index') }}" class="flex">
                        <div
                            class="p-2 flex gap-4 items-center hover:ml-3 hover:text-pink-500 dark:hover:text-pink-600 {{ request()->is('attendance', 'attendance/*') ? 'text-pink-500 dark:text-pink-600' : 'text-gray-500 dark:text-gray-400' }} duration-300">
                            <span class="material-symbols-outlined text-lg ">checklist_rtl</span>
                            <h1>Absensi Harian</h1>
                        </div>
                    </a>
                </div>
            @endif
            {{-- LOGOUT BUTTON --}}
            <form action="{{ Auth::guard('student')->check() ? '/logout-student' : '/logout' }}" method="post">
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
</div>

<!-- Navbar Mobile -->
<div class="lg:hidden block w-screen h-16 content-center shadow-md bg-white dark:bg-gray-900">
    <div class="flex justify-between items-center h-full px-4">
        <div class="flex items-center space-x-2 gap-4">
            <span @click="sidebarOpen = true" class="material-symbols-outlined text-pink-500 cursor-pointer">
                menu
            </span>
        </div>
    </div>
</div>

<!-- Navbar -->
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
