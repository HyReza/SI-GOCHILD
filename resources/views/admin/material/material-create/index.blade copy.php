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

    <div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }">
        <!-- Button to Open Form Modal -->
        <div class="flex justify-start mb-4">
            <x-primary-button @click="open = true">Tambah Materi</x-primary-button>
        </div>

        <!-- Form Modal -->
        <div x-show="open" x-cloak
            class="fixed inset-0 flex items-start justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-8 relative mt-10 mx-4">
                <!-- Close Button -->
                <button @click="open = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                    &times;
                </button>

                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Tambah Materi Baru</h2>

                <!-- Form for Adding Material -->
                <form id="materialForm" method="POST" action="{{ route('material.store') }}"
                    enctype="multipart/form-data" onsubmit="confirmAdd(event)">
                    @csrf
                    <!-- Material Code -->
                    <div class="mb-4">
                        <x-input-label for="material_code" :value="__('Kode Materi')" />
                        <x-text-input id="material_code" class="block mt-1 w-full" type="text" name="material_code"
                            value="{{ old('material_code') }}" required />
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

                    <!-- Upload Document -->
                    <div class="mb-4">
                        <x-input-label for="material_document" :value="__('Upload Dokumen')" />
                        <input type="file" name="material_document" id="material_document"
                            accept=".pdf,.docx,.txt,.jpg,.jpeg,.png"
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
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light dark:text-gray-100">
                        @forelse($materials as $index => $material)
                            <tr
                                class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    {{ ($materials->currentPage() - 1) * $materials->perPage() + $index + 1 }}
                                </td>
                                <td class="py-3 px-6 text-left">{{ $material->material_code }}</td>
                                <td class="py-3 px-6 text-left">{{ $material->material_name }}</td>
                                <td class="py-3 px-6 text-left">{{ $material->subTheme->sub_theme_name }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Illuminate\Support\Str::limit($material->material_description, 30) }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    @if ($material->material_document)
                                        <a href="{{ asset('storage/material_documents/' . basename($material->material_document)) }}"
                                            target="_blank" class="text-blue-500 underline">Lihat Dokumen</a>
                                    @else
                                        Tidak Ada Dokumen
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('material.show', $material->id) }}" class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                                visibility
                                            </span>
                                            <span
                                                class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                                Lihat Detail
                                            </span>
                                        </a>
                                        <a href="{{ route('material.edit', $material->id) }}" class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                                edit_square
                                            </span>
                                            <span
                                                class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                                Edit Data
                                            </span>
                                        </a>
                                        <form id="delete-form-{{ $material->id }}"
                                            action="{{ route('material.destroy', $material) }}" method="POST"
                                            class="relative group delete-form"
                                            data-material-name="{{ $material->material_name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $material->id }})"
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
                                <td colspan="8" class="py-3 px-6 text-center text-gray-500">Tidak ada materi
                                    ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="mt-4">
                {{ $materials->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <script>
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
