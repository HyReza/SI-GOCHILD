<x-app-layout>
    <div
        class="max-w-4xl mx-auto bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-400 p-8 rounded-lg shadow-lg space-y-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800 dark:text-gray-200">Al Jannah Preschool and Day Care
        </h2>
        <h3 class="text-lg font-semibold mb-8 text-center text-gray-800 dark:text-gray-200">Laporan Harian Usia 25 Bulan
            - 72 Bulan</h3>

        <!-- Tampilkan pesan sukses jika ada -->
        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 p-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulir dimulai -->
        <form action="{{ route('laporan-harian.store') }}" method="POST">
            @csrf

            <!-- Periode dan Suhu Tubuh -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="periode"
                        class="block text-sm font-medium text-gray-800 dark:text-gray-200">Periode:</label>
                    <input type="date" name="periode" id="periode"
                        class="mt-2 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                        value="{{ old('periode', now()->toDateString()) }}">
                    @error('periode')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="suhu" class="block text-sm font-medium text-gray-800 dark:text-gray-200">Suhu Tubuh
                        (°C):</label>
                    <input type="number" step="0.1" name="suhu" id="suhu" placeholder="Masukkan Suhu"
                        class="mt-2 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                        value="{{ old('suhu') }}">
                    @error('suhu')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Jam Datang dan Jam Pulang -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="jam_datang" class="block text-sm font-medium text-gray-800 dark:text-gray-200">Jam
                        Datang:</label>
                    <input type="time" name="jam_datang" id="jam_datang"
                        class="mt-2 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                        value="{{ old('jam_datang') }}">
                    @error('jam_datang')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="jam_pulang" class="block text-sm font-medium text-gray-800 dark:text-gray-200">Jam
                        Pulang:</label>
                    <input type="time" name="jam_pulang" id="jam_pulang"
                        class="mt-2 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                        value="{{ old('jam_pulang') }}">
                    @error('jam_pulang')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Makan Pagi dan Kesehatan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-200">Makan Pagi:</label>
                    <div class="mt-2 flex space-x-8">
                        <label class="inline-flex items-center">
                            <input type="radio" name="makan_pagi" value="sudah"
                                {{ old('makan_pagi') == 'sudah' ? 'checked' : '' }}
                                class="form-radio text-indigo-600 dark:text-indigo-400">
                            <span class="ml-2 text-gray-800 dark:text-gray-200">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="makan_pagi" value="belum"
                                {{ old('makan_pagi') == 'belum' ? 'checked' : '' }}
                                class="form-radio text-indigo-600 dark:text-indigo-400">
                            <span class="ml-2 text-gray-800 dark:text-gray-200">Belum</span>
                        </label>
                    </div>
                    @error('makan_pagi')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-200">Kesehatan:</label>
                    <div class="mt-2 flex space-x-8">
                        <label class="inline-flex items-center">
                            <input type="radio" name="kesehatan" value="sehat"
                                {{ old('kesehatan') == 'sehat' ? 'checked' : '' }}
                                class="form-radio text-indigo-600 dark:text-indigo-400">
                            <span class="ml-2 text-gray-800 dark:text-gray-200">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="kesehatan" value="sakit"
                                {{ old('kesehatan') == 'sakit' ? 'checked' : '' }}
                                class="form-radio text-indigo-600 dark:text-indigo-400"
                                onclick="document.getElementById('deskripsi_sakit').style.display='block'">
                            <span class="ml-2 text-gray-800 dark:text-gray-200">Sakit</span>
                        </label>
                    </div>
                    <div id="deskripsi_sakit"
                        style="{{ old('kesehatan') == 'sakit' ? 'display:block;' : 'display:none;' }}" class="mt-4">
                        <label for="deskripsi_sakit_input"
                            class="block text-sm font-medium text-gray-800 dark:text-gray-200">Deskripsi Sakit:</label>
                        <input type="text" name="deskripsi_sakit" id="deskripsi_sakit_input"
                            class="mt-2 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                            value="{{ old('deskripsi_sakit') }}">
                        <div class="mt-2 flex space-x-8">
                            <label class="inline-flex items-center">
                                <input type="radio" name="obat" value="dengan_obat"
                                    {{ old('obat') == 'dengan_obat' ? 'checked' : '' }}
                                    class="form-radio text-indigo-600 dark:text-indigo-400">
                                <span class="ml-2 text-gray-800 dark:text-gray-200">Dengan Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="obat" value="tanpa_obat"
                                    {{ old('obat') == 'tanpa_obat' ? 'checked' : '' }}
                                    class="form-radio text-indigo-600 dark:text-indigo-400">
                                <span class="ml-2 text-gray-800 dark:text-gray-200">Tanpa Obat</span>
                            </label>
                        </div>
                    </div>
                    @error('kesehatan')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tabel Kegiatan -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Waktu</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Kegiatan</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">06:00 -
                                07:00</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">Kerapian
                                dan Kemandirian</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                <textarea
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                                    name="keterangan_kerapian">{{ old('keterangan_kerapian') }}</textarea>
                            </td>
                        </tr>
                        <!-- Tambahkan entri tambahan sesuai kebutuhan -->
                    </tbody>
                </table>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end mt-6 space-x-4">
                <x-secondary-button id="back-btn">Back</x-secondary-button>
                <x-primary-button id="submitFormButton" class="ml-auto">
                    {{ __('Simpan') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        // Script untuk mengontrol tampilan bagian deskripsi sakit
        document.querySelectorAll('input[name="kesehatan"]').forEach((radio) => {
            radio.addEventListener('change', function() {
                if (this.value === 'sakit') {
                    document.getElementById('deskripsi_sakit').style.display = 'block';
                } else {
                    document.getElementById('deskripsi_sakit').style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>
