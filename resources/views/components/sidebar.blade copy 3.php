<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

<aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0 lg:shadow-none'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 transition-transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] lg:static lg:inset-auto flex flex-col h-screen">

    <div class="flex items-center gap-3 px-6 h-20 border-b border-gray-100 dark:border-gray-800 shrink-0">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo"
            class="h-20 w-auto hover:scale-105 transition-transform duration-300">
        <button @click="sidebarOpen = false"
            class="lg:hidden ml-auto text-gray-400 hover:text-pink-500 transition-colors p-1 rounded-md">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto no-scrollbar py-6 px-4 space-y-1">

        {{-- BAGIAN: MENU UTAMA --}}
        <div class="mb-6">
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Menu Utama</p>

            {{-- Dashboard Link --}}
            <a href="{{ url('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group hover:translate-x-1
               {{ request()->is('dashboard', 'dashboard_user')
                   ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                   : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('dashboard', 'dashboard_user') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">dashboard</span>
                Dashboard
            </a>

            {{-- Order Layanan Link --}}
            <a href="{{ route('service-orders.catalog') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group hover:translate-x-1
               {{ request()->is('service-catalog*')
                   ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                   : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('service-catalog*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">shopping_cart</span>
                Order Layanan
            </a>
        </div>

        {{-- BAGIAN: ADMIN MENU --}}
        @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'admin')
            {{-- GROUP: MASTER DATA --}}
            <div class="mb-6">
                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Master Data</p>

                {{-- Dropdown Keuangan --}}
                <div x-data="{ open: {{ request()->is('coming-soon*') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('coming-soon*')
                            ? 'bg-pink-50 dark:bg-gray-800 text-pink-700 dark:text-pink-400 font-semibold'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                        <div class="flex items-center gap-3">
                            <span
                                class="material-symbols-outlined text-[22px] {{ request()->is('coming-soon*') ? 'text-pink-600' : 'text-gray-400 group-hover:text-pink-500' }}">payments</span>
                            Keuangan
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-300"
                            :class="open ? 'rotate-180 text-pink-600' : 'text-gray-400'">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                        class="pl-4 pr-1 mt-1 space-y-1 border-l-2 border-pink-100 ml-4">
                        <a href="/coming-soon"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 text-gray-500 hover:text-pink-600 hover:bg-pink-50/50">Pembayaran
                            Masuk</a>
                        <a href="/coming-soon"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 text-gray-500 hover:text-pink-600 hover:bg-pink-50/50">Tagihan</a>
                    </div>
                </div>

                {{-- Dropdown Kurikulum --}}
                <div x-data="{ open: {{ request()->is('themes*', 'subthemes*', 'material*') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('themes*', 'subthemes*', 'material*')
                            ? 'bg-pink-50 dark:bg-gray-800 text-pink-700 dark:text-pink-400 font-semibold'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                        <div class="flex items-center gap-3">
                            <span
                                class="material-symbols-outlined text-[22px] {{ request()->is('themes*', 'subthemes*', 'material*') ? 'text-pink-600' : 'text-gray-400 group-hover:text-pink-500' }}">menu_book</span>
                            Kurikulum
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-300"
                            :class="open ? 'rotate-180 text-pink-600' : 'text-gray-400'">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                        class="pl-4 pr-1 mt-1 space-y-1 border-l-2 border-pink-100 ml-4">
                        <a href="{{ route('themes.create') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                           {{ request()->is('themes*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">
                            Tema
                        </a>
                        <a href="{{ route('subthemes.create') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                           {{ request()->is('subthemes*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">
                            Sub Tema
                        </a>
                        <a href="{{ route('material.create') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                           {{ request()->is('material*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">
                            Materi
                        </a>
                    </div>
                </div>

                {{-- Dropdown Ref Tumbuh Kembang --}}
                <div x-data="{ open: {{ request()->is('growth-standards*', 'category-parameter*', 'mmdst-parameter*') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('growth-standards*', 'category-parameter*', 'mmdst-parameter*')
                            ? 'bg-pink-50 dark:bg-gray-800 text-pink-700 dark:text-pink-400 font-semibold'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                        <div class="flex items-center gap-3">
                            <span
                                class="material-symbols-outlined text-[22px] {{ request()->is('growth-standards*', 'category-parameter*', 'mmdst-parameter*') ? 'text-pink-600' : 'text-gray-400 group-hover:text-pink-500' }}">tune</span>
                            Tumbuh Kembang
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-300"
                            :class="open ? 'rotate-180 text-pink-600' : 'text-gray-400'">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                        class="pl-4 pr-1 mt-1 space-y-1 border-l-2 border-pink-100 ml-4">
                        <a href="{{ route('growth-standards.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('growth-standards*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Std
                            Pertumbuhan</a>
                        <a href="{{ route('category-parameter.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('category-parameter*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Kategori
                            MMDST</a>
                        <a href="{{ route('mmdst-parameter.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('mmdst-parameter*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Parameter
                            MMDST</a>
                    </div>
                </div>

                {{-- Dropdown Layanan --}}
                <div x-data="{ open: {{ request()->is('catalog-service*', 'catalog-programs*', 'extra-services*') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('catalog-service*', 'catalog-programs*', 'extra-services*')
                            ? 'bg-pink-50 dark:bg-gray-800 text-pink-700 dark:text-pink-400 font-semibold'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                        <div class="flex items-center gap-3">
                            <span
                                class="material-symbols-outlined text-[22px] {{ request()->is('catalog-service*', 'catalog-programs*', 'extra-services*') ? 'text-pink-600' : 'text-gray-400 group-hover:text-pink-500' }}">design_services</span>
                            Layanan
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-300"
                            :class="open ? 'rotate-180 text-pink-600' : 'text-gray-400'">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                        class="pl-4 pr-1 mt-1 space-y-1 border-l-2 border-pink-100 ml-4">
                        <a href="{{ route('catalog-service.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('catalog-service*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Katalog
                            Service</a>
                        <a href="{{ route('catalog-programs.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('catalog-programs*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Katalog
                            Program</a>
                        <a href="{{ route('extra-services.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('extra-services*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Layanan
                            Extra</a>
                    </div>
                </div>

                {{-- Dropdown Konten & Blog --}}
                <div x-data="{ open: {{ request()->is('articles*', 'categories*', 'gallery-activity*') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('articles*', 'categories*', 'gallery-activity*')
                            ? 'bg-pink-50 dark:bg-gray-800 text-pink-700 dark:text-pink-400 font-semibold'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                        <div class="flex items-center gap-3">
                            <span
                                class="material-symbols-outlined text-[22px] {{ request()->is('articles*', 'categories*', 'gallery-activity*') ? 'text-pink-600' : 'text-gray-400 group-hover:text-pink-500' }}">rss_feed</span>
                            Konten & Blog
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-300"
                            :class="open ? 'rotate-180 text-pink-600' : 'text-gray-400'">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                        class="pl-4 pr-1 mt-1 space-y-1 border-l-2 border-pink-100 ml-4">
                        <a href="{{ route('gallery-activity.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('gallery-activity*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Galeri
                            Aktivitas</a>
                        <a href="{{ route('articles.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('articles*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Artikel</a>
                        <a href="{{ route('categories.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('categories*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Kategori
                            Blog</a>
                    </div>
                </div>

                {{-- Dropdown Master User --}}
                <div x-data="{ open: {{ request()->is('siswa*', 'pengajar*', 'admin*') ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('siswa*', 'pengajar*', 'admin*')
                            ? 'bg-pink-50 dark:bg-gray-800 text-pink-700 dark:text-pink-400 font-semibold'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-pink-600' }}">
                        <div class="flex items-center gap-3">
                            <span
                                class="material-symbols-outlined text-[22px] {{ request()->is('siswa*', 'pengajar*', 'admin*') ? 'text-pink-600' : 'text-gray-400 group-hover:text-pink-500' }}">group</span>
                            Master User
                        </div>
                        <span class="material-symbols-outlined text-sm transition-transform duration-300"
                            :class="open ? 'rotate-180 text-pink-600' : 'text-gray-400'">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                        class="pl-4 pr-1 mt-1 space-y-1 border-l-2 border-pink-100 ml-4">
                        <a href="{{ route('siswa.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('siswa*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Data
                            Siswa</a>
                        <a href="{{ route('pengajar.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('pengajar*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Data
                            Pengajar</a>
                        <a href="{{ route('admin.index') }}"
                            class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1 {{ request()->is('admin*') ? 'text-pink-600 font-bold bg-pink-50/50' : 'text-gray-500 hover:text-pink-600 hover:bg-pink-50/50' }}">Data
                            Admin</a>
                    </div>
                </div>
            </div>

            {{-- GROUP: LAPORAN & AKTIVITAS --}}
            <div class="mb-6">
                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Laporan</p>

                <a href="{{ route('attendance.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('attendance*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('attendance*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">checklist_rtl</span>
                    Absensi Harian
                </a>

                <a href="{{ url('daily-report') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('daily-report*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('daily-report*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">edit_note</span>
                    Laporan Harian
                </a>

                <a href="{{ url('measurement') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('measurement*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('measurement*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">show_chart</span>
                    Laporan Pertumbuhan
                </a>

                <a href="{{ url('mmdst') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('mmdst*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('mmdst*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">psychology</span>
                    Laporan Perkembangan
                </a>

                <a href="{{ route('reports.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('reports*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('reports*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">assignment</span>
                    Raport Siswa
                </a>
            </div>
        @endif

        {{-- BAGIAN: MENU GURU --}}
        @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'teacher')
            <div class="mb-6">
                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Menu Guru</p>
                <a href="{{ route('attendance.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('attendance*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('attendance*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">checklist_rtl</span>
                    Absensi Harian
                </a>
                <a href="{{ url('daily-report') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('daily-report*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('daily-report*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">edit_note</span>
                    Laporan Harian
                </a>
                <a href="{{ url('measurement') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('measurement*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('measurement*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">show_chart</span>
                    Laporan Pertumbuhan
                </a>
                <a href="{{ url('mmdst') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('mmdst*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('mmdst*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">psychology</span>
                    Laporan Perkembangan
                </a>
                <a href="{{ route('gallery-activity.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('gallery-activity*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('gallery-activity*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">photo_library</span>
                    Galeri Aktivitas
                </a>
            </div>
        @endif

        {{-- BAGIAN: SISWA --}}
        @if (Auth::guard('student')->check())
            <div class="mb-6">
                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Siswa</p>
                <a href="{{ route('attendance.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('attendance*')
                        ? 'bg-gradient-to-r from-pink-500 to-pink-600 text-white shadow-lg shadow-pink-500/30'
                        : 'text-gray-600 hover:bg-pink-50 hover:text-pink-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('attendance*') ? 'text-white' : 'text-gray-400 group-hover:text-pink-500' }}">checklist_rtl</span>
                    Absensi Saya
                </a>
            </div>
        @endif

    </div>

    <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 shrink-0">
        <form action="{{ Auth::guard('student')->check() ? '/logout-student' : '/logout' }}" method="post">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 text-red-500 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 border border-gray-100 dark:border-gray-700 rounded-xl transition-all duration-300 group shadow-sm hover:shadow-md hover:border-red-200">
                <span
                    class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform duration-300">logout</span>
                <span class="font-bold text-sm">Sign Out</span>
            </button>
        </form>
    </div>
</aside>
