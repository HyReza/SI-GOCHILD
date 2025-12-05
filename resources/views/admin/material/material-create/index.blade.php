<x-app-layout>
    {{-- SweetAlert for Success Message --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    {{-- SweetAlert for Error Message --}}
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <div x-data="{ open: false }">
        <!-- Button to Open Form Modal -->
        <div class="flex flex-col md:flex-row justify-between items-center w-full gap-6">
            <!-- Button Section -->
            <div class="w-full md:w-auto">
                <x-primary-button @click="open = true" class="w-full md:w-auto py-3">
                    Tambah Materi
                </x-primary-button>
            </div>

            <!-- Search Input Section -->
            <div class="w-full md:w-1/2 flex">
                <input id="searchInput" type="text" placeholder="Cari Materi (kode/nama/deskripsi)..."
                    class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-opacity-50
                  dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-blue-500
                  bg-white text-gray-900 border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                <!-- Loading indicator -->
                <div id="loadingDot" class="hidden animate-pulse text-sm text-gray-500 dark:text-gray-300 mt-2">
                    <span class="material-symbols-outlined">sync</span>
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <div x-show="open" x-cloak
            class="fixed inset-0 flex items-start justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-8 relative mt-10 mb-10 mx-4">
                <button @click="open = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
                <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Tambah Materi Baru</h2>

                <!-- Form for Adding Material -->
                <form id="materialForm" method="POST" action="{{ route('material.store') }}"
                    enctype="multipart/form-data" onsubmit="confirmAdd(event)">
                    @csrf
                    <!-- Material Code -->
                    <div class="mb-4">
                        <x-input-label for="material_code" :value="__('Kode Materi')" />
                        <x-text-input id="material_code" class="block mt-1 w-full" type="text" name="material_code"
                            value="{{ old('material_code') }}" required readonly />
                        <x-input-error :messages="$errors->get('material_code')" class="mt-2" />
                    </div>

                    <!-- Select Sub Theme Dropdown -->
                    <div class="mb-4">
                        <x-input-label for="sub_theme_id" :value="__('Sub Tema')" />
                        <select id="sub_theme_id" name="sub_theme_id" required
                            class="block w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option value="">Pilih Sub Tema</option>
                            @foreach ($subThemes as $subTheme)
                                <option value="{{ $subTheme->id }}"
                                    {{ old('sub_theme_id') == $subTheme->id ? 'selected' : '' }}>
                                    {{ $subTheme->sub_theme_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sub_theme_id')" class="mt-2" />
                    </div>

                    <!-- Material Name -->
                    <div class="mb-4">
                        <x-input-label for="material_name" :value="__('Nama Materi')" />
                        <x-text-input id="material_name" class="block mt-1 w-full" type="text" name="material_name"
                            value="{{ old('material_name') }}" required />
                        <x-input-error :messages="$errors->get('material_name')" class="mt-2" />
                    </div>

                    <!-- Material Description -->
                    <div class="mb-4">
                        <x-input-label for="material_description" :value="__('Deskripsi Materi')" />
                        <textarea id="material_description" name="material_description" rows="4" required
                            class="block mt-1 w-full p-2 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600">{{ old('material_description') }}</textarea>
                        <x-input-error :messages="$errors->get('material_description')" class="mt-2" />
                    </div>

                    {{-- Material On Report --}}
                    <div class="mb-4">
                        <x-input-label for="material_on_report" :value="__('Materi Masuk Raport')" />
                        <select id="material_on_report" name="material_on_report" required
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md">
                            <option value="">Pilih</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>

                    <!-- Upload Document -->
                    <div class="mb-4">
                        <x-input-label for="material_document" :value="__('Upload Dokumen')" />
                        <input type="file" name="material_document" id="material_document" accept=".pdf,.docx,.ppx"
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-blue-500 bg-white text-gray-900 border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                        <x-input-error :messages="$errors->get('material_document')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ms-4">
                            {{ __('Tambah') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table to Display Material List -->
        <div
            class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse mb-4">
                    <thead>
                        <tr
                            class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-200 text-sm leading-normal">
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">Kode Materi</th>
                            <th class="py-3 px-6 text-left">Nama Materi</th>
                            <th class="py-3 px-6 text-left">Sub Tema</th>
                            <th class="py-3 px-6 text-left">Deskripsi</th>
                            <th class="py-3 px-6 text-left">Dokumen</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Masuk Raport</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="materialList" class="text-gray-600 text-sm font-light dark:text-gray-100">
                        @include('admin.material.material-create.material-list', [
                            'materials' => $materials,
                        ])
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="mt-4">
                @include('admin.material.material-create.theme-pagination', ['materials' => $materials])
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const materialList = document.getElementById('materialList');
        const pagination = document.getElementById('pagination');

        searchInput.addEventListener('input', function() {
            const searchQuery = searchInput.value.trim();
            fetchMaterials(searchQuery);
        });

        function fetchMaterials(searchQuery = '') {
            const url = new URL('{{ route('material.create') }}');
            if (searchQuery) {
                url.searchParams.set('search', searchQuery);
            }

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    materialList.innerHTML = data.tbody;
                    pagination.innerHTML = data.pagination;
                })
                .catch(error => {
                    console.error('Error fetching materials:', error);
                });
        }

        document.getElementById('sub_theme_id').addEventListener('change', function() {
            const subThemeId = this.value;

            // Fetch the generated material code based on sub-theme ID
            fetch(`/generate-material-code/${subThemeId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('material_code').value = data.newCode;
                })
                .catch(error => {
                    console.error('Error generating material code:', error);
                });
        });


        function confirmAdd(event) {
            event.preventDefault(); // Prevent default form submission
            Swal.fire({
                title: 'Tambah Materi?',
                text: "Apakah Anda yakin ingin menambah materi ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tambah!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false, // Disable clicking outside to close
                        didOpen: () => {
                            Swal.showLoading(); // Show the loading animation
                        }
                    });

                    // Submit the form after the user confirms
                    setTimeout(() => {
                        document.getElementById('materialForm').submit(); // Submit form
                    }, 2000);
                }
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Materi?',
                text: "Apakah Anda yakin ingin menghapus Materi ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false, // Disable clicking outside to close
                        didOpen: () => {
                            Swal.showLoading(); // Show the loading animation
                        }
                    });

                    // Submit the form after the user confirms
                    setTimeout(() => {
                        document.getElementById(`delete-form-${id}`).submit(); // Submit delete form
                    }, 2000);
                }
            });
        }
    </script>
</x-app-layout>
