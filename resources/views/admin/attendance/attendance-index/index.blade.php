<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Absensi') }}
        </h2>
    </x-slot>

    {{-- SweetAlert untuk Notifikasi Sukses & Error --}}
    @if (session('success') || session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{{ session('success') ? 'success' : 'error' }}',
                    title: '{{ session('success') ? 'Berhasil!' : 'Oops...' }}',
                    text: '{{ session('success') ?: (session('error') ?: $errors->first()) }}',
                });
            });
        </script>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Kontainer Utama dengan Aksi dan Filter --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
                        {{-- Judul dan Deskripsi --}}
                        <div class="flex-grow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daftar Absensi</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Kelola data absensi harian untuk setiap layanan.
                            </p>
                        </div>

                        {{-- Tombol Aksi Utama --}}
                        <a href="{{ route('attendance.create') }}">
                            <x-primary-button>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Tambah Absensi
                            </x-primary-button>
                        </a>
                    </div>

                    {{-- Form Filter --}}
                    <form method="GET" action="{{ route('attendance.index') }}"
                        class="p-4 mb-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg flex flex-row md:flex-row flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[150px]">
                            <x-input-label for="service_id" :value="__('Layanan')" />
                            <select name="service_id" id="service_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Semua Layanan</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex-1 min-w-[150px]">
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date"
                                :value="request('start_date')" />
                        </div>

                        <div class="flex-1 min-w-[150px]">
                            <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date"
                                :value="request('end_date')" />
                        </div>

                        <x-primary-button class="h-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ __('Filter') }}
                        </x-primary-button>
                    </form>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto mb-4">
                            <thead>
                                <tr
                                    class="bg-gray-100 dark:bg-gray-700 text-sm text-gray-600 dark:text-gray-300 uppercase">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Layanan</th>
                                    <th class="py-3 px-6 text-center">Hadir</th>
                                    <th class="py-3 px-6 text-center">Izin</th>
                                    <th class="py-3 px-6 text-center">Sakit</th>
                                    <th class="py-3 px-6 text-center">Alpa</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="text-sm font-light text-gray-600 dark:text-gray-400 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($attendanceTransactions as $index => $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-3 px-6 text-left">
                                            {{ $attendanceTransactions->firstItem() + $index }}</td>
                                        <td class="py-3 px-6 text-left">
                                            {{ \Carbon\Carbon::setLocale('id') }}
                                            {{ \Carbon\Carbon::parse($transaction->date_attendance)->isoFormat('dddd, D MMMM Y') }}
                                        </td>
                                        <td class="py-3 px-6 text-left font-medium">
                                            {{ $transaction->service?->service_name ?: 'N/A' }}</td>
                                        <td class="py-3 px-6 text-center">
                                            {{ $transaction->attendances->where('check_in_status', 'Present')->count() }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            {{ $transaction->attendances->where('check_in_status', 'Excused')->count() }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            {{ $transaction->attendances->where('check_in_status', 'Sick')->count() }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            {{ $transaction->attendances->where('check_in_status', 'Absent')->count() }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex gap-2 justify-center">
                                                <a href="{{ route('attendance.show', $transaction->id) }}"
                                                    class="relative group">
                                                    <span
                                                        class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                                        visibility
                                                    </span>
                                                    <span
                                                        class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                                        Lihat Detail
                                                    </span>
                                                </a>
                                                <a href="{{ route('attendance.edit', $transaction->id) }}"
                                                    class="relative group">
                                                    <span
                                                        class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                                        edit_square
                                                    </span>
                                                    <span
                                                        class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                                        Edit Data
                                                    </span>
                                                </a>
                                                <form id="delete-form-{{ $transaction->id }}"
                                                    action="{{ route('attendance.destroy', $transaction->id) }}"
                                                    method="POST" class="relative group delete-form"
                                                    data-theme-name="{{ $transaction->date_attendance }}"
                                                    data-service-name="{{ $transaction->service->service_name }}"
                                                    data-date-attendance="{{ $transaction->date_attendance }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="confirmDelete({{ $transaction->id }}, '{{ $transaction->date_attendance }}', '{{ $transaction->service->service_name }}')"
                                                        class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">
                                                        delete
                                                    </button>
                                                    <span
                                                        class="absolute z-50 right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                                        Hapus Data
                                                    </span>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-10 text-gray-500">
                                            Data absensi tidak ditemukan. Silakan ubah filter atau tambah data baru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    <div class="mt-6">
                        {{ $attendanceTransactions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Konfirmasi Hapus --}}
    <script>
        function confirmDelete(id, date, serviceName) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus data absensi untuk layanan "${serviceName}" pada tanggal ${date}. Aksi ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>
