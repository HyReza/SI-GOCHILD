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
        // Safety fallback nama variabel dari controller
        $selectedStudent = $selectedStudent ?? ($student ?? null);
        $studentIsNormal = isset($studentIsNormal) ? (bool) $studentIsNormal : true;
        $ageInDays = isset($ageInDays) ? (int) $ageInDays : 0;
        $filtered_categories = $filtered_categories ?? collect();
        $previousMap = $previousMap ?? collect(); // [param_id => ['result_code' => 'P', 'note' => '...']]
        $todayISO = now()->toDateString();

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
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1">Siswa</label>
                    <select name="student_id" id="student_id"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700" required>
                        <option value="">— Pilih Siswa —</option>
                        @foreach ($students as $s)
                            <option value="{{ $s->id }}" @selected(old('student_id', optional($selectedStudent)->id) == $s->id)>
                                {{ $s->student_name }} ({{ $s->student_number }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Status: <b id="student-normal-badge">{{ $studentIsNormal ? 'Normal' : 'Kebutuhan Khusus' }}</b>
                    </p>
                </div>
                <div>
                    <label class="block text-sm mb-1">Tanggal Penilaian</label>
                    <input type="date" name="assessment_date" id="assessment_date"
                        value="{{ old('assessment_date', $todayISO) }}"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700" required>
                    <p class="text-[11px] text-gray-500 mt-1">Usia: <b id="age-label">{{ $ageInDays }}</b> hari</p>
                </div>
                <div class="flex items-end gap-2">
                    <label class="inline-flex items-center gap-2 text-xs">
                        <input type="checkbox" id="auto-summary-toggle" checked>
                        <span>Auto ringkas catatan parameter ke “Catatan utama”</span>
                    </label>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-sm mb-1">Catatan utama (otomatis terisi dari catatan per parameter)</label>
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
                                        $exist = $previousMap[$p->id] ?? null;
                                        $checked = $exist ? true : false;
                                        $code = $exist['result_code'] ?? null;
                                        $note = $exist['note'] ?? '';
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

                                        {{-- Hidden parameter_id --}}
                                        <input type="hidden" name="items[{{ $rowIndex }}][parameter_id]"
                                            value="{{ $p->id }}" {{ $checked ? '' : 'disabled' }}>

                                        {{-- Radios P,F,R,OP,NR --}}
                                        @foreach (['P', 'F', 'R', 'OP', 'NR'] as $opt)
                                            <td class="py-2 px-3 text-center">
                                                <input type="radio" name="items[{{ $rowIndex }}][result_code]"
                                                    value="{{ $opt }}" {{ $checked ? '' : 'disabled' }}
                                                    @checked($code === $opt)>
                                            </td>
                                        @endforeach

                                        {{-- Notes --}}
                                        <td class="py-2 px-3">
                                            <input type="text" name="items[{{ $rowIndex }}][note]"
                                                value="{{ $note }}" {{ $checked ? '' : 'disabled' }}
                                                class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-900 dark:border-gray-700 note-input">
                                        </td>

                                        {{-- Badge --}}
                                        <td class="py-2 px-3 text-[11px] md:text-xs">
                                            <span
                                                class="px-2 py-0.5 rounded {{ $bucketBadge }}">{{ $bucketTextId }}</span>
                                        </td>
                                    </tr>
                                    @php $rowIndex++; @endphp
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-4 px-3 text-center text-gray-500">
                                            Tidak ada item relevan untuk usia ini.
                                        </td>
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
        // ===== Helpers
        const csrf = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const $ = (sel, ctx = document) => ctx.querySelector(sel);
        const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
        const routeFilterParams = @json(route('mmdst.filter-params'));

        // ===== UI toggles per-row
        function wireRowToggle(row) {
            const chk = row.querySelector('.chk-include');
            if (!chk) return;
            const rowIdx = chk.dataset.row;
            const toggle = () => {
                row.querySelectorAll(
                    `input[name="items[${rowIdx}][parameter_id]"], input[name="items[${rowIdx}][result_code]"], input[name="items[${rowIdx}][note]"]`
                ).forEach(el => el.disabled = !chk.checked);
            };
            toggle();
            chk.addEventListener('change', () => {
                toggle();
                buildMainNotes(); // refresh ringkasan saat aktif/nonaktif baris
            });
        }
        $$('.param-row').forEach(wireRowToggle);

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

        // Pencarian (filter baris)
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

        // ===== Ringkas catatan parametrik → catatan utama
        const autoToggle = $('#auto-summary-toggle');
        const mainNotes = $('#main-notes');

        function buildMainNotes() {
            if (!autoToggle.checked) return;
            const blocksByCategory = {};
            $$('.param-row').forEach(row => {
                const rowIdx = row.querySelector('.chk-include')?.dataset.row;
                const enabled = row.querySelector(`input[name="items[${rowIdx}][parameter_id]"]`)?.disabled ===
                    false;
                if (!enabled) return;

                const noteEl = row.querySelector(`input[name="items[${rowIdx}][note]"]`);
                const note = (noteEl?.value || '').trim();
                if (!note) return;

                // ambil info param
                const catWrap = row.closest('.border.rounded-md');
                const catName = catWrap?.querySelector('.font-medium')?.textContent?.trim() || 'Kategori';
                const paramName = row.querySelector('td:nth-child(2) div > div:first-child')?.textContent?.trim() ||
                    'Unsur';
                const code = row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value || '-';

                if (!blocksByCategory[catName]) blocksByCategory[catName] = [];
                blocksByCategory[catName].push(`- ${paramName} [${code}]: ${note}`);
            });

            // Gabung jadi teks
            const parts = [];
            Object.keys(blocksByCategory).forEach(cat => {
                parts.push(`${cat}:\n${blocksByCategory[cat].join('\n')}`);
            });
            mainNotes.value = parts.join('\n\n');
        }

        // bind input notes
        function wireNoteInput(row) {
            const rowIdx = row.querySelector('.chk-include')?.dataset.row;
            const noteEl = row.querySelector(`input[name="items[${rowIdx}][note]"]`);
            if (noteEl) noteEl.addEventListener('input', buildMainNotes);
            // juga saat pilih radio
            $$(`input[name="items[${rowIdx}][result_code]"]`, row).forEach(r => {
                r.addEventListener('change', buildMainNotes);
            });
        }
        $$('.param-row').forEach(wireNoteInput);

        // Jika user mematikan auto ringkas, kita tidak overwrite
        autoToggle.addEventListener('change', () => {
            if (autoToggle.checked) buildMainNotes();
        });

        // ===== Re-render tabel saat tanggal berubah (khusus normal = true)
        const studentSel = $('#student_id');
        const assessmentDate = $('#assessment_date');
        const ageLabel = $('#age-label');
        const paramContainer = $('#param-container');
        const studentNormalBadge = $('#student-normal-badge');

        async function reloadParamsIfNeeded() {
            const studentId = studentSel.value;
            if (!studentId) return;
            // Hanya auto-filter saat NORMAL. Server bisa ikut menentukan.
            const isNormal = (studentNormalBadge.textContent || '').toLowerCase().includes('normal');
            const dateVal = assessmentDate.value;

            const currentState = captureCurrentState();

            try {
                const url = new URL(routeFilterParams, window.location.origin);
                url.searchParams.set('student_id', studentId);
                if (dateVal) url.searchParams.set('assessment_date', dateVal);
                // url.searchParams.set('date', dateVal);

                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });

                const json = await res.json();
                if (!res.ok || json.ok === false) throw new Error(json.message || 'Gagal memuat parameter.');

                // Update usia label
                ageLabel.textContent = json.age_in_days ?? '-';

                // Render ulang kategori/parameter dari server (json.filtered_categories)
                renderCategories(json.filtered_categories || {}, currentState);

                // Re-apply search filter yang sedang aktif
                filterInput?.dispatchEvent(new Event('input'));

                // rebuild auto summary
                buildMainNotes();

            } catch (e) {
                console.error(e);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: e.message || e.toString()
                });
            }
        }

        assessmentDate.addEventListener('change', reloadParamsIfNeeded);

        // Simpan kondisi saat ini (ceklist, radio, note) biar tidak hilang saat refresh
        function captureCurrentState() {
            const state = {};
            $$('.param-row').forEach(row => {
                const pid = row.getAttribute('data-param-id');
                const rowIdx = row.querySelector('.chk-include')?.dataset.row;
                const enabled = row.querySelector(`input[name="items[${rowIdx}][parameter_id]"]`)?.disabled ===
                    false;

                state[pid] = {
                    enabled,
                    code: row.querySelector(`input[name="items[${rowIdx}][result_code]"]:checked`)?.value ||
                        null,
                    note: row.querySelector(`input[name="items[${rowIdx}][note]"]`)?.value || ''
                };
            });
            return state;
        }

        // Render ulang kategori + baris berdasarkan data JSON dari server
        function renderCategories(filteredCategories, previousState) {
            // Bersihkan kontainer
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

            // filteredCategories: { "Nama Kategori": [ {id, test_element_name, ...} ] }
            Object.keys(filteredCategories).forEach(catName => {
                const slug = slugify(catName);
                const params = filteredCategories[catName] || [];

                // Kartu kategori
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

                params.forEach(p => {
                    const pid = p.id;
                    const prev = previousState?.[pid] || {};
                    const prefill = @json($previousMap->toArray());
                    const prevMap = prefill[pid] || {};

                    // Ambil prioritas prefill: previousState (user ketik sekarang) > previousMap (riwayat lama)
                    const enabled = prev.enabled ?? !!prevMap.result_code ?? false;
                    const code = prev.code ?? prevMap.result_code ?? null;
                    const note = prev.note ?? prevMap.note ?? '';

                    const age = parseInt($('#age-label').textContent || '0', 10) || 0;
                    const bkt = (function() {
                        const p25 = p.percent_25,
                            p100 = p.percent_100;
                        if (p25 == null || p100 == null) return 'NOT_YET';
                        if (age < p25) return 'NOT_YET';
                        if (age > p100) return 'OVERDUE';
                        if (age === p25 || age === p100) return 'AT_LINE';
                        return 'IN_WINDOW';
                    })();
                    const passed = code === 'P';

                    const tr = document.createElement('tr');
                    tr.className = 'border-t border-gray-200 dark:border-gray-700 param-row';
                    tr.setAttribute('data-cat', `cat-${slug}`);
                    tr.setAttribute('data-text',
                        `${(p.test_element_name || '')} ${(p.test_element_description || '')}`
                        .toLowerCase());
                    tr.setAttribute('data-param-id', pid);

                    tr.innerHTML = `
                        <td class="py-2 px-3 align-top">
                            <input type="checkbox" class="chk-include" data-row="${rowIndex}" ${enabled ? 'checked' : ''}>
                        </td>
                        <td class="py-2 px-3 align-top font-medium">
                            <div class="space-y-1">
                                <div>${p.test_element_name || '-'}</div>
                                ${p.test_element_description ? `<div class="md:hidden text-[11px] text-gray-500">${escapeHtml(p.test_element_description).slice(0,80)}</div>` : ''}
                                <div class="text-[10px] text-gray-500">
                                    25/50/75/100:
                                    ${p.percent_25 ?? '—'}/${p.percent_50 ?? '—'}/${p.percent_75 ?? '—'}/${p.percent_100 ?? '—'}
                                </div>
                            </div>
                        </td>
                        <td class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                            ${escapeHtml(p.test_element_description || '-')}
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
                            <span class="px-2 py-0.5 rounded ${bucketClass(bkt, passed)}">${bucketText(bkt, passed)}</span>
                        </td>
                    `;

                    tbody.appendChild(tr);
                    wireRowToggle(tr);
                    wireNoteInput(tr);
                    rowIndex++;
                });

                paramContainer.appendChild(wrap);
            });

            // Re-bind kategori toggler
            $$('.toggle-all-in-cat').forEach(tg => {
                tg.addEventListener('change', () => {
                    const cat = tg.dataset.target;
                    $$(`tr[data-cat="${cat}"] .chk-include`).forEach(chk => {
                        chk.checked = tg.checked;
                        chk.dispatchEvent(new Event('change'));
                    });
                });
            });
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

        // ===== Submit
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
            // (opsional) pastikan ada radio terpilih untuk yang dicentang
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

        // Build initial main notes (prefill dari previousMap yang sudah tercentang)
        document.addEventListener('DOMContentLoaded', buildMainNotes);
    </script>
</x-app-layout>
