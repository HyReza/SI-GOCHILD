<x-app-layout>
    <x-slot:title>Detail Siswa</x-slot:title>

    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex mb-6">
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
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180"></span>
                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Detail Siswa
                </a>
            </li>
        </ol>
    </nav>

    <!-- Main Content -->
    <div class="mx-auto p-6 space-y-6 bg-white dark:bg-gray-900 rounded-md">
        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg overflow-hidden">

            <!-- Student Information Card -->
            <div class="p-6 bg-gray-200 dark:bg-gray-800 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Siswa</h2>
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-1/3">
                        <img src="{{ asset('storage/' . $student->user_photo) }}" alt="{{ $student->student_name }}"
                            class="w-full h-auto rounded-lg shadow-md object-cover mb-4" style="aspect-ratio: 1 / 1;">
                        <div class="bg-white dark:bg-gray-900 p-4 text-center rounded-lg shadow-md">
                            <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Status Siswa :</h3>
                            <p class="text-gray-800 dark:text-gray-200">
                                @if ($student->activityTransaction->student_status == 1)
                                    <span class="text-green-600 font-semibold">Siswa Active</span>
                                @else
                                    <span class="text-red-600 font-semibold">Siswa Tidak Active</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="w-full md:w-2/3">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-900 p-6 rounded-xl shadow-md">
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">NIS:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->student_number }}</p>
                            </div>
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">NIK:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->national_id ?? 'kosong' }}</p>
                            </div>
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Nama Lengkap:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->student_name }}</p>
                            </div>
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Nama Panggilan:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->nickname }}</p>
                            </div>
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Jenis Kelamin:</h3>
                                <p class="text-gray-800 dark:text-gray-200">
                                    {{ $student->gender ? 'Laki-laki' : 'Perempuan' }}</p>
                            </div>
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Tempat, Tanggal Lahir:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->birth_place }},
                                    {{ $student->birth_date }}</p>
                            </div>
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Nama Ayah:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->father_name }}</p>
                            </div>
                            <div>
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Nama Ibu:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->mother_name }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">No. Telepon:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->phone_number }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <h3 class="text-gray-700 dark:text-gray-400 font-semibold">Alamat:</h3>
                                <p class="text-gray-800 dark:text-gray-200">{{ $student->street }},
                                    {{ $student->village }}, {{ $student->subdistrict }},
                                    {{ $student->district }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Program and Service Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-6 py-6 bg-gray-200 rounded-md">
                <div class="bg-white dark:bg-gray-900 p-4 rounded-md shadow-md">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Program</h2>
                    <div class="text-gray-800 dark:text-gray-200">
                        <p><strong>Nama Program:</strong>
                            {{ optional($student->activityTransaction->program)->program_name ?? 'Program tidak tersedia' }}
                        </p>
                        <p><strong>Deskripsi:</strong>
                            {{ optional($student->activityTransaction->program)->program_description ?? 'Deskripsi tidak tersedia' }}
                        </p>
                        <p><strong>Jam Mulai:</strong>
                            {{ optional($student->activityTransaction->program)->start_time ?? '-' }}</p>
                        <p><strong>Jam Selesai:</strong>
                            {{ optional($student->activityTransaction->program)->end_time ?? '-' }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 p-4 rounded-md shadow-md">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Layanan</h2>
                    <div class="text-gray-800 dark:text-gray-200">
                        <p><strong>Nama Layanan:</strong>
                            {{ optional($student->activityTransaction->service)->service_name ?? 'Layanan tidak tersedia' }}
                        </p>
                        <p><strong>Deskripsi:</strong>
                            {{ optional($student->activityTransaction->service)->service_description ?? 'Deskripsi tidak tersedia' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status and Description Student -->
            <div class="flex bg-gray-200 dark:bg-gray-800 p-6 rounded-md shadow-md mt-6">
                <div class="w-full text-center p-6 bg-white dark:bg-gray-900 rounded-lg shadow-md">
                    <h3 class="text-gray-700 dark:text-gray-400 font-semibold text-lg">Kondisi Siswa:</h3>
                    <p class="text-gray-800 dark:text-gray-200 mt-2">
                        @if ($student->activityTransaction->student_is_normal == 1)
                            <span class="text-green-600 font-semibold">Normal</span>
                        @else
                            <span class="text-red-600 font-semibold">Abnormal</span>
                        @endif
                    </p>
                    <p class="mt-3">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Keterangan:</span>
                    </p>
                    <p class="mt-1 text-gray-600 dark:text-gray-400 italic">
                        {{ $student->student_description ?: 'Tidak ada catatan.' }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
