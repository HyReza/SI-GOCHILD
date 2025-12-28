<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start">
            {{-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Raport Siswa') }}
        </h2> --}}
            <div class="mt-4 md:mt-0">
                <a href="{{ route('reports.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18">
                        </path>
                    </svg>
                    Kembali ke Daftar Siswa
                </a>
            </div>
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-indigo-500">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex items-center space-x-4 mb-4 md:mb-0">
                            <div class="flex-shrink-0 h-16 w-16">
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-100"
                                    src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=eef2ff&color=4f46e5&bold=true"
                                    alt="Foto Siswa">
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $student->student_name }}</h3>
                                <p class="text-sm text-gray-500 font-mono">{{ $student->student_number }}</p>
                                <div class="mt-1">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                                        {{ $reports->total() }} Laporan Tersimpan
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('reports.selectPeriod', $student->id) }}"
                                class="inline-flex items-center px-5 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Buat Raport Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2 md:mb-0">Daftar Riwayat</h3>

                        <form method="GET" action="{{ route('reports.history', $student->id) }}"
                            class="w-full md:w-1/3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="w-full py-2 pl-10 pr-4 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Cari judul raport...">
                                @if (request('search'))
                                    <a href="{{ route('reports.history', $student->id) }}"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert">
                            <strong class="font-bold">Berhasil!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif --}}

                    @if ($reports->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Judul & Tanggal
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Periode
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data Fisik
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($reports as $report)
                                        @php
                                            // --- LOGIKA PERHITUNGAN TAHUN AJARAN DI SISI VIEW ---
                                            $startDate = \Carbon\Carbon::parse($report->start_date);
                                            $startYear = $startDate->year;
                                            $endYear = $startYear + 1;
                                            if ($startDate->month < 7) {
                                                $startYear = $startYear - 1;
                                                $endYear = $startYear + 1;
                                            }
                                            $academicYear = $startYear . ' / ' . $endYear;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-900">
                                                        {{ $report->report_title }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 mt-1">
                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 mt-0.5">
                                                        Oleh: {{ $report->creator->name ?? 'Admin' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($report->start_date)->format('d/m/y') }} -
                                                    {{ \Carbon\Carbon::parse($report->end_date)->format('d/m/y') }}
                                                </div>
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mt-1">
                                                    {{ $report->semester ?? 'Semester Tidak Diketahui' }}
                                                </span>
                                                <span class="text-xs text-gray-500 mt-1 block">
                                                    TA: {{ $academicYear }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-xs text-gray-600">
                                                    <div><strong>BB:</strong> {{ $report->weight ?? '-' }} kg</div>
                                                    <div><strong>TB:</strong> {{ $report->height ?? '-' }} cm</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex justify-center space-x-2">

                                                    <a href="{{ route('reports.show', $report->id) }}"
                                                        class="group bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-md transition-all duration-200"
                                                        title="Lihat Detail Lengkap">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <a href="{{ route('reports.print', $report->id) }}" target="_blank"
                                                        class="group bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-2 rounded-md transition-all duration-200"
                                                        title="Cetak PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>

                                                    <a href="{{ route('reports.edit', $report->id) }}"
                                                        class="group bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white px-3 py-2 rounded-md transition-all duration-200"
                                                        title="Edit Data">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <button type="button"
                                                        onclick="confirmDelete('{{ $report->id }}')"
                                                        class="group bg-gray-50 text-gray-600 hover:bg-gray-600 hover:text-white px-3 py-2 rounded-md transition-all duration-200"
                                                        title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>

                                                    <form id="delete-form-{{ $report->id }}"
                                                        action="{{ route('reports.destroy', $report->id) }}"
                                                        method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $reports->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ request('search') ? 'Tidak ditemukan data pencarian.' : 'Belum ada riwayat raport' }}
                            </h3>
                            <p class="text-gray-500 mt-1 max-w-sm mx-auto">
                                {{ request('search') ? 'Coba kata kunci lain atau bersihkan pencarian.' : 'Siswa ini belum memiliki data raport yang tersimpan.' }}
                            </p>
                            @if (!request('search'))
                                <div class="mt-6">
                                    <a href="{{ route('reports.selectPeriod', $student->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        + Buat Raport Sekarang
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeDetailModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-info text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Detail Ringkas Raport
                            </h3>
                            <div class="mt-4 space-y-3">
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <p class="text-sm text-gray-500 font-bold">Data Fisik</p>
                                    <div class="flex justify-between mt-1 text-sm text-gray-800">
                                        <span>Berat: <span id="modal-weight" class="font-semibold">-</span> kg</span>
                                        <span>Tinggi: <span id="modal-height" class="font-semibold">-</span> cm</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-md">
                                    <p class="text-sm text-gray-500 font-bold mb-1">Ringkasan Kehadiran</p>
                                    <div class="grid grid-cols-4 gap-2 text-center text-xs">
                                        <div class="bg-white border rounded p-1">
                                            <span class="block text-gray-400">Sakit</span>
                                            <span id="att-sakit" class="font-bold text-gray-800">0</span>
                                        </div>
                                        <div class="bg-white border rounded p-1">
                                            <span class="block text-gray-400">Izin</span>
                                            <span id="att-izin" class="font-bold text-gray-800">0</span>
                                        </div>
                                        <div class="bg-white border rounded p-1">
                                            <span class="block text-gray-400">Alpha</span>
                                            <span id="att-alpha" class="font-bold text-gray-800">0</span>
                                        </div>
                                        <div class="bg-white border rounded p-1">
                                            <span class="block text-gray-400">Hadir</span>
                                            <span id="att-hadir" class="font-bold text-gray-800">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeDetailModal()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- 1. MODAL LOGIC ---
        function openDetailModal(id, title, weight, height, attendanceJson) {
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-weight').innerText = weight || '-';
            document.getElementById('modal-height').innerText = height || '-';

            // Parse Attendance JSON
            let att = {
                Sakit: 0,
                Izin: 0,
                Alpha: 0,
                Hadir: 0
            };
            try {
                if (attendanceJson) {
                    const parsed = JSON.parse(attendanceJson);
                    // Mapping lowercase keys to Display
                    att.Sakit = parsed.Sakit || parsed.sakit || 0;
                    att.Izin = parsed.Izin || parsed.izin || 0;
                    att.Alpha = parsed.Alpha || parsed.alpha || 0;
                    att.Hadir = parsed.Hadir || parsed.hadir || 0;
                }
            } catch (e) {
                console.error("Error parsing attendance", e);
            }

            document.getElementById('att-sakit').innerText = att.Sakit;
            document.getElementById('att-izin').innerText = att.Izin;
            document.getElementById('att-alpha').innerText = att.Alpha;
            document.getElementById('att-hadir').innerText = att.Hadir;

            // Show Modal
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // --- 2. DELETE CONFIRMATION ---
        function confirmDelete(reportId) {
            Swal.fire({
                title: 'Hapus Raport?',
                text: "Data raport dan tanda tangan digital akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + reportId).submit();
                }
            })
        }
    </script>
</x-app-layout>
