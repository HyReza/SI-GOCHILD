<x-app-layout>
    @php
        // ========== Self-healing $student ==========
        // Kalau controller lupa nge-pass $student, ambil dari parameter route {student}
        // (bisa berupa model binding atau sekadar ID), lalu fetch model bila perlu.
        if (!isset($student)) {
            $routeParam = request()->route('student') ?? null;
            if ($routeParam instanceof \App\Models\Student) {
                $student = $routeParam;
            } elseif (!is_null($routeParam)) {
                try {
                    $student = \App\Models\Student::find($routeParam);
                } catch (\Throwable $e) {
                    $student = null;
                }
            } else {
                $student = null;
            }
        }

        // Helper tampilan
        $studentName = $student?->student_name ?? '—';
        $studentNumber = $student?->student_number ?? '—';
        $birthDateTxt =
            $student && $student->birth_date
                ? \Illuminate\Support\Carbon::parse($student->birth_date)->format('d M Y')
                : '—';
        $genderTxt = is_null($student?->gender) ? '—' : ($student->gender ? 'Laki-laki' : 'Perempuan');
    @endphp

    <x-slot:title>Riwayat MMDST — {{ $studentName }}</x-slot:title>

    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold">
                {{ $studentName }}
                <span class="text-gray-400">({{ $studentNumber }})</span>
            </h1>
            <p class="text-sm text-gray-500">
                Lahir: {{ $birthDateTxt }} • Gender: {{ $genderTxt }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('mmdst.index') }}"
                class="inline-flex items-center gap-1 border rounded-lg px-3 md:px-4 h-10">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Kembali</span>
            </a>

            @if ($student)
                <button type="button"
                    class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white rounded-lg px-3 md:px-4 h-10"
                    onclick="autoReport({{ $student->id }})">
                    <span class="material-symbols-outlined text-base">add_task</span>
                    <span>Buat Laporan</span>
                </button>
            @endif
        </div>
    </div>

    <div class="p-4 md:p-6 bg-white dark:bg-gray-900 rounded-md shadow">
        {{-- Filter bar --}}
        <div class="space-y-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                <div class="flex flex-col">
                    <label for="hq" class="text-xs text-gray-500 mb-1">Cari hasil/catatan</label>
                    <input id="hq" type="text" placeholder="Ketik untuk mencari…"
                        class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700">
                </div>

                <div class="flex flex-col">
                    <label for="from" class="text-xs text-gray-500 mb-1">Dari tanggal</label>
                    <input id="from" type="date"
                        class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700">
                </div>

                <div class="flex flex-col">
                    <label for="to" class="text-xs text-gray-500 mb-1">Sampai tanggal</label>
                    <input id="to" type="date"
                        class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700">
                </div>
            </div>

            <div class="flex items-center justify-between text-[11px] md:text-xs text-gray-500">
                <span>Ketik pada kolom untuk memfilter data secara langsung.</span>
                <button type="button" id="btn-reset"
                    class="inline-flex items-center gap-1 px-2 py-1 rounded border text-gray-700 dark:text-gray-200">
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs md:text-sm">
                    <tr>
                        <th class="py-2 px-3 text-left">Tanggal</th>
                        <th class="py-2 px-3 text-left">Usia (hari)</th>
                        <th class="py-2 px-3 text-left">Hasil</th>
                        <th class="py-2 px-3 text-left">Petugas</th>
                        <th class="py-2 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rows" class="text-sm"></tbody>
            </table>
            <p id="empty" class="text-center text-gray-500 py-6">Memuat…</p>
        </div>
    </div>

    <script>
        const csrf = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const rowsEl = document.getElementById('rows');
        const emptyEl = document.getElementById('empty');
        const qEl = document.getElementById('hq');
        const fromEl = document.getElementById('from');
        const toEl = document.getElementById('to');
        const btnReset = document.getElementById('btn-reset');

        function resultBadge(result) {
            let label = result ?? '—';
            let cls = 'bg-green-100 text-green-700';
            if (result === 'ABNORMAL') cls = 'bg-red-100 text-red-700';
            else if (result === 'QUESTIONABLE') cls = 'bg-yellow-100 text-yellow-700';
            else if (result === 'UNTESTABLE') cls = 'bg-gray-200 text-gray-700';
            return `<span class="px-2 py-1 rounded text-xs font-semibold ${cls}">${label}</span>`;
        }

        // Bangun URL data dari path saat ini: /mmdst/{id}/history -> /mmdst/{id}/history/data
        function historyDataUrl() {
            const base = window.location.origin + window.location.pathname.replace(/\/+$/, '') + '/data';
            const url = new URL(base);
            if (qEl.value) url.searchParams.set('q', qEl.value);
            if (fromEl.value) url.searchParams.set('from', fromEl.value);
            if (toEl.value) url.searchParams.set('to', toEl.value);
            return url.toString();
        }

        async function loadHistory() {
            try {
                const res = await fetch(historyDataUrl(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Gagal memuat data (' + res.status + ')');
                const json = await res.json();

                rowsEl.innerHTML = '';
                if (!json.data || json.data.length === 0) {
                    emptyEl.textContent = 'Tidak ada data.';
                    emptyEl.classList.remove('hidden');
                    return;
                }
                emptyEl.classList.add('hidden');

                json.data.forEach(a => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b border-gray-200 dark:border-gray-700';
                    tr.innerHTML = `
                        <td class="py-2 px-3">${a.date}</td>
                        <td class="py-2 px-3">${a.age_in_days}</td>
                        <td class="py-2 px-3">${resultBadge(a.overall_result)}</td>
                        <td class="py-2 px-3">${a.created_by ?? '-'}</td>
                        <td class="py-2 px-3">
                            <div class="flex gap-2 justify-center">
                                <a href="${a.show_url}"
                                   class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    <span>Detail</span>
                                </a>
                                <a href="${a.edit_url}"
                                   class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                    <span class="material-symbols-outlined text-sm">edit_square</span>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </td>
                    `;
                    rowsEl.appendChild(tr);
                });
            } catch (e) {
                rowsEl.innerHTML = '';
                emptyEl.textContent = e.message || 'Gagal memuat data';
                emptyEl.classList.remove('hidden');
            }
        }

        const debounce = (fn, ms = 300) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), ms);
            };
        };

        // Live filtering
        [qEl, fromEl, toEl].forEach(el => el.addEventListener('input', debounce(loadHistory, 250)));

        // Reset filter
        btnReset.addEventListener('click', () => {
            qEl.value = '';
            fromEl.value = '';
            toEl.value = '';
            loadHistory();
            qEl.focus();
        });

        // Buat laporan otomatis (ke endpoint yang sudah kamu miliki)
        async function autoReport(studentId) {
            // endpoint baru -> start-report
            const url = @json(url('/mmdst')) + `/${studentId}/start-report`;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const json = await res.json();

                if (!res.ok || json.ok === false) {
                    throw new Error(json.message || 'Gagal membuat laporan.');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Laporan dibuat',
                    text: 'Mengalihkan ke halaman tambah (create)…',
                    timer: 1200,
                    showConfirmButton: false
                });

                // gunakan create_url (bukan edit_url)
                setTimeout(() => {
                    window.location.href = json.create_url;
                }, 800);

            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: e.message || e.toString()
                });
            }
        }

        // init
        document.addEventListener('DOMContentLoaded', loadHistory);
        window.autoReport = autoReport;
    </script>
</x-app-layout>
