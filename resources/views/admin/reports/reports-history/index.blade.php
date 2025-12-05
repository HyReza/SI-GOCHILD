<x-app-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Riwayat Rapor Siswa
                </h2>
                {{-- Menampilkan nama siswa dan programnya sebagai sub-judul --}}
                <p class="mt-1 text-sm text-gray-600">
                    {{ $activity_transaction->student->student_name }}
                    ({{ $activity_transaction->program->program_name }})
                </p>
            </div>
            {{-- Tombol aksi di header --}}
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('reports.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                    Kembali
                </a>
                <a href="{{ route('reports.selectPeriod', $activity_transaction) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition">
                    Buat Rapor Baru
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Menampilkan notifikasi sukses jika ada --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm"
                    role="alert">
                    <p class="font-bold">Sukses</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Rapor Tersimpan</h3>

                    {{-- Tabel Riwayat Rapor --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Judul Rapor</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Periode</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Dibuat</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reports as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $report->report_title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($report->start_date)->isoFormat('D MMM Y') }} -
                                            {{ \Carbon\Carbon::parse($report->end_date)->isoFormat('D MMM Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $report->created_at->isoFormat('D MMMM Y, HH:mm') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-4">
                                                {{-- Tombol Lihat --}}
                                                <a href="{{ route('reports.show', $report) }}"
                                                    class="text-blue-600 hover:text-blue-900" title="Lihat">
                                                    Lihat
                                                </a>
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('reports.edit', $report) }}"
                                                    class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                    Edit
                                                </a>
                                                {{-- Tombol Unduh PDF --}}
                                                <a href="{{ route('reports.downloadPdf', $report) }}"
                                                    class="text-green-600 hover:text-green-900" title="Unduh PDF">
                                                    Unduh
                                                </a>
                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route('reports.destroy', $report) }}" method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus rapor ini secara permanen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        title="Hapus">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                            Belum ada riwayat rapor untuk siswa ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Paginasi --}}
                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
