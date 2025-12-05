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

    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200">Edit Sub Tema</h2>

        <form method="POST" action="{{ route('subthemes.update', $subTheme->id) }}" enctype="multipart/form-data"
            id="subThemeForm">
            @csrf
            @method('PUT')

            <!-- Kode Sub Tema -->
            <div class="mb-4">
                <x-input-label for="sub_theme_code" :value="__('Kode Sub Tema')" />
                <x-text-input id="sub_theme_code"
                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                    type="text" name="sub_theme_code" value="{{ old('sub_theme_code', $subTheme->sub_theme_code) }}"
                    readonly required />
                <x-input-error :messages="$errors->get('sub_theme_code')" class="mt-2" />
            </div>

            <!-- Select Theme Dropdown -->
            <div class="mb-4">
                <x-input-label for="theme_id" :value="__('Tema')" />
                <select id="theme_id" name="theme_id" required
                    class="block w-full mt-1 rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600">
                    <option value="">Pilih Tema</option>
                    @foreach ($themes as $theme)
                        <option value="{{ $theme->id }}"
                            {{ old('theme_id', $subTheme->theme_id) == $theme->id ? 'selected' : '' }}>
                            {{ $theme->theme_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('theme_id')" class="mt-2" />
            </div>

            <!-- Nama Sub Tema -->
            <div class="mb-4">
                <x-input-label for="sub_theme_name" :value="__('Nama Sub Tema')" />
                <x-text-input id="sub_theme_name"
                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                    type="text" name="sub_theme_name" value="{{ old('sub_theme_name', $subTheme->sub_theme_name) }}"
                    required />
                <x-input-error :messages="$errors->get('sub_theme_name')" class="mt-2" />
            </div>

            <!-- Deskripsi Sub Tema -->
            <div class="mb-4">
                <x-input-label for="sub_theme_description" :value="__('Deskripsi Sub Tema')" />
                <textarea id="sub_theme_description" name="sub_theme_description" rows="4" required
                    class="block mt-1 w-full p-2 rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600">{{ old('sub_theme_description', $subTheme->sub_theme_description) }}</textarea>
                <x-input-error :messages="$errors->get('sub_theme_description')" class="mt-2" />
            </div>

            <!-- Start and End Dates -->
            <div class="block md:flex gap-4 mb-4">
                <div class="flex-1">
                    <x-input-label for="sub_theme_start" :value="__('Mulai')" />
                    <x-text-input id="sub_theme_start" type="date" name="sub_theme_start"
                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                        value="{{ old('sub_theme_start', $subTheme->sub_theme_start) }}" required />
                    <x-input-error :messages="$errors->get('sub_theme_start')" class="mt-2" />
                </div>
                <div class="flex-1">
                    <x-input-label for="sub_theme_end" :value="__('Berakhir')" />
                    <x-text-input id="sub_theme_end" type="date" name="sub_theme_end"
                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                        value="{{ old('sub_theme_end', $subTheme->sub_theme_end) }}" required />
                    <x-input-error :messages="$errors->get('sub_theme_end')" class="mt-2" />
                </div>
            </div>

            <!-- Dropdown for Sub theme on report -->
            <div class="mb-4">
                <x-input-label for="sub_theme_on_report" :value="__('Sub Tema Masuk Raport')" />
                <select id="sub_theme_on_report" name="sub_theme_on_report"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                    <option value="1" {{ $subTheme->sub_theme_on_report == 1 ? 'selected' : '' }}>Ya
                    </option>
                    <option value="0" {{ $subTheme->sub_theme_on_report == 0 ? 'selected' : '' }}>Tidak
                    </option>
                </select>
                <x-input-error :messages="$errors->get('sub_theme_on_report')" class="mt-2" />
            </div>

            <!-- Dropdown for Sub Tema Status -->
            <div class="mb-4">
                <x-input-label for="sub_theme_is_active" :value="__('Status Sub Tema')" />
                <select id="sub_theme_is_active" name="sub_theme_is_active"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                    <option value="1" {{ $subTheme->sub_theme_is_active == 1 ? 'selected' : '' }}>Aktif
                    </option>
                    <option value="0" {{ $subTheme->sub_theme_is_active == 0 ? 'selected' : '' }}>Non Aktif
                    </option>
                </select>
                <x-input-error :messages="$errors->get('sub_theme_is_active')" class="mt-2" />
            </div>


            <!-- Upload Document -->
            <div class="mb-4">
                <x-input-label for="sub_theme_document" :value="__('Upload Dokumen')" />
                <div class="flex items-center justify-center">
                    <input type="file" name="sub_theme_document" id="sub_theme_document"
                        accept=".pdf,.docx,.txt,.jpg,.jpeg,.png"
                        class="block mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
                    <x-input-error :messages="$errors->get('sub_theme_document')" class="mt-2" />

                    <!-- Display Uploaded Document -->
                    <div id="uploaded-file-display" class="p-2 m-1 rounded-md bg-gray-100 dark:bg-gray-700 shadow-sm"
                        style="display:none;">
                        <div id="uploaded-file-info" class="flex items-center">
                            <!-- Remove File Button -->
                            <button type="button" id="remove-file-btn" onclick="removeFile()"
                                class="text-sm font-thin text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">
                                <span class="material-symbols-outlined">
                                    close
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <!-- Cancel Button -->
                <a href="{{ route('subthemes.create') }}"
                    class="px-6 py-2 rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-600">
                    {{ __('Batal') }}
                </a>

                <x-primary-button
                    class="px-6 py-2 rounded-md text-white bg-gray-700 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:hover:bg-gray-600"
                    id="submitFormButton">
                    {{ __('Perbarui') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        let initialThemeId = {{ $subTheme->theme_id }};
        let initialSubThemeCode = "{{ $subTheme->sub_theme_code }}";

        // Automatically update sub-theme code when theme is changed
        const themeSelect = document.getElementById('theme_id');
        const subThemeCodeInput = document.getElementById('sub_theme_code');

        themeSelect.addEventListener('change', function() {
            const selectedThemeId = this.value;

            if (selectedThemeId != initialThemeId) {
                // Fetch the generated sub-theme code based on the new theme
                fetch(`/generate-sub-theme-code/${selectedThemeId}`)
                    .then(response => response.json())
                    .then(data => {
                        subThemeCodeInput.value = data.newCode; // Update the code field
                    })
                    .catch(error => {
                        console.error('Error generating sub-theme code:', error);
                    });
            } else {
                subThemeCodeInput.value =
                    initialSubThemeCode; // Restore the original code if the theme is unchanged
            }
        });



        // Handle file input changes
        document.getElementById('sub_theme_document').addEventListener('change', function(event) {
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
            document.getElementById('sub_theme_document').value = '';
            document.getElementById('uploaded-file-display').style.display = 'none'; // Hide the remove button
        }

        // Handle form submission confirmation
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin memperbarui sub tema ini?",
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
                    document.getElementById('subThemeForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
