<header x-data="{
    isOpen: false,
    scrolled: false,
    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 20;
        })
    }
}" id="navbar"
    :class="{
        'bg-white/70 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/60 dark:border-slate-800/60 shadow-xl': scrolled,
        'bg-transparent border-b border-transparent': !scrolled
    }"
    class="fixed w-full top-0 left-0 z-[9999] transition-all duration-500 ease-in-out">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between">

            <div class="flex-shrink-0 transition-transform duration-500 hover:scale-105">
                <a class="block interactive flex items-center gap-3" href="{{ route('quest.index') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="logo" class="h-7 w-auto">
                </a>
            </div>

            <div class="hidden md:flex md:items-center md:gap-10">
                <nav aria-label="Main Navigation">
                    <ul class="flex items-center gap-8 text-[13px] font-bold uppercase tracking-widest">
                        <li>
                            <a class="interactive relative py-2 transition-all duration-300 {{ request()->is('/', 'login') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-500' }} group"
                                href="{{ route('quest.index') }}">
                                Beranda
                                <span
                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 transform {{ request()->is('/', 'login') ? 'scale-x-100' : 'scale-x-0' }} group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                            </a>
                        </li>
                        <li>
                            <a class="interactive relative py-2 transition-all duration-300 {{ request()->is('tentang-kami') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-500' }} group"
                                href="{{ route('quest.about') }}">
                                Tentang Kami
                                <span
                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 transform {{ request()->is('tentang-kami') ? 'scale-x-100' : 'scale-x-0' }} group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                            </a>
                        </li>
                        <li>
                            <a class="interactive relative py-2 transition-all duration-300 {{ request()->is('layanan-kami') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-500' }} group"
                                href="{{ route('quest.service') }}">
                                Layanan
                                <span
                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 transform {{ request()->is('layanan-kami') ? 'scale-x-100' : 'scale-x-0' }} group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                            </a>
                        </li>
                        <li>
                            <a class="interactive relative py-2 transition-all duration-300 {{ request()->is('blogs', 'blogs/*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-500' }} group"
                                href="{{ route('blogs.index') }}">
                                Blogs
                                <span
                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500 transform {{ request()->is('blogs', 'blogs/*') ? 'scale-x-100' : 'scale-x-0' }} group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="flex items-center gap-4 border-l border-slate-200 dark:border-slate-800 pl-8">
                    @if (Auth::guard('web')->check() || Auth::guard('student')->check())
                        <a class="interactive px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-black tracking-widest uppercase rounded-xl shadow-xl hover:scale-105 active:scale-95 transition-all"
                            href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    @else
                        <a class="interactive px-8 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black tracking-widest uppercase rounded-xl shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all"
                            href="{{ route('login') }}">
                            Login
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center md:hidden">
                <button @click="isOpen = !isOpen"
                    class="interactive p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 transition-all active:scale-90">
                    <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="md:hidden absolute top-full left-0 w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border-b border-slate-200 dark:border-slate-800 shadow-2xl p-6"
        x-cloak>

        <nav aria-label="Mobile Navigation">
            <ul class="flex flex-col gap-6 text-center">
                <li>
                    <a class="block text-sm font-bold uppercase tracking-[0.2em] {{ request()->is('/', 'login') ? 'text-emerald-600' : 'text-slate-600 dark:text-slate-400' }}"
                        href="{{ route('quest.index') }}" @click="isOpen = false">Beranda</a>
                </li>
                <li>
                    <a class="block text-sm font-bold uppercase tracking-[0.2em] {{ request()->is('tentang-kami') ? 'text-emerald-600' : 'text-slate-600 dark:text-slate-400' }}"
                        href="{{ route('quest.about') }}" @click="isOpen = false">Tentang Kami</a>
                </li>
                <li>
                    <a class="block text-sm font-bold uppercase tracking-[0.2em] {{ request()->is('layanan-kami') ? 'text-emerald-600' : 'text-slate-600 dark:text-slate-400' }}"
                        href="{{ route('quest.service') }}" @click="isOpen = false">Layanan</a>
                </li>
                <li>
                    <a class="block text-sm font-bold uppercase tracking-[0.2em] {{ request()->is('blogs', 'blogs/*') ? 'text-emerald-600' : 'text-slate-600 dark:text-slate-400' }}"
                        href="{{ route('blogs.index') }}" @click="isOpen = false">Blogs</a>
                </li>
                <li class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    @if (Auth::guard('web')->check() || Auth::guard('student')->check())
                        <a class="block py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl"
                            href="{{ route('dashboard') }}">DASHBOARD</a>
                    @else
                        <a class="block py-4 bg-emerald-600 text-white font-black rounded-2xl"
                            href="{{ route('login') }}">LOGIN SISTEM</a>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
</header>

<button id="scrollToTopBtn"
    class="fixed z-[9998] bottom-8 right-8 w-14 h-14 bg-emerald-600 text-white rounded-2xl shadow-[0_20px_40px_rgba(16,185,129,0.4)] flex items-center justify-center transform transition-all duration-500 hover:scale-110 active:scale-90 opacity-0 pointer-events-none group interactive">
    <span class="material-symbols-outlined transition-transform group-hover:-translate-y-1">
        north
    </span>
</button>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scrollToTopBtn = document.getElementById("scrollToTopBtn");

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.remove("opacity-0", "pointer-events-none", "translate-y-10");
                scrollToTopBtn.classList.add("opacity-100", "pointer-events-auto", "translate-y-0");
            } else {
                scrollToTopBtn.classList.add("opacity-0", "pointer-events-none", "translate-y-10");
                scrollToTopBtn.classList.remove("opacity-100", "pointer-events-auto", "translate-y-0");
            }
        });

        scrollToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    });
</script>
