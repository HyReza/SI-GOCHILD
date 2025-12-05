<x-app-layout>
    <x-slot:title>Management Articles</x-slot:title>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <div class="grid grid-cols-12 gap-6">
        <!-- Button to Add New Article -->
        <div class="col-span-12 lg:col-span-4">
            <a href="{{ route('articles.create') }}">
                <x-primary-button class="lg:w-auto px-2 py-3 text-xs font-semibold">
                    Tambah Artikel Baru <span class="material-symbols-outlined text-xs font-normal">add</span>
                </x-primary-button>
            </a>
        </div>

        <!-- Search Form -->
        <div class="col-span-12 lg:col-span-8">
            <form method="GET" action="{{ route('articles.index') }}" class="flex items-center mt-4 lg:mt-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Artikel..."
                    class="h-10 w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                    class="ml-2 p-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-200">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="mt-6 p-8 bg-white dark:bg-gray-900 rounded-lg shadow-md">
        @if (request('search'))
            <div class="mb-4 text-gray-600 dark:text-gray-300">
                Menampilkan hasil pencarian untuk "<strong>{{ request('search') }}</strong>"
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Judul</th>
                        <th class="py-3 px-6 text-left">Kategori</th>
                        <th class="py-3 px-6 text-left">Gambar</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-200 text-sm font-light">
                    @forelse ($articles as $article)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 transition">
                            <td class="py-3 px-6">{{ $articles->firstItem() + $loop->iteration - 1 }}</td>
                            <td class="py-3 px-6">{{ $article->title }}</td>
                            <td class="py-3 px-6">{{ $article->category->category_name }}</td>
                            <td class="py-3 px-6">
                                <img src="{{ asset('storage/' . $article->image) }}" alt="Article Image"
                                    class="w-16 h-16 object-cover rounded-md">
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex gap-2 justify-center">
                                    <!-- View -->
                                    <a href="{{ route('articles.show', $article->id) }}" class="relative group">
                                        <span
                                            class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                            visibility
                                        </span>
                                        <span
                                            class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Lihat Detail
                                        </span>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('articles.edit', $article->id) }}" class="relative group">
                                        <span
                                            class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                            edit_square
                                        </span>
                                        <span
                                            class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Edit Data
                                        </span>
                                    </a>

                                    <!-- Delete -->
                                    <form id="delete-form-{{ $article->id }}"
                                        action="{{ route('articles.destroy', $article->id) }}" method="POST"
                                        class="relative group delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $article->id }})"
                                            class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">
                                            delete
                                        </button>
                                        <span
                                            class="absolute z-50 right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Hapus Data
                                        </span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada artikel yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $articles->links() }}
        </div>
    </div>

    <script>
        function confirmDelete(articleId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Artikel ini akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Artikel...',
                        text: 'Artikel Anda sedang dihapus.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('delete-form-' + articleId).submit();
                }
            });
        }
    </script>
</x-app-layout>
