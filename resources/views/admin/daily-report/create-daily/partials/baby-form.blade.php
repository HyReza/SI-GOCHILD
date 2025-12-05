<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg" x-data="{
    asiList: [{ jam: '', takaran: '', asi: false }],
    mpList: [{ jam: '', jumlah: '' }],
    naps: [{ tidur: '', bangun: '' }],
    diapers: [{ jam: '', bak: false, bab: false }]
}">

    <h2
        class="text-center text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-800 py-3">
        Konsumsi & Perawatan Bayi
    </h2>

    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- 1. ASI / Formula --}}
            <div class="border rounded-lg overflow-hidden border-gray-200 dark:border-gray-700">
                <div
                    class="px-4 py-2 font-semibold text-blue-600 dark:text-blue-300 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    Susu ASI / Susu Formula
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                    Jam</th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Takaran (ml)</th>
                                <th
                                    class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                    ASI</th>
                                <th
                                    class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(asi, i) in asiList" :key="i">
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="time" x-model="asi.jam"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" min="0" x-model="asi.takaran" placeholder="ml"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox" x-model="asi.asi"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click.prevent="asiList.splice(i,1)"
                                            class="text-red-600 hover:text-red-900">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click.prevent="asiList.push({ jam:'', takaran:'', asi:false })"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        + Tambah Baris
                    </button>
                </div>
            </div>

            {{-- 2. MP-ASI --}}
            <div class="border rounded-lg overflow-hidden border-gray-200 dark:border-gray-700">
                <div
                    class="px-4 py-2 font-semibold text-green-600 dark:text-green-300 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    Makanan Pendamping (MP) ASI
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                    Jam</th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Jumlah</th>
                                <th
                                    class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, i) in mpList" :key="'mp' + i">
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="time" x-model="row.jam"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex gap-2 items-center text-xs">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" :name="'mp_jumlah_' + i" value="banyak"
                                                    x-model="row.jumlah" class="form-radio text-green-600 w-3 h-3">
                                                <span class="ml-1 text-gray-700 dark:text-gray-300">Banyak</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" :name="'mp_jumlah_' + i" value="sedikit"
                                                    x-model="row.jumlah" class="form-radio text-green-600 w-3 h-3">
                                                <span class="ml-1 text-gray-700 dark:text-gray-300">Sedikit</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click.prevent="mpList.splice(i,1)"
                                            class="text-red-600 hover:text-red-900">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click.prevent="mpList.push({ jam:'', jumlah:'' })"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        + Tambah Baris
                    </button>
                </div>

                <div
                    class="p-3 grid grid-cols-1 gap-2 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                    <input type="text" name="infant_breakfast_text" placeholder="Makan Pagi (opsional)"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <input type="text" name="infant_lunch_text" placeholder="Makan Siang (opsional)"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <input type="text" name="infant_dinner_text" placeholder="Makan Malam (opsional)"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>
        </div>

        {{-- Baris Kedua: Tidur & Popok --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

            {{-- 3. Tidur --}}
            <div class="border rounded-lg overflow-hidden border-gray-200 dark:border-gray-700">
                <div
                    class="px-4 py-2 font-semibold text-purple-600 dark:text-purple-300 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    Tidur
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Mulai</th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Bangun</th>
                                <th
                                    class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, i) in naps" :key="'nap' + i">
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="time" x-model="row.tidur"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="time" x-model="row.bangun"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click.prevent="naps.splice(i,1)"
                                            class="text-red-600 hover:text-red-900">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click.prevent="naps.push({ tidur:'', bangun:'' })"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        + Tambah Baris
                    </button>
                </div>
            </div>

            {{-- 4. Popok --}}
            <div class="border rounded-lg overflow-hidden border-gray-200 dark:border-gray-700">
                <div
                    class="px-4 py-2 font-semibold text-yellow-600 dark:text-yellow-300 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    Popok
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Jam</th>
                                <th
                                    class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                    BAK</th>
                                <th
                                    class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                    BAB</th>
                                <th
                                    class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, i) in diapers" :key="'dip' + i">
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="time" x-model="row.jam"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox" x-model="row.bak"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4">
                                        <span class="text-[10px] block text-gray-500">Ya</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox" x-model="row.bab"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4">
                                        <span class="text-[10px] block text-gray-500">Ya</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click.prevent="diapers.splice(i,1)"
                                            class="text-red-600 hover:text-red-900">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click.prevent="diapers.push({ jam:'', bak:false, bab:false })"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        + Tambah Baris
                    </button>
                </div>
            </div>
        </div>

        {{-- Hidden Inputs untuk kirim ke Controller --}}
        <input type="hidden" name="asi_formula_items" x-bind:value="JSON.stringify(asiList)">
        <input type="hidden" name="mpasi_items" x-bind:value="JSON.stringify(mpList)">
        <input type="hidden" name="naps" x-bind:value="JSON.stringify(naps)">
        <input type="hidden" name="diapers" x-bind:value="JSON.stringify(diapers)">
    </div>
</div>
