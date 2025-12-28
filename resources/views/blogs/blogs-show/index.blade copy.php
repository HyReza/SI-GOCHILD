<x-guest-layout>
    @slot('seo_key', $article->seo_key ?? 'detail artikel, si-gochild, kesehatan anak')
    @slot('seo_description', Str::limit(strip_tags($article->content), 160))
    @slot('seo_meta_title', $article->title)
    @slot('seo_title', $article->title . ' | SI-GoChild Blog')

    @push('styles')
    <style>
        html {
            cursor: none !important;
        }

        /* --- PREMIUM QUILL CONTENT STYLING --- */
        .ql-content {
            @apply text-slate-700 dark:text-slate-300 leading-[1.8] text-lg;
        }

        .ql-content h1,
        .ql-content h2,
        .ql-content h3 {
            @apply font-heading font-black text-slate-900 dark:text-white mt-12 mb-6 tracking-tighter;
        }

        .ql-content h1 {
            @apply text-4xl;
        }

        .ql-content h2 {
            @apply text-3xl;
        }

        .ql-content h3 {
            @apply text-2xl;
        }

        .ql-content p {
            @apply mb-6 text-justify;
        }

        .ql-content blockquote {
            @apply pl-6 border-l-4 border-emerald-500 italic text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 py-4 pr-4 rounded-r-2xl my-8;
        }

        .ql-content ul {
            @apply list-disc pl-6 mb-6 space-y-2;
        }

        .ql-content ol {
            @apply list-decimal pl-6 mb-6 space-y-2;
        }

        .ql-content li {
            @apply pl-2;
        }

        .ql-content img {
            @apply rounded-[2rem] shadow-2xl my-10 mx-auto border border-slate-100 dark:border-slate-800;
        }

        .ql-content a {
            @apply text-emerald-600 dark:text-emerald-400 font-bold underline decoration-emerald-500/30 underline-offset-4 hover:text-emerald-500 transition-colors;
        }

        /* Reading Progress Bar */
        #reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(to right, #10b981, #3b82f6);
            z-index: 1000001;
            transition: width 0.1s ease;
        }
    </style>
    @endpush

    <div id="reading-progress"></div>

    <div class="relative min-h-screen">

        <section class="relative pt-40 pb-20 overflow-hidden">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="reveal flex flex-wrap items-center justify-center gap-4 mb-10">
                    <a href="{{ route('blogs.index') }}"
                        class="interactive text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-emerald-500 transition-colors">Digital
                        Library</a>
                    <span class="text-slate-300">/</span>
                    <span
                        class="px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                        {{ $article->category->category_name }}
                    </span>
                </div>

                <div class="text-center space-y-8 mb-16">
                    <h1
                        class="reveal delay-100 text-4xl md:text-6xl lg:text-7xl font-black tracking-tighter text-slate-900 dark:text-white leading-[0.95]">
                        {{ $article->title }}
                    </h1>

                    <div class="reveal delay-200 flex flex-col md:flex-row items-center justify-center gap-6 text-sm">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center overflow-hidden border-2 border-emerald-500/20">
                                <span class="material-symbols-outlined text-slate-400">person</span>
                            </div>
                            <div class="text-left">
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Penulis</p>
                                <p class="font-bold text-slate-900 dark:text-white">
                                    {{ $article->user ? $article->user->user_name : 'Tim Ahli SI-GoChild' }}
                                </p>
                            </div>
                        </div>
                        <div class="hidden md:block w-px h-8 bg-slate-200 dark:bg-slate-800"></div>
                        <div class="flex items-center gap-3 text-left">
                            <span class="material-symbols-outlined text-emerald-500">calendar_month</span>
                            <div>
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Diterbitkan
                                </p>
                                <p class="font-bold text-slate-900 dark:text-white">
                                    {{ $article->created_at->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reveal delay-300 relative group">
                    <div
                        class="absolute -inset-4 bg-gradient-to-tr from-emerald-500/20 to-blue-500/20 rounded-[3.5rem] blur-3xl opacity-50 group-hover:opacity-80 transition-opacity duration-1000">
                    </div>
                    <div
                        class="relative rounded-[3rem] overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.2)] border border-white/20">
                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}"
                            class="w-full h-[300px] md:h-[500px] lg:h-[600px] object-cover transform group-hover:scale-105 transition-transform duration-1000">
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 relative z-10 bg-white dark:bg-[#020617] transition-colors duration-1000">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">

                <div class="reveal glass-premium p-8 md:p-16 rounded-[3.5rem] shadow-2xl relative">
                    <div
                        class="absolute top-10 right-10 opacity-[0.03] dark:opacity-[0.05] pointer-events-none select-none">
                        <img src="{{ asset('images/logo.png') }}" class="w-40 h-auto" alt="watermark">
                    </div>

                    <div class="ql-content relative z-10">
                        {!! $article->content !!}
                    </div>

                    <div
                        class="mt-20 pt-10 border-t border-slate-100 dark:border-slate-800 flex flex-col items-center gap-6">
                        <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-400">Bagikan Artikel Ini
                        </p>
                        <div class="flex gap-4">
                            <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
                                target="_blank"
                                class="interactive w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                                <span class="material-symbols-outlined">share</span>
                            </a>
                            <button
                                onclick="navigator.clipboard.writeText(window.location.href); alert('Link berhasil disalin!')"
                                class="interactive w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-emerald-500 transition-all border border-slate-100 dark:border-slate-700 shadow-sm">
                                <span class="material-symbols-outlined">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-32 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 reveal">
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
            // 1. Reading Progress Logic
            const progress = document.getElementById('reading-progress');
            window.addEventListener('scroll', () => {
                const totalHeight = document.body.scrollHeight - window.innerHeight;
                const progressWidth = (window.pageYOffset / totalHeight) * 100;
                progress.style.width = progressWidth + '%';
            });

            // 2. Reveal Observer
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
