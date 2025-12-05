<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-indigo-500">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
                {{ __('Daftar Pesanan Layanan') }}
            </h2>
            <a href="{{ route('service-orders.create') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Buat Pesanan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter / Statistik Ringkas (Opsional) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Card Pending -->
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pesanan Pending</p>
                        <p class="text-2xl font-bold text-amber-500">{{ $orders->where('status', 'pending')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-full text-amber-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <!-- Card Bill Later -->
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum Ditagih</p>
                        <p class="text-2xl font-bold text-blue-500">
                            {{ $orders->where('payment_method', 'bill_later')->whereNull('billing_id')->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-full text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Siswa</th>
                                <th class="px-6 py-4">Layanan</th>
                                <th class="px-6 py-4 text-center">Total</th>
                                <th class="px-6 py-4 text-center">Pembayaran</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($orders as $order)
                                <tr
                                    class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                        <div class="text-xs text-gray-400 font-normal">
                                            #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $order->student->student_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->student->student_number ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($order->extraService->image_url)
                                                <img src="{{ asset('storage/' . $order->extraService->image_url) }}"
                                                    class="w-8 h-8 rounded-md object-cover mr-2" alt="">
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $order->extraService->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->quantity }} Unit</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->total_final_price == 0)
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">GRATIS</span>
                                        @else
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">Rp
                                                {{ number_format($order->total_final_price, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($order->payment_method == 'pay_now')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                                Bayar Langsung
                                            </span>
                                        @else
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 mb-1">
                                                    Tagihan Nanti
                                                </span>
                                                @if ($order->billing_id)
                                                    <span
                                                        class="text-[10px] text-green-600 font-semibold flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Ditagihkan
                                                    </span>
                                                @else
                                                    <span class="text-[10px] text-amber-500 font-medium">Belum Masuk
                                                        Tagihan</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'completed' => 'bg-green-100 text-green-800 border-green-200',
                                                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                            ];
                                            $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-end">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('service-orders.show', $order) }}"
                                                class="p-2 text-gray-400 hover:text-indigo-600 transition bg-gray-50 hover:bg-indigo-50 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            @if (!$order->billing_id)
                                                <a href="{{ route('service-orders.edit', $order) }}"
                                                    class="p-2 text-gray-400 hover:text-amber-600 transition bg-gray-50 hover:bg-amber-50 rounded-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button
                                                    onclick="confirmDelete('{{ route('service-orders.destroy', $order) }}')"
                                                    class="p-2 text-gray-400 hover:text-red-600 transition bg-gray-50 hover:bg-red-50 rounded-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="p-2 text-gray-300 cursor-not-allowed"
                                                    title="Sudah ditagih, tidak dapat diedit/hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"
                                        class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p>Belum ada pesanan layanan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t dark:border-gray-700">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
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

            function confirmDelete(url) {
                Swal.fire({
                    title: 'Hapus Pesanan?',
                    text: "Pesanan yang dihapus tidak dapat dikembalikan.",
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
        </script>
    @endpush
</x-app-layout>
