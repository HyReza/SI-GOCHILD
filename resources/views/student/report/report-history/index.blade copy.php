<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Laporan Pembelajaran Saya') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-indigo-500">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-16 w-16">
                            <img class="h-16 w-16 rounded-full object-cover border-2 border-indigo-100"
                                src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=eef2ff&color=4f46e5&bold=true"
                                alt="Foto Siswa">
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $student->student_name }}</h3>
                            <p class="text-sm text-gray-500 font-mono">{{ $student->student_number }}</p>
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                                    {{ $reports->total() }} Laporan Tersimpan
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Riwayat Rapor Semester</h3>

                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            <span class="font-medium">Berhasil!</span> {{ session('success') }}
                        </div>
                    @endif

                    @if ($reports->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Judul & Tanggal Terbit
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Periode & Status
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanda Tangan Ortu
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
                                            // LOGIKA TAHUN AJARAN YANG STABIL (dari start_date)
                                            $startDate = \Carbon\Carbon::parse($report->start_date);
                                            $startYear =
                                                $startDate->month >= 7 ? $startDate->year : $startDate->year - 1;
                                            $endYear = $startYear + 1;
                                            $academicYear = $startYear . '/' . $endYear;

                                            $isSigned = !empty($report->parent_signature);
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-900">
                                                        {{ $report->report_title }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 mt-1">
                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                        Terbit:
                                                        {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    {{ $report->semester ?? 'Semester Tidak Diketahui' }}
                                                </span>
                                                <span class="text-xs text-gray-500 mt-1 block">
                                                    TA: {{ $academicYear }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-xs font-semibold {{ $isSigned ? 'text-green-600' : 'text-red-600' }}">
                                                    <i
                                                        class="fas {{ $isSigned ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                    {{ $isSigned ? $report->parent_name : 'Belum ditandatangani' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex justify-center space-x-2">

                                                    <a href="{{ route('student.report.show', $report->id) }}"
                                                        class="group bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-md transition-all duration-200"
                                                        title="Lihat Detail & Tanda Tangan">
                                                        <i class="fas fa-file-signature"></i>
                                                    </a>

                                                    <a href="{{ route('student.report.pdf', $report->id) }}"
                                                        target="_blank"
                                                        class="group bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-2 rounded-md transition-all duration-200"
                                                        title="Cetak PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
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
                            <h3 class="text-lg font-medium text-gray-900">Belum ada riwayat rapor</h3>
                            <p class="text-gray-500 mt-1 max-w-sm mx-auto">
                                Rapor semester/akhir Anda akan muncul di sini setelah diterbitkan oleh guru.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(reportId) {
            Swal.fire({
                title: 'Hapus Raport?',
                text: "Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Logika delete jika ada route delete di sisi siswa
                    // document.getElementById('delete-form-' + reportId).submit();
                    Swal.fire('Fitur Terkunci', 'Penghapusan hanya dapat dilakukan oleh Admin.', 'info');
                }
            })
        }
    </script>
</x-app-layout>
