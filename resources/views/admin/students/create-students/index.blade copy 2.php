<x-app-layout>
    <x-slot:title>Tambah Siswa</x-slot:title>

    <nav aria-label="Breadcrumb" class="flex mb-4">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('siswa.index') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>

                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar Siswa </span>
                </a>
            </li>

            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180">
                </span>

                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Registrasi Siswa
                </a>
            </li>
        </ol>
    </nav>

    <div
        class="p-8 bg-white dark:bg-gray-900 dark:text-gray-200 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <form id="studentForm" method="POST" action="{{ route('siswa.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Data Siswa -->
            <div id="step-1">
                <div>
                    <h2 class="sr-only">Steps</h2>
                    {{-- STEP INDIKATOR --}}
                    <div
                        class="relative after:absolute after:inset-x-0 after:top-1/2 after:block after:h-0.5 after:-translate-y-1/2 after:rounded-lg after:bg-gray-100 my-6">
                        <ol
                            class="relative z-10 flex justify-between text-sm font-medium text-gray-300 dark:text-gray-400">
                            <li class="flex items-center gap-2 bg-white dark:bg-gray-900 p-2">
                                <span
                                    class="size-6 rounded-full bg-blue-600 dark:bg-blue-800 text-center text-[10px]/6 font-bold">
                                    1
                                </span>

                                <span class="hidden sm:block"> Daftar Siswa </span>
                            </li>

                            <li class="flex items-center gap-2 bg-white dark:bg-gray-900 p-2">
                                <span
                                    class="size-6 rounded-full bg-gray-100 dark:bg-gray-500 text-center text-[10px]/6 font-bold text-white">
                                    2
                                </span>

                                <span class="hidden sm:block"> Program & Service </span>
                            </li>
                            <li class="flex items-center gap-2 bg-white dark:bg-gray-900 p-2">
                                <span
                                    class="size-6 rounded-full bg-gray-100 dark:bg-gray-500 text-center text-[10px]/6 font-bold text-white">
                                    3
                                </span>

                                <span class="hidden sm:block"> Finished </span>
                            </li>
                        </ol>
                    </div>
                </div>

                <input type="hidden" name="entry_date" value="{{ now()->toDateString() }}">

                <!-- NIS -->
                <div>
                    <x-input-label for="student_number" :value="__('Nomer Induk Sekolah')" />
                    <x-text-input id="student_number" class="block mt-1 w-full" type="text" name="student_number"
                        :value="$newStudentNumber" readonly />
                    <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
                </div>

                <!-- Nama Panggilan -->
                <div class="mt-4">
                    <div class="flex">
                        <x-input-label for="national_id" :value="__('NIK')" />
                        <span class="text-xs content-center text-gray-500 mx-1">*opsional</span>
                    </div>
                    <x-text-input id="national_id" class="block mt-1 w-full" type="text" name="national_id"
                        :value="old('national_id')" autocomplete="national_id" />
                    <x-input-error :messages="$errors->get('national_id')" class="mt-2" />
                </div>

                <!-- Name -->
                <div class="mt-4">
                    <x-input-label for="student_name" :value="__('Nama')" />
                    <x-text-input id="student_name" class="block mt-1 w-full" type="text" name="student_name"
                        :value="old('student_name')" required autofocus autocomplete="student_name" />
                    <x-input-error :messages="$errors->get('student_name')" class="mt-2" />
                </div>

                <!-- Nama Panggilan -->
                <div class="mt-4">
                    <x-input-label for="nickname" :value="__('Nama Panggilan')" />
                    <x-text-input id="nickname" class="block mt-1 w-full" type="text" name="nickname"
                        :value="old('nickname')" required autocomplete="nickname" />
                    <x-input-error :messages="$errors->get('nickname')" class="mt-2" />
                </div>

                <!-- Pilihan Gender -->
                <div class="mt-4">
                    <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                    <div class="flex items-center mt-2">
                        <input id="gender_man" type="radio" name="gender" value="1"
                            class="mr-2 dark:text-gray-300 dark:bg-gray-800"
                            {{ old('gender') == '1' ? 'checked' : '' }}>
                        <label for="gender_man" class="mr-4 dark:text-gray-300">Pria</label>

                        <input id="gender_woman" type="radio" name="gender" value="0"
                            class="mr-2 dark:text-gray-300 dark:bg-gray-800"
                            {{ old('gender') == '0' ? 'checked' : '' }}>
                        <label for="gender_woman" class="dark:text-gray-300">Wanita</label>
                    </div>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>

                <!-- Tempat Lahir -->
                <div class="mt-4">
                    <x-input-label for="birth_place" :value="__('Tempat Lahir')" />
                    <x-text-input id="birth_place" class="block mt-1 w-full" type="text" name="birth_place"
                        :value="old('birth_place')" required autocomplete="birth_place" />
                    <x-input-error :messages="$errors->get('birth_place')" class="mt-2" />
                </div>

                <!-- Tanggal Lahir -->
                <div class="mt-4">
                    <x-input-label for="birth_date" :value="__('Tanggal Lahir')" />
                    <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date"
                        :value="old('birth_date')" required autocomplete="birth_date" />
                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                </div>

                <!-- Nama Ayah -->
                <div class="mt-4">
                    <x-input-label for="father_name" :value="__('Nama Ayah')" />
                    <x-text-input id="father_name" class="block mt-1 w-full" type="text" name="father_name"
                        :value="old('father_name')" required autocomplete="father_name" />
                    <x-input-error :messages="$errors->get('father_name')" class="mt-2" />
                </div>

                <!-- Nama Ibu -->
                <div class="mt-4">
                    <x-input-label for="mother_name" :value="__('Nama Ibu')" />
                    <x-text-input id="mother_name" class="block mt-1 w-full" type="text" name="mother_name"
                        :value="old('mother_name')" required autocomplete="mother_name" />
                    <x-input-error :messages="$errors->get('mother_name')" class="mt-2" />
                </div>

                <!-- Jalan -->
                <div class="mt-4">
                    <x-input-label for="street" :value="__('Jalan')" />
                    <x-text-input id="street" class="block mt-1 w-full" type="text" name="street"
                        :value="old('street')" required autocomplete="street" />
                    <x-input-error :messages="$errors->get('street')" class="mt-2" />
                </div>

                <!-- Desa / Kelurahan -->
                <div class="mt-4">
                    <x-input-label for="village" :value="__('Desa / Kelurahan')" />
                    <x-text-input id="village" class="block mt-1 w-full" type="text" name="village"
                        :value="old('village')" required autocomplete="village" />
                    <x-input-error :messages="$errors->get('village')" class="mt-2" />
                </div>

                <!-- Kecamatan -->
                <div class="mt-4">
                    <x-input-label for="district" :value="__('Kecamatan')" />
                    <x-text-input id="subdistrict" class="block mt-1 w-full" type="text" name="subdistrict"
                        :value="old('subdistrict')" required autocomplete="subdistrict" />
                    <x-input-error :messages="$errors->get('subdistrict')" class="mt-2" />
                </div>

                <!-- Kecamatan -->
                <div class="mt-4">
                    <x-input-label for="district" :value="__('Kabupaten / Kota')" />
                    <x-text-input id="district" class="block mt-1 w-full" type="text" name="district"
                        :value="old('district')" required autocomplete="district" />
                    <x-input-error :messages="$errors->get('district')" class="mt-2" />
                </div>


                <!-- Nomer Telephone -->
                <div class="mt-4">
                    <x-input-label for="phone_number" :value="__('Nomer Telephone / WA')" />
                    <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number"
                        :value="old('phone_number')" required autocomplete="phone_number" />
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                {{-- <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                        name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div> --}}

                <!-- Password -->
                <div class="mt-4" x-data="{ showPassword: false }">
                    <x-input-label for="password" :value="__('Password')" />
                    <div class="relative">
                        <input id="password"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 pr-12"
                            :type="showPassword ? 'text' : 'password'" name="password" required
                            autocomplete="new-password" />
                        <!-- Password visibility toggle -->
                        <span @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer mx-1">
                            <i x-show="!showPassword" class="material-symbols-outlined text-gray-500">visibility</i>
                            <i x-show="showPassword"
                                class="material-symbols-outlined text-gray-500">visibility_off</i>
                        </span>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4" x-data="{ showConfirmPassword: false }">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <div class="relative">
                        <input id="password_confirmation"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 pr-12"
                            :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required
                            autocomplete="new-password" />
                        <!-- Confirm Password visibility toggle -->
                        <span @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer mx-1">
                            <i x-show="!showConfirmPassword"
                                class="material-symbols-outlined text-gray-500">visibility</i>
                            <i x-show="showConfirmPassword"
                                class="material-symbols-outlined text-gray-500">visibility_off</i>
                        </span>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Foto -->
                <div class="mt-4">
                    <x-input-label for="user_photo" :value="__('Foto')" />
                    <x-text-input id="user_photo" class="block mt-1 w-full" type="file" name="user_photo"
                        accept="image/*" onchange="previewImage(event)" />
                    <x-input-error :messages="$errors->get('user_photo')" class="mt-2" />
                    <div id="image-preview" class="mt-2"></div>
                </div>

                <!-- Button for next step -->
                <div class="flex justify-between mt-4">
                    <span></span> <!-- Empty span for alignment -->
                    <x-primary-button id="next-btn" class="ml-auto">Next</x-primary-button>
                </div>
            </div>

            <!-- Step 2: Program dan Service -->
            <div id="step-2" style="display:none;">
                <div>
                    <h2 class="sr-only">Steps</h2>
                    {{-- STEP INDIKATOR --}}
                    <div
                        class="relative after:absolute after:inset-x-0 after:top-1/2 after:block after:h-0.5 after:-translate-y-1/2 after:rounded-lg after:bg-gray-100 my-6">
                        <ol
                            class="relative z-10 flex justify-between text-sm font-medium text-gray-300 dark:text-gray-400">
                            <li class="flex items-center gap-2 bg-white dark:bg-gray-900 p-2">
                                <span
                                    class="size-6 rounded-full bg-gray-100 dark:bg-gray-500 text-center text-[10px]/6 font-bold">
                                    1
                                </span>

                                <span class="hidden sm:block"> Daftar Siswa </span>
                            </li>

                            <li class="flex items-center gap-2 bg-white dark:bg-gray-900 p-2">
                                <span
                                    class="size-6 rounded-full bg-blue-600 dark:bg-blue-800 text-center text-[10px]/6 font-bold text-white">
                                    2
                                </span>

                                <span class="hidden sm:block"> Program & Service </span>
                            </li>
                            <li class="flex items-center gap-2 bg-white dark:bg-gray-900 p-2">
                                <span
                                    class="size-6 rounded-full bg-gray-100 dark:bg-gray-500 text-center text-[10px]/6 font-bold text-white">
                                    3
                                </span>

                                <span class="hidden sm:block"> Finished </span>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- Dropdown for Program -->
                <div class="mt-4">
                    <x-input-label for="program_id" :value="__('Program')" />
                    <select id="program_id" name="program_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}"
                                {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->program_name }} - {{ $program->program_description }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
                </div>

                <!-- Dropdown for Service -->
                <div class="mt-4">
                    <x-input-label for="service_id" :value="__('Service')" />
                    <select id="service_id" name="service_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}"
                                {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->service_name }} - {{ $service->service_description }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                </div>

                <!-- Button for back and submit -->
                <div class="flex justify-between mt-4">
                    <x-secondary-button id="back-btn">Back</x-secondary-button>
                    <x-primary-button id="submitFormButton" class="ml-auto">
                        {{ __('Simpan') }}
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Image preview function
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const previewContainer = document.getElementById('image-preview');
                previewContainer.innerHTML =
                    `<img src="${reader.result}" alt="Image Preview" style="max-width: 200px; max-height: 200px; width: auto; height: auto;" />`;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // Handle step navigation
        document.getElementById('next-btn').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('step-2').style.display = 'block';
        });

        document.getElementById('back-btn').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('step-2').style.display = 'none';
            document.getElementById('step-1').style.display = 'block';
        });

        // Handle form submission confirmation
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah data yang Anda input sudah benar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form after the user confirms
                    document.getElementById('studentForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
