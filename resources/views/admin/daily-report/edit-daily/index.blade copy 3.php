<x-app-layout>
    <x-slot:title>Edit Laporan Harian</x-slot:title>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">
            Al Jannah Preschool and Day Care
        </h2>

        @if ($activityTransaction->service_id == 1)
            <h3 class="text-lg font-semibold mb-8 text-center text-gray-700 dark:text-gray-300">
                Edit Laporan Harian — Baby Childhood
            </h3>
        @else
            <h3 class="text-lg font-semibold mb-8 text-center text-gray-700 dark:text-gray-300">
                Edit Laporan Harian Usia 25 Bulan - 72 Bulan — Children Daycare
            </h3>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 rounded bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                <p class="font-bold">Terjadi kesalahan:</p>
                <ul class="list-disc ml-5 mt-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="dailyReportEditForm" action="{{ route('daily-report.update', $dailyReport->id) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Identitas Siswa --}}
            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                <div>
                    <label for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama:</label>
                    <input type="text" id="name"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm bg-gray-100 dark:bg-gray-800 cursor-not-allowed dark:text-gray-300"
                        value="{{ $activityTransaction->student->student_name }}" disabled>
                    <p id="attendance-status" class="text-xs text-gray-500 dark:text-gray-400 mt-1"></p>
                </div>
                <input type="hidden" name="activity_transaction_id" value="{{ $activityTransaction->id }}">
            </div>

            {{-- Tanggal & Kesehatan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="period"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode:</label>
                    <input type="date" name="period" id="period"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                        value="{{ old('period', \Illuminate\Support\Carbon::parse($dailyReport->period)->toDateString()) }}">
                </div>

                <div>
                    <label for="body_temperature" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Suhu Tubuh (°C):
                    </label>
                    <input type="number" step="0.1" name="body_temperature" id="body_temperature"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                        value="{{ old('body_temperature', $dailyReport->body_temperature) }}" placeholder="36.5">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Makan pagi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Makan Pagi:</label>
                    <div class="mt-2 space-x-6">
                        @php $bf = old('breakfast', $dailyReport->breakfast); @endphp
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="sudah" {{ $bf === 'sudah' ? 'checked' : '' }}
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="belum" {{ $bf === 'belum' ? 'checked' : '' }}
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Belum</span>
                        </label>
                    </div>
                </div>

                {{-- Kesehatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kesehatan:</label>
                    <div class="mt-2 space-x-6">
                        @php $hs = old('health_status', $dailyReport->health_status); @endphp
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sehat"
                                {{ $hs === 'sehat' ? 'checked' : '' }} class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sakit"
                                {{ $hs === 'sakit' ? 'checked' : '' }} class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sakit</span>
                        </label>
                    </div>

                    <div id="deskripsi_kesehatan" class="mt-3 {{ $hs === 'sakit' ? '' : 'hidden' }}">
                        <label for="sickness_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Deskripsi Sakit:
                        </label>
                        <textarea name="sickness_description" id="sickness_description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('sickness_description', $dailyReport->sickness_description) }}</textarea>

                        @php $med = old('medication_status', $dailyReport->medication_status); @endphp
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="disertai obat"
                                    {{ $med === 'disertai obat' ? 'checked' : '' }} class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Disertai Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="tanpa obat"
                                    {{ $med === 'tanpa obat' ? 'checked' : '' }} class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Tanpa Obat</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================
                    SERVICE 1: BABY
            ========================== --}}
            @if ($activityTransaction->service_id == 1)
                @php
                    $bd = $dailyReport->babyDetail;
                    // Ambil nilai awal untuk editor (old() > model)
                    $initAsi =
                        old('asi_formula_items') !== null
                            ? (json_decode(old('asi_formula_items'), true) ?:
                            [])
                            : (is_array($bd->asi_formula_items ?? null)
                                ? $bd->asi_formula_items
                                : []);
                    $initMpasi =
                        old('mpasi_items') !== null
                            ? (json_decode(old('mpasi_items'), true) ?:
                            [])
                            : (is_array($bd->mpasi_items ?? null)
                                ? $bd->mpasi_items
                                : []);
                    $initNaps =
                        old('naps') !== null
                            ? (json_decode(old('naps'), true) ?:
                            [])
                            : (is_array($bd->naps ?? null)
                                ? $bd->naps
                                : []);
                    $initDiap =
                        old('diapers') !== null
                            ? (json_decode(old('diapers'), true) ?:
                            [])
                            : (is_array($bd->diapers ?? null)
                                ? $bd->diapers
                                : []);
                @endphp

                <div id="babyEditorBox"
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg"
                    x-data='babyEditor({
                        asi: @json($initAsi),
                        mpasi: @json($initMpasi),
                        naps: @json($initNaps),
                        diapers: @json($initDiap)
                     })'>
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
                                <button type="button" @click="addAsi(); $nextTick(()=>serializeBabyJson());"
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
                                                <td class="px-3 py-2">
                                                    <input type="time" x-model="asi.jam"
                                                        @change="serializeBabyJson()"
                                                        class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="number" min="0" x-model="asi.takaran"
                                                        @change="serializeBabyJson()"
                                                        class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="checkbox" x-model="asi.asi"
                                                        @change="serializeBabyJson()">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <button type="button"
                                                        @click="removeAsi(i); $nextTick(()=>serializeBabyJson());"
                                                        class="text-xs text-red-600 hover:underline">
                                                        <span
                                                            class="material-symbols-outlined text-sm align-middle">delete</span>
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="asiList.length === 0">
                                            <td colspan="4"
                                                class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                Belum ada data.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- MPASI --}}
                        <div class="border rounded-lg overflow-hidden">
                            <div
                                class="px-4 py-2 font-semibold text-green-600 dark:text-green-300 border-b flex items-center justify-between">
                                <span>Makanan Pendamping (MP) ASI</span>
                                <button type="button" @click="addMpasi(); $nextTick(()=>serializeBabyJson());"
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
                                                <td class="px-3 py-2">
                                                    <input type="time" x-model="row.jam"
                                                        @change="serializeBabyJson()"
                                                        class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <div class="space-x-3">
                                                        <label class="text-sm">
                                                            <input type="radio" :name="'mp_jumlah_' + i"
                                                                value="banyak" x-model="row.jumlah"
                                                                @change="serializeBabyJson()">
                                                            <span class="ml-1">Banyak</span>
                                                        </label>
                                                        <label class="text-sm">
                                                            <input type="radio" :name="'mp_jumlah_' + i"
                                                                value="sedikit" x-model="row.jumlah"
                                                                @change="serializeBabyJson()">
                                                            <span class="ml-1">Sedikit</span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <button type="button"
                                                        @click="removeMpasi(i); $nextTick(()=>serializeBabyJson());"
                                                        class="text-xs text-red-600 hover:underline">
                                                        <span
                                                            class="material-symbols-outlined text-sm align-middle">delete</span>
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="mpList.length === 0">
                                            <td colspan="3"
                                                class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                Belum ada data.</td>
                                        </tr>
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
                            {{-- Naps --}}
                            <div class="border rounded-lg overflow-hidden">
                                <div
                                    class="px-4 py-2 font-semibold text-purple-600 dark:text-purple-300 border-b flex items-center justify-between">
                                    <span>Tidur</span>
                                    <button type="button" @click="addNap(); $nextTick(()=>serializeBabyJson());"
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
                                                    <td class="px-3 py-2">
                                                        <input type="time" x-model="row.tidur"
                                                            @change="serializeBabyJson()"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="time" x-model="row.bangun"
                                                            @change="serializeBabyJson()"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button"
                                                            @click="removeNap(i); $nextTick(()=>serializeBabyJson());"
                                                            class="text-xs text-red-600 hover:underline">
                                                            <span
                                                                class="material-symbols-outlined text-sm align-middle">delete</span>
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr x-show="napList.length === 0">
                                                <td colspan="3"
                                                    class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    Belum ada data.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Diapers --}}
                            <div class="border rounded-lg overflow-hidden">
                                <div
                                    class="px-4 py-2 font-semibold text-yellow-600 dark:text-yellow-300 border-b flex items-center justify-between">
                                    <span>Popok</span>
                                    <button type="button" @click="addDiaper(); $nextTick(()=>serializeBabyJson());"
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
                                                    <td class="px-3 py-2">
                                                        <input type="time" x-model="row.jam"
                                                            @change="serializeBabyJson()"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="checkbox" x-model="row.bak"
                                                            @change="serializeBabyJson()">
                                                        <span class="text-xs ml-1">Ya</span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="checkbox" x-model="row.bab"
                                                            @change="serializeBabyJson()">
                                                        <span class="text-xs ml-1">Ya</span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button"
                                                            @click="removeDiaper(i); $nextTick(()=>serializeBabyJson());"
                                                            class="text-xs text-red-600 hover:underline">
                                                            <span
                                                                class="material-symbols-outlined text-sm align-middle">delete</span>
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr x-show="diaperList.length === 0">
                                                <td colspan="4"
                                                    class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    Belum ada data.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden JSON holders (selalu diisi via Alpine serialize) --}}
                        <input type="hidden" name="asi_formula_items" id="asi_formula_items_json"
                            value="{{ e(json_encode($initAsi)) }}">
                        <input type="hidden" name="mpasi_items" id="mpasi_items_json"
                            value="{{ e(json_encode($initMpasi)) }}">
                        <input type="hidden" name="naps" id="naps_json"
                            value="{{ e(json_encode($initNaps)) }}">
                        <input type="hidden" name="diapers" id="diapers_json"
                            value="{{ e(json_encode($initDiap)) }}">
                    </div>
                </div>
            @endif

            {{-- =========================
                    SERVICE 2: CHILDREN
            ========================== --}}
            @if ($activityTransaction->service_id == 2)
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
                                        {{-- akan diisi via JS --}}
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
                                        {{-- akan diisi via JS --}}
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
                                    @php $hs2 = old('healthy_sleep', $cd->healthy_sleep ?? null); @endphp
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="healthy_sleep" value="tidur"
                                            {{ $hs2 === 'tidur' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidur</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="healthy_sleep" value="tidur sebentar"
                                            {{ $hs2 === 'tidur sebentar' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidur
                                            Sebentar</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="healthy_sleep" value="tidak tidur"
                                            {{ $hs2 === 'tidak tidur' ? 'checked' : '' }}
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

            {{-- Kondisi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi:</label>
                <div class="mt-2 space-x-6">
                    @php $cond = old('condition', $dailyReport->condition); @endphp
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="tenang"
                            {{ $cond === 'tenang' ? 'checked' : '' }} class="form-radio text-indigo-600">
                        <span class="ml-2">Tenang</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="rewel"
                            {{ $cond === 'rewel' ? 'checked' : '' }} class="form-radio text-indigo-600">
                        <span class="ml-2">Rewel</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="temper tantrum"
                            {{ $cond === 'temper tantrum' ? 'checked' : '' }} class="form-radio text-indigo-600">
                        <span class="ml-2">Temper Tantrum</span>
                    </label>
                </div>
            </div>

            {{-- Stimulasi (Auto, Readonly) --}}
            <div>
                <label for="stimulation_description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stimulasi (otomatis dari
                    MMDST):</label>
                <div class="flex items-center gap-2 mt-1">
                    <textarea name="stimulation_description" id="stimulation_description" rows="4" readonly
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 cursor-not-allowed"
                        placeholder="Memuat saran stimulasi otomatis...">{{ old('stimulation_description', $dailyReport->stimulation_description) }}</textarea>
                    <button type="button" id="reloadStimBtn" title="Muat Ulang Saran Stimulasi"
                        class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white p-2 rounded h-10 w-10 flex-shrink-0">
                        <span class="material-symbols-outlined">refresh</span>
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">*Terisi otomatis berdasarkan rentang usia
                    (hari) & item yang belum lulus. (Tidak dapat diedit)</p>
            </div>

            {{-- Catatan --}}
            <div>
                <label for="notes"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan:</label>
                <textarea name="notes" id="notes" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('notes', $dailyReport->notes) }}</textarea>
            </div>

            <div class="flex justify-end mt-6 space-x-4 pt-6 border-t dark:border-gray-700">
                <a href="{{ route('daily-report.index') }}"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md font-semibold text-sm hover:bg-gray-300 dark:hover:bg-gray-600">
                    Batal
                </a>
                <button type="submit" id="submitFormButton"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md font-semibold text-sm hover:bg-indigo-700">
                    <span class="flex items-center"><span class="material-symbols-outlined mr-1 text-base">save</span>
                        Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===================================
            // Variabel Global
            // ===================================
            const studentId = {{ $activityTransaction->student->id }};
            const activityTransactionId = {{ $activityTransaction->id }};
            const serviceId = {{ $activityTransaction->service_id }};
            const periodEl = document.getElementById('period');

            // ===================================
            // Helper Functions
            // ===================================
            const toggleKesehatanUI = (visible) => {
                document.getElementById('deskripsi_kesehatan')?.classList.toggle('hidden', !visible);
            };

            const hhmm = (time) => time ? String(time).slice(0, 5) : '';

            const showThemeName = (selectId, targetId) => {
                const sel = document.getElementById(selectId);
                const tgt = document.getElementById(targetId);
                if (!sel || !tgt) return;
                const opt = sel.options[sel.selectedIndex];
                const theme = opt?.dataset.themeName || '';
                const sub = opt?.dataset.subThemeName || '';
                tgt.innerHTML = (theme || sub) ?
                    `<p class="text-xs"><strong>Tema:</strong> ${theme}</p><p class="text-xs"><strong>Sub Tema:</strong> ${sub}</p>` :
                    '';
            };

            // ===================================
            // AJAX Functions
            // ===================================
            const checkAttendance = (studentId, dateStr) => {
                fetch(`{{ route('daily-report.check-attendance', ['student' => '_SID_', 'date' => '_DATE_']) }}`
                        .replace('_SID_', studentId).replace('_DATE_', dateStr))
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('attendance-status').textContent = data.status || '';
                    }).catch(() => {
                        document.getElementById('attendance-status').textContent = 'Gagal memuat absensi.';
                    });
            };

            const loadStimulation = (actTxId, dateStr) => {
                const textarea = document.getElementById('stimulation_description');
                if (!textarea) return;
                textarea.value = 'Memuat saran stimulasi...';
                fetch(`{{ route('daily-report.stimulation.suggest', ['activityTransaction' => '_AT_', 'date' => '_DATE_']) }}`
                        .replace('_AT_', actTxId).replace('_DATE_', dateStr))
                    .then(r => r.json())
                    .then(data => {
                        textarea.value = (data && data.text) ? data.text : 'Tidak ada saran stimulasi.';
                    }).catch(() => {
                        textarea.value = 'Gagal memuat saran stimulasi.';
                    });
            };

            const loadMaterials = (dateStr, selected1 = null, selected2 = null) => {
                const s1 = document.getElementById('session1_material_id');
                const s2 = document.getElementById('session2_material_id');
                if (!s1 && !s2) return;

                const loadingOpt = '<option value="" disabled>Memuat materi...</option>';
                if (s1) s1.innerHTML = loadingOpt;
                if (s2) s2.innerHTML = loadingOpt;

                fetch(`{{ route('daily-report.get-subthemes', ['date' => '_DATE_']) }}`.replace('_DATE_',
                        dateStr))
                    .then(r => r.json())
                    .then(({
                        materials
                    }) => {
                        let options = ['<option value="">-- Pilih Materi --</option>'];
                        if (!materials || materials.length === 0) {
                            options.push(
                                '<option value="" disabled>Tidak ada materi pada periode ini</option>');
                        } else {
                            materials.forEach(m => {
                                options.push(
                                    `<option value="${m.id}" data-theme-name="${m.theme_name}" data-sub-theme-name="${m.sub_theme_name}">${m.material_name}</option>`
                                );
                            });
                        }

                        if (s1) {
                            s1.innerHTML = options.join('');
                            if (selected1) s1.value = String(selected1);
                            showThemeName('session1_material_id', 'themeName1');
                        }
                        if (s2) {
                            s2.innerHTML = options.join('');
                            if (selected2) s2.value = String(selected2);
                            showThemeName('session2_material_id', 'themeName2');
                        }
                    })
                    .catch(() => {
                        const errorOpt = '<option value="" disabled>Gagal memuat materi</option>';
                        if (s1) s1.innerHTML = errorOpt;
                        if (s2) s2.innerHTML = errorOpt;
                    });
            };

            // ===================================
            // Event Listeners & Initializations
            // ===================================

            // 1. Inisialisasi awal saat halaman dimuat
            checkAttendance(studentId, periodEl.value);
            loadStimulation(activityTransactionId, periodEl.value);

            if (serviceId === 2) {
                loadMaterials(
                    periodEl.value,
                    @json(old('session1_material_id', $cd->session1_material_id ?? null)),
                    @json(old('session2_material_id', $cd->session2_material_id ?? null))
                );
                document.getElementById('session1_material_id')?.addEventListener('change', () => showThemeName(
                    'session1_material_id', 'themeName1'));
                document.getElementById('session2_material_id')?.addEventListener('change', () => showThemeName(
                    'session2_material_id', 'themeName2'));
            }

            // 2. Event listener untuk perubahan tanggal
            periodEl.addEventListener('change', function() {
                const newDate = this.value;
                checkAttendance(studentId, newDate);
                loadStimulation(activityTransactionId, newDate);
                if (serviceId === 2) {
                    loadMaterials(
                        newDate,
                        document.getElementById('session1_material_id')?.value,
                        document.getElementById('session2_material_id')?.value
                    );
                }
            });


            // 3. Event listener untuk tombol muat ulang stimulasi
            document.getElementById('reloadStimBtn')?.addEventListener('click', () => {
                loadStimulation(activityTransactionId, periodEl.value);
            });

            // 4. Inisialisasi dan event listener radio button kesehatan
            document.querySelectorAll('input[name="health_status"]').forEach(el => {
                el.addEventListener('change', (e) => toggleKesehatanUI(e.target.value === 'sakit'));
            });
            toggleKesehatanUI(document.querySelector('input[name="health_status"]:checked')?.value === 'sakit');

            // 5. Konfirmasi sebelum submit form
            document.getElementById('dailyReportEditForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: 'Pastikan semua data yang Anda masukkan sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#4f46e5',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
