<x-app-layout>
    <x-slot:title>Riwayat MMDST — {{ $student->student_name }}</x-slot:title>

    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold">
                {{ $student->student_name }}
                <span class="text-gray-400">({{ $student->student_number }})</span>
            </h1>
            <p class="text-sm text-gray-500">
                Lahir: {{ optional($student->birth_date)->format('d M Y') ?? '—' }} •
                Gender:
                @if (is_null($student->gender))
                    —
                @else
                    {{ $student->gender ? 'Laki-laki' : 'Perempuan' }}
                @endif
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('mmdst.index') }}"
                class="inline-flex items-center gap-1 border rounded-lg px-3 md:px-4 h-10">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Kembali</span>
            </a>

            <button type="button"
                class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white rounded-lg px-3 md:px-4 h-10"
                onclick="autoReport({{ $student->id }})">
                <span class="material-symbols-outlined text-base">add_task</span>
                <span>Buat Laporan</span>
            </button>
        </div>
    </div>

    <div class="p-4 md:p-6 bg-white dark:bg-gray-900 rounded-md shadow">
        {{-- Filter bar: mobile-first grid, rapi dan tidak overflow --}}
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
            if (result === 'ABNORMAL') {
                cls = 'bg-red-100 text-red-700';
            } else if (result === 'QUESTIONABLE') {
                cls = 'bg-yellow-100 text-yellow-700';
            } else if (result === 'UNTESTABLE') {
                cls = 'bg-gray-200 text-gray-700';
            }
            return `<span class="px-2 py-1 rounded text-xs font-semibold ${cls}">${label}</span>`;
        }

        async function loadHistory() {
            const base = @json(route('mmdst.history.data', $student));
            const url = new URL(base);
            if (qEl.value) url.searchParams.set('q', qEl.value);
            if (fromEl.value) url.searchParams.set('from', fromEl.value);
            if (toEl.value) url.searchParams.set('to', toEl.value);

            try {
                const res = await fetch(url);
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

        // Buat laporan otomatis
        async function autoReport(studentId) {
            const url = @json(url('/mmdst')) + `/${studentId}/auto-report`;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf(),
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                if (!res.ok || json.ok === false) throw new Error(json.message || 'Gagal membuat laporan.');
                Swal.fire({
                    icon: 'success',
                    title: 'Laporan dibuat',
                    text: 'Mengalihkan ke halaman edit…',
                    timer: 1200,
                    showConfirmButton: false
                });
                setTimeout(() => {
                    window.location.href = json.edit_url;
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
        loadHistory();
    </script>
</x-app-layout>
