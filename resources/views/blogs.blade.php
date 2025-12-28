<x-guest-layout>
    @slot('seo_key', $seo_key)
    @slot('seo_description', $seo_description)
    @slot('seo_meta_title', $seo_meta_title)
    @slot('seo_title', $seo_title)

    {{-- Lottie Player for Empty State --}}
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    @push('styles')
        <style>
            /* Styling khusus untuk konten hasil editor Quill agar tetap rapi di dark mode */
            .article-excerpt ol {
                list-style-type: decimal;
                padding-left: 1.2rem;
            }

            .article-excerpt ul {
                list-style-type: disc;
                padding-left: 1.2rem;
            }

            /* Glassmorphism for Inputs */
            .input-glass {
                @apply bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl transition-all duration-300;
            }

            .input-glass:focus {
                @apply border-emerald-500 ring-4 ring-emerald-500/10 outline-none;
            }

            /* Hover Zoom Effect */
            .img-hover-zoom {
                @apply transition-transform duration-700 ease-in-out;
            }

            .group:hover .img-hover-zoom {
                @apply scale-110;
            }
        </style>
    @endpush

    <div class="relative min-h-screen">

        <section class="relative pt-40 pb-16 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <div class="reveal space-y-6">
                    <div
                        class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg mx-auto">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span
                            class="text-[10px] font-black tracking-[0.4em] uppercase text-emerald-600 dark:text-emerald-400 font-heading">Digital
                            Library</span>
                    </div>

                    <h1
                        class="text-5xl md:text-7xl font-black tracking-tighter text-slate-900 dark:text-white leading-none">
                        Wawasan <br> <span class="gradient-brand italic">SI-GoChild.</span>
                    </h1>

                    <p
                        class="text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed font-medium">
                        Temukan informasi terkini seputar kesehatan, pola asuh, dan perkembangan anak dari para ahli.
                    </p>
                </div>
            </div>
        </section>

        <section class="py-12 relative z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="reveal">
                    <div
                        class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-slate-200/50 dark:border-slate-800/50 p-2 md:p-3 rounded-[2rem] md:rounded-full shadow-2xl shadow-slate-200/50 dark:shadow-none">

                        <form method="GET" action="{{ route('blogs.index') }}"
                            class="flex flex-col md:flex-row items-stretch md:items-center gap-2">

                            <div class="flex-grow relative group">
                                <div
                                    class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                    <span class="material-symbols-outlined text-xl">search</span>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari judul artikel kesehatan..."
                                    class="w-full bg-white dark:bg-slate-800 border-none focus:ring-2 focus:ring-emerald-500/20 py-4 pl-14 pr-6 rounded-3xl md:rounded-full text-sm font-bold text-slate-700 dark:text-slate-200 placeholder:text-slate-400 transition-all outline-none shadow-sm">
                            </div>

                            <div class="relative min-w-[220px] group" x-data="{
                                open: false,
                                selectedName: '{{ $categories->firstWhere('id', request('category'))->category_name ?? 'Semua Kategori' }}',
                                selectedValue: '{{ request('category') }}'
                            }"
                                @click.away="open = false">

                                <input type="hidden" name="category" :value="selectedValue">

                                <button type="button" @click="open = !open"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none focus:ring-2 focus:ring-emerald-500/20 py-4 pl-14 pr-12 rounded-3xl md:rounded-full text-sm font-bold text-slate-700 dark:text-slate-200 text-left transition-all shadow-sm hover:bg-white dark:hover:bg-slate-700 relative">

                                    <div
                                        class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-slate-400 group-hover:text-emerald-500 transition-colors">
                                        <span class="material-symbols-outlined text-xl">category</span>
                                    </div>

                                    <span class="truncate" x-text="selectedName"></span>

                                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400 transition-transform duration-300"
                                        :class="open ? 'rotate-180 text-emerald-500' : ''">
                                        <span class="material-symbols-outlined text-lg">expand_more</span>
                                    </div>
                                </button>

                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                    class="absolute z-[60] mt-3 w-full bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-[2rem] shadow-2xl overflow-hidden backdrop-blur-xl"
                                    style="display: none;">

                                    <div class="p-2 max-h-64 overflow-y-auto custom-scrollbar">
                                        <div @click="selectedValue = ''; selectedName = 'Semua Kategori'; open = false"
                                            class="flex items-center gap-3 px-5 py-3 rounded-2xl cursor-pointer transition-colors"
                                            :class="selectedValue === '' ?
                                                'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' :
                                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                            <span class="text-xs font-black uppercase tracking-widest">Semua
                                                Kategori</span>
                                        </div>

                                        @foreach ($categories as $category)
                                            <div @click="selectedValue = '{{ $category->id }}'; selectedName = '{{ $category->category_name }}'; open = false"
                                                class="flex items-center justify-between px-5 py-3 rounded-2xl cursor-pointer transition-all group/item"
                                                :class="selectedValue == '{{ $category->id }}' ?
                                                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' :
                                                    'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'">

                                                <span class="text-sm font-bold">{{ $category->category_name }}</span>

                                                <span class="material-symbols-outlined text-sm"
                                                    x-show="selectedValue == '{{ $category->id }}'">check_circle</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-3xl md:rounded-full shadow-lg shadow-emerald-600/20 hover:scale-[1.03] active:scale-95 transition-all flex items-center justify-center gap-3 group">
                                <span class="text-[11px] uppercase tracking-[0.2em]">Terapkan</span>
                                <span
                                    class="material-symbols-outlined text-sm transition-transform group-hover:rotate-12">filter_list</span>
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @forelse ($articles as $article)
                        <article class="reveal group h-full flex flex-col bento-luxury overflow-hidden">
                            <div class="relative h-64 overflow-hidden bg-slate-200 dark:bg-slate-800">
                                <img class="img-hover-zoom w-full h-full object-cover"
                                    src="{{ asset('storage/' . $article->image) }}"
                                    onerror="this.src='https://images.pexels.com/photos/3662667/pexels-photo-3662667.jpeg?auto=compress&cs=tinysrgb&w=800'"
                                    alt="{{ $article->title }}">

                                <div class="absolute top-4 left-4">
                                    <span
                                        class="px-4 py-1.5 rounded-full bg-white/90 dark:bg-slate-950/90 backdrop-blur text-[10px] font-black uppercase tracking-widest text-emerald-600 shadow-lg border border-white/20">
                                        {{ $article->category->category_name ?? 'Info' }}
                                    </span>
                                </div>

                                <div
                                    class="absolute inset-0 bg-emerald-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                </div>
                            </div>

                            <div class="p-8 flex flex-col flex-grow bg-white dark:bg-slate-900/50">
                                <div
                                    class="flex items-center gap-3 text-slate-400 mb-4 text-[10px] font-bold uppercase tracking-widest">
                                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                                    {{ $article->created_at->translatedFormat('d M Y') }}
                                </div>

                                <h2
                                    class="text-2xl font-bold text-slate-900 dark:text-white mb-4 leading-snug group-hover:text-emerald-500 transition-colors duration-300">
                                    <a href="{{ route('blogs.show', $article->slug) }}">{{ $article->title }}</a>
                                </h2>

                                <div
                                    class="article-excerpt text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-8 line-clamp-3">
                                    {!! Str::limit(
                                        preg_replace(
                                            ['/<img[^>]+>/i', '/<ul[^>]*>.*?<\/ul>/is', '/<li[^>]*>.*?<\/li>/is', '/<ol[^>]*>.*?<\/ol>/is'],
                                            '',
                                            $article->content,
                                        ),
                                        180,
                                    ) !!}
                                </div>

                                <div class="mt-auto pt-6 border-t border-slate-100 dark:border-slate-800">
                                    <a href="{{ route('blogs.show', $article->slug) }}"
                                        class="interactive inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 group/link">
                                        Selengkapnya
                                        <span
                                            class="material-symbols-outlined text-sm transition-transform group-hover/link:translate-x-2">arrow_forward_ios</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div
                            class="col-span-1 md:col-span-3 py-20 flex flex-col items-center justify-center text-center reveal">
                            <div class="w-64 h-64 mb-8">
                                <dotlottie-player
                                    src="https://lottie.host/6a4c8f09-46fc-4762-b14a-d8a558d68c9e/ZNeRo4Cuud.lottie"
                                    background="transparent" speed="1" loop autoplay>
                                </dotlottie-player>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Belum Ada Artikel</h3>
                            <p class="text-slate-500 dark:text-slate-400 max-w-sm">Maaf, kami tidak menemukan artikel
                                yang sesuai dengan pencarian Anda.</p>
                            <a href="{{ route('blogs.index') }}"
                                class="interactive mt-8 px-8 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-2xl text-xs tracking-widest uppercase">Lihat
                                Semua Artikel</a>
                        </div>
                    @endforelse
                </div>

                <div class="mt-24 flex justify-center reveal">
                    <div
                        class="glass-premium px-6 py-4 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-800">
                        {{ $articles->links() }}
                    </div>
                </div>
            </div>
        </section>

        <section class="py-32 relative bg-slate-950 overflow-hidden text-center">
            <div
                class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,rgba(16,185,129,0.3),transparent)] animate-pulse">
            </div>
            <div class="max-w-4xl mx-auto px-4 relative z-10">
                <h2 class="reveal text-3xl md:text-5xl font-black text-white tracking-tighter mb-8 leading-tight">
                    Dapatkan Update Kesehatan <br> <span class="gradient-brand italic">Langsung ke WhatsApp.</span>
                </h2>
                <div class="reveal delay-200">
                    <a href="https://wa.me/6285602766027" target="_blank"
                        class="interactive inline-flex items-center justify-center px-12 py-5 bg-white text-slate-900 font-black rounded-full shadow-2xl hover:scale-110 transition-all tracking-[0.2em] text-[10px] uppercase">
                        Hubungi Admin Official
                    </a>
                </div>
            </div>
        </section>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px"
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        // Menambahkan sedikit delay untuk efek staggered grid
                        setTimeout(() => {
                            entry.target.classList.add('active');
                        }, index * 100);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>
</x-guest-layout>
