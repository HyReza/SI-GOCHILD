<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Raport Siswa') }}
            </h2>
            {{-- Breadcrumb Sederhana --}}
            <nav class="text-sm font-medium text-gray-500" aria-label="Breadcrumb">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path
                                d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                        </svg>
                    </li>
                    <li>
                        <span class="text-gray-900">Raport</span>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Search & Filter Section --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Daftar Siswa Aktif</h3>
                            <p class="text-sm text-gray-500">Pilih siswa untuk mengelola penilaian dan cetak raport.</p>
                        </div>

                        <form action="{{ route('reports.index') }}" method="GET" class="flex w-full md:w-auto">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari Nama / No. Induk..."
                                    class="pl-10 block w-full md:w-72 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                Cari
                            </button>
                            @if (request('search'))
                                <a href="{{ route('reports.index') }}"
                                    class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">
                                        No
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Data Siswa
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Info Akademik
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Usia
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($students as $index => $student)
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        {{-- NO --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $students->firstItem() + $index }}
                                        </td>

                                        {{-- DATA SISWA --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    @if ($student->user_photo)
                                                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                                                            src="{{ asset('storage/' . $student->user_photo) }}"
                                                            alt="">
                                                    @else
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold border border-indigo-200">
                                                            {{ substr($student->student_name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $student->student_name }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        NIPD: {{ $student->student_number }}
                                                        @if ($student->nickname)
                                                            <span class="mx-1">•</span> {{ $student->nickname }}
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-400 mt-0.5">
                                                        {{ $student->gender == 1 ? 'Laki-laki' : 'Perempuan' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- INFO AKADEMIK (Diambil dari activityTransaction) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                {{-- Tampilkan Nama Service / Kelas --}}
                                                <div>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor"
                                                            viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{-- PERUBAHAN DISINI: Mengakses via activityTransaction --}}
                                                        {{ $student->activityTransaction->service->service_name ?? 'Tanpa Layanan' }}
                                                    </span>
                                                </div>
                                                {{-- Tampilkan Nama Program --}}
                                                <div>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                        <svg class="mr-1.5 h-2 w-2 text-emerald-400" fill="currentColor"
                                                            viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        {{-- PERUBAHAN DISINI: Mengakses via activityTransaction --}}
                                                        {{ $student->activityTransaction->program->program_name ?? 'Tanpa Program' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- USIA --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            @if ($student->birth_date)
                                                @php
                                                    $dob = \Carbon\Carbon::parse($student->birth_date);
                                                    $now = \Carbon\Carbon::now();
                                                    $diff = $dob->diff($now);
                                                @endphp
                                                <div class="font-medium">{{ $diff->y }} Thn {{ $diff->m }}
                                                    Bln</div>
                                                <div class="text-xs text-gray-400">{{ $dob->format('d M Y') }}</div>
                                            @else
                                                <span class="text-gray-400 italic">-</span>
                                            @endif
                                        </td>

                                        {{-- AKSI --}}
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('reports.selectPeriod', $student->id) }}"
                                                    class="group relative inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all"
                                                    title="Buat Raport Baru">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Buat
                                                </a>

                                                <a href="{{ route('reports.history', $student->id) }}"
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all"
                                                    title="Lihat Riwayat Raport">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4 mr-1.5 text-gray-500" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Riwayat
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-gray-100 rounded-full p-3 mb-4">
                                                    <svg class="h-10 w-10 text-gray-400" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </div>
                                                <span class="text-base font-medium text-gray-900">Tidak ada data siswa
                                                    aktif ditemukan.</span>
                                                <p class="text-sm text-gray-500 mt-1">Pastikan data siswa sudah diinput
                                                    dan memiliki transaksi aktivitas yang aktif.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $students->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert Scripts --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Notifikasi Sukses
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        confirmButtonColor: '#4F46E5',
                        timer: 3000,
                        timerProgressBar: true
                    });
                @endif

                // Notifikasi Error
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#EF4444',
                    });
                @endif
            });
        </script>
    @endpush
</x-app-layout>
