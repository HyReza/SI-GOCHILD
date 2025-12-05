<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Riwayat Pengukuran Anak') }}
        </h2>
    </x-slot>
    {{-- SweetAlert for Success Message --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
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
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Identitas Siswa -->
            <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-white mb-4">Identitas Siswa</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="flex justify-center sm:justify-start">
                        <img src="{{ $activityTransaction->student->user_photo ? asset('storage/' . $activityTransaction->student->user_photo) : asset('images/profile-1.png') }}"
                            alt="Foto Siswa"
                            class="w-52 h-52 object-cover rounded-lg shadow-md border-4 border-indigo-600">
                    </div>
                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                                {{ \Carbon\Carbon::parse($activityTransaction->student->birth_date)->format('d-m-Y') }}
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
                                $ageInMonths = $birthDate->diffInMonths($now);
                                $years = floor($ageInMonths / 12);
                                $months = $ageInMonths % 12;
                                $days = $birthDate->diffInDays($now) % 30;
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

            <!-- Filter Tanggal (Dalam Card) -->
            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-6 mb-6">
                <h3 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Filter Riwayat Pengukuran</h3>
                <form method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <!-- Input: Tanggal Mulai -->
                        <div class="md:col-span-1">
                            <label for="start_date" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Dari
                                Tanggal</label>
                            <input type="date" name="start_date" id="start_date"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500"
                                value="{{ request('start_date') }}">
                        </div>

                        <!-- Input: Tanggal Akhir -->
                        <div class="md:col-span-1">
                            <label for="end_date" class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Sampai
                                Tanggal</label>
                            <input type="date" name="end_date" id="end_date"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-indigo-500"
                                value="{{ request('end_date') }}">
                        </div>

                        <!-- Tombol Filter -->
                        <div class="md:col-span-1">
                            <label class="block text-sm text-transparent mb-1">.</label>
                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-base">filter_alt</span> Filter
                            </button>
                        </div>

                        <!-- Tombol Reset -->
                        <div class="md:col-span-1">
                            <label class="block text-sm text-transparent mb-1">.</label>
                            <a href="{{ route('measurement.history', $activityTransaction->id) }}"
                                class="w-full text-center bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-md flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-base">restart_alt</span> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>


            <!-- Tabel Riwayat -->
            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Riwayat Pengukuran</h3>
                @if ($measurements->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200">
                            <thead
                                class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-600 dark:text-gray-300">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Berat (kg)</th>
                                    <th class="px-4 py-3">Tinggi (cm)</th>
                                    <th class="px-4 py-3">Lingkar Kepala</th>
                                    <th class="px-4 py-3">Lingkar Lengan</th>
                                    <th class="px-4 py-3">Catatan</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($measurements as $i => $measurement)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:hover:bg-opacity-50 transition">
                                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($measurement->date_measurement)->format('d-m-Y') }}
                                        </td>
                                        <td class="px-4 py-3">{{ $measurement->weight }}</td>
                                        <td class="px-4 py-3">{{ $measurement->height }}</td>
                                        <td class="px-4 py-3">{{ $measurement->head_circumference }}</td>
                                        <td class="px-4 py-3">{{ $measurement->arm_circumference }}</td>
                                        <td class="px-4 py-3">{{ $measurement->note_measurement ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('measurement.show', $measurement->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 transition">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </a>
                                                <a href="{{ route('measurement.edit', $measurement->id) }}"
                                                    class="text-yellow-500 hover:text-yellow-600 transition">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </a>
                                                <form id="delete-form-{{ $measurement->id }}"
                                                    action="{{ route('measurement.destroy', $measurement->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="text-red-600 hover:text-red-700 transition btn-delete"
                                                        data-id="{{ $measurement->id }}"
                                                        data-date="{{ \Carbon\Carbon::parse($measurement->date_measurement)->format('d-m-Y') }}">
                                                        <span class="material-symbols-outlined">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 mt-4">Belum ada data pengukuran.</p>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const measurementId = this.dataset.id;
                    const measurementDate = this.dataset.date;

                    Swal.fire({
                        title: 'Hapus Pengukuran?',
                        text: `Data pengukuran tanggal ${measurementDate} akan dihapus. Lanjutkan?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById(
                                `delete-form-${measurementId}`);
                            if (form) form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
