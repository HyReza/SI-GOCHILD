<x-app-layout>
    {{-- SweetAlert Messages --}}
    @if (session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{{ session('success') ? 'success' : 'error' }}',
                    title: '{{ session('success') ? 'Berhasil!' : 'Terjadi Kesalahan!' }}',
                    text: "{{ session('success') ?? session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <div class="container mx-auto p-2 md:p-4">
        <!-- Button to create a new gallery -->
        <button>
            <a href="{{ route('gallery-activity.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 focus:bg-gray-700 dark:focus:bg-gray-500 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Tambah Galeri
            </a>
        </button>

        <!-- Table to Display Gallery List -->
        <div
            class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse mb-4">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-white text-sm leading-normal">
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">Judul Galeri</th>
                            <th class="py-3 px-6 text-left">Deskripsi Galeri</th>
                            <th class="py-3 px-6 text-left">Tanggal Galeri</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-200 text-sm font-light">
                        @forelse($galleries as $index => $gallery)
                            <tr
                                class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                                <td class="py-3 px-6 text-left">{{ $index + 1 }}</td>
                                <td class="py-3 px-6 text-left">{{ $gallery->gallery_title }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Illuminate\Support\Str::limit($gallery->gallery_description, 30) }}</td>
                                <td class="py-3 px-6 text-left">{{ $gallery->gallery_date }}</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('gallery-activity.show', $gallery->id) }}"
                                            class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">visibility</span>
                                            <span
                                                class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Lihat
                                                Detail</span>
                                        </a>
                                        <a href="{{ route('gallery-activity.edit', $gallery->id) }}"
                                            class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">edit_square</span>
                                            <span
                                                class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Edit
                                                Data</span>
                                        </a>
                                        <form id="delete-form-{{ $gallery->id }}"
                                            action="{{ route('gallery-activity.destroy', $gallery->id) }}"
                                            method="POST" class="relative group">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $gallery->id }})"
                                                class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">delete</button>
                                            <span
                                                class="absolute z-50 right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Hapus
                                                Data</span>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">Tidak
                                    ada galeri ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $galleries->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Galeri?',
                text: "Apakah Anda yakin ingin menghapus galeri ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        document.getElementById(`delete-form-${id}`).submit();
                    }, 1000);
                }
            });
        }
    </script>
</x-app-layout>
