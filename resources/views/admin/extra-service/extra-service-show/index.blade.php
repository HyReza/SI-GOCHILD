<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Layanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 relative">

                {{-- Tombol Kembali Absolute --}}
                <div class="absolute top-6 left-6 z-10">
                    <a href="{{ route('extra-services.index') }}"
                        class="inline-flex items-center px-3 py-1.5 bg-white/80 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition dark:bg-gray-700/80 dark:border-gray-600 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-5 min-h-[400px]">

                    {{-- Kolom Gambar (Kiri/Atas) --}}
                    <div
                        class="md:col-span-2 bg-gray-50 dark:bg-gray-700/30 relative flex items-center justify-center p-8 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700">
                        <div
                            class="relative w-full aspect-square max-w-[200px] md:max-w-full rounded-2xl overflow-hidden shadow-lg ring-4 ring-white dark:ring-gray-800">
                            @if ($extraService->image_url)
                                <img src="{{ asset('storage/' . $extraService->image_url) }}"
                                    alt="{{ $extraService->name }}" class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-20 w-20 text-blue-300 dark:text-blue-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Badge Status Floating --}}
                        <div class="absolute top-6 right-6 md:right-auto md:left-6">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold shadow-sm {{ $extraService->is_active ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                {{ $extraService->is_active ? 'Status: Aktif' : 'Status: Non-Aktif' }}
                            </span>
                        </div>
                    </div>

                    {{-- Kolom Informasi (Kanan/Bawah) --}}
                    <div class="md:col-span-3 p-8 md:p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-end mb-4 md:mb-0">
                                <span class="text-xs font-mono text-gray-400 uppercase tracking-widest">ID:
                                    #{{ str_pad($extraService->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">
                                {{ $extraService->name }}</h1>

                            <div class="flex items-center mb-6">
                                <span class="text-3xl font-bold text-blue-600 dark:text-blue-400 mr-2">Rp
                                    {{ number_format($extraService->base_price, 0, ',', '.') }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">/ unit</span>
                            </div>

                            <div class="prose prose-sm dark:prose-invert mb-8 text-gray-600 dark:text-gray-300">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Deskripsi
                                    Layanan</h4>
                                <p class="leading-relaxed">
                                    {{ $extraService->description ?: 'Belum ada deskripsi untuk layanan ini.' }}</p>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div
                                    class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">
                                        {{ $extraService->creator->name ?? 'Sistem' }}</p>
                                </div>
                                <div class="w-px h-8 bg-gray-200 dark:bg-gray-600 mx-2"></div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Buat</p>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">
                                        {{ $extraService->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                            <a href="{{ route('extra-services.edit', $extraService) }}"
                                class="inline-flex items-center px-6 py-3 bg-amber-500 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-amber-600 shadow-lg shadow-amber-500/30 transition transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Data
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
