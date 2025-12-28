<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Langkah 1: Pilih Periode Raport') }}
            </h2>
            <nav class="text-sm font-medium text-gray-500">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <span class="text-indigo-600 font-bold">1. Periode</span>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path
                                d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                        </svg>
                    </li>
                    <li class="flex items-center">
                        <span>2. Penilaian & Review</span>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan Error Validasi (Server Side) --}}
            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm"
                    role="alert">
                    <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path
                                    d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                            </svg></div>
                        <div>
                            <p class="font-bold">Perhatian</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center flex-col md:flex-row">
                        <div class="flex-shrink-0 h-24 w-24 mb-4 md:mb-0">
                            @if ($student->user_photo)
                                <img class="h-24 w-24 rounded-full object-cover border-4 border-indigo-50 shadow-sm"
                                    src="{{ asset('storage/' . $student->user_photo) }}"
                                    alt="{{ $student->student_name }}">
                            @else
                                <div
                                    class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-3xl font-bold border-4 border-indigo-50 shadow-sm">
                                    {{ substr($student->student_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="md:ml-6 text-center md:text-left">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $student->student_name }}</h3>
                            <div class="mt-2 flex flex-col md:flex-row md:space-x-6 text-sm text-gray-600">
                                <div class="flex items-center justify-center md:justify-start">
                                    <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    NIPD: <span class="font-semibold ml-1">{{ $student->student_number }}</span>
                                </div>
                                <div class="flex items-center justify-center md:justify-start mt-1 md:mt-0">
                                    <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Kelas: <span
                                        class="font-semibold ml-1">{{ $student->activityTransaction->service->service_name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-center md:justify-start mt-1 md:mt-0">
                                    <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    Program: <span
                                        class="font-semibold ml-1">{{ $student->activityTransaction->program->program_name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-bold text-gray-900">Tentukan Rentang Waktu</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Sistem akan mengambil data <strong>Presensi</strong> dan menghitung <strong>Modus (Nilai
                                Terbanyak)</strong> dari laporan harian pada rentang tanggal ini.
                        </p>
                    </div>

                    <form action="{{ route('reports.create', $student->id) }}" method="GET" class="space-y-6"
                        id="periodForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai Periode')" class="text-base font-semibold" />
                                <div class="relative mt-2">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="start_date" id="start_date"
                                        class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 transition duration-150 ease-in-out"
                                        required value="{{ request('start_date') }}">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Contoh: 2024-07-01 (Awal Semester)</p>
                            </div>

                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Akhir Periode')" class="text-base font-semibold" />
                                <div class="relative mt-2">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="end_date" id="end_date"
                                        class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 transition duration-150 ease-in-out"
                                        required value="{{ request('end_date') ?? date('Y-m-d') }}">
                                    {{-- Otomatis isi hari ini jika belum ada request --}}
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Contoh: {{ date('Y-m-d') }} (Hari Ini)</p>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-4 rounded-r-md shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Pastikan data harian siswa sudah terisi dalam rentang tanggal tersebut agar
                                        perhitungan nilai otomatis akurat. <br>
                                        <span class="font-bold text-red-600">*Pastikan Tanggal Mulai lebih kecil dari
                                            Tanggal Akhir.</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-100">
                            <a href="{{ route('reports.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali
                            </a>

                            <x-primary-button class="ml-3 px-6 py-3 transition transform hover:scale-105">
                                {{ __('Lanjut ke Penilaian') }}
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('periodForm');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            form.addEventListener('submit', function(e) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                // Validasi 1: Pastikan kedua tanggal diisi (HTML required sudah handle, tapi double check di JS)
                if (!startDateInput.value || !endDateInput.value) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'Harap isi Tanggal Mulai dan Tanggal Akhir.',
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                // Validasi 2: Start Date tidak boleh lebih besar dari End Date
                if (startDate > endDate) {
                    e.preventDefault(); // Stop form submission

                    Swal.fire({
                        icon: 'error',
                        title: 'Rentang Tanggal Salah',
                        html: '<b>Tanggal Mulai</b> tidak boleh lebih besar dari <b>Tanggal Akhir</b>.<br><br>Silakan periksa kembali tahun dan bulannya.',
                        confirmButtonColor: '#ef4444' // Merah
                    });
                }
            });
        });
    </script>
</x-app-layout>
