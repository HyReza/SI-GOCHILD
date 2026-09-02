<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

<aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0 lg:shadow-none'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 transition-transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] lg:static lg:inset-auto flex flex-col h-screen">

    <div class="flex items-center gap-3 px-6 h-20 border-b border-gray-100 dark:border-gray-800 shrink-0">
        <img src="{{ asset('images/logo.png') }}" alt="Logo"
            class="h-8 w-auto hover:scale-105 transition-transform duration-300">
        <button @click="sidebarOpen = false"
            class="lg:hidden ml-auto text-gray-400 hover:text-emerald-500 transition-colors p-1 rounded-md">
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
                   ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                   : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('dashboard', 'dashboard_user') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">dashboard</span>
                Dashboard
            </a>



            {{-- BAGIAN: ADMIN MENU --}}
            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'admin')
                {{-- Order Layanan Link --}}
                {{-- <a href="{{ route('orders.select-student') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group hover:translate-x-1
               {{ request()->is('service-catalog*')
                   ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                   : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('service-catalog*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">shopping_cart</span>
                    Order Layanan
                </a> --}}


                {{-- LOGIC: Hitung Data & Cek Status Menu --}}
                {{-- @php
                    // 1. Hitung jumlah pending (Ganti 'ServiceOrder' dengan nama Model Anda jika beda)
                    $pendingCount = \App\Models\ServiceOrder::where('status', 'pending_confirmation')->count();

                    // 2. Cek apakah menu ini sedang aktif
                    $isActive = request()->routeIs('orders.index', 'orders.show', 'orders.payment');
                @endphp --}}

                {{-- <a href="{{ route('orders.index') }}"
                    class="relative flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group hover:translate-x-1
                    {{ $isActive
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}"> --}}

                {{-- Icon --}}
                {{-- <span
                    class="material-symbols-outlined text-[22px] transition-colors duration-300
                        {{ $isActive ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                    receipt_long
                </span> --}}

                {{-- Teks Menu --}}
                {{-- <span class="flex-1">Data Pesanan</span> --}}

                {{-- BADGE DINAMIS --}}
                {{-- @if ($pendingCount > 0)
                    <span
                        class="flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full shadow-sm transition-colors duration-300
                            {{-- JIKA AKTIF: Badge Putih, Teks emerald (Kontras dengan background gradient) --}}
                {{-- JIKA TIDAK AKTIF: Badge Merah, Teks Putih (Kontras dengan background putih/abu) --}}
                {{-- {{ $isActive ? 'bg-white text-emerald-600' : 'bg-red-500 text-white group-hover:bg-emerald-600' }}"> --}}
                {{-- {{ $pendingCount }} --}}
                {{-- </span> --}}
                {{-- @endif --}}
                {{-- </a> --}}
        </div>
        {{-- GROUP: MASTER DATA --}}
        <div class="mb-6">
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Master Data</p>

            {{-- Dropdown Keuangan --}}
            {{-- <div x-data="{ open: {{ request()->is('coming-soon*') ? 'true' : 'false' }} }" class="mb-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('coming-soon*')
                            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[22px] {{ request()->is('coming-soon*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">payments</span>
                        Keuangan
                    </div>
                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('coming-soon*') ? 'text-white' : 'text-gray-400' }}"
                        :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                    class="pl-4 pr-1 mt-1 space-y-1 ml-2">
                    <a href="/coming-soon"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('coming-soon/pembayaran*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Pembayaran Masuk
                    </a>
                    <a href="/coming-soon"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('coming-soon/tagihan*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Tagihan
                    </a>
                </div>
            </div> --}}

            {{-- Dropdown Kurikulum --}}
            <div x-data="{ open: {{ request()->is('themes*', 'subthemes*', 'material*') ? 'true' : 'false' }} }" class="mb-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('themes*', 'subthemes*', 'material*')
                            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[22px] {{ request()->is('themes*', 'subthemes*', 'material*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">menu_book</span>
                        Kurikulum
                    </div>
                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('themes*', 'subthemes*', 'material*') ? 'text-white' : 'text-gray-400' }}"
                        :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                    class="pl-4 pr-1 mt-1 space-y-1 ml-2">
                    <a href="{{ route('themes.create') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('themes*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Tema
                    </a>
                    <a href="{{ route('subthemes.create') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('subthemes*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Sub Tema
                    </a>
                    <a href="{{ route('material.create') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('material*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Materi
                    </a>
                </div>
            </div>

            {{-- Dropdown Ref Tumbuh Kembang --}}
            <div x-data="{ open: {{ request()->is('growth-standards*', 'category-parameter*', 'mmdst-parameter*') ? 'true' : 'false' }} }" class="mb-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('growth-standards*', 'category-parameter*', 'mmdst-parameter*')
                            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[22px] {{ request()->is('growth-standards*', 'category-parameter*', 'mmdst-parameter*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">tune</span>
                        Tumbuh Kembang
                    </div>
                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('growth-standards*', 'category-parameter*', 'mmdst-parameter*') ? 'text-white' : 'text-gray-400' }}"
                        :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                    class="pl-4 pr-1 mt-1 space-y-1 ml-2">
                    <a href="{{ route('growth-standards.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('growth-standards*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Std Pertumbuhan
                    </a>
                    <a href="{{ route('category-parameter.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('category-parameter*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Kategori MMDST
                    </a>
                    <a href="{{ route('mmdst-parameter.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('mmdst-parameter*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Parameter MMDST
                    </a>
                </div>
            </div>

            {{-- Dropdown Layanan --}}
            <div x-data="{ open: {{ request()->is('catalog-service*', 'catalog-programs*', 'extra-services*') ? 'true' : 'false' }} }" class="mb-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('catalog-service*', 'catalog-programs*', 'extra-services*')
                            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[22px] {{ request()->is('catalog-service*', 'catalog-programs*', 'extra-services*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">design_services</span>
                        Layanan
                    </div>
                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('catalog-service*', 'catalog-programs*', 'extra-services*') ? 'text-white' : 'text-gray-400' }}"
                        :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                    class="pl-4 pr-1 mt-1 space-y-1 ml-2">
                    <a href="{{ route('catalog-service.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('catalog-service*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Katalog Service
                    </a>
                    <a href="{{ route('catalog-programs.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('catalog-programs*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Katalog Program
                    </a>
                    <a href="{{ route('extra-services.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('extra-services*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Layanan Extra
                    </a>
                </div>
            </div>

            {{-- Dropdown Konten & Blog --}}
            <div x-data="{ open: {{ request()->is('articles*', 'categories*', 'gallery-activity*') ? 'true' : 'false' }} }" class="mb-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('articles*', 'categories*', 'gallery-activity*')
                            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[22px] {{ request()->is('articles*', 'categories*', 'gallery-activity*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">rss_feed</span>
                        Konten & Blog
                    </div>
                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('articles*', 'categories*', 'gallery-activity*') ? 'text-white' : 'text-gray-400' }}"
                        :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                    class="pl-4 pr-1 mt-1 space-y-1 ml-2">
                    <a href="{{ route('gallery-activity.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('gallery-activity*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Galeri Aktivitas
                    </a>
                    <a href="{{ route('articles.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('articles*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Artikel
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('categories*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Kategori Blog
                    </a>
                </div>
            </div>

            {{-- Dropdown Master User --}}
            <div x-data="{ open: {{ request()->is('siswa*', 'pengajar*', 'admin*') ? 'true' : 'false' }} }" class="mb-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                        {{ request()->is('siswa*', 'pengajar*', 'admin*')
                            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[22px] {{ request()->is('siswa*', 'pengajar*', 'admin*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">group</span>
                        Master User
                    </div>
                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('siswa*', 'pengajar*', 'admin*') ? 'text-white' : 'text-gray-400' }}"
                        :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                    class="pl-4 pr-1 mt-1 space-y-1 ml-2">
                    <a href="{{ route('siswa.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('siswa*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Data Siswa
                    </a>
                    <a href="{{ route('pengajar.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('pengajar*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Data Pengajar
                    </a>
                    <a href="{{ route('admin.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
                            {{ request()->is('admin*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Data Admin
                    </a>
                </div>
            </div>
        </div>

        {{-- GROUP: LAPORAN & AKTIVITAS --}}
        <div class="mb-6">
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Laporan</p>

            <a href="{{ route('attendance.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('attendance*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('attendance*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">checklist_rtl</span>
                Absensi Harian
            </a>

            <a href="{{ url('daily-report') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('daily-report*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('daily-report*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">edit_note</span>
                Laporan Harian
            </a>

            <a href="{{ url('measurement') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('measurement*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('measurement*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">show_chart</span>
                Pertumbuhan (Fisik)
            </a>

            <a href="{{ url('mmdst') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('mmdst*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('mmdst*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">psychology</span>
                Perkembangan (MMDST)
            </a>

            {{-- Logic cek URL diupdate agar mendeteksi 'development-and-growth-report*' --}}
            <div x-data="{ open: {{ request()->is('reports*', 'development-and-growth-report*') ? 'true' : 'false' }} }" class="mb-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
        {{ request()->is('reports*', 'development-and-growth-report*')
            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <div class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[22px] {{ request()->is('reports*', 'development-and-growth-report*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                            assignment
                        </span>
                        Raport Siswa
                    </div>
                    <span
                        class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('reports*', 'development-and-growth-report*') ? 'text-white' : 'text-gray-400' }}"
                        :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                    class="pl-4 pr-1 mt-1 space-y-1 ml-2">

                    {{-- Menu Raport Kurikulum --}}
                    <a href="{{ route('reports.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
            {{ request()->is('reports*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Raport Kurikulum
                    </a>

                    {{-- Menu Hasil Tumbuh Kembang --}}
                    <a href="{{ route('development-reports.index') }}"
                        class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
            {{ request()->is('development-and-growth-report*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                        • Hasil Tumbuh Kembang
                    </a>
                </div>
            </div>

            {{-- GROUP: PENGATURAN --}}
            <div class="mb-6">
                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Pengaturan</p>

                <a href="{{ route('api-gemini.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('api-gemini*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('api-gemini*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                        smart_toy
                    </span>
                    Konfigurasi AI
                </a>
            </div>
        </div>
        @endif

        {{-- BAGIAN: MENU GURU --}}
        @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'teacher')
            {{-- Order Layanan Link --}}
            <a href="{{ route('orders.select-student') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group hover:translate-x-1
               {{ request()->is('service-catalog*')
                   ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                   : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('service-catalog*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">shopping_cart</span>
                Order Layanan
            </a>
    </div>

    <div class="mb-6">
        <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Menu Guru</p>
        <a href="{{ route('attendance.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('attendance*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->is('attendance*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">checklist_rtl</span>
            Absensi Harian
        </a>
        <a href="{{ url('daily-report') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('daily-report*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->is('daily-report*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">edit_note</span>
            Laporan Harian
        </a>
        <a href="{{ url('measurement') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('measurement*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->is('measurement*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">show_chart</span>
            Pertumbuhan (Fisik)
        </a>
        <a href="{{ url('mmdst') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('mmdst*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->is('mmdst*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">psychology</span>
            Perkembangan (MMDST)
        </a>

        {{-- Logic cek URL diupdate agar mendeteksi 'development-and-growth-report*' --}}
        <div x-data="{ open: {{ request()->is('reports*', 'development-and-growth-report*') ? 'true' : 'false' }} }" class="mb-1">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
        {{ request()->is('reports*', 'development-and-growth-report*')
            ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
            : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                <div class="flex items-center gap-3">
                    <span
                        class="material-symbols-outlined text-[22px] {{ request()->is('reports*', 'development-and-growth-report*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                        assignment
                    </span>
                    Raport Siswa
                </div>
                <span
                    class="material-symbols-outlined text-sm transition-transform duration-300 {{ request()->is('reports*', 'development-and-growth-report*') ? 'text-white' : 'text-gray-400' }}"
                    :class="open ? 'rotate-180' : ''">expand_more</span>
            </button>
            <div x-show="open" x-collapse x-transition:enter="transition ease-out duration-200"
                class="pl-4 pr-1 mt-1 space-y-1 ml-2">

                {{-- Menu Raport Kurikulum --}}
                <a href="{{ route('reports.index') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
            {{ request()->is('reports*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                    • Raport Kurikulum
                </a>

                {{-- Menu Hasil Tumbuh Kembang --}}
                <a href="{{ route('development-reports.index') }}"
                    class="block px-4 py-2 text-sm rounded-lg transition-all duration-200 hover:translate-x-1
            {{ request()->is('development-and-growth-report*') ? 'bg-emerald-100 text-emerald-700 font-bold' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                    • Hasil Tumbuh Kembang
                </a>
            </div>
        </div>

        <a href="{{ route('gallery-activity.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('gallery-activity*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
            <span
                class="material-symbols-outlined text-[22px] {{ request()->is('gallery-activity*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">photo_library</span>
            Galeri Aktivitas
        </a>

    </div>
    @endif

    {{-- BAGIAN: SISWA --}}
    @if (Auth::guard('student')->check())
        <div class="mb-6">
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 mt-3">Siswa</p>
            <a href="{{ route('student.attendance.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group
                    {{ request()->is('my-attendance*')
                        ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                        : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('my-attendance*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">checklist_rtl</span>
                Absensi Saya
            </a>
            <a href="{{ route('student.daily-report.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group {{ request()->is('student-daily-report*')
                    ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('student-daily-report*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                    edit_note
                </span>
                Laporan Harian Saya
            </a>
            <a href="{{ route('student.measurement.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group {{ request()->is('measurements*')
                    ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('measurements*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                    monitoring
                </span>
                Pertumbuhan (Fisik) Saya
            </a>
            <a href="{{ route('student.development.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group {{ request()->is('development-reports*')
                    ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('development-reports*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                    psychology
                </span>
                Perkembangan (MMDST) Saya
            </a>

            <a href="{{ route('student.report.history') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 hover:translate-x-1 group {{ request()->is('report*')
                    ? 'bg-gradient-to-r from-emerald-400 to-emerald-500 text-white shadow-lg shadow-emerald-500/30'
                    : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-gray-800 hover:text-emerald-600' }}">
                <span
                    class="material-symbols-outlined text-[22px] {{ request()->is('report*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-500' }}">
                    assignment
                </span>
                Laporan Raport Saya
            </a>
        </div>
    @endif

    </div>

    {{-- SIDEBAR FOOTER (Watermark) --}}
    <div class="p-4 border-t-2 border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900/50 shrink-0 text-center">
        <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-tight">
            &copy; {{ date('Y') }} Al-Jannah Daycare.<br>
            All rights reserved.
        </p>
    </div>
</aside>
