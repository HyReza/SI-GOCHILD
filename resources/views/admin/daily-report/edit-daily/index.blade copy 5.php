<x-app-layout>
    @push('head')
        {{-- Library Signature Pad --}}
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <style>
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .transition-all {
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
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                Edit Laporan Harian —
                {{ $activityTransaction->service_id == 1 ? 'Baby Childhood' : 'Children Daycare' }}
            </h3>
        </div>

        {{-- ALERTS --}}
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

        <form id="dailyReportEditForm" action="{{ route('daily-report.update', $dailyReport->id) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="activity_transaction_id" value="{{ $activityTransaction->id }}">

            {{-- 1. IDENTITAS SISWA --}}
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Siswa:</label>
                    <input type="text"
                        class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-600 cursor-not-allowed shadow-sm focus:ring-0 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
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
                        value="{{ old('period', \Carbon\Carbon::parse($dailyReport->period)->toDateString()) }}">
                </div>

                <div>
                    <label for="body_temperature"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Suhu Tubuh (°C):</label>
                    <input type="number" step="0.1" name="body_temperature" id="body_temperature"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="36.5" value="{{ old('body_temperature', $dailyReport->body_temperature) }}">
                </div>
            </div>

            {{-- 3. MAKAN PAGI & KESEHATAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Makan Pagi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Makan Pagi:</label>
                    <div class="flex gap-4">
                        @php $bf = old('breakfast', $dailyReport->breakfast); @endphp
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="sudah" {{ $bf == 'sudah' ? 'checked' : '' }}
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="belum" {{ $bf == 'belum' ? 'checked' : '' }}
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Belum</span>
                        </label>
                    </div>
                </div>

                {{-- Kesehatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kesehatan:</label>
                    <div class="flex gap-4">
                        @php $hs = old('health_status', $dailyReport->health_status); @endphp
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sehat"
                                {{ $hs == 'sehat' ? 'checked' : '' }} class="form-radio text-indigo-600"
                                onchange="toggleKesehatanUI(false)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sakit"
                                {{ $hs == 'sakit' ? 'checked' : '' }} class="form-radio text-indigo-600"
                                onchange="toggleKesehatanUI(true)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sakit</span>
                        </label>
                    </div>

                    <div id="deskripsi_kesehatan"
                        class="{{ $hs == 'sakit' ? '' : 'hidden' }} mt-3 p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi
                            Sakit:</label>
                        <textarea name="sickness_description" id="sickness_description" rows="2"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Contoh: Batuk, Pilek...">{{ old('sickness_description', $dailyReport->sickness_description) }}</textarea>

                        @php $med = old('medication_status', $dailyReport->medication_status); @endphp
                        <div class="mt-2 flex gap-4 text-sm">
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="disertai obat"
                                    {{ $med == 'disertai obat' ? 'checked' : '' }} class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Disertai Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="tanpa obat"
                                    {{ $med == 'tanpa obat' ? 'checked' : '' }} class="form-radio text-indigo-600">
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
                    @php $cond = old('condition', $dailyReport->condition); @endphp
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="tenang" {{ $cond == 'tenang' ? 'checked' : '' }}
                            class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Tenang</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="rewel"
                            {{ $cond == 'rewel' ? 'checked' : '' }} class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Rewel</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="temper tantrum"
                            {{ $cond == 'temper tantrum' ? 'checked' : '' }} class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Temper Tantrum</span>
                    </label>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- FORM DAILY ACTIVITY --}}
            {{-- ======================================================== --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                @if ($activityTransaction->service_id == 1)
                    @include('admin.daily-report.create-daily.partials.baby-form')
                @elseif ($activityTransaction->service_id == 2)
                    @include('admin.daily-report.create-daily.partials.children-form')
                @endif
            </div>


            {{-- Stimulasi & Catatan --}}
            <div>
                <label for="stimulation_description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Stimulasi (otomatis dari MMDST):
                </label>
                <textarea name="stimulation_description" id="stimulation_description" rows="4" readonly
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 cursor-not-allowed resize-none overflow-hidden"
                    placeholder="Memuat saran stimulasi otomatis...">{{ old('stimulation_description', $dailyReport->stimulation_description) }}</textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    *Terisi otomatis berdasarkan rentang usia & item belum lulus.
                </p>
            </div>

            <div>
                <label for="notes"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan:</label>
                <textarea name="notes" id="notes" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">{{ old('notes', $dailyReport->notes) }}</textarea>
            </div>

            {{-- VALIDASI GURU --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-8 mt-8">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 text-center">Validasi Guru Pendamping
                </h3>

                <div
                    class="max-w-md mx-auto bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    {{-- Input Nama Guru --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama
                            Guru</label>
                        <input type="text" name="teacher_name"
                            value="{{ old('teacher_name', $dailyReport->teacher_name ?? Auth::user()->user_name) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 text-sm font-medium">
                    </div>

                    {{-- Canvas Tanda Tangan --}}
                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tanda
                            Tangan</label>

                        @if ($dailyReport->teacher_signature)
                            <div id="signature-display"
                                class="relative group border-2 border-green-400 border-dashed rounded-lg bg-green-50 p-2 flex justify-center items-center h-40 mb-2">
                                <img src="{{ asset('storage/' . $dailyReport->teacher_signature) }}"
                                    class="max-h-full max-w-full object-contain">
                                <div
                                    class="absolute inset-0 bg-black/50 rounded-lg hidden group-hover:flex items-center justify-center transition">
                                    <button type="button" id="btn-change-sig"
                                        class="bg-white text-red-600 px-4 py-2 rounded font-bold shadow hover:bg-red-50">Ganti
                                        TTD</button>
                                </div>
                                <input type="hidden" name="clear_signature" id="clear_signature_input"
                                    value="0">
                            </div>
                        @endif

                        <div id="signature-pad-container"
                            class="relative group border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 overflow-hidden hover:border-indigo-400 transition-colors {{ $dailyReport->teacher_signature ? 'hidden' : '' }}">
                            <canvas id="teacher-signature-pad"
                                class="block w-full h-40 cursor-crosshair touch-none"></canvas>
                            <div class="absolute top-2 right-2">
                                <button type="button" id="clear-sig-btn"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded transition">
                                    Hapus
                                </button>
                            </div>
                            <input type="hidden" name="teacher_signature" id="teacher-signature-input">
                        </div>
                        <p class="text-xs text-gray-400 mt-1 text-center">Goreskan tanda tangan di kotak di atas.</p>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-end mt-6 space-x-4 border-t pt-4 border-gray-100 dark:border-gray-700">
                <a href="{{ route('daily-report.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Batal
                </a>
                <x-primary-button id="submitFormButton" class="ml-auto">
                    {{ __('Simpan Perubahan') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const studentId = {{ $activityTransaction->student->id }};
            const atId = {{ $activityTransaction->id }};
            const serviceId = {{ $activityTransaction->service_id }};
            const periodEl = document.getElementById('period');

            // 1. SIGNATURE PAD
            const canvas = document.getElementById('teacher-signature-pad');
            const displayDiv = document.getElementById('signature-display');
            const padContainer = document.getElementById('signature-pad-container');
            const changeBtn = document.getElementById('btn-change-sig');
            const clearInput = document.getElementById('clear_signature_input');
            let signaturePad;

            function initSignaturePad() {
                if (canvas && !signaturePad) {
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
                        penColor: 'rgb(0, 0, 0)'
                    });
                    document.getElementById('clear-sig-btn').addEventListener('click', () => signaturePad.clear());
                }
            }

            if (displayDiv && changeBtn) {
                changeBtn.addEventListener('click', () => {
                    displayDiv.classList.add('hidden');
                    padContainer.classList.remove('hidden');
                    if (clearInput) clearInput.value = '1';
                    initSignaturePad();
                });
            } else {
                initSignaturePad();
            }

            // 2. SUBMIT HANDLER
            document.getElementById('submitFormButton').addEventListener('click', function(e) {
                e.preventDefault();

                if (signaturePad && !signaturePad.isEmpty()) {
                    document.getElementById('teacher-signature-input').value = signaturePad.toDataURL(
                        'image/png');
                }

                // Ensure baby JSON is serialized if exists
                if (document.getElementById('babyEditorBox')) {
                    // AlpineJS usually auto-updates hidden inputs via x-effect/x-init,
                    // but we can force a check if needed. The current baby-form uses x-init which is good.
                }

                Swal.fire({
                    title: 'Konfirmasi Simpan',
                    text: "Pastikan data sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#4f46e5'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            didOpen: () => Swal.showLoading()
                        });
                        document.getElementById('dailyReportEditForm').submit();
                    }
                });
            });

            // 3. LOGIC KESEHATAN
            window.toggleKesehatanUI = function(isSick) {
                const el = document.getElementById('deskripsi_kesehatan');
                if (isSick) el.classList.remove('hidden');
                else el.classList.add('hidden');
            }
            // Init state from old/DB
            const checkedHealth = document.querySelector('input[name="health_status"]:checked');
            if (checkedHealth && checkedHealth.value === 'sakit') toggleKesehatanUI(true);

            document.querySelectorAll('input[name="health_status"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    toggleKesehatanUI(this.value === 'sakit');
                });
            });


            // 4. HELPER: RESIZE TEXTAREA
            function autoResizeTextarea(elem) {
                if (!elem) return;
                elem.style.height = 'auto';
                elem.style.height = (elem.scrollHeight) + 'px';
            }

            // 5. CEK ABSENSI
            function checkAttendance(sid, date) {
                const statusEl = document.getElementById('attendance-status');
                const iconEl = document.getElementById('att-icon');
                const boxEl = document.getElementById('attendance-status-box');

                statusEl.textContent = 'Mengecek...';

                fetch(`{{ route('daily-report.check-attendance', ['student' => '_SID_', 'date' => '_DATE_']) }}`
                        .replace('_SID_', sid).replace('_DATE_', date))
                    .then(r => r.json())
                    .then(data => {
                        statusEl.textContent = data.status || 'Status tidak diketahui';

                        if (data.status === 'Hadir') {
                            boxEl.className = "mt-2 flex items-center gap-2 text-sm text-green-600 font-bold";
                            iconEl.textContent = 'check_circle';
                            iconEl.className = "material-symbols-outlined text-lg text-green-600";
                        } else {
                            boxEl.className = "mt-2 flex items-center gap-2 text-sm text-amber-600 font-bold";
                            iconEl.textContent = 'warning';
                            iconEl.className = "material-symbols-outlined text-lg text-amber-500";
                        }
                    })
                    .catch(() => {
                        statusEl.textContent = 'Gagal koneksi.';
                    });
            }

            // 6. LOAD STIMULASI
            function loadStimulation(txId, date) {
                const area = document.getElementById('stimulation_description');
                if (!area) return;

                // Jangan timpa jika ini load awal (karena ada data dari DB/Old)
                // Hanya load jika tanggal BERUBAH dari value awal
                // Tapi untuk simplifikasi, kita cek apakah area kosong atau tidak.
                // Jika user ganti tanggal, kita load baru.

                // fetch only on date change logic
            }

            function fetchStimulationAjax(date) {
                const area = document.getElementById('stimulation_description');
                area.value = 'Memuat...';
                autoResizeTextarea(area);

                fetch(`{{ route('daily-report.stimulation.suggest', ['activityTransaction' => '_AT_', 'date' => '_DATE_']) }}`
                        .replace('_AT_', atId).replace('_DATE_', date))
                    .then(r => r.json())
                    .then(d => {
                        area.value = d.text || 'Tidak ada saran stimulasi.';
                        autoResizeTextarea(area);
                    })
                    .catch(() => area.value = 'Gagal memuat.');
            }

            // 7. LOAD MATERI (CHILDREN)
            function loadSubthemes(dateStr, sel1 = null, sel2 = null) {
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

                        if (sel1) {
                            s1.value = sel1;
                            s1.dispatchEvent(new Event('change'));
                        }
                        if (sel2) {
                            s2.value = sel2;
                            s2.dispatchEvent(new Event('change'));
                        }

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
                            `<div class="mt-1 text-xs text-indigo-600 bg-indigo-50 p-1 rounded border border-indigo-100">Tema: ${th} <br> Sub: ${sub}</div>`;
                    } else {
                        tgt.innerHTML = '';
                    }
                });
            }

            // --- INITIALIZATION ---
            checkAttendance(studentId, periodEl.value);
            autoResizeTextarea(document.getElementById('stimulation_description'));

            if (serviceId === 2) {
                // Load initial materials with selected values from DB
                loadSubthemes(
                    periodEl.value,
                    "{{ old('session1_material_id', $dailyReport->childrenDetail->session1_material_id ?? '') }}",
                    "{{ old('session2_material_id', $dailyReport->childrenDetail->session2_material_id ?? '') }}"
                );
                bindMaterialChange('session1_material_id', 'themeName1');
                bindMaterialChange('session2_material_id', 'themeName2');
            }

            // Date Change Event
            periodEl.addEventListener('change', function() {
                checkAttendance(studentId, this.value);
                fetchStimulationAjax(this.value);
                if (serviceId === 2) loadSubthemes(this.value);
            });

        });
    </script>
</x-app-layout>
