<x-guest-layout>
    @slot('seo_key',
        'si-gochild, growth and development child information system, sistem informasi anak, antropometri,
        mmdst')
        @slot('seo_description',
            'SI-GoChild: Sistem informasi revolusioner untuk memantau tumbuh kembang anak dengan
            presisi medis.')
            @slot('seo_meta_title', 'SI-GoChild - Precise Growth Intelligence')
            @slot('seo_title', 'SI-GoChild')

            {{-- AlpineJS --}}
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

            @push('styles')
                <link
                    href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
                    rel="stylesheet">
                <style>
                    /* --- DESIGN TOKENS --- */
                    :root {
                        --primary: #10b981;
                        --secondary: #3b82f6;
                        --cursor-bg: rgba(16, 185, 129, 0.3);
                    }

                    html {
                        scroll-behavior: smooth;
                        cursor: none !important;
                    }

                    a,
                    button,
                    .interactive {
                        cursor: none !important;
                    }

                    body {
                        font-family: 'Plus Jakarta Sans', sans-serif;
                        @apply bg-[#f8fafc] text-slate-900 dark:bg-[#020617] dark:text-slate-100 transition-colors duration-700 overflow-x-hidden;
                    }

                    h1,
                    h2,
                    h3,
                    h4,
                    .font-heading {
                        font-family: 'Outfit', sans-serif;
                    }

                    /* --- HYPER-INTERACTIVE CURSOR --- */
                    #cursor-dot {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 8px;
                        height: 8px;
                        background: var(--primary);
                        border-radius: 50%;
                        pointer-events: none;
                        z-index: 1000000;
                        transform: translate(-50%, -50%);
                    }

                    #cursor-ring {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 40px;
                        height: 40px;
                        border: 1.5px solid var(--primary);
                        border-radius: 50%;
                        pointer-events: none;
                        z-index: 999999;
                        transform: translate(-50%, -50%);
                        transition: width 0.4s cubic-bezier(0.19, 1, 0.22, 1), height 0.4s cubic-bezier(0.19, 1, 0.22, 1), background 0.4s ease;
                    }

                    /* --- LUXURY BENTO GRID --- */
                    .bento-luxury {
                        @apply relative overflow-hidden bg-white/80 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800/60 rounded-[3rem] transition-all duration-700;
                        backdrop-filter: blur(20px);
                    }

                    .bento-luxury:hover {
                        @apply -translate-y-3 border-emerald-500/40;
                        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.1);
                    }

                    /* --- ANIMATIONS --- */
                    .reveal {
                        opacity: 0;
                        transform: translateY(40px) scale(0.98);
                        transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1);
                    }

                    .reveal.active {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }

                    .gradient-brand {
                        @apply text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 via-teal-500 to-blue-600 dark:from-emerald-400 dark:via-teal-300 dark:to-blue-400;
                        background-size: 200% auto;
                        animation: shine 6s linear infinite;
                    }

                    @keyframes shine {
                        to {
                            background-position: 200% center;
                        }
                    }

                    /* Background Pattern */
                    .bg-dot-grid {
                        position: fixed;
                        inset: 0;
                        z-index: -1;
                        background-image: radial-gradient(circle at center, rgba(16, 185, 129, 0.08) 1.5px, transparent 0);
                        background-size: 40px 40px;
                    }

                    /* Floating 3D Image */
                    .hero-mockup {
                        transition: transform 0.2s ease-out;
                        transform-style: preserve-3d;
                    }
                </style>
            @endpush

            <div x-data="{ openModal: null }" class="relative">

                <div class="bg-dot-grid"></div>
                <div class="fixed inset-0 pointer-events-none z-0">
                    <div
                        class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-emerald-500/5 rounded-full blur-[120px] animate-pulse">
                    </div>
                    <div
                        class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-500/5 rounded-full blur-[120px] animate-pulse delay-1000">
                    </div>
                </div>

                <section id="home" class="relative z-10 pt-32 lg:pt-48 pb-20">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">

                            <div class="text-center lg:text-left space-y-10">
                                <div
                                    class="reveal inline-flex items-center gap-3 px-5 py-2 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span
                                        class="text-[10px] font-black tracking-[0.4em] uppercase text-slate-500 dark:text-emerald-400">Expert
                                        System SI-GoChild</span>
                                </div>

                                <div class="reveal delay-100">
                                    <h2
                                        class="text-xs font-bold tracking-[0.6em] uppercase text-slate-400 dark:text-slate-500 mb-6 leading-relaxed">
                                        Growth and Development of Child Information System
                                    </h2>
                                    <h1
                                        class="text-6xl md:text-7xl lg:text-8xl font-black tracking-tighter text-slate-900 dark:text-white leading-[0.9]">
                                        Masa Depan <br> <span class="gradient-brand italic">Sangat Presisi.</span>
                                    </h1>
                                </div>

                                <p
                                    class="reveal delay-200 text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed">
                                    Mendigitalisasi pemantauan tumbuh kembang anak melalui analisis
                                    <strong>Antropometri</strong> & <strong>MMDST</strong> dengan standar akurasi absolut
                                    Kemenkes RI.
                                </p>

                                <div
                                    class="reveal delay-300 flex flex-col sm:flex-row gap-5 justify-center lg:justify-start pt-4">
                                    <a href="#layanan"
                                        class="interactive group px-12 py-5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl shadow-2xl hover:scale-105 active:scale-95 transition-all tracking-widest text-[10px] uppercase">
                                        MULAI SKRINING
                                    </a>
                                    <a href="#tentang"
                                        class="interactive px-12 py-5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold rounded-2xl border-2 border-slate-200 dark:border-slate-700 hover:border-emerald-500 transition-all text-[10px] uppercase tracking-widest">
                                        METODOLOGI
                                    </a>
                                </div>
                            </div>

                            <div class="relative reveal delay-500 hero-container">
                                <div
                                    class="relative z-10 hero-mockup p-2 bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                    <img src="{{ asset('images/hero.png') }}"
                                        onerror="this.src='https://images.pexels.com/photos/3933245/pexels-photo-3933245.jpeg?auto=compress&cs=tinysrgb&w=800'"
                                        class="w-full h-[400px] lg:h-[550px] object-cover rounded-[2.5rem]" alt="Hero Image">
                                </div>

                                <div class="absolute -bottom-6 -right-6 bg-emerald-600 text-white p-6 rounded-[2.5rem] shadow-2xl z-20 hidden lg:block animate-bounce"
                                    style="animation-duration: 4s;">
                                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Accuracy Rate</p>
                                    <p class="text-3xl font-black">100%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="tentang" class="py-32 relative z-10 bg-white dark:bg-[#020617] transition-colors duration-1000">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                        <div class="mb-24 reveal">
                            <h2
                                class="text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-[0.5em] mb-4">
                                Core Competency</h2>
                            <h3 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white tracking-tighter">Mengapa
                                Harus <br> <span class="gradient-brand italic">SI-GoChild?</span></h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

                            <div class="md:col-span-8 bento-luxury p-12 lg:p-14 group reveal">
                                <div class="relative z-10">
                                    <div
                                        class="w-16 h-16 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mb-10 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-3xl font-bold text-slate-900 dark:text-white mb-6 tracking-tight">Kepatuhan
                                        Terhadap Standar RI</h4>
                                    <p class="text-slate-500 dark:text-slate-400 text-xl leading-relaxed max-w-xl font-medium">
                                        Algoritma kami dibangun berdasarkan <strong>Permenkes No 2 Tahun 2020</strong>. Kami
                                        memastikan perhitungan status gizi (Z-Score) dan analisis perkembangan anak akurat dan
                                        diakui secara medis di Indonesia.
                                    </p>
                                </div>
                            </div>

                            <div
                                class="md:col-span-4 bento-luxury p-12 bg-slate-900 dark:bg-emerald-950/20 text-white border-none reveal delay-100">
                                <div class="h-full flex flex-col justify-between">
                                    <div>
                                        <h4 class="text-3xl font-bold mb-6 tracking-tight">Data <br> Real-Time.</h4>
                                        <p class="text-slate-400 text-lg leading-relaxed font-medium">Laporan grafik pertumbuhan
                                            digital yang otomatis terupdate setiap pemeriksaan.</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping"></div>
                                        <span class="text-xs font-black uppercase tracking-widest text-emerald-400">Monitoring
                                            Aktif</span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="md:col-span-5 bento-luxury p-12 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 dark:from-blue-600/10 dark:to-indigo-600/10 reveal delay-200">
                                <h4 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    Deteksi MMDST
                                </h4>
                                <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Mengevaluasi sektor
                                    perkembangan motorik, bahasa, dan sosial untuk cegah keterlambatan dini.</p>
                            </div>

                            <div
                                class="md:col-span-7 bento-luxury p-12 flex flex-col md:flex-row items-center gap-10 bg-white dark:bg-slate-900 reveal delay-300">
                                <div class="flex-1">
                                    <h4
                                        class="text-3xl font-black text-slate-900 dark:text-white mb-4 italic leading-none tracking-tighter uppercase">
                                        Zero Latency Analysis.</h4>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium">Ubah proses input manual yang
                                        lambat menjadi hasil diagnosa otomatis dalam hitungan detik.</p>
                                </div>
                                <div class="w-32 h-32 flex items-center justify-center relative">
                                    <div class="absolute inset-0 bg-emerald-500/10 rounded-full animate-ping"></div>
                                    <div
                                        class="relative w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center shadow-2xl">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="layanan" class="py-32 relative z-10 bg-slate-950 overflow-hidden">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center mb-24 reveal">
                            <h2 class="text-4xl md:text-6xl font-black text-white leading-none tracking-tighter">Integrasi <span
                                    class="gradient-brand italic text-glow-premium">Solusi Terpadu.</span></h2>
                        </div>

                        <div class="grid md:grid-cols-2 gap-12 lg:gap-20">
                            <div class="group relative reveal">
                                <div
                                    class="absolute -inset-4 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 rounded-[4rem] blur-3xl opacity-0 group-hover:opacity-100 transition-all duration-1000">
                                </div>
                                <div
                                    class="relative bg-slate-900 border border-white/5 rounded-[3.5rem] p-12 lg:p-16 flex flex-col items-center group-hover:border-emerald-500/50 transition-colors duration-500">
                                    <div
                                        class="w-24 h-24 bg-emerald-500/10 rounded-3xl flex items-center justify-center text-emerald-400 mb-12 border border-emerald-500/20 shadow-inner">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-3xl font-black text-white mb-6 uppercase tracking-tighter">Skrining
                                        Pertumbuhan</h4>
                                    <p class="text-slate-400 text-lg mb-12 font-medium">Analisis status gizi si kecil
                                        berdasarkan tinggi, berat, dan umur secara klinis.</p>
                                    <button @click="openModal = 'antropometri'"
                                        class="interactive w-full py-5 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all active:scale-95 uppercase tracking-[0.3em] text-[10px]">RINCIAN
                                        TEKNIS</button>
                                </div>
                            </div>

                            <div class="group relative reveal delay-200">
                                <div
                                    class="absolute -inset-4 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 rounded-[4rem] blur-3xl opacity-0 group-hover:opacity-100 transition-all duration-1000">
                                </div>
                                <div
                                    class="relative bg-slate-900 border border-white/5 rounded-[3.5rem] p-12 lg:p-16 flex flex-col items-center group-hover:border-blue-500/50 transition-colors duration-500">
                                    <div
                                        class="w-24 h-24 bg-blue-500/10 rounded-3xl flex items-center justify-center text-blue-400 mb-12 border border-blue-500/20 shadow-inner">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-3xl font-black text-white mb-6 uppercase tracking-tighter">Skrining
                                        Perkembangan</h4>
                                    <p class="text-slate-400 text-lg mb-12 font-medium">Evaluasi milestone kecerdasan &
                                        perkembangan motorik melalui metode MMDST.</p>
                                    <button @click="openModal = 'mmdst'"
                                        class="interactive w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all active:scale-95 uppercase tracking-[0.3em] text-[10px]">RINCIAN
                                        TEKNIS</button>
                                </div>
                            </div>

                            <template x-teleport="body">
                                <div x-show="openModal" class="relative z-[1000001]" x-cloak>
                                    <div x-show="openModal" x-transition.opacity
                                        class="fixed inset-0 bg-slate-950/95 backdrop-blur-2xl"></div>
                                    <div class="fixed inset-0 flex items-center justify-center p-6">
                                        <div x-show="openModal" @click.away="openModal = null"
                                            x-transition:enter="ease-out duration-500"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-[3.5rem] p-12 lg:p-16 relative border border-slate-200 dark:border-slate-800 shadow-2xl">
                                            <button @click="openModal = null"
                                                class="interactive absolute top-10 right-10 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all hover:rotate-90">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <div class="text-center">
                                                <h4 class="text-4xl font-black mb-8 dark:text-white uppercase tracking-tighter"
                                                    x-text="openModal === 'antropometri' ? 'Rincian Antropometri' : 'Logika MMDST'">
                                                </h4>
                                                <div
                                                    class="space-y-6 text-slate-500 dark:text-slate-400 text-xl leading-relaxed font-medium">
                                                    <p x-show="openModal === 'antropometri'">Menggunakan rumus Z-Score dari
                                                        Kemenkes untuk variabel BB/U, TB/U, dan BB/TB secara otomatis untuk
                                                        mendeteksi indikasi stunting, kurus, atau obesitas.</p>
                                                    <p x-show="openModal === 'mmdst'">Skrining sektor perkembangan
                                                        Personal-Sosial, Bahasa, Motorik Halus & Kasar. Menjamin setiap tahap
                                                        pertumbuhan otak dan fisik terpantau dengan standar internasional.</p>
                                                </div>
                                                <div class="mt-12 flex justify-center">
                                                    <button @click="openModal = null"
                                                        class="interactive px-12 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl tracking-widest text-[10px] uppercase">MENGERTI</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </section>

                <section class="py-48 relative z-10 bg-white dark:bg-[#020617] text-center transition-colors duration-1000">
                    <div class="max-w-4xl mx-auto px-4">
                        <h2
                            class="reveal text-6xl md:text-8xl font-black text-slate-900 dark:text-white tracking-tighter mb-12 leading-[0.85]">
                            Masa Depan <br> <span class="gradient-brand italic">Indonesia Berawal Di Sini.</span>
                        </h2>
                        <p
                            class="reveal delay-100 text-xl text-slate-500 dark:text-slate-400 mb-20 max-w-2xl mx-auto font-medium">
                            Pastikan buah hati Anda tumbuh maksimal dengan data yang tepat.</p>

                        <div class="reveal delay-200 flex flex-col sm:flex-row items-center justify-center gap-8">
                            <a href="https://wa.me/6281991545653" target="_blank"
                                class="interactive w-full sm:w-auto px-16 py-7 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-full shadow-2xl hover:scale-110 transition-all tracking-widest text-[10px] uppercase">
                                Hubungi Admin Official
                            </a>
                            <a href="{{ route('login') }}"
                                class="interactive w-full sm:w-auto px-16 py-7 bg-transparent border-2 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white font-black rounded-full hover:bg-slate-100 dark:hover:bg-slate-900 transition-all tracking-widest text-[10px] uppercase">Masuk
                                Sistem</a>
                        </div>

                        <div
                            class="reveal delay-400 mt-48 text-[10px] text-slate-400 dark:text-slate-700 font-black uppercase tracking-[1.5em]">
                            &copy; 2025 Expert System SI-GoChild &bull; Built for the Future
                        </div>
                    </div>
                </section>

            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {

                    // 1. ADVANCED CURSOR SYSTEM (Fixed Position Outside Main flow)
                    const dot = document.querySelector('#cursor-dot');
                    const ring = document.querySelector('#cursor-ring');

                    let mouseX = 0,
                        mouseY = 0;
                    let dotX = 0,
                        dotY = 0;
                    let ringX = 0,
                        ringY = 0;

                    document.addEventListener('mousemove', (e) => {
                        mouseX = e.clientX;
                        mouseY = e.clientY;
                    });

                    const animateCursor = () => {
                        // Lerp smoothing (0.2 for dot, 0.12 for ring)
                        dotX += (mouseX - dotX) * 0.25;
                        dotY += (mouseY - dotY) * 0.25;
                        ringX += (mouseX - ringX) * 0.15;
                        ringY += (mouseY - ringY) * 0.15;

                        dot.style.left = `${dotX}px`;
                        dot.style.top = `${dotY}px`;
                        ring.style.left = `${ringX}px`;
                        ring.style.top = `${ringY}px`;

                        requestAnimationFrame(animateCursor);
                    };
                    animateCursor();

                    // Interactivity Hook
                    const setupInteractivity = () => {
                        const interactives = document.querySelectorAll('.interactive, button, a');
                        interactives.forEach(el => {
                            el.addEventListener('mouseenter', () => {
                                ring.style.width = '70px';
                                ring.style.height = '70px';
                                ring.style.background = 'rgba(16, 185, 129, 0.15)';
                                ring.style.borderColor = 'transparent';
                                dot.style.opacity = '0';
                            });
                            el.addEventListener('mouseleave', () => {
                                ring.style.width = '34px';
                                ring.style.height = '34px';
                                ring.style.background = 'transparent';
                                ring.style.borderColor = '#10b981';
                                dot.style.opacity = '1';
                            });
                        });
                    }
                    setupInteractivity();

                    // 2. SCROLL REVEAL ENGINE
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('active');
                            }
                        });
                    }, {
                        threshold: 0.1,
                        rootMargin: "0px 0px -50px 0px"
                    });

                    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

                    // 3. 3D HERO TILT EFFECT
                    const heroContainer = document.querySelector('.hero-container');
                    const heroMockup = document.querySelector('.hero-mockup');
                    if (heroContainer && heroMockup) {
                        heroContainer.addEventListener('mousemove', (e) => {
                            const rect = heroContainer.getBoundingClientRect();
                            const x = (e.clientX - rect.left) / rect.width - 0.5;
                            const y = (e.clientY - rect.top) / rect.height - 0.5;
                            heroMockup.style.transform =
                                `perspective(1000px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg)`;
                        });
                        heroContainer.addEventListener('mouseleave', () => {
                            heroMockup.style.transform = `perspective(1000px) rotateY(0deg) rotateX(0deg)`;
                        });
                    }
                });
            </script>
        </x-guest-layout>
