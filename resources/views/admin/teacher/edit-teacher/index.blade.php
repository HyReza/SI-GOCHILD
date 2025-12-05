<x-app-layout>
    <x-slot:title>Pengaturan Pengajar</x-slot:title>
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
    <nav aria-label="Breadcrumb" class="flex">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('pengajar.index') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>

                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar Pengajar </span>
                </a>
            </li>

            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180">
                </span>

                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Form edit pengajar
                </a>
            </li>
        </ol>
    </nav>
    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Edit Pengajar</h2>

        <form action="{{ route('pengajar.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="user_name" :value="__('Nama Pengajar')" />
                <x-text-input id="user_name" class="block mt-1 w-full" type="text" name="user_name" :value="old('user_name', $teacher->user_name)"
                    required />
                <x-input-error :messages="$errors->get('user_name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $teacher->email)"
                    required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <x-input-label for="phone_number" :value="__('Nomor HP / WA')" />
                <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number"
                    :value="old('phone_number', $teacher->phone_number)" />
                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
            </div>

            <!-- Role Dropdown -->
            <div class="mb-4">
                <x-input-label for="role_id" :value="__('Role')" />
                <select id="role_id" name="role_id"
                    class="block w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    required>
                    <option value="1" {{ old('role_id', $teacher->role_id) == 1 ? 'selected' : '' }}>Admin</option>
                    <option value="2" {{ old('role_id', $teacher->role_id) == 2 ? 'selected' : '' }}>Pengajar
                    </option>
                    <!-- You can add more roles here -->
                </select>
                <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4" x-data="{ showPassword: false }">
                <x-input-label for="password" :value="__('Password (Kosongkan jika tidak diubah)')" />
                <div class="relative">
                    <input id="password"
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        :type="showPassword ? 'text' : 'password'" name="password" />
                    <span @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                        <i x-show="!showPassword" class="material-symbols-outlined text-gray-500">visibility</i>
                        <i x-show="showPassword" class="material-symbols-outlined text-gray-500">visibility_off</i>
                    </span>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-4" x-data="{ showConfirmPassword: false }">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <div class="relative">
                    <input id="password_confirmation"
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" />
                    <span @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                        <i x-show="!showConfirmPassword" class="material-symbols-outlined text-gray-500">visibility</i>
                        <i x-show="showConfirmPassword"
                            class="material-symbols-outlined text-gray-500">visibility_off</i>
                    </span>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="mt-4 text-right">
                <x-primary-button>
                    {{ __('Simpan Perubahan') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function confirmAdd(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Apakah Anda yakin ingin menyimpan perubahan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false, // Disable outside clicks
                        didOpen: () => {
                            Swal.showLoading(); // Show loading animation
                        }
                    });

                    // Submit the form after a short delay
                    setTimeout(() => {
                        document.getElementById('teacherForm').submit(); // Submit form
                    }, 1000); // Adjust the delay time as needed
                }
            });
        }
    </script>
</x-app-layout>
