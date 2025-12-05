<x-app-layout>
    @push('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <style>
            .table-container::-webkit-scrollbar {
                height: 8px;
            }

            .table-container::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .table-container::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            .dark .table-container::-webkit-scrollbar-track {
                background: #1e293b;
            }

            .dark .table-container::-webkit-scrollbar-thumb {
                background: #475569;
            }
        </style>
    @endpush

    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">

        {{-- HEADER & ACTIONS --}}
        <div
            class="flex flex-col md:flex-row items-start justify-between gap-4 border-b border-gray-200 dark:border-gray-800 pb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Laporan Harian</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Al Jannah Preschool and Day Care
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Kembali --}}
                <a href="{{ route('daily-report.index') }}"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali
                </a>

                <a href="{{ route('daily-report.pdf', $dailyReport->id) }}" target="_blank"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm shadow transition">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                    PDF
                </a>
                {{-- Edit --}}
                <a href="{{ route('daily-report.edit', $dailyReport) }}"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white text-sm transition shadow">
                    <span class="material-symbols-outlined text-base">edit</span>
                    Edit
                </a>

                {{-- Hapus (SweetAlert) --}}
                <button type="button" id="btnDelete" data-id="{{ $dailyReport->id }}"
                    data-date="{{ $dailyReport->period }}"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm transition shadow">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Hapus
                </button>


                {{-- Form Delete (Hidden) --}}
                <form id="deleteForm" action="{{ route('daily-report.destroy', $dailyReport) }}" method="POST"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        {{-- INFO UTAMA --}}
        @php
            $tx = $dailyReport->activityTransaction;
            $student = $tx?->student;
            $service = $tx?->service;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            {{-- Kiri: Data Anak --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <table class="min-w-full text-gray-700 dark:text-gray-200">
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400 w-32">Nama Anak</td>
                        <td class="py-1 font-bold text-lg">{{ $student->student_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">NIS</td>
                        <td class="py-1 font-mono">{{ $student->student_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Layanan</td>
                        <td class="py-1">
                            <span
                                class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 text-xs font-bold uppercase">
                                {{ $service->service_name ?? '-' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Kanan: Meta Laporan --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <table class="min-w-full text-gray-700 dark:text-gray-200">
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400 w-32">Tanggal</td>
                        <td class="py-1 font-bold text-pink-600">
                            {{ \Carbon\Carbon::parse($dailyReport->period)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">ID Laporan</td>
                        <td class="py-1 font-mono">#{{ $dailyReport->id }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Dibuat Pada</td>
                        <td class="py-1">{{ $dailyReport->created_at?->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- KESEHATAN & KONDISI --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
            <div
                class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800 font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500">ecg_heart</span>
                Kesehatan & Kondisi
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    {{-- Kolom 1 --}}
                    <table class="w-full">
                        <tr>
                            <td class="py-2 pr-3 text-gray-500 dark:text-gray-400 w-32">Suhu Tubuh</td>
                            <td class="py-2 font-bold text-lg">
                                {{ $dailyReport->body_temperature ? number_format($dailyReport->body_temperature, 1) . ' °C' : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-3 text-gray-500 dark:text-gray-400">Makan Pagi</td>
                            <td class="py-2 capitalize">{{ $dailyReport->breakfast ?? '-' }}</td>
                        </tr>
                    </table>
                    {{-- Kolom 2 --}}
                    <table class="w-full">
                        <tr>
                            <td class="py-2 pr-3 text-gray-500 dark:text-gray-400 w-32">Kesehatan</td>
                            <td class="py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs font-bold uppercase {{ $dailyReport->health_status == 'sakit' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $dailyReport->health_status ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-3 text-gray-500 dark:text-gray-400">Kondisi</td>
                            <td class="py-2 capitalize font-medium">{{ $dailyReport->condition ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Detail Sakit --}}
                @if ($dailyReport->health_status === 'sakit')
                    <div
                        class="mt-4 mx-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                        <strong>Keluhan:</strong> {{ $dailyReport->sickness_description ?? '-' }} <br>
                        <strong>Penanganan:</strong> {{ ucfirst($dailyReport->medication_status ?? '-') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- STIMULASI --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
            <div
                class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800 font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-500">psychology</span>
                Stimulasi & Pembelajaran (MMDST)
            </div>
            <div class="p-4">
                <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <pre class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300 font-sans">{{ $dailyReport->stimulation_description ?? 'Tidak ada data stimulasi.' }}</pre>
                </div>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- DETAIL: BABY (Service 1) --}}
        {{-- ========================= --}}
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
                                    <td colspan="3" class="px-3 py-3 text-center text-gray-500">Tidak ada data.
                                    </td>
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

        {{-- CATATAN GURU --}}
        <div
            class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800 rounded-xl p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <span class="material-symbols-outlined text-6xl text-yellow-600">edit_note</span>
            </div>
            <h3 class="text-sm font-bold text-yellow-800 dark:text-yellow-400 uppercase tracking-widest mb-2">Catatan
                Guru</h3>
            <p class="text-gray-700 dark:text-gray-300 italic leading-relaxed">
                "{{ $dailyReport->notes ?? 'Tidak ada catatan khusus hari ini.' }}"
            </p>
        </div>

        {{-- ========================================== --}}
        {{-- AREA TANDA TANGAN (DUA KOLOM) --}}
        {{-- ========================================== --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h3
                class="text-lg font-bold text-gray-800 dark:text-white mb-6 text-center border-b pb-4 border-gray-100 dark:border-gray-700">
                Validasi Laporan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                {{-- 1. TANDA TANGAN ORANG TUA --}}
                <div class="flex flex-col items-center">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                        Orang Tua / Wali</p>

                    @if ($dailyReport->parent_guardian_signature)
                        {{-- Sudah TTD --}}
                        <div class="relative group">
                            <div
                                class="border-2 border-green-400 border-dashed bg-green-50 dark:bg-green-900/10 rounded-xl p-4 w-64 h-32 flex items-center justify-center">
                                <img src="{{ asset('storage/' . $dailyReport->parent_guardian_signature) }}"
                                    alt="TTD Orang Tua"
                                    class="max-h-full max-w-full object-contain filter dark:invert">
                            </div>
                            <div class="absolute -top-3 -right-3 bg-green-500 text-white rounded-full p-1 shadow">
                                <span class="material-symbols-outlined text-sm">check</span>
                            </div>
                        </div>
                        <p class="mt-3 font-bold text-gray-800 dark:text-white">
                            {{ $dailyReport->parent_guardian_name ?? '(Nama Tidak Terdata)' }}</p>
                        <p class="text-xs text-gray-400">Sudah dikonfirmasi</p>
                    @else
                        {{-- Belum TTD --}}
                        <div
                            class="w-64 h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl flex flex-col items-center justify-center text-gray-400 bg-gray-50 dark:bg-gray-800">
                            <span class="material-symbols-outlined text-3xl mb-1">edit_off</span>
                            <span class="text-xs">Belum ditandatangani</span>
                        </div>
                        <p class="mt-3 font-bold text-gray-400">..........................</p>
                    @endif
                </div>

                {{-- 2. TANDA TANGAN GURU --}}
                <div class="flex flex-col items-center">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                        Guru Pendamping</p>

                    @if ($dailyReport->teacher_signature)
                        {{-- Sudah TTD --}}
                        <div class="relative group">
                            <div
                                class="border-2 border-indigo-400 border-dashed bg-indigo-50 dark:bg-indigo-900/10 rounded-xl p-4 w-64 h-32 flex items-center justify-center">
                                <img src="{{ asset('storage/' . $dailyReport->teacher_signature) }}" alt="TTD Guru"
                                    class="max-h-full max-w-full object-contain filter dark:invert">
                            </div>
                            <div class="absolute -top-3 -right-3 bg-indigo-500 text-white rounded-full p-1 shadow">
                                <span class="material-symbols-outlined text-sm">verified</span>
                            </div>
                        </div>
                        <p class="mt-3 font-bold text-gray-800 dark:text-white">
                            {{ $dailyReport->teacher_name ?? 'Guru' }}</p>
                        <p class="text-xs text-gray-400">Sudah diverifikasi</p>
                    @else
                        {{-- Belum TTD --}}
                        <div
                            class="w-64 h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl flex flex-col items-center justify-center text-gray-400 bg-gray-50 dark:bg-gray-800">
                            <span class="material-symbols-outlined text-3xl mb-1">pending</span>
                            <span class="text-xs">Menunggu verifikasi guru</span>
                        </div>
                        <p class="mt-3 font-bold text-gray-400">..........................</p>
                    @endif
                </div>

            </div>
        </div>

    </div>

    {{-- Script Delete (SweetAlert) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('btnDelete')?.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const form = document.getElementById('deleteForm');

            Swal.fire({
                title: 'Hapus Laporan?',
                text: "Data laporan beserta seluruh tanda tangan akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>
