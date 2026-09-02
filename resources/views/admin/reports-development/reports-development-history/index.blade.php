<x-app-layout>
    <x-slot:title>Riwayat Hasil Tumbuh Kembang: {{ $student->student_name }}</x-slot:title>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Riwayat Hasil Pertumbuhan & Perkembangan') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola & cetak hasil pertumbuhan dan perkembangan siswa.</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('development-reports.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                </a>
                <a href="{{ route('development-reports.select-period', $student->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Buat Baru
                </a>
            </div>
        </div>
    </x-slot>

    {{-- LIBRARIES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. INFO SISWA CARD --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-center gap-6">
                <div class="flex-shrink-0">
                    <img class="h-20 w-20 rounded-full object-cover border-4 border-indigo-50"
                        src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=random&size=128"
                        alt="{{ $student->student_name }}">
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $student->student_name }}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{ $student->student_number ?? 'No ID' }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $student->gender == 1 || $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ $student->activityTransaction->program->program_name ?? 'Program -' }}
                        </span>
                    </div>
                </div>
                <div
                    class="text-center md:text-right border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Total Laporan</p>
                    <p class="text-3xl font-black text-indigo-600">{{ $reports->total() }}</p>
                </div>
            </div>

            {{-- 2. TABEL RIWAYAT --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Riwayat</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Tanggal & Periode</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Hasil Diagnosa (MMDST)</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($reports as $report)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $report->semester }} - {{ $report->academic_year }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $result = $report->mmdst_final_result ?? 'UNTESTABLE';
                                            $colorClass = match ($result) {
                                                'NORMAL' => 'bg-green-100 text-green-800',
                                                'SUSPECT', 'QUESTIONABLE', 'CAUTION' => 'bg-yellow-100 text-yellow-800',
                                                'ABNORMAL' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                            {{ $result }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($report->status == 'published')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Published
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            {{-- VIEW --}}
                                            <a href="{{ route('development-reports.show', $report->id) }}"
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- EDIT --}}
                                            <a href="{{ route('development-reports.edit', $report->id) }}"
                                                class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition"
                                                title="Edit Data">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>

                                            {{-- PRINT PDF --}}
                                            <a href="{{ route('development-reports.print', $report->id) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 text-red-600 hover:text-white bg-red-50 hover:bg-red-600 px-3 py-1.5 rounded-lg text-xs font-bold transition border border-red-200 shadow-sm"
                                                title="Cetak PDF Raport Tumbuh Kembang">
                                                <i class="fas fa-file-pdf"></i>
                                                Cetak PDF
                                            </a>

                                            {{-- DELETE --}}
                                            <button type="button" onclick="confirmDelete('{{ $report->id }}')"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"
                                                title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>

                                            <form id="delete-form-{{ $report->id }}"
                                                action="{{ route('development-reports.destroy', $report->id) }}"
                                                method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 rounded-full p-4 mb-3">
                                                <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm font-medium">Belum ada riwayat laporan untuk
                                                siswa ini.</p>
                                            <a href="{{ route('development-reports.select-period', $student->id) }}"
                                                class="mt-3 text-indigo-600 hover:text-indigo-800 text-sm font-bold">
                                                + Buat Laporan Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- SWEETALERT SCRIPTS --}}
    <script>
        // Notifikasi Sukses dari Controller
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        @endif

        // Notifikasi Error
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        @endif

        // Konfirmasi Hapus
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data laporan ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>
