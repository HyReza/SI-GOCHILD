<x-app-layout>
    <x-slot:title>Edit Siswa</x-slot:title>

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
                    Edit Siswa
                </a>
            </li>
        </ol>
    </nav>

    <div
        class="p-8 bg-white dark:bg-gray-900 dark:text-gray-200 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <form id="studentForm" method="POST" action="{{ route('siswa.update', $student->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Hidden Input for Tanggal Masuk -->
            <input type="hidden" name="tanggal_masuk" value="{{ $student->tanggal_masuk }}">

            <!-- NIS -->
            <div>
                <x-input-label for="no_induk" :value="__('Nomer Induk Sekolah')" />
                <x-text-input id="no_induk" class="block mt-1 w-full" type="text" name="no_induk" :value="$student->no_induk"
                    readonly />
                <x-input-error :messages="$errors->get('no_induk')" class="mt-2" />
            </div>

            <!-- NIK -->
            <div class="mt-4">
                <div class="flex">
                    <x-input-label for="nik" :value="__('NIK')" />
                    <span class="text-xs content-center text-gray-500 mx-1">*opsional</span>
                </div>
                <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik', $student->nik)"
                    autocomplete="nik" />
                <x-input-error :messages="$errors->get('nik')" class="mt-2" />
            </div>

            <!-- Nama -->
            <div class="mt-4">
                <x-input-label for="nama" :value="__('Nama')" />
                <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama', $student->nama)"
                    required autofocus autocomplete="nama" />
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>

            <!-- Nama Panggilan -->
            <div class="mt-4">
                <x-input-label for="nama_panggilan" :value="__('Nama Panggilan')" />
                <x-text-input id="nama_panggilan" class="block mt-1 w-full" type="text" name="nama_panggilan"
                    :value="old('nama_panggilan', $student->nama_panggilan)" required autocomplete="nama_panggilan" />
                <x-input-error :messages="$errors->get('nama_panggilan')" class="mt-2" />
            </div>

            <!-- Pilihan Gender -->
            <div class="mt-4">
                <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                <div class="flex items-center mt-2">
                    <input id="gender_pria" type="radio" name="gender" value="1"
                        class="mr-2 dark:text-gray-300 dark:bg-gray-800"
                        {{ old('gender', $student->gender) == '1' ? 'checked' : '' }}>
                    <label for="gender_pria" class="mr-4 dark:text-gray-300">Pria</label>

                    <input id="gender_wanita" type="radio" name="gender" value="0"
                        class="mr-2 dark:text-gray-300 dark:bg-gray-800"
                        {{ old('gender', $student->gender) == '0' ? 'checked' : '' }}>
                    <label for="gender_wanita" class="dark:text-gray-300">Wanita</label>
                </div>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>

            <!-- Tempat Lahir -->
            <div class="mt-4">
                <x-input-label for="tempat_lahir" :value="__('Tempat Lahir')" />
                <x-text-input id="tempat_lahir" class="block mt-1 w-full" type="text" name="tempat_lahir"
                    :value="old('tempat_lahir', $student->tempat_lahir)" required autocomplete="tempat_lahir" />
                <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
            </div>

            <!-- Tanggal Lahir -->
            <div class="mt-4">
                <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                <x-text-input id="tanggal_lahir" class="block mt-1 w-full" type="date" name="tanggal_lahir"
                    :value="old('tanggal_lahir', $student->tanggal_lahir)" required autocomplete="tanggal_lahir" />
                <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
            </div>

            <!-- Nama Ayah -->
            <div class="mt-4">
                <x-input-label for="nama_ayah" :value="__('Nama Ayah')" />
                <x-text-input id="nama_ayah" class="block mt-1 w-full" type="text" name="nama_ayah"
                    :value="old('nama_ayah', $student->nama_ayah)" required autocomplete="nama_ayah" />
                <x-input-error :messages="$errors->get('nama_ayah')" class="mt-2" />
            </div>

            <!-- Nama Ibu -->
            <div class="mt-4">
                <x-input-label for="nama_ibu" :value="__('Nama Ibu')" />
                <x-text-input id="nama_ibu" class="block mt-1 w-full" type="text" name="nama_ibu"
                    :value="old('nama_ibu', $student->nama_ibu)" required autocomplete="nama_ibu" />
                <x-input-error :messages="$errors->get('nama_ibu')" class="mt-2" />
            </div>

            <!-- Jalan -->
            <div class="mt-4">
                <x-input-label for="jalan" :value="__('Jalan')" />
                <x-text-input id="jalan" class="block mt-1 w-full" type="text" name="jalan"
                    :value="old('jalan', $student->jalan)" required autocomplete="jalan" />
                <x-input-error :messages="$errors->get('jalan')" class="mt-2" />
            </div>

            <!-- Desa -->
            <div class="mt-4">
                <x-input-label for="desa" :value="__('Desa / Kelurahan')" />
                <x-text-input id="desa" class="block mt-1 w-full" type="text" name="desa"
                    :value="old('desa', $student->desa)" required autocomplete="desa" />
                <x-input-error :messages="$errors->get('desa')" class="mt-2" />
            </div>

            <!-- Kecamatan -->
            <div class="mt-4">
                <x-input-label for="kecamatan" :value="__('Kecamatan')" />
                <x-text-input id="kecamatan" class="block mt-1 w-full" type="text" name="kecamatan"
                    :value="old('kecamatan', $student->kecamatan)" required autocomplete="kecamatan" />
                <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
            </div>

            <!-- Kabupaten -->
            <div class="mt-4">
                <x-input-label for="kabupaten" :value="__('Kabupaten')" />
                <x-text-input id="kabupaten" class="block mt-1 w-full" type="text" name="kabupaten"
                    :value="old('kabupaten', $student->kabupaten)" required autocomplete="kabupaten" />
                <x-input-error :messages="$errors->get('kabupaten')" class="mt-2" />
            </div>

            <!-- Nomor Telepon -->
            <div class="mt-4">
                <x-input-label for="no_telephone" :value="__('Nomor Telepon')" />
                <x-text-input id="no_telephone" class="block mt-1 w-full" type="text" name="no_telephone"
                    :value="old('no_telephone', $student->no_telephone)" required autocomplete="no_telephone" />
                <x-input-error :messages="$errors->get('no_telephone')" class="mt-2" />
            </div>


            <!-- Pilih Service -->
            <div class="mt-4">
                <x-input-label for="service_id" :value="__('Service')" />
                <select id="service_id" name="service_id"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}"
                            {{ old('service_id', $student->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->nama_service }} - {{ $service->deskripsi_service }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
            </div>

            <!-- pilih Program -->
            <div class="mt-4">
                <x-input-label for="program_id" :value="__('Program')" />
                <select id="program_id" name="program_id"
                    class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}"
                            {{ old('program_id', $student->program_id) == $program->id ? 'selected' : '' }}>
                            {{ $program->nama_program }} - {{ $program->deskripsi_program }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Konfirmasi Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Foto -->
            <div class="mt-4">
                <x-input-label for="foto_user" :value="__('Foto Siswa')" />
                <input id="foto_user" type="file" name="foto_user"
                    class="block mt-1 w-full dark:text-gray-300 dark:bg-gray-800" onchange="previewImage(event)">
                <x-input-error :messages="$errors->get('foto_user')" class="mt-2" />
                <div class="mt-2">
                    <img id="foto_user_preview" src="{{ asset('storage/' . $student->foto_user) }}" alt="Foto Siswa"
                        class="w-32 h-32 object-cover rounded">
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ml-4" @click="loading = true" id="submit-button">
                    {{ __('Perbarui') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('foto_user_preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }


        document.getElementById('studentForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah anda yakin akan mengubah data ?",
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
                    this.submit();
                }
            });
        });
    </script>
</x-app-layout>
