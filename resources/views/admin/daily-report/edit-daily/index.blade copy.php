<x-app-layout>
    <x-slot:title>Edit Daily Report</x-slot:title>

    {{-- Flash message --}}
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
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-200">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                Edit Laporan Harian — {{ $dailyReport->activityTransaction->service->service_name ?? 'Service' }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('daily-report.show', $dailyReport->id) }}"
                    class="inline-flex items-center bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 px-3 py-2 rounded">
                    <span class="material-symbols-outlined mr-1">visibility</span> Lihat
                </a>
                <a href="{{ route('daily-report.history', $dailyReport->activity_transaction_id) }}"
                    class="inline-flex items-center bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 text-indigo-700 dark:text-indigo-200 px-3 py-2 rounded">
                    <span class="material-symbols-outlined mr-1">history</span> Riwayat
                </a>
            </div>
        </div>

        <form id="dailyReportEditForm" action="{{ route('daily-report.update', $dailyReport->id) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Identitas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Anak</label>
                    <input type="text" value="{{ $dailyReport->activityTransaction->student->student_name ?? '-' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white"
                        disabled>
                    <p id="attendance-status" class="text-xs text-gray-500 dark:text-gray-400 mt-1"></p>
                </div>
                <div>
                    <label for="period"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode</label>
                    <input type="date" id="period" name="period"
                        value="{{ old('period', \Illuminate\Support\Carbon::parse($dailyReport->period)->toDateString()) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            {{-- Vital & Kehadiran --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="body_temperature" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Suhu Tubuh (°C)
                    </label>
                    <input type="number" step="0.1" id="body_temperature" name="body_temperature"
                        value="{{ old('body_temperature', $dailyReport->body_temperature) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="arrival_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jam
                        Datang</label>
                    <input type="time" id="arrival_time" name="arrival_time"
                        value="{{ old('arrival_time', optional($dailyReport->arrival_time)->format('H:i') ?? $dailyReport->arrival_time) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="departure_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jam
                        Pulang</label>
                    <input type="time" id="departure_time" name="departure_time"
                        value="{{ old('departure_time', optional($dailyReport->departure_time)->format('H:i') ?? $dailyReport->departure_time) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            {{-- Makan pagi & Kesehatan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Makan Pagi</label>
                    <div class="mt-2 space-x-6">
                        @php $bf = old('breakfast', $dailyReport->breakfast); @endphp
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="sudah"
                                {{ $bf === 'sudah' ? 'checked' : '' }} class="form-radio text-indigo-600">
                            <span class="ml-2">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="belum"
                                {{ $bf === 'belum' ? 'checked' : '' }} class="form-radio text-indigo-600">
                            <span class="ml-2">Belum</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kesehatan</label>
                    <div class="mt-2 space-x-6">
                        @php $hs = old('health_status', $dailyReport->health_status); @endphp
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sehat"
                                {{ $hs === 'sehat' ? 'checked' : '' }} class="form-radio text-indigo-600">
                            <span class="ml-2">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sakit"
                                {{ $hs === 'sakit' ? 'checked' : '' }} class="form-radio text-indigo-600">
                            <span class="ml-2">Sakit</span>
                        </label>
                    </div>

                    <div id="deskripsi_kesehatan" class="mt-3 {{ $hs === 'sakit' ? '' : 'hidden' }}">
                        <label for="sickness_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Sakit</label>
                        <textarea id="sickness_description" name="sickness_description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">{{ old('sickness_description', $dailyReport->sickness_description) }}</textarea>

                        @php $med = old('medication_status', $dailyReport->medication_status); @endphp
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="disertai obat"
                                    {{ $med === 'disertai obat' ? 'checked' : '' }} class="form-radio text-indigo-600">
                                <span class="ml-2">Disertai Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="tanpa obat"
                                    {{ $med === 'tanpa obat' ? 'checked' : '' }} class="form-radio text-indigo-600">
                                <span class="ml-2">Tanpa Obat</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kondisi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi</label>
                <div class="mt-2 space-x-6">
                    @php $cond = old('condition', $dailyReport->condition); @endphp
                    <label class="inline-flex items-center"><input type="radio" name="condition" value="tenang"
                            {{ $cond === 'tenang' ? 'checked' : '' }} class="form-radio text-indigo-600"><span
                            class="ml-2">Tenang</span></label>
                    <label class="inline-flex items-center"><input type="radio" name="condition" value="rewel"
                            {{ $cond === 'rewel' ? 'checked' : '' }} class="form-radio text-indigo-600"><span
                            class="ml-2">Rewel</span></label>
                    <label class="inline-flex items-center"><input type="radio" name="condition"
                            value="temper tantrum" {{ $cond === 'temper tantrum' ? 'checked' : '' }}
                            class="form-radio text-indigo-600"><span class="ml-2">Temper Tantrum</span></label>
                </div>
            </div>

            {{-- =========================
                 SERVICE 1: BABY (dinamis)
            ========================== --}}
            @if ((int) $dailyReport->service_id === 1)
                @php
                    $bd = $dailyReport->babyDetail;
                    $initAsi = old('asi_formula_items')
                        ? json_decode(old('asi_formula_items'), true)
                        : $bd->asi_formula_items ?? [];
                    $initMpasi = old('mpasi_items') ? json_decode(old('mpasi_items'), true) : $bd->mpasi_items ?? [];
                    $initNaps = old('naps') ? json_decode(old('naps'), true) : $bd->naps ?? [];
                    $initDiap = old('diapers') ? json_decode(old('diapers'), true) : $bd->diapers ?? [];
                @endphp

                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg"
                    x-data="babyEditor({
                        asi: @json($initAsi ?: [['jam' => '', 'takaran' => '', 'asi' => false]]),
                        mpasi: @json($initMpasi ?: [['jam' => '', 'jumlah' => '']]),
                        naps: @json($initNaps ?: [['tidur' => '', 'bangun' => '']]),
                        diapers: @json($initDiap ?: [['jam' => '', 'bak' => false, 'bab' => false]]),
                    })">
                    <h3
                        class="text-center text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-800 py-3">
                        Konsumsi & Perawatan Bayi
                    </h3>

                    <div class="p-4 space-y-6">
                        {{-- ASI / Formula --}}
                        <div class="border rounded-lg overflow-hidden">
                            <div
                                class="px-4 py-2 font-semibold text-blue-600 dark:text-blue-300 border-b flex items-center justify-between">
                                <span>Susu ASI / Formula</span>
                                <button type="button" @click="addAsi()"
                                    class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                    <span class="material-symbols-outlined text-sm align-middle">add</span> Tambah
                                    Baris
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium">Jam</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium">Takaran (ml)</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium">ASI</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(asi, i) in asiList" :key="'asi' + i">
                                            <tr class="border-t">
                                                <td class="px-3 py-2"><input type="time" x-model="asi.jam"
                                                        class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-3 py-2"><input type="number" min="0"
                                                        x-model="asi.takaran"
                                                        class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-3 py-2"><input type="checkbox" x-model="asi.asi"></td>
                                                <td class="px-3 py-2">
                                                    <button type="button" @click="removeAsi(i)"
                                                        class="text-xs text-red-600 hover:underline">
                                                        <span
                                                            class="material-symbols-outlined text-sm align-middle">delete</span>
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- MPASI --}}
                        <div class="border rounded-lg overflow-hidden">
                            <div
                                class="px-4 py-2 font-semibold text-green-600 dark:text-green-300 border-b flex items-center justify-between">
                                <span>Makanan Pendamping (MP) ASI</span>
                                <button type="button" @click="addMpasi()"
                                    class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                    <span class="material-symbols-outlined text-sm align-middle">add</span> Tambah
                                    Baris
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium">Jam</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium">Jumlah</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, i) in mpList" :key="'mp' + i">
                                            <tr class="border-t">
                                                <td class="px-3 py-2"><input type="time" x-model="row.jam"
                                                        class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <div class="space-x-3">
                                                        <label class="text-sm"><input type="radio"
                                                                :name="'mp_jumlah_' + i" value="banyak"
                                                                x-model="row.jumlah"><span
                                                                class="ml-1">Banyak</span></label>
                                                        <label class="text-sm"><input type="radio"
                                                                :name="'mp_jumlah_' + i" value="sedikit"
                                                                x-model="row.jumlah"><span
                                                                class="ml-1">Sedikit</span></label>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <button type="button" @click="removeMpasi(i)"
                                                        class="text-xs text-red-600 hover:underline">
                                                        <span
                                                            class="material-symbols-outlined text-sm align-middle">delete</span>
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-3 grid grid-cols-1 gap-2">
                                <input type="text" name="infant_breakfast_text"
                                    value="{{ old('infant_breakfast_text', $bd->infant_breakfast_text ?? '') }}"
                                    placeholder="Makan Pagi (opsional)"
                                    class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                <input type="text" name="infant_lunch_text"
                                    value="{{ old('infant_lunch_text', $bd->infant_lunch_text ?? '') }}"
                                    placeholder="Makan Siang (opsional)"
                                    class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                <input type="text" name="infant_dinner_text"
                                    value="{{ old('infant_dinner_text', $bd->infant_dinner_text ?? '') }}"
                                    placeholder="Makan Malam (opsional)"
                                    class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        {{-- Naps & Diapers --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border rounded-lg overflow-hidden">
                                <div
                                    class="px-4 py-2 font-semibold text-purple-600 dark:text-purple-300 border-b flex items-center justify-between">
                                    <span>Tidur</span>
                                    <button type="button" @click="addNap()"
                                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                        <span class="material-symbols-outlined text-sm align-middle">add</span> Tambah
                                        Baris
                                    </button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-auto">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Tidur</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Bangun</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(row, i) in napList" :key="'nap' + i">
                                                <tr class="border-t">
                                                    <td class="px-3 py-2"><input type="time" x-model="row.tidur"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2"><input type="time" x-model="row.bangun"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button" @click="removeNap(i)"
                                                            class="text-xs text-red-600 hover:underline">
                                                            <span
                                                                class="material-symbols-outlined text-sm align-middle">delete</span>
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="border rounded-lg overflow-hidden">
                                <div
                                    class="px-4 py-2 font-semibold text-yellow-600 dark:text-yellow-300 border-b flex items-center justify-between">
                                    <span>Popok</span>
                                    <button type="button" @click="addDiaper()"
                                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                        <span class="material-symbols-outlined text-sm align-middle">add</span> Tambah
                                        Baris
                                    </button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-auto">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Jam</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">BAK</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">BAB</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(row, i) in diaperList" :key="'dip' + i">
                                                <tr class="border-t">
                                                    <td class="px-3 py-2"><input type="time" x-model="row.jam"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2"><input type="checkbox" x-model="row.bak">
                                                        <span class="text-xs ml-1">Ya</span>
                                                    </td>
                                                    <td class="px-3 py-2"><input type="checkbox" x-model="row.bab">
                                                        <span class="text-xs ml-1">Ya</span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button" @click="removeDiaper(i)"
                                                            class="text-xs text-red-600 hover:underline">
                                                            <span
                                                                class="material-symbols-outlined text-sm align-middle">delete</span>
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden JSON holders --}}
                        <input type="hidden" name="asi_formula_items" id="asi_formula_items_json">
                        <input type="hidden" name="mpasi_items" id="mpasi_items_json">
                        <input type="hidden" name="naps" id="naps_json">
                        <input type="hidden" name="diapers" id="diapers_json">
                    </div>
                </div>
            @endif

            {{-- =========================
                 SERVICE 2: CHILDREN
            ========================== --}}
            @if ((int) $dailyReport->service_id === 2)
                @php $cd = $dailyReport->childrenDetail; @endphp
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-indigo-600 dark:bg-indigo-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Waktu</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Kegiatan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Salam & Doa Pagi --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">06:30 - 07:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Salam Penyambutan dan
                                    Do'a Pagi</td>
                                <td class="px-6 py-4">
                                    @php $gmp = old('greeting_and_morning_prayer', $cd->greeting_and_morning_prayer ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="greeting_and_morning_prayer" value="mengikuti"
                                            {{ $gmp === 'mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="greeting_and_morning_prayer" value="tidak mengikuti"
                                            {{ $gmp === 'tidak mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>

                            {{-- Session 1 --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">07:30 - 09:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Bermain & Belajar <span
                                        class="font-semibold">Session 1</span></td>
                                <td class="px-6 py-4">
                                    <select id="session1_material_id" name="session1_material_id"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                                        <option value="">-- Pilih Sub Theme / Materi --</option>
                                        {{-- akan diisi via AJAX --}}
                                    </select>
                                    <div id="themeName1" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></div>

                                    @php $s1a = old('session1_activity', $cd->session1_activity ?? null); @endphp
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="BB"
                                                {{ $s1a === 'BB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="MB"
                                                {{ $s1a === 'MB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="BSH"
                                                {{ $s1a === 'BSH' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="BSB"
                                                {{ $s1a === 'BSB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSB</span></label>
                                    </div>
                                </td>
                            </tr>

                            {{-- Toilet & Dhuha --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">09:00 - 09:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Toilet Training & Sholat
                                    Dhuha</td>
                                <td class="px-6 py-4">
                                    @php $td = old('toilet_training_and_duha_prayer', $cd->toilet_training_and_duha_prayer ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="toilet_training_and_duha_prayer" value="mengikuti"
                                            {{ $td === 'mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="toilet_training_and_duha_prayer" value="tidak mengikuti"
                                            {{ $td === 'tidak mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>

                            {{-- Session 2 --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">09:30 - 10:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Bermain & Belajar <span
                                        class="font-semibold">Session 2</span></td>
                                <td class="px-6 py-4">
                                    <select id="session2_material_id" name="session2_material_id"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                                        <option value="">-- Pilih Sub Theme / Materi --</option>
                                        {{-- akan diisi via AJAX --}}
                                    </select>
                                    <div id="themeName2" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></div>

                                    @php $s2a = old('session2_activity', $cd->session2_activity ?? null); @endphp
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="BB"
                                                {{ $s2a === 'BB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="MB"
                                                {{ $s2a === 'MB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="BSH"
                                                {{ $s2a === 'BSH' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="BSB"
                                                {{ $s2a === 'BSB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSB</span></label>
                                    </div>
                                </td>
                            </tr>

                            {{-- Snack Pagi --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">10:00 - 10:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Snack Pagi</td>
                                <td class="px-6 py-4">
                                    @php $ms = old('morning_snack', $cd->morning_snack ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="morning_snack" value="habis"
                                            {{ $ms === 'habis' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Habis</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="morning_snack" value="tidak habis"
                                            {{ $ms === 'tidak habis' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Habis</span></label>
                                </td>
                            </tr>

                            {{-- Kerapian & Kemandirian --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">10:30 - 11:15</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Kerapian & Kemandirian
                                </td>
                                <td class="px-6 py-4">
                                    @php $ni = old('neatness_and_independence', $cd->neatness_and_independence ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="neatness_and_independence" value="mandiri"
                                            {{ $ni === 'mandiri' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mandiri</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="neatness_and_independence" value="kurang mandiri"
                                            {{ $ni === 'kurang mandiri' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Kurang
                                            Mandiri</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="neatness_and_independence" value="tidak mandiri"
                                            {{ $ni === 'tidak mandiri' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Mandiri</span></label>
                                </td>
                            </tr>

                            {{-- Makan Siang Ceria --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">11:15 - 11:45</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Makan Siang Ceria</td>
                                <td class="px-6 py-4">
                                    @php $cl = old('cheerful_lunch', $cd->cheerful_lunch ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cheerful_lunch" value="habis"
                                            {{ $cl === 'habis' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Habis</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cheerful_lunch" value="sisa sedikit"
                                            {{ $cl === 'sisa sedikit' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Sisa
                                            Sedikit</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="cheerful_lunch" value="sisa banyak"
                                            {{ $cl === 'sisa banyak' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Sisa
                                            Banyak</span></label>
                                </td>
                            </tr>

                            {{-- Kebersihan & Gosok Gigi --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">11:45 - 12:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Kebersihan & Training
                                    Gosok Gigi</td>
                                <td class="px-6 py-4">
                                    @php $cb = old('cleanliness_and_brushing_training', $cd->cleanliness_and_brushing_training ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cleanliness_and_brushing_training" value="kurang"
                                            {{ $cb === 'kurang' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Kurang</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cleanliness_and_brushing_training" value="cukup"
                                            {{ $cb === 'cukup' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Cukup</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="cleanliness_and_brushing_training" value="baik"
                                            {{ $cb === 'baik' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Baik</span></label>
                                </td>
                            </tr>

                            {{-- Sholat Dzuhur --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">12:00 - 12:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Sholat Dzuhur</td>
                                <td class="px-6 py-4">
                                    @php $dp = old('dhuhr_prayer', $cd->dhuhr_prayer ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="dhuhr_prayer" value="mengikuti"
                                            {{ $dp === 'mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="dhuhr_prayer"
                                            value="tidak mengikuti" {{ $dp === 'tidak mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>

                            {{-- Tidur Sehat --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">12:30 - 14:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Tidur Sehat (Penjemputan
                                    1)</td>
                                <td class="px-6 py-4">
                                    @php $hs = old('healthy_sleep', $cd->healthy_sleep ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="healthy_sleep" value="tidur"
                                            {{ $hs === 'tidur' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidur</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="healthy_sleep" value="tidur sebentar"
                                            {{ $hs === 'tidur sebentar' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidur
                                            Sebentar</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="healthy_sleep" value="tidak tidur"
                                            {{ $hs === 'tidak tidur' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Tidur</span></label>
                                </td>
                            </tr>

                            {{-- Mandi Sore --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">14:00 - 14:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Mandi Sore</td>
                                <td class="px-6 py-4">
                                    @php $ab = old('afternoon_bath', $cd->afternoon_bath ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="afternoon_bath" value="mengikuti"
                                            {{ $ab === 'mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="afternoon_bath" value="tidak mengikuti"
                                            {{ $ab === 'tidak mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>

                            {{-- Snack Sore --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">14:30 - 15:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Snack Sore</td>
                                <td class="px-6 py-4">
                                    @php $as = old('afternoon_snack', $cd->afternoon_snack ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="afternoon_snack" value="habis"
                                            {{ $as === 'habis' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Habis</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="afternoon_snack" value="tidak habis"
                                            {{ $as === 'tidak habis' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Habis</span></label>
                                </td>
                            </tr>

                            {{-- Sholat Ashar --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">15:00 - 15:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Sholat Ashar</td>
                                <td class="px-6 py-4">
                                    @php $ap = old('asr_prayer', $cd->asr_prayer ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="asr_prayer" value="mengikuti"
                                            {{ $ap === 'mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio" name="asr_prayer"
                                            value="tidak mengikuti" {{ $ap === 'tidak mengikuti' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>

                            {{-- Ekstra Stimulasi --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">15:30 - 16:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Ekstra Stimulasi
                                    (Penjemputan 2)</td>
                                <td class="px-6 py-4">
                                    <textarea name="extra_stimulation_description" rows="2"
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm"
                                        placeholder="Deskripsi (opsional)">{{ old('extra_stimulation_description', $cd->extra_stimulation_description ?? '') }}</textarea>
                                    @php $es = old('extra_stimulation', $cd->extra_stimulation ?? null); @endphp
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="BB"
                                                {{ $es === 'BB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="MB"
                                                {{ $es === 'MB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="BSH"
                                                {{ $es === 'BSH' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="BSB"
                                                {{ $es === 'BSB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSB</span></label>
                                    </div>
                                </td>
                            </tr>

                            {{-- Permainan Ceria --}}
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">16:00 - 17:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Permainan Ceria
                                    (Penjemputan 3)</td>
                                <td class="px-6 py-4">
                                    <textarea name="cheerful_play_description" rows="2"
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm"
                                        placeholder="Deskripsi (opsional)">{{ old('cheerful_play_description', $cd->cheerful_play_description ?? '') }}</textarea>
                                    @php $cp = old('cheerful_play', $cd->cheerful_play ?? null); @endphp
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="BB"
                                                {{ $cp === 'BB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="MB"
                                                {{ $cp === 'MB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="BSH"
                                                {{ $cp === 'BSH' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="BSB"
                                                {{ $cp === 'BSB' ? 'checked' : '' }}
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSB</span></label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Stimulasi (Auto, Readonly) --}}
            <div>
                <label for="stimulation_description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stimulasi (otomatis dari
                    MMDST)</label>
                <div class="flex gap-2 items-center">
                    <textarea name="stimulation_description" id="stimulation_description" rows="4" readonly
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 cursor-not-allowed"
                        placeholder="Memuat saran stimulasi otomatis...">{{ old('stimulation_description', $dailyReport->stimulation_description) }}</textarea>
                    <button type="button" id="reloadStimBtn"
                        class="mt-1 inline-flex items-center bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded">
                        <span class="material-symbols-outlined mr-1">refresh</span> Muat Ulang
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Terisi otomatis berdasarkan rentang usia &
                    item yang belum lulus.</p>
            </div>

            {{-- Catatan --}}
            <div>
                <label for="notes"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                <textarea id="notes" name="notes" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white">{{ old('notes', $dailyReport->notes) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('daily-report.show', $dailyReport) }}"
                    class="inline-flex items-center bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 px-4 py-2 rounded">
                    <span class="material-symbols-outlined mr-1">arrow_back</span> Kembali
                </a>
                <button type="submit" id="submitFormButton"
                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                    <span class="material-symbols-outlined mr-1">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts --}}
    <script>
        // Toggle form sakit
        function toggleKesehatanUI(visible) {
            const el = document.getElementById('deskripsi_kesehatan');
            if (!el) return;
            el.classList.toggle('hidden', !visible);
        }
        document.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'health_status') {
                toggleKesehatanUI(e.target.value === 'sakit');
            }
        });

        // Helper HH:mm
        function hhmm(time) {
            if (!time) return '';
            return (time || '').slice(0, 5);
        }

        // Absensi
        function checkAttendance(studentId, dateStr) {
            fetch(`{{ route('daily-report.check-attendance', ['student' => '_SID_', 'date' => '_DATE_']) }}`
                    .replace('_SID_', studentId).replace('_DATE_', dateStr))
                .then(r => r.json())
                .then(data => {
                    document.getElementById('attendance-status').textContent = data.status || '';
                    if (data.check_in_time && data.check_in_time !== 'Belum Check-in') {
                        document.getElementById('arrival_time').value = hhmm(data.check_in_time);
                    }
                    if (data.check_out_time && data.check_out_time !== 'Belum Check-out') {
                        document.getElementById('departure_time').value = hhmm(data.check_out_time);
                    }
                })
                .catch(() => {
                    document.getElementById('attendance-status').textContent = 'Gagal memuat data absensi.';
                });
        }

        // Subthemes (Children)
        function loadSubthemes(dateStr, selected1 = null, selected2 = null) {
            const s1 = document.getElementById('session1_material_id');
            const s2 = document.getElementById('session2_material_id');
            if (!s1 && !s2) return;

            fetch(`{{ route('daily-report.get-subthemes', ['date' => '_DATE_']) }}`.replace('_DATE_', dateStr))
                .then(r => r.json())
                .then(({
                    subthemes
                }) => {
                    const options = ['<option value="">-- Pilih Sub Theme / Materi --</option>'];
                    (subthemes || []).forEach(st => {
                        (st.material || []).forEach(m => {
                            options.push(
                                `<option value="${m.id}" data-theme-name="${st.theme_name}" data-sub-theme-name="${st.sub_theme_name}">
                                    ${m.material_name}
                                 </option>`
                            );
                        });
                    });

                    if (s1) {
                        s1.innerHTML = options.join('');
                        if (selected1) {
                            s1.value = String(selected1);
                            showThemeName('session1_material_id', 'themeName1');
                        }
                    }
                    if (s2) {
                        s2.innerHTML = options.join('');
                        if (selected2) {
                            s2.value = String(selected2);
                            showThemeName('session2_material_id', 'themeName2');
                        }
                    }
                })
                .catch(() => {});
        }

        function showThemeName(selectId, targetId) {
            const sel = document.getElementById(selectId);
            const tgt = document.getElementById(targetId);
            if (!sel || !tgt) return;
            const opt = sel.options[sel.selectedIndex];
            const theme = opt?.getAttribute('data-theme-name') || '';
            const sub = opt?.getAttribute('data-sub-theme-name') || '';
            tgt.innerHTML = (theme || sub) ?
                `<p><strong>Tema:</strong> ${theme}</p><p><strong>Sub Tema:</strong> ${sub}</p>` : '';
        }

        // Stimulasi
        function loadStimulation(activityTransactionId, dateStr) {
            const textarea = document.getElementById('stimulation_description');
            if (!textarea) return;
            textarea.value = 'Memuat saran stimulasi...';

            fetch(`{{ route('daily-report.stimulation.suggest', ['activityTransaction' => '_AT_', 'date' => '_DATE_']) }}`
                    .replace('_AT_', {{ $dailyReport->activity_transaction_id }})
                    .replace('_DATE_', dateStr))
                .then(r => r.json())
                .then(data => {
                    textarea.value = (data && data.text) ? data.text : (data?.message ||
                        'Tidak ada saran stimulasi tersedia.');
                })
                .catch(() => {
                    textarea.value = 'Gagal memuat saran stimulasi.';
                });
        }

        // INIT
        document.addEventListener('DOMContentLoaded', function() {
            const studentId = {{ $dailyReport->activityTransaction->student_id }};
            const serviceId = {{ (int) $dailyReport->service_id }};
            const periodEl = document.getElementById('period');

            // Absensi & Stimulasi awal
            checkAttendance(studentId, periodEl.value);
            loadStimulation({{ $dailyReport->activity_transaction_id }}, periodEl.value);

            // Children: load subthemes + preselect existing
            @if ((int) $dailyReport->service_id === 2)
                loadSubthemes(
                    periodEl.value,
                    @json(old('session1_material_id', $cd->session1_material_id ?? null)),
                    @json(old('session2_material_id', $cd->session2_material_id ?? null))
                );

                document.getElementById('session1_material_id')?.addEventListener('change', () => showThemeName(
                    'session1_material_id', 'themeName1'));
                document.getElementById('session2_material_id')?.addEventListener('change', () => showThemeName(
                    'session2_material_id', 'themeName2'));
            @endif

            // On change period
            periodEl.addEventListener('change', function() {
                const d = this.value;
                checkAttendance(studentId, d);
                loadStimulation({{ $dailyReport->activity_transaction_id }}, d);
                if (serviceId === 2) {
                    loadSubthemes(
                        d,
                        document.getElementById('session1_material_id')?.value || null,
                        document.getElementById('session2_material_id')?.value || null
                    );
                }
            });

            // Toggler sakit initial
            const checkedHealth = document.querySelector('input[name="health_status"]:checked');
            toggleKesehatanUI(checkedHealth && checkedHealth.value === 'sakit');

            // Reload stimulasi by button
            document.getElementById('reloadStimBtn')?.addEventListener('click', () => {
                loadStimulation({{ $dailyReport->activity_transaction_id }}, periodEl.value);
            });

            // Serializer JSON (Baby only)
            document.getElementById('dailyReportEditForm').addEventListener('submit', () => {
                const root = document.querySelector('[x-data]');
                if (!root) return; // non-baby
                const scope = Alpine.$data(root);

                const asis = (scope.asiList || []).map(r => ({
                    jam: (r.jam || '').trim(),
                    takaran: r.takaran !== '' && r.takaran != null ? Number(r.takaran) : null,
                    asi: !!r.asi
                })).filter(r => r.jam || r.takaran || r.asi);

                const mpasi = (scope.mpList || []).map(r => ({
                    jam: (r.jam || '').trim(),
                    jumlah: (r.jumlah || '').trim()
                })).filter(r => r.jam || r.jumlah);

                const naps = (scope.napList || []).map(r => ({
                    tidur: (r.tidur || '').trim(),
                    bangun: (r.bangun || '').trim()
                })).filter(r => r.tidur || r.bangun);

                const diapers = (scope.diaperList || []).map(r => ({
                    jam: (r.jam || '').trim(),
                    bak: !!r.bak,
                    bab: !!r.bab
                })).filter(r => r.jam || r.bak || r.bab);

                const setVal = (id, v) => {
                    const el = document.getElementById(id);
                    if (el) el.value = JSON.stringify(v);
                };
                setVal('asi_formula_items_json', asis);
                setVal('mpasi_items_json', mpasi);
                setVal('naps_json', naps);
                setVal('diapers_json', diapers);
            });
        });

        // Alpine helpers for Baby editor
        function babyEditor({
            asi = [],
            mpasi = [],
            naps = [],
            diapers = []
        }) {
            return {
                asiList: asi,
                mpList: mpasi,
                napList: naps,
                diaperList: diapers,

                addAsi() {
                    this.asiList.push({
                        jam: '',
                        takaran: '',
                        asi: false
                    });
                },
                removeAsi(i) {
                    this.asiList.splice(i, 1);
                },

                addMpasi() {
                    this.mpList.push({
                        jam: '',
                        jumlah: ''
                    });
                },
                removeMpasi(i) {
                    this.mpList.splice(i, 1);
                },

                addNap() {
                    this.napList.push({
                        tidur: '',
                        bangun: ''
                    });
                },
                removeNap(i) {
                    this.napList.splice(i, 1);
                },

                addDiaper() {
                    this.diaperList.push({
                        jam: '',
                        bak: false,
                        bab: false
                    });
                },
                removeDiaper(i) {
                    this.diaperList.splice(i, 1);
                },
            }
        }
    </script>
</x-app-layout>
