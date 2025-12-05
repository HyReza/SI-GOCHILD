<x-app-layout>
    <div class=" ">
        <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-white">Al Jannah Preschool and Day
                Care
            </h2>
            <h3 class="text-lg font-semibold mb-8 text-center text-gray-700 dark:text-gray-300">Laporan Harian Usia 25
                Bulan
                - 72 Bulan</h3>

            @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 p-4 rounded mb-6">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('daily-report.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- FORM NAMA --}}
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                            :</label>
                        <input type="text" name="name" id="name"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            value="{{ $activityTransaction->student->student_name }}" disabled>
                        <p id="attendance-status" class="text-red-500 text-sm mt-2"></p>
                    </div>
                    <input type="hidden" value="{{ $activityTransaction->id }}" name="activity_transaction_id">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- FORM PERIODE --}}
                    <div>
                        <label for="period"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode:</label>
                        <input type="date" name="period" id="period"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            value="{{ now()->toDateString() }}">
                    </div>

                    {{-- FORM SUHU TUBUH --}}
                    <div>
                        <label for="body_temperature"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Suhu
                            Tubuh
                            (째C):</label>
                        <input type="number" step="0.1" name="body_temperature" id="body_temperature"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"
                            placeholder="36.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- FORM JAM DATANG --}}
                    <div>
                        <label for="arrival_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jam
                            Datang:</label>
                        <input type="time" name="arrival_time" id="arrival_time" value="{{ old('arrival_time') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                    </div>
                    {{-- FORM JAM PULANG --}}
                    <div>
                        <label for="departure_time"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jam Pulang:</label>
                        <input type="time" name="departure_time" id="departure_time"
                            value="{{ old('departure_time') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                {{-- FORM MAKAN PAGI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Makan Pagi:</label>
                    <div class="mt-2 space-x-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="sudah" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="belum" class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Belum</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kesehatan:</label>
                    <div class="mt-2 space-x-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sehat" class="form-radio text-indigo-600"
                                onchange="toggleKesehatan(false)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sakit" class="form-radio text-indigo-600"
                                onchange="toggleKesehatan(true)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sakit</span>
                        </label>
                    </div>
                    <div id="deskripsi_kesehatan" class="mt-4 hidden">
                        <label for="sickness_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Sakit:</label>
                        <textarea name="sickness_description" id="sickness_description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"></textarea>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="disertai obat"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Disertai Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="tanpa obat"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Tanpa Obat</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- TABLE KEGIATAN --}}
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-300 dark:divide-gray-700 border dark:border-gray-800">
                        <thead class="bg-indigo-600 dark:bg-indigo-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-50 uppercase tracking-wider">
                                    Waktu</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-50 uppercase tracking-wider">
                                    Kegiatan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white dark:text-gray-50 uppercase tracking-wider">
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
                                            <input type="radio" name="greeting_and_morning_prayer"
                                                value="mengikuti" class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="greeting_and_morning_prayer"
                                                value="tidak mengikuti" class="form-radio text-indigo-600">
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
                                    <select id="session1_description" name="session1_description"
                                        class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                                        <option value="">-- Pilih Sub Theme --</option>
                                        @forelse ($subthemes as $subtheme)
                                        @foreach ($subtheme->material as $material)
                                        <option value="{{ $material->id }}"
                                            data-theme-name="{{ $subtheme->theme->theme_name }}"
                                            {{ old('session1_description') == $material->id ? 'selected' : '' }}>
                                            {{ $material->material_name }}
                                        </option>
                                        @endforeach
                                        @empty
                                        <option value="">No available subthemes for the selected period
                                        </option>
                                        @endforelse
                                    </select>
                                    <x-input-error :messages="$errors->get('session1_description')" class="mt-2" />

                                    <div id="themeName"
                                        class="mt-2 w-96 text-gray-700 dark:text-gray-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                    </div>

                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="session1_activity" value="BB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="session1_activity" value="MB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="session1_activity" value="BSH"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="session1_activity" value="BSB"
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
                                            <input type="radio" name="toilet_training_and_duha_prayer"
                                                value="mengikuti" class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="toilet_training_and_duha_prayer"
                                                value="tidak mengikuti" class="form-radio text-indigo-600">
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
                                        <select id="session2_description" name="session2_description"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-300 dark:focus:ring-indigo-300 rounded-md shadow-sm">
                                            <option value="">-- Pilih Sub Theme --</option>
                                            @forelse ($subthemes as $subtheme)
                                            @foreach ($subtheme->material as $material)
                                            <option value="{{ $material->id }}"
                                                data-theme-name="{{ $subtheme->theme->theme_name }}"
                                                {{ old('session2_description') == $material->id ? 'selected' : '' }}>
                                                {{ $material->material_name }}
                                            </option>
                                            @endforeach
                                            @empty
                                            <option value="">No available subthemes for the selected period
                                            </option>
                                            @endforelse
                                        </select>
                                        <x-input-error :messages="$errors->get('session2_description')" class="mt-2" />

                                        <div id="themeName2"
                                            class="mt-2 w-96 text-gray-700 dark:text-gray-300 overflow-hidden text-ellipsis whitespace-nowrap">
                                        </div>

                                        <div class="mt-2 space-x-6">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="session2_activity" value="BB"
                                                    class="form-radio text-indigo-600">
                                                <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="session2_activity" value="MB"
                                                    class="form-radio text-indigo-600">
                                                <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="session2_activity" value="BSH"
                                                    class="form-radio text-indigo-600">
                                                <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="session2_activity" value="BSB"
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
                                            <input type="radio" name="morning_snack" value="habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Habis</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="morning_snack" value="tidak habis"
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
                                            <input type="radio" name="neatness_and_independence" value="mandiri"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mandiri</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="neatness_and_independence"
                                                value="kurang mandiri" class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Kurang Mandiri</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="neatness_and_independence"
                                                value="tidak mandiri" class="form-radio text-indigo-600">
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
                                            <input type="radio" name="cheerful_lunch" value="habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Habis</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cheerful_lunch" value="sisa sedikit"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sisa Sedikit</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cheerful_lunch" value="sisa banyak"
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
                                            <input type="radio" name="cleanliness_and_brushing_training"
                                                value="kurang" class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Kurang</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cleanliness_and_brushing_training"
                                                value="cukup" class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Cukup</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cleanliness_and_brushing_training"
                                                value="baik" class="form-radio text-indigo-600">
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
                                            <input type="radio" name="dhuhr_prayer" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="dhuhr_prayer" value="tidak mengikuti"
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
                                            <input type="radio" name="healthy_sleep" value="tidur"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidur</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="healthy_sleep" value="tidur sebentar"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tidur Sebentar</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="healthy_sleep" value="tidak tidur"
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
                                            <input type="radio" name="afternoon_bath" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="afternoon_bath" value="tidak mengikuti"
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
                                            <input type="radio" name="afternoon_snack" value="habis"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Habis</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="afternoon_snack" value="tidak habis"
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
                                            <input type="radio" name="asr_prayer" value="mengikuti"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">Mengikuti</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="asr_prayer" value="tidak mengikuti"
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
                                        name="extra_stimulation_description">{{ old('extra_stimulation_description') }}</textarea>
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="extra_stimulation" value="BB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="extra_stimulation" value="MB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="extra_stimulation" value="BSH"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="extra_stimulation" value="BSB"
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
                                        name="cheerful_play_description">{{ old('cheerful_play_description') }}</textarea>
                                    <div class="mt-2 space-x-6">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cheerful_play" value="BB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cheerful_play" value="MB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">MB</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cheerful_play" value="BSH"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSH</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="cheerful_play" value="BSB"
                                                class="form-radio text-indigo-600">
                                            <span class="ml-2 text-gray-700 dark:text-gray-300">BSB</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <!-- Tambahkan entri tambahan sesuai kebutuhan -->

                        </tbody>
                    </table>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi:</label>
                    <div class="mt-2 space-x-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="condition" value="tenang"
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Tenang</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="condition" value="rewel"
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Rewel</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="condition" value="temper tantrum"
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Temper Tantrum</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="stimulation_description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stimulasi:</label>
                    <textarea name="stimulation_description" id="stimulation_description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div>
                    <label for="notes"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan:</label>
                    <textarea name="notes" id="notes" rows="4"
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

        // Function to check attendance using AJAX
        function checkAttendance(studentId, date) {
            $.ajax({
                url: `/check-attendance/${studentId}/${date}`,
                type: 'GET',
                success: function(data) {
                    // Display the attendance status
                    $('#attendance-status').text(data.status);

                    // Convert check_in_time and check_out_time to HH:MM format
                    function formatTime(time) {
                        if (!time || time === 'Belum Check-in' || time === 'Belum Check-out') {
                            return ''; // Return empty string if no valid time
                        }
                        // Assuming time format is HH:mm:ss, extract HH:MM
                        return time.slice(0, 5); // Take first 5 characters (HH:MM)
                    }

                    // Check if check_in_time and check_out_time are available and not default
                    if (data.check_in_time && data.check_in_time !== 'Belum Check-in') {
                        $('#arrival_time').val(formatTime(data
                            .check_in_time)); // Set arrival_time to check_in_time
                    } else {
                        $('#arrival_time').val(
                            '{{ old('
                            arrival_time ') }}'); // Clear arrival_time if no check_in_time
                    }

                    if (data.check_out_time && data.check_out_time !== 'Belum Check-out') {
                        $('#departure_time').val(formatTime(data
                            .check_out_time)); // Set departure_time to check_out_time
                    } else {
                        $('#departure_time').val(
                            '{{ old('
                            arrival_time ') }}'); // Clear departure_time if no check_out_time
                    }
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
            let period = $('#period').val(); // Get the initial date from the input field

            // Check attendance on page load (for today's date)
            checkAttendance(studentId, period);

            // Re-check attendance when the 'period' date is changed
            $('#period').on('change', function() {
                const newDate = $(this).val(); // Get the new date when 'period' changes
                checkAttendance(studentId,
                    newDate); // Call the function to check attendance with the new date
            });
        });


        $(document).ready(function() {
            // Fungsi untuk update subthemes dropdown ketika period berubah
            function updateSubthemesDropdown(period) {
                $.ajax({
                    url: `/get-subthemes/${period}`, // Pastikan ini sesuai dengan route yang benar
                    type: 'GET',
                    success: function(data) {
                        let options = '<option value="">-- Pilih Sub Theme --</option>';

                        // Menambahkan subthemes ke dropdown
                        if (data.subthemes && data.subthemes.length > 0) {
                            data.subthemes.forEach(function(subtheme) {
                                subtheme.material.forEach(function(material) {
                                    options += `
                           <option value="${material.id}"
                                    data-theme-name="${subtheme.theme_name}"
                                    data-theme-description="${subtheme.theme_description}"
                                    data-sub-theme-name="${subtheme.sub_theme_name}"
                                    data-sub-theme-description="${subtheme.sub_theme_description}"
                                    title="${subtheme.theme_name} - ${subtheme.sub_theme_name}">
                                ${material.material_name}
                           </option>`;
                                });
                            });
                        } else {
                            options +=
                                '<option value="">No available subthemes for the selected period</option>';
                        }

                        $('#session1_description').html(
                            options); // Update the dropdown with the new options
                        $('#session2_description').html(
                            options); // Update the dropdown with the new options
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error); // Log error if AJAX fails
                    }
                });
            }

            // Update dropdown when the page loads initially
            const initialPeriod = $('#period').val(); // Get the initial date from the input field
            updateSubthemesDropdown(initialPeriod); // Call the function to update the dropdown

            // Update dropdown when the period changes
            $('#period').on('change', function() {
                const newPeriod = $(this).val(); // Get the new date when 'period' changes
                updateSubthemesDropdown(
                    newPeriod); // Call the function to update the dropdown with new data
            });

            // Update the theme and sub-theme description when the user selects an option in the dropdown
            $('#session1_description').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var themeName = selectedOption.data('theme-name');
                var subThemeName = selectedOption.data('sub-theme-name');

                // Display the theme and sub-theme descriptions
                $('#themeName').html(`
                <p><strong>Tema: </strong> ${themeName}</p>
                <p><strong>Sub Tema: </strong> ${subThemeName}</p>`);
            });

            // Update the theme and sub-theme description when the user selects an option in the dropdown
            $('#session2_description').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var themeName = selectedOption.data('theme-name');
                var subThemeName = selectedOption.data('sub-theme-name');

                // Display the theme and sub-theme descriptions
                $('#themeName2').html(`
                <p><strong>Tema: </strong> ${themeName}</p>
                <p><strong>Sub Tema: </strong> ${subThemeName}</p>`);
            });
        });
    </script>
</x-app-layout>