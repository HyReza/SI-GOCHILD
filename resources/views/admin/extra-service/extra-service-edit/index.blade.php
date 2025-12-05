<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Layanan') }}
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

                <div class="p-8 pt-16">

                    <div class="mb-8 text-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Layanan:
                            {{ $extraService->name }}</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Perbarui informasi layanan di bawah
                            ini.</p>
                    </div>

                    <form id="editForm" action="{{ route('extra-services.update', $extraService->id) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Nama Layanan --}}
                        <div class="grid gap-2">
                            <x-input-label for="name" value="Nama Layanan" class="text-gray-700 font-semibold" />
                            <x-text-input id="name" name="name" type="text"
                                class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500"
                                :value="old('name', $extraService->name)" required />
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
                                    class="pl-10 w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500"
                                    :value="old('base_price', $extraService->base_price)" required />
                            </div>
                            <x-input-error :messages="$errors->get('base_price')" />
                        </div>

                        {{-- Deskripsi --}}
                        <div class="grid gap-2">
                            <x-input-label for="description" value="Deskripsi" class="text-gray-700 font-semibold" />
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ old('description', $extraService->description) }}</textarea>
                        </div>

                        {{-- Upload Gambar --}}
                        <div class="grid gap-2">
                            <x-input-label for="image" value="Ganti Gambar (Opsional)"
                                class="text-gray-700 font-semibold" />

                            <div class="flex flex-col sm:flex-row gap-4">
                                {{-- Gambar Lama --}}
                                @if ($extraService->image_url)
                                    <div class="w-full sm:w-1/3">
                                        <p class="text-xs text-gray-500 mb-2 text-center">Gambar Saat Ini</p>
                                        <div
                                            class="rounded-lg overflow-hidden border border-gray-200 shadow-sm h-32 flex items-center justify-center bg-gray-50">
                                            <img src="{{ asset('storage/' . $extraService->image_url) }}"
                                                class="h-full w-full object-cover" alt="Current">
                                        </div>
                                    </div>
                                @endif

                                {{-- Input Upload --}}
                                <div class="w-full {{ $extraService->image_url ? 'sm:w-2/3' : '' }}">
                                    <label for="image"
                                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-amber-50 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 transition group h-full">
                                        <div
                                            class="flex flex-col items-center justify-center pt-5 pb-6 group-hover:scale-105 transition-transform duration-300">
                                            <svg class="w-8 h-8 mb-2 text-gray-400 group-hover:text-amber-500 transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Klik untuk ganti gambar
                                            </p>
                                        </div>
                                        <input id="image" name="image" type="file" class="hidden"
                                            accept="image/*" onchange="previewImage(this)" />
                                    </label>
                                </div>
                            </div>

                            {{-- Preview Gambar Baru --}}
                            <div id="preview-box"
                                class="mt-4 hidden p-2 border rounded-lg bg-gray-50 dark:bg-gray-700/50 text-center">
                                <p class="text-xs font-bold text-amber-600 mb-2 uppercase tracking-wide">Preview Gambar
                                    Baru</p>
                                <img id="img-preview" src=""
                                    class="max-h-48 rounded-lg shadow-sm mx-auto border border-gray-200" alt="Preview">
                            </div>
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
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                    {{ $extraService->is_active ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500">
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
                                class="px-5 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-4 focus:outline-none focus:ring-amber-300 shadow-lg shadow-amber-500/30 transition transform hover:-translate-y-0.5">
                                Perbarui Layanan
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

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let form = this;
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: 'Perubahan data akan disimpan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Update',
                confirmButtonColor: '#f59e0b', // Amber 500
                cancelButtonColor: '#9ca3af',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading(),
                        showConfirmButton: false,
                        background: document.documentElement.classList.contains('dark') ?
                            '#1f2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' :
                            '#1f2937'
                    });
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>
