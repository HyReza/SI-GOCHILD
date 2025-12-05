<x-app-layout>
    <x-slot:title>Tambah Penilaian MMDST</x-slot:title>

    {{-- SweetAlert Error --}}
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json($errors - > first())
            });
        });
    </script>
    @endif

    <form id="assessment-form" action="{{ route('mmdst-assessments.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Data Dasar --}}
        <div class="p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <h2 class="font-semibold mb-4">Data Penilaian</h2>
            <div class="grid md:grid-cols-3 gap-4">
                {{-- Student fixed (bukan dropdown) --}}
                <div class="md:col-span-1">
                    <label class="block text-sm mb-1">Siswa</label>
                    <div class="h-10 flex items-center px-3 border rounded-lg dark:bg-gray-900 dark:border-gray-700">
                        <span class="font-medium">
                            {{ $selectedStudent?->student_name ?? '—' }}
                        </span>
                    </div>
                    <input type="hidden" name="student_id" id="student_id" value="{{ $selectedStudent?->id }}">
                    @if($selectedStudent)
                    <div class="text-[11px] text-gray-500 mt-1">
                        NIS: {{ $selectedStudent->student_number ?? '—' }} •
                        Tgl Lahir: {{ optional($selectedStudent->birth_date)->format('d M Y') ?? '—' }}
                    </div>
                    @endif
                </div>

                {{-- Tanggal penilaian --}}
                <div>
                    <label class="block text-sm mb-1">Tanggal Penilaian</label>
                    <input type="date" name="assessment_date" id="assessment_date"
                        value="{{ old('assessment_date', $assessmentDate) }}"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700" required>
                    <div id="age_hint" class="text-[11px] text-gray-500 mt-1">
                        Umur (hari): <span id="age_in_days">{{ (int)$ageInDays }}</span>
                        • Status: <span id="status_is_normal">{{ $studentIsNormal ? 'Normal' : 'Kebutuhan Khusus' }}</span>
                    </div>
                </div>

                {{-- Catatan utama (auto-resume) --}}
                <div>
                    <label class="block text-sm mb-1">Catatan Utama (otomatis dirangkum)</label>
                    <textarea name="notes" id="main_notes" rows="3"
                        class="w-full border rounded-lg px-3 py-2 dark:bg-gray-900 dark:border-gray-700"
                        placeholder="Catatan umum/otomatis dari item akan muncul di sini...">{{ old('notes') }}</textarea>
                    <div class="text-[11px] text-gray-500 mt-1">Ketik catatan di kolom item; ringkasan akan terbentuk real-time di sini.</div>
                </div>
            </div>
        </div>

        {{-- KETERANGAN / LEGENDA --}}
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
                    <li><b>NR (Not Required / Tidak Wajib Diujikan)</b>: digunakan jika parameter tidak perlu diujikan pada sesi ini.</li>
                </ul>
            </div>
        </div>

        {{-- Container Parameter (dirender dinamis) --}}
        <div id="param-container" class="space-y-6">
            {{-- Server-side initial render sebagai fallback (pakai $filtered_categories) --}}
            @php $rowIndex = 0; @endphp
            @foreach ($filtered_categories as $categoryName => $params)
            @php $slug = \Illuminate\Support\Str::slug($categoryName, '-'); @endphp
            <div class="border dark:border-gray-700 rounded-md">
                <div class="flex items-center justify-between px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-md">
                    <div class="font-medium">{{ $categoryName }}</div>
                    <label class="text-xs flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" class="toggle-all-in-cat" data-target="cat-{{ $slug }}">
                        <span>Pilih semua</span>
                    </label>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2 px-3 text-left w-14">Nilai</th>
                                <th class="py-2 px-3 text-left">Nama Unsur</th>
                                <th class="py-2 px-3 text-left hidden md:table-cell">Deskripsi</th>
                                <th class="py-2 px-3 text-center">P</th>
                                <th class="py-2 px-3 text-center">F</th>
                                <th class="py-2 px-3 text-center">R</th>
                                <th class="py-2 px-3 text-center">OP</th>
                                <th class="py-2 px-3 text-center">NR</th>
                                <th class="py-2 px-3 text-left w-56">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($params as $p)
                            <tr class="border-t border-gray-200 dark:border-gray-700 param-row" data-cat="cat-{{ $slug }}">
                                <td class="py-2 px-3 align-top">
                                    <input type="checkbox" class="chk-include" data-row="{{ $rowIndex }}">
                                </td>
                                <td class="py-2 px-3 align-top font-medium">
                                    <div class="space-y-1">
                                        <div>{{ $p->test_element_name }}</div>
                                        <div class="text-[10px] text-gray-500">
                                            Usia 25/50/75/100:
                                            {{ $p->percent_25 ?? '—' }}/{{ $p->percent_50 ?? '—' }}/{{ $p->percent_75 ?? '—' }}/{{ $p->percent_100 ?? '—' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                    {{ $p->test_element_description ?? '-' }}
                                </td>

                                {{-- Hidden parameter_id (aktif saat dicentang) --}}
                                <input type="hidden" name="items[{{ $rowIndex }}][parameter_id]" value="{{ $p->id }}" disabled>

                                @foreach (['P','F','R','OP','NR'] as $code)
                                <td class="py-2 px-3 text-center">
                                    <input type="radio" name="items[{{ $rowIndex }}][result_code]" value="{{ $code }}" disabled>
                                </td>
                                @endforeach

                                <td class="py-2 px-3">
                                    <input type="text" name="items[{{ $rowIndex }}][note]" class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-900 dark:border-gray-700" disabled>
                                </td>
                            </tr>
                            @php $rowIndex++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            {{-- Jika hasil filter kosong, tampilkan info --}}
            @if($filtered_categories->flatten(1)->count() === 0)
            <div class="p-4 text-center text-gray-500 border rounded-md">Tidak ada parameter untuk kombinasi usia & status saat ini.</div>
            @endif
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('mmdst-assessments.index') }}" class="border rounded-lg px-4 h-10 flex items-center">Batal</a>
            <button type="button" onclick="confirmSubmit()" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 h-10">Simpan</button>
        </div>
    </form>

    <script>
        const csrf = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ===== Prefill dari assessment terakhir (disimpan global) =====
        let PREV_MAP = @json($previousMap ?? collect());
        // Struktur: { paramId: { result_code: 'P|F|R|OP|NR', note: '...' } }

        // ===== Util: enable/disable per baris saat checkbox "Nilai" dicentang =====
        function wireRowToggle(container = document) {
            container.querySelectorAll('.chk-include').forEach(chk => {
                const row = chk.closest('tr');
                const rowIdx = chk.dataset.row;
                const toggle = () => {
                    row.querySelectorAll(
                        `input[name="items[${rowIdx}][parameter_id]"], input[name="items[${rowIdx}][result_code]"], input[name="items[${rowIdx}][note]"]`
                    ).forEach(el => el.disabled = !chk.checked);
                };
                toggle();
                chk.addEventListener('change', toggle);
            });
        }

        // ===== Util: toggle semua per kategori =====
        function wireToggleAll(container = document) {
            container.querySelectorAll('.toggle-all-in-cat').forEach(tg => {
                tg.addEventListener('change', () => {
                    const cat = tg.dataset.target;
                    container.querySelectorAll(`tr[data-cat="${cat}"] .chk-include`).forEach(chk => {
                        chk.checked = tg.checked;
                        chk.dispatchEvent(new Event('change'));
                    });
                });
            });
        }

        // ===== Util: build tabel dari payload AJAX =====
        function renderParams(payload) {
            // payload: { "Kategori": [ { id, name, description, percent_25... } ] }
            const container = document.getElementById('param-container');
            container.innerHTML = '';
            let rowIndex = 0;

            const catNames = Object.keys(payload || {});
            if (catNames.length === 0) {
                container.innerHTML = `<div class="p-4 text-center text-gray-500 border rounded-md">Tidak ada parameter untuk kombinasi usia & status saat ini.</div>`;
                return;
            }

            catNames.forEach(catName => {
                const slug = 'cat-' + catName.toLowerCase().replace(/\s+/g, '-');
                const items = payload[catName] || [];

                let html = `
                <div class="border dark:border-gray-700 rounded-md">
                    <div class="flex items-center justify-between px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-md">
                        <div class="font-medium">${catName}</div>
                        <label class="text-xs flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" class="toggle-all-in-cat" data-target="${slug}">
                            <span>Pilih semua</span>
                        </label>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="py-2 px-3 text-left w-14">Nilai</th>
                                    <th class="py-2 px-3 text-left">Nama Unsur</th>
                                    <th class="py-2 px-3 text-left hidden md:table-cell">Deskripsi</th>
                                    <th class="py-2 px-3 text-center">P</th>
                                    <th class="py-2 px-3 text-center">F</th>
                                    <th class="py-2 px-3 text-center">R</th>
                                    <th class="py-2 px-3 text-center">OP</th>
                                    <th class="py-2 px-3 text-center">NR</th>
                                    <th class="py-2 px-3 text-left w-56">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                items.forEach(p => {
                    const prev = PREV_MAP[p.id] || null;
                    const checked = prev ? 'checked' : '';
                    const radios = ['P', 'F', 'R', 'OP', 'NR'].map(code => {
                        const sel = prev && prev.result_code === code ? 'checked' : '';
                        return `
                            <td class="py-2 px-3 text-center">
                                <input type="radio" name="items[${rowIndex}][result_code]" value="${code}" ${prev?'':'disabled'} ${sel}>
                            </td>
                        `;
                    }).join('');

                    const noteVal = prev ? (prev.note || '') : '';
                    html += `
                        <tr class="border-t border-gray-200 dark:border-gray-700 param-row" data-cat="${slug}">
                            <td class="py-2 px-3 align-top">
                                <input type="checkbox" class="chk-include" data-row="${rowIndex}" ${checked}>
                            </td>
                            <td class="py-2 px-3 align-top font-medium">
                                <div class="space-y-1">
                                    <div>${p.name}</div>
                                    <div class="text-[10px] text-gray-500">
                                        Usia 25/50/75/100: ${p.percent_25 ?? '—'}/${p.percent_50 ?? '—'}/${p.percent_75 ?? '—'}/${p.percent_100 ?? '—'}
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                ${p.description ?? '-'}
                            </td>

                            <input type="hidden" name="items[${rowIndex}][parameter_id]" value="${p.id}" ${prev?'':'disabled'}>

                            ${radios}

                            <td class="py-2 px-3">
                                <input type="text" name="items[${rowIndex}][note]" value="${noteVal.replace(/"/g,'&quot;')}" ${prev?'':'disabled'}
                                       class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-900 dark:border-gray-700 note-input"
                                       data-param-name="${p.name}">
                            </td>
                        </tr>
                    `;
                    rowIndex++;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                </div>
                `;

                const wrapper = document.createElement('div');
                wrapper.innerHTML = html;
                container.appendChild(wrapper.firstElementChild);
            });

            // Wire events ulang setelah render
            wireRowToggle(container);
            wireToggleAll(container);
            wireNotesAutoResume();
        }

        // ===== Auto-resume notes ke Catatan Utama =====
        function wireNotesAutoResume(container = document) {
            const main = document.getElementById('main_notes');

            function rebuild() {
                const lines = [];
                document.querySelectorAll('.note-input').forEach(inp => {
                    const row = inp.closest('tr');
                    const rowIdx = inp.name.match(/items\[(\d+)\]/)?.[1];
                    const title = inp.dataset.paramName || 'Parameter';
                    const val = (inp.value || '').trim();
                    if (!val) return;

                    // ambil result code terpilih
                    let rc = '';
                    const rcs = row.querySelectorAll(`input[name="items[${rowIdx}][result_code]"]`);
                    rcs.forEach(r => {
                        if (r.checked) rc = r.value;
                    });

                    const label = rc || 'OP';
                    lines.push(`• ${title} — [${label}] ${val}`);
                });
                // jangan hapus input manual user: gabungkan uniq (di sini kita overwrite penuh agar konsisten)
                main.value = lines.join('\n');
            }

            // pasang listener input
            container.querySelectorAll('.note-input').forEach(inp => {
                inp.addEventListener('input', rebuild);
            });
            // juga saat radio berubah
            container.querySelectorAll('input[type="radio"][name*="[result_code]"]').forEach(r => {
                r.addEventListener('change', rebuild);
            });

            // build awal
            rebuild();
        }

        // ===== AJAX load berdasarkan student_id + assessment_date =====
        async function reloadParamsByDate() {
            const studentId = document.getElementById('student_id')?.value;
            const dateVal = document.getElementById('assessment_date')?.value;

            if (!studentId || !dateVal) return;

            const url = new URL(@json(route('mmdst.filter-params')), window.location.origin);
            url.searchParams.set('student_id', studentId);
            url.searchParams.set('assessment_date', dateVal);

            try {
                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });
                const json = await res.json();
                if (!res.ok || json.ok === false) throw new Error(json?.message || 'Gagal memuat parameter.');

                // update hint umur & status
                document.getElementById('age_in_days').textContent = json.age_in_days ?? '-';
                document.getElementById('status_is_normal').textContent = json.student_is_normal ? 'Normal' : 'Kebutuhan Khusus';

                // render tabel dari payload
                renderParams(json.data);

                // Prefill dari assessment terakhir (jika belum ada, biarkan PREV_MAP apa adanya)
                // (Kalau ingin refresh PREV_MAP setiap kali, boleh panggil last-results di sini)
                // await refreshPrevMap(studentId);
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: e.message || e.toString()
                });
            }
        }

        // (Opsional) ambil ulang prefill terakhir
        async function refreshPrevMap(studentId) {
            if (!studentId) return;
            const url = @json(route('mmdst.last-results', ['student' => '_ID_']));
            const final = url.replace('_ID_', studentId);
            try {
                const res = await fetch(final, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                if (res.ok && json.ok) {
                    PREV_MAP = json.items || {};
                }
            } catch (e) {}
        }

        // ===== Konfirmasi submit =====
        function confirmSubmit() {
            const anyChecked = Array.from(document.querySelectorAll('.chk-include')).some(c => c.checked);
            if (!anyChecked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada item',
                    text: 'Silakan pilih minimal 1 item untuk dinilai.'
                });
                return;
            }
            Swal.fire({
                title: 'Simpan Penilaian?',
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

        // ===== Init on DOM Ready =====
        document.addEventListener('DOMContentLoaded', async () => {
            wireRowToggle();
            wireToggleAll();
            wireNotesAutoResume();

            const studentId = document.getElementById('student_id')?.value;
            if (studentId) {
                // prefilling awal (sudah diberikan dari server via PREV_MAP)
                // saat ganti tanggal → reload params
                document.getElementById('assessment_date').addEventListener('change', reloadParamsByDate);
            }
        });
    </script>
</x-app-layout>