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

    <div class="my-6 p-8 bg-white dark:bg-gray-900 rounded-xl shadow-lg hover:shadow-xl duration-300 ease-in-out">
        <h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Edit Materi</h2>

        <form method="POST" action="{{ route('material.update', $material->id) }}" enctype="multipart/form-data"
            id="materialForm">
            @csrf
            @method('PUT')

            <!-- Material Code -->
            <div class="mb-4">
                <x-input-label for="material_code" :value="__('Kode Materi')" />
                <x-text-input id="material_code"
                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500"
                    type="text" name="material_code" value="{{ old('material_code', $material->material_code) }}"
                    required readonly />
                <x-input-error :messages="$errors->get('material_code')" class="mt-2" />
            </div>

            <!-- Select Sub Theme Dropdown -->
            <div class="mb-4">
                <x-input-label for="sub_theme_id" :value="__('Sub Tema')" />
                <select id="sub_theme_id" name="sub_theme_id" required
                    class="block w-full mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Pilih Sub Tema</option>
                    @foreach ($subThemes as $subTheme)
                        <option value="{{ $subTheme->id }}"
                            {{ old('sub_theme_id', $material->sub_theme_id) == $subTheme->id ? 'selected' : '' }}>
                            {{ $subTheme->sub_theme_name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('sub_theme_id')" class="mt-2" />
            </div>

            <!-- Material Name -->
            <div class="mb-4">
                <x-input-label for="material_name" :value="__('Nama Materi')" />
                <x-text-input id="material_name" class="block mt-1 w-full" type="text" name="material_name"
                    value="{{ old('material_name', $material->material_name) }}" required />
                <x-input-error :messages="$errors->get('material_name')" class="mt-2" />
            </div>

            <!-- Material Description -->
            <div class="mb-4">
                <x-input-label for="material_description" :value="__('Deskripsi Materi')" />
                <textarea id="material_description" name="material_description" rows="4" required
                    class="block mt-1 w-full p-2 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">{{ old('material_description', $material->material_description) }}</textarea>
                <x-input-error :messages="$errors->get('material_description')" class="mt-2" />
            </div>

            <!-- Dropdown for Sub theme on report -->
            <div class="mb-4">
                <x-input-label for="material_on_report" :value="__('Materi Masuk Raport')" />
                <select id="material_on_report" name="material_on_report"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                    <option value="1" {{ $material->material_on_report == 1 ? 'selected' : '' }}>Ya
                    </option>
                    <option value="0" {{ $material->material_on_report == 0 ? 'selected' : '' }}>Tidak
                    </option>
                </select>
                <x-input-error :messages="$errors->get('material_on_report')" class="mt-2" />
            </div>

            <!-- Dropdown for Material Status -->
            <div class="mb-4">
                <x-input-label for="material_is_active" :value="__('Status Materi')" />
                <select id="material_is_active" name="material_is_active"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                    <option value="1" {{ $material->material_is_active == 1 ? 'selected' : '' }}>Aktif
                    </option>
                    <option value="0" {{ $material->material_is_active == 0 ? 'selected' : '' }}>Non Aktif
                    </option>
                </select>
                <x-input-error :messages="$errors->get('material_is_active')" class="mt-2" />
            </div>

            <!-- Upload Document -->
            <div class="mb-4">
                <x-input-label for="material_document" :value="__('Upload Dokumen')" />
                <div class="flex items-center justify-center">
                    <input type="file" name="material_document" id="material_document"
                        accept=".pdf,.docx,.txt,.jpg,.jpeg,.png"
                        class="block mt-1 w-full p-2 border border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:focus:ring-blue-500 focus:ring-indigo-500 focus:border-indigo-500" />
                    <x-input-error :messages="$errors->get('material_document')" class="mt-2" />

                    <!-- Display Uploaded Document -->
                    <div id="uploaded-file-display" class="p-2 m-1 rounded-md bg-gray-100 dark:bg-gray-700 shadow-sm"
                        style="display:none;">
                        <div id="uploaded-file-info" class="flex items-center">
                            <!-- Remove File Button -->
                            <button type="button" id="remove-file-btn" onclick="removeFile()"
                                class="text-sm font-thin text-gray-500 dark:text-gray-300 hover:text-gray-700">
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
                <a href="{{ route('material.create') }}"
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

    <script>
        // Track initial values
        let initialSubThemeId = {{ $material->sub_theme_id }};
        let initialMaterialCode = "{{ $material->material_code }}";

        // Automatically update material code when sub-theme is changed
        const subThemeSelect = document.getElementById('sub_theme_id');
        const materialCodeInput = document.getElementById('material_code');

        subThemeSelect.addEventListener('change', function() {
            const selectedSubThemeId = this.value;

            if (selectedSubThemeId != initialSubThemeId) {
                // Fetch the generated material code based on the new sub-theme
                fetch(`/generate-material-code/${selectedSubThemeId}`)
                    .then(response => response.json())
                    .then(data => {
                        materialCodeInput.value = data.newCode; // Update the material code
                    })
                    .catch(error => {
                        console.error('Error generating material code:', error);
                    });
            } else {
                materialCodeInput.value =
                    initialMaterialCode; // Restore the original code if sub-theme is unchanged
            }
        });

        // Handle file input changes
        document.getElementById('material_document').addEventListener('change', function(event) {
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
            document.getElementById('material_document').value = '';
            document.getElementById('uploaded-file-display').style.display = 'none'; // Hide the remove button
        }

        // Handle form submission confirmation
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin memperbarui materi ini?",
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
                    document.getElementById('materialForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
