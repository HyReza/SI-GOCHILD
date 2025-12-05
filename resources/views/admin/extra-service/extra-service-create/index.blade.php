<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Layanan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 relative">

                {{-- Tombol Kembali --}}
                <div class="absolute top-6 left-6">
                    <a href="{{ route('extra-services.index') }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>

                <div class="p-8 pt-16"> {{-- Padding top ditambah agar tidak menabrak tombol kembali --}}

                    <div class="mb-8 text-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Input Data Layanan</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Lengkapi formulir berikut untuk
                            menambahkan layanan baru ke katalog.</p>
                    </div>

                    <form id="createForm" action="{{ route('extra-services.store') }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Nama Layanan --}}
                        <div class="grid gap-2">
                            <x-input-label for="name" value="Nama Layanan" class="text-gray-700 font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <x-text-input id="name" name="name" type="text"
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    :value="old('name')" required placeholder="Contoh: SPA Baby Premium" />
                            </div>
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        {{-- Harga --}}
                        <div class="grid gap-2">
                            <x-input-label for="base_price" value="Harga Dasar (Rp)"
                                class="text-gray-700 font-semibold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold text-sm">Rp</span>
                                </div>
                                <x-text-input id="base_price" name="base_price" type="number"
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    :value="old('base_price')" required placeholder="0" />
                            </div>
                            <x-input-error :messages="$errors->get('base_price')" />
                        </div>

                        {{-- Deskripsi --}}
                        <div class="grid gap-2">
                            <x-input-label for="description" value="Deskripsi" class="text-gray-700 font-semibold" />
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300"
                                placeholder="Jelaskan detail manfaat dan fasilitas layanan ini...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Upload Gambar --}}
                        <div class="grid gap-2">
                            <x-input-label for="image" value="Gambar Layanan" class="text-gray-700 font-semibold" />

                            <div class="flex items-center justify-center w-full">
                                <label for="image"
                                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 transition group">
                                    <div
                                        class="flex flex-col items-center justify-center pt-5 pb-6 group-hover:scale-105 transition-transform duration-300">
                                        <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="mb-1 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold text-blue-600 hover:underline">Klik untuk
                                                upload</span> atau drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or JPEG (MAX. 2MB)
                                        </p>
                                    </div>
                                    <input id="image" name="image" type="file" class="hidden" accept="image/*"
                                        onchange="previewImage(this)" />
                                </label>
                            </div>

                            {{-- Preview Container --}}
                            <div id="preview-box"
                                class="mt-4 hidden p-2 border rounded-lg bg-gray-50 dark:bg-gray-700/50 text-center">
                                <p class="text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Preview Gambar
                                </p>
                                <img id="img-preview" src=""
                                    class="max-h-64 rounded-lg shadow-sm mx-auto border border-gray-200" alt="Preview">
                            </div>
                            <x-input-error :messages="$errors->get('image')" />
                        </div>

                        {{-- Status Switch --}}
                        <div
                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Status Layanan</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Layanan aktif dapat dipesan oleh
                                    siswa.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        {{-- Actions --}}
                        <div
                            class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('extra-services.index') }}"
                                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5">
                                Simpan Layanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function previewImage(input) {
            const previewBox = document.getElementById('preview-box');
            const previewImg = document.getElementById('img-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewBox.classList.remove('hidden');
                    previewBox.classList.add('block');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                previewBox.classList.add('hidden');
            }
        }

        document.getElementById('createForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let form = this;

            Swal.fire({
                title: 'Simpan Data?',
                text: "Pastikan data yang dimasukkan sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', // Blue 600
                cancelButtonColor: '#9ca3af', // Gray 400
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading(),
                        background: document.documentElement.classList.contains('dark') ?
                            '#1f2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' :
                            '#1f2937',
                        showConfirmButton: false
                    });
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>
