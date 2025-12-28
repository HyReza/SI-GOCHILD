<x-app-layout>
    <x-slot:title>Tambah Penilaian MMDST</x-slot:title>

    {{-- Style Khusus --}}
    <style>
        /* Textarea Auto-Expand */
        textarea.auto-expand {
            min-height: 80px;
            overflow-y: hidden;
            resize: none;
            transition: height 0.1s ease;
        }

        /* Transisi halus */
        tr.param-row {
            transition: background-color 0.2s;
        }

        /* Baris TIDAK Dicentang (Disabled) - Tetap Terbaca Jelas */
        tr.row-disabled {
            background-color: #f9fafb;
            /* gray-50 */
            color: #4b5563;
            /* gray-600 */
        }

        .dark tr.row-disabled {
            background-color: #1f2937;
            /* gray-800 */
            color: #9ca3af;
        }

        /* Baris AKTIF (Dicentang) */
        tr.row-active {
            background-color: #ffffff;
            color: #111827;
        }

        .dark tr.row-active {
            background-color: #111827;
            /* gray-900 */
            color: #f9fafb;
        }
    </style>

    {{-- SweetAlert Error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json($errors->first())
                });
            });
        </script>
    @endif

    @php
        // --- SETUP DATA ---
        $selectedStudent = $selectedStudent ?? ($student ?? null);
        $studentIsNormal = isset($studentIsNormal) ? (bool) $studentIsNormal : true;
        $ageInDays = isset($ageInDays) ? (int) $ageInDays : 0;
        $filtered_categories = $filtered_categories ?? collect();
        $previousMap = $previousMap ?? collect();
        $assessmentDate = $assessmentDate ?? now()->toDateString();
        $todayISO = now()->toDateString();

        // --- LOGIKA BUCKET USIA ---
        function bucketOf($age, $p25, $p75, $p100)
        {
            $p25 = (int) $p25;
            $p75 = (int) $p75;
            $p100 = (int) $p100;

            if ($age < $p25) {
                return 'NOT_YET';
            }
            if ($age > $p100) {
                return 'OVERDUE';
            }
            if ($age >= $p75 && $age <= $p100) {
                return 'CRITICAL';
            }
            if ($age == $p25) {
                return 'AT_LINE';
            }
            return 'IN_WINDOW';
        }

        // Konfigurasi Tampilan
        $bucketConfig = [
            'OVERDUE' => ['label' => 'Lewat Usia', 'class' => 'bg-red-100 text-red-700'],
            'AT_LINE' => ['label' => 'Di Garis Usia', 'class' => 'bg-blue-600 text-white font-bold'],
            'IN_WINDOW' => ['label' => 'Rentang Usia', 'class' => 'bg-blue-100 text-blue-700'],
            'CRITICAL' => [
                'label' => 'Zona Kritis',
                'class' => 'bg-orange-100 text-orange-800 font-bold border border-orange-200',
            ],
            'NOT_YET' => ['label' => 'Belum Waktunya', 'class' => 'bg-gray-200 text-gray-700'],
        ];
    @endphp

    <form id="assessment-form" action="{{ route('mmdst-assessments.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- === DATA PENILAIAN === --}}
        <div class="p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <h2 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Data Penilaian</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Identitas Siswa --}}
                <div class="md:col-span-1">
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">Siswa</label>
                    <div class="h-10 flex items-center px-3 border rounded-lg dark:bg-gray-800 dark:border-gray-700">
                        <span
                            class="font-medium text-gray-900 dark:text-gray-100">{{ $selectedStudent?->student_name ?? '—' }}</span>
                    </div>
                    <input type="hidden" name="student_id" id="student_id" value="{{ $selectedStudent?->id }}">
                    @if ($selectedStudent)
                        <div class="text-[11px] text-gray-500 mt-1">
                            NIS: {{ $selectedStudent->student_number ?? '-' }} •
                            Tgl Lahir:
                            {{ $selectedStudent->birth_date ? \Illuminate\Support\Carbon::parse($selectedStudent->birth_date)->format('d M Y') : '-' }}
                        </div>
                    @endif
                    <p class="text-[11px] text-gray-500 mt-1">
                        Status: <b id="student-normal-badge">{{ $studentIsNormal ? 'Normal' : 'Kebutuhan Khusus' }}</b>
                    </p>
                </div>

                {{-- Tanggal --}}
                <div class="md:col-span-1">
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">Tanggal Penilaian</label>
                    <input type="date" name="assessment_date" id="assessment_date"
                        value="{{ old('assessment_date', $todayISO, $assessmentDate) }}"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700" required>
                    <p class="text-[11px] text-gray-500 mt-1">Usia: <b id="age-label">{{ $ageInDays }}</b> hari</p>
                </div>

                {{-- Auto Summary Toggle --}}
                <div class="md:col-span-1 flex items-end">
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                        <input type="checkbox" id="auto-summary-toggle" checked
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span>Auto ringkas (hanya jika catatan diisi)</span>
                    </label>
                </div>

                {{-- Catatan Utama --}}
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">Catatan Utama</label>
                    <textarea name="notes" id="main-notes" rows="3"
                        class="w-full border rounded-lg px-3 py-2 dark:bg-gray-900 dark:border-gray-700 auto-expand">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- === LEGENDA === --}}
        <div class="p-5 bg-white dark:bg-gray-900 rounded-md shadow text-xs">
            <h3
                class="font-bold text-gray-800 dark:text-gray-100 mb-3 uppercase tracking-wide border-b pb-2 dark:border-gray-700">
                Panduan & Legenda</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kolom Kiri: Hasil Tes --}}
                <div class="space-y-3">
                    <div class="font-semibold text-gray-600 dark:text-gray-400 mb-1">Hasil Penilaian (Result):</div>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-green-100 text-green-700 border border-green-200 font-bold min-w-[30px] text-center">P</span>
                            <span class="text-gray-600 dark:text-gray-300"><b>Lulus (Pass)</b>: Anak mampu melakukan
                                item tes.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-red-100 text-red-700 border border-red-200 font-bold min-w-[30px] text-center">F</span>
                            <span class="text-gray-600 dark:text-gray-300"><b>Gagal (Fail)</b>: Anak mencoba namun belum
                                berhasil.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700 border border-yellow-200 font-bold min-w-[30px] text-center">R</span>
                            <span class="text-gray-600 dark:text-gray-300"><b>Menolak (Refusal)</b>: Anak tidak mau
                                mencoba (uji ulang nanti).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-gray-200 text-gray-700 border border-gray-300 font-bold min-w-[30px] text-center">OP</span>
                            <span class="text-gray-600 dark:text-gray-300"><b>Belum Ada Kesempatan</b>: Tidak ada
                                alat/situasi tidak memungkinkan.</span>
                        </li>
                    </ul>
                </div>

                {{-- Kolom Kanan: Status Usia --}}
                <div class="space-y-3">
                    <div class="font-semibold text-gray-600 dark:text-gray-400 mb-1">Indikator Posisi Usia:</div>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-blue-600 text-white font-bold w-24 text-center text-[10px]">Di
                                Garis Usia</span>
                            <span class="text-gray-600 dark:text-gray-300">Usia anak tepat sama dengan batas awal
                                (25%).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 border border-blue-200 w-24 text-center text-[10px]">Rentang
                                Usia</span>
                            <span class="text-gray-600 dark:text-gray-300">Usia anak di antara 25% - 75%. Fase normal
                                belajar.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-orange-100 text-orange-800 border border-orange-300 font-bold w-24 text-center text-[10px]">Zona
                                Kritis</span>
                            <span class="text-gray-600 dark:text-gray-300">Usia 75% - 100%. <span
                                    class="text-red-600 font-bold">Waspada jika Gagal (F).</span></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="px-2 py-0.5 rounded bg-red-100 text-red-700 border border-red-200 w-24 text-center text-[10px]">Lewat
                                Usia</span>
                            <span class="text-gray-600 dark:text-gray-300">Usia > 100%. Sudah seharusnya bisa
                                (Keterlambatan).</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- === FILTER === --}}
        <div class="flex items-center justify-between">
            <div class="flex gap-2">
                <input id="filter-input" type="text" placeholder="Cari parameter..."
                    class="border rounded-lg px-3 h-9 dark:bg-gray-900 dark:border-gray-700 w-64 text-sm">
            </div>
            <div class="text-xs text-gray-500">
                {{ $studentIsNormal ? 'Mode: Filter by usia (Normal)' : 'Mode: Tampilkan semua (KH)' }}
            </div>
        </div>

        {{-- === TABEL ITEM (LOOP) === --}}
        @php $rowIndex = 0; @endphp
        <div class="space-y-6" id="param-container">
            @foreach ($filtered_categories as $categoryName => $params)
                @php $slug = \Illuminate\Support\Str::slug($categoryName, '-'); @endphp

                <div class="border dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                    {{-- Header Kategori --}}
                    <div
                        class="flex items-center justify-between px-4 py-2 bg-white dark:bg-gray-800 rounded-t-md border-b border-gray-200 dark:border-gray-600">
                        <div class="font-medium text-gray-700 dark:text-gray-200">{{ $categoryName }}</div>
                        <label
                            class="text-xs flex items-center gap-2 cursor-pointer select-none text-blue-600 hover:text-blue-800">
                            <input type="checkbox" class="toggle-all-in-cat rounded border-gray-300"
                                data-target="cat-{{ $slug }}">
                            <span>Pilih semua</span>
                        </label>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-fixed text-sm divide-y divide-gray-100 dark:divide-gray-700">
                            <thead
                                class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-[10px] tracking-wider font-semibold">
                                <tr>
                                    <th class="py-3 px-3 text-center w-10">Nilai</th>
                                    <th class="py-3 px-3 text-left w-56">Nama Unsur</th>
                                    <th class="py-3 px-3 text-left hidden md:table-cell w-[28rem]">Deskripsi</th>
                                    {{-- Opsi --}}
                                    <th class="py-3 px-1 text-center w-12 text-green-600">P</th>
                                    <th class="py-3 px-1 text-center w-12 text-red-600">F</th>
                                    <th class="py-3 px-1 text-center w-12 text-yellow-600">R</th>
                                    <th class="py-3 px-1 text-center w-12 text-gray-500">OP</th>
                                    <th class="py-3 px-3 text-left w-64">Catatan</th>
                                    <th class="py-3 px-3 text-left w-40">Tanda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($params as $p)
                                    @php
                                        $prev = $previousMap[$p->id] ?? null;
                                        $checked = (bool) $prev;
                                        $code = $prev['result_code'] ?? null;
                                        $note = $prev['note'] ?? '';

                                        // Bucket
                                        $bucketKey = bucketOf(
                                            $ageInDays,
                                            $p->percent_25,
                                            $p->percent_75,
                                            $p->percent_100,
                                        );
                                        $bLabel = $bucketConfig[$bucketKey]['label'];
                                        $bClass = $bucketConfig[$bucketKey]['class'];

                                        if ($bucketKey === 'OVERDUE' && $code === 'P') {
                                            $bClass = 'bg-green-100 text-green-700';
                                            $bLabel = 'Lewat Usia (Lulus)';
                                        }

                                        // Badge Hasil
                                        $resText = '—';
                                        $resClass = 'bg-gray-100 text-gray-400';
                                        if ($code) {
                                            $resText = match ($code) {
                                                'P' => 'LULUS',
                                                'F' => 'GAGAL',
                                                'R' => 'ULANG',
                                                'OP' => 'BELUM',
                                                default => '-',
                                            };
                                            $resClass = match ($code) {
                                                'P' => 'bg-green-100 text-green-700',
                                                'F' => 'bg-red-100 text-red-700',
                                                'R' => 'bg-yellow-100 text-yellow-700',
                                                'OP' => 'bg-gray-200 text-gray-700',
                                                default => 'bg-gray-100 text-gray-400',
                                            };
                                        }

                                        // Style Baris (Active vs Disabled - TANPA TRANSPARANSI)
                                        $rowClass = $checked ? 'row-active' : 'row-disabled';
                                    @endphp

                                    <tr class="border-t border-gray-200 dark:border-gray-700 param-row {{ $rowClass }}"
                                        data-cat="cat-{{ $slug }}"
                                        data-text="{{ \Illuminate\Support\Str::lower($p->test_element_name . ' ' . ($p->test_element_description ?? '')) }}"
                                        data-param-id="{{ $p->id }}">

                                        <td class="py-2 px-3 align-top text-center">
                                            <input type="checkbox"
                                                class="chk-include rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer w-4 h-4"
                                                data-row="{{ $rowIndex }}" @checked($checked)>
                                        </td>

                                        <td class="py-2 px-3 align-top font-medium">
                                            <div class="space-y-1">
                                                <div class="text-gray-900 dark:text-gray-100">
                                                    {{ $p->test_element_name }}</div>
                                                @if ($p->test_element_description)
                                                    <div
                                                        class="md:hidden text-[10px] italic border-l-2 pl-2 border-gray-300 text-gray-500">
                                                        {{ \Illuminate\Support\Str::limit($p->test_element_description, 60) }}
                                                    </div>
                                                @endif
                                                <div class="text-[10px] text-gray-500 font-mono">
                                                    {{ $p->percent_25 ?? '-' }}/<b>{{ $p->percent_75 ?? '-' }}</b>/{{ $p->percent_100 ?? '-' }}
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="py-2 px-3 align-top text-xs text-gray-500 hidden md:table-cell leading-relaxed">
                                            {{ $p->test_element_description ?? '-' }}
                                        </td>

                                        <input type="hidden" name="items[{{ $rowIndex }}][parameter_id]"
                                            value="{{ $p->id }}" {{ $checked ? '' : 'disabled' }}>

                                        @foreach (['P', 'F', 'R', 'OP'] as $opt)
                                            <td class="py-2 px-1 text-center align-top">
                                                <label
                                                    class="block w-full h-full cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 rounded p-1 transition">
                                                    <input type="radio"
                                                        name="items[{{ $rowIndex }}][result_code]"
                                                        value="{{ $opt }}" {{ $checked ? '' : 'disabled' }}
                                                        @checked($code === $opt)
                                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                                </label>
                                            </td>
                                        @endforeach

                                        <td class="py-2 px-3">
                                            <input type="text" name="items[{{ $rowIndex }}][note]"
                                                value="{{ $note }}" {{ $checked ? '' : 'disabled' }}
                                                class="w-full border rounded px-2 py-1.5 text-sm dark:bg-gray-900 dark:border-gray-700 note-input focus:ring-blue-500"
                                                placeholder="Ket...">
                                        </td>

                                        <td class="py-2 px-3 text-[10px]">
                                            <div class="flex flex-col gap-1.5 items-start">
                                                <span
                                                    class="px-2 py-0.5 rounded uppercase tracking-wide border {{ $bClass }} bucket-badge whitespace-normal text-center leading-tight">
                                                    {{ $bLabel }}
                                                </span>
                                                <span
                                                    class="result-badge px-2 py-0.5 rounded font-bold border shadow-sm {{ $resClass }}">
                                                    {{ $resText }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $rowIndex++; @endphp
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-8 text-center text-gray-500">Tidak ada item
                                            relevan untuk usia ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('mmdst.index') }}"
                class="bg-white text-gray-700 border border-gray-300 rounded-lg px-5 h-10 flex items-center hover:bg-gray-50 transition shadow-sm text-sm font-medium">Batal</a>
            <button type="button" onclick="confirmSubmit()"
                class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-6 h-10 transition shadow-md text-sm font-medium flex items-center gap-2">
                Simpan Penilaian
            </button>
        </div>
    </form>

    {{-- JavaScript Logic --}}
    <script>
        const $ = (sel, ctx = document) => ctx.querySelector(sel);
        const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
        const routeFilterParams = @json(route('mmdst.filter-params'));
        const initialPreviousMap = @json($previousMap->toArray());

        // 1. Logika Bucket Usia JS (Wajib Sama dengan PHP)
        function getBucketInfoJS(age, p25, p75, p100) {
            age = parseInt(age);
            p25 = parseInt(p25) || 0;
            p75 = parseInt(p75) || 0;
            p100 = parseInt(p100) || 99999;
            if (age < p25) return {
                label: 'Belum Waktunya',
                cls: 'bg-gray-200 text-gray-700'
            };
            if (age > p100) return {
                label: 'Lewat Usia',
                cls: 'bg-red-100 text-red-700'
            };
            if (age >= p75 && age <= p100) return {
                label: 'Zona Kritis',
                cls: 'bg-orange-100 text-orange-800 font-bold border border-orange-200'
            };
            if (age === p25) return {
                label: 'Di Garis Usia',
                cls: 'bg-blue-600 text-white font-bold'
            };
            return {
                label: 'Rentang Usia',
                cls: 'bg-blue-100 text-blue-700'
            };
        }

        // 2. Auto Expand Textarea
        const mainNotes = $('#main-notes');

        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }
        if (mainNotes) {
            mainNotes.addEventListener('input', () => autoResize(mainNotes));
            window.addEventListener('load', () => autoResize(mainNotes));
        }

        // 3. Row Interaction
        function updateResultBadge(row) {
            const rb = row.querySelector('.result-badge');
            if (!rb) return;
            const rowIdx = row.querySelector('.chk-include')?.dataset.row;
            const selected = row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value;

            let label = '—',
                cls = 'bg-gray-100 text-gray-400 border-gray-200';
            if (selected === 'P') {
                label = 'LULUS';
                cls = 'bg-green-100 text-green-700';
            }
            if (selected === 'F') {
                label = 'GAGAL';
                cls = 'bg-red-100 text-red-700';
            }
            if (selected === 'R') {
                label = 'ULANG';
                cls = 'bg-yellow-100 text-yellow-700';
            }
            if (selected === 'OP') {
                label = 'BELUM';
                cls = 'bg-gray-200 text-gray-700';
            }

            rb.textContent = label;
            rb.className = `result-badge px-2 py-0.5 rounded font-bold border shadow-sm ${cls}`;
        }

        function wireRowToggle(row) {
            if (row.dataset.wired) return;
            row.dataset.wired = "true";

            const chk = row.querySelector('.chk-include');
            const rowIdx = chk.dataset.row;

            const toggle = () => {
                const inputs = row.querySelectorAll(`input[name^="items[${rowIdx}]"]`);
                inputs.forEach(el => {
                    if (el !== chk) el.disabled = !chk.checked;
                });

                // Style Change: Solid colors
                if (!chk.checked) {
                    row.classList.remove('row-active', 'bg-white', 'dark:bg-gray-800');
                    row.classList.add('row-disabled');
                } else {
                    row.classList.remove('row-disabled');
                    row.classList.add('row-active', 'bg-white', 'dark:bg-gray-800');
                }
                updateResultBadge(row);
            };

            toggle();
            chk.addEventListener('change', () => {
                toggle();
                buildMainNotes();
            });
            row.querySelectorAll('input[type="radio"]').forEach(r => {
                r.addEventListener('change', () => {
                    updateResultBadge(row);
                    buildMainNotes();
                });
            });
            row.querySelector('.note-input')?.addEventListener('input', buildMainNotes);
        }

        $$('.param-row').forEach(wireRowToggle);

        // 4. Dynamic Reload
        const studentIdInput = $('#student_id');
        const assessmentDate = $('#assessment_date');
        const ageLabel = $('#age-label');
        const paramContainer = $('#param-container');

        function escapeHtml(s) {
            return (s ?? '').toString().replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            } [c]));
        }

        function escapeAttr(s) {
            return escapeHtml(s).replace(/"/g, '&quot;');
        }

        function slugify(s) {
            return (s || '').toString().toLowerCase().replace(/[^a-z0-9]+/g, '-');
        }

        function captureCurrentState() {
            const state = {};
            $$('.param-row').forEach(row => {
                const pid = row.dataset.paramId;
                const chk = row.querySelector('.chk-include');
                if (!pid || !chk) return;
                const rowIdx = chk.dataset.row;
                state[pid] = {
                    enabled: chk.checked,
                    code: row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value ||
                        null,
                    note: row.querySelector(`input[name="items[${rowIdx}][note]"]`)?.value || '',
                    userInteracted: true
                };
            });
            return state;
        }

        assessmentDate.addEventListener('change', async function() {
            const studentId = studentIdInput.value;
            if (!studentId) return;

            const currentState = captureCurrentState();
            ageLabel.textContent = '...';

            try {
                const url = new URL(routeFilterParams, window.location.origin);
                url.searchParams.set('student_id', studentId);
                url.searchParams.set('assessment_date', assessmentDate.value);

                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const json = await res.json();
                if (!res.ok) throw new Error('Gagal memuat.');

                ageLabel.textContent = json.age_in_days;

                renderCategories(json.data || {}, currentState, json.age_in_days);
                $('#filter-input').dispatchEvent(new Event('input'));
                buildMainNotes();

            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: e.message
                });
            }
        });

        function renderCategories(filteredCategories, currentState, age) {
            paramContainer.innerHTML = '';
            let rowIndex = 0;

            Object.keys(filteredCategories).forEach(catName => {
                const slug = slugify(catName);
                const items = filteredCategories[catName] || [];

                const wrap = document.createElement('div');
                wrap.className =
                    'border dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 shadow-sm overflow-hidden';
                wrap.innerHTML = `
                    <div class="flex items-center justify-between px-4 py-2 bg-white dark:bg-gray-800 rounded-t-md border-b border-gray-200 dark:border-gray-600">
                        <div class="font-medium text-gray-700 dark:text-gray-200 text-sm uppercase">${catName}</div>
                        <label class="text-xs flex items-center gap-2 cursor-pointer select-none text-blue-600 hover:text-blue-800">
                            <input type="checkbox" class="toggle-all-in-cat rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-target="cat-${slug}">
                            <span>Pilih semua</span>
                        </label>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-fixed text-sm">
                            <thead class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-[10px] tracking-wider font-semibold">
                                <tr>
                                    <th class="py-2 px-3 text-left w-10">Nilai</th>
                                    <th class="py-2 px-3 text-left w-56">Nama Unsur</th>
                                    <th class="py-2 px-3 text-left hidden md:table-cell w-[28rem]">Deskripsi</th>
                                    <th class="py-2 px-3 text-center w-12 text-green-600">P</th>
                                    <th class="py-2 px-3 text-center w-12 text-red-600">F</th>
                                    <th class="py-2 px-3 text-center w-12 text-yellow-600">R</th>
                                    <th class="py-2 px-3 text-center w-12 text-gray-500">OP</th>
                                    <th class="py-2 px-3 text-left w-64">Catatan</th>
                                    <th class="py-2 px-3 text-left w-44">Tanda</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700"></tbody>
                        </table>
                    </div>
                `;
                const tbody = wrap.querySelector('tbody');

                items.forEach(p => {
                    const pid = p.id;
                    let savedState = currentState[pid] || {};
                    let userHasInteracted = savedState.userInteracted === true;
                    let history = initialPreviousMap[pid] || {};
                    let historyCode = history.result_code || null;
                    let historyNote = history.note || '';

                    let enabled, code, note;
                    if (userHasInteracted) {
                        enabled = savedState.enabled;
                        code = savedState.code;
                        note = savedState.note;
                    } else {
                        enabled = !!historyCode;
                        code = historyCode;
                        note = historyNote;
                    }

                    const bucket = getBucketInfoJS(age, p.percent_25, p.percent_75, p.percent_100);
                    let bLabel = bucket.label;
                    let bClass = bucket.cls;

                    if (bucket.label === 'Lewat Usia' && code === 'P') {
                        bClass = 'bg-green-100 text-green-700 border-green-200';
                        bLabel = 'Lewat Usia (Lulus)';
                    }

                    let resLabel = '—',
                        resClass = 'bg-gray-100 text-gray-400 border-gray-200';
                    if (code === 'P') {
                        resLabel = 'LULUS';
                        resClass = 'bg-green-100 text-green-700';
                    }
                    if (code === 'F') {
                        resLabel = 'GAGAL';
                        resClass = 'bg-red-100 text-red-700';
                    }
                    if (code === 'R') {
                        resLabel = 'ULANG';
                        resClass = 'bg-yellow-100 text-yellow-700';
                    }
                    if (code === 'OP') {
                        resLabel = 'BELUM';
                        resClass = 'bg-gray-200 text-gray-700';
                    }

                    const tr = document.createElement('tr');
                    const rowClass = enabled ? 'row-active bg-white dark:bg-gray-800' : 'row-disabled';
                    tr.className =
                        `param-row border-t border-gray-200 dark:border-gray-700 transition-colors ${rowClass}`;
                    tr.dataset.cat = `cat-${slug}`;
                    tr.dataset.text = (p.name + ' ' + (p.description || '')).toLowerCase();
                    tr.dataset.paramId = pid;

                    const renderRadio = (val) => `
                        <td class="py-2 px-3 text-center">
                            <input type="radio" name="items[${rowIndex}][result_code]" value="${val}"
                                ${enabled ? '' : 'disabled'} ${code === val ? 'checked' : ''}>
                        </td>`;

                    tr.innerHTML = `
                        <td class="py-2 px-3 align-top">
                            <input type="checkbox" class="chk-include" data-row="${rowIndex}" ${enabled ? 'checked' : ''}>
                        </td>
                        <td class="py-2 px-3 align-top font-medium">
                            <div class="space-y-1">
                                <div>${escapeHtml(p.name)}</div>
                                ${p.description ? `<div class="md:hidden text-[10px] text-gray-500">${escapeHtml(p.description).slice(0,60)}</div>` : ''}
                                <div class="text-[10px] text-gray-500 font-mono">
                                    ${p.percent_25??'-'}/<b>${p.percent_75??'-'}</b>/${p.percent_100??'-'}
                                </div>
                            </div>
                        </td>
                        <td class="py-2 px-3 align-top text-xs text-gray-500 hidden md:table-cell">
                            ${escapeHtml(p.description)}
                        </td>
                        <input type="hidden" name="items[${rowIndex}][parameter_id]" value="${pid}" ${enabled ? '' : 'disabled'}>
                        ${renderRadio('P')} ${renderRadio('F')} ${renderRadio('R')} ${renderRadio('OP')}
                        <td class="py-2 px-3">
                            <input type="text" name="items[${rowIndex}][note]" value="${escapeAttr(note)}" ${enabled ? '' : 'disabled'}
                                class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-900 dark:border-gray-700 note-input">
                        </td>
                        <td class="py-2 px-3 text-[10px]">
                            <div class="flex flex-col gap-1 items-start">
                                <span class="px-2 py-0.5 rounded bucket-badge ${bClass} whitespace-normal text-center h-auto leading-tight">${bLabel}</span>
                                <span class="result-badge px-2 py-0.5 rounded font-bold border shadow-sm ${resClass}">${resLabel}</span>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                    wireRowToggle(tr);
                    rowIndex++;
                });
                paramContainer.appendChild(wrap);

                wrap.querySelector('.toggle-all-in-cat').addEventListener('change', (e) => {
                    $$(`tr[data-cat="cat-${slug}"] .chk-include`).forEach(c => {
                        c.checked = e.target.checked;
                        c.dispatchEvent(new Event('change'));
                    });
                });
            });
        }

        // 5. Filter & Summary
        $('#filter-input')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            $$('.param-row').forEach(row => {
                const txt = row.dataset.text || '';
                row.style.display = txt.includes(q) ? '' : 'none';
            });
        });

        $$('.toggle-all-in-cat').forEach(tg => {
            tg.addEventListener('change', (e) => {
                $$(`tr[data-cat="${e.target.dataset.target}"] .chk-include`).forEach(c => {
                    c.checked = e.target.checked;
                    c.dispatchEvent(new Event('change'));
                });
            });
        });

        const autoToggle = $('#auto-summary-toggle');

        // --- 6. AUTO SUMMARY (FIXED: HANYA JIKA NOTE DIISI) ---
        function buildMainNotes() {
            if (!autoToggle.checked) return;
            const blocks = {};
            $$('.param-row').forEach(row => {
                const chk = row.querySelector('.chk-include');
                if (!chk.checked) return;

                const rowIdx = chk.dataset.row;
                const note = (row.querySelector('.note-input')?.value || '').trim();
                const code = row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value || '-';

                // JANGAN MASUKKAN jika note kosong
                if (!note) return;

                const catName = row.closest('.rounded-md').querySelector('.font-medium').innerText.trim();
                const paramName = row.querySelector('td:nth-child(2) div').innerText.trim().split('\n')[0];

                if (!blocks[catName]) blocks[catName] = [];
                let line = `- ${paramName}`;
                if (code !== '-') line += ` [${code}]`;
                line += `: ${note}`;

                blocks[catName].push(line);
            });
            const lines = [];
            Object.keys(blocks).forEach(c => {
                if (blocks[c].length) lines.push(`${c}:\n${blocks[c].join('\n')}`);
            });
            mainNotes.value = lines.join('\n\n');
            autoResize(mainNotes);
        }
        autoToggle.addEventListener('change', () => {
            if (autoToggle.checked) buildMainNotes();
        });

        // 7. Submit
        window.confirmSubmit = function() {
            const anyChecked = $$('.chk-include:checked').length > 0;
            if (!anyChecked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih minimal 1 item.'
                });
                return;
            }
            const invalid = $$('.param-row').some(row => {
                const chk = row.querySelector('.chk-include');
                return chk.checked && !row.querySelector('input[type="radio"]:checked');
            });
            if (invalid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Lengkap',
                    text: 'Isi nilai (P/F/R/OP) untuk item yang dicentang.'
                });
                return;
            }

            Swal.fire({
                title: 'Simpan?',
                text: 'Pastikan data sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan'
            }).then(res => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('assessment-form').submit();
                }
            });
        };

        // Init
        window.addEventListener('load', () => {
            buildMainNotes();
            autoResize(mainNotes);
        });
    </script>
</x-app-layout>
