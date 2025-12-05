<x-app-layout>
    @push('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    @endpush

    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Laporan Harian</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Al Jannah Preschool and Day Care
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Kembali --}}
                <a href="{{ route('daily-report.history', $dailyReport->activity_transaction_id) }}"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali
                </a>

                {{-- ⬅️⬅️  TOMBOL EDIT  --}}
                <a href="{{ route('daily-report.edit', $dailyReport) }}"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white text-sm">
                    <span class="material-symbols-outlined text-base">edit</span>
                    Edit
                </a>

                {{-- Hapus (SweetAlert) --}}
                <button type="button" id="btnDelete"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Hapus
                </button>
                <form id="deleteForm" action="{{ route('daily-report.destroy', $dailyReport) }}" method="POST"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        {{-- RINGKASAN ATAS --}}
        @php
            $tx = $dailyReport->ActivityTransaction;
            $student = $tx?->student;
            $service = $tx?->service;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 dark:bg-gray-800 rounded p-4">
                <table class="min-w-full text-gray-700 dark:text-gray-200">
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Nama Anak</td>
                        <td class="py-1 font-medium">{{ $student->student_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">NIS</td>
                        <td class="py-1">{{ $student->student_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Layanan</td>
                        <td class="py-1">{{ $service->service_name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded p-4">
                <table class="min-w-full text-gray-700 dark:text-gray-200">
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Tanggal</td>
                        <td class="py-1 font-medium">{{ \Carbon\Carbon::parse($dailyReport->period)->format('d M Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">ID Laporan</td>
                        <td class="py-1">#{{ $dailyReport->id }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Dibuat</td>
                        <td class="py-1">{{ $dailyReport->created_at?->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- DATA UMUM --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 font-semibold">
                Data Umum
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <table class="w-full table-fixed">
                        <tr>
                            <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Suhu Tubuh</td>
                            <td class="py-1">
                                {{ $dailyReport->body_temperature ? number_format($dailyReport->body_temperature, 1) . ' °C' : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Makan Pagi</td>
                            <td class="py-1 capitalize">{{ $dailyReport->breakfast ?? '-' }}</td>
                        </tr>
                    </table>
                    <table class="w-full table-fixed">
                        <tr>
                            <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Kesehatan</td>
                            <td class="py-1">
                                <div class="capitalize">{{ $dailyReport->health_status ?? '-' }}</div>
                                @if ($dailyReport->health_status === 'sakit')
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $dailyReport->sickness_description ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                                        {{ $dailyReport->medication_status ?? '-' }}</div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Kondisi</td>
                            <td class="py-1 capitalize">{{ $dailyReport->condition ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- STIMULASI --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 font-semibold">
                Stimulasi (otomatis MMDST)
            </div>
            <div class="p-4">
                <pre class="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200">{{ $dailyReport->stimulation_description ?? '-' }}</pre>
            </div>
        </div>

        {{-- DETAIL SPESIFIK LAYANAN --}}
        @if ((int) $dailyReport->service_id === 1)
            {{-- BABY --}}
            @php $baby = $dailyReport->babyDetail; @endphp

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 font-semibold">
                    Susu ASI / Susu Formula
                </div>
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">Jam</th>
                                <th class="px-3 py-2 text-left">Takaran (ml)</th>
                                <th class="px-3 py-2 text-left">ASI</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse(($baby->asi_formula_items ?? []) as $i => $row)
                                <tr class="border-b border-gray-200 dark:border-gray-800">
                                    <td class="px-3 py-2">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2">{{ $row['jam'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ isset($row['takaran']) ? $row['takaran'] : '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if (!empty($row['asi']))
                                            <span class="inline-flex items-center gap-1 text-green-600">
                                                <span class="material-symbols-outlined text-base">check_circle</span> Ya
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-gray-500">
                                                <span class="material-symbols-outlined text-base">cancel</span> Tidak
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-3 text-center text-gray-500">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 font-semibold">
                    Makanan Pendamping ASI (MP-ASI)
                </div>
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">Jam</th>
                                <th class="px-3 py-2 text-left">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse(($baby->mpasi_items ?? []) as $i => $row)
                                <tr class="border-b border-gray-200 dark:border-gray-800">
                                    <td class="px-3 py-2">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2">{{ $row['jam'] ?? '-' }}</td>
                                    <td class="px-3 py-2 capitalize">{{ $row['jumlah'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-3 text-center text-gray-500">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4 text-sm">
                        <div>
                            <div class="text-gray-500 dark:text-gray-400">Makan Pagi</div>
                            <div class="font-medium">{{ $baby->infant_breakfast_text ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 dark:text-gray-400">Makan Siang</div>
                            <div class="font-medium">{{ $baby->infant_lunch_text ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 dark:text-gray-400">Makan Malam</div>
                            <div class="font-medium">{{ $baby->infant_dinner_text ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 font-semibold">
                        Tidur
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">Tidur</th>
                                    <th class="px-3 py-2 text-left">Bangun</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse(($baby->naps ?? []) as $i => $row)
                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2">{{ $row['tidur'] ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $row['bangun'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-3 text-center text-gray-500">Tidak ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 font-semibold">
                        Popok
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">Jam</th>
                                    <th class="px-3 py-2 text-left">BAK</th>
                                    <th class="px-3 py-2 text-left">BAB</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse(($baby->diapers ?? []) as $i => $row)
                                    <tr class="border-b border-gray-200 dark:border-gray-800">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2">{{ $row['jam'] ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            {!! !empty($row['bak'])
                                                ? '<span class="inline-flex items-center gap-1 text-green-600"><span class="material-symbols-outlined text-base">check_circle</span>Ya</span>'
                                                : '<span class="inline-flex items-center gap-1 text-gray-500"><span class="material-symbols-outlined text-base">cancel</span>Tidak</span>' !!}
                                        </td>
                                        <td class="px-3 py-2">
                                            {!! !empty($row['bab'])
                                                ? '<span class="inline-flex items-center gap-1 text-green-600"><span class="material-symbols-outlined text-base">check_circle</span>Ya</span>'
                                                : '<span class="inline-flex items-center gap-1 text-gray-500"><span class="material-symbols-outlined text-base">cancel</span>Tidak</span>' !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-3 text-center text-gray-500">Tidak ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif((int) $dailyReport->service_id === 2)
            {{-- CHILDREN --}}
            @php $ch = $dailyReport->childrenDetail; @endphp

            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-indigo-600 dark:bg-indigo-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Kegiatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700 text-sm">

                        {{-- Salam & Doa Pagi --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">06:30 - 07:30</td>
                            <td class="px-6 py-3">Salam Penyambutan dan Do'a Pagi</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->greeting_and_morning_prayer ?? '-' }}</td>
                        </tr>

                        {{-- Session 1 --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">07:30 - 09:00</td>
                            <td class="px-6 py-3">
                                Bermain & Belajar <span class="font-semibold">Session 1</span><br>
                                <span class="text-xs text-gray-500">
                                    Materi: {{ $ch?->session1Material?->material_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="uppercase">{{ $ch->session1_activity ?? '-' }}</span>
                            </td>
                        </tr>

                        {{-- Toilet & Dhuha --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">09:00 - 09:30</td>
                            <td class="px-6 py-3">Toilet Training & Sholat Dhuha</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->toilet_training_and_duha_prayer ?? '-' }}</td>
                        </tr>

                        {{-- Session 2 --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">09:30 - 10:00</td>
                            <td class="px-6 py-3">
                                Bermain & Belajar <span class="font-semibold">Session 2</span><br>
                                <span class="text-xs text-gray-500">
                                    Materi: {{ $ch?->session2Material?->material_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="uppercase">{{ $ch->session2_activity ?? '-' }}</span>
                            </td>
                        </tr>

                        {{-- Snack Pagi --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">10:00 - 10:30</td>
                            <td class="px-6 py-3">Snack Pagi</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->morning_snack ?? '-' }}</td>
                        </tr>

                        {{-- Kerapian & Kemandirian --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">10:30 - 11:15</td>
                            <td class="px-6 py-3">Kerapian & Kemandirian</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->neatness_and_independence ?? '-' }}</td>
                        </tr>

                        {{-- Makan Siang Ceria --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">11:15 - 11:45</td>
                            <td class="px-6 py-3">Makan Siang Ceria</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->cheerful_lunch ?? '-' }}</td>
                        </tr>

                        {{-- Kebersihan & Gosok Gigi --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">11:45 - 12:00</td>
                            <td class="px-6 py-3">Kebersihan & Training Gosok Gigi</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->cleanliness_and_brushing_training ?? '-' }}</td>
                        </tr>

                        {{-- Sholat Dzuhur --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">12:00 - 12:30</td>
                            <td class="px-6 py-3">Sholat Dzuhur</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->dhuhr_prayer ?? '-' }}</td>
                        </tr>

                        {{-- Tidur Sehat --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">12:30 - 14:00</td>
                            <td class="px-6 py-3">Tidur Sehat (Penjemputan 1)</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->healthy_sleep ?? '-' }}</td>
                        </tr>

                        {{-- Mandi Sore --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">14:00 - 14:30</td>
                            <td class="px-6 py-3">Mandi Sore</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->afternoon_bath ?? '-' }}</td>
                        </tr>

                        {{-- Snack Sore --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">14:30 - 15:00</td>
                            <td class="px-6 py-3">Snack Sore</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->afternoon_snack ?? '-' }}</td>
                        </tr>

                        {{-- Sholat Ashar --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">15:00 - 15:30</td>
                            <td class="px-6 py-3">Sholat Ashar</td>
                            <td class="px-6 py-3 capitalize">{{ $ch->asr_prayer ?? '-' }}</td>
                        </tr>

                        {{-- Ekstra Stimulasi --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">15:30 - 16:00</td>
                            <td class="px-6 py-3">Ekstra Stimulasi (Penjemputan 2)</td>
                            <td class="px-6 py-3">
                                <div class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-line">
                                    {{ $ch->extra_stimulation_description ?? '-' }}
                                </div>
                                <div class="mt-1 uppercase">{{ $ch->extra_stimulation ?? '-' }}</div>
                            </td>
                        </tr>

                        {{-- Permainan Ceria --}}
                        <tr>
                            <td class="px-6 py-3 text-gray-500">16:00 - 17:00</td>
                            <td class="px-6 py-3">Permainan Ceria (Penjemputan 3)</td>
                            <td class="px-6 py-3">
                                <div class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-line">
                                    {{ $ch->cheerful_play_description ?? '-' }}
                                </div>
                                <div class="mt-1 uppercase">{{ $ch->cheerful_play ?? '-' }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- CATATAN --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-800 font-semibold">
                Catatan
            </div>
            <div class="p-4">
                <div class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">
                    {{ $dailyReport->notes ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert & Delete --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('btnDelete')?.addEventListener('click', async function() {
            const id = this.dataset.id;
            const date = this.dataset.date || '-';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const confirm = await Swal.fire({
                title: 'Hapus laporan?',
                html: `Tanggal: <b>${date}</b><br>Data akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            });

            if (!confirm.isConfirmed) return;

            try {
                const resp = await fetch(`{{ url('/daily-report') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await resp.json().catch(() => ({}));
                if (!resp.ok) throw new Error(data?.message || 'Request gagal');

                await Swal.fire({
                    icon: 'success',
                    title: 'Terhapus',
                    text: data?.message || 'Laporan dihapus.'
                });

                // Prefer balik ke history kalau ada referrer; kalau tidak, ke index
                if (document.referrer) {
                    location.replace(document.referrer);
                } else {
                    location.replace(`{{ route('daily-report.index') }}`);
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal menghapus laporan.'
                });
            }
        });
    </script>
</x-app-layout>
