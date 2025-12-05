<!-- resources/views/measurement/form.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Form Pengukuran Anak') }}
        </h2>
    </x-slot>

    <nav aria-label="Breadcrumb" class="flex sm:px-4 lg:px-8 mb-6">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('measurement.index') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300">Daftar Pengukuran</span>
                </a>
            </li>

            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180"></span>
                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">Form
                    Pengukuran Anak</a>
            </li>
        </ol>
    </nav>

    <div class="max-w-7xl mx-auto sm:px-4 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-lg text-gray-800 dark:text-white mb-4 md:text-start text-center">Identitas
                Siswa</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Foto Siswa -->
                <div class="flex justify-center sm:justify-start">
                    <img src="{{ $activityTransaction->student->user_photo ? asset('storage/' . $activityTransaction->student->user_photo) : asset('images/profile-1.png') }}"
                        alt="Foto Siswa" class="w-52 h-52 object-cover rounded-lg shadow-md border-4 border-indigo-600">
                </div>

                <!-- Informasi Siswa -->
                <div class="sm:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div><strong class="text-gray-600 dark:text-gray-400">Nama Anak:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->student_name }}</p>
                        </div>
                        <div><strong class="text-gray-600 dark:text-gray-400">NIS:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->student_number }}</p>
                        </div>
                        <div><strong class="text-gray-600 dark:text-gray-400">Tanggal Lahir:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->birth_date ? \Carbon\Carbon::parse($activityTransaction->student->birth_date)->format('d-m-Y') : '-' }}
                            </p>
                        </div>
                        <div><strong class="text-gray-600 dark:text-gray-400">Nama Ibu:</strong>
                            <p class="text-gray-800 dark:text-gray-200">{{ $activityTransaction->student->mother_name }}
                            </p>
                        </div>
                        <div><strong class="text-gray-600 dark:text-gray-400">Umur:</strong>
                            @php
                                $birthDate = \Carbon\Carbon::parse($activityTransaction->student->birth_date);
                                $now = \Carbon\Carbon::now();
                                $ageInMonths = $birthDate->diffInMonths($now); // Umur dalam bulan
                                $years = floor($ageInMonths / 12); // Menghitung tahun
                                $months = $ageInMonths % 12; // Menghitung bulan
                                $days = $birthDate->diffInDays($now) % 30; // Menghitung sisa hari
                            @endphp
                            <p class="text-gray-800 dark:text-gray-200">{{ $years }} Tahun, {{ $months }}
                                Bulan, {{ $days }} Hari</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Form Pengukuran -->
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
            <form id="measurementForm" action="{{ route('measurement.calculate') }}" method="POST">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @csrf
                    <input type="hidden" name="activity_transaction_id" value="{{ $activityTransaction->id }}">
                    <input type="hidden" id="gender" name="gender"
                        value="{{ $activityTransaction->student->gender == 1 ? 'male' : 'female' }}">

                    <!-- Nama Anak -->
                    <div>
                        <label for="student_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Anak</label>
                        <input type="text" name="student_name" id="student_name"
                            value="{{ $activityTransaction->student->student_name }}"
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            disabled>
                    </div>

                    <!-- Tanggal Pengukuran -->
                    <div>
                        <label for="date_measurement"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                            Pengukuran</label>
                        <input type="date" name="date_measurement" id="date_measurement"
                            value="{{ old('date_measurement', now()->toDateString()) }}" required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('date_measurement') border-red-500 @enderror">
                        @error('date_measurement')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Posisi Pengukuran -->
                    <div>
                        <label for="measurement_position"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Posisi Pengukuran</label>
                        <select name="measurement_position" id="measurement_position"
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="standing">Berdiri</option>
                            <option value="lying_down">Terlentang</option>
                        </select>
                    </div>

                    <!-- Berat Badan -->
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Berat
                            Badan (kg)</label>
                        <input type="number" name="weight" id="weight" step="0.01" value="{{ old('weight') }}"
                            required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('weight') border-red-500 @enderror"
                            placeholder="Contoh: 15.5" oninput="calculateResults()">
                        @error('weight')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tinggi Badan -->
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            id="height_label">Tinggi Badan (cm)</label>
                        <input type="number" name="height" id="height" step="0.01" value="{{ old('height') }}"
                            required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('height') border-red-500 @enderror"
                            placeholder="Contoh: 110.5" oninput="calculateResults()">
                        @error('height')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan Pengukuran -->
                    <div class="sm:col-span-2">
                        <label for="note_measurement"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                            Pengukuran</label>
                        <textarea name="note_measurement" id="note_measurement" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('note_measurement') border-red-500 @enderror"
                            placeholder="Contoh: Anak tampak sehat, berat badan naik dengan baik.">{{ old('note_measurement') }}</textarea>
                        @error('note_measurement')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hasil Kategori SD untuk Semua Parameter -->
                    <div class="sm:col-span-2">
                        <label for="sd_category_all"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori SD BB/U, TB/U,
                            PB/U, IMT/U, PB/BB, TB/BB</label>
                        <textarea name="sd_category_all" id="sd_category_all" readonly
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>

                    <!-- Hasil Perhitungan Gizi untuk Semua Parameter -->
                    <div class="sm:col-span-2">
                        <label for="nutrition_status_all"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hasil Perhitungan Gizi
                            BB/U, TB/U, PB/U, IMT/U, PB/BB, TB/BB</label>
                        <textarea name="nutrition_status_all" id="nutrition_status_all" readonly
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>

                </div>

                <div class="mt-6">
                    <button type="submit" id="submitButton"
                        class="w-full bg-indigo-600 dark:bg-indigo-700 text-white py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Simpan Pengukuran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function calculateResults() {
            let weight = parseFloat(document.getElementById('weight').value);
            let height = parseFloat(document.getElementById('height').value);
            const gender = document.getElementById('gender').value;
            const ageMonths = Math.floor({{ $ageInMonths }}); // Membulatkan umur menjadi bilangan bulat
            const measurementPosition = document.getElementById('measurement_position').value; // Posisi pengukuran

            // Menyesuaikan tinggi badan berdasarkan umur dan posisi pengukuran
            if (ageMonths <= 24) {
                if (measurementPosition === 'standing') {
                    height += 0.7; // Tambahkan 0.7 cm jika umur 24 bulan atau kurang dan posisi berdiri
                } else if (measurementPosition === 'lying_down') {
                    height -= 0.7; // Kurangi 0.7 cm jika umur 24 bulan atau kurang dan posisi terlentang
                }
            }

            // Kirim data ke backend untuk menghitung SD dan status gizi
            fetch(`/measurement/calculate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        age_months: ageMonths,
                        gender: gender,
                        parameter: measurementPosition === 'standing' ? 'TB/U' :
                        'PB/U', // Pilih parameter berdasarkan posisi
                        value: measurementPosition === 'standing' ? height : weight,
                        measurement_position: measurementPosition,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    // Update kategori SD dan hasil gizi untuk semua parameter
                    document.getElementById('sd_category_all').value =
                        `BB/U: ${data.sd_category_BB_U}\nTB/U: ${data.sd_category_TB_U}\nPB/U: ${data.sd_category_PB_U}\nIMT/U: ${data.sd_category_IMT_U}\nPB/BB: ${data.sd_category_PB_BB}\nTB/BB: ${data.sd_category_TB_BB}`;
                    document.getElementById('nutrition_status_all').value =
                        `BB/U: ${data.nutritional_status_BB_U}\nTB/U: ${data.nutritional_status_TB_U}\nPB/U: ${data.nutritional_status_PB_U}\nIMT/U: ${data.nutritional_status_IMT_U}\nPB/BB: ${data.nutritional_status_PB_BB}\nTB/BB: ${data.nutritional_status_TB_BB}`;
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</x-app-layout>
