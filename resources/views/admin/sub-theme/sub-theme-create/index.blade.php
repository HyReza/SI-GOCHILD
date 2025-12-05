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

    <div x-data="{ open: false }" class="flex justify-start mb-4">
        <div class="flex flex-col md:flex-row justify-between items-center w-full gap-6">
            <!-- Button Section -->
            <div class="w-full md:w-auto">
                <x-primary-button @click="open = true" class="w-full md:w-auto py-3">
                    Tambah Sub Tema
                </x-primary-button>
            </div>

            <!-- Search Input Section -->
            <div class="w-full md:w-1/2 flex">
                <input id="searchInput" type="text" placeholder="Cari sub tema (kode/nama/deskripsi)..."
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
            class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex justify-center items-start overflow-y-auto">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-lg w-full p-8 mt-10 mb-10 mx-4 relative">
                <button @click="open = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>

                <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Tambah Sub Tema Baru</h2>

                <!-- Form for Adding Sub Theme -->
                <form id="subThemeForm" method="POST" action="{{ route('subthemes.store') }}"
                    enctype="multipart/form-data" onsubmit="confirmAdd(event)">
                    @csrf

                    <!-- Kode Sub Tema (Auto-Generated and Read-Only) -->
                    <div class="mb-4">
                        <x-input-label for="sub_theme_code" :value="__('Kode Sub Tema')" />
                        <x-text-input id="sub_theme_code" class="block mt-1 w-full" type="text" name="sub_theme_code"
                            value="{{ old('sub_theme_code', $newCode) }}" readonly required />
                        <x-input-error :messages="$errors->get('sub_theme_code')" class="mt-2" />
                    </div>

                    <!-- Tema Dropdown -->
                    <div class="mb-4">
                        <x-input-label for="theme_id" :value="__('Tema')" />
                        <select id="theme_id" name="theme_id" required
                            class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            <option value="">Pilih Tema</option>
                            @foreach ($themes as $theme)
                                <option value="{{ $theme->id }}"
                                    {{ old('theme_id') == $theme->id ? 'selected' : '' }}>
                                    {{ $theme->theme_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('theme_id')" class="mt-2" />
                    </div>

                    <!-- Nama Sub Tema -->
                    <div class="mb-4">
                        <x-input-label for="sub_theme_name" :value="__('Nama Sub Tema')" />
                        <x-text-input id="sub_theme_name" class="block mt-1 w-full" type="text" name="sub_theme_name"
                            value="{{ old('sub_theme_name') }}" required />
                        <x-input-error :messages="$errors->get('sub_theme_name')" class="mt-2" />
                    </div>

                    {{-- Sub Theme On Report --}}
                    <div class="mb-4">
                        <x-input-label for="sub_theme_on_report" :value="__('Sub Tema Masuk Raport')" />
                        <select id="sub_theme_on_report" name="sub_theme_on_report" required
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md">
                            <option value="">Pilih</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>

                    <!-- Deskripsi Sub Tema -->
                    <div class="mb-4">
                        <x-input-label for="sub_theme_description" :value="__('Deskripsi Sub Tema')" />
                        <textarea id="sub_theme_description" name="sub_theme_description" rows="4" required
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md">{{ old('sub_theme_description') }}</textarea>
                        <x-input-error :messages="$errors->get('sub_theme_description')" class="mt-2" />
                    </div>

                    <!-- Start and End Dates -->
                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <x-input-label for="sub_theme_start" :value="__('Mulai')" />
                            <x-text-input id="sub_theme_start" type="date" name="sub_theme_start"
                                class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('sub_theme_start')" class="mt-2" />
                        </div>
                        <div class="flex-1">
                            <x-input-label for="sub_theme_end" :value="__('Berakhir')" />
                            <x-text-input id="sub_theme_end" type="date" name="sub_theme_end"
                                class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('sub_theme_end')" class="mt-2" />
                        </div>
                    </div>


                    <!-- Upload Document -->
                    <div class="mb-4">
                        <x-input-label for="sub_theme_document" :value="__('Upload Dokumen')" />
                        <input type="file" name="sub_theme_document" id="sub_theme_document" accept=".pdf,.docx,.ppx"
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600" />
                        <x-input-error :messages="$errors->get('sub_theme_document')" class="mt-2" />
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

    <!-- Table for Displaying Sub Themes -->
    <div class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md">
        <div class="overflow-x-auto p-2">
            <table class="min-w-full table-auto mb-2">
                <thead
                    class="text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Kode</th>
                        <th class="py-3 px-6 text-left">Nama Sub Tema</th>
                        <th class="py-3 px-6 text-left">Deskripsi</th>
                        <th class="py-3 px-6 text-left">Dokumen</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Masuk Raport</th>
                        <th class="py-3 px-6 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody id="subThemeList" class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @include('admin.sub-theme.sub-theme-create.sub-theme-list', [
                        'subThemes' => $subThemes,
                    ])
                </tbody>
            </table>
        </div>

        <div id="pagination" class="mt-4">
            @include('admin.sub-theme.sub-theme-create.theme-pagination', ['subThemes' => $subThemes])
        </div>
    </div>

    <script>
        // Confirm add sub theme
        function confirmAdd(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Tambah Sub Tema?',
                text: "Apakah Anda yakin ingin menambahkan sub tema ini?",
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
                    document.getElementById('subThemeForm').submit();
                }
            });
        }


        // Confirm Delete Function
        function confirmDelete(id) {
            const form = document.getElementById(`delete-form-${id}`);
            const themeName = form.getAttribute('data-sub-theme-name');

            Swal.fire({
                title: `Hapus Sub Tema?`,
                text: `Apakah Anda yakin ingin menghapus sub tema: "${themeName}"?`,
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



        const searchInput = document.getElementById('searchInput');
        const subThemeList = document.getElementById('subThemeList');
        const loadingDot = document.getElementById('loadingDot');
        // Update sub-theme code when theme is selected
        const themeSelect = document.getElementById('theme_id');
        const subThemeCodeInput = document.getElementById('sub_theme_code');

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
        async function fetchSubThemes(search = '', page = 1) {
            loadingDot.classList.remove('hidden');

            const params = new URLSearchParams();
            if (search) params.set('search', search);
            if (page) params.set('page', page);

            const url = `{{ route('subthemes.create') }}?${params.toString()}`;

            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Gagal memuat data');

                const data = await res.json();

                // Update tbody & pagination
                subThemeList.innerHTML = data.tbody;
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

        searchInput.addEventListener('input', debounce((e) => {
            const searchQuery = e.target.value.trim();
            fetchSubThemes(searchQuery);
        }, 350));

        // SUB THEME CODE OTOMATIS
        themeSelect.addEventListener('change', function() {
            const selectedThemeId = this.value;

            // Fetch the generated code dynamically based on the selected theme
            fetch(`/generate-sub-theme-code/${selectedThemeId}`)
                .then(response => response.json())
                .then(data => {
                    subThemeCodeInput.value = data.newCode; // Update the code in the input
                })
                .catch(error => {
                    console.error('Error generating sub-theme code:', error);
                });
        });
    </script>
</x-app-layout>
