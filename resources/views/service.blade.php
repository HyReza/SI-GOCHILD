<x-guest-layout>
    @slot('seo_key',
        'layanan si-gochild, skrining antropometri, tes mmdst, deteksi stunting digital, grafik kms online,
        monitoring anak')
        @slot('seo_description',
            'Jelajahi layanan unggulan SI-GoChild: Analisis Antropometri presisi dan Skrining MMDST
            komprehensif untuk tumbuh kembang optimal anak.')
            @slot('seo_meta_title', 'Layanan Unggulan - SI-GoChild Future System')
            @slot('seo_title', 'Layanan Kami | SI-GoChild')

            <div x-data="{ openModal: null }" class="relative min-h-screen">

                <section class="relative pt-40 pb-20 overflow-hidden">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                        <div class="reveal space-y-6">
                            <div
                                class="inline-flex items-center gap-3 px-5 py-2 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl mx-auto">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                <span
                                    class="text-[10px] font-black tracking-[0.4em] uppercase text-blue-600 dark:text-blue-400">Ekosistem
                                    Komprehensif</span>
                            </div>

                            <h1
                                class="text-5xl md:text-8xl font-black tracking-tighter text-slate-900 dark:text-white leading-none uppercase">
                                Solusi <span class="gradient-brand italic">Masa Depan.</span>
                            </h1>

                            <p
                                class="text-lg md:text-2xl text-slate-500 dark:text-slate-400 max-w-3xl mx-auto leading-relaxed font-medium">
                                Kami menyediakan infrastruktur digital untuk mendeteksi, menganalisis, dan memantau setiap tahap
                                pertumbuhan anak secara absolut.
                            </p>
                        </div>
                    </div>
                </section>

                <section
                    class="py-24 relative z-10 bg-white dark:bg-slate-950 transition-colors duration-1000 border-y border-slate-100 dark:border-slate-900">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                            <div class="reveal bento-luxury p-10 lg:p-14 group flex flex-col h-full">
                                <div class="flex justify-between items-start mb-12">
                                    <div
                                        class="w-16 h-16 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-500">
                                        <span class="material-symbols-outlined text-4xl font-light">straighten</span>
                                    </div>
                                    <span
                                        class="text-[10px] font-black text-slate-300 dark:text-slate-600 tracking-widest uppercase">Pilar
                                        01</span>
                                </div>
                                <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-6">Skrining Pertumbuhan
                                    (Antropometri)</h3>
                                <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed font-medium mb-10">
                                    Ubah data fisik menjadi diagnosa klinis. Kami menggunakan standar Antropometri Sesuai
                                    Peraturan Kementrian Kesehatan No 2 Tahun 2020 untuk hasil
                                    mutlak mengenai kondisi gizi dan pertumbuhan linear anak.
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
                                    <div
                                        class="p-5 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 hover:border-emerald-500/30 transition-colors">
                                        <p
                                            class="font-black text-emerald-600 dark:text-emerald-400 text-sm mb-1 uppercase tracking-tighter">
                                            Status BB / U</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Berat Badan berdasarkan Umur.</p>
                                    </div>
                                    <div
                                        class="p-5 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 hover:border-emerald-500/30 transition-colors">
                                        <p
                                            class="font-black text-emerald-600 dark:text-emerald-400 text-sm mb-1 uppercase tracking-tighter">
                                            Status TB / U</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Deteksi dini indikasi Stunting.
                                        </p>
                                    </div>
                                </div>

                                <button @click="openModal = 'antropometri'"
                                    class="interactive mt-auto w-full py-5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl shadow-xl hover:bg-emerald-600 dark:hover:bg-emerald-500 hover:text-white transition-all tracking-[0.2em] text-[10px] uppercase">
                                    Lihat Rincian Teknis
                                </button>
                            </div>

                            <div
                                class="reveal delay-200 bento-luxury p-10 lg:p-14 group flex flex-col h-full bg-slate-900 dark:bg-slate-900 text-white border-none">
                                <div class="flex justify-between items-start mb-12">
                                    <div
                                        class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-xl shadow-blue-500/30 group-hover:scale-110 transition-transform duration-500">
                                        <span class="material-symbols-outlined text-4xl font-light">psychology</span>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-600 tracking-widest uppercase">Pilar
                                        02</span>
                                </div>
                                <h3 class="text-3xl font-black mb-6">Skrining Perkembangan (MMDST)</h3>
                                <p class="text-slate-400 text-lg leading-relaxed font-medium mb-10">
                                    Evaluasi kematangan fungsi otak dan saraf. Metode tervalidasi untuk mendeteksi risiko
                                    keterlambatan perkembangan kognitif, motorik, dan sosial.
                                </p>

                                <div class="space-y-4 mb-10">
                                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl border border-white/10">
                                        <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                                        </div>
                                        <span class="font-bold text-sm">Personal Sosial:</span>
                                        <span class="text-xs text-slate-400">Kemandirian dan interaksi lingkungan.</span>
                                    </div>
                                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl border border-white/10">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]">
                                        </div>
                                        <span class="font-bold text-sm">Bahasa & Motorik:</span>
                                        <span class="text-xs text-slate-400">Koordinasi gerak dan kemampuan verbal.</span>
                                    </div>
                                </div>

                                <button @click="openModal = 'mmdst'"
                                    class="interactive mt-auto w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all active:scale-95 shadow-xl shadow-blue-500/20 tracking-[0.2em] text-[10px] uppercase">
                                    Lihat Rincian Teknis
                                </button>
                            </div>

                        </div>
                    </div>
                </section>

                <section class="py-32 relative z-10">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center mb-20 reveal">
                            <h2
                                class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.5em] mb-4">
                                Advance Capabilities
                            </h2>
                            <h3 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white leading-tight uppercase">
                                Fitur Pendukung <span class="gradient-brand italic">Sistem.</span>
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="bento-luxury p-8 reveal group hover:border-blue-500/50 transition-all duration-500">
                                <div
                                    class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                    <span class="material-symbols-outlined text-3xl font-bold">auto_graph</span>
                                </div>
                                <h4 class="text-xl font-bold dark:text-white mb-3 tracking-tighter uppercase">KMS Digital
                                    Interaktif</h4>
                                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium">
                                    Visualisasi grafik tren pertumbuhan otomatis yang presisi, memudahkan orang tua memantau
                                    progres fisik anak secara visual.
                                </p>
                            </div>

                            <div
                                class="bento-luxury p-8 reveal delay-100 group hover:border-emerald-500/50 transition-all duration-500">
                                <div
                                    class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-6 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500">
                                    <span class="material-symbols-outlined text-3xl font-bold">devices_other</span>
                                </div>
                                <h4 class="text-xl font-bold dark:text-white mb-3 tracking-tighter uppercase">Akses Data
                                    Real-Time</h4>
                                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium">
                                    Pantau data perkembangan anak kapan saja dan di mana saja. Sinkronisasi cerdas memastikan
                                    informasi selalu terbaru di semua perangkat Anda.
                                </p>
                            </div>

                            <div
                                class="bento-luxury p-8 reveal delay-200 group hover:border-amber-500/50 transition-all duration-500">
                                <div
                                    class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400 mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                    <span class="material-symbols-outlined text-3xl font-bold">analytics</span>
                                </div>
                                <h4 class="text-xl font-bold dark:text-white mb-3 tracking-tighter uppercase">Laporan Informatif
                                </h4>
                                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium">
                                    Hasil skrining disajikan dalam format yang sangat mudah dipahami dan kaya informasi,
                                    memberikan wawasan mendalam bagi orang tua.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <template x-teleport="body">
                    <div x-show="openModal" class="relative z-[1000001]" x-cloak>
                        <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-slate-950/95 backdrop-blur-2xl">
                        </div>
                        <div class="fixed inset-0 flex items-center justify-center p-6">
                            <div x-show="openModal" @click.away="openModal = null" x-transition:enter="ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-10"
                                class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-[3.5rem] p-12 lg:p-16 relative border border-slate-200 dark:border-slate-800 shadow-2xl">

                                <button @click="openModal = null"
                                    class="interactive absolute top-10 right-10 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all hover:rotate-90">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="text-center">
                                    <h4 class="text-4xl font-black mb-8 dark:text-white uppercase tracking-tighter"
                                        x-text="openModal === 'antropometri' ? 'Antropometri' : 'MMDST Intelligence'"></h4>
                                    <div
                                        class="space-y-6 text-slate-500 dark:text-slate-400 text-lg leading-relaxed font-medium">
                                        <p x-show="openModal === 'antropometri'">Sistem memproses parameter Berat Badan (BB),
                                            Tinggi Badan (TB), dan Lingkar Kepala (LK) berdasarkan standar antropometri Kemenkes
                                            RI 2020 untuk mendeteksi gizi kurang, stunting, hingga mikrosefali.</p>
                                        <p x-show="openModal === 'mmdst'">Metode validasi perkembangan untuk memantau apakah
                                            anak sudah mencapai tahapan (milestone) fungsional yang sesuai dengan usianya,
                                            mencakup sektor motorik dan kognitif.</p>
                                    </div>
                                    <div class="mt-14">
                                        <button @click="openModal = null"
                                            class="interactive px-12 py-4 bg-emerald-600 text-white font-black rounded-2xl tracking-widest text-[10px] uppercase shadow-2xl hover:scale-105 transition-all">SAYA
                                            MENGERTI</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('active');
                            }
                        });
                    }, {
                        threshold: 0.1
                    });

                    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
                });
            </script>
        </x-guest-layout>
