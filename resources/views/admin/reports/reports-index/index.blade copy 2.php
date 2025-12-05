<x-app-layout>
    <x-slot:title>Manajemen Rapor Siswa</x-slot:title>

    {{-- SweetAlert untuk pesan keberhasilan --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    {{-- SweetAlert untuk pesan error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json($errors->first())
                });
            });
        </script>
    @endif

    {{-- Bagian header --}}
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-lg font-semibold">Identitas Siswa MMDST</h1>
            <p class="text-sm text-gray-500">Cari siswa, filter layanan, lalu buat laporan atau cek riwayat.</p>
        </div>

        {{-- Form filter dan pencarian --}}
        <div class="w-full md:w-auto flex flex-col md:flex-row gap-2 md:items-end">
            <div class="w-full md:w-72">
                <label class="block text-xs text-gray-500 mb-1">Cari (nama/NIS/NIK/ibu)</label>
                <input id="q" type="text" placeholder="Ketik untuk mencari…"
                    class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700" name="search"
                    value="{{ request('search') }}">
            </div>

            <div class="w-full md:w-64">
                <label class="block text-xs text-gray-500 mb-1">Filter Layanan</label>
                <select id="service_id" name="service_id"
                    class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700">
                    <option value="">— Semua Layanan —</option>
                    @foreach ($services as $svc)
                        <option value="{{ $svc->id }}" @selected(request('service_id') == $svc->id)>
                            {{ $svc->service_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tabel daftar siswa --}}
    <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead
                    class="text-xs uppercase tracking-wide bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="py-3 px-3 text-left">No</th>
                        <th class="py-3 px-3 text-left">NIS</th>
                        <th class="py-3 px-3 text-left">Nama Anak</th>
                        <th class="py-3 px-3 text-left">Nama Ibu</th>
                        <th class="py-3 px-3 text-left">Service</th>
                        <th class="py-3 px-3 text-left">Tanggal Masuk</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody" class="text-sm">
                    @foreach ($active_transactions as $transaction)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-3 px-3">{{ $loop->iteration }}</td>
                            <td class="py-3 px-3">{{ $transaction->student->student_number }}</td>
                            <td class="py-3 px-3 font-medium">{{ $transaction->student->student_name }}</td>
                            <td class="py-3 px-3">{{ $transaction->student->mother_name ?? '—' }}</td>
                            <td class="py-3 px-3">{{ $transaction->service->service_name ?? '—' }}</td>
                            <td class="py-3 px-3">
                                {{ \Carbon\Carbon::parse($transaction->start_date)->isoFormat('D MMMM YYYY') }}</td>
                            <td class="py-3 px-3 text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('reports.create', $transaction->id) }}"
                                        class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm">
                                        <span class="material-symbols-outlined text-sm">add_task</span>
                                        Buat Laporan
                                    </a>
                                    <a href="{{ route('reports.history', $transaction) }}"
                                        class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs shadow-sm">
                                        <span class="material-symbols-outlined text-sm">history</span>
                                        Riwayat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Menampilkan pesan jika data kosong --}}
            @if ($active_transactions->isEmpty())
                <p class="text-center text-gray-500 py-6">Tidak ada data yang ditemukan.</p>
            @endif
        </div>

        {{-- Paginasi --}}
        <div class="mt-6">
            {{ $active_transactions->appends(request()->query())->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi load data jika menggunakan filter atau pencarian
            loadData();
        });
    </script>

</x-app-layout>
