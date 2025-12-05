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
                Edit Laporan Harian - Al Jannah
            </h2>
            @if ($activityTransaction->service_id == 1)
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                    Baby Childhood
                </h3>
            @else
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                    Children Daycare
                </h3>
            @endif
        </div>

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

        {{-- FORM UPDATE --}}
        <form id="dailyReportForm" action="{{ route('daily-report.update', $dailyReport->id) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PUT')

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
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="sudah" @checked(old('breakfast', $dailyReport->breakfast) == 'sudah')
                                class="form-radio text-indigo-600">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sudah</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="breakfast" value="belum" @checked(old('breakfast', $dailyReport->breakfast) == 'belum')
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
                            <input type="radio" name="health_status" value="sehat" @checked(old('health_status', $dailyReport->health_status) == 'sehat')
                                class="form-radio text-indigo-600" onchange="toggleKesehatanUI(false)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sehat</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="health_status" value="sakit" @checked(old('health_status', $dailyReport->health_status) == 'sakit')
                                class="form-radio text-indigo-600" onchange="toggleKesehatanUI(true)">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Sakit</span>
                        </label>
                    </div>

                    {{-- Form Detail Sakit (Hidden by default) --}}
                    <div id="deskripsi_kesehatan"
                        class="mt-3 hidden p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md">

                        <label for="sickness_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi Sakit:
                        </label>

                        {{-- Perubahan di sini: menambahkan style overflow-hidden, resize-none, dan event oninput --}}
                        <textarea name="sickness_description" id="sickness_description" rows="2" oninput="autoResize(this)"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none overflow-hidden transition-all duration-200 ease-in-out"
                            placeholder="Contoh: Batuk, Pilek...">{{ old('sickness_description', $dailyReport->sickness_description) }}</textarea>

                        <div class="mt-2 flex gap-4 text-sm">
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="disertai obat"
                                    @checked(old('medication_status', $dailyReport->medication_status) == 'disertai obat') class="form-radio text-indigo-600">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Disertai Obat</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="medication_status" value="tanpa obat"
                                    @checked(old('medication_status', $dailyReport->medication_status) == 'tanpa obat') class="form-radio text-indigo-600">
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
                        <input type="radio" name="condition" value="tenang" @checked(old('condition', $dailyReport->condition) == 'tenang')
                            class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Tenang</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="rewel" @checked(old('condition', $dailyReport->condition) == 'rewel')
                            class="form-radio text-indigo-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Rewel</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="condition" value="temper tantrum" @checked(old('condition', $dailyReport->condition) == 'temper tantrum')
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
                    @include('admin.daily-report.edit-daily.partials.baby-form')
                @elseif ($activityTransaction->service_id == 2)
                    @include('admin.daily-report.edit-daily.partials.children-form')
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
                        placeholder="Memuat saran stimulasi otomatis...">{{ old('stimulation_description', $dailyReport->stimulation_description) }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">*Terisi otomatis berdasarkan usia & item
                        MMDST (jika data berubah).</p>
                </div>

                <div>
                    <label for="notes"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan:</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Catatan tambahan untuk orang tua...">{{ old('notes', $dailyReport->notes) }}</textarea>
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
                            value="{{ old('teacher_name', $dailyReport->teacher_name) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- Canvas / Image --}}
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanda
                            Tangan</label>

                        <div
                            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 overflow-hidden text-center">
                            {{-- Jika TTD sudah ada, tampilkan gambarnya --}}
                            @if ($dailyReport->teacher_signature)
                                <div id="signature-preview-container" class="relative">
                                    <img src="{{ asset('storage/' . $dailyReport->teacher_signature) }}"
                                        alt="Tanda Tangan" class="h-40 mx-auto object-contain">
                                    <button type="button" id="change-signature-btn"
                                        class="absolute top-2 right-2 text-xs bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded transition border border-red-300">
                                        Hapus/Ganti
                                    </button>
                                </div>
                                <canvas id="teacher-signature-pad"
                                    class="hidden w-full h-40 cursor-crosshair touch-none"></canvas>
                            @else
                                <canvas id="teacher-signature-pad"
                                    class="block w-full h-40 cursor-crosshair touch-none"></canvas>
                            @endif

                            {{-- Tombol Hapus Canvas (Muncul jika Canvas Aktif) --}}
                            <button type="button" id="clear-sig-btn"
                                class="{{ $dailyReport->teacher_signature ? 'hidden' : '' }} absolute top-2 right-2 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded transition">
                                Hapus
                            </button>

                            <input type="hidden" name="teacher_signature" id="teacher-signature-input">
                            <input type="hidden" name="clear_signature" id="clear-signature-flag" value="0">
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-center" id="sig-helper-text">
                            {{ $dailyReport->teacher_signature ? 'Tanda tangan tersimpan.' : 'Silakan tanda tangan di kotak di atas.' }}
                        </p>
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
                    Simpan Perubahan
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
            const hasExistingSignature = {{ $dailyReport->teacher_signature ? 'true' : 'false' }};

            // 1. SIGNATURE PAD LOGIC
            const canvas = document.getElementById('teacher-signature-pad');
            const previewContainer = document.getElementById('signature-preview-container');
            const changeBtn = document.getElementById('change-signature-btn');
            const clearBtn = document.getElementById('clear-sig-btn');
            const sigInput = document.getElementById('teacher-signature-input');
            const clearFlag = document.getElementById('clear-signature-flag');
            const helperText = document.getElementById('sig-helper-text');

            let signaturePad = null;

            function initSignaturePad() {
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
                }
            }

            // Inisialisasi awal jika tidak ada gambar
            if (!hasExistingSignature) {
                initSignaturePad();
            }

            // Handle Tombol "Ganti/Hapus" pada gambar
            if (changeBtn) {
                changeBtn.addEventListener('click', function() {
                    // Sembunyikan preview gambar
                    previewContainer.classList.add('hidden');
                    // Tampilkan canvas
                    canvas.classList.remove('hidden');
                    // Tampilkan tombol clear canvas
                    clearBtn.classList.remove('hidden');
                    // Ubah text helper
                    helperText.textContent = "Silakan buat tanda tangan baru.";
                    // Set flag untuk hapus tanda tangan lama di backend
                    clearFlag.value = "1";

                    // Init pad baru
                    initSignaturePad();
                });
            }

            // Handle Tombol Clear Canvas
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    if (signaturePad) {
                        signaturePad.clear();
                    }
                });
            }

            // 2. SUBMIT LOGIC
            document.getElementById('submitFormButton').addEventListener('click', function(e) {
                e.preventDefault();

                // Cek apakah ada signature baru di canvas
                if (signaturePad && !signaturePad.isEmpty()) {
                    sigInput.value = signaturePad.toDataURL('image/png');
                }

                Swal.fire({
                    title: 'Simpan Perubahan?',
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
            // Init resize for existing content
            document.querySelectorAll('textarea').forEach(el => autoResizeTextarea(el));

            // 4. CEK ABSENSI (Keep existing logic just in case date changes)
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

            // 5. LOAD STIMULASI (Only if empty or date changes)
            function loadStimulation(txId, date) {
                const area = document.getElementById('stimulation_description');
                if (!area) return;

                // Jika sudah ada isinya (dari DB), jangan overwrite kecuali kosong
                if (area.value.trim() !== '' && area.value !== 'Memuat data MMDST...' && area.value !==
                    'Tidak ada saran stimulasi.') {
                    return;
                }

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

            // 6. LOAD MATERI (CHILDREN) - Untuk handle perubahan tanggal
            // Note: Pada Edit, data awal sudah di-load via PHP loop di partials.
            // Fungsi ini hanya dipanggil jika user MENGUBAH tanggal periode.
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
                        // Clear labels
                        document.getElementById('themeName1').innerHTML = '';
                        document.getElementById('themeName2').innerHTML = '';
                    });
            }

            function bindMaterialChange(selectId, targetId) {
                const sel = document.getElementById(selectId);
                const tgt = document.getElementById(targetId);
                if (!sel || !tgt) return;

                // Fungsi update text
                const updateText = () => {
                    const opt = sel.options[sel.selectedIndex];
                    const th = opt?.getAttribute('data-theme') || '';
                    const sub = opt?.getAttribute('data-sub') || '';
                    if (th || sub) {
                        tgt.innerHTML =
                            `<div class="mt-1 text-xs text-gray-500 bg-gray-50 p-1 rounded border"><strong>Tema:</strong> ${th} <br> <strong>Sub:</strong> ${sub}</div>`;
                    } else {
                        tgt.innerHTML = '';
                    }
                };

                sel.addEventListener('change', updateText);
                // Trigger sekali saat load agar label muncul (untuk selected option dari PHP)
                updateText();
            }

            // 7. KESEHATAN UI
            window.toggleKesehatanUI = function(isSick) {
                const el = document.getElementById('deskripsi_kesehatan');
                if (isSick) el.classList.remove('hidden');
                else el.classList.add('hidden');
            }

            // --- INIT ---
            checkAttendance(studentId, periodEl.value);

            if (serviceId === 2) {
                // Jangan loadSubthemes() saat init edit, karena akan me-reset pilihan yang tersimpan.
                // Hanya bind event change.
                bindMaterialChange('session1_material_id', 'themeName1');
                bindMaterialChange('session2_material_id', 'themeName2');
            }

            const checkedHealth = document.querySelector('input[name="health_status"]:checked');
            if (checkedHealth && checkedHealth.value === 'sakit') toggleKesehatanUI(true);

            // --- LISTENERS ---
            periodEl.addEventListener('change', function() {
                // Jika tanggal berubah, barulah kita reload logic-logis dinamis
                checkAttendance(studentId, this.value);
                // Force reload stimulation karena tanggal berubah (umur berubah)
                const area = document.getElementById('stimulation_description');
                if (area) area.value = ''; // Kosongkan dulu agar loadStimulation jalan
                loadStimulation(atId, this.value);

                if (serviceId === 2) loadSubthemes(this.value);
            });

            document.querySelectorAll('input[name="health_status"]').forEach(el => {
                el.addEventListener('change', () => toggleKesehatanUI(el.value === 'sakit'));
            });
        });

        function autoResize(elem) {
            elem.style.height = 'auto'; // Reset height
            elem.style.height = elem.scrollHeight + 'px'; // Set height sesuai konten
        }

        // Jalankan saat halaman selesai dimuat agar teks dari database langsung pas ukurannya
        document.addEventListener("DOMContentLoaded", function() {
            const textArea = document.getElementById('sickness_description');
            if (textArea) {
                autoResize(textArea);
            }
        });
    </script>
</x-app-layout>
