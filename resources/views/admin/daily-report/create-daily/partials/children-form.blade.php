<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-indigo-500">school</span>
            Jadwal & Aktivitas Anak
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[800px]">
            <thead class="bg-indigo-600 dark:bg-indigo-800 text-white">
                <tr>
                    <th class="px-6 py-4 w-40 whitespace-nowrap">Waktu</th>
                    <th class="px-6 py-4 w-1/3">Kegiatan</th>
                    <th class="px-6 py-4">Keterangan / Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">

                {{-- 06:30 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">06:30 - 07:30</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Salam Penyambutan & Doa</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <label class="inline-flex items-center"><input type="radio"
                                    name="greeting_and_morning_prayer" value="mengikuti" @checked(old('greeting_and_morning_prayer', 'mengikuti') == 'mengikuti')
                                    class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Mengikuti</span></label>
                            <label class="inline-flex items-center"><input type="radio"
                                    name="greeting_and_morning_prayer" value="tidak mengikuti"
                                    @checked(old('greeting_and_morning_prayer') == 'tidak mengikuti') class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Tidak Mengikuti</span></label>
                        </div>
                    </td>
                </tr>

                {{-- 07:30 - SESI 1 --}}
                <tr>
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">07:30
                        - 09:00</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Bermain & Belajar <span
                            class="font-bold">Sesi
                            1</span>
                    </td>
                    <td class="px-6 py-4">
                        {{-- Dropdown Materi --}}
                        <select id="session1_material_id" name="session1_material_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">-- Pilih Materi --</option>
                            {{-- Option diisi via JS --}}
                        </select>
                        <div id="themeName1" class="mt-1 text-xs text-gray-500 dark:text-gray-400 italic"></div>

                        {{-- Penilaian --}}
                        <div class="flex flex-wrap gap-4 mt-3">
                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $s)
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="session1_activity" value="{{ $s }}"
                                        @checked(old('session1_activity', 'BSB') == $s)
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <span
                                        class="ml-1.5 text-gray-600 dark:text-gray-300 text-xs font-bold group-hover:text-indigo-600 transition-colors">{{ $s }}</span>
                                </label>
                            @endforeach
                        </div>
                    </td>
                </tr>
                {{-- 09:00 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">09:00 - 09:30</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Toilet Training & Sholat Dhuha
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <label class="inline-flex items-center"><input type="radio"
                                    name="toilet_training_and_duha_prayer" value="mengikuti"
                                    @checked(old('toilet_training_and_duha_prayer', 'mengikuti') == 'mengikuti') class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Mengikuti</span></label>
                            <label class="inline-flex items-center"><input type="radio"
                                    name="toilet_training_and_duha_prayer" value="tidak mengikuti"
                                    @checked(old('toilet_training_and_duha_prayer') == 'tidak mengikuti') class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Tidak Mengikuti</span></label>
                        </div>
                    </td>
                </tr>

                {{-- 09:30 - SESI 2 --}}
                <tr>
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">09:30
                        - 10:00</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Bermain & Belajar <span
                            class="font-bold">Sesi
                            2</span>
                    </td>
                    <td class="px-6 py-4">
                        {{-- Dropdown Materi --}}
                        <select id="session2_material_id" name="session2_material_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">-- Pilih Materi --</option>
                            {{-- Option diisi via JS --}}
                        </select>
                        <div id="themeName2" class="mt-1 text-xs text-gray-500 dark:text-gray-400 italic"></div>

                        {{-- Penilaian --}}
                        <div class="flex flex-wrap gap-4 mt-3">
                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $s)
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="radio" name="session2_activity" value="{{ $s }}"
                                        @checked(old('session2_activity', 'BSB') == $s)
                                        class="text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <span
                                        class="ml-1.5 text-gray-600 dark:text-gray-300 text-xs font-bold group-hover:text-indigo-600 transition-colors">{{ $s }}</span>
                                </label>
                            @endforeach
                        </div>
                    </td>
                </tr>

                {{-- 10:00 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">10:00 - 10:30</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Snack Pagi</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <label class="inline-flex items-center"><input type="radio" name="morning_snack"
                                    value="habis" @checked(old('morning_snack', 'habis') == 'habis') class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Habis</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="morning_snack"
                                    value="tidak habis" @checked(old('morning_snack') == 'tidak habis')
                                    class="form-radio text-indigo-600"><span class="ml-2 text-gray-700">Tidak
                                    Habis</span></label>
                        </div>
                    </td>
                </tr>

                {{-- 10:30 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">10:30 - 11:15</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Kerapian & Kemandirian</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-4">
                            @foreach (['mandiri' => 'Mandiri', 'kurang mandiri' => 'Kurang', 'tidak mandiri' => 'Tidak'] as $val => $label)
                                <label class="inline-flex items-center"><input type="radio"
                                        name="neatness_and_independence" value="{{ $val }}"
                                        @checked(old('neatness_and_independence', 'mandiri') == $val) class="form-radio text-indigo-600"><span
                                        class="ml-2 text-gray-700">{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </td>
                </tr>

                {{-- 11:15 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">11:15 - 11:45</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Makan Siang Ceria</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-4">
                            @foreach (['habis', 'sisa sedikit', 'sisa banyak'] as $val)
                                <label class="inline-flex items-center"><input type="radio" name="cheerful_lunch"
                                        value="{{ $val }}" @checked(old('cheerful_lunch', 'habis') == $val)
                                        class="form-radio text-indigo-600"><span
                                        class="ml-2 text-gray-700 capitalize">{{ $val }}</span></label>
                            @endforeach
                        </div>
                    </td>
                </tr>

                {{-- 11:45 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">11:45 - 12:00</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Gosok Gigi</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            @foreach (['baik', 'cukup', 'kurang'] as $val)
                                <label class="inline-flex items-center"><input type="radio"
                                        name="cleanliness_and_brushing_training" value="{{ $val }}"
                                        @checked(old('cleanliness_and_brushing_training', 'baik') === $val) class="form-radio text-indigo-600"><span
                                        class="ml-2 text-gray-700 capitalize">{{ $val }}</span></label>
                            @endforeach
                        </div>
                    </td>
                </tr>

                {{-- 12:00 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">12:00 - 12:30</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Sholat Dzuhur</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <label class="inline-flex items-center"><input type="radio" name="dhuhr_prayer"
                                    value="mengikuti" @checked(old('dhuhr_prayer', 'mengikuti') == 'mengikuti')
                                    class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Mengikuti</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="dhuhr_prayer"
                                    value="tidak mengikuti" @checked(old('dhuhr_prayer') == 'tidak mengikuti')
                                    class="form-radio text-indigo-600"><span class="ml-2 text-gray-700">Tidak
                                    Mengikuti
                                    Mengikuti</span></label>
                        </div>
                    </td>
                </tr>

                {{-- 12:30 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">12:30 - 14:00</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Tidur Sehat</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-4">
                            @foreach (['tidur' => 'Tidur', 'tidur sebentar' => 'Sebentar', 'tidak tidur' => 'Tidak'] as $val => $label)
                                <label class="inline-flex items-center"><input type="radio" name="healthy_sleep"
                                        value="{{ $val }}" @checked(old('healthy_sleep', 'tidur') == $val)
                                        class="form-radio text-indigo-600"><span
                                        class="ml-2 text-gray-700">{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </td>
                </tr>

                {{-- 14:00 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">14:00 - 14:30</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Mandi Sore</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <label class="inline-flex items-center"><input type="radio" name="afternoon_bath"
                                    value="mengikuti" @checked(old('afternoon_bath', 'mengikuti') == 'mengikuti')
                                    class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Mengikuti</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="afternoon_bath"
                                    value="tidak mengikuti" @checked(old('afternoon_bath') == 'tidak mengikuti')
                                    class="form-radio text-indigo-600"><span class="ml-2 text-gray-700">Tidak
                                    Mengikuti</span></label>
                        </div>
                    </td>
                </tr>

                {{-- 14:30 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">14:30 - 15:00</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Snack Sore</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <label class="inline-flex items-center"><input type="radio" name="afternoon_snack"
                                    value="habis" @checked(old('afternoon_snack', 'habis') == 'habis')
                                    class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Habis</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="afternoon_snack"
                                    value="tidak habis" @checked(old('afternoon_snack') == 'tidak habis')
                                    class="form-radio text-indigo-600"><span class="ml-2 text-gray-700">Tidak
                                    Habis</span></label>
                        </div>
                    </td>
                </tr>

                {{-- 15:00 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">15:00 - 15:30</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Sholat Ashar</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4">
                            <label class="inline-flex items-center"><input type="radio" name="asr_prayer"
                                    value="mengikuti" @checked(old('asr_prayer', 'mengikuti') == 'mengikuti')
                                    class="form-radio text-indigo-600"><span
                                    class="ml-2 text-gray-700">Mengikuti</span></label>
                            <label class="inline-flex items-center"><input type="radio" name="asr_prayer"
                                    value="tidak mengikuti" @checked(old('asr_prayer') == 'tidak mengikuti')
                                    class="form-radio text-indigo-600"><span class="ml-2 text-gray-700">Tidak
                                    Mengikuti</span></label>
                        </div>
                    </td>
                </tr>

                {{-- 15:30 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">15:30 - 16:00</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Ekstra Stimulasi</td>
                    <td class="px-6 py-4 space-y-2">
                        <textarea name="extra_stimulation_description" rows="1"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Deskripsi (opsional)">{{ old('extra_stimulation_description') }}</textarea>
                        <div class="flex gap-3">
                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $opt)
                                <label class="inline-flex items-center"><input type="radio"
                                        name="extra_stimulation" value="{{ $opt }}"
                                        @checked(old('extra_stimulation', 'BSB') === $opt) class="form-radio text-indigo-600"><span
                                        class="ml-1 text-xs font-bold text-gray-600">{{ $opt }}</span></label>
                            @endforeach
                        </div>
                    </td>
                </tr>

                {{-- 16:00 --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">16:00 - 17:00</td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">Permainan Ceria</td>
                    <td class="px-6 py-4 space-y-2">
                        <textarea name="cheerful_play_description" rows="1"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Deskripsi (opsional)">{{ old('cheerful_play_description') }}</textarea>
                        <div class="flex gap-3">
                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $opt)
                                <label class="inline-flex items-center"><input type="radio" name="cheerful_play"
                                        value="{{ $opt }}" @checked(old('cheerful_play', 'BSB') === $opt)
                                        class="form-radio text-indigo-600"><span
                                        class="ml-1 text-xs font-bold text-gray-600">{{ $opt }}</span></label>
                            @endforeach
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
