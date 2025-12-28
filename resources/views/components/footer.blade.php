{{-- FOOTER SECTION --}}
<footer
    class="relative z-10 bg-white dark:bg-[#020617] border-t border-slate-200 dark:border-slate-800 transition-colors duration-1000">
    <div
        class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent">
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8">

            <div class="reveal space-y-6">
                <a href="{{ route('quest.index') }}" class="interactive inline-flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        class="h-10 w-auto transition-transform duration-500 group-hover:scale-110">
                </a>
                <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400 font-medium">
                    Sistem informasi cerdas yang didedikasikan untuk memantau setiap milimeter pertumbuhan dan setiap
                    tahap perkembangan anak Indonesia dengan standar medis tertinggi.
                </p>
                <div class="flex items-center gap-4 pt-2">
                    <a href="#"
                        class="interactive w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all border border-slate-100 dark:border-slate-700">
                        <i class="material-symbols-outlined text-xl">language</i>
                    </a>
                    <a href="#"
                        class="interactive w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all border border-slate-100 dark:border-slate-700">
                        <i class="material-symbols-outlined text-xl">public</i>
                    </a>
                </div>
            </div>

            <div class="reveal delay-100 space-y-6">
                <h4 class="text-xs font-black uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">
                    Navigasi</h4>
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('quest.index') }}"
                            class="interactive text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700"></span> Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('quest.about') }}"
                            class="interactive text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700"></span> Tentang Kami
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('quest.service') }}"
                            class="interactive text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700"></span> Layanan Kami
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blogs.index') }}"
                            class="interactive text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700"></span> Blogs Kami
                        </a>
                    </li>
                </ul>
            </div>

            <div class="reveal delay-200 space-y-6">
                <h4 class="text-xs font-black uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Temukan
                    Kami</h4>
                <div class="space-y-4">
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400 font-medium">
                        Jl Giok no 17 Blok B-5 Perumahan Villa Pisma Asri (Berlian) Desa Podo, Kec. Kedungwuni, Kab.
                        Pekalongan, 51173
                    </p>
                    <a href="https://maps.google.com/?q=Villa+Pisma+Asri+Pekalongan" target="_blank"
                        class="interactive inline-flex items-center gap-3 px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-black tracking-widest uppercase hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm">
                        <span>Buka Google Maps</span>
                        <span class="material-symbols-outlined text-sm">location_on</span>
                    </a>
                </div>
            </div>

            <div class="reveal delay-300 space-y-6">
                <h4 class="text-xs font-black uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Hubungi
                    Kami</h4>
                <div class="space-y-4">
                    <a href="mailto:info@sigochild.my.id"
                        class="interactive group flex items-center gap-4 p-4 rounded-[1.5rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 hover:border-blue-500/30 transition-all">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                            <span class="material-symbols-outlined text-xl">mail</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Email Respon Cepat
                            </p>
                            <p
                                class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-500 transition-colors">
                                info@sigochild.my.id</p>
                        </div>
                    </a>
                    <a href="https://wa.me/6281991545653" target="_blank"
                        class="interactive group flex items-center gap-4 p-4 rounded-[1.5rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 hover:border-emerald-500/30 transition-all">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined text-xl">chat</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">WhatsApp Admin</p>
                            <p
                                class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-emerald-500 transition-colors">
                                0819-9154-5653</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div
            class="mt-20 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">
                    &copy; 2025 SI-GoChild Systems. All rights reserved.
                </p>
                <p class="text-[10px] text-slate-400 dark:text-slate-600 font-medium mt-1">
                    Growth and Development of Child Information System &bull; Versi 1.0 Excellence
                </p>
            </div>

            <div class="reveal delay-500 text-xs font-bold text-slate-500 dark:text-slate-400">
                Crafted with <span class="text-rose-500 animate-pulse inline-block mx-1">❤️</span> by
                <a href="https://www.linkedin.com/in/reza-edi-saputra/"
                    class="interactive text-slate-900 dark:text-white hover:text-emerald-500 transition-colors underline decoration-emerald-500/30 underline-offset-4">Reza
                    Edi Saputra</a>
            </div>
        </div>
    </div>
</footer>
