<x-app-layout>
    {{-- Slot untuk header halaman, sesuai standar layout Breeze/Jetstream --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Rapor Siswa') }}
        </h2>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Deskripsi singkat --}}
                    <p class="text-gray-600 mb-6">
                        Halaman ini berisi daftar semua siswa yang aktif. Anda dapat membuat rapor baru atau melihat
                        riwayat rapor untuk setiap siswa.
                    </p>

                    {{-- Form Pencarian (Tanpa JavaScript) --}}
                    <form action="{{ route('reports.index') }}" method="GET" class="mb-6">
                        <div class="flex items-center">
                            <input type="text" name="search" placeholder="Cari nama siswa..."
                                value="{{ request('search') }}"
                                class="w-full md:w-1/3 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <button type="submit"
                                class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cari
                            </button>
                        </div>
                    </form>

                    {{-- Tabel Daftar Siswa --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Siswa</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Program</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Masuk</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($active_transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loop->iteration + ($active_transactions->currentPage() - 1) * $active_transactions->perPage() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->student->student_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $transaction->student->nickname }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $transaction->program->program_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($transaction->start_date)->isoFormat('D MMMM Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('reports.selectPeriod', $transaction) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">Buat Rapor</a>
                                                <a href="{{ route('reports.history', $transaction) }}"
                                                    class="text-gray-600 hover:text-gray-900">History</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                            @if (request('search'))
                                                Siswa dengan nama "{{ request('search') }}" tidak ditemukan.
                                            @else
                                                Tidak ada data siswa aktif yang ditemukan.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Paginasi --}}
                    <div class="mt-6">
                        {{-- Menambahkan query string pencarian ke link paginasi agar filter tetap aktif --}}
                        {{ $active_transactions->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
