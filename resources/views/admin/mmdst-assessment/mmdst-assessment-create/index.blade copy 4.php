<x-app-layout>
    <x-slot:title>Tambah Penilaian MMDST</x-slot:title>

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
        // Fallback variabel dari controller
        $selectedStudent = $selectedStudent ?? ($student ?? null);
        $studentIsNormal = isset($studentIsNormal) ? (bool) $studentIsNormal : true;
        $ageInDays = isset($ageInDays) ? (int) $ageInDays : 0;
        $filtered_categories = $filtered_categories ?? collect();
        $previousMap = $previousMap ?? collect(); // [param_id => ['result_code'=>'P','note'=>'...']]
        $assessmentDate = $assessmentDate ?? now()->toDateString();

        function bucketOf($age, $p25, $p100)
        {
            if (is_null($p25) || is_null($p100)) {
                return 'NOT_YET';
            }
            if ($age < $p25) {
                return 'NOT_YET';
            }
            if ($age > $p100) {
                return 'OVERDUE';
            }
            if ($age == $p25 || $age == $p100) {
                return 'AT_LINE';
            }
            return 'IN_WINDOW';
        }
        $bucketText = [
            'OVERDUE' => 'Lewat Usia',
            'AT_LINE' => 'Di Garis Usia',
            'IN_WINDOW' => 'Rentang Usia',
            'NOT_YET' => 'Belum Waktunya',
        ];
    @endphp

    <form id="assessment-form" action="{{ route('mmdst-assessments.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Data Penilaian --}}
        <div class="p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <h2 class="font-semibold mb-4">Data Penilaian</h2>
            <div class="grid md:grid-cols-4 gap-4">
                {{-- Identitas siswa (fix, tidak dropdown) --}}
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1">Siswa</label>
                    <div class="h-10 flex items-center px-3 border rounded-lg dark:bg-gray-900 dark:border-gray-700">
                        <span class="font-medium">{{ $selectedStudent?->student_name ?? '—' }}</span>
                    </div>
                    <input type="hidden" name="student_id" id="student_id" value="{{ $selectedStudent?->id }}">
                    @if ($selectedStudent)
                        <div class="text-[11px] text-gray-500 mt-1">
                            NIS: {{ $selectedStudent->student_number ?? '—' }} •
                            Tgl Lahir:
                            {{ $selectedStudent->birth_date ? \Illuminate\Support\Carbon::parse($selectedStudent->birth_date)->format('d M Y') : '—' }}
                        </div>
                    @endif
                    <p class="text-[11px] text-gray-500 mt-1">
                        Status: <b id="student-normal-badge">{{ $studentIsNormal ? 'Normal' : 'Kebutuhan Khusus' }}</b>
                    </p>
                </div>

                {{-- Tanggal + usia dinamis --}}
                <div>
                    <label class="block text-sm mb-1">Tanggal Penilaian</label>
                    <input type="date" name="assessment_date" id="assessment_date"
                        value="{{ old('assessment_date', $assessmentDate) }}"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700" required>
                    <p class="text-[11px] text-gray-500 mt-1">Usia: <b id="age-label">{{ $ageInDays }}</b> hari</p>
                </div>

                {{-- Toggle auto summary --}}
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-xs">
                        <input type="checkbox" id="auto-summary-toggle" checked>
                        <span>Auto ringkas catatan parameter ke “Catatan utama”</span>
                    </label>
                </div>

                {{-- Catatan Utama --}}
                <div class="md:col-span-4">
                    <label class="block text-sm mb-1">Catatan utama (otomatis dari catatan per-parameter)</label>
                    <textarea name="notes" id="main-notes" rows="3"
                        class="w-full border rounded-lg px-3 py-2 dark:bg-gray-900 dark:border-gray-700">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- LEGENDA --}}
        <div class="p-4 bg-white dark:bg-gray-900 rounded-md shadow text-[11px] md:text-xs">
            <div class="space-y-2">
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <span class="font-medium">Hasil Tes:</span>
                    <span class="px-2 py-0.5 rounded bg-green-100 text-green-700">LULUS (P)</span>
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">GAGAL (F)</span>
                    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">ULANG (R)</span>
                    <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700">BELUM (OP)</span>
                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700">TIDAK WAJIB (NR)</span>
                </div>
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <span class="font-medium">Status Usia:</span>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700">Di Garis Usia</span>
                    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">Rentang Usia</span>
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">Lewat Usia</span>
                    <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700">Belum Waktunya</span>
                </div>
                <ul class="list-disc pl-5 text-gray-600">
                    <li><b>NR (Not Required / Tidak Wajib Diujikan)</b> untuk item yang tidak perlu diujikan pada sesi
                        ini.</li>
                    <li>Bila <b>Lewat Usia</b> namun hasil <b>LULUS</b>, tetap dianggap sesuai (normal) dan akan
                        ditandai hijau.</li>
                </ul>
            </div>
        </div>

        {{-- Pencarian --}}
        <div class="flex items-center justify-between">
            <div class="flex gap-2">
                <input id="filter-input" type="text" placeholder="Cari parameter..."
                    class="border rounded-lg px-3 h-9 dark:bg-gray-900 dark:border-gray-700">
            </div>
            <div class="text-xs text-gray-500">
                {{ $studentIsNormal ? 'Mode: Filter by usia (Normal)' : 'Mode: Tampilkan semua (KH)' }}
            </div>
        </div>

        {{-- Parameter Per Kategori --}}
        @php $rowIndex = 0; @endphp
        <div class="space-y-6" id="param-container">
            @foreach ($filtered_categories as $categoryName => $params)
                @php $slug = \Illuminate\Support\Str::slug($categoryName, '-'); @endphp
                <div class="border dark:border-gray-700 rounded-md">
                    <div class="flex items-center justify-between px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-md">
                        <div class="font-medium">{{ $categoryName }}</div>
                        <label class="text-xs flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="toggle-all-in-cat" data-target="cat-{{ $slug }}">
                            <span>Pilih semua</span>
                        </label>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="py-2 px-3 text-left w-16">Nilai</th>
                                    <th class="py-2 px-3 text-left">Nama Unsur</th>
                                    <th class="py-2 px-3 text-left hidden md:table-cell">Deskripsi</th>
                                    <th class="py-2 px-3 text-center">P</th>
                                    <th class="py-2 px-3 text-center">F</th>
                                    <th class="py-2 px-3 text-center">R</th>
                                    <th class="py-2 px-3 text-center">OP</th>
                                    <th class="py-2 px-3 text-center">NR</th>
                                    <th class="py-2 px-3 text-left w-48">Catatan</th>
                                    <th class="py-2 px-3 text-left">Tanda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($params as $p)
                                    @php
                                        $prev = $previousMap[$p->id] ?? null;
                                        $checked = (bool) $prev;
                                        $code = $prev['result_code'] ?? null;
                                        $note = $prev['note'] ?? '';

                                        $bucket = bucketOf($ageInDays, $p->percent_25, $p->percent_100);
                                        $bucketBadge = match ($bucket) {
                                            'OVERDUE' => 'bg-red-100 text-red-700',
                                            'AT_LINE' => 'bg-blue-100 text-blue-700',
                                            'IN_WINDOW' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-200 text-gray-700',
                                        };
                                        if ($bucket === 'OVERDUE' && $code === 'P') {
                                            $bucketBadge = 'bg-green-100 text-green-700';
                                        }
                                        $bucketTextId =
                                            $bucket === 'OVERDUE' && $code === 'P'
                                                ? 'Lewat Usia (Lulus)'
                                                : $bucketText[$bucket] ?? $bucket;

                                        $resultLabel = match ($code) {
                                            'P' => 'LULUS',
                                            'F' => 'GAGAL',
                                            'R' => 'ULANG',
                                            'OP' => 'BELUM',
                                            'NR' => 'TIDAK WAJIB',
                                            default => '—',
                                        };
                                        $resultBadge = match ($resultLabel) {
                                            'LULUS' => 'bg-green-100 text-green-700',
                                            'GAGAL' => 'bg-red-100 text-red-700',
                                            'ULANG' => 'bg-yellow-100 text-yellow-700',
                                            'TIDAK WAJIB' => 'bg-purple-100 text-purple-700',
                                            'BELUM' => 'bg-gray-200 text-gray-700',
                                            default => 'bg-gray-200 text-gray-700',
                                        };
                                    @endphp

                                    <tr class="border-t border-gray-200 dark:border-gray-700 param-row"
                                        data-cat="cat-{{ $slug }}"
                                        data-text="{{ \Illuminate\Support\Str::lower($p->test_element_name . ' ' . ($p->test_element_description ?? '')) }}"
                                        data-param-id="{{ $p->id }}">
                                        <td class="py-2 px-3 align-top">
                                            <input type="checkbox" class="chk-include" data-row="{{ $rowIndex }}"
                                                @checked($checked)>
                                        </td>

                                        <td class="py-2 px-3 align-top font-medium">
                                            <div class="space-y-1">
                                                <div>{{ $p->test_element_name }}</div>
                                                @if ($p->test_element_description)
                                                    <div class="md:hidden text-[11px] text-gray-500">
                                                        {{ \Illuminate\Support\Str::limit($p->test_element_description, 80) }}
                                                    </div>
                                                @endif
                                                <div class="text-[10px] text-gray-500">
                                                    25/50/75/100:
                                                    {{ $p->percent_25 ?? '—' }}/{{ $p->percent_50 ?? '—' }}/{{ $p->percent_75 ?? '—' }}/{{ $p->percent_100 ?? '—' }}
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                            {{ $p->test_element_description ?? '-' }}
                                        </td>

                                        {{-- hidden id param --}}
                                        <input type="hidden" name="items[{{ $rowIndex }}][parameter_id]"
                                            value="{{ $p->id }}" {{ $checked ? '' : 'disabled' }}>

                                        {{-- Radios P/F/R/OP/NR --}}
                                        @foreach (['P', 'F', 'R', 'OP', 'NR'] as $opt)
                                            <td class="py-2 px-3 text-center">
                                                <input type="radio" name="items[{{ $rowIndex }}][result_code]"
                                                    value="{{ $opt }}" {{ $checked ? '' : 'disabled' }}
                                                    @checked($code === $opt)>
                                            </td>
                                        @endforeach

                                        {{-- Note --}}
                                        <td class="py-2 px-3">
                                            <input type="text" name="items[{{ $rowIndex }}][note]"
                                                value="{{ $note }}" {{ $checked ? '' : 'disabled' }}
                                                class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-900 dark:border-gray-700 note-input">
                                        </td>

                                        {{-- Badges: usia + hasil --}}
                                        <td class="py-2 px-3 text-[11px] md:text-xs">
                                            <div class="flex flex-wrap gap-1 items-center">
                                                <span
                                                    class="px-2 py-0.5 rounded bucket-badge {{ $bucketBadge }}">{{ $bucketTextId }}</span>
                                                <span
                                                    class="px-2 py-0.5 rounded result-badge {{ $resultBadge }}">{{ $resultLabel }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $rowIndex++; @endphp
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-4 px-3 text-center text-gray-500">Tidak ada item
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
        <div class="flex justify-end gap-2">
            <a href="{{ route('mmdst.index') }}" class="border rounded-lg px-4 h-10 flex items-center">Batal</a>
            <button type="button" onclick="confirmSubmit()"
                class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 h-10">Simpan</button>
        </div>
    </form>

    <script>
        // ========= Helpers =========
        const csrf = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const $ = (sel, ctx = document) => ctx.querySelector(sel);
        const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
        const routeFilterParams = @json(route('mmdst.filter-params'));
        const previousMapJSON = @json($previousMap->toArray());

        // ========= Per-row enabler =========
        function wireRowToggle(row) {
            const chk = row.querySelector('.chk-include');
            if (!chk) return;
            const rowIdx = chk.dataset.row;

            const toggle = () => {
                row.querySelectorAll(
                    `input[name="items[${rowIdx}][parameter_id]"], input[name="items[${rowIdx}][result_code]"], input[name="items[${rowIdx}][note]"]`
                ).forEach(el => el.disabled = !chk.checked);

                // update badge hasil saat baris enable/disable
                updateResultBadge(row);
            };

            toggle();
            chk.addEventListener('change', () => {
                toggle();
                buildMainNotes();
            });
        }

        function updateResultBadge(row) {
            const rb = row.querySelector('.result-badge');
            if (!rb) return;
            const rowIdx = row.querySelector('.chk-include')?.dataset.row;
            const selected = row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value || null;

            let label = 'BELUM',
                cls = 'bg-gray-200 text-gray-700';
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
            if (selected === 'NR') {
                label = 'TIDAK WAJIB';
                cls = 'bg-purple-100 text-purple-700';
            }

            rb.textContent = label;
            rb.className = 'px-2 py-0.5 rounded result-badge ' + cls;
        }

        $$('.param-row').forEach(row => {
            wireRowToggle(row);
            wireNoteAndRadio(row);
        });

        // Toggle semua di kategori
        $$('.toggle-all-in-cat').forEach(tg => {
            tg.addEventListener('change', () => {
                const cat = tg.dataset.target;
                $$(`tr[data-cat="${cat}"] .chk-include`).forEach(chk => {
                    chk.checked = tg.checked;
                    chk.dispatchEvent(new Event('change'));
                });
            });
        });

        // Pencarian
        const filterInput = $('#filter-input');
        if (filterInput) {
            filterInput.addEventListener('input', () => {
                const q = filterInput.value.toLowerCase();
                $$('.param-row').forEach(row => {
                    const txt = row.getAttribute('data-text') || '';
                    row.style.display = txt.includes(q) ? '' : 'none';
                });
            });
        }

        // ========= Auto Summary ke Catatan Utama =========
        const autoToggle = $('#auto-summary-toggle');
        const mainNotes = $('#main-notes');

        function buildMainNotes() {
            if (!autoToggle.checked) return;
            const blocksByCategory = {};
            $$('.param-row').forEach(row => {
                const chk = row.querySelector('.chk-include');
                if (!chk?.checked) return;

                const rowIdx = chk.dataset.row;
                const noteEl = row.querySelector(`input[name="items[${rowIdx}][note]"]`);
                const note = (noteEl?.value || '').trim();
                if (!note) return;

                const catWrap = row.closest('.border.rounded-md');
                const catHeader = catWrap?.querySelector('.font-medium')?.textContent?.trim() || 'Kategori';
                const paramName = row.querySelector('td:nth-child(2) div > div:first-child')?.textContent?.trim() ||
                    'Unsur';
                const code = row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value || '-';

                (blocksByCategory[catHeader] ??= []).push(`- ${paramName} [${code}]: ${note}`);
            });

            const parts = [];
            Object.keys(blocksByCategory).forEach(cat => {
                parts.push(`${cat}:\n${blocksByCategory[cat].join('\n')}`);
            });
            mainNotes.value = parts.join('\n\n');
        }

        function wireNoteAndRadio(row) {
            const rowIdx = row.querySelector('.chk-include')?.dataset.row;
            const noteEl = row.querySelector(`input[name="items[${rowIdx}][note]"]`);
            if (noteEl) noteEl.addEventListener('input', buildMainNotes);
            $$(`input[name="items[${rowIdx}][result_code]"]`, row).forEach(r => {
                r.addEventListener('change', () => {
                    updateResultBadge(row);
                    buildMainNotes();
                });
            });
        }

        // ========= Dynamic reload saat tanggal berubah (NORMAL saja) =========
        const studentIdInput = $('#student_id');
        const assessmentDate = $('#assessment_date');
        const ageLabel = $('#age-label');
        const paramContainer = $('#param-container');
        const studentNormalBadge = $('#student-normal-badge');

        assessmentDate.addEventListener('change', reloadParamsIfNeeded);

        async function reloadParamsIfNeeded() {
            const studentId = studentIdInput.value;
            if (!studentId) return;

            // Hanya auto-reload untuk Normal. (Server masih ikut menentukan.)
            const isNormal = (studentNormalBadge.textContent || '').toLowerCase().includes('normal');

            const currentState = captureCurrentState();

            try {
                const url = new URL(routeFilterParams, window.location.origin);
                url.searchParams.set('student_id', studentId);
                url.searchParams.set('assessment_date', assessmentDate.value);

                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });
                const json = await res.json();
                if (!res.ok || json.ok === false) throw new Error(json.message || 'Gagal memuat parameter.');

                // Update usia
                ageLabel.textContent = json.age_in_days ?? '-';

                // Render ulang (pakai json.data !!!)
                renderCategories(json.data || {}, currentState);

                // Apply filter text yang sedang aktif
                filterInput?.dispatchEvent(new Event('input'));

                // rebuild ringkasan
                buildMainNotes();
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: e.message || e.toString()
                });
            }
        }

        // Simpan kondisi saat ini
        function captureCurrentState() {
            const state = {};
            $$('.param-row').forEach(row => {
                const pid = row.getAttribute('data-param-id');
                const chk = row.querySelector('.chk-include');
                if (!pid || !chk) return;

                const rowIdx = chk.dataset.row;
                state[pid] = {
                    enabled: chk.checked,
                    code: row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value ||
                        null,
                    note: row.querySelector(`input[name="items[${rowIdx}][note]"]`)?.value || ''
                };
            });
            return state;
        }

        // Render ulang dari JSON server: { "Kategori": [ {id, name/description/percent_*} ] }
        function renderCategories(filteredCategories, previousState) {
            paramContainer.innerHTML = '';
            let rowIndex = 0;

            const bucketClass = (bucket, passed) => {
                if (bucket === 'OVERDUE' && passed) return 'bg-green-100 text-green-700';
                switch (bucket) {
                    case 'OVERDUE':
                        return 'bg-red-100 text-red-700';
                    case 'AT_LINE':
                        return 'bg-blue-100 text-blue-700';
                    case 'IN_WINDOW':
                        return 'bg-yellow-100 text-yellow-700';
                    default:
                        return 'bg-gray-200 text-gray-700';
                }
            };
            const bucketText = (b, passed) => {
                if (b === 'OVERDUE' && passed) return 'Lewat Usia (Lulus)';
                switch (b) {
                    case 'OVERDUE':
                        return 'Lewat Usia';
                    case 'AT_LINE':
                        return 'Di Garis Usia';
                    case 'IN_WINDOW':
                        return 'Rentang Usia';
                    default:
                        return 'Belum Waktunya';
                }
            };

            const age = parseInt($('#age-label').textContent || '0', 10) || 0;

            Object.keys(filteredCategories).forEach(catName => {
                const slug = slugify(catName);
                const items = filteredCategories[catName] || [];

                const wrap = document.createElement('div');
                wrap.className = 'border dark:border-gray-700 rounded-md';
                wrap.innerHTML = `
                    <div class="flex items-center justify-between px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-md">
                        <div class="font-medium">${catName}</div>
                        <label class="text-xs flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="toggle-all-in-cat" data-target="cat-${slug}">
                            <span>Pilih semua</span>
                        </label>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="py-2 px-3 text-left w-16">Nilai</th>
                                    <th class="py-2 px-3 text-left">Nama Unsur</th>
                                    <th class="py-2 px-3 text-left hidden md:table-cell">Deskripsi</th>
                                    <th class="py-2 px-3 text-center">P</th>
                                    <th class="py-2 px-3 text-center">F</th>
                                    <th class="py-2 px-3 text-center">R</th>
                                    <th class="py-2 px-3 text-center">OP</th>
                                    <th class="py-2 px-3 text-center">NR</th>
                                    <th class="py-2 px-3 text-left w-48">Catatan</th>
                                    <th class="py-2 px-3 text-left">Tanda</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                `;
                const tbody = wrap.querySelector('tbody');

                items.forEach(p => {
                    const pid = p.id;
                    const prevS = previousState?.[pid] || {};
                    const prevM = previousMapJSON[pid] || {};

                    // Prioritas: state saat ini > prefill histori
                    const enabled = prevS.enabled ?? (!!prevM.result_code) ?? false;
                    const code = prevS.code ?? prevM.result_code ?? null;
                    const note = prevS.note ?? prevM.note ?? '';

                    // Hitung bucket untuk badge usia
                    const p25 = p.percent_25,
                        p100 = p.percent_100;
                    let bkt = 'NOT_YET';
                    if (p25 == null || p100 == null) bkt = 'NOT_YET';
                    else if (age < p25) bkt = 'NOT_YET';
                    else if (age > p100) bkt = 'OVERDUE';
                    else if (age === p25 || age === p100) bkt = 'AT_LINE';
                    else bkt = 'IN_WINDOW';

                    const passed = code === 'P';

                    const tr = document.createElement('tr');
                    tr.className = 'border-t border-gray-200 dark:border-gray-700 param-row';
                    tr.setAttribute('data-cat', `cat-${slug}`);
                    tr.setAttribute('data-text',
                        `${(p.name || p.test_element_name || '')} ${(p.description || p.test_element_description || '')}`
                        .toLowerCase());
                    tr.setAttribute('data-param-id', pid);

                    const name = p.name ?? p.test_element_name ?? '-';
                    const desc = p.description ?? p.test_element_description ?? '-';

                    tr.innerHTML = `
                        <td class="py-2 px-3 align-top">
                            <input type="checkbox" class="chk-include" data-row="${rowIndex}" ${enabled ? 'checked' : ''}>
                        </td>
                        <td class="py-2 px-3 align-top font-medium">
                            <div class="space-y-1">
                                <div>${escapeHtml(name)}</div>
                                ${desc ? `<div class="md:hidden text-[11px] text-gray-500">${escapeHtml(desc).slice(0, 80)}</div>` : ''}
                                <div class="text-[10px] text-gray-500">
                                    25/50/75/100:
                                    ${(p.percent_25 ?? '—')}/${(p.percent_50 ?? '—')}/${(p.percent_75 ?? '—')}/${(p.percent_100 ?? '—')}
                                </div>
                            </div>
                        </td>
                        <td class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                            ${escapeHtml(desc)}
                        </td>

                        <input type="hidden" name="items[${rowIndex}][parameter_id]" value="${pid}" ${enabled ? '' : 'disabled'}>

                        ${['P','F','R','OP','NR'].map(opt => `
                                <td class="py-2 px-3 text-center">
                                    <input type="radio" name="items[${rowIndex}][result_code]" value="${opt}" ${enabled ? '' : 'disabled'} ${code===opt?'checked':''}>
                                </td>
                            `).join('')}

                        <td class="py-2 px-3">
                            <input type="text" name="items[${rowIndex}][note]" value="${escapeAttr(note)}" ${enabled ? '' : 'disabled'}
                                   class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-900 dark:border-gray-700 note-input">
                        </td>

                        <td class="py-2 px-3 text-[11px] md:text-xs">
                            <div class="flex flex-wrap gap-1 items-center">
                                <span class="px-2 py-0.5 rounded bucket-badge ${bucketClass(bkt, passed)}">${bucketText(bkt, passed)}</span>
                                <span class="px-2 py-0.5 rounded result-badge ${resultClass(code)}">${resultText(code)}</span>
                            </div>
                        </td>
                    `;

                    tbody.appendChild(tr);
                    wireRowToggle(tr);
                    wireNoteAndRadio(tr);
                    rowIndex++;
                });

                paramContainer.appendChild(wrap);

                // rebinding toggle per kategori
                wrap.querySelector('.toggle-all-in-cat')?.addEventListener('change', (e) => {
                    const cat = e.target.dataset.target;
                    $$(`tr[data-cat="${cat}"] .chk-include`).forEach(chk => {
                        chk.checked = e.target.checked;
                        chk.dispatchEvent(new Event('change'));
                    });
                });
            });
        }

        function resultText(code) {
            switch (code) {
                case 'P':
                    return 'LULUS';
                case 'F':
                    return 'GAGAL';
                case 'R':
                    return 'ULANG';
                case 'NR':
                    return 'TIDAK WAJIB';
                case 'OP':
                    return 'BELUM';
                default:
                    return '—';
            }
        }

        function resultClass(code) {
            switch (code) {
                case 'P':
                    return 'bg-green-100 text-green-700';
                case 'F':
                    return 'bg-red-100 text-red-700';
                case 'R':
                    return 'bg-yellow-100 text-yellow-700';
                case 'NR':
                    return 'bg-purple-100 text-purple-700';
                case 'OP':
                    return 'bg-gray-200 text-gray-700';
                default:
                    return 'bg-gray-200 text-gray-700';
            }
        }

        function slugify(s) {
            return (s || '').toString().toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }

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

        // ========= Submit =========
        function confirmSubmit() {
            const anyChecked = $$('.chk-include').some(c => c.checked);
            if (!anyChecked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada item',
                    text: 'Silakan pilih minimal 1 item.'
                });
                return;
            }
            const invalid = $$('.param-row').some(row => {
                const chk = row.querySelector('.chk-include');
                if (!chk?.checked) return false;
                const rowIdx = chk.dataset.row;
                return !row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`);
            });
            if (invalid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nilai belum lengkap',
                    text: 'Pilih P/F/R/OP/NR untuk item yang dicentang.'
                });
                return;
            }
            Swal.fire({
                title: 'Simpan Penilaian?',
                text: 'Pastikan data sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('assessment-form').submit();
                }
            });
        }
        window.confirmSubmit = confirmSubmit;

        // Build ringkasan awal (dari prefill)
        document.addEventListener('DOMContentLoaded', buildMainNotes);
    </script>
</x-app-layout>
