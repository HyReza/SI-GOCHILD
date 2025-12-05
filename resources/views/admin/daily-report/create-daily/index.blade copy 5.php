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
                    <div>
                        <label for="arrival_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jam Datang:
                        </label>
                        <input type="time" name="arrival_time" id="arrival_time" value="{{ old('arrival_time') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="departure_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jam Pulang:
                        </label>
                        <input type="time" name="departure_time" id="departure_time"
                            value="{{ old('departure_time') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Makan pagi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Makan Pagi:</label>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="breakfast" value="sudah"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="breakfast" value="belum"
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
                                <input type="radio" name="health_status" value="sehat" checked
                                    class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="health_status" value="sakit"
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
                            {{-- ... Kode Alpine.js untuk form bayi ... --}}
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
                                {{-- Salam & Doa Pagi --}}
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">06:30 - 07:30</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Salam Penyambutan
                                        dan Do'a Pagi</td>
                                    <td class="px-6 py-4">
                                        {{-- ... radio buttons ... --}}
                                    </td>
                                </tr>

                                {{-- Session 1 --}}
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
                                            {{-- ... radio buttons (BB, MB, BSH, BSB) ... --}}
                                        </div>
                                    </td>
                                </tr>

                                {{-- Toilet & Dhuha --}}
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">09:00 - 09:30</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">Toilet Training &
                                        Sholat Dhuha</td>
                                    <td class="px-6 py-4">
                                        {{-- ... radio buttons ... --}}
                                    </td>
                                </tr>

                                {{-- Session 2 --}}
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
                                            {{-- ... radio buttons (BB, MB, BSH, BSB) ... --}}
                                        </div>
                                    </td>
                                </tr>

                                {{-- ... Sisa baris tabel (Snack Pagi, Kerapian, dll) ... --}}
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Kondisi --}}
                <div>
                    {{-- ... kode radio button kondisi ... --}}
                </div>

                {{-- Stimulasi (Auto, Readonly) --}}
                <div>
                    {{-- ... kode textarea stimulasi ... --}}
                </div>

                {{-- Catatan --}}
                <div>
                    {{-- ... kode textarea catatan ... --}}
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

            fetch(`{{ route('daily-report.get-subthemes', ['date' => '_DATE_']) }}`.replace('_DATE_', dateStr))
                .then(r => r.json())
                .then(({
                    materials
                }) => {
                    const options = ['<option value="">-- Pilih Materi --</option>'];
                    if (!materials || materials.length === 0) {
                        options.push('<option value="" disabled>Tidak ada materi pada periode ini</option>');
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
                    `<p><strong>Tema:</strong> ${theme}</p><p><strong>Sub Tema:</strong> ${sub}</p>` :
                    '';
            });
        }

        // Auto stimulasi (readonly)
        function loadStimulation(activityTransactionId, dateStr) {
            // ... (kode tidak berubah)
        }

        // INIT
        document.addEventListener('DOMContentLoaded', function() {
            const studentId = {{ $activityTransaction->student->id }};
            const atId = {{ $activityTransaction->id }};
            const serviceId = {{ $activityTransaction->service_id }};
            const periodEl = document.getElementById('period');

            // initial
            checkAttendance(studentId, periodEl.value);
            loadStimulation(atId, periodEl.value);
            if (serviceId === 2) {
                loadSubthemes(periodEl.value);
                bindMaterialChange('session1_material_id', 'themeName1');
                bindMaterialChange('session2_material_id', 'themeName2');
            }

            // on change
            periodEl.addEventListener('change', function() {
                const d = this.value;
                checkAttendance(studentId, d);
                loadStimulation(atId, d);
                if (serviceId === 2) loadSubthemes(d);
            });

            // show/hide sakit block on initial
            const checkedHealth = document.querySelector('input[name="health_status"]:checked');
            toggleKesehatanUI(checkedHealth && checkedHealth.value === 'sakit');
        });
    </script>
</x-app-layout>
