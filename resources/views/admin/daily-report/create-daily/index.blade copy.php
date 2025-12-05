<x-app-layout>
    <div class=" ">
        <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Al Jannah Preschool and Day
                Care
            </h2>
            <h3 class="text-lg font-semibold mb-8 text-center text-gray-700 dark:text-gray-300">Laporan Harian Usia 25
                Bulan
                - 72 Bulan</h3>
            {{-- <h4 class="text-base font-semibold mb-8 text-center text-gray-700 dark:text-gray-300">Nama :
                {{ $activityTransaction->student->student_name }}
            </h4> --}}

            @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 p-4 rounded mb-6">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('daily-report.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            :</label>
                        <input type="text" name="name" id="name"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            value="{{ $activityTransaction->student->student_name }}" disabled>
                        <p id="attendance-status" class="text-red-500 text-sm mt-2"></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="periode"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode:</label>
                        <input type="date" name="periode" id="periode"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            value="{{ now()->toDateString() }}">
                    </div>
                    <div>
                        <label for="suhu" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Suhu
                            Tubuh
                            (°C):</label>
                        <input type="number" step="0.1" name="suhu" id="suhu"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            placeholder="36.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="jam_datang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jam
                            Datang:</label>
                        <input type="time" name="jam_datang" id="jam_datang"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="jam_pulang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jam
                            Pulang:</label>
                        <input type="time" name="jam_pulang" id="jam_pulang"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Makan Pagi:</label>
                    <div class="mt-2 space-x-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="makan_pagi" value="sudah" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="makan_pagi" value="belum" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Belum</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kesehatan:</label>
                    <div class="mt-2 space-x-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="kesehatan" value="sehat" class="form-radio text-indigo-600"
                                onchange="toggleKesehatan(false)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="kesehatan" value="sakit" class="form-radio text-indigo-600"
                                onchange="toggleKesehatan(true)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sakit</span>
                        </label>
                    </div>
                    <div id="deskripsi_kesehatan" class="mt-4 hidden">
                        <label for="deskripsi_sakit"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Sakit:</label>
                        <textarea name="deskripsi_sakit" id="deskripsi_sakit" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"></textarea>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="obat" value="dengan_obat"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Disertai Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="obat" value="tanpa_obat"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Tanpa Obat</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- TABLE KEGIATAN --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700 border dark:border-gray-800">
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

                            {{-- SALAM PENYAMBUTAN DAN DOA PAGI --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">06:30
                                    -
                                    07:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Salam Penyambutan dan Do'a Pagi</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="tidak_mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Mengikuti</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- BERMAIN DAN BELAJAR SESSION 1 --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">07:30
                                    -
                                    09:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Bermain dan Belajar <span class="font-semibold">Session 1</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-4">
                                        <select id="service_id" name="service_id"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                                            <option value="">-- Pilih Sub Theme --</option>
                                            @foreach ($subthemes as $subtheme)
                                            <option value="{{ $subtheme->id }}"
                                                data-theme-name="{{ $subtheme->theme->theme_name }}"
                                                {{ old('service_id') == $subtheme->id ? 'selected' : '' }}>
                                                {{ $subtheme->code_theme }} - {{ $subtheme->sub_theme_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                                    </div>

                                    <div id="themeName" class="mt-2 text-gray-700 dark:text-gray-300"></div>


                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="MB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSH"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSB</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- TOILET TRAINING DAN SHOLAT DHUHA --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">09:00
                                    -
                                    09:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Toilet training dan sholat Dhuha</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="tidak_mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Mengikuti</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- BERMAIN DAN BELAJAR SESSION 2 --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">09:30
                                    -
                                    10:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Bermain dan Belajar <span class="font-semibold">Session 2</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-4">
                                        <select id="service_id2" name="service_id"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                                            <option value="">-- Pilih Sub Theme --</option>
                                            @foreach ($subthemes as $subtheme)
                                            <option value="{{ $subtheme->id }}"
                                                data-theme-name="{{ $subtheme->theme->theme_name }}"
                                                {{ old('service_id') == $subtheme->id ? 'selected' : '' }}>
                                                {{ $subtheme->code_theme }} - {{ $subtheme->sub_theme_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                                    </div>

                                    <div id="themeName2" class="mt-2 text-gray-700 dark:text-gray-300"></div>

                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="MB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSH"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSB</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- SNACK PAGI --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">10:00
                                    -
                                    10:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Snack Pagi</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="snack_pagi" value="habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Habis</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="snack_pagi" value="tidak_habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Habis</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- KERAPIAN DAN KEMANDIRIAN --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">10:30
                                    -
                                    11:15</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Kerapian
                                    dan Kemandirian</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="kerapian_kemandirian" value="mandiri"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mandiri</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="kerapian_kemandirian" value="kurang_mandiri"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Kurang Mandiri</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="kerapian_kemandirian" value="tidak_mandiri"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Mandiri</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- MAKAN SIANG CERIA --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">11:15
                                    -
                                    11:45</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Makan Siang Ceria</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="makan_siang" value="habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Habis</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="makan_siang" value="sisa_sedikit"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sisa Sedikit</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="makan_siang" value="sisa_banyak"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sisa Banyak</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- KEBERSIHAN  DAN TRAINING GOSOK GIGI --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">11:45
                                    -
                                    12:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    kebersihan dan Training Gosok Gigi</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="kebersihan_gigi" value="kurang"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Kurang</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="kebersihan_gigi" value="cukup"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Cukup</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="kebersihan_gigi" value="baik"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Baik</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- SHOLAT DZUHUR --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">12:00
                                    -
                                    12:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Sholat Dzuhur</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="tidak_mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Mengikuti</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- TIDUR SEHAT --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">12:30
                                    -
                                    14:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Tidur Sehat <span class="font-semibold">( Penjemputan 1 )</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="tidur_sehat" value="tidur"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidur</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="tidur_sehat" value="tidur_sebentar"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidur Sebentar</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="tidur_sehat" value="tidak_tidur"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Tidur</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- MANDI SORE --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">14:00
                                    -
                                    14:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Mandi Sore</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="mandi_sore" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="mandi_sore" value="tidak_mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Mengikuti</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            {{-- SNACK SORE --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">14:30
                                    -
                                    15:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Snack Sore</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="snack_pagi" value="habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Habis</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="snack_pagi" value="tidak_habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Habis</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- SHOLAT ASHAR --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">15:00
                                    -
                                    15:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Sholat Ashar</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="salam_penyambutan" value="tidak_mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidak Mengikuti</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- EKSTRA STIMULASI --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">15:30
                                    -
                                    16:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Ekstra Stimulasi <span class="font-semibold">( Penjemputan 2 )</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <textarea
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                                        name="keterangan_kerapian">{{ old('keterangan_kerapian') }}</textarea>
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="MB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSH"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSB</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>

                            {{-- PERMAINAN CERIA --}}
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">16:00
                                    -
                                    17:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Permainan Ceria <span class="font-semibold">( Penjemputan 3 )</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <textarea
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                                        name="keterangan_kerapian">{{ old('keterangan_kerapian') }}</textarea>
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="MB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSH"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="ekstra_stimulasi_rating" value="BSB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSB</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>


                            {{-- <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:bg-opacity-50 duration-200 ease-in">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">06:00
                                    -
                                    07:00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    Kerapian
                                    dan Kemandirian</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    <textarea
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm"
                                        name="keterangan_kerapian">{{ old('keterangan_kerapian') }}</textarea>
                            </td>
                            </tr> --}}
                            <!-- Tambahkan entri tambahan sesuai kebutuhan -->

                        </tbody>
                    </table>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi:</label>
                    <div class="mt-2 space-x-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="kondisi" value="tenang" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tenang</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="kondisi" value="rewel" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Rewel</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="kondisi" value="temper_tantrum"
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Temper Tantrum</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="stimulasi"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stimulasi:</label>
                    <textarea name="stimulasi" id="stimulasi" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div>
                    <label for="catatan"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan:</label>
                    <textarea name="catatan" id="catatan" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
                <div class="flex justify-end mt-6 space-x-4">
                    <x-secondary-button id="back-btn">Back</x-secondary-button>
                    <x-primary-button id="submitFormButton" class="ml-auto">
                        {{ __('Simpan') }}
                    </x-primary-button>
                </div>
        </div>
        </form>
    </div>

    </div>
    <script>
        function toggleKesehatan(isSick) {
            const deskripsiKesehatan = document.getElementById('deskripsi_kesehatan');
            deskripsiKesehatan.style.display = isSick ? 'block' : 'none';
        }

        // Initialize the form based on the initial state
        document.addEventListener('DOMContentLoaded', function() {
            const kesehatanRadios = document.querySelectorAll('input[name="kesehatan"]');
            kesehatanRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleKesehatan(this.value === 'sakit');
                });
            });

            // Check initial state
            const initialState = document.querySelector('input[name="kesehatan"]:checked');
            if (initialState) {
                toggleKesehatan(initialState.value === 'sakit');
            }
        });



        $(document).ready(function() {
            // Fungsi untuk dropdown pertama
            $('#service_id').on('change', function() {
                var themeName = $(this).find('option:selected').data('theme-name');
                if (themeName) {
                    $('#themeName').text('Tema : ' + themeName);
                } else {
                    $('#themeName').text('');
                }
            });

            // Fungsi untuk dropdown kedua
            $('#service_id2').on('change', function() {
                var themeName2 = $(this).find('option:selected').data('theme-name');
                if (themeName2) {
                    $('#themeName2').text('Tema : ' + themeName2);
                } else {
                    $('#themeName2').text('');
                }
            });
        });


        // Function to check attendance using AJAX
        function checkAttendance(studentId, date) {
            $.ajax({
                url: `/check-attendance/${studentId}/${date}`,
                type: 'GET',
                success: function(data) {
                    // Display the attendance status
                    $('#attendance-status').text(data.status);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        // Function to check attendance using AJAX
        function checkAttendance(studentId, date) {
            $.ajax({
                url: `/check-attendance/${studentId}/${date}`,
                type: 'GET',
                success: function(data) {
                    // Display the attendance status
                    $('#attendance-status').text(data.status);
                },
                error: function(xhr, status, error) {
                    // Handle error if needed
                    console.error('Error:', error);
                    $('#attendance-status').text('Terjadi kesalahan saat memeriksa absensi.');
                }
            });
        }

        $(document).ready(function() {
            const studentId = {
                {
                    $activityTransaction - > student - > id
                }
            }; // Get student ID from the backend
            let periode = $('#periode').val(); // Get the initial date from the input field

            // Check attendance on page load (for today's date)
            checkAttendance(studentId, periode);

            // Re-check attendance when the 'periode' date is changed
            $('#periode').on('change', function() {
                const newDate = $(this).val(); // Get the new date when 'periode' changes
                checkAttendance(studentId,
                    newDate); // Call the function to check attendance with the new date
            });
        });
    </script>
</x-app-layout>