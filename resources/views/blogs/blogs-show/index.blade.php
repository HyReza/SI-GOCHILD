<x-guest-layout>
    @slot('seo_key', $article->seo_key ?? 'detail artikel, si-gochild, kesehatan anak')
    @slot('seo_description', Str::limit(strip_tags($article->content), 160))
    @slot('seo_meta_title', $article->title)
    @slot('seo_title', $article->title . ' | SI-GoChild')

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* --- PROGRESS BAR MEMBACA --- */
        #reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 5px;
            background: linear-gradient(to right, #10b981, #3b82f6);
            z-index: 1100005;
            transition: width 0.15s ease-out;
        }

        /* --- STYLING PRIORITAS UTAMA UNTUK QUILL JS --- */
        .ql-content {
            @apply text-slate-700 dark:text-slate-300 leading-[2] text-lg md:text-xl;
        }

        /* Fix List Tailwind Reset */
        .ql-content ol {
            list-style-type: decimal !important;
            padding-left: 2rem !important;
            margin-bottom: 1.5rem !important;
            display: block !important;
        }

        .ql-content ul {
            list-style-type: disc !important;
            padding-left: 2rem !important;
            margin-bottom: 1.5rem !important;
            display: block !important;
        }

        .ql-content li {
            display: list-item !important;
            padding-left: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        /* Heading di dalam konten */
        .ql-content h1,
        .ql-content h2,
        .ql-content h3 {
            @apply font-heading font-black text-slate-900 dark:text-white mt-12 mb-6 tracking-tighter !important;
        }

        .ql-content h1 {
            font-size: 2.5rem !important;
        }

        .ql-content h2 {
            font-size: 2rem !important;
        }

        .ql-content h3 {
            font-size: 1.5rem !important;
        }

        .ql-content p {
            @apply mb-6 text-justify;
        }

        .ql-content blockquote {
            @apply pl-8 border-l-4 border-emerald-500 italic text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/40 py-6 pr-6 rounded-r-[2.5rem] my-10 !important;
        }

        /* Gambar di dalam konten dibuat full tanpa potong */
        .ql-content img {
            @apply rounded-[2.5rem] shadow-2xl my-12 mx-auto border border-white/20 h-auto w-full max-w-full !important;
        }

        .ql-content a {
            @apply text-emerald-600 dark:text-emerald-400 font-bold underline decoration-emerald-500/30 underline-offset-4 hover:text-emerald-500 transition-colors;
        }

        /* Alignment Priority */
        .ql-align-center {
            text-align: center !important;
        }

        .ql-align-right {
            text-align: right !important;
        }

        .ql-align-justify {
            text-align: justify !important;
        }

        /* --- LUXURY SWEETALERT POPUP --- */
        .premium-swal-popup {
            @apply rounded-[3rem] p-12 border border-white/10 shadow-[0_50px_100px_-20px_rgba(0, 0, 0, 0.4)] bg-white/90 dark:bg-slate-900/90 backdrop-blur-3xl !important;
        }

        .premium-swal-title {
            @apply font-heading font-black text-3xl text-slate-900 dark:text-white tracking-tighter !important;
        }

        .premium-swal-confirm {
            @apply bg-emerald-600 hover:bg-emerald-500 text-white rounded-full px-12 py-4 font-black tracking-widest text-[11px] uppercase shadow-xl transition-all !important;
        }

        /* Click Ripple Effect */
        .click-ripple {
            position: fixed;
            width: 10px;
            height: 10px;
            background: rgba(16, 185, 129, 0.4);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1100006;
            transform: translate(-50%, -50%);
            animation: ripple-out 0.6s ease-out forwards;
        }

        @keyframes ripple-out {
            from {
                width: 0;
                height: 0;
                opacity: 1;
            }

            to {
                width: 100px;
                height: 100px;
                opacity: 0;
            }
        }
    </style>

    <div id="reading-progress"></div>

    <div class="relative min-h-screen">

        <section class="relative pt-32 md:pt-40 pb-20 overflow-hidden">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="reveal flex items-center justify-center gap-3 mb-8">
                    <a href="{{ route('blogs.index') }}"
                        class="interactive text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-emerald-500 transition-colors">Digital
                        Library</a>
                    <span class="text-slate-300">/</span>
                    <span
                        class="px-5 py-2 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                        {{ $article->category->category_name }}
                    </span>
                </div>

                <h1
                    class="reveal delay-100 text-4xl md:text-6xl lg:text-7xl font-black tracking-tighter text-slate-900 dark:text-white leading-[0.95] text-center mb-16">
                    {{ $article->title }}
                </h1>

                <div class="reveal delay-200 relative group mb-16">
                    <div
                        class="absolute -inset-10 bg-gradient-to-tr from-emerald-500/10 to-blue-500/10 rounded-[4rem] blur-[100px] opacity-40">
                    </div>
                    <div
                        class="relative rounded-[3rem] md:rounded-[4rem] overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.2)] border border-white dark:border-slate-800">
                        <img src="{{ asset('storage/' . $article->image) }}"
                            onerror="this.src='https://images.pexels.com/photos/3662667/pexels-photo-3662667.jpeg?auto=compress&cs=tinysrgb&w=1280'"
                            alt="{{ $article->title }}"
                            class="w-full h-auto block transform hover:scale-[1.02] transition-transform duration-[1.5s]">
                    </div>
                </div>

                <div class="reveal delay-300 flex flex-col md:flex-row items-center justify-center gap-8 text-sm">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-xl">
                            <span class="material-symbols-outlined text-emerald-500">person</span>
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-black uppercase text-slate-400">Penulis</p>
                            <p class="font-bold dark:text-white">
                                {{ $article->user ? $article->user->user_name : 'Tim Ahli SI-GoChild' }}</p>
                        </div>
                    </div>
                    <div class="hidden md:block w-px h-8 bg-slate-200 dark:bg-slate-800"></div>
                    <div class="flex items-center gap-3 text-left">
                        <div
                            class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-xl">
                            <span class="material-symbols-outlined text-emerald-500">calendar_today</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-slate-400">Diterbitkan</p>
                            <p class="font-bold dark:text-white">{{ $article->created_at->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <section
            class="py-20 relative z-10 bg-white dark:bg-[#020617] transition-colors duration-1000 border-y border-slate-100 dark:border-slate-900">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">

                <div class="reveal glass-premium p-8 md:p-16 rounded-[4rem] shadow-2xl relative border border-white/20">
                    <div
                        class="absolute top-10 right-10 opacity-[0.03] dark:opacity-[0.05] pointer-events-none select-none">
                        <img src="{{ asset('images/logo.png') }}" class="w-40 h-auto" alt="watermark">
                    </div>

                    <div class="ql-content ql-editor relative z-10">
                        {!! $article->content !!}
                    </div>

                    <div
                        class="mt-24 pt-12 border-t border-slate-100 dark:border-slate-800 flex flex-col items-center gap-8">
                        <p class="text-[11px] font-black uppercase tracking-[0.5em] text-slate-400">Bagikan Wawasan Ini
                        </p>
                        <div class="flex gap-6">
                            <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
                                target="_blank"
                                class="interactive w-14 h-14 rounded-2xl bg-[#25D366] text-white flex items-center justify-center shadow-xl hover:scale-110 active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-3xl">share</span>
                            </a>
                            <button id="luxury-copy-btn"
                                class="interactive w-14 h-14 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center shadow-xl hover:scale-110 active:scale-95 transition-all">
                                <span class="material-symbols-outlined text-3xl">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-32 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start mb-16 reveal">
                    <div class="text-left">
                        <h2
                            class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.4em] mb-4">
                            Mungkin Anda Suka</h2>
                        <h3
                            class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white tracking-tighter leading-none">
                            Berita <span class="gradient-brand italic">Terkait.</span></h3>
                    </div>
                    <a href="{{ route('blogs.index') }}"
                        class="interactive mt-6 md:mt-0 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-emerald-500 transition-colors flex items-center gap-2">
                        Lihat Semua Artikel <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                @if ($relatedArticles->isEmpty())
                    <div class="reveal glass-premium p-12 rounded-[3rem] text-center border-dashed border-2">
                        <p class="text-slate-500">Tidak ada artikel terkait yang ditemukan.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach ($relatedArticles as $related)
                            <article class="reveal group h-full flex flex-col bento-luxury overflow-hidden">
                                <div class="relative h-48 overflow-hidden">
                                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                        src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->title }}">
                                    <div
                                        class="absolute inset-0 bg-emerald-600/10 opacity-0 group-hover:opacity-100 transition-opacity">
                                    </div>
                                </div>
                                <div
                                    class="p-8 flex flex-col flex-grow bg-white dark:bg-slate-900/40 transition-colors group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50">
                                    <h4
                                        class="text-xl font-bold text-slate-900 dark:text-white mb-4 leading-snug group-hover:text-emerald-500 transition-colors">
                                        <a href="{{ route('blogs.show', $related->slug) }}">{{ $related->title }}</a>
                                    </h4>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-auto">
                                        {{ $related->created_at->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="py-40 relative bg-slate-950 overflow-hidden text-center border-t border-white/5">
            <div
                class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,rgba(16,185,129,0.3),transparent)] animate-pulse">
            </div>
            <div class="max-w-4xl mx-auto px-4 relative z-10">
                <h2 class="reveal text-4xl md:text-6xl font-black text-white tracking-tighter mb-10 leading-none">
                    TETAP TERHUBUNG <br> <span class="gradient-brand italic uppercase">DENGAN UPDATE KAMI.</span>
                </h2>
                <div class="reveal delay-200">
                    <a href="https://wa.me/6285602766027" target="_blank"
                        class="interactive inline-flex items-center justify-center px-16 py-7 bg-emerald-600 text-white font-black rounded-full shadow-2xl hover:scale-110 transition-all tracking-[0.3em] text-[10px] uppercase">
                        Gabung Komunitas WhatsApp
                    </a>
                </div>
            </div>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // 1. Reading Progress
            const progress = document.getElementById('reading-progress');
            window.addEventListener('scroll', () => {
                const totalHeight = document.body.scrollHeight - window.innerHeight;
                const progressWidth = (window.pageYOffset / totalHeight) * 100;
                progress.style.width = progressWidth + '%';
            });

            // 2. Ripple Effect on Click
            document.addEventListener('mousedown', (e) => {
                const ripple = document.createElement('div');
                ripple.className = 'click-ripple';
                ripple.style.left = `${e.clientX}px`;
                ripple.style.top = `${e.clientY}px`;
                document.body.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });

            // 3. Copy Link with SweetAlert2
            const copyBtn = document.getElementById('luxury-copy-btn');
            if (copyBtn) {
                copyBtn.addEventListener('click', () => {
                    navigator.clipboard.writeText(window.location.href);

                    Swal.fire({
                        title: 'BERHASIL DISALIN',
                        html: 'Tautan artikel telah tersimpan di papan klip.<br><span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-4 block">SI-GoChild Intelligence</span>',
                        icon: 'success',
                        iconColor: '#10b981',
                        confirmButtonText: 'LANJUTKAN',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'premium-swal-popup',
                            title: 'premium-swal-title',
                            confirmButton: 'premium-swal-confirm'
                        },
                        showClass: {
                            popup: 'animate__animated animate__zoomIn'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut'
                        }
                    });
                });
            }

            // 4. Reveal Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) entry.target.classList.add('active');
                });
            }, {
                threshold: 0.1
            });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>
</x-guest-layout>
