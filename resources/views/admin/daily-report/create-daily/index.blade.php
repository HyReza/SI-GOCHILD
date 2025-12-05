<x-app-layout>
    @push('head')
        {{-- Library Signature Pad --}}
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <style>
            /* Hide scrollbar */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            /* Smooth Transition */
            .transition-all {
                transition-property: all;
                transition-duration: 200ms;
            }
        </style>
    @endpush

    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6 my-6">

        {{-- HEADER --}}
        <div class="text-center">
            <h2 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">
                Al Jannah Preschool and Day Care
            </h2>
            @if ($activityTransaction->service_id == 1)
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                    Laporan Harian — Baby Childhood
                </h3>
            @else
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                    Laporan Harian Usia 25 Bulan - 72 Bulan — Children Daycare
                </h3>
            @endif
        </div>

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- ALERT ERROR --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Perhatian!</strong>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="dailyReportForm" action="{{ route('daily-report.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="activity_transaction_id" value="{{ $activityTransaction->id }}">

            {{-- 1. IDENTITAS SISWA --}}
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Siswa:</label>
                    <input type="text"
                        class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-600 cursor-not-allowed shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        value="{{ $activityTransaction->student->student_name }}" disabled>

                    <div class="mt-2 flex items-center gap-2 text-sm" id="attendance-status-box">
                        <span class="material-symbols-outlined text-gray-400 text-lg" id="att-icon">pending</span>
                        <span class="font-medium text-gray-500" id="attendance-status">Mengecek status absensi...</span>
                    </div>
                </div>
            </div>

            {{-- 2. TANGGAL & SUHU --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="period"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode:</label>
                    <input type="date" name="period" id="period"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        value="{{ now()->toDateString() }}">
                </div>

                <div>
                    <label for="body_temperature"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Suhu Tubuh (°C):</label>
                    <input type="number" step="0.1" name="body_temperature" id="body_temperature"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="36.5" value="{{ old('body_temperature') }}">
                </div>
            </div>

            {{-- 3. MAKAN PAGI & KESEHATAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Makan Pagi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Makan Pagi:</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="sudah" @checked(old('breakfast', 'sudah') == 'sudah')
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="belum" @checked(old('breakfast') == 'belum')
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Belum</span>
                        </label>
                    </div>
                </div>

                {{-- Kesehatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kesehatan:</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sehat" @checked(old('health_status', 'sehat') == 'sehat')
                                class="form-radio text-indigo-600" onchange="toggleKesehatanUI(false)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sakit" @checked(old('health_status') == 'sakit')
                                class="form-radio text-indigo-600" onchange="toggleKesehatanUI(true)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sakit</span>
                        </label>
                    </div>

                    {{-- Form Detail Sakit (Hidden by default) --}}
                    <div id="deskripsi_kesehatan"
                        class="mt-3 hidden p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md">
                        <label for="sickness_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi
                            Sakit:</label>
                        <textarea name="sickness_description" id="sickness_description" rows="2"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Contoh: Batuk, Pilek...">{{ old('sickness_description') }}</textarea>

                        <div class="mt-2 flex gap-4 text-sm">
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="disertai obat"
                                    @checked(old('medication_status') == 'disertai obat') class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Disertai Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="tanpa obat"
                                    @checked(old('medication_status') == 'tanpa obat') class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Tanpa Obat</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. KONDISI --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kondisi:</label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="tenang" @checked(old('condition', 'tenang') == 'tenang')
                            class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Tenang</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="rewel" @checked(old('condition') == 'rewel')
                            class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Rewel</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="temper tantrum" @checked(old('condition') == 'temper tantrum')
                            class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Temper Tantrum</span>
                    </label>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- INCLUDE FORM LAYANAN (BABY / CHILDREN) --}}
            {{-- ======================================================== --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                @if ($activityTransaction->service_id == 1)
                    @include('admin.daily-report.create-daily.partials.baby-form')
                @elseif ($activityTransaction->service_id == 2)
                    @include('admin.daily-report.create-daily.partials.children-form')
                @endif
            </div>

            {{-- 5. STIMULASI & CATATAN --}}
            <div class="grid grid-cols-1 gap-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div>
                    <label for="stimulation_description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Stimulasi (otomatis dari MMDST):
                    </label>
                    <textarea name="stimulation_description" id="stimulation_description" rows="4" readonly
                        class="w-full rounded-md border-gray-300 bg-gray-50 text-gray-600 text-sm cursor-not-allowed resize-none shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"
                        placeholder="Memuat saran stimulasi otomatis..."></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">*Terisi otomatis berdasarkan usia & item
                        MMDST.</p>
                </div>

                <div>
                    <label for="notes"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan:</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Catatan tambahan untuk orang tua...">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- 6. VALIDASI GURU (TANDA TANGAN) --}}
            <div
                class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Validasi Guru Pendamping</h3>

                <div class="max-w-md mx-auto text-left">
                    {{-- Nama Guru --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama
                            Guru</label>
                        <input type="text" name="teacher_name"
                            value="{{ old('teacher_name', Auth::user()->user_name) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- Canvas --}}
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanda
                            Tangan</label>
                        <div
                            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 overflow-hidden">
                            <canvas id="teacher-signature-pad"
                                class="block w-full h-40 cursor-crosshair touch-none"></canvas>
                            <button type="button" id="clear-sig-btn"
                                class="absolute top-2 right-2 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded transition">
                                Hapus
                            </button>
                            <input type="hidden" name="teacher_signature" id="teacher-signature-input">
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-center">Silakan tanda tangan di kotak di atas.</p>
                    </div>
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('daily-report.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                    Kembali
                </a>
                <button type="button" id="submitFormButton"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white">
                    Simpan
                </button>
            </div>

        </form>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const studentId = {{ $activityTransaction->student->id }};
            const atId = {{ $activityTransaction->id }};
            const serviceId = {{ $activityTransaction->service_id }};
            const periodEl = document.getElementById('period');

            // 1. SIGNATURE PAD
            const canvas = document.getElementById('teacher-signature-pad');
            let signaturePad;
            if (canvas) {
                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                }
                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();

                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(0,0,0,0)',
                    penColor: 'rgb(0, 0, 0)',
                    velocityFilterWeight: 0.7
                });
                document.getElementById('clear-sig-btn').addEventListener('click', () => signaturePad.clear());
            }

            // 2. SUBMIT LOGIC
            document.getElementById('submitFormButton').addEventListener('click', function(e) {
                e.preventDefault();
                if (signaturePad && !signaturePad.isEmpty()) {
                    document.getElementById('teacher-signature-input').value = signaturePad.toDataURL(
                        'image/png');
                }

                Swal.fire({
                    title: 'Simpan Laporan?',
                    text: "Pastikan data sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            didOpen: () => Swal.showLoading()
                        });
                        document.getElementById('dailyReportForm').submit();
                    }
                });
            });

            // 3. HELPER: AUTO RESIZE TEXTAREA
            function autoResizeTextarea(elem) {
                if (!elem) return;
                elem.style.height = 'auto';
                elem.style.height = (elem.scrollHeight) + 'px';
            }

            // 4. CEK ABSENSI
            function checkAttendance(sid, date) {
                const statusEl = document.getElementById('attendance-status');
                const iconEl = document.getElementById('att-icon');
                const boxEl = document.getElementById('attendance-status-box');

                statusEl.textContent = 'Mengecek...';

                fetch(`{{ route('daily-report.check-attendance', ['student' => '_SID_', 'date' => '_DATE_']) }}`
                        .replace('_SID_', sid).replace('_DATE_', date))
                    .then(r => r.json())
                    .then(data => {
                        statusEl.textContent = data.status || 'Tidak diketahui';

                        // Reset Class
                        boxEl.className = "mt-2 flex items-center gap-2 text-sm";

                        if (data.status === 'Hadir') {
                            boxEl.classList.add('text-green-600', 'font-bold');
                            iconEl.textContent = 'check_circle';
                            iconEl.className = "material-symbols-outlined text-lg text-green-600";
                        } else {
                            boxEl.classList.add('text-amber-600', 'font-bold');
                            iconEl.textContent = 'warning';
                            iconEl.className = "material-symbols-outlined text-lg text-amber-500";
                        }
                    })
                    .catch(() => statusEl.textContent = 'Gagal koneksi.');
            }

            // 5. LOAD STIMULASI
            function loadStimulation(txId, date) {
                const area = document.getElementById('stimulation_description');
                if (!area) return;
                area.value = 'Memuat data MMDST...';
                autoResizeTextarea(area);

                fetch(`{{ route('daily-report.stimulation.suggest', ['activityTransaction' => '_AT_', 'date' => '_DATE_']) }}`
                        .replace('_AT_', txId).replace('_DATE_', date))
                    .then(r => r.json())
                    .then(d => {
                        area.value = d.text || 'Tidak ada saran stimulasi.';
                        autoResizeTextarea(area);
                    })
                    .catch(() => area.value = 'Gagal memuat.');
            }

            // 6. LOAD MATERI (CHILDREN)
            function loadSubthemes(dateStr) {
                const s1 = document.getElementById('session1_material_id');
                const s2 = document.getElementById('session2_material_id');
                if (!s1 && !s2) return;

                const loadingOpt = '<option value="" disabled selected>Memuat materi...</option>';
                s1.innerHTML = loadingOpt;
                s2.innerHTML = loadingOpt;

                fetch(`{{ route('daily-report.get-subthemes', ['date' => '_DATE_']) }}`.replace('_DATE_', dateStr))
                    .then(r => r.json())
                    .then(({
                        materials
                    }) => {
                        let options = '<option value="">-- Pilih Materi --</option>';
                        if (!materials || materials.length === 0) {
                            options += '<option value="" disabled>Tidak ada materi</option>';
                        } else {
                            materials.forEach(m => {
                                options +=
                                    `<option value="${m.id}" data-theme="${m.theme_name}" data-sub="${m.sub_theme_name}">${m.material_name}</option>`;
                            });
                        }
                        s1.innerHTML = options;
                        s2.innerHTML = options;

                        document.getElementById('themeName1').innerHTML = '';
                        document.getElementById('themeName2').innerHTML = '';
                    });
            }

            function bindMaterialChange(selectId, targetId) {
                const sel = document.getElementById(selectId);
                const tgt = document.getElementById(targetId);
                if (!sel || !tgt) return;

                sel.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    const th = opt?.getAttribute('data-theme') || '';
                    const sub = opt?.getAttribute('data-sub') || '';
                    if (th || sub) {
                        tgt.innerHTML =
                            `<div class="mt-1 text-xs text-gray-500 bg-gray-50 p-1 rounded border"><strong>Tema:</strong> ${th} <br> <strong>Sub:</strong> ${sub}</div>`;
                    } else {
                        tgt.innerHTML = '';
                    }
                });
            }

            // 7. KESEHATAN UI
            window.toggleKesehatanUI = function(isSick) {
                const el = document.getElementById('deskripsi_kesehatan');
                if (isSick) el.classList.remove('hidden');
                else el.classList.add('hidden');
            }

            // --- INIT ---
            checkAttendance(studentId, periodEl.value);
            loadStimulation(atId, periodEl.value);

            if (serviceId === 2) {
                loadSubthemes(periodEl.value);
                bindMaterialChange('session1_material_id', 'themeName1');
                bindMaterialChange('session2_material_id', 'themeName2');
            }

            const checkedHealth = document.querySelector('input[name="health_status"]:checked');
            if (checkedHealth && checkedHealth.value === 'sakit') toggleKesehatanUI(true);

            // --- LISTENERS ---
            periodEl.addEventListener('change', function() {
                checkAttendance(studentId, this.value);
                loadStimulation(atId, this.value);
                if (serviceId === 2) loadSubthemes(this.value);
            });

            document.querySelectorAll('input[name="health_status"]').forEach(el => {
                el.addEventListener('change', () => toggleKesehatanUI(el.value === 'sakit'));
            });
        });
    </script>
</x-app-layout>
