<x-app-layout>
    <x-slot:title>Management Categories</x-slot:title>
    {{-- SweetAlert for Success --}}
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

    {{-- SweetAlert for Error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ $errors->first() }}", // Show first error
                    showConfirmButton: true
                });
            });
        </script>
    @endif

    {{-- Search Form --}}
    <div class="mb-4 flex justify-between items-center">
        <button onclick="openModal()"
            class="flex justify-center items-center text-center font-semibold bg-green-500 dark:bg-green-600 h-10 w-36 rounded-lg shadow-md hover:shadow-none hover:bg-green-600 dark:hover:bg-green-700 text-white">
            <h1 class="flex gap-4 text-xs">Tambah Kategori
                <span class="flex material-symbols-outlined text-center text-xs">add</span>
            </h1>
        </button>
        <form method="GET" action="{{ route('categories.index') }}" class="flex gap-4 items-center w-full md:w-1/2">
            <input type="text" name="search" placeholder="Search categories..."
                class="h-10 w-full border bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500 dark:text-white"
                value="{{ request()->get('search') }}">
            <button type="submit" class="bg-blue-500 text-white rounded-lg px-4 py-2">Search</button>
        </form>
    </div>

    {{-- Categories Table --}}
    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse mb-4">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-500 text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Nama Kategori</th>
                        <th class="py-3 px-6 text-left">Deskripsi Kategori</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @forelse ($categories as $category)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="py-3 px-6 text-left">{{ $category->category_name }}</td>
                            <td class="py-3 px-6 text-left">
                                {{ \Illuminate\Support\Str::limit($category->category_description, 30) }}</td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex gap-2 justify-center">
                                    <button onclick="viewCategory({{ $category->id }})" class="relative group">
                                        <span
                                            class="material-symbols-outlined bg-indigo-400 px-2 py-1 rounded-md text-white text-base font-extralight">visibility</span>
                                        <span
                                            class="z-50 absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Lihat Detail
                                        </span>
                                    </button>
                                    <button onclick="editCategory({{ $category->id }})" class="relative group">
                                        <span
                                            class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">edit_square</span>
                                        <span
                                            class="z-50 absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Edit
                                            Data</span>
                                    </button>
                                    <form id="delete-form-{{ $category->id }}"
                                        action="{{ route('categories.destroy', $category) }}" method="POST"
                                        class="relative group delete-form"
                                        data-category-name="{{ $category->category_name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(event, {{ $category->id }})"
                                            class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">
                                            delete
                                        </button>
                                        <span
                                            class="z-50 absolute right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Hapus Data
                                        </span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">Tidak ada
                                category ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $categories->links('pagination::tailwind') }}
        </div>
    </div>

    <div id="category-modal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50"
        style="display: none;">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg max-w-lg w-full p-8 relative m-4">
            <button onclick="closeModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>
            <h2 id="modal-title" class="text-lg font-semibold mb-4">Tambah Kategori Baru</h2>
            <form id="category-form" method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div id="method-field" class="mb-4"></div>
                <div class="mb-4">
                    <x-input-label for="category_name" :value="__('Nama Kategori')" />
                    <x-text-input id="category_name" class="block mt-1 w-full" type="text" name="category_name"
                        required autofocus value="{{ old('category_name') }}" />
                    @error('category_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <x-input-label for="category_description" :value="__('Deskripsi Kategori')" />
                    <textarea id="category_description" name="category_description"
                        class="block mt-1 w-full p-2 border dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md" rows="4"
                        required>{{ old('category_description') }}</textarea>
                    @error('category_description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-primary-button id="form-submit-button">Tambah Kategori</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <div id="view-modal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg p-6 max-w-md w-full relative overflow-y-auto max-h-[80vh]">
            <button onclick="document.getElementById('view-modal').classList.add('hidden')"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>
            <h3 class="text-xl font-bold mb-2" id="view-category-title"></h3>
            <p id="view-category-description" class="break-words whitespace-pre-line"></p>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("category-modal").style.display = "flex";
            document.getElementById("modal-title").textContent = "Tambah Kategori Baru";
            document.getElementById("category-form").action = "{{ route('categories.store') }}";
            document.getElementById("form-submit-button").textContent = "Tambah Kategori";
            document.getElementById("method-field").innerHTML = '';
        }

        function closeModal() {
            document.getElementById("category-modal").style.display = "none";
        }

        function editCategory(id) {
            fetch(`/categories/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("category_name").value = data.category_name;
                    document.getElementById("category_description").value = data.category_description;
                    document.getElementById("category-form").action = `/categories/${id}`;
                    document.getElementById("form-submit-button").textContent = "Simpan Perubahan";
                    document.getElementById("modal-title").textContent = "Edit Kategori";
                    document.getElementById("method-field").innerHTML =
                        '<input type="hidden" name="_method" value="PUT">';
                    document.getElementById("category-modal").style.display = "flex";
                });
        }

        function viewCategory(id) {
            fetch(`/categories/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('view-category-title').textContent = data.category_name;
                    document.getElementById('view-category-description').textContent = data.category_description;
                    document.getElementById('view-modal').classList.remove('hidden');
                });
        }

        function confirmDelete(event, categoryId) {
            event.preventDefault();
            const form = document.getElementById('delete-form-' + categoryId);
            fetch(`/categories/${categoryId}/check-articles`)
                .then(response => response.json())
                .then(data => {
                    if (data.has_articles) {
                        Swal.fire({
                            title: 'Kategori digunakan!',
                            text: 'Kategori ini digunakan dalam artikel dan tidak bisa dihapus.',
                            icon: 'error',
                        });
                    } else {
                        Swal.fire({
                            title: 'Hapus Kategori?',
                            text: 'Apakah Anda yakin ingin menghapus kategori ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                });
        }
    </script>
</x-app-layout>
