<x-app-layout>
    @push('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Library Signature Pad --}}
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        {{-- Google Icons --}}
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

        {{-- HEADER: JUDUL & TOMBOL AKSI --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Laporan Harian</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Al Jannah Preschool and Day Care</p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Tombol Kembali --}}
                <a href="{{ route('student.daily-report.index') }}"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali
                </a>

                {{-- Tombol Download PDF --}}
                <a href="{{ route('student.daily-report.pdf', $dailyReport->id) }}" target="_blank"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm shadow transition">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                    PDF
                </a>
            </div>
        </div>

        {{-- RINGKASAN DATA (ATAS) --}}
        @php
            $tx = $dailyReport->activityTransaction;
            $student = $tx?->student;
            $service = $tx?->service;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            {{-- Card Kiri: Data Anak --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded p-4 border border-gray-100 dark:border-gray-700">
                <table class="min-w-full text-gray-700 dark:text-gray-200">
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400 w-32">Nama Anak</td>
                        <td class="py-1 font-bold">{{ $student->student_name ?? '-' }}</td>
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

            {{-- Card Kanan: Meta Laporan --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded p-4 border border-gray-100 dark:border-gray-700">
                <table class="min-w-full text-gray-700 dark:text-gray-200">
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400 w-32">Tanggal</td>
                        <td class="py-1 font-bold text-pink-600">
                            {{ \Carbon\Carbon::parse($dailyReport->period)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">ID Laporan</td>
                        <td class="py-1">#{{ $dailyReport->id }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Status TTD</td>
                        <td class="py-1">
                            @if ($dailyReport->parent_guardian_signature)
                                <span
                                    class="text-green-600 font-bold text-xs px-2 py-0.5 bg-green-100 rounded">Sudah</span>
                            @else
                                <span
                                    class="text-amber-600 font-bold text-xs px-2 py-0.5 bg-amber-100 rounded">Belum</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- DATA UMUM (KESEHATAN) --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div
                class="px-4 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800 font-semibold text-gray-700 dark:text-gray-200">
                Data Kesehatan & Kondisi
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <table class="w-full table-fixed">
                        <tr>
                            <td class="py-1 pr-3 text-gray-500 dark:text-gray-400">Suhu Tubuh</td>
                            <td class="py-1 font-mono font-bold">
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
                                <div
                                    class="font-semibold capitalize {{ $dailyReport->health_status == 'sakit' ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $dailyReport->health_status ?? '-' }}
                                </div>
                                @if ($dailyReport->health_status === 'sakit')
                                    <div
                                        class="text-xs text-red-500 mt-1 bg-red-50 p-1.5 rounded border border-red-100">
                                        Keluhan: {{ $dailyReport->sickness_description ?? '-' }} <br>
                                        ({{ $dailyReport->medication_status ?? '-' }})
                                    </div>
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
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div
                class="px-4 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800 font-semibold text-gray-700 dark:text-gray-200">
                Stimulasi & Pembelajaran (MMDST)
            </div>
            <div class="p-4">
                <pre class="whitespace-pre-wrap text-sm text-gray-600 dark:text-gray-300 font-sans leading-relaxed">{{ $dailyReport->stimulation_description ?? '-' }}</pre>
            </div>
        </div>

        {{-- ================================== --}}
        {{-- DETAIL LAYANAN 1: BABY --}}
        {{-- ================================== --}}
        @if ((int) $dailyReport->service_id === 1 && $dailyReport->babyDetail)
            @php $baby = $dailyReport->babyDetail; @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- SUSU --}}
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                    <div
                        class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800 font-bold text-blue-700 dark:text-blue-300">
                        🍼 Susu (ASI / Sufor)
                    </div>
                    <div class="p-0">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Jam</th>
                                    <th class="px-4 py-2 text-left">Takaran</th>
                                    <th class="px-4 py-2 text-left">Jenis</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse(($baby->asi_formula_items ?? []) as $item)
                                    <tr>
                                        <td class="px-4 py-2 font-mono">{{ $item['jam'] ?? '-' }}</td>
                                        <td class="px-4 py-2 font-semibold">{{ $item['takaran'] ?? '-' }} ml</td>
                                        <td class="px-4 py-2">
                                            <span
                                                class="px-2 py-0.5 rounded text-xs {{ $item['asi'] ?? false ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $item['asi'] ?? false ? 'ASI' : 'Sufor' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-400 text-xs">Tidak ada
                                            data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TIDUR --}}
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                    <div
                        class="px-4 py-2 bg-purple-50 dark:bg-purple-900/20 border-b border-purple-100 dark:border-purple-800 font-bold text-purple-700 dark:text-purple-300">
                        💤 Tidur
                    </div>
                    <div class="p-0">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Mulai</th>
                                    <th class="px-4 py-2 text-left">Bangun</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse(($baby->naps ?? []) as $item)
                                    <tr>
                                        <td class="px-4 py-2 font-mono">{{ $item['tidur'] ?? '-' }}</td>
                                        <td class="px-4 py-2 font-mono">{{ $item['bangun'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-4 text-center text-gray-400 text-xs">Tidak ada
                                            data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- POPOK --}}
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                    <div
                        class="px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-100 dark:border-yellow-800 font-bold text-yellow-700 dark:text-yellow-300">
                        🧷 Popok
                    </div>
                    <div class="p-4 flex flex-wrap gap-2">
                        @forelse(($baby->diapers ?? []) as $item)
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm">
                                <span class="font-mono font-bold">{{ $item['jam'] ?? '-' }}</span>
                                @if ($item['bak'] ?? false)
                                    <span class="text-xs font-bold text-blue-500">BAK</span>
                                @endif
                                @if ($item['bab'] ?? false)
                                    <span class="text-xs font-bold text-amber-600">BAB</span>
                                @endif
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic w-full text-center">Tidak ada data popok.</span>
                        @endforelse
                    </div>
                </div>

                {{-- MAKAN --}}
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                    <div
                        class="px-4 py-2 bg-green-50 dark:bg-green-900/20 border-b border-green-100 dark:border-green-800 font-bold text-green-700 dark:text-green-300">
                        🥣 Makan (MP-ASI)
                    </div>
                    <div class="p-4 space-y-4">
                        {{-- Tabel kecil riwayat makan --}}
                        <ul class="space-y-1">
                            @forelse(($baby->mpasi_items ?? []) as $item)
                                <li class="flex justify-between text-sm border-b border-dashed border-gray-100 pb-1">
                                    <span class="font-mono">{{ $item['jam'] ?? '-' }}</span>
                                    <span class="font-medium capitalize">{{ $item['jumlah'] ?? '-' }}</span>
                                </li>
                            @empty
                                <li class="text-xs text-gray-400 italic text-center mb-2">Belum ada log makan.</li>
                            @endforelse
                        </ul>

                        {{-- Menu --}}
                        <div class="text-xs space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex justify-between"><span class="text-gray-500">Pagi</span> <span
                                    class="font-medium">{{ $baby->infant_breakfast_text ?? '-' }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Siang</span> <span
                                    class="font-medium">{{ $baby->infant_lunch_text ?? '-' }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Malam</span> <span
                                    class="font-medium">{{ $baby->infant_dinner_text ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- ================================== --}}
        {{-- DETAIL LAYANAN 2: CHILDREN --}}
        {{-- ================================== --}}
        @if ((int) $dailyReport->service_id === 2 && $dailyReport->childrenDetail)
            @php $ch = $dailyReport->childrenDetail; @endphp
            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm">
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                    <span class="material-symbols-outlined">schedule</span> Jadwal & Aktivitas
                </div>

                <div class="overflow-x-auto table-container">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Waktu</th>
                                <th class="px-6 py-3 text-left">Kegiatan</th>
                                <th class="px-6 py-3 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                            {{-- ROWS SESUAI FORMAT ADMIN --}}
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">06:30 - 07:30</td>
                                <td class="px-6 py-3 font-medium">Salam & Doa</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->greeting_and_morning_prayer ?? '-' }}</td>
                            </tr>
                            <tr class="bg-indigo-50/30 dark:bg-indigo-900/10">
                                <td class="px-6 py-3 font-mono text-gray-500">07:30 - 09:00</td>
                                <td class="px-6 py-3">
                                    <span class="font-bold text-indigo-700 dark:text-indigo-400">Belajar Sesi 1</span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $ch->session1Material->material_name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-3"><span
                                        class="px-2 py-1 rounded text-xs font-bold bg-white border shadow-sm">{{ $ch->session1_activity ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">09:00 - 09:30</td>
                                <td class="px-6 py-3 font-medium">Toilet Training & Dhuha</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->toilet_training_and_duha_prayer ?? '-' }}
                                </td>
                            </tr>
                            <tr class="bg-indigo-50/30 dark:bg-indigo-900/10">
                                <td class="px-6 py-3 font-mono text-gray-500">09:30 - 10:00</td>
                                <td class="px-6 py-3">
                                    <span class="font-bold text-indigo-700 dark:text-indigo-400">Belajar Sesi 2</span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $ch->session2Material->material_name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-3"><span
                                        class="px-2 py-1 rounded text-xs font-bold bg-white border shadow-sm">{{ $ch->session2_activity ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">10:00 - 10:30</td>
                                <td class="px-6 py-3 font-medium">Snack Pagi</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->morning_snack ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">10:30 - 11:15</td>
                                <td class="px-6 py-3 font-medium">Kerapian & Kemandirian</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->neatness_and_independence ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">11:15 - 11:45</td>
                                <td class="px-6 py-3 font-medium">Makan Siang</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->cheerful_lunch ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">11:45 - 12:00</td>
                                <td class="px-6 py-3 font-medium">Gosok Gigi</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->cleanliness_and_brushing_training ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">12:00 - 12:30</td>
                                <td class="px-6 py-3 font-medium">Sholat Dzuhur</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->dhuhr_prayer ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">12:30 - 14:00</td>
                                <td class="px-6 py-3 font-medium">Tidur Siang</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->healthy_sleep ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">14:00 - 14:30</td>
                                <td class="px-6 py-3 font-medium">Mandi Sore</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->afternoon_bath ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">14:30 - 15:00</td>
                                <td class="px-6 py-3 font-medium">Snack Sore</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->afternoon_snack ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">15:00 - 15:30</td>
                                <td class="px-6 py-3 font-medium">Sholat Ashar</td>
                                <td class="px-6 py-3 capitalize">{{ $ch->asr_prayer ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">15:30 - 16:00</td>
                                <td class="px-6 py-3 font-medium">Ekstra Stimulasi</td>
                                <td class="px-6 py-3">
                                    <div class="text-xs text-gray-600 mb-1">
                                        {{ $ch->extra_stimulation_description ?? '-' }}</div>
                                    <span
                                        class="text-xs font-bold bg-gray-100 px-1.5 py-0.5 rounded uppercase">{{ $ch->extra_stimulation ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono text-gray-500">16:00 - 17:00</td>
                                <td class="px-6 py-3 font-medium">Permainan Ceria</td>
                                <td class="px-6 py-3">
                                    <div class="text-xs text-gray-600 mb-1">
                                        {{ $ch->cheerful_play_description ?? '-' }}</div>
                                    <span
                                        class="text-xs font-bold bg-gray-100 px-1.5 py-0.5 rounded uppercase">{{ $ch->cheerful_play ?? '-' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- CATATAN GURU --}}
        <div
            class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800 rounded-lg p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <span class="material-symbols-outlined text-6xl text-yellow-600">edit_note</span>
            </div>
            <h3 class="text-sm font-bold text-yellow-800 dark:text-yellow-400 uppercase tracking-widest mb-2">Catatan
                Guru</h3>
            <p class="text-gray-700 dark:text-gray-300 italic leading-relaxed">
                "{{ $dailyReport->notes ?? 'Tidak ada catatan khusus hari ini.' }}"
            </p>
        </div>

        {{-- AREA TANDA TANGAN --}}
        <div
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 p-6 sm:p-8 text-center">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Konfirmasi Orang Tua / Wali</h3>

            @if ($dailyReport->parent_guardian_signature)
                {{-- JIKA SUDAH TTD --}}
                <div class="flex flex-col items-center animate-fade-in">
                    <div class="relative">
                        <div class="absolute -top-3 -right-3 bg-green-500 text-white rounded-full p-1 shadow-md">
                            <span class="material-symbols-outlined text-lg">check</span>
                        </div>
                        <div
                            class="border-2 border-green-400 border-dashed bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                            <img src="{{ asset('storage/' . $dailyReport->parent_guardian_signature) }}"
                                alt="Tanda Tangan" class="h-32 w-auto object-contain filter dark:invert">
                        </div>
                    </div>
                    <p class="mt-4 text-gray-800 dark:text-white font-semibold">
                        {{ $dailyReport->parent_guardian_name }}
                    </p>
                    <p class="text-green-600 font-bold text-sm">Laporan Telah Dikonfirmasi</p>
                    <p class="text-xs text-gray-400 mt-1">Terima kasih Ayah/Bunda atas kerjasamanya.</p>
                </div>
            @else
                {{-- JIKA BELUM TTD (FORM) --}}
                <div class="max-w-md mx-auto text-left">

                    {{-- Input Nama Orang Tua (BARU) --}}
                    <div class="mb-4">
                        <label for="visible_parent_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Orang Tua /
                            Wali</label>
                        <input type="text" id="visible_parent_name"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-pink-500 focus:border-pink-500"
                            placeholder="Masukkan nama lengkap...">
                    </div>

                    <p class="text-sm text-gray-500 mb-2">Tanda tangan di kotak di bawah ini:</p>

                    {{-- Canvas Container --}}
                    <div class="relative group mb-4">
                        <canvas id="signature-pad"
                            class="block w-full bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-crosshair touch-none shadow-inner h-48"></canvas>
                        <div
                            class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20 group-hover:opacity-10 transition-opacity">
                            <span class="text-2xl text-gray-400 font-bold select-none">Area Tanda Tangan</span>
                        </div>
                    </div>

                    <div class="flex justify-center gap-3">
                        <button type="button" id="clear-btn"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Ulangi
                        </button>
                        <button type="button" id="save-btn"
                            class="px-6 py-2 text-sm font-bold text-white bg-pink-500 rounded-lg hover:bg-pink-600 transition shadow-md">
                            Simpan Konfirmasi
                        </button>
                    </div>

                    {{-- Hidden Form --}}
                    <form id="signature-form" action="{{ route('student.daily-report.sign', $dailyReport->id) }}"
                        method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="signature" id="signature-input">
                        {{-- Input hidden untuk nama --}}
                        <input type="hidden" name="parent_name" id="parent-name-input">
                    </form>
                </div>
            @endif
        </div>

    </div>

    {{-- SCRIPT --}}
    @if (!$dailyReport->parent_guardian_signature)
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const canvas = document.getElementById('signature-pad');
                const nameInput = document.getElementById('visible_parent_name'); // Input nama terlihat

                if (canvas) {
                    function resizeCanvas() {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                    }
                    window.addEventListener("resize", resizeCanvas);
                    resizeCanvas();

                    const signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(0, 0, 0)'
                    });

                    document.getElementById('clear-btn').addEventListener('click', () => signaturePad.clear());

                    document.getElementById('save-btn').addEventListener('click', () => {
                        // Validasi Nama
                        if (!nameInput.value.trim()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Nama Kosong',
                                text: 'Mohon masukkan nama orang tua/wali.',
                                confirmButtonColor: '#ec4899'
                            });
                            return;
                        }

                        // Validasi Tanda Tangan
                        if (signaturePad.isEmpty()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tanda Tangan Kosong',
                                text: 'Mohon tanda tangan terlebih dahulu.',
                                confirmButtonColor: '#ec4899'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Simpan Konfirmasi?',
                            text: 'Laporan akan ditandai sebagai sudah dibaca.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#ec4899',
                            confirmButtonText: 'Ya, Simpan'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Set value ke hidden inputs
                                document.getElementById('signature-input').value = signaturePad
                                    .toDataURL('image/png');
                                document.getElementById('parent-name-input').value = nameInput.value;

                                // Submit form
                                document.getElementById('signature-form').submit();
                                Swal.fire({
                                    title: 'Menyimpan...',
                                    didOpen: () => Swal.showLoading()
                                });
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
