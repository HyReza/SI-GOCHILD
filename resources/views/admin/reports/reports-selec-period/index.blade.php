<x-app-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Pilih Periode Rapor
                </h2>
                {{-- Menampilkan nama siswa sebagai sub-judul --}}
                <p class="mt-1 text-sm text-gray-600">
                    Untuk: {{ $activity_transaction->student->student_name }}
                </p>
            </div>
            <div>
                {{-- Tombol Kembali ke halaman riwayat (history) --}}
                <a href="{{ route('reports.history', $activity_transaction) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 bg-white border-b border-gray-200">

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tentukan Rentang Tanggal</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Data perkembangan siswa akan diambil secara otomatis berdasarkan rentang tanggal yang Anda pilih
                        di bawah ini.
                    </p>

                    {{-- Form untuk memilih periode --}}
                    {{-- Metode GET digunakan agar tanggal terpilih muncul di URL saat diarahkan ke halaman 'create' --}}
                    <form action="{{ route('reports.create', $activity_transaction) }}" method="GET">
                        {{-- Laravel tidak memerlukan @csrf untuk metode GET --}}

                        <div class="space-y-6">
                            {{-- Input Tanggal Mulai --}}
                            <div>
                                <label for="start_date" class="block font-medium text-sm text-gray-700">Tanggal
                                    Mulai</label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                                    {{-- Menjaga nilai input jika validasi gagal --}} required
                                    class="block w-full mt-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Input Tanggal Selesai --}}
                            <div>
                                <label for="end_date" class="block font-medium text-sm text-gray-700">Tanggal
                                    Selesai</label>
                                <input type="date" id="end_date" name="end_date"
                                    value="{{ old('end_date', now()->format('Y-m-d')) }}" {{-- Default ke hari ini --}}
                                    required
                                    class="block w-full mt-1 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                @error('end_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-8 border-t border-gray-200 pt-6">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition">
                                Lanjutkan ke Pengisian Rapor
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
