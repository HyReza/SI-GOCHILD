<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg dark:bg-indigo-900 dark:text-indigo-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </span>
                {{ __('Data Pesanan & Layanan') }}
            </h2>

            {{-- Tombol Buat Pesanan (Hanya untuk Admin) --}}
            @if (Auth::guard('web')->check() && Auth::guard('web')->user()->role->role_name == 'admin')
                <a href="{{ route('orders.select-student') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Pesanan Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- SECTION: SEARCH & FILTER --}}
            <div
                class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col md:flex-row gap-4">

                    {{-- 1. Search Input --}}
                    <div class="flex-1 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all"
                            placeholder="Cari Nama Siswa atau Layanan..." autocomplete="off">

                        {{-- Loading Spinner --}}
                        <div id="searchLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    {{-- 2. Status Filter --}}
                    <div class="w-full md:w-64">
                        <select id="statusFilter" name="status"
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 cursor-pointer">
                            <option value="">-- Semua Status --</option>
                            <option value="pending_payment"
                                {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran
                            </option>
                            <option value="pending_confirmation"
                                {{ request('status') == 'pending_confirmation' ? 'selected' : '' }}>Menunggu Konfirmasi
                                Admin</option>
                            <option value="pending_process"
                                {{ request('status') == 'pending_process' ? 'selected' : '' }}>Siap Dikerjakan (Guru)
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                Dibatalkan</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                    </div>

                    {{-- 3. Reset Button --}}
                    <a href="{{ route('orders.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Reset
                    </a>
                </div>
            </div>

            {{-- SECTION: TABLE ORDER (Container untuk AJAX) --}}
            <div id="ordersTableContainer"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700 relative min-h-[300px]">

                {{-- Konten Tabel --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-2xl">Tanggal</th>
                                <th class="px-6 py-4">Siswa</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                <th class="px-6 py-4 text-center">Pembayaran</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-end rounded-tr-2xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">

                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>

                                    {{-- Siswa --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($order->student->user_photo)
                                                <img class="h-8 w-8 rounded-full object-cover mr-3 border border-gray-200"
                                                    src="{{ asset('storage/' . $order->student->user_photo) }}">
                                            @else
                                                <div
                                                    class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold mr-3">
                                                    {{ substr($order->student->student_name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white">
                                                    {{ $order->student->student_name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $order->student->student_number ?? 'No ID' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Layanan --}}
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900 dark:text-white font-medium">
                                            {{ $order->extraService->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            Qty: <span class="font-bold">{{ $order->quantity }}</span>
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->total_final_price == 0)
                                            <span
                                                class="px-2.5 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold border border-green-200">GRATIS</span>
                                        @else
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">
                                                Rp {{ number_format($order->total_final_price, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Pembayaran --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->payment_method == 'pay_now')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                Bayar Langsung
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                Tagihan Nanti
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $colors = [
                                                'pending_payment' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'pending_confirmation' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'pending_process' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                'completed' => 'bg-green-100 text-green-800 border-green-200',
                                                'cancelled' => 'bg-red-50 text-red-600 border-red-200',
                                                'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                            ];
                                            $labels = [
                                                'pending_payment' => 'Menunggu Bayar',
                                                'pending_confirmation' => 'Konfirmasi Admin',
                                                'pending_process' => 'Siap Dikerjakan',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Batal',
                                                'rejected' => 'Ditolak',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-bold border {{ $colors[$order->status] ?? 'bg-gray-100' }}">
                                            {{ $labels[$order->status] ?? ucfirst($order->status) }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-6 py-4 text-end">
                                        <div class="flex justify-end items-center gap-2">

                                            {{-- [TOMBOL ADMIN] Konfirmasi Order --}}
                                            @if (
                                                $order->status === 'pending_confirmation' &&
                                                    Auth::guard('web')->check() &&
                                                    Auth::guard('web')->user()->role->role_name == 'admin')
                                                <button
                                                    onclick="confirmStatusChange('{{ route('orders.update-status', $order->id) }}', 'pending_process', 'Verifikasi Pesanan?', 'Pesanan akan masuk antrian pengajar.')"
                                                    class="p-2 text-green-600 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition"
                                                    title="Verifikasi / Terima">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>

                                                <button
                                                    onclick="confirmStatusChange('{{ route('orders.update-status', $order->id) }}', 'rejected', 'Tolak Pesanan?', 'Pesanan akan ditolak.')"
                                                    class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition"
                                                    title="Tolak Pesanan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            @endif

                                            {{-- [TOMBOL GURU] Selesaikan Order --}}
                                            @if ($order->status === 'pending_process')
                                                <a href="{{ route('orders.completion', $order->id) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg shadow-sm transition-transform hover:-translate-y-0.5"
                                                    title="Selesaikan Pekerjaan">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Selesaikan
                                                </a>
                                            @endif

                                            {{-- [TOMBOL DETAIL] --}}
                                            <a href="{{ route('orders.show', $order) }}"
                                                class="p-2 text-gray-400 hover:text-indigo-600 bg-gray-50 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100"
                                                title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            {{-- [TOMBOL DELETE] --}}
                                            @if (!$order->billing_id && in_array($order->status, ['pending_payment', 'pending_confirmation', 'cancelled']))
                                                <button
                                                    onclick="confirmDelete('{{ route('orders.destroy', $order) }}')"
                                                    class="p-2 text-gray-400 hover:text-red-600 bg-gray-50 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100"
                                                    title="Hapus Pesanan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada
                                                pesanan</h3>
                                            <p class="text-gray-500 dark:text-gray-400 mt-1 max-w-sm">Tidak ditemukan
                                                data pesanan sesuai kriteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Form Delete (Hidden) --}}
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Form Update Status (Hidden) --}}
    <form id="status-form" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" id="status-input">
    </form>

    {{-- SCRIPT: Langsung diletakkan di sini, tanpa @push --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. SWEETALERT: Notification Session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}"
            });
        @endif

        // 2. SWEETALERT: Konfirmasi Hapus
        function confirmDelete(url) {
            Swal.fire({
                title: 'Hapus Pesanan?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        didOpen: () => Swal.showLoading()
                    });
                    let form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }

        // 3. SWEETALERT: Konfirmasi Update Status
        function confirmStatusChange(url, status, title, text) {
            const color = status === 'rejected' ? '#ef4444' : '#10b981';
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: color,
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        didOpen: () => Swal.showLoading()
                    });
                    let form = document.getElementById('status-form');
                    form.action = url;
                    document.getElementById('status-input').value = status;
                    form.submit();
                }
            });
        }

        // 4. LIVE SEARCH (Vanilla JS - Tanpa Alpine)
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const tableContainer = document.getElementById('ordersTableContainer');
            const searchLoading = document.getElementById('searchLoading');

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            function fetchOrders() {
                // Tampilkan loading
                if (searchLoading) searchLoading.classList.remove('hidden');
                if (tableContainer) tableContainer.classList.add('opacity-50', 'pointer-events-none');

                const params = new URLSearchParams(window.location.search);
                if (searchInput) params.set('search', searchInput.value);
                if (statusFilter) params.set('status', statusFilter.value);

                const newUrl = `${window.location.pathname}?${params.toString()}`;

                // Update URL di browser tanpa reload
                window.history.pushState({}, '', newUrl);

                fetch(newUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableContent = doc.getElementById('ordersTableContainer');

                        if (newTableContent && tableContainer) {
                            tableContainer.innerHTML = newTableContent.innerHTML;
                        } else {
                            console.error('Element #ordersTableContainer tidak ditemukan dalam respon.');
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error))
                    .finally(() => {
                        if (searchLoading) searchLoading.classList.add('hidden');
                        if (tableContainer) tableContainer.classList.remove('opacity-50',
                        'pointer-events-none');
                    });
            }

            if (searchInput) {
                searchInput.addEventListener('input', debounce(fetchOrders, 500));
            }
            if (statusFilter) {
                statusFilter.addEventListener('change', fetchOrders);
            }
        });
    </script>
</x-app-layout>
