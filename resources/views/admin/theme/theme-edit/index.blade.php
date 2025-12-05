<x-app-layout>
    <nav aria-label="Breadcrumb" class="flex">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('themes.create') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar Tema </span>
                </a>
            </li>
            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180"></span>
                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Form Edit Tema
                </a>
            </li>
        </ol>
    </nav>

    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <div class="overflow-x-auto p-2">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700 dark:text-gray-200">Edit Tema</h2>

            {{-- Form untuk mengedit tema --}}
            <form method="POST" action="{{ route('themes.update', $theme->id) }}" enctype="multipart/form-data"
                id="themeForm">
                @csrf
                @method('PUT')

                <!-- THEME CODE -->
                <div class="mb-4">
                    <x-input-label for="theme_code" :value="__('Kode Tema')" />
                    <x-text-input id="theme_code" class="block mt-1 w-full" type="text" name="theme_code"
                        :value="old('theme_code', $theme->theme_code)" required readonly />
                    <x-input-error :messages="$errors->get('theme_code')" class="mt-2" />
                </div>

                <!-- THEME NAME -->
                <div class="mb-4">
                    <x-input-label for="theme_name" :value="__('Nama Tema')" />
                    <x-text-input id="theme_name" class="block mt-1 w-full" type="text" name="theme_name"
                        :value="old('theme_name', $theme->theme_name)" required autocomplete="theme_name" />
                    <x-input-error :messages="$errors->get('theme_name')" class="mt-2" />
                </div>

                <!-- THEME DESCRIPTION -->
                <div class="mb-4">
                    <x-input-label for="theme_description" :value="__('Deskripsi Tema')" />
                    <textarea id="theme_description" name="theme_description" rows="4" required
                        class="block mt-1 w-full p-2 border border-gray-300 rounded-md">{{ old('theme_description', $theme->theme_description) }}</textarea>
                    <x-input-error :messages="$errors->get('theme_description')" class="mt-2" />
                </div>

                <!-- Dropdown for theme on report -->
                <div class="mb-4">
                    <x-input-label for="theme_on_report" :value="__('Tema Masuk Raport')" />
                    <select id="theme_on_report" name="theme_on_report"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                        <option value="1" {{ $theme->theme_on_report == 1 ? 'selected' : '' }}>Ya
                        </option>
                        <option value="0" {{ $theme->theme_on_report == 0 ? 'selected' : '' }}>Tidak
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('theme_on_report')" class="mt-2" />
                </div>

                <!-- Dropdown for theme Status -->
                <div class="mb-4">
                    <x-input-label for="theme_is_active" :value="__('Status Tema')" />
                    <select id="theme_is_active" name="theme_is_active"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                        <option value="1" {{ $theme->theme_is_active == 1 ? 'selected' : '' }}>Aktif
                        </option>
                        <option value="0" {{ $theme->theme_is_active == 0 ? 'selected' : '' }}>Non Aktif
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('theme_is_active')" class="mt-2" />
                </div>

                <!-- DOCUMENT UPLOAD -->
                <div class="mb-4">
                    <x-input-label for="theme_document" :value="__('Upload Dokumen')" />
                    <div class="flex items-center justify-center w-full">
                        <!-- File Upload Input -->
                        <input type="file" name="theme_document" id="theme_document"
                            accept=".pdf,.docx,.txt,.jpg,.jpeg,.png"
                            class="block mt-1 w-full p-2 border border-gray-300 rounded-md" />

                        <x-input-error :messages="$errors->get('theme_document')" class="mt-2" />

                        <!-- Display Uploaded Document -->
                        <div id="uploaded-file-display"
                            class="p-2 m-1 rounded-md bg-gray-100 dark:bg-gray-700 shadow-sm" style="display:none;">
                            <div id="uploaded-file-info" class="flex items-center">
                                <!-- Remove File Button -->
                                <button type="button" id="remove-file-btn" onclick="removeFile()"
                                    class="text-sm font-thin text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <!-- Cancel Button -->
                    <a href="{{ route('themes.create') }}"
                        class="px-6 py-2 rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                        {{ __('Batal') }}
                    </a>

                    <x-primary-button
                        class="px-6 py-2 rounded-md text-white bg-gray-700 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        id="submitFormButton">
                        {{ __('Perbarui') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Handle file input changes
        document.getElementById('theme_document').addEventListener('change', function(event) {
            const fileInput = event.target;
            const fileName = fileInput.files[0]?.name;

            // Display the uploaded file name and remove button
            if (fileName) {
                document.getElementById('uploaded-file-display').style.display = 'block'; // Show remove button
            }
        });

        // Function to remove the file
        function removeFile() {
            // Clear the file input and hide the file display
            document.getElementById('theme_document').value = '';
            document.getElementById('uploaded-file-display').style.display = 'none'; // Hide the remove button
        }

        // Handle form submission confirmation
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin memperbarui tema ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui',
                cancelButtonText: 'Batal'
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
                    document.getElementById('themeForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
