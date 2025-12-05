<x-app-layout>
    <x-slot:title>Edit Program</x-slot:title>

    {{-- SweetAlert Error --}}
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    showConfirmButton: true,
                });
            });
        </script>
    @endif

    <nav aria-label="Breadcrumb" class="flex">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('catalog-programs.index') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>

                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Katalog programs </span>
                </a>
            </li>

            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180">
                </span>

                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Edit layanan programs
                </a>
            </li>
        </ol>
    </nav>

    <form id="editForm" method="POST" action="{{ route('catalog-programs.update', $program->id) }}"
        class="max-w-full mx-auto mt-8 bg-white dark:bg-gray-900 p-8 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 space-y-6 transition-all">
        @csrf
        @method('PUT')

        {{-- Nama Program --}}
        <div>
            <label class="flex items-center gap-2 mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                <span class="material-symbols-outlined text-base text-indigo-500">label</span> Nama Program
            </label>
            <input type="text" name="program_name" value="{{ old('program_name', $program->program_name) }}"
                class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('program_name') ? 'border-red-500' : '' }}">
            @error('program_name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Deskripsi Program --}}
        <div>
            <label class="flex items-center gap-2 mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                <span class="material-symbols-outlined text-base text-indigo-500">notes</span> Deskripsi Program
            </label>
            <textarea name="program_description" rows="3"
                class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('program_description') ? 'border-red-500' : '' }}">{{ old('program_description', $program->program_description) }}</textarea>
            @error('program_description')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Harga Program --}}
        <div>
            <label class="flex items-center gap-2 mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                <span class="material-symbols-outlined text-base text-indigo-500">payments</span> Harga Program
            </label>
            <input type="number" name="program_price" value="{{ old('program_price', $program->program_price) }}"
                class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('program_price') ? 'border-red-500' : '' }}">
            @error('program_price')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Jam Mulai & Selesai --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="flex items-center gap-2 mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    <span class="material-symbols-outlined text-base text-indigo-500">schedule</span> Jam Mulai
                </label>
                <input type="time" name="start_time" value="{{ old('start_time', $program->start_time) }}"
                    class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('start_time') ? 'border-red-500' : '' }}">
                @error('start_time')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="flex items-center gap-2 mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    <span class="material-symbols-outlined text-base text-indigo-500">schedule_send</span> Jam Selesai
                </label>
                <input type="time" name="end_time" value="{{ old('end_time', $program->end_time) }}"
                    class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 {{ $errors->has('end_time') ? 'border-red-500' : '' }}">
                @error('end_time')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex justify-end mt-6">
            <button type="submit" id="submitBtn"
                class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <span class="material-symbols-outlined text-sm mr-1">save</span> Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- SweetAlert Konfirmasi dan Loading --}}
    <script>
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: 'Perubahan akan disimpan ke sistem.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#111827'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        background: document.documentElement.classList.contains('dark') ?
                            '#1F2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#e5e7eb' :
                            '#111827'
                    });

                    // Submit setelah loading
                    setTimeout(() => {
                        e.target.submit();
                    }, 800); // delay kecil agar loading terlihat
                }
            });
        });
    </script>
</x-app-layout>
