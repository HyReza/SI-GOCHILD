<x-guest-layout>
    @slot('seo_key', 'login si-gochild, masuk sistem, sistem informasi tumbuh kembang')
    @slot('seo_description', 'Portal Otentikasi SI-GoChild Excellence Edition.')
    @slot('seo_meta_title', 'Otentikasi - SI-GoChild')
    @slot('seo_title', 'Otentikasi Sistem | SI-GoChild')

    <div
        class="relative min-h-screen w-full flex items-center justify-center pt-28 pb-12 px-6 overflow-hidden font-sans selection:bg-emerald-500/30">

        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/blogs1.jpg') }}" class="w-full h-full object-cover" alt="System Backdrop">

            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/60 to-slate-950/80"></div>

            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.1),transparent_50%)]">
            </div>
        </div>

        <div class="relative z-10 w-full max-w-6xl grid lg:grid-cols-12 gap-12 items-center">

            <div class="lg:col-span-7 hidden lg:block space-y-10">
                <div class="space-y-4">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 backdrop-blur-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-400">Expert
                            System</span>
                    </div>
                    <h1 class="text-8xl font-black text-white leading-none tracking-tighter drop-shadow-2xl">
                        SI-GO<span class="text-emerald-500">CHILD</span>
                    </h1>
                    <p class="text-xl text-slate-300 font-light max-w-lg leading-relaxed italic">
                        "Presisi dalam deteksi, cerdas dalam memberikan solusi tumbuh kembang anak."
                    </p>
                </div>

                <div class="flex gap-6 pt-4">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Method</p>
                        <p class="text-sm font-bold text-white uppercase tracking-tighter">MMDST</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1">Method</p>
                        <p class="text-sm font-bold text-white uppercase tracking-tighter">Antropometri</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 w-full max-w-md mx-auto" x-data="{ loading: false }">
                <div
                    class="bg-slate-900/40 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-10 lg:p-12 shadow-[0_20px_50px_rgba(0,0,0,0.5)]">

                    <div class="mb-10">
                        <h2 class="text-3xl font-black text-white tracking-tight">Login</h2>
                        <p class="text-slate-400 mt-2 text-sm font-medium">Selamat datang kembali, silakan login.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-6" @submit="loading = true">
                        @csrf

                        <div class="space-y-2 group">
                            <label
                                class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 group-focus-within:text-emerald-500 transition-colors">Email
                                / Nomor Induk Sekolah</label>
                            <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                                class="w-full bg-white/5 border border-white/10 focus:border-emerald-500/50 py-4 px-6 rounded-2xl text-white transition-all outline-none placeholder:text-slate-700 font-medium"
                                placeholder="Email atau NIS">
                            <x-input-error :messages="$errors->get('identifier')" class="mt-1 text-xs text-red-400" />
                        </div>

                        <div class="space-y-2 group" x-data="{ show: false }">
                            <label
                                class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 group-focus-within:text-emerald-400 transition-colors">Kata
                                Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required
                                    class="w-full bg-white/5 border border-white/10 focus:border-emerald-500/50 py-4 px-6 rounded-2xl text-white transition-all outline-none placeholder:text-slate-700 font-medium tracking-[0.2em]"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show"
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-emerald-500 transition-colors">
                                    <span class="material-symbols-outlined text-xl"
                                        x-text="show ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-lg shadow-emerald-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-70 group">

                            <div x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span class="text-xs tracking-[0.2em]">MEMPROSES...</span>
                            </div>

                            <div x-show="!loading" class="flex items-center gap-2">
                                <span class="text-xs tracking-[0.2em]">MASUK SEKARANG</span>
                                <span
                                    class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_right_alt</span>
                            </div>
                        </button>
                    </form>
                </div>

                <div class="mt-10 text-center">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.4em]">
                        Developed by <span class="text-slate-300">Reza Edi Saputra</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
