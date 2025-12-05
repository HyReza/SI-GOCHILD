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

                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar Siswa </span>
                </a>
            </li>

            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180">
                </span>

                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Form Layanan Posyandu
                </a>
            </li>
        </ol>
    </nav>
    <div class="max-w-7xl mx-auto sm:px-4 lg:px-8">
        <!-- Card Pembungkus Identitas Siswa -->
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
                        <div>
                            <strong class="text-gray-600 dark:text-gray-400">Nama Anak:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->student_name }}</p>
                        </div>
                        <div>
                            <strong class="text-gray-600 dark:text-gray-400">NIS:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->student_number }}</p>
                        </div>
                        <div>
                            <strong class="text-gray-600 dark:text-gray-400">Tanggal Lahir:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->birth_date ? \Carbon\Carbon::parse($activityTransaction->student->birth_date)->format('d-m-Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <strong class="text-gray-600 dark:text-gray-400">Nama Ibu:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->mother_name }}</p>
                        </div>
                        <div>
                            <strong class="text-gray-600 dark:text-gray-400">Umur:</strong>
                            @php
                                $birthDate = \Carbon\Carbon::parse($activityTransaction->student->birth_date);
                                $now = \Carbon\Carbon::now();
                                $ageInMonths = $birthDate->diffInMonths($now); // Umur dalam bulan
                                $years = floor($ageInMonths / 12); // Menghitung tahun
                                $months = $ageInMonths % 12; // Menghitung bulan
                                $days = $birthDate->diffInDays($now) % 30; // Menghitung sisa hari
                            @endphp
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $years }} Tahun, {{ $months }} Bulan, {{ $days }} Hari
                            </p>
                        </div>

                        <div>
                            <strong class="text-gray-600 dark:text-gray-400">Alamat:</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $activityTransaction->student->street }},
                                {{ $activityTransaction->student->village }},
                                {{ $activityTransaction->student->subdistrict }},
                                {{ $activityTransaction->student->district }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Form Pengukuran -->
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
            <form id="measurementForm" action="{{ route('measurement.store') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="mb-4">
                        <ul class="list-disc text-red-500">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <input type="hidden" name="activity_transaction_id" value="{{ $activityTransaction->id }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="student_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Anak</label>
                        <input type="text" name="student_name" id="student_name"
                            value="{{ $activityTransaction->student->student_name }}"
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            disabled>
                    </div>

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

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Berat
                            Badan
                            (kg)</label>
                        <input type="number" name="weight" id="weight" step="0.01" value="{{ old('weight') }}"
                            required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('weight') border-red-500 @enderror"
                            placeholder="Contoh: 15.5">
                        @error('weight')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @if ($ageInMonths <= 18)
                                Panjang Badan (cm)
                            @else
                                Tinggi Badan (cm)
                            @endif
                        </label>
                        <input type="number" name="height" id="height" step="0.01" value="{{ old('height') }}"
                            required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('height') border-red-500 @enderror"
                            placeholder="Contoh: 110.5">
                        @error('height')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="head_circumference"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lingkar Kepala
                            (cm)</label>
                        <input type="number" name="head_circumference" id="head_circumference" step="0.01"
                            value="{{ old('head_circumference') }}" required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('head_circumference') border-red-500 @enderror"
                            placeholder="Contoh: 46.2">
                        @error('head_circumference')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="arm_circumference"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lingkar Lengan
                            (cm)</label>
                        <input type="number" name="arm_circumference" id="arm_circumference" step="0.01"
                            value="{{ old('arm_circumference') }}" required
                            class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('arm_circumference') border-red-500 @enderror"
                            placeholder="Contoh: 18.5">
                        @error('arm_circumference')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

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
</x-app-layout>

<script>
    document.getElementById('measurementForm').addEventListener('submit', function(event) {
        event.preventDefault();
        let form = this;

        // SweetAlert Confirmation
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data pengukuran ini akan disimpan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Tidak',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menyimpan data...',
                    text: 'Tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                form.submit();
            }
        });
    });
</script>
