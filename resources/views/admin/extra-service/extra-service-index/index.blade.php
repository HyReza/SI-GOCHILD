<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-blue-600 dark:text-blue-400">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h4.018a1.5 1.5 0 001.538-1.17l1.38-5.206a4.125 4.125 0 00-8.063-2.263A4.125 4.125 0 005.463 10.2a1.5 1.5 0 00-1.538 1.17l-1.38 5.206a1.5 1.5 0 001.538 1.924H18z" />
                </svg>
                {{ __('Katalog Layanan') }}
            </h2>
            {{-- Tombol Tambah Layanan di Header (Opsional, bisa dihapus jika ingin hanya satu tombol di konten utama) --}}
            {{-- <a href="{{ route('extra-services.create') }}" ... > ... </a> --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700">

                {{-- Header Tabel & Tombol Aksi Utama --}}
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Menampilkan daftar layanan tambahan yang tersedia untuk siswa.
                    </div>

                    {{-- Tombol Tambah Layanan Utama --}}
                    <a href="{{ route('extra-services.create') }}"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Layanan Baru
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider w-16 text-center">No</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Layanan</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Author</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($extraServices as $index => $service)
                                <tr
                                    class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <td class="px-6 py-4 text-center text-gray-400">
                                        {{ $extraServices->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-12 w-12 flex-shrink-0 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm group">
                                                @if ($service->image_url)
                                                    <img src="{{ asset('storage/' . $service->image_url) }}"
                                                        alt="{{ $service->name }}"
                                                        class="h-full w-full object-cover transform group-hover:scale-110 transition duration-300">
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ $service->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
                                                    {{ Str::limit($service->description, 50) ?: 'Tidak ada deskripsi' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                            Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $service->is_active ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800' : 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $service->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                            {{ $service->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $service->creator->name ?? 'Admin' }}</span>
                                            <span
                                                class="text-[10px] text-gray-400">{{ $service->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Tombol SHOW --}}
                                            <a href="{{ route('extra-services.show', $service) }}"
                                                class="group relative p-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span
                                                    class="absolute bottom-full mb-2 hidden group-hover:block w-auto p-2 min-w-max rounded-md shadow-md text-white bg-gray-900 text-xs font-bold transition-all duration-100 transform -translate-x-1/2 left-1/2 z-10">
                                                    Detail
                                                </span>
                                            </a>

                                            {{-- Tombol EDIT --}}
                                            <a href="{{ route('extra-services.edit', $service) }}"
                                                class="group relative p-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                <span
                                                    class="absolute bottom-full mb-2 hidden group-hover:block w-auto p-2 min-w-max rounded-md shadow-md text-white bg-gray-900 text-xs font-bold transition-all duration-100 transform -translate-x-1/2 left-1/2 z-10">
                                                    Edit
                                                </span>
                                            </a>

                                            {{-- Tombol DELETE --}}
                                            <button
                                                onclick="confirmDelete('{{ route('extra-services.destroy', $service) }}')"
                                                class="group relative p-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span
                                                    class="absolute bottom-full mb-2 hidden group-hover:block w-auto p-2 min-w-max rounded-md shadow-md text-white bg-gray-900 text-xs font-bold transition-all duration-100 transform -translate-x-1/2 left-1/2 z-10">
                                                    Hapus
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-4 mb-4">
                                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-300" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada
                                                Layanan</h3>
                                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mt-1 mb-6">
                                                Mulai buat katalog layanan tambahan Anda sekarang.
                                            </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/30">
                    {{ $extraServices->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Form Hapus Tersembunyi --}}
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- SweetAlert Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}"
            });
        @endif

        function confirmDelete(url) {
            Swal.fire({
                title: 'Hapus Layanan?',
                text: "Layanan yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Tailwind red-500
                cancelButtonColor: '#6b7280', // Tailwind gray-500
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        didOpen: () => Swal.showLoading(),
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' :
                            '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937',
                        showConfirmButton: false
                    });
                    let form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
