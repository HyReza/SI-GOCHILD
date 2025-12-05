<x-app-layout>
    <div class="">
        <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">
                Al Jannah Preschool and Day Care
            </h2>

            @if ($activityTransaction->service_id == 1)
            <h3 class="text-lg font-semibold mb-8 text-center text-gray-700 dark:text-gray-300">
                Laporan Harian — Baby Childhood
            </h3>
            @else
            <h3 class="text-lg font-semibold mb-8 text-center text-gray-700 dark:text-gray-300">
                Laporan Harian Usia 25 Bulan - 72 Bulan — Children Daycare
            </h3>
            @endif

            @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 p-4 rounded mb-6">
                {{ session('success') }}
            </div>
            @endif

            <form id="dailyReportForm" action="{{ route('daily-report.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Identitas Siswa --}}
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <div>
                        <label for="name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama:</label>
                        <input type="text" id="name"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
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
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            value="{{ now()->toDateString() }}">
                    </div>

                    <div>
                        <label for="body_temperature"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Suhu Tubuh (°C):
                        </label>
                        <input type="number" step="0.1" name="body_temperature" id="body_temperature"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            placeholder="36.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Makan pagi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Makan Pagi:</label>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input @checked(old('breakfast', 'sudah' )==='sudah' ) type="radio" name="breakfast" value="sudah"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input @checked(old('breakfast')==='belum' ) type="radio" name="breakfast" value="belum"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Belum</span>
                            </label>
                        </div>
                    </div>

                    {{-- Kesehatan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kesehatan:</label>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input @checked(old('health_status', 'sehat' )==='sehat' ) type="radio" name="health_status" value="sehat"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input @checked(old('health_status')==='sakit' ) type="radio" name="health_status" value="sakit"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Sakit</span>
                            </label>
                        </div>

                        <div id="deskripsi_kesehatan" class="mt-3 hidden">
                            <label for="sickness_description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Deskripsi Sakit:
                            </label>
                            <textarea name="sickness_description" id="sickness_description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('sickness_description') }}</textarea>

                            <div class="mt-2 space-x-6">
                                <label class="inline-flex items-center">
                                    <input @checked(old('medication_status')==='disertai obat' ) type="radio" name="medication_status"
                                        value="disertai obat" class="form-radio text-indigo-600">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Disertai Obat</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input @checked(old('medication_status', 'tanpa obat' )==='tanpa obat' ) type="radio" name="medication_status"
                                        value="tanpa obat" class="form-radio text-indigo-600">
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
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <h2
                        class="text-center text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-800 py-3">
                        Konsumsi & Perawatan Bayi
                    </h2>

                    <div class="p-4" x-data="{
                            asiList: [{ jam: '', takaran: '', asi: false }],
                            mpList: [{ jam: '', jumlah: '' }],
                            naps: [{ tidur: '', bangun: '' }],
                            diapers: [{ jam: '', bak: false, bab: false }]
                        }">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- ASI / Formula --}}
                            <div class="border rounded-lg overflow-hidden">
                                <div class="px-4 py-2 font-semibold text-blue-600 dark:text-blue-300 border-b">Susu
                                    ASI / Susu Formula</div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-auto">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Jam</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Takaran (ml)
                                                </th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">ASI</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(asi, i) in asiList" :key="i">
                                                <tr class="border-t">
                                                    <td class="px-3 py-2">
                                                        <input type="time" x-model="asi.jam"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="number" min="0"
                                                            x-model="asi.takaran" placeholder="ml"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="checkbox" x-model="asi.asi">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button"
                                                            @click.prevent="asiList.splice(i,1)"
                                                            class="text-xs text-red-600 hover:underline">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3">
                                    <button type="button"
                                        @click.prevent="asiList.push({ jam:'', takaran:'', asi:false })"
                                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                        + Tambah Baris
                                    </button>
                                </div>
                            </div>

                            {{-- MP-ASI --}}
                            <div class="border rounded-lg overflow-hidden">
                                <div class="px-4 py-2 font-semibold text-green-600 dark:text-green-300 border-b">
                                    Makanan Pendamping (MP) ASI
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
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <div class="space-x-3">
                                                            <label class="text-sm">
                                                                <input type="radio" :name="'mp_jumlah_' + i"
                                                                    value="banyak" x-model="row.jumlah">
                                                                <span class="ml-1">Banyak</span>
                                                            </label>
                                                            <label class="text-sm">
                                                                <input type="radio" :name="'mp_jumlah_' + i"
                                                                    value="sedikit" x-model="row.jumlah">
                                                                <span class="ml-1">Sedikit</span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button" @click.prevent="mpList.splice(i,1)"
                                                            class="text-xs text-red-600 hover:underline">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3">
                                    <button type="button" @click.prevent="mpList.push({ jam:'', jumlah:'' })"
                                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                        + Tambah Baris
                                    </button>
                                </div>

                                <div class="p-3 grid grid-cols-1 gap-2">
                                    <input type="text" name="infant_breakfast_text"
                                        placeholder="Makan Pagi (opsional)"
                                        class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                    <input type="text" name="infant_lunch_text"
                                        placeholder="Makan Siang (opsional)"
                                        class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                    <input type="text" name="infant_dinner_text"
                                        placeholder="Makan Malam (opsional)"
                                        class="w-full border rounded p-2 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            {{-- Tidur --}}
                            <div class="border rounded-lg overflow-hidden">
                                <div class="px-4 py-2 font-semibold text-purple-600 dark:text-purple-300 border-b">
                                    Tidur</div>
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
                                            <template x-for="(row, i) in naps" :key="'nap' + i">
                                                <tr class="border-t">
                                                    <td class="px-3 py-2">
                                                        <input type="time" x-model="row.tidur"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="time" x-model="row.bangun"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button" @click.prevent="naps.splice(i,1)"
                                                            class="text-xs text-red-600 hover:underline">Hapus</button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3">
                                    <button type="button" @click.prevent="naps.push({ tidur:'', bangun:'' })"
                                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                        + Tambah Baris
                                    </button>
                                </div>
                            </div>

                            {{-- Popok --}}
                            <div class="border rounded-lg overflow-hidden">
                                <div class="px-4 py-2 font-semibold text-yellow-600 dark:text-yellow-300 border-b">
                                    Popok</div>
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
                                            <template x-for="(row, i) in diapers" :key="'dip' + i">
                                                <tr class="border-t">
                                                    <td class="px-3 py-2">
                                                        <input type="time" x-model="row.jam"
                                                            class="w-full border rounded p-1 dark:bg-gray-700 dark:text-white">
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="checkbox" x-model="row.bak"> <span
                                                            class="text-xs ml-1">Ya</span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <input type="checkbox" x-model="row.bab"> <span
                                                            class="text-xs ml-1">Ya</span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <button type="button"
                                                            @click.prevent="diapers.splice(i,1)"
                                                            class="text-xs text-red-600 hover:underline">Hapus</button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3">
                                    <button type="button"
                                        @click.prevent="diapers.push({ jam:'', bak:false, bab:false })"
                                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                        + Tambah Baris
                                    </button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="asi_formula_items" x-bind:value="JSON.stringify(asiList)">
                        <input type="hidden" name="mpasi_items" x-bind:value="JSON.stringify(mpList)">
                        <input type="hidden" name="naps" x-bind:value="JSON.stringify(naps)">
                        <input type="hidden" name="diapers" x-bind:value="JSON.stringify(diapers)">
                    </div>
                </div>
                @endif

                {{-- =========================
                        SERVICE 2: CHILDREN
                ========================== --}}
                @if ($activityTransaction->service_id == 2)
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
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">06:30 - 07:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Salam Penyambutan
                                    dan Do'a Pagi</td>
                                <td class="px-6 py-4">
                                    <div class="space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" @checked(old('greeting_and_morning_prayer', 'mengikuti' )==='mengikuti' )
                                                name="greeting_and_morning_prayer" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" @checked(old('greeting_and_morning_prayer')==='tidak mengikuti' )
                                                name="greeting_and_morning_prayer" value="tidak mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2">Tidak Mengikuti</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">07:30 - 09:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    Bermain & Belajar <span class="font-semibold">Session 1</span>
                                </td>
                                <td class="px-6 py-4">
                                    <select id="session1_material_id" name="session1_material_id"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                                        <option value="">-- Pilih Materi --</option>
                                        @forelse ($materials as $material)
                                        <option value="{{ $material['id'] }}"
                                            data-theme-name="{{ $material['theme_name'] }}"
                                            data-sub-theme-name="{{ $material['sub_theme_name'] }}">
                                            {{ $material['material_name'] }}
                                        </option>
                                        @empty
                                        <option value="" disabled>Tidak ada materi pada periode ini
                                        </option>
                                        @endforelse
                                    </select>
                                    <div id="themeName1" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    </div>
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="BB"
                                                @checked(old('toilet_training_and_duha_prayer')==='BB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="MB"
                                                @checked(old('toilet_training_and_duha_prayer')==='MB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="BSH"
                                                @checked(old('toilet_training_and_duha_prayer')==='BSH' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session1_activity" value="BSB"
                                                @checked(old('toilet_training_and_duha_prayer', 'BSB' )==='BSB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSB</span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">09:00 - 09:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Toilet Training &
                                    Sholat Dhuha</td>
                                <td class="px-6 py-4">
                                    <div class="space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="toilet_training_and_duha_prayer" value="mengikuti"
                                                @checked(old('toilet_training_and_duha_prayer', 'mengikuti' )==='mengikuti' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">Mengikuti</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="toilet_training_and_duha_prayer" value="tidak mengikuti"
                                                @checked(old('toilet_training_and_duha_prayer')==='tidak mengikuti' )
                                                class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                                Mengikuti</span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">09:30 - 10:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    Bermain & Belajar <span class="font-semibold">Session 2</span>
                                </td>
                                <td class="px-6 py-4">
                                    <select id="session2_material_id" name="session2_material_id"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                                        <option value="">-- Pilih Materi --</option>
                                        @forelse ($materials as $material)
                                        <option value="{{ $material['id'] }}"
                                            data-theme-name="{{ $material['theme_name'] }}"
                                            data-sub-theme-name="{{ $material['sub_theme_name'] }}">
                                            {{ $material['material_name'] }}
                                        </option>
                                        @empty
                                        <option value="" disabled>Tidak ada materi pada periode ini
                                        </option>
                                        @endforelse
                                    </select>
                                    <div id="themeName2" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    </div>
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="BB"
                                                @checked(old('session2_activity')==='BB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="MB"
                                                @checked(old('session2_activity')==='MB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="BSH"
                                                @checked(old('session2_activity')==='BSH' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="session2_activity" value="BSB"
                                                @checked(old('session2_activity', 'BSB' )==='BSB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSB</span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">10:00 - 10:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Snack Pagi</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="morning_snack" value="habis" @checked(old('morning_snack', 'habis' )==='habis' )
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Habis</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="morning_snack" value="tidak habis" @checked(old('morning_snack')==='tidak habis' )
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Habis</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">10:30 - 11:15</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Kerapian &
                                    Kemandirian</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="neatness_and_independence" value="mandiri"
                                            @checked(old('neatness_and_independence', 'mandiri' )==='mandiri' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Mandiri</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="neatness_and_independence" value="kurang mandiri"
                                            @checked(old('neatness_and_independence')==='kurang mandiri' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Kurang
                                            Mandiri</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="neatness_and_independence" value="tidak mandiri"
                                            @checked(old('neatness_and_independence')==='tidak mandiri' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidak
                                            Mandiri</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">11:15 - 11:45</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Makan Siang Ceria
                                </td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cheerful_lunch" value="habis" @checked(old('cheerful_lunch', 'habis' )==='habis' )
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Habis</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cheerful_lunch" value="sisa sedikit"
                                            @checked(old('cheerful_lunch')==='sisa sedikit' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Sisa
                                            Sedikit</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="cheerful_lunch" value="sisa banyak"
                                            @checked(old('cheerful_lunch')==='sisa banyak' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Sisa
                                            Banyak</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">11:45 - 12:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Kebersihan &
                                    Training Gosok Gigi</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cleanliness_and_brushing_training" value="kurang"
                                            @checked(old('cleanliness_and_brushing_training')==='kurang' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Kurang</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="cleanliness_and_brushing_training" value="cukup"
                                            @checked(old('cleanliness_and_brushing_training')==='cukup' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Cukup</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="cleanliness_and_brushing_training" value="baik"
                                            @checked(old('cleanliness_and_brushing_training', 'baik' )==='baik' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Baik</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">12:00 - 12:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Sholat Dzuhur</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="dhuhr_prayer" value="mengikuti" @checked(old('dhuhr_prayer', 'mengikuti' )==='mengikuti' )
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="dhuhr_prayer" value="tidak mengikuti"
                                            @checked(old('dhuhr_prayer')==='tidak mengikuti' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">12:30 - 14:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Tidur Sehat
                                    (Penjemputan 1)</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="healthy_sleep" value="tidur" @checked(old('healthy_sleep', 'tidur' , 'tidur' )==='tidur' )
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidur</span></label>
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="healthy_sleep" value="tidur sebentar"
                                            @checked(old('healthy_sleep')==='tidur sebentar' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidur
                                            Sebentar</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="healthy_sleep" value="tidak tidur" @checked(old('healthy_sleep')==='tidak tidur' )
                                            class="form-radio text-indigo-600"><span class="ml-2">Tidak
                                            Tidur</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">14:00 - 14:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Mandi Sore</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="afternoon_bath" value="mengikuti" @checked(old('afternoon_bath', 'mengikuti' )==='mengikuti' )
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="afternoon_bath" value="tidak mengikuti"
                                            @checked(old('afternoon_bath')==='tidak mengikuti' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">14:30 - 15:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Snack Sore</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="afternoon_snack" value="habis" @checked(old('afternoon_snack', 'habis' )==='habis' )
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Habis</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="afternoon_snack" value="tidak habis"
                                            @checked(old('afternoon_snack')==='tidak habis' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidak
                                            Habis</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">15:00 - 15:30</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Sholat Ashar</td>
                                <td class="px-6 py-4">
                                    <label class="inline-flex items-center mr-6"><input type="radio"
                                            name="asr_prayer" value="mengikuti" @checked(old('asr_prayer', 'mengikuti' )==='mengikuti' )
                                            class="form-radio text-indigo-600"><span
                                            class="ml-2">Mengikuti</span></label>
                                    <label class="inline-flex items-center"><input type="radio"
                                            name="asr_prayer" value="tidak mengikuti"
                                            @checked(old('asr_prayer')==='tidak mengikuti' ) class="form-radio text-indigo-600"><span
                                            class="ml-2">Tidak
                                            Mengikuti</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">15:30 - 16:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Ekstra Stimulasi
                                    (Penjemputan 2)</td>
                                <td class="px-6 py-4">
                                    <textarea name="extra_stimulation_description" rows="2"
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm"
                                        placeholder="Deskripsi (opsional)">{{ old('extra_stimulation_description') }}</textarea>
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="BB"
                                                @checked(old('extra_stimulation')==='BB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="MB"
                                                @checked(old('extra_stimulation')==='MB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="BSH"
                                                @checked(old('extra_stimulation')==='BSH' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="extra_stimulation" value="BSB"
                                                @checked(old('extra_stimulation', 'BSB' )==='BSB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSB</span></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">16:00 - 17:00</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Permainan Ceria
                                    (Penjemputan 3)</td>
                                <td class="px-6 py-4">
                                    <textarea name="cheerful_play_description" rows="2"
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm"
                                        placeholder="Deskripsi (opsional)">{{ old('cheerful_play_description') }}</textarea>
                                    <div class="mt-3 space-x-6">
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="BB" @checked(old('cheerful_play')==='BB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="MB" @checked(old('cheerful_play')==='MB' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">MB</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="BSH" @checked(old('cheerful_play')==='BSH' )
                                                class="form-radio text-indigo-600"><span
                                                class="ml-2">BSH</span></label>
                                        <label class="inline-flex items-center"><input type="radio"
                                                name="cheerful_play" value="BSB" @checked(old('cheerful_play', 'BSB' )==='BSB' )
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
                        <label class="inline-flex items-center">
                            <input @checked(old('condition', 'tenang' )==='tenang' ) type="radio" name="condition" value="tenang"
                                class="form-radio text-indigo-600">
                            <span class="ml-2">Tenang</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input @checked(old('condition')==='rewel' ) type="radio" name="condition" value="rewel"
                                class="form-radio text-indigo-600">
                            <span class="ml-2">Rewel</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input @checked(old('condition')==='temper tantrum' ) type="radio" name="condition"
                                value="temper tantrum" class="form-radio text-indigo-600">
                            <span class="ml-2">Temper Tantrum</span>
                        </label>
                    </div>
                </div>
                {{-- Stimulasi (Auto, Readonly) --}}
                <div>
                    <label for="stimulation_description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Stimulasi (otomatis dari MMDST):
                    </label>
                    <textarea name="stimulation_description" id="stimulation_description" rows="4" readonly
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 cursor-not-allowed"
                        placeholder="Memuat saran stimulasi otomatis..."></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        *Terisi otomatis berdasarkan rentang usia (hari) & item yang belum lulus. (Tidak dapat diedit)
                    </p>
                </div>

                {{-- Catatan --}}
                <div>
                    <label for="notes"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan:</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <x-secondary-button id="back-btn" type="button">Kembali</x-secondary-button>
                    <x-primary-button id="submitFormButton" class="ml-auto">
                        {{ __('Simpan') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle form sakit
            function toggleKesehatanUI(visible) {
                const el = document.getElementById('deskripsi_kesehatan');
                if (!el) return;
                el.classList.toggle('hidden', !visible);
            }

            // Back button
            document.getElementById('back-btn')?.addEventListener('click', () => history.back());

            // Helper: format HH:mm
            function hhmm(time) {
                if (!time) return '';
                return (time || '').slice(0, 5);
            }

            // Check attendance + autofill jam
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

            // Load materials by date (Children)
            function loadSubthemes(dateStr) {
                const s1 = document.getElementById('session1_material_id');
                const s2 = document.getElementById('session2_material_id');
                if (!s1 && !s2) return;

                // Tampilkan status loading
                const loadingOpt = '<option value="" disabled>Memuat materi...</option>';
                if (s1) s1.innerHTML = loadingOpt;
                if (s2) s2.innerHTML = loadingOpt;

                fetch(`{{ route('daily-report.get-subthemes', ['date' => '_DATE_']) }}`.replace('_DATE_', dateStr))
                    .then(r => r.json())
                    .then(({
                        materials
                    }) => {
                        const options = ['<option value="">-- Pilih Materi --</option>'];
                        if (!materials || materials.length === 0) {
                            options.push(
                                '<option value="" disabled>Tidak ada materi pada periode ini</option>');
                        } else {
                            materials.forEach(m => {
                                options.push(
                                    `<option value="${m.id}" data-theme-name="${m.theme_name}" data-sub-theme-name="${m.sub_theme_name}">
                                        ${m.material_name}
                                     </option>`
                                );
                            });
                        }
                        if (s1) s1.innerHTML = options.join('');
                        if (s2) s2.innerHTML = options.join('');

                        const tn1 = document.getElementById('themeName1');
                        const tn2 = document.getElementById('themeName2');
                        if (tn1) tn1.textContent = '';
                        if (tn2) tn2.textContent = '';
                    })
                    .catch(() => {
                        const errorOpt = '<option value="" disabled>Gagal memuat materi</option>';
                        if (s1) s1.innerHTML = errorOpt;
                        if (s2) s2.innerHTML = errorOpt;
                    });
            }

            // Show selected theme/subtheme (Children)
            function bindMaterialChange(selectId, targetId) {
                const sel = document.getElementById(selectId);
                const tgt = document.getElementById(targetId);
                if (!sel || !tgt) return;
                sel.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    const theme = opt?.getAttribute('data-theme-name') || '';
                    const sub = opt?.getAttribute('data-sub-theme-name') || '';
                    tgt.innerHTML = theme || sub ?
                        `<p class="text-xs"><strong>Tema:</strong> ${theme}</p><p class="text-xs"><strong>Sub Tema:</strong> ${sub}</p>` :
                        '';
                });
            }

            // Auto stimulasi (readonly)
            function loadStimulation(activityTransactionId, dateStr) {
                const textarea = document.getElementById('stimulation_description');
                if (!textarea) return;
                textarea.value = 'Memuat saran stimulasi...';

                fetch(`{{ route('daily-report.stimulation.suggest', ['activityTransaction' => '_AT_', 'date' => '_DATE_']) }}`
                        .replace('_AT_', activityTransactionId).replace('_DATE_', dateStr))
                    .then(r => r.json())
                    .then(data => {
                        textarea.value = (data && data.text) ? data.text : (data?.message ||
                            'Tidak ada saran stimulasi tersedia.');
                    })
                    .catch(() => {
                        textarea.value = 'Gagal memuat saran stimulasi.';
                    });
            }

            // ============
            // INIT
            // ============
            const studentId = {
                {
                    $activityTransaction - > student - > id
                }
            };
            const atId = {
                {
                    $activityTransaction - > id
                }
            };
            const serviceId = {
                {
                    $activityTransaction - > service_id
                }
            };
            const periodEl = document.getElementById('period');

            // Jalankan fungsi awal saat halaman dimuat
            checkAttendance(studentId, periodEl.value);
            loadStimulation(atId, periodEl.value);
            if (serviceId === 2) {
                loadSubthemes(periodEl.value);
                bindMaterialChange('session1_material_id', 'themeName1');
                bindMaterialChange('session2_material_id', 'themeName2');
            }

            // Tambahkan event listener untuk perubahan tanggal
            periodEl.addEventListener('change', function() {
                const d = this.value;
                checkAttendance(studentId, d);
                loadStimulation(atId, d);
                if (serviceId === 2) loadSubthemes(d);
            });

            // Inisialisasi tampilan form "sakit"
            document.querySelectorAll('input[name="health_status"]').forEach(el => {
                el.addEventListener('change', (e) => toggleKesehatanUI(e.target.value === 'sakit'));
            });
            const checkedHealth = document.querySelector('input[name="health_status"]:checked');
            toggleKesehatanUI(checkedHealth && checkedHealth.value === 'sakit');
        });

        // Handle form submission confirmation
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah data yang Anda input sudah benar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form after the user confirms
                    document.getElementById('dailyReportForm').submit();
                }
            });
        });
    </script>
</x-app-layout>