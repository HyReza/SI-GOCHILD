<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            {{-- Breadcrumb & Title --}}
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-600 text-white text-sm font-bold shadow-lg shadow-indigo-200 dark:shadow-none">2</span>
                    {{ __('Katalog Layanan') }}
                </h2>
                <div class="flex items-center text-sm text-gray-500 mt-1 ml-11">
                    <a href="{{ route('orders.select-student') }}" class="hover:text-indigo-600 transition-colors">Pilih
                        Siswa</a>
                    <svg class="w-4 h-4 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Pilih Layanan</span>
                </div>
            </div>

            {{-- Selected Student Card (Highlight) --}}
            <div
                class="w-full md:w-auto bg-white dark:bg-gray-800 border border-indigo-100 dark:border-indigo-900 rounded-xl p-1 pr-4 shadow-sm flex items-center gap-3">
                <div class="relative">
                    @if ($student->user_photo)
                        <img src="{{ asset('storage/' . $student->user_photo) }}"
                            class="h-10 w-10 rounded-lg object-cover">
                    @else
                        <div
                            class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold">
                            {{ substr($student->student_name, 0, 1) }}
                        </div>
                    @endif
                    <div
                        class="absolute -bottom-1 -right-1 bg-green-500 h-3 w-3 rounded-full border-2 border-white dark:border-gray-800">
                    </div>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Memesan untuk</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $student->student_name }}</p>
                    <p class="text-xs text-indigo-500">{{ $student->student_number }}</p>
                </div>
                {{-- Tombol Ganti Siswa (Hidden on Mobile) --}}
                <div class="ml-auto border-l pl-3 border-gray-100 dark:border-gray-700 md:block hidden">
                    <a href="{{ route('orders.select-student') }}"
                        class="text-xs text-gray-400 hover:text-red-500 transition-colors" title="Ganti Siswa">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search Bar Section --}}
            <div class="mb-8 max-w-xl mx-auto">
                <form action="{{ route('orders.catalog', $student->id) }}" method="GET" class="relative group">
                    {{-- Input Search --}}
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama layanan atau deskripsi..."
                        class="w-full pl-11 pr-12 py-3.5 rounded-2xl border-none shadow-sm focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 transition-shadow group-hover:shadow-md">

                    {{-- Icon Kaca Pembesar (Tombol Submit) --}}
                    <button type="submit"
                        class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    {{-- Tombol Clear (Hanya muncul jika ada search) --}}
                    @if (request('search'))
                        <a href="{{ route('orders.catalog', $student->id) }}"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 transition-colors cursor-pointer"
                            title="Hapus pencarian">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            {{-- Grid Layanan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($services as $service)
                    <div
                        class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:-translate-y-1 flex flex-col h-full overflow-hidden">

                        {{-- Image Section --}}
                        <div class="relative aspect-video overflow-hidden bg-gray-100 dark:bg-gray-700">
                            @if ($service->image_url)
                                <img src="{{ asset('storage/' . $service->image_url) }}" alt="{{ $service->name }}"
                                    class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div
                                    class="w-full h-full flex flex-col items-center justify-center text-gray-300 dark:text-gray-600 bg-gray-50 dark:bg-gray-700">
                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs font-medium">No Image</span>
                                </div>
                            @endif

                            {{-- Badge "Tersedia" (Opsional, pengganti kategori) --}}
                            <div class="absolute top-3 left-3">
                                <span
                                    class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm text-[10px] font-bold px-2 py-0.5 rounded-md text-gray-600 dark:text-gray-300 shadow-sm border border-gray-200 dark:border-gray-600 uppercase tracking-wide">
                                    Layanan
                                </span>
                            </div>
                        </div>

                        {{-- Content Body --}}
                        <div class="p-5 flex-1 flex flex-col">
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2 group-hover:text-indigo-600 transition-colors">
                                {{ $service->name }}
                            </h3>

                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 mb-4 flex-1">
                                {{ $service->description ?? 'Layanan tambahan untuk menunjang aktivitas siswa.' }}
                            </p>

                            {{-- Divider --}}
                            <div class="border-t border-dashed border-gray-200 dark:border-gray-700 my-3"></div>

                            {{-- Footer: Price & Action --}}
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] uppercase text-gray-400 font-semibold tracking-wide">Harga
                                        Satuan</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <a href="{{ route('orders.checkout', ['student' => $student->id, 'service' => $service->id]) }}"
                                    class="relative inline-flex items-center justify-center p-0.5 mb-2 mr-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-indigo-500 to-purple-500 group-hover:from-indigo-600 group-hover:to-purple-600 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-indigo-200 dark:focus:ring-indigo-800">
                                    <span
                                        class="relative px-4 py-2 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0 text-indigo-600 dark:text-indigo-400 group-hover:text-white font-bold flex items-center gap-2">
                                        Pesan
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-16 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <div
                            class="w-24 h-24 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            @if (request('search'))
                                Layanan tidak ditemukan
                            @else
                                Belum ada layanan
                            @endif
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1 max-w-sm text-center">
                            @if (request('search'))
                                Tidak ada layanan yang cocok dengan kata kunci "{{ request('search') }}".
                            @else
                                Admin belum menambahkan data layanan tambahan ke dalam sistem.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
