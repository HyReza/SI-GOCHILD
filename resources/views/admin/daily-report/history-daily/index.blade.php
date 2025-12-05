<x-app-layout>
    <x-slot:title>History Laporan Harian</x-slot:title>

    <div class="max-w-6xl mx-auto mt-6 space-y-6">
        {{-- Header Siswa / Layanan --}}
        <div class="bg-white dark:bg-gray-900 p-4 sm:p-6 rounded-lg shadow">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $tx->student->student_name ?? 'Siswa' }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        NIS: {{ $tx->student->student_number ?? '-' }} • Layanan:
                        {{ $tx->service->service_name ?? '-' }}
                    </p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Activity Transaction ID: {{ $tx->id }}
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white dark:bg-gray-900 p-4 sm:p-6 rounded-lg shadow">
            <form id="historyFilter" onsubmit="return false;" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
                {{-- Cari teks --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cari (Catatan/Stimulasi)</label>
                    <input type="text" id="q" name="q" placeholder="Ketik untuk mencari..."
                        class="w-full h-10 border bg-white dark:bg-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 rounded-md px-3">
                </div>

                {{-- Tanggal dari --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Dari Tanggal</label>
                    <input type="date" id="date_from" name="date_from"
                        class="w-full h-10 border bg-white dark:bg-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 rounded-md px-3">
                </div>

                {{-- Tanggal sampai --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                    <input type="date" id="date_to" name="date_to"
                        class="w-full h-10 border bg-white dark:bg-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 rounded-md px-3">
                </div>

                {{-- Kesehatan --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Kesehatan</label>
                    <select id="health_status" name="health_status"
                        class="w-full h-10 border bg-white dark:bg-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 rounded-md px-3">
                        <option value="">Semua</option>
                        <option value="sehat">Sehat</option>
                        <option value="sakit">Sakit</option>
                    </select>
                </div>

                {{-- Kondisi --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Kondisi</label>
                    <select id="condition" name="condition"
                        class="w-full h-10 border bg-white dark:bg-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 rounded-md px-3">
                        <option value="">Semua</option>
                        <option value="tenang">Tenang</option>
                        <option value="rewel">Rewel</option>
                        <option value="temper tantrum">Temper Tantrum</option>
                    </select>
                </div>

                {{-- Aksi --}}
                <div class="lg:col-span-6 flex items-center gap-2">
                    <button type="button" id="btnApply"
                        class="bg-indigo-600 text-white px-4 h-10 rounded-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">search</span>
                        Terapkan
                    </button>

                    <button type="button" id="btnReset"
                        class="bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-4 h-10 rounded-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">restart_alt</span>
                        Reset
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel History --}}
        <div class="bg-white dark:bg-gray-900 p-0 sm:p-0 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Tanggal</th>
                            <th class="py-3 px-4 text-left">Datang</th>
                            <th class="py-3 px-4 text-left">Pulang</th>
                            <th class="py-3 px-4 text-left">Suhu</th>
                            <th class="py-3 px-4 text-left">Makan Pagi</th>
                            <th class="py-3 px-4 text-left">Kesehatan</th>
                            <th class="py-3 px-4 text-left">Kondisi</th>
                            <th class="py-3 px-4 text-left hidden lg:table-cell">Stimulasi (ringkas)</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="history-body" class="text-gray-700 dark:text-gray-200 text-sm">
                        @include('admin.daily-report.history-daily._rows', [
                            'reports' => $reports,
                            'tx' => $tx,
                        ])
                    </tbody>
                </table>
            </div>

            <div id="history-pagination" class="p-4 border-t border-gray-200 dark:border-gray-800">
                @include('admin.daily-report.history-daily._pagination', [
                    'reports' => $reports,
                    'tx' => $tx,
                ])
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Live filter & pagination (AJAX) --}}
    <script>
        const $q = document.getElementById('q');
        const $from = document.getElementById('date_from');
        const $to = document.getElementById('date_to');
        const $hs = document.getElementById('health_status');
        const $cond = document.getElementById('condition');
        const $apply = document.getElementById('btnApply');
        const $reset = document.getElementById('btnReset');

        const $body = document.getElementById('history-body');
        const $pager = document.getElementById('history-pagination');

        let typingTimer = null;
        const DEBOUNCE = 350;

        function buildParams(page = 1) {
            const params = new URLSearchParams();
            if (($q.value || '').trim()) params.set('q', $q.value.trim());
            if ($from.value) params.set('date_from', $from.value);
            if ($to.value) params.set('date_to', $to.value);
            if ($hs.value) params.set('health_status', $hs.value);
            if ($cond.value) params.set('condition', $cond.value);
            params.set('page', page);
            params.set('partial', '1');
            return params;
        }

        function setLoading(isLoading) {
            const wrapper = $body.closest('.bg-white, .dark\\:bg-gray-900');
            if (!wrapper) return;
            wrapper.style.opacity = isLoading ? '0.6' : '1';
            wrapper.style.pointerEvents = isLoading ? 'none' : 'auto';
        }

        async function fetchHistory(page = 1) {
            setLoading(true);
            try {
                const url = `{{ route('daily-report.history', $tx->id) }}?` + buildParams(page).toString();
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                $body.innerHTML = data.rows || '';
                $pager.innerHTML = data.pagination || '';

                // Update URL (tanpa partial=1)
                const params = buildParams(page);
                params.delete('partial');
                history.replaceState({}, '', `?${params.toString()}`);
            } catch (e) {
                console.error(e);
            } finally {
                setLoading(false);
            }
        }

        function scheduleFetch() {
            if (typingTimer) clearTimeout(typingTimer);
            typingTimer = setTimeout(() => fetchHistory(1), DEBOUNCE);
        }

        // Events
        $q.addEventListener('input', scheduleFetch);
        $from.addEventListener('change', () => fetchHistory(1));
        $to.addEventListener('change', () => fetchHistory(1));
        $hs.addEventListener('change', () => fetchHistory(1));
        $cond.addEventListener('change', () => fetchHistory(1));
        $apply.addEventListener('click', () => fetchHistory(1));

        $reset.addEventListener('click', () => {
            $q.value = '';
            $from.value = '';
            $to.value = '';
            $hs.value = '';
            $cond.value = '';
            fetchHistory(1);
        });

        // Intercept pagination clicks
        $pager.addEventListener('click', function(e) {
            const a = e.target.closest('a');
            if (!a) return;
            const url = new URL(a.href);
            const page = url.searchParams.get('page') || 1;
            e.preventDefault();
            fetchHistory(page);
        });

        $body.addEventListener('click', async function(e) {
            const btn = e.target.closest('.btn-delete-report');
            if (!btn) return;

            e.preventDefault();

            const id = btn.dataset.id;
            const period = btn.dataset.period || '-';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const confirm = await Swal.fire({
                title: 'Hapus laporan?',
                html: `Tanggal: <b>${period}</b><br>Data akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            });

            if (!confirm.isConfirmed) return;

            try {
                const resp = await fetch(`{{ url('/daily-report') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                if (!resp.ok) throw new Error('Request failed');

                const data = await resp.json();
                await Swal.fire({
                    icon: 'success',
                    title: 'Terhapus',
                    text: data.message || 'Laporan dihapus.'
                });

                // reload current page (ambil dari querystring, default 1)
                const current = new URLSearchParams(location.search);
                const page = current.get('page') || 1;
                fetchHistory(page);
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal menghapus laporan.'
                });
            }
        });
    </script>
</x-app-layout>
