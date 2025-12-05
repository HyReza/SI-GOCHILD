<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Katalog Layanan') }}
            </h2>

            {{-- Tombol Riwayat untuk Siswa --}}
            @if (auth()->user()->hasRole('student'))
                <a href="{{ route('service-orders.history') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Pesanan Saya
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero / Intro Banner (Opsional) --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-8 mb-8 text-white shadow-lg">
                <h1 class="text-3xl font-bold mb-2">Layanan Ekstrakurikuler & Tambahan</h1>
                <p class="text-indigo-100">Pilih layanan terbaik untuk menunjang tumbuh kembang buah hati Anda.</p>
            </div>

            {{-- Grid Layanan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services as $service)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                        {{-- Gambar --}}
                        <div class="h-48 w-full bg-gray-200 relative overflow-hidden group">
                            @if ($service->image_url)
                                <img src="{{ asset('storage/' . $service->image_url) }}" alt="{{ $service->name }}"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div
                                    class="w-full h-full flex items-center justify-center bg-indigo-50 dark:bg-gray-700 text-indigo-200 dark:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4">
                                <span
                                    class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm text-indigo-600 dark:text-indigo-400 px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                                    {{ $service->category ?? 'Umum' }}
                                </span>
                            </div>
                        </div>

                        {{-- Konten --}}
                        <div class="p-6 flex-grow flex flex-col">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $service->name }}</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-3 flex-grow">
                                {{ $service->description ?: 'Tidak ada deskripsi tersedia.' }}
                            </p>

                            <div
                                class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Harga Mulai</p>
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                        Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <a href="{{ route('service-orders.create', ['service_id' => $service->id]) }}"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                    Pesan
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">Belum ada layanan yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
