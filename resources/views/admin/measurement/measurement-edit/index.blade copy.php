<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Edit Pengukuran Anak') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-white mb-4 md:text-start text-center">Identitas
                    Siswa</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Foto Siswa -->
                    <div class="flex justify-center sm:justify-start">
                        <img src="{{ $measurement->activityTransaction->student->user_photo ? asset('storage/' . $measurement->activityTransaction->student->user_photo) : asset('images/profile-1.png') }}"
                            alt="Foto Siswa"
                            class="w-52 h-52 object-cover rounded-lg shadow-md border-4 border-indigo-600">
                    </div>

                    <!-- Informasi Siswa -->
                    <div class="sm:col-span-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <strong class="text-gray-600 dark:text-gray-400">Nama Anak:</strong>
                                <p class="text-gray-800 dark:text-gray-200">
                                    {{ $measurement->activityTransaction->student->student_name }}</p>
                            </div>
                            <div>
                                <strong class="text-gray-600 dark:text-gray-400">NIS:</strong>
                                <p class="text-gray-800 dark:text-gray-200">
                                    {{ $measurement->activityTransaction->student->student_number }}</p>
                            </div>
                            <div>
                                <strong class="text-gray-600 dark:text-gray-400">Tanggal Lahir:</strong>
                                <p class="text-gray-800 dark:text-gray-200">
                                    {{ $measurement->activityTransaction->student->birth_date ? \Carbon\Carbon::parse($measurement->activityTransaction->student->birth_date)->format('d-m-Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <strong class="text-gray-600 dark:text-gray-400">Nama Ibu:</strong>
                                <p class="text-gray-800 dark:text-gray-200">
                                    {{ $measurement->activityTransaction->student->mother_name }}</p>
                            </div>
                            <div>
                                <strong class="text-gray-600 dark:text-gray-400">Umur:</strong>
                                @php
                                    $birthDate = \Carbon\Carbon::parse(
                                        $measurement->activityTransaction->student->birth_date,
                                    );
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
                                    {{ $measurement->activityTransaction->student->street }},
                                    {{ $measurement->activityTransaction->student->village }},
                                    {{ $measurement->activityTransaction->student->subdistrict }},
                                    {{ $measurement->activityTransaction->student->district }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-8">
                <h3
                    class="text-xl font-semibold text-gray-800 dark:text-white mb-6 border-b border-gray-300 dark:border-gray-700 pb-2">
                    Form Edit Pengukuran
                </h3>

                <form method="POST" action="{{ route('measurement.update', $measurement->id) }}"
                    id="formEditMeasurement">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="date_measurement"
                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Tanggal Pengukuran</label>
                            <input type="date" id="date_measurement" name="date_measurement"
                                value="{{ old('date_measurement', $measurement->date_measurement) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500 @error('date_measurement') border-red-500 @enderror">
                            @error('date_measurement')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="weight" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Berat
                                Badan (kg)</label>
                            <input type="number" step="0.01" id="weight" name="weight"
                                value="{{ old('weight', $measurement->weight) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500 @error('weight') border-red-500 @enderror">
                            @error('weight')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="height"
                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Tinggi/Panjang Badan
                                (cm)</label>
                            <input type="number" step="0.01" id="height" name="height"
                                value="{{ old('height', $measurement->height) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500 @error('height') border-red-500 @enderror">
                            @error('height')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="head_circumference"
                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Lingkar Kepala (cm)</label>
                            <input type="number" step="0.01" id="head_circumference" name="head_circumference"
                                value="{{ old('head_circumference', $measurement->head_circumference) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500 @error('head_circumference') border-red-500 @enderror">
                            @error('head_circumference')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="arm_circumference"
                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Lingkar Lengan (cm)</label>
                            <input type="number" step="0.01" id="arm_circumference" name="arm_circumference"
                                value="{{ old('arm_circumference', $measurement->arm_circumference) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500 @error('arm_circumference') border-red-500 @enderror">
                            @error('arm_circumference')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="note_measurement"
                                class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                            <textarea id="note_measurement" name="note_measurement" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500 @error('note_measurement') border-red-500 @enderror">{{ old('note_measurement', $measurement->note_measurement) }}</textarea>
                            @error('note_measurement')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('measurement.history', $measurement->activityTransaction->id) }}"
                            class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-md shadow-sm">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById('formEditMeasurement');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan semua data sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>
