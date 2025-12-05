<x-app-layout>
    <x-slot:title>Management Students</x-slot:title>

    {{-- SweetAlert for Success Message --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    {{-- SweetAlert for Error Message --}}
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
            <div class="rounded-lg mt-4 lg:mt-0">
                <form id="searchForm" method="GET" onsubmit="return false;"
                    class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <!-- Search Input -->
                    <input type="text" id="searchInput" name="query" placeholder="Cari siswa..."
                        class="h-10 w-full sm:w-full lg:w-full max-w-4xl border bg-white dark:bg-gray-900 dark:text-gray-300 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500"
                        value="{{ request()->query('query') }}" />

                    <!-- Filter by Service -->
                    <select id="serviceFilter" name="service_id"
                        class="h-10 w-full sm:w-auto px-6 border bg-white dark:bg-gray-900 dark:text-gray-300 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500">
                        <option value="">Service All</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}"
                                {{ request()->query('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->service_name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Submit Button (opsional, tidak reload) -->
                    <button type="button" id="btnSearch"
                        class="bg-indigo-600 text-white py-2 px-4 rounded-md w-full sm:w-auto">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>


    <div class="col-span-12">
        <div
            class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
            <div class="overflow-x-auto w-full">
                <table class="min-w-full table-auto border-collapse mb-4">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-500 text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">NIS</th>
                            <th class="py-3 px-6 text-left">Nama Anak</th>
                            <th class="py-3 px-6 text-left">Nama Ibu</th>
                            <th class="py-3 px-6 text-left hidden lg:table-cell">Alamat</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="list-body" class="text-gray-600 text-sm font-light">
                        @include('admin.daily-report.index-daily._rows')
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="list-pagination" class="mt-4">
                @include('admin.daily-report.index-daily._pagination')
            </div>
        </div>
    </div>

    {{-- Live search JS --}}
    <script>
        const $search = document.getElementById('searchInput');
        const $service = document.getElementById('serviceFilter');
        const $btn = document.getElementById('btnSearch');
        const $body = document.getElementById('list-body');
        const $pager = document.getElementById('list-pagination');

        let typingTimer = null;
        const DEBOUNCE_MS = 350;

        function buildParams(page = 1) {
            const params = new URLSearchParams();
            const q = ($search.value || '').trim();
            const s = ($service.value || '').trim();

            if (q) params.set('query', q);
            if (s) params.set('service_id', s);
            params.set('page', page);
            params.set('partial', '1'); // supaya controller balas partial JSON
            return params;
        }

        function setLoading(isLoading) {
            const target = $body?.closest('.my-6');
            if (!target) return;
            target.style.opacity = isLoading ? '0.6' : '1';
            target.style.pointerEvents = isLoading ? 'none' : 'auto';
        }

        async function fetchList(page = 1) {
            setLoading(true);
            try {
                const url = `{{ route('daily-report.index') }}?` + buildParams(page).toString();
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                // Render rows + pagination
                $body.innerHTML = data.rows || '';
                $pager.innerHTML = data.pagination || '';

                // Update URL (tanpa reload)
                const urlParams = new URLSearchParams(buildParams(page));
                urlParams.delete('partial');
                history.replaceState({}, '', `?${urlParams.toString()}`);
            } catch (e) {
                console.error(e);
            } finally {
                setLoading(false);
            }
        }

        function scheduleFetch() {
            if (typingTimer) clearTimeout(typingTimer);
            typingTimer = setTimeout(() => fetchList(1), DEBOUNCE_MS);
        }

        // Events
        $search.addEventListener('input', scheduleFetch);
        $service.addEventListener('change', () => fetchList(1));
        $btn.addEventListener('click', () => fetchList(1));

        // Intercept pagination clicks → AJAX
        $pager.addEventListener('click', function(e) {
            const a = e.target.closest('a');
            if (!a) return;
            // Tailwind pagination link biasanya punya ?page=N
            const url = new URL(a.href);
            const page = url.searchParams.get('page') || 1;
            e.preventDefault();
            fetchList(page);
        });
    </script>
</x-app-layout>
