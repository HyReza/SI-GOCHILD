<x-app-layout>
    <x-slot:title>Detail Pengukuran</x-slot:title>

    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Detail Hasil Pengukuran') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('measurement.edit', $measurement) }}"
                    class="inline-flex items-center gap-2 bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-600 shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 3.732z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('measurement.history', $measurement->activityTransaction) }}"
                    class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600 shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Riwayat
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $student = $measurement->activityTransaction->student;
        $birthDate = \Carbon\Carbon::parse($student->birth_date);
        $measurementDate = $measurement->date_measurement;
        $age = $birthDate->diff($measurementDate);
        $ageInMonths = $birthDate->diffInMonths($measurementDate);
    @endphp

    <div class="max-w-7xl mx-auto sm:px-4 lg:px-8 space-y-6">
        {{-- Card Identitas Siswa --}}
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-800 dark:text-white mb-4">Identitas Siswa</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="flex justify-center sm:justify-start">
                    <img src="{{ $student->user_photo ? asset('storage/' . $student->user_photo) : asset('images/profile-1.png') }}"
                        alt="Foto Siswa" class="w-52 h-52 object-cover rounded-lg shadow-md border-4 border-indigo-600">
                </div>
                <div class="sm:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <strong class="text-gray-500 dark:text-gray-400 block">Nama Anak</strong>
                            <p class="text-gray-800 dark:text-gray-200">{{ $student->student_name }}</p>
                        </div>
                        <div>
                            <strong class="text-gray-500 dark:text-gray-400 block">NIS</strong>
                            <p class="text-gray-800 dark:text-gray-200">{{ $student->student_number }}</p>
                        </div>
                        <div>
                            <strong class="text-gray-500 dark:text-gray-400 block">Tanggal Lahir</strong>
                            <p class="text-gray-800 dark:text-gray-200">{{ $birthDate->format('d M Y') }}</p>
                        </div>
                        <div>
                            <strong class="text-gray-500 dark:text-gray-400 block">Gender</strong>
                            <p class="text-gray-800 dark:text-gray-200">
                                {{ $student->gender == 1 || $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                            </p>
                        </div>
                        <div class="sm:col-span-2">
                            <strong class="text-gray-500 dark:text-gray-400 block">Umur Saat Pengukuran</strong>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold text-base">{{ $age->y }}
                                Tahun,
                                {{ $age->m }} Bulan, {{ $age->d }} Hari</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Data Pengukuran --}}
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-800 dark:text-white mb-4">Data Pengukuran</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-y-4 gap-x-6 text-sm">
                <div>
                    <strong class="text-gray-500 dark:text-gray-400 block">Tanggal Pengukuran</strong>
                    <p class="text-gray-800 dark:text-gray-200">{{ $measurementDate->format('d M Y') }}</p>
                </div>
                <div>
                    <strong class="text-gray-500 dark:text-gray-400 block">Berat Badan</strong>
                    <p class="text-gray-800 dark:text-gray-200">{{ $measurement->weight }} kg</p>
                </div>
                <div>
                    <strong class="text-gray-500 dark:text-gray-400 block">Tinggi/Panjang Badan</strong>
                    <p class="text-gray-800 dark:text-gray-200">{{ $measurement->height }} cm</p>
                </div>
                <div>
                    <strong class="text-gray-500 dark:text-gray-400 block">Posisi Saat Diukur</strong>
                    <p class="text-gray-800 dark:text-gray-200 capitalize">{{ $measurement->measurement_condition }}
                    </p>
                </div>
                <div>
                    <strong class="text-gray-500 dark:text-gray-400 block">Lingkar Kepala</strong>
                    <p class="text-gray-800 dark:text-gray-200">{{ $measurement->head_circumference }} cm</p>
                </div>
                <div>
                    <strong class="text-gray-500 dark:text-gray-400 block">Lingkar Lengan</strong>
                    <p class="text-gray-800 dark:text-gray-200">{{ $measurement->arm_circumference }} cm</p>
                </div>
                <div class="col-span-2 sm:col-span-3 md:col-span-4">
                    <strong class="text-gray-500 dark:text-gray-400 block">Catatan Pengukuran</strong>
                    <p class="text-gray-800 dark:text-gray-200 italic">
                        {{ $measurement->note_measurement ?: 'Tidak ada catatan.' }}</p>
                </div>
                <div>
                    <strong class="text-gray-500 dark:text-gray-400 block">Diukur oleh</strong>
                    <p class="text-gray-800 dark:text-gray-200">{{ $measurement->user->user_name ?? 'N/A' }}</p>
                </div>
            </div>

            @php
                $adjustmentNote = '';
                if ($ageInMonths < 24 && $measurement->measurement_condition == 'berdiri') {
                    $adjustedHeight = $measurement->height + 0.7;
                    $adjustmentNote =
                        'Anak diukur dalam posisi berdiri di bawah usia 2 tahun. Untuk perhitungan status gizi, tinggi badan dikonversi menjadi panjang badan dengan menambahkan 0.7 cm (menjadi ' .
                        number_format($adjustedHeight, 2) .
                        ' cm).';
                } elseif ($ageInMonths >= 24 && $measurement->measurement_condition == 'terlentang') {
                    $adjustedHeight = $measurement->height - 0.7;
                    $adjustmentNote =
                        'Anak diukur dalam posisi terlentang di atas usia 2 tahun. Untuk perhitungan status gizi, panjang badan dikonversi menjadi tinggi badan dengan mengurangi 0.7 cm (menjadi ' .
                        number_format($adjustedHeight, 2) .
                        ' cm).';
                }
            @endphp

            @if ($adjustmentNote)
                <div class="mt-4 border-t dark:border-gray-700 pt-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.636-1.21 2.37-1.21 3.006 0l5.414 10.33c.636 1.21-.29 2.77-1.503 2.77H4.346c-1.213 0-2.139-1.56-1.503-2.77l5.414-10.33zM9 12a1 1 0 112 0 1 1 0 01-2 0zm1-4a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <strong>Catatan Koreksi:</strong> {{ $adjustmentNote }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Card Hasil Analisis Status Gizi --}}
        <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-800 dark:text-white mb-4">Hasil Analisis Status Gizi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $parameters = [
                        'BB/U' => 'Berat Badan vs Umur',
                        'TB/U' => 'Tinggi Badan vs Umur',
                        'PB/U' => 'Panjang Badan vs Umur',
                        'TB/BB' => 'Berat Badan vs Tinggi Badan',
                        'PB/BB' => 'Berat Badan vs Panjang Badan',
                        'IMT/U' => 'Indeks Massa Tubuh vs Umur',
                    ];

                    $getArray = function ($data) {
                        if (is_array($data)) {
                            return $data;
                        }
                        if (is_string($data)) {
                            $decoded = json_decode($data, true);
                            return is_array($decoded) ? $decoded : [];
                        }
                        return [];
                    };

                    $calcResults = $getArray($measurement->calculation_results);
                    $measureResults = $getArray($measurement->measurement_results);
                    $sdCategory = $getArray($measurement->sd_category);
                @endphp

                @forelse ($calcResults as $key => $status)
                    @if (array_key_exists($key, $parameters))
                        @php
                            $label = $parameters[$key];
                            $z_score = $measureResults[$key]['zScore'] ?? 'N/A';
                            $sd_cat = $sdCategory[$key] ?? 'N/A';
                            $imt_value = $measureResults[$key]['imt'] ?? null;

                            $status_color_class = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                            if (Str::contains($status, ['Sangat Kurang', 'Gizi Buruk', 'Sangat Pendek', 'Severely'])) {
                                $status_color_class = 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300';
                            } elseif (Str::contains($status, ['Kurang', 'Pendek', 'Wasted', 'Thin'])) {
                                $status_color_class =
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300';
                            } elseif (Str::contains($status, ['Normal', 'Baik'])) {
                                $status_color_class =
                                    'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300';
                            } elseif (Str::contains($status, ['Risiko', 'Berisiko'])) {
                                $status_color_class =
                                    'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300';
                            } elseif (Str::contains($status, ['Lebih', 'Gemuk', 'Obesitas'])) {
                                $status_color_class =
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300';
                            }
                        @endphp

                        <div class="p-4 border rounded-lg dark:border-gray-700 space-y-2 bg-gray-50/50 dark:bg-gray-800/50">
                            <h4 class="font-bold text-indigo-600 dark:text-indigo-400">{{ $label }}
                                ({{ $key }})
                            </h4>
                            <div class="text-sm space-y-1">
                                @if ($imt_value)
                                    <p><strong>Nilai IMT:</strong> <span class="font-mono">{{ $imt_value }}</span>
                                    </p>
                                @endif
                                <p><strong>Nilai Z-Score:</strong> <span class="font-mono">{{ $z_score }}</span>
                                </p>
                                <p><strong>Kategori SD:</strong> {{ $sd_cat }}</p>
                                <p class="flex items-center gap-2"><strong>Status Gizi:</strong>
                                    <span
                                        class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $status_color_class }}">
                                        {{ $status }}
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700/60 pt-1.5 mt-1">
                                    <strong>Rentang Acuan WHO:</strong> -2.00 SD s.d. {{ in_array($key, ['TB/U', 'PB/U']) ? '+2.00 SD' : '+1.00 SD' }} (Normal)
                                </p>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-1 md:col-span-2 text-center text-gray-500 py-4">
                        <p>Tidak ada data hasil analisis yang tersimpan untuk pengukuran ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
