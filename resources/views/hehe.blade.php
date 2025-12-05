<x-app-layout>
    <div
        class="max-w-5xl mx-auto px-6 py-8 font-sans bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg shadow-xl text-white">
        <div class="border border-gray-700 rounded-lg overflow-hidden divide-y divide-gray-600">
            <!-- Header -->
            <div class="bg-gray-900 p-6 text-center">
                <h1 class="text-2xl font-bold uppercase tracking-wide text-white">Laporan Harian Usia 01 Bulan - 24 Bulan
                </h1>
                <p class="text-sm text-gray-400">Al Jannah Preschool and Day Care</p>
            </div>

            <!-- Info Umum -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 p-6 bg-gray-800">
                <div>
                    <label class="block text-sm font-semibold text-gray-300">Periode</label>
                    <input type="date"
                        class="w-full border border-gray-600 p-2 rounded-md shadow-sm bg-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300">Jam Datang</label>
                    <input type="time"
                        class="w-full border border-gray-600 p-2 rounded-md shadow-sm bg-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300">Jam Pulang</label>
                    <input type="time"
                        class="w-full border border-gray-600 p-2 rounded-md shadow-sm bg-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300">Suhu Tubuh (°C)</label>
                    <input type="number" step="0.1"
                        class="w-full border border-gray-600 p-2 rounded-md shadow-sm bg-gray-700 text-white" />
                </div>
            </div>

            <!-- Kesehatan & Makan Pagi -->
            <div class="p-6 bg-gray-700">
                <div class="flex flex-wrap gap-6 mb-4">
                    <label class="text-sm font-medium text-gray-300"><input type="checkbox" class="mr-2">Sudah</label>
                    <label class="text-sm font-medium text-gray-300"><input type="checkbox" class="mr-2">Belum</label>
                </div>
                <div class="flex flex-wrap gap-6 items-center">
                    <label class="text-sm font-medium text-gray-300"><input type="checkbox" class="mr-2">Sehat</label>
                    <label class="text-sm font-medium text-gray-300"><input type="checkbox" class="mr-2">Sakit</label>
                    <input type="text" placeholder="Keterangan (misal: pilek)"
                        class="flex-1 border border-gray-600 p-2 rounded-md shadow-sm bg-gray-700 text-white" />
                </div>
            </div>

            <!-- Infant's Meals Section -->
            <div class="bg-gray-800 border-t border-gray-600">
                <div class="p-6">
                    <h2 class="text-center text-lg font-bold text-gray-300 border-b pb-2 mb-4">Infant's Meals</h2>
                    <div class="grid md:grid-cols-2 gap-4">

                        <!-- Susu ASI / Formula -->
                        <div x-data="{ asiList: [{ jam: '', takaran: '', asi: false }] }" class="mb-4">
                            <h3 class="font-semibold text-blue-500 mb-2">Susu ASI / Susu Formula</h3>
                            <div class="space-y-2">
                                <template x-for="(asi, index) in asiList" :key="index">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-300">Jam:</label>
                                        <input type="time" x-model="asi.jam"
                                            class="border p-1 rounded w-28 bg-gray-700 text-white" />
                                        <label class="text-sm text-gray-300">Takaran:</label>
                                        <input type="number" x-model="asi.takaran" placeholder="ml"
                                            class="border p-1 rounded w-24 bg-gray-700 text-white" />
                                        <label class="text-sm text-gray-300"><input type="checkbox" x-model="asi.asi"
                                                class="mr-1" />ASI</label>
                                        <button type="button" @click="asiList.splice(index, 1)"
                                            class="text-red-600 text-xs ml-2 hover:underline">Hapus</button>
                                    </div>
                                </template>
                            </div>
                            <button @click="asiList.push({ jam: '', takaran: '', asi: false })"
                                class="mt-2 text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                + Tambah Baris
                            </button>
                        </div>

                        <!-- MP ASI -->
                        <div x-data="{ mpList: [{ jam: '', banyak: false, sedikit: false }] }" class="mb-4">
                            <h3 class="font-semibold text-green-500 mb-2">Makanan Pendamping (MP) ASI</h3>
                            <div class="space-y-2">
                                <template x-for="(mp, index) in mpList" :key="index">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm text-gray-300">Jam:</label>
                                        <input type="time" x-model="mp.jam"
                                            class="border p-1 rounded w-28 bg-gray-700 text-white" />
                                        <label class="text-sm text-gray-300">
                                            <input type="radio" x-model="mp.banyak" :name="'mp-asi-amount-' + index"
                                                class="mr-1" />Banyak
                                        </label>
                                        <label class="text-sm text-gray-300">
                                            <input type="radio" x-model="mp.sedikit" :name="'mp-asi-amount-' + index"
                                                class="mr-1" />Sedikit
                                        </label>
                                        <button type="button" @click="mpList.splice(index, 1)"
                                            class="text-red-600 text-xs ml-2 hover:underline">Hapus</button>
                                    </div>
                                </template>
                            </div>
                            <button @click="mpList.push({ jam: '', banyak: false, sedikit: false })"
                                class="mt-2 text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">
                                + Tambah Baris
                            </button>

                            <div class="mt-4 space-y-2">
                                <input type="text" placeholder="Menu Pagi"
                                    class="w-full border p-2 rounded bg-gray-700 text-white" />
                                <input type="text" placeholder="Menu Siang"
                                    class="w-full border p-2 rounded bg-gray-700 text-white" />
                                <input type="text" placeholder="Menu Sore"
                                    class="w-full border p-2 rounded bg-gray-700 text-white" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tidur dan Popok -->
            <div class="grid md:grid-cols-2 gap-0">

                <!-- Tidur -->
                <div x-data="{ naps: [{ tidur: '', bangun: '' }] }" class="p-6 bg-gray-800 border-t border-gray-600">
                    <h2 class="font-bold text-lg text-purple-400 mb-4">Tidur Bayi</h2>
                    <div class="space-y-2">
                        <template x-for="(nap, index) in naps" :key="index">
                            <div class="flex gap-3 items-center">
                                <input type="time" x-model="nap.tidur"
                                    class="border p-2 rounded-md bg-gray-700 text-white" />
                                <input type="time" x-model="nap.bangun"
                                    class="border p-2 rounded-md bg-gray-700 text-white" />
                                <button type="button" @click="naps.splice(index, 1)"
                                    class="text-red-600 text-xs hover:underline">Hapus</button>
                            </div>
                        </template>
                    </div>
                    <button @click="naps.push({ tidur: '', bangun: '' })"
                        class="mt-2 text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">+
                        Tambah</button>
                </div>

                <!-- Popok -->
                <div x-data="{ diapers: [{ jam: '', bak: false, bab: false }] }" class="p-6 bg-gray-800 border-t border-gray-600">
                    <h2 class="font-bold text-lg text-yellow-400 mb-4">Popok Bayi</h2>
                    <div class="space-y-2">
                        <template x-for="(diaper, index) in diapers" :key="index">
                            <div class="flex gap-3 items-center">
                                <input type="time" x-model="diaper.jam"
                                    class="border p-2 rounded-md bg-gray-700 text-white" />
                                <label class="text-sm text-gray-300">
                                    <input type="radio" x-model="diaper.bak" :name="'diaper-bak-' + index"
                                        class="mr-1" />BAK
                                </label>
                                <label class="text-sm text-gray-300">
                                    <input type="radio" x-model="diaper.bab" :name="'diaper-bab-' + index"
                                        class="mr-1" />BAB
                                </label>
                                <button type="button" @click="diapers.splice(index, 1)"
                                    class="text-red-600 text-xs hover:underline">Hapus</button>
                            </div>
                        </template>
                    </div>
                    <button @click="diapers.push({ jam: '', bak: false, bab: false })"
                        class="mt-2 text-xs bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700">+
                        Tambah</button>
                </div>
            </div>

            <!-- Kondisi dan Catatan -->
            <div class="p-6 bg-gray-700">
                <div class="flex flex-wrap gap-6 mb-4">
                    <label class="text-gray-300">
                        <input type="radio" name="kondisi" class="mr-2" /> Tenang
                    </label>
                    <label class="text-gray-300">
                        <input type="radio" name="kondisi" class="mr-2" /> Rewel
                    </label>
                    <label class="text-gray-300">
                        <input type="radio" name="kondisi" class="mr-2" /> Temper Tantrum
                    </label>
                </div>
                <label class="block text-sm font-semibold text-gray-300 mb-1">Stimulasi:</label>
                <input type="text" class="w-full border p-2 rounded-md mb-4 bg-gray-700 text-white" />

                <label class="block text-sm font-semibold text-gray-300 mb-1">Catatan:</label>
                <textarea class="w-full border p-2 rounded-md bg-gray-700 text-white" rows="4"></textarea>
            </div>
        </div>
    </div>
</x-app-layout>
