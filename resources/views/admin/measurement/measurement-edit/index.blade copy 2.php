<x-app-layout>
    <x-slot:title>Edit Pengukuran</x-slot:title>

    {{-- Notifikasi Sukses dengan SweetAlert setelah update --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Edit Pengukuran Anak') }}
        </h2>
    </x-slot>

    {{-- Breadcrumb --}}
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
                <a href="{{ route('measurement.show', $measurement) }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300">Detail</span>
                </a>
            </li>
            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180"></span>
                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Edit Pengukuran
                </a>
            </li>
        </ol>
    </nav>

    <div class="max-w-7xl mx-auto sm:px-4 lg:px-8">
        {{-- Card Identitas Siswa --}}
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-lg text-gray-800 dark:text-white mb-4 md:text-start text-center">Identitas
                Siswa</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="flex justify-center sm:justify-start">
                    <img src="{{ $activityTransaction->student->user_photo ? asset('storage/' . $activityTransaction->student->user_photo) : asset('images/profile-1.png') }}"
                        alt="Foto Siswa" class="w-52 h-52 object-cover rounded-lg shadow-md border-4 border-indigo-600">
                </div>
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
                                {{ $activityTransaction->student->birth_date ? \Carbon\Carbon::parse($activityTransaction->student->birth_date)->format('d M Y') : '-' }}
                            </p>
                        </div>
                        <div><strong class="text-gray-600 dark:text-gray-400">Gender:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->gender == 1 || $activityTransaction->student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                            </p>
                        </div>
                        <div>
                            <strong class="text-gray-600 dark:text-gray-400">Umur Saat Pengukuran:</strong>
                            <p id="age-display" class="text-gray-800 dark:text-gray-200 font-medium">Menghitung...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Form Pengukuran --}}
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
            <form id="measurementForm" action="{{ route('measurement.update', $measurement) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="sd_category" id="hidden_sd_category">
                <input type="hidden" name="calculation_results" id="hidden_calculation_results">
                <input type="hidden" name="measurement_results" id="hidden_measurement_results">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="date_measurement"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                            Pengukuran</label>
                        <input type="date" name="date_measurement" id="date_measurement"
                            value="{{ old('date_measurement', $measurement->date_measurement->format('Y-m-d')) }}"
                            required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Posisi
                            Pengukuran</label>
                        <div class="mt-2 flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="measurement_condition" value="berdiri"
                                    id="condition_berdiri"
                                    {{ old('measurement_condition', $measurement->measurement_condition) == 'berdiri' ? 'checked' : '' }}
                                    class="form-radio text-indigo-600 dark:bg-gray-800 dark:border-gray-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Berdiri (TB)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="measurement_condition" value="terlentang"
                                    id="condition_terlentang"
                                    {{ old('measurement_condition', $measurement->measurement_condition) == 'terlentang' ? 'checked' : '' }}
                                    class="form-radio text-indigo-600 dark:bg-gray-800 dark:border-gray-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Terlentang (PB)</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Berat
                            Badan (kg)</label>
                        <input type="number" name="weight" id="weight" step="0.01"
                            value="{{ old('weight', $measurement->weight) }}" required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            id="height_label">Tinggi/Panjang Badan (cm)</label>
                        <input type="number" name="height" id="height" step="0.01"
                            value="{{ old('height', $measurement->height) }}" required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="head_circumference"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lingkar Kepala
                            (cm)</label>
                        <input type="number" name="head_circumference" id="head_circumference" step="0.01"
                            value="{{ old('head_circumference', $measurement->head_circumference) }}" required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="arm_circumference"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lingkar Lengan
                            (cm)</label>
                        <input type="number" name="arm_circumference" id="arm_circumference" step="0.01"
                            value="{{ old('arm_circumference', $measurement->arm_circumference) }}" required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="note_measurement"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                            Pengukuran</label>
                        <textarea name="note_measurement" id="note_measurement" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm">{{ old('note_measurement', $measurement->note_measurement) }}</textarea>
                    </div>
                </div>

                <div id="results-container" class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Hasil Perhitungan Status Gizi
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <h4 class="font-bold text-indigo-600 dark:text-indigo-400">Berat Badan vs Umur (BB/U)</h4>
                            <p><strong>Nilai Z-Score:</strong> <span id="zscore_bbu">-</span></p>
                            <p><strong>Kategori SD:</strong> <span id="sd_bbu">-</span></p>
                            <p><strong>Status Gizi:</strong> <span id="status_bbu" class="font-semibold">-</span></p>
                        </div>
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <h4 id="height_param_label" class="font-bold text-indigo-600 dark:text-indigo-400">Tinggi
                                Badan vs Umur (TB/U)</h4>
                            <p><strong>Nilai Z-Score:</strong> <span id="zscore_tbu">-</span></p>
                            <p><strong>Kategori SD:</strong> <span id="sd_tbu">-</span></p>
                            <p><strong>Status Gizi:</strong> <span id="status_tbu" class="font-semibold">-</span></p>
                        </div>
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <h4 id="weight_height_param_label" class="font-bold text-indigo-600 dark:text-indigo-400">
                                Berat Badan vs Tinggi Badan (TB/BB)</h4>
                            <p><strong>Nilai Z-Score:</strong> <span id="zscore_bbtb">-</span></p>
                            <p><strong>Kategori SD:</strong> <span id="sd_bbtb">-</span></p>
                            <p><strong>Status Gizi:</strong> <span id="status_bbtb" class="font-semibold">-</span></p>
                        </div>
                        <div class="p-4 border rounded-lg dark:border-gray-700">
                            <h4 class="font-bold text-indigo-600 dark:text-indigo-400">Indeks Massa Tubuh vs Umur
                                (IMT/U)</h4>
                            <p><strong>Nilai IMT:</strong> <span id="imt_value">-</span></p>
                            <p><strong>Nilai Z-Score:</strong> <span id="zscore_imtu">-</span></p>
                            <p><strong>Kategori SD:</strong> <span id="sd_imtu">-</span></p>
                            <p><strong>Status Gizi:</strong> <span id="status_imtu" class="font-semibold">-</span></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" id="submitButton"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Update Pengukuran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Referensi Elemen Form ---
            const measurementForm = document.getElementById('measurementForm');
            const weightInput = document.getElementById('weight');
            const heightInput = document.getElementById('height');
            const dateInput = document.getElementById('date_measurement');
            const conditionRadios = document.querySelectorAll('input[name="measurement_condition"]');
            const radioBerdiri = document.getElementById('condition_berdiri');
            const radioTerlentang = document.getElementById('condition_terlentang');
            const ageDisplay = document.getElementById('age-display');

            // --- Data Siswa dari Blade ---
            const studentBirthDate = '{{ $activityTransaction->student->birth_date }}';
            const studentGender =
                '{{ $activityTransaction->student->gender == 1 || $activityTransaction->student->gender === 'male' ? 'male' : 'female' }}';

            // --- Fungsi-Fungsi Helper ---
            function getAgeInMonths(birthDateStr, measurementDateStr) {
                const birthDate = new Date(birthDateStr);
                const measurementDate = new Date(measurementDateStr);
                if (isNaN(birthDate.getTime()) || isNaN(measurementDate.getTime())) return null;
                let age = (measurementDate.getFullYear() - birthDate.getFullYear()) * 12;
                age -= birthDate.getMonth();
                age += measurementDate.getMonth();
                if (measurementDate.getDate() < birthDate.getDate()) {
                    age--;
                }
                return age < 0 ? 0 : age;
            }

            function getFullAge(birthDateStr, measurementDateStr) {
                const birthDate = new Date(birthDateStr);
                const measurementDate = new Date(measurementDateStr);
                if (isNaN(birthDate.getTime()) || isNaN(measurementDate.getTime())) return null;
                let years = measurementDate.getFullYear() - birthDate.getFullYear();
                let months = measurementDate.getMonth() - birthDate.getMonth();
                let days = measurementDate.getDate() - birthDate.getDate();
                if (days < 0) {
                    months--;
                    days += new Date(measurementDate.getFullYear(), measurementDate.getMonth(), 0).getDate();
                }
                if (months < 0) {
                    years--;
                    months += 12;
                }
                return {
                    years,
                    months,
                    days
                };
            }

            function updateAgeDisplay() {
                const age = getFullAge(studentBirthDate, dateInput.value);
                if (age) {
                    ageDisplay.textContent = `${age.years} Tahun, ${age.months} Bulan, ${age.days} Hari`;
                } else {
                    ageDisplay.textContent = 'Tanggal tidak valid';
                }
            }

            function setOptimalCondition() {
                const ageInMonths = getAgeInMonths(studentBirthDate, dateInput.value);
                if (ageInMonths === null) return;
                // Hanya atur jika tidak ada yang dipilih (untuk menghormati data lama)
                if (!document.querySelector('input[name="measurement_condition"]:checked')) {
                    if (ageInMonths < 24) {
                        radioTerlentang.checked = true;
                    } else {
                        radioBerdiri.checked = true;
                    }
                }
            }

            async function fetchGrowthStandards() {
                const weight = parseFloat(weightInput.value);
                const height = parseFloat(heightInput.value);
                const measurementDate = dateInput.value;
                const conditionEl = document.querySelector('input[name="measurement_condition"]:checked');
                if (!conditionEl) return; // Jangan jalankan jika tidak ada radio yang terpilih
                const condition = conditionEl.value;
                const ageInMonths = getAgeInMonths(studentBirthDate, measurementDate);

                if (isNaN(weight) || isNaN(height) || weight <= 0 || height <= 0 || !measurementDate ||
                    ageInMonths === null) {
                    resetResults();
                    return;
                }

                let adjustedHeight = height;
                if (ageInMonths < 24 && condition === 'berdiri') {
                    adjustedHeight = height + 0.7;
                } else if (ageInMonths >= 24 && condition === 'terlentang') {
                    adjustedHeight = height - 0.7;
                }

                document.getElementById('results-container').classList.remove('hidden');
                updateAllTexts('Menghitung...');

                try {
                    const apiUrl =
                        `{{ route('api.growth-standards') }}?gender=${studentGender}&age_months=${ageInMonths}&height=${adjustedHeight}&measurement_condition=${condition}`;
                    const response = await fetch(apiUrl);
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        throw new Error("Server Error. Cek log Laravel.");
                    }
                    const data = await response.json();
                    if (!response.ok) {
                        const firstError = data.errors ? Object.values(data.errors)[0][0] : data.message ||
                            'Error dari server.';
                        throw new Error(firstError);
                    }
                    if (Object.keys(data).length === 0) {
                        updateAllTexts('Data standar tidak ditemukan.');
                        return;
                    }
                    calculateAndDisplayResults(data, weight, adjustedHeight, condition);
                } catch (error) {
                    console.error('Gagal mengambil data standar:', error.message);
                    updateAllTexts(`Gagal: ${error.message}`);
                }
            }

            function calculateAndDisplayResults(standards, weight, height, condition) {
                const results = {};
                const heightParam = condition === 'berdiri' ? 'TB/U' : 'PB/U';
                const weightHeightParam = condition === 'berdiri' ? 'TB/BB' : 'PB/BB';
                const bmi = calculateBMI(weight, height);
                document.getElementById('height_param_label').textContent = condition === 'berdiri' ?
                    'Tinggi Badan vs Umur (TB/U)' : 'Panjang Badan vs Umur (PB/U)';
                document.getElementById('weight_height_param_label').textContent = condition === 'berdiri' ?
                    'Berat Badan vs Tinggi Badan (TB/BB)' : 'Berat Badan vs Panjang Badan (PB/BB)';
                document.getElementById('height_label').textContent = condition === 'berdiri' ?
                    'Tinggi Badan (cm)' : 'Panjang Badan (cm)';
                results['BB/U'] = calculateForParameter(standards['BB/U'], weight);
                results[heightParam] = calculateForParameter(standards[heightParam], height);
                results[weightHeightParam] = calculateForParameter(standards[weightHeightParam], weight);
                results['IMT/U'] = calculateForParameter(standards['IMT/U'], bmi);
                updateUI('bbu', results['BB/U']);
                updateUI('tbu', results[heightParam]);
                updateUI('bbtb', results[weightHeightParam]);
                updateUI('imtu', results['IMT/U'], bmi);
                fillHiddenInputs(results, heightParam, weightHeightParam, bmi);
            }

            function calculateForParameter(standard, value) {
                if (!standard || value === null || isNaN(value)) return {
                    zScore: 'N/A',
                    sdCategory: 'Data standar tidak ada',
                    status: 'Tidak terklasifikasi'
                };
                const median = parseFloat(standard.median);
                const sd_plus_1 = parseFloat(standard.plus_1_sd);
                const sd_minus_1 = parseFloat(standard.minus_1_sd);
                if ((sd_plus_1 - median) === 0 || (median - sd_minus_1) === 0) return {
                    zScore: 'N/A',
                    sdCategory: 'Data standar tidak valid',
                    status: 'Tidak terklasifikasi'
                };
                let zScore = (value >= median) ? (value - median) / (sd_plus_1 - median) : (value - median) / (
                    median - sd_minus_1);
                zScore = zScore.toFixed(2);
                const sdCategory = getSDCategory(zScore);
                const status = getStatus(standard.parameter, zScore);
                return {
                    zScore,
                    sdCategory,
                    status
                };
            }

            function getStatus(parameter, zScore) {
                const z = parseFloat(zScore);
                if (isNaN(z)) return 'Tidak terklasifikasi';
                let mainParam = parameter;
                if (parameter === 'PB/U') mainParam = 'TB/U';
                if (parameter === 'PB/BB' || parameter === 'PB/BB') mainParam = 'TB/BB';
                if (parameter === 'TB/BB') mainParam = 'TB/BB';
                switch (mainParam) {
                    case 'BB/U':
                        if (z < -3) return 'Berat Badan Sangat Kurang';
                        if (z >= -3 && z < -2) return 'Berat Badan Kurang';
                        if (z >= -2 && z <= 1) return 'Berat Badan Normal';
                        if (z > 1) return 'Risiko Berat Badan Lebih';
                        return 'Tidak Terklasifikasi';
                    case 'TB/U':
                        if (z < -3) return 'Sangat Pendek (Severely Stunted)';
                        if (z >= -3 && z < -2) return 'Pendek (Stunted)';
                        if (z >= -2) return 'Normal';
                        return 'Tidak Terklasifikasi';
                    case 'TB/BB':
                        if (z < -3) return 'Gizi Buruk (Severely Wasted)';
                        if (z >= -3 && z < -2) return 'Gizi Kurang (Wasted)';
                        if (z >= -2 && z <= 1) return 'Gizi Baik (Normal)';
                        if (z > 1 && z <= 2) return 'Berisiko Gizi Lebih';
                        if (z > 2 && z <= 3) return 'Gizi Lebih (Overweight)';
                        if (z > 3) return 'Obesitas (Obese)';
                        return 'Tidak Terklasifikasi';
                    case 'IMT/U':
                        if (z < -3) return 'Sangat Kurus (Severely Thinness)';
                        if (z >= -3 && z < -2) return 'Kurus (Thinness)';
                        if (z >= -2 && z <= 1) return 'Normal';
                        if (z > 1 && z <= 2) return 'Berisiko Gemuk';
                        if (z > 2 && z <= 3) return 'Gemuk';
                        if (z > 3) return 'Obesitas';
                        return 'Tidak Terklasifikasi';
                    default:
                        return 'Parameter Tidak Dikenal';
                }
            }

            function getSDCategory(zScore) {
                const z = parseFloat(zScore);
                if (isNaN(z)) return 'N/A';
                if (z > 3) return 'Diatas +3 SD';
                if (z > 2) return 'Antara +2 SD dan +3 SD';
                if (z > 1) return 'Antara +1 SD dan +2 SD';
                if (z >= -1) return 'Antara -1 SD dan +1 SD (Median)';
                if (z >= -2) return 'Antara -2 SD dan -1 SD';
                if (z >= -3) return 'Antara -3 SD dan -2 SD';
                return 'Dibawah -3 SD';
            }

            const calculateBMI = (weight, height) => (height > 0 ? (weight / Math.pow(height / 100, 2)) : 0)
                .toFixed(2);

            function updateUI(prefix, result, bmi = null) {
                document.getElementById(`zscore_${prefix}`).textContent = result.zScore;
                document.getElementById(`sd_${prefix}`).textContent = result.sdCategory;
                document.getElementById(`status_${prefix}`).textContent = result.status;
                if (prefix === 'imtu' && bmi !== null) document.getElementById('imt_value').textContent = bmi;
            }

            function resetResults() {
                if (!document.getElementById('results-container').classList.contains('hidden')) updateAllTexts('-');
            }

            function updateAllTexts(text) {
                ['bbu', 'tbu', 'bbtb', 'imtu'].forEach(p => {
                    document.getElementById(`zscore_${p}`).textContent = text;
                    document.getElementById(`sd_${p}`).textContent = text;
                    document.getElementById(`status_${p}`).textContent = text;
                });
                document.getElementById('imt_value').textContent = text;
            }

            function fillHiddenInputs(results, heightParam, weightHeightParam, bmi) {
                const sdCategories = {
                    'BB/U': results['BB/U']?.sdCategory || null,
                    [heightParam]: results[heightParam]?.sdCategory || null,
                    [weightHeightParam]: results[weightHeightParam]?.sdCategory || null,
                    'IMT/U': results['IMT/U']?.sdCategory || null
                };
                const calcResults = {
                    'BB/U': results['BB/U']?.status || null,
                    [heightParam]: results[heightParam]?.status || null,
                    [weightHeightParam]: results[weightHeightParam]?.status || null,
                    'IMT/U': results['IMT/U']?.status || null
                };
                const measurementResults = {
                    'BB/U': {
                        zScore: results['BB/U']?.zScore || null
                    },
                    [heightParam]: {
                        zScore: results[heightParam]?.zScore || null
                    },
                    [weightHeightParam]: {
                        zScore: results[weightHeightParam]?.zScore || null
                    },
                    'IMT/U': {
                        zScore: results['IMT/U']?.zScore || null,
                        imt: bmi
                    }
                };
                document.getElementById('hidden_sd_category').value = JSON.stringify(sdCategories);
                document.getElementById('hidden_calculation_results').value = JSON.stringify(calcResults);
                document.getElementById('hidden_measurement_results').value = JSON.stringify(measurementResults);
            }

            measurementForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Hentikan submit form otomatis

                Swal.fire({
                    title: 'Konfirmasi Penyimpanan',
                    text: "Apakah Anda yakin ingin menyimpan data pengukuran ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4338CA', // Warna Indigo
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan Data!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    // Jika pengguna menekan tombol "Ya, Simpan Data!"
                    if (result.isConfirmed) {
                        // Lanjutkan proses submit form
                        this.submit();
                    }
                });
            });

            measurementForm.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: "Apakah Anda yakin ingin menyimpan perubahan data ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4338CA',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            dateInput.addEventListener('input', () => {
                updateAgeDisplay();
                setOptimalCondition();
                fetchGrowthStandards();
            });
            [weightInput, heightInput].forEach(el => el.addEventListener('input', fetchGrowthStandards));
            conditionRadios.forEach(radio => radio.addEventListener('change', fetchGrowthStandards));

            // --- Inisialisasi Saat Halaman Dimuat ---
            updateAgeDisplay();
            fetchGrowthStandards(); // <-- PENTING: Jalankan perhitungan otomatis saat halaman dimuat
        });
    </script>
</x-app-layout>
