<header x-data="{ isOpen: false }" :class="{ 'dark': window.matchMedia('(prefers-color-scheme: dark)').matches }"
    id="navbar"
    class="bg-white dark:bg-gray-900 dark:text-white fixed w-full top-0 left-0 z-50 transition-all ease-in-out duration-500">
    <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo Section -->
            <div class="md:flex md:items-center md:gap-12">
                <a class="block" href="#">
                    <span class="sr-only">Beranda</span>
                    <img src="{{ asset('images/logo.svg') }}" alt="logo" class="h-16">
                </a>
            </div>

            <!-- Navbar Links Section -->
            <nav class="hidden md:block" aria-label="Main Navigation">
                <ul class="flex items-center gap-6 text-sm">
                    <li>
                        <a class="{{ request()->is('/', 'login') ? 'text-orange-500 font-bold dark:text-orange-500 dark:font-bold' : 'text-gray-500 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-500 ease-in duration-300"
                            href="{{ route('quest.index') }}">Beranda</a>
                    </li>
                    <li>
                        <a class="{{ request()->is('tentang-kami') ? 'text-orange-500 font-bold dark:text-orange-500 dark:font-bold' : 'text-gray-500 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-500 ease-in duration-300"
                            href="{{ route('quest.about') }}">Tentang Kami</a>
                    </li>
                    <li>
                        <a class="{{ request()->is('layanan-kami') ? 'text-orange-500 font-bold dark:text-orange-500 dark:font-bold' : 'text-gray-500 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-500 ease-in duration-300"
                            href="{{ route('quest.service') }}">Layanan</a>
                    </li>
                    <li>
                        <a class="{{ request()->is('blogs', 'blogs/*') ? 'text-orange-500 font-bold dark:text-orange-500 dark:font-bold' : 'text-gray-500 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-500 ease-in duration-300"
                            href="{{ route('blogs.index') }}">Blogs</a>
                    </li>
                </ul>
            </nav>

            <!-- Auth / Dashboard Section -->
            <div class="sm:flex sm:gap-4">
                @if (Route::has('login'))
                    @if (Auth::guard('web')->check())
                        <a class="rounded-md bg-orange-500 hover:bg-orange-600 text-white px-5 md:px-8 lg:px-10 py-2.5 text-sm font-medium shadow dark:bg-orange-600 dark:hover:bg-orange-700 dark:text-gray-200 ease-in duration-300"
                            href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    @elseif(Auth::guard('student')->check())
                        <a class="rounded-md bg-orange-500 hover:bg-orange-600 text-white px-5 md:px-8 lg:px-10 py-2.5 text-sm font-medium shadow dark:bg-orange-600 dark:hover:bg-orange-700 dark:text-gray-200 ease-in duration-300"
                            href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    @else
                        <a class="rounded-md bg-orange-500 hover:bg-orange-600 text-white px-5 md:px-8 lg:px-10 py-2.5 text-sm font-medium shadow dark:bg-orange-600 dark:hover:bg-orange-700 dark:text-gray-200 ease-in duration-300"
                            href="{{ route('login') }}">
                            Login
                        </a>
                    @endif
                @endif
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="block md:hidden">
                <button @click="isOpen = !isOpen"
                    class="rounded bg-gray-100 p-2 text-gray-600 transition hover:text-gray-600/75 dark:bg-gray-800 dark:text-gray-300 dark:hover:text-gray-300/75">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Section -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="md:hidden bg-white shadow-lg rounded-lg mt-2 p-4 dark:bg-gray-800">
        <nav aria-label="Mobile Navigation">
            <ul class="flex flex-col items-center gap-4 text-sm">
                <li>
                    <a class="{{ request()->is('/', 'login') ? 'text-orange-500 font-semibold dark:text-orange-600 dark:font-semibold' : 'text-gray-70 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-700"
                        href="/">Beranda</a>
                </li>
                <li>
                    <a class="{{ request()->is('tentang-kami') ? 'text-orange-500 font-semibold dark:text-orange-600 dark:font-semibold' : 'text-gray-70 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-700"
                        href="/tentang-kami">Tentang Kami</a>
                </li>
                <li>
                    <a class="{{ request()->is('layanan-kami') ? 'text-orange-500 font-semibold dark:text-orange-600 dark:font-semibold' : 'text-gray-70 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-700"
                        href="/layanan-kami">Layanan Kami</a>
                </li>
                <li>
                    <a class="{{ request()->is('blogs') ? 'text-orange-500 font-semibold dark:text-orange-600 dark:font-semibold' : 'text-gray-70 dark:text-gray-300' }} transition hover:text-orange-500 dark:hover:text-orange-700"
                        href="/blogs">Blogs</a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<!-- Scroll to Top Button -->
<button id="scrollToTopBtn"
    class="fixed z-50 bottom-6 right-6 bg-orange-500 text-white rounded-full shadow-lg transform transition-all ease-in-out duration-300 hover:scale-110 hover:shadow-xl focus:outline-none opacity-0 pointer-events-none">
    <span class="material-symbols-outlined p-3">
        arrow_upward
    </span>
</button>

<script>
    let lastScrollTop = 0; // Track scroll position
    const navbar = document.getElementById('navbar');
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");

    // Fungsi untuk menangani scroll
    window.onscroll = function() {
        // Scroll ke bawah: tambahkan shadow ke navbar jika sudah scroll lebih dari 100px
        if (window.pageYOffset > 100) {
            navbar.classList.add("shadow-lg");
            navbar.classList.add("shadow-md");
            scrollToTopBtn.classList.remove("opacity-0", "pointer-events-none");
            scrollToTopBtn.classList.add("opacity-100", "pointer-events-auto");
        } else {
            // Scroll ke atas: hilangkan shadow pada navbar
            navbar.classList.remove("shadow-lg");
            navbar.classList.remove("shadow-md");
            scrollToTopBtn.classList.add("opacity-0", "pointer-events-none");
            scrollToTopBtn.classList.remove("opacity-100", "pointer-events-auto");
        }

        lastScrollTop = window.pageYOffset <= 0 ? 0 : window
            .pageYOffset; // Mencegah scroll ke atas menyebabkan masalah
    };

    // Fungsi untuk scroll ke atas saat tombol diklik
    document.getElementById("scrollToTopBtn").addEventListener("click", function() {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>
