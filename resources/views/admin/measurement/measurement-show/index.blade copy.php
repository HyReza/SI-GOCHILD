<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Detail Pengukuran Anak') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @php
                $student = $measurement->activityTransaction->student;
                $birthDate = \Carbon\Carbon::parse($student->birth_date);
                $now = \Carbon\Carbon::now();
                $ageNow = $birthDate->diff($now);
                $dateMeasured = \Carbon\Carbon::parse($measurement->date_measurement);
                $ageMeasured = $birthDate->diff($dateMeasured);
            @endphp

            <!-- Card: Identitas Anak -->
            <div class="bg-white dark:bg-gray-900 shadow-md rounded-xl p-8">
                <h3
                    class="text-2xl font-semibold text-gray-800 dark:text-white mb-6 border-b border-gray-300 dark:border-gray-700 pb-2">
                    Identitas Anak
                </h3>
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <div class="flex-shrink-0">
                        <img src="{{ $student->user_photo ? asset('storage/' . $student->user_photo) : asset('images/profile-1.png') }}"
                            alt="Foto Siswa"
                            class="w-56 h-56 object-cover aspect-square rounded-lg border-4 border-indigo-500 shadow-md">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-300">Nama Anak</span>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $student->student_name }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-300">NIS</span>
                            <p class="text-base font-medium text-gray-900 dark:text-white">
                                {{ $student->student_number }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-300">Tanggal Lahir</span>
                            <p class="text-base font-medium text-gray-900 dark:text-white">
                                {{ $birthDate->format('d-m-Y') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-300">Nama Ibu</span>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $student->mother_name }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-300">Usia Saat Ini</span>
                            <p class="text-base font-medium text-gray-900 dark:text-white">
                                {{ $ageNow->y }} Tahun, {{ $ageNow->m }} Bulan, {{ $ageNow->d }} Hari
                            </p>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-sm text-gray-500 dark:text-gray-300">Alamat</span>
                            <p class="text-base font-medium text-gray-900 dark:text-white">
                                {{ $student->street }}, {{ $student->village }}, {{ $student->subdistrict }},
                                {{ $student->district }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Data Pengukuran -->
            <div class="bg-white dark:bg-gray-900 shadow-md rounded-xl p-8 mt-8">
                <h3
                    class="text-2xl font-semibold text-gray-800 dark:text-white mb-6 border-b border-gray-300 dark:border-gray-700 pb-2">
                    Data Pengukuran
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-300">Tanggal Pengukuran</span>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $dateMeasured->format('d-m-Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-300">Usia Saat Pengukuran</span>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $ageMeasured->y }} Tahun, {{ $ageMeasured->m }} Bulan, {{ $ageMeasured->d }} Hari
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-300">Berat Badan (kg)</span>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ $measurement->weight }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-300">Tinggi/Panjang Badan (cm)</span>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ $measurement->height }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-300">Lingkar Kepala (cm)</span>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $measurement->head_circumference }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-300">Lingkar Lengan (cm)</span>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $measurement->arm_circumference }}</p>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <span class="text-sm text-gray-500 dark:text-gray-300">Catatan Pemeriksaan</span>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $measurement->note_measurement ?? '-' }}
                        </p>
                    </div>
                </div>
                <!-- Tombol Kembali -->
                <div class="text-end mt-8">
                    <a href="{{ route('measurement.history', $measurement->activityTransaction->id) }}"
                        class="inline-block bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-5 py-2 rounded-md shadow-sm transition">
                        ← Kembali
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
