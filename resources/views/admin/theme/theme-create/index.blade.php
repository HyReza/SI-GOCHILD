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
    {{-- Button to Open Form Modal --}}
    <div x-data="{ open: false }" class="flex justify-start mb-4">
        <div class="flex flex-col md:flex-row justify-between items-center w-full gap-6">
            <!-- Button Section (Atas di mobile) -->
            <div class="w-full md:w-auto">
                <x-primary-button @click="open = true" class="w-full md:w-auto py-3">
                    Tambah Tema
                </x-primary-button>
            </div>

            <!-- Search Input Section (Bawah di mobile) -->
            <div class="w-full md:w-1/2 flex">
                <input id="searchInput" type="text" placeholder="Cari tema (kode/nama/deskripsi)..."
                    class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-opacity-50
                  dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:focus:ring-blue-500
                  bg-white text-gray-900 border-gray-300 focus:ring-blue-500 focus:border-blue-500" />

                <!-- Loading indicator -->
                <div id="loadingDot" class="hidden animate-pulse text-sm text-gray-500 dark:text-gray-300 mt-2">
                    <span class="material-symbols-outlined">
                        sync
                    </span>
                </div>
            </div>
        </div>

        {{-- Form Modal --}}
        <div x-show="open" x-cloak
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-8 relative m-4">
                {{-- Close Button --}}
                <button @click="open = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                    &times;
                </button>

                <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Tambah Tema Baru</h2>

                {{-- Form for Adding Theme --}}
                <form id="themeForm" method="POST" action="{{ route('themes.store') }}" enctype="multipart/form-data"
                    onsubmit="confirmAdd(event)">
                    @csrf

                    <!-- Kode Tema (Auto-Generated and Read-Only) -->
                    <div class="mb-4">
                        <x-input-label for="theme_code" :value="__('Kode Tema')" />
                        <x-text-input id="theme_code" class="block mt-1 w-full" type="text" name="theme_code"
                            :value="old('theme_code', $themeCode)" required readonly />
                        <x-input-error :messages="$errors->get('theme_code')" class="mt-2" />
                    </div>

                    <!-- Nama Tema -->
                    <div class="mb-4">
                        <x-input-label for="theme_name" :value="__('Nama Tema')" />
                        <x-text-input id="theme_name" class="block mt-1 w-full" type="text" name="theme_name"
                            :value="old('theme_name')" required autocomplete="theme_name" />
                        <x-input-error :messages="$errors->get('theme_name')" class="mt-2" />
                    </div>

                    <!-- Deskripsi Tema -->
                    <div class="mb-4">
                        <x-input-label for="theme_description" :value="__('Deskripsi Tema')" />
                        <textarea id="theme_description" name="theme_description" rows="4" required
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md">{{ old('theme_description') }}</textarea>
                        <x-input-error :messages="$errors->get('theme_description')" class="mt-2" />
                    </div>

                    {{-- Theme On Report --}}
                    <div class="mb-4">
                        <x-input-label for="theme_on_report" :value="__('Tema Masuk Raport')" />
                        <select id="theme_on_report" name="theme_on_report" required
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md">
                            <option value="">Pilih</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>

                    <!-- Upload Dokumen -->
                    <div class="mb-4">
                        <x-input-label for="theme_document" :value="__('Upload Dokumen')" />
                        <input type="file" name="theme_document" id="theme_document" accept=".pdf,.docx,.ppx"
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md" />
                        <x-input-error :messages="$errors->get('theme_document')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ms-4">
                            {{ __('Tambah') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">

        <div class="overflow-x-auto p-2">
            <table class="min-w-full table-auto mb-2">
                <thead
                    class="text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Kode</th>
                        <th class="py-3 px-6 text-left">Nama Tema</th>
                        <th class="py-3 px-6 text-left">Deskripsi</th>
                        <th class="py-3 px-6 text-left">Dokumen</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Masuk Raport</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>

                {{-- TBody hasil render server --}}
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light" id="themeList">
                    @include('admin.theme.theme-create.theme-list', ['themes' => $themes])
                </tbody>
            </table>
        </div>
        {{-- Paginasi --}}
        <div id="pagination" class="mt-4">
            @include('admin.theme.theme-create.theme-pagination', ['themes' => $themes])
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const themeList = document.getElementById('themeList');
        const pagination = document.getElementById('pagination');
        const loadingDot = document.getElementById('loadingDot');

        let debounceTimer = null;
        let currentSearch = new URLSearchParams(window.location.search).get('search') || '';

        // Inisialisasi value input dari URL (jika ada)
        if (currentSearch) {
            searchInput.value = currentSearch;
        }

        // Debounce util
        function debounce(fn, delay = 350) {
            return (...args) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        // Fetch & render list
        async function fetchThemes({
            search = '',
            page = 1
        } = {}) {
            loadingDot.classList.remove('hidden');

            const params = new URLSearchParams();
            if (search) params.set('search', search);
            if (page) params.set('page', page);

            const url = `{{ route('themes.create') }}?${params.toString()}`;

            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Gagal memuat data');

                const data = await res.json();

                // Update tbody & paginasi
                themeList.innerHTML = data.tbody;
                pagination.innerHTML = data.pagination;

                // Update URL (pushState) agar bisa di-back/refresh tanpa hilang query
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('search', search);
                newUrl.searchParams.set('page', page);
                window.history.replaceState({}, '', newUrl);

            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat memuat data.');
            } finally {
                loadingDot.classList.add('hidden');
            }
        }

        // Event: ketik di input (debounce)
        searchInput.addEventListener('input', debounce((e) => {
            currentSearch = e.target.value.trim();
            fetchThemes({
                search: currentSearch,
                page: 1
            });
        }, 350));

        // Event: klik paginasi (intercept agar AJAX)
        pagination.addEventListener('click', function(e) {
            const a = e.target.closest('a');
            if (!a) return;

            const href = new URL(a.href);
            const page = href.searchParams.get('page') || 1;

            e.preventDefault();
            fetchThemes({
                search: currentSearch,
                page
            });
        });

        // Confirm add sub theme
        function confirmAdd(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Tambah Sub Tema?',
                text: "Apakah Anda yakin ingin menambahkan tema ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tambah!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false, // Disable clicking outside to close
                        didOpen: () => {
                            Swal.showLoading(); // Show loading spinner
                        }
                    });

                    // Submit the form after the user confirms
                    document.getElementById('themeForm').submit();
                }
            });
        }

        // Confirm Delete Function
        function confirmDelete(id) {
            const form = document.getElementById(`delete-form-${id}`);
            const themeName = form.getAttribute('data-theme-name');

            Swal.fire({
                title: `Hapus Sub Tema?`,
                text: `Apakah Anda yakin ingin menghapus tema: "${themeName}"?`,
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
                        allowOutsideClick: false, // Disable clicking outside to close
                        didOpen: () => {
                            Swal.showLoading(); // Show loading spinner
                        }
                    });

                    // Submit the form after the user confirms
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
