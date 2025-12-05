<x-app-layout>
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
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-8 relative m-4">
                <button @click="open = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>

                <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Tambah Sub Tema Baru</h2>

                <!-- Form for Adding Sub Theme -->
                <form method="POST" action="{{ route('subthemes.store') }}" enctype="multipart/form-data">
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
                        <input type="file" name="sub_theme_document" id="sub_theme_document"
                            accept=".pdf,.docx,.txt,.jpg,.jpeg,.png"
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

    <script>
        // Update sub-theme code when theme is selected
        const themeSelect = document.getElementById('theme_id');
        const subThemeCodeInput = document.getElementById('sub_theme_code');

        themeSelect.addEventListener('change', function() {
            const selectedThemeId = this.value;
            const selectedThemePrefix = themeSelect.options[themeSelect.selectedIndex].text.slice(0,
            2); // Extract the prefix (e.g., AA)

            // Generate the sub-theme code dynamically
            fetch(`/generate-sub-theme-code/${selectedThemeId}`)
                .then(response => response.json())
                .then(data => {
                    subThemeCodeInput.value = data.newCode; // Update the code in the input
                });
        });
    </script>
</x-app-layout>
