<x-app-layout>
    <x-slot:title>Buat Raport: {{ $student->student_name }}</x-slot:title>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Buat Raport Baru') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('development-reports.select-period', $student->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    {{-- EXTERNAL LIBRARIES --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Auto-resize textarea style */
        textarea {
            overflow: hidden;
            resize: none;
            min-height: 100px;
            transition: height 0.2s ease-out;
        }

        .tab-btn.active {
            color: #db2777;
            /* Pink-600 */
            border-bottom-width: 3px;
            border-color: #db2777;
            background-color: rgba(219, 39, 119, 0.05);
        }

        .tab-panel.hidden {
            display: none;
        }

        /* FIX TANDA TANGAN: Pastikan canvas punya dimensi block */
        .signature-container {
            position: relative;
            width: 100%;
            height: 180px;
            background-color: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
        }

        canvas.signature-canvas {
            display: block;
            width: 100%;
            height: 100%;
            touch-action: none;
        }

        .chart-btn {
            transition: all 0.2s;
        }

        .chart-btn:hover {
            transform: translateY(-1px);
        }
    </style>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('development-reports.store') }}" method="POST" id="reportForm">
                @csrf

                {{-- 1. INPUT HIDDEN WAJIB (DATA UTAMA & FIX ERROR SQL) --}}
                <input type="hidden" name="student_id" id="student_id" value="{{ $student->id }}">
                <input type="hidden" id="student_name" value="{{ $student->student_name }}">
                <input type="hidden" name="period_start_date" value="{{ $startDate }}">
                <input type="hidden" name="period_end_date" value="{{ $endDate }}">
                <input type="hidden" name="age_in_months" value="{{ $ageInMonthsNow }}">
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">
                <input type="hidden" name="mmdst_assessment_id" value="{{ $latestMmdst->id ?? '' }}">

                {{-- 2. INPUT HIDDEN UNTUK GAMBAR GRAFIK (DIISI JS) --}}
                <input type="hidden" name="chart_bbu_image" id="input_chart_bbu">
                <input type="hidden" name="chart_tbu_image" id="input_chart_tbu">
                <input type="hidden" name="chart_bbtb_image" id="input_chart_bbtb">
                <input type="hidden" name="chart_imtu_image" id="input_chart_imtu">

                {{-- INFO SISWA --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm border-b dark:border-gray-700 pb-4">
                        {{-- Profil --}}
                        <div class="flex items-start gap-3 border-r dark:border-gray-700 pr-4">
                            <div class="flex-shrink-0">
                                <img class="h-12 w-12 rounded-full object-cover border-2 border-pink-100"
                                    src="https://ui-avatars.com/api/?name={{ $student->student_name }}&background=random">
                            </div>
                            <div>
                                <strong class="text-[10px] text-gray-500 uppercase tracking-wider block mb-1">Identitas
                                    Siswa</strong>
                                <div class="font-bold text-gray-900 dark:text-white">{{ $student->student_name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->student_number ?? 'No ID' }}</div>
                                <span
                                    class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800">
                                    {{ $student->gender == 1 || $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </div>
                        </div>
                        {{-- Akademik --}}
                        <div class="border-r dark:border-gray-700 pr-4 pl-2">
                            <strong class="text-[10px] text-gray-500 uppercase tracking-wider block mb-1">Info
                                Akademik</strong>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $student->activityTransaction->service->service_name ?? 'Layanan -' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $student->activityTransaction->program->program_name ?? 'Program -' }}</p>
                        </div>
                        {{-- Usia --}}
                        <div class="border-r dark:border-gray-700 pr-4 pl-2">
                            <strong class="text-[10px] text-gray-500 uppercase tracking-wider block mb-1">Usia &
                                Lahir</strong>
                            @php
                                $dob = \Carbon\Carbon::parse($student->birth_date);
                                $now = \Carbon\Carbon::now();
                                $diff = $dob->diff($now);
                            @endphp
                            <div class="font-bold text-gray-900 dark:text-white text-lg">
                                {{ $diff->y }} Thn {{ $diff->m }} Bln
                            </div>
                            <div class="text-xs text-gray-500">{{ $dob->translatedFormat('d F Y') }}</div>
                        </div>
                        {{-- Periode --}}
                        <div class="pl-2">
                            <strong class="text-[10px] text-gray-500 uppercase tracking-wider block mb-1">Periode
                                Raport</strong>
                            <div class="flex flex-col gap-1">
                                <select name="semester"
                                    class="w-full text-xs font-bold border-gray-300 rounded text-pink-600 focus:ring-pink-500">
                                    <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Semester 1
                                        (Ganjil)</option>
                                    <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Semester 2
                                        (Genap)</option>
                                </select>
                                <input type="date" name="report_date" value="{{ date('Y-m-d') }}"
                                    class="w-full text-xs font-medium border-gray-300 rounded text-gray-600 focus:ring-pink-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABS --}}
                <div
                    class="mb-6 border-b border-gray-200 bg-white dark:bg-gray-800 rounded-t-xl overflow-hidden shadow-sm">
                    <ul class="flex flex-wrap text-xs font-bold uppercase tracking-wider text-center" id="myTab">
                        <li class="flex-1"><button type="button" class="w-full p-4 tab-btn active"
                                data-target="#kms-panel"><i class="fas fa-chart-line mr-2"></i>1. Grafik
                                Pertumbuhan</button></li>
                        <li class="flex-1"><button type="button" class="w-full p-4 tab-btn"
                                data-target="#mmdst-panel"><i class="fas fa-brain mr-2"></i>2. Perkembangan
                                MMDST</button></li>
                        <li class="flex-1"><button type="button" class="w-full p-4 tab-btn"
                                data-target="#health-panel"><i class="fas fa-notes-medical mr-2"></i>3. Fisik &
                                Kesehatan</button></li>
                        <li class="flex-1"><button type="button" class="w-full p-4 tab-btn" data-target="#legal-panel"
                                id="legal-tab-trigger"><i class="fas fa-file-signature mr-2"></i>4. Pengesahan</button>
                        </li>
                    </ul>
                </div>

                <div id="tabContent">
                    {{-- 1. PANEL GRAFIK --}}
                    <div class="tab-panel bg-white dark:bg-gray-900 shadow-md sm:rounded-lg p-6 border border-gray-100 dark:border-gray-700"
                        id="kms-panel">
                        <div class="border-b dark:border-gray-700 pb-4 mb-4">
                            <h3 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Pilih Jenis Grafik
                            </h3>
                            <div id="chart-buttons" class="flex flex-wrap gap-2"></div>
                        </div>
                        <div>
                            <h3 id="chart-title"
                                class="font-semibold text-lg text-gray-800 dark:text-white mb-4 text-center"></h3>
                            <div class="relative w-full h-80 sm:h-96 md:h-[32rem]">
                                <canvas id="kmsChart"></canvas>
                            </div>
                        </div>
                        <div class="mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-bold text-gray-500 uppercase">Analisis Grafik (AI)</label>
                                <button type="button"
                                    onclick="generateAI('Pertumbuhan Fisik', 'growth_analysis_desc')"
                                    class="px-3 py-1 bg-pink-50 text-pink-600 rounded text-[10px] font-bold border border-pink-200 hover:bg-pink-100 transition">✨
                                    Generate AI</button>
                            </div>
                            <textarea name="growth_analysis_desc" id="growth_analysis_desc" oninput="autoResize(this)"
                                class="w-full text-sm border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
                                placeholder="Narasi analisis akan muncul di sini..."></textarea>
                        </div>
                    </div>

                    {{-- 2. PANEL MMDST --}}
                    <div class="tab-panel hidden space-y-6" id="mmdst-panel">
                        <div
                            class="bg-indigo-50 border border-indigo-100 p-4 rounded-lg flex justify-between items-center mb-4">
                            <div>
                                <p class="text-xs font-bold text-indigo-800 uppercase">Diagnosa Global MMDST</p>
                                <p class="text-lg font-bold text-indigo-600">
                                    {{ $latestMmdst->overall_result ?? 'BELUM ADA DATA' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-indigo-500 uppercase">Tanggal Tes</p>
                                <p class="text-sm font-bold text-indigo-700">
                                    {{ $latestMmdst ? \Carbon\Carbon::parse($latestMmdst->assessment_date)->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <input type="hidden" name="mmdst_final_result"
                                value="{{ $latestMmdst->overall_result ?? 'UNTESTABLE' }}">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ([['key' => 'personal_social', 'label' => 'Personal Sosial', 'db_key' => 'personal_social'], ['key' => 'fine_motor', 'label' => 'Motorik Halus', 'db_key' => 'fine_motor'], ['key' => 'language', 'label' => 'Bahasa', 'db_key' => 'language'], ['key' => 'gross_motor', 'label' => 'Motorik Kasar', 'db_key' => 'gross_motor']] as $asp)
                                @php $val = $mmdstResults[$asp['db_key']] ?? 'UNTESTABLE'; @endphp
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-bold text-gray-800 text-sm uppercase">{{ $asp['label'] }}</h4>
                                        <select name="mmdst_{{ $asp['key'] }}_result" id="res_{{ $asp['key'] }}"
                                            class="text-[10px] font-bold rounded-full py-0.5 border-gray-300 uppercase bg-gray-50 focus:ring-pink-500">
                                            <option value="NORMAL" {{ $val == 'NORMAL' ? 'selected' : '' }}>NORMAL
                                            </option>
                                            <option value="SUSPECT"
                                                {{ $val == 'SUSPECT' || $val == 'QUESTIONABLE' || $val == 'CAUTION' || $val == 'ABNORMAL' ? 'selected' : '' }}>
                                                SUSPECT/CAUTION</option>
                                            <option value="UNTESTABLE"
                                                {{ $val == 'UNTESTABLE' || $val == 'REFUSAL' ? 'selected' : '' }}>
                                                UNTESTABLE</option>
                                        </select>
                                    </div>
                                    <div class="relative">
                                        <textarea name="{{ $asp['key'] }}_desc" id="desc_{{ $asp['key'] }}" oninput="autoResize(this)"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-pink-500 focus:border-pink-500"
                                            placeholder="Narasi perkembangan..."></textarea>
                                        <button type="button"
                                            onclick="generateAI('{{ $asp['label'] }}', 'desc_{{ $asp['key'] }}', 'res_{{ $asp['key'] }}')"
                                            class="absolute bottom-2 right-2 text-pink-600 bg-white border border-pink-100 px-2 py-0.5 rounded text-[10px] font-bold shadow-sm hover:bg-pink-50 transition">✨
                                            AI</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. PANEL FISIK & ABSENSI --}}
                    <div class="tab-panel hidden grid grid-cols-1 lg:grid-cols-2 gap-8" id="health-panel">
                        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 uppercase text-xs tracking-widest">
                                Snapshot Fisik</h3>
                            @if (isset($prefillPhysical['date']) && $prefillPhysical['date'])
                                <div
                                    class="mb-4 flex items-center gap-2 text-xs bg-green-50 text-green-700 p-2 rounded border border-green-100">
                                    <i class="fas fa-calendar-check"></i> <span>Data pengukuran tgl:
                                        <strong>{{ $prefillPhysical['date'] }}</strong></span>
                                </div>
                            @else
                                <div
                                    class="mb-4 flex items-center gap-2 text-xs bg-yellow-50 text-yellow-700 p-2 rounded border border-yellow-100">
                                    <i class="fas fa-exclamation-triangle"></i> <span>Belum ada data pengukuran.
                                        Silakan input manual.</span>
                                </div>
                            @endif
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div><label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Berat
                                        (kg)</label><input type="number" step="0.01" name="weight_kg"
                                        id="weight_input" value="{{ $prefillPhysical['weight'] }}"
                                        class="w-full rounded border-gray-300 font-bold text-gray-800 text-lg"></div>
                                <div><label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Tinggi
                                        (cm)</label><input type="number" step="0.01" name="height_cm"
                                        id="height_input" value="{{ $prefillPhysical['height'] }}"
                                        class="w-full rounded border-gray-300 font-bold text-gray-800 text-lg"></div>
                                <div><label class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Lingkar
                                        Kepala</label><input type="number" step="0.01"
                                        name="head_circumference_cm" value="{{ $prefillPhysical['head'] }}"
                                        class="w-full rounded border-gray-300 font-bold text-gray-800 text-lg"></div>
                                <div><label
                                        class="text-[10px] font-bold text-gray-400 uppercase block mb-1">BMI</label><input
                                        type="number" step="0.01" name="bmi"
                                        value="{{ $prefillPhysical['bmi'] }}"
                                        class="w-full rounded border-gray-300 bg-gray-50 text-gray-500 font-bold text-lg"
                                        readonly></div>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 uppercase text-xs tracking-widest">
                                Kesehatan</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($healthItems as $item)
                                    <div><label
                                            class="text-[10px] font-bold text-gray-500 uppercase block mb-1">{{ $item }}</label><select
                                            name="health[{{ $item }}]"
                                            class="w-full text-xs font-semibold rounded border-gray-300">
                                            <option value="Baik">Baik</option>
                                            <option value="Cukup">Cukup</option>
                                            <option value="Perlu Perhatian">Perlu Perhatian</option>
                                            <option value="Dalam Perawatan">Dalam Perawatan</option>
                                        </select></div>
                                @endforeach
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                            <h3
                                class="font-bold text-gray-800 mb-4 border-b pb-2 uppercase text-xs tracking-widest text-center">
                                Rekap Absensi</h3>
                            <div class="grid grid-cols-4 gap-2 mb-6">
                                @php $attendMap = ['Hadir' => 'present', 'Sakit' => 'sick', 'Izin' => 'permission', 'Alpha' => 'alpha']; @endphp
                                @foreach ($attendanceSummary as $key => $val)
                                    <div
                                        class="bg-gray-50 p-2 rounded text-center border border-gray-200 hover:border-pink-300 transition">
                                        <span
                                            class="block text-[9px] font-bold text-gray-400 uppercase">{{ $key }}</span>
                                        <span
                                            class="block text-2xl font-black text-pink-600">{{ $val }}</span>
                                        <input type="hidden" name="attendance_{{ $attendMap[$key] ?? 'alpha' }}"
                                            value="{{ $val }}">
                                    </div>
                                @endforeach
                            </div>
                            <label class="text-[10px] font-bold text-gray-400 block mb-1 uppercase">Catatan
                                Guru</label>
                            <textarea name="teacher_notes" oninput="autoResize(this)" class="w-full text-sm border-gray-300 rounded mb-3"
                                placeholder="Catatan perkembangan..."></textarea>
                            <label class="text-[10px] font-bold text-gray-400 block mb-1 uppercase">Rekomendasi</label>
                            <textarea name="teacher_recommendations" oninput="autoResize(this)" class="w-full text-sm border-gray-300 rounded"
                                placeholder="Saran untuk orang tua..."></textarea>
                        </div>
                    </div>

                    {{-- 4. PENGESAHAN --}}
                    <div class="tab-panel hidden bg-white rounded-xl p-8 shadow-sm border border-gray-200"
                        id="panel-legal">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach ([['id' => 'sig_ortu', 'input' => 'parent_signature', 'name' => 'parent_name', 'label' => 'Orang Tua / Wali', 'def' => ''], ['id' => 'sig_guru', 'input' => 'teacher_signature', 'name' => 'teacher_name', 'label' => 'Wali Kelas', 'def' => $defaultTeacherName], ['id' => 'sig_konsultan', 'input' => 'consultant_signature', 'name' => 'consultant_name', 'label' => 'Konsultan', 'def' => $defaultConsultantName], ['id' => 'sig_kepsek', 'input' => 'principal_signature', 'name' => 'principal_name', 'label' => 'Kepala Sekolah', 'def' => $defaultPrincipalName]] as $sig)
                                <div class="flex flex-col items-center w-full">
                                    <label
                                        class="font-bold text-xs uppercase text-gray-500 mb-2 tracking-wider">{{ $sig['label'] }}</label>
                                    <div class="signature-container group hover:border-pink-400 transition">
                                        <canvas id="{{ $sig['id'] }}" class="signature-canvas"></canvas>
                                        <button type="button" onclick="clearPad('{{ $sig['id'] }}')"
                                            class="absolute top-2 right-2 text-gray-400 hover:text-red-500 bg-white p-1.5 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition-all z-10"><i
                                                class="fas fa-eraser"></i></button>
                                    </div>
                                    <input type="hidden" name="{{ $sig['input'] }}"
                                        id="input_{{ $sig['id'] }}">
                                    <input type="text" name="{{ $sig['name'] }}" value="{{ $sig['def'] }}"
                                        placeholder="Nama Terang"
                                        class="w-full mt-3 text-xs font-bold text-center border-gray-300 rounded-md focus:ring-pink-500">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4 pb-20">
                    <button type="button" onclick="window.history.back()"
                        class="px-6 py-3 bg-white border border-gray-300 rounded-lg font-bold text-gray-600 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" onclick="submitFinal(event)"
                        class="px-10 py-3 bg-gradient-to-r from-pink-600 to-purple-600 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">Simpan
                        Raport</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT: JANGAN UBAH BAGIAN CHART --}}
    <script>
        // 1. TABS LOGIC
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
                this.classList.add('active');
                document.querySelector(this.dataset.target).classList.remove('hidden');

                if (this.dataset.target === '#legal-panel') {
                    setTimeout(() => {
                        resizeAllCanvases();
                    }, 200);
                }
            });
        });

        // 2. SIGNATURE PAD INIT & RESIZE
        const pads = {};
        const sigIds = ['sig_ortu', 'sig_guru', 'sig_konsultan', 'sig_kepsek'];

        function initSignatures() {
            sigIds.forEach(id => {
                const canvas = document.getElementById(id);
                if (canvas) {
                    pads[id] = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(255,255,255,0)'
                    });
                }
            });
        }

        window.clearPad = function(id) {
            if (pads[id]) pads[id].clear();
        }

        function resizeAllCanvases() {
            sigIds.forEach(id => {
                const canvas = document.getElementById(id);
                if (canvas && canvas.parentElement.offsetWidth > 0) {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.parentElement.offsetWidth * ratio;
                    canvas.height = canvas.parentElement.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                }
            });
        }

        // 3. CHART LOGIC (100% SAMA SEPERTI REFERENSI)
        const studentDataPoints = @json($chartData ?? []);
        const allStandards = @json($allStandardCurves ?? []);
        const studentBirthDate = '{{ $student->birth_date }}';
        const isMale = {{ $student->gender == 1 || $student->gender == 'male' ? 'true' : 'false' }};
        const studentPointColor = isMale ? 'rgba(59, 130, 246, 1)' : 'rgba(236, 72, 153, 1)';
        const canvas = document.getElementById('kmsChart');
        const chartTitleEl = document.getElementById('chart-title');
        const buttonsContainer = document.getElementById('chart-buttons');
        let currentChart = null;

        if (!studentDataPoints || studentDataPoints.length === 0) {
            chartTitleEl.textContent = 'Tidak ada data pengukuran untuk ditampilkan.';
        }

        function getFullAge(birthDateStr, measurementDateStr) {
            const birthDate = new Date(birthDateStr);
            const measurementDate = new Date(measurementDateStr);
            if (isNaN(birthDate.getTime()) || isNaN(measurementDate.getTime())) return null;
            let years = measurementDate.getFullYear() - birthDate.getFullYear();
            let months = measurementDate.getMonth() - birthDate.getMonth();
            let days = measurementDate.getDate() - birthDate.getDate();
            if (days < 0) {
                months--;
                days += new Date(measurementDate.getFullYear(), measurementDate.getMonth(), 0).getDate();
            }
            if (months < 0) {
                years--;
                months += 12;
            }
            return {
                years,
                months
            };
        }

        const createLineBreaks = (points, xKey, yKey) => {
            if (!points || points.length < 2) return points;
            const processed = [];
            for (let i = 0; i < points.length; i++) {
                let pCopy = {
                    ...points[i]
                };
                if (i > 0) {
                    const diff = points[i][xKey] - points[i - 1][xKey];
                    if (diff > 4) {
                        let breakPoint = {
                            ...points[i]
                        };
                        breakPoint[yKey] = NaN;
                        processed.push(breakPoint);
                    }
                }
                processed.push(pCopy);
            }
            return processed;
        };

        function renderChart(config) {
            if (currentChart) currentChart.destroy();
            chartTitleEl.textContent = config.title;

            const mapStd = (data) => data ? data : [];
            // Gunakan warna yang sama persis dengan generator gambar
            const boundaryLine = {
                borderWidth: 1,
                pointRadius: 0,
                tension: 0.4
            };

            currentChart = new Chart(canvas, {
                type: 'line',
                data: {
                    datasets: [{
                            ...boundaryLine,
                            label: '+3 SD',
                            data: mapStd(config.standard?.plus_3_sd),
                            borderColor: 'rgba(150, 150, 150, 0.4)'
                        },
                        {
                            ...boundaryLine,
                            label: '+2 SD',
                            data: mapStd(config.standard?.plus_2_sd),
                            borderColor: 'rgba(239, 68, 68, 0.4)',
                            fill: '+1',
                            backgroundColor: 'rgba(239, 68, 68, 0.05)'
                        },
                        {
                            ...boundaryLine,
                            label: '+1 SD',
                            data: mapStd(config.standard?.plus_1_sd),
                            borderColor: 'rgba(234, 179, 8, 0.4)',
                            fill: '+1',
                            backgroundColor: 'rgba(234, 179, 8, 0.1)'
                        },
                        {
                            ...boundaryLine,
                            label: 'Median',
                            data: mapStd(config.standard?.median),
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2
                        },
                        {
                            ...boundaryLine,
                            label: '-1 SD',
                            data: mapStd(config.standard?.minus_1_sd),
                            borderColor: 'rgba(34, 197, 94, 0.4)'
                        },
                        {
                            ...boundaryLine,
                            label: '-2 SD',
                            data: mapStd(config.standard?.minus_2_sd),
                            borderColor: 'rgba(34, 197, 94, 0.4)',
                            fill: '-1',
                            backgroundColor: 'rgba(34, 197, 94, 0.15)'
                        },
                        {
                            ...boundaryLine,
                            label: '-3 SD',
                            data: mapStd(config.standard?.minus_3_sd),
                            borderColor: 'rgba(239, 68, 68, 0.4)',
                            fill: '-1',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)'
                        },
                        {
                            label: 'Pengukuran Anak',
                            data: config.studentPoints,
                            borderColor: studentPointColor,
                            backgroundColor: studentPointColor,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            showLine: true,
                            spanGaps: false,
                            tension: 0.1,
                            borderWidth: 3,
                            parsing: {
                                xAxisKey: 'x',
                                yAxisKey: 'y'
                            }
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            title: {
                                display: true,
                                text: config.xAxisLabel,
                                font: {
                                    size: 14
                                }
                            },
                            min: config.standard?.min,
                            max: config.standard?.max,
                            ticks: {
                                autoSkip: false
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: config.yAxisLabel,
                                font: {
                                    size: 14
                                }
                            },
                            beginAtZero: false,
                            ticks: {
                                autoSkip: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            if (window.innerWidth < 768) delete currentChart.options.scales.y.ticks.stepSize;
            else {
                currentChart.options.scales.x.ticks.stepSize = 1;
                currentChart.options.scales.y.ticks.stepSize = 1;
            }
            currentChart.update();
        }

        const chartConfigs = {
            'BB/U': {
                title: 'Berat Badan menurut Umur',
                yAxisLabel: 'Berat (kg)',
                xKey: 'age',
                yKey: 'weight',
                standard: allStandards['BB/U'],
                dataSource: studentDataPoints.filter(p => p)
            },
            'PB/U': {
                title: 'Panjang Badan menurut Umur',
                yAxisLabel: 'Panjang (cm)',
                xKey: 'age',
                yKey: 'height',
                standard: allStandards['PB/U'],
                dataSource: studentDataPoints.filter(p => p.age < 24)
            },
            'TB/U': {
                title: 'Tinggi Badan menurut Umur',
                yAxisLabel: 'Tinggi (cm)',
                xKey: 'age',
                yKey: 'height',
                standard: allStandards['TB/U'],
                dataSource: studentDataPoints.filter(p => p.age >= 24)
            },
            'IMT/U': {
                title: 'IMT menurut Umur',
                yAxisLabel: 'IMT',
                xKey: 'age',
                yKey: 'bmi',
                standard: allStandards['IMT/U'],
                dataSource: studentDataPoints.filter(p => p)
            },
            'PB/BB': {
                title: 'BB / Panjang Badan',
                yAxisLabel: 'Berat (kg)',
                xKey: 'height',
                yKey: 'weight',
                standard: allStandards['PB/BB'],
                dataSource: studentDataPoints.filter(p => p.age < 24)
            },
            'TB/BB': {
                title: 'BB / Tinggi Badan',
                yAxisLabel: 'Berat (kg)',
                xKey: 'height',
                yKey: 'weight',
                standard: allStandards['TB/BB'],
                dataSource: studentDataPoints.filter(p => p.age >= 24)
            }
        };

        const allPossibleButtons = ['BB/U', 'PB/U', 'TB/U', 'IMT/U', 'PB/BB', 'TB/BB'];
        allPossibleButtons.forEach(key => {
            if (chartConfigs[key] && chartConfigs[key].standard) {
                const button = document.createElement('button');
                button.textContent = key;
                button.type = 'button';
                button.className =
                    'px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 chart-btn mr-2 mb-2 transition-all';
                button.dataset.chartKey = key;
                button.onclick = function() {
                    document.querySelectorAll('.chart-btn').forEach(btn => {
                        btn.classList.remove('bg-pink-600', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('bg-pink-600', 'text-white');

                    const cfg = chartConfigs[key];
                    const xLabel = cfg.standard.x_axis_key === 'age_months' ? 'Umur (bulan)' : (cfg.standard
                        .x_axis_key === 'body_length' ? 'Panjang (cm)' : 'Tinggi (cm)');
                    const points = cfg.dataSource.map(d => ({
                        ...d,
                        x: d[cfg.xKey],
                        y: d[cfg.yKey]
                    }));
                    renderChart({
                        ...cfg,
                        xAxisLabel: xLabel,
                        chartKey: key,
                        studentPoints: createLineBreaks(points, 'x', 'y')
                    });
                };
                buttonsContainer.appendChild(button);
            }
        });

        if (buttonsContainer.firstChild) buttonsContainer.firstChild.click();

        // 4. AI GENERATOR
        async function generateAI(category, targetId, resId = null) {
            const student = document.getElementById('student_name').value;
            const res = resId ? document.getElementById(resId).value : 'Data Pertumbuhan';
            const area = document.getElementById(targetId);
            const token = document.querySelector('input[name="_token"]').value;
            area.placeholder = "AI sedang menyusun...";
            try {
                const response = await fetch("{{ route('development-reports.generate-ai') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        student_name: student,
                        category: category,
                        result_summary: res
                    })
                });
                const data = await response.json();
                if (data.status === 'success') {
                    area.value = data.text;
                    autoResize(area);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Berhasil',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'AI Error'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal koneksi ke server AI'
                });
            }
        }

        // 5. HELPER RESIZE
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }
        document.querySelectorAll('textarea').forEach(tx => autoResize(tx));

        // 6. GENERATE CHART IMAGES
        async function generateAllChartsImages() {
            const lastPoint = studentDataPoints[studentDataPoints.length - 1];
            const lastAge = lastPoint ? lastPoint.age : 0;
            const isBaby = lastAge < 24;

            const targets = [{
                    inputId: 'input_chart_bbu',
                    chartKey: 'BB/U'
                },
                {
                    inputId: 'input_chart_imtu',
                    chartKey: 'IMT/U'
                },
                {
                    inputId: 'input_chart_tbu',
                    chartKey: isBaby ? 'PB/U' : 'TB/U'
                },
                {
                    inputId: 'input_chart_bbtb',
                    chartKey: isBaby ? 'PB/BB' : 'TB/BB'
                }
            ];

            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = 1200; // High Res
            tempCanvas.height = 600;
            const ctx = tempCanvas.getContext('2d');

            for (let target of targets) {
                const config = chartConfigs[target.chartKey];
                if (!config) continue;

                // Reset Canvas
                ctx.clearRect(0, 0, tempCanvas.width, tempCanvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

                const xLabel = config.standard.x_axis_key === 'age_months' ? 'Umur (bulan)' : (config.standard
                    .x_axis_key === 'body_length' ? 'Panjang (cm)' : 'Tinggi (cm)');
                const mapStd = (data) => data ? data : [];
                const points = config.dataSource.map(d => ({
                    ...d,
                    x: d[config.xKey],
                    y: d[config.yKey]
                }));

                // RENDER CHART FOR IMAGE (Persis seperti tampilan web)
                const tempChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        datasets: [
                            // SD LINES: Warna disesuaikan agar sama persis dengan tampilan web
                            {
                                label: '+3 SD',
                                data: mapStd(config.standard?.plus_3_sd),
                                borderColor: 'rgba(150, 150, 150, 0.4)',
                                borderWidth: 1,
                                pointRadius: 0,
                                tension: 0.4
                            },
                            {
                                label: '+2 SD',
                                data: mapStd(config.standard?.plus_2_sd),
                                borderColor: 'rgba(239, 68, 68, 0.4)',
                                fill: '+1',
                                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                borderWidth: 1,
                                pointRadius: 0,
                                tension: 0.4
                            },
                            {
                                label: '+1 SD',
                                data: mapStd(config.standard?.plus_1_sd),
                                borderColor: 'rgba(234, 179, 8, 0.4)',
                                fill: '+1',
                                backgroundColor: 'rgba(234, 179, 8, 0.1)',
                                borderWidth: 1,
                                pointRadius: 0,
                                tension: 0.4
                            },
                            {
                                label: 'Median',
                                data: mapStd(config.standard?.median),
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 2,
                                pointRadius: 0,
                                tension: 0.4
                            },
                            {
                                label: '-1 SD',
                                data: mapStd(config.standard?.minus_1_sd),
                                borderColor: 'rgba(34, 197, 94, 0.4)',
                                borderWidth: 1,
                                pointRadius: 0,
                                tension: 0.4
                            },
                            {
                                label: '-2 SD',
                                data: mapStd(config.standard?.minus_2_sd),
                                borderColor: 'rgba(34, 197, 94, 0.4)',
                                fill: '-1',
                                backgroundColor: 'rgba(34, 197, 94, 0.15)',
                                borderWidth: 1,
                                pointRadius: 0,
                                tension: 0.4
                            },
                            {
                                label: '-3 SD',
                                data: mapStd(config.standard?.minus_3_sd),
                                borderColor: 'rgba(239, 68, 68, 0.4)',
                                fill: '-1',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 1,
                                pointRadius: 0,
                                tension: 0.4
                            },

                            // STUDENT DATA
                            {
                                label: 'Anak',
                                data: createLineBreaks(points, 'x', 'y'),
                                borderColor: studentPointColor,
                                backgroundColor: studentPointColor,
                                pointRadius: 5, // Sedikit diperbesar agar jelas di PDF
                                pointHoverRadius: 5,
                                borderWidth: 3,
                                showLine: true,
                                spanGaps: false,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: false,
                        animation: false,
                        devicePixelRatio: 2,
                        scales: {
                            x: {
                                type: 'linear',
                                title: {
                                    display: true,
                                    text: xLabel,
                                    font: {
                                        size: 18,
                                        weight: 'bold'
                                    }
                                },
                                min: config.standard?.min,
                                max: config.standard?.max,
                                ticks: {
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: config.yAxisLabel,
                                    font: {
                                        size: 18,
                                        weight: 'bold'
                                    }
                                },
                                beginAtZero: false,
                                ticks: {
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                document.getElementById(target.inputId).value = tempCanvas.toDataURL('image/png');
                tempChart.destroy();
            }
        }

        // 7. SUBMIT
        function submitFinal(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang memproses grafik untuk laporan. Mohon tunggu sejenak.',
                allowOutsideClick: false,
                didOpen: async () => {
                    Swal.showLoading();

                    // 1. Tanda Tangan
                    sigIds.forEach(id => {
                        if (pads[id] && !pads[id].isEmpty()) document.getElementById('input_' + id)
                            .value = pads[id].toDataURL();
                    });

                    // 2. Generate Gambar Grafik
                    await new Promise(r => setTimeout(r, 100)); // Delay agar UI tidak freeze
                    await generateAllChartsImages(); // <--- Generate gambar grafik

                    // 3. Submit
                    document.getElementById('reportForm').submit();
                }
            });
        }

        // INIT
        window.onload = () => {
            initSignatures();
            document.querySelectorAll('textarea').forEach(tx => autoResize(tx));
        };

        // Tab Listener
        document.getElementById('legal-tab-trigger').addEventListener('click', function() {
            setTimeout(resizeAllCanvases, 200);
        });
    </script>
</x-app-layout>
