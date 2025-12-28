<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('orders.index') }}"
                    class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Detail Pesanan') }} <span
                        class="text-gray-400">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                </h2>
            </div>

            <div class="flex items-center gap-3">
                {{-- Tombol Cetak --}}
                <button onclick="window.print()"
                    class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak
                </button>

                {{-- Status Badge --}}
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
                        'pending_payment' => 'Menunggu Pembayaran',
                        'pending_confirmation' => 'Menunggu Konfirmasi',
                        'pending_process' => 'Sedang Diproses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        'rejected' => 'Ditolak',
                    ];
                @endphp
                <span
                    class="px-3 py-1 rounded-full text-sm font-bold border {{ $colors[$order->status] ?? 'bg-gray-100' }}">
                    {{ $labels[$order->status] ?? ucfirst($order->status) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ALERT BOX KHUSUS STATUS DITOLAK --}}
            @if ($order->status == 'rejected')
                <div
                    class="mb-6 rounded-xl bg-red-50 border-l-4 border-red-500 p-4 shadow-sm dark:bg-red-900/20 dark:border-red-600">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800 dark:text-red-200">Pesanan Ditolak</h3>
                            <div class="mt-1 text-sm text-red-700 dark:text-red-300">
                                <p>Mohon maaf, pesanan ini telah ditolak oleh Admin/Pengajar. Silakan hubungi admin
                                    sekolah jika ini kesalahan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: Info Utama --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- 1. Kartu Rincian Layanan --}}
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                            Rincian Layanan
                        </h3>

                        <div class="overflow-hidden border rounded-xl border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Layanan</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Harga</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Qty</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $order->extraService->name ?? 'Layanan Tidak Ditemukan' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                            Rp {{ number_format($order->single_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $order->quantity }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-medium text-gray-500">Total
                                            Tagihan</td>
                                        <td class="px-6 py-4 text-right font-bold text-indigo-600 text-lg">
                                            Rp {{ number_format($order->total_final_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Catatan --}}
                        @if ($order->notes)
                            <div
                                class="mt-4 flex gap-3 p-4 bg-yellow-50 rounded-xl border border-yellow-100 text-sm text-yellow-800">
                                <svg class="w-5 h-5 flex-shrink-0 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                <div>
                                    <span class="font-bold block mb-1">Catatan Pemesan:</span>
                                    "{{ $order->notes }}"
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 2. Kartu Informasi Pembayaran (OVERHAULED) --}}
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            Informasi Pembayaran
                        </h3>

                        <div class="flex flex-col gap-4">
                            {{-- Info Utama --}}
                            <div class="flex flex-col md:flex-row gap-4">
                                <div
                                    class="flex-1 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600">
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Metode Pembayaran
                                    </p>
                                    <p class="text-gray-900 dark:text-white mt-1 font-semibold text-lg">
                                        @if ($order->payment_method == 'pay_now')
                                            Bayar Sekarang (Transfer)
                                        @else
                                            Bayar Nanti (Tagihan SPP)
                                        @endif
                                    </p>
                                </div>
                                <div
                                    class="flex-1 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600">
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Status Pembayaran
                                    </p>
                                    <div class="mt-1">
                                        @if ($order->payment_status == 'paid')
                                            <div class="flex items-center gap-2 text-emerald-600 font-bold text-lg">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                LUNAS
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-red-500 font-bold text-lg">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                BELUM LUNAS
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Alert jika Belum Bayar (Khusus Pay Now) --}}
                            @if ($order->payment_method == 'pay_now' && $order->payment_status != 'paid')
                                <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <div>
                                        <h4 class="font-bold text-red-800 text-sm">Menunggu Pembayaran</h4>
                                        <p class="text-sm text-red-600 mt-1">
                                            Pesanan ini menggunakan metode transfer namun belum terverifikasi lunas.
                                            Mohon cek bukti pembayaran atau ingatkan wali murid.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- List Bukti Pembayaran --}}
                            @if ($order->payments && $order->payments->count() > 0)
                                <div class="mt-4 border-t border-gray-100 dark:border-gray-600 pt-4">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Riwayat & Bukti
                                        Transaksi</h4>
                                    <div class="space-y-3">
                                        @foreach ($order->payments as $payment)
                                            <div
                                                class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl hover:shadow-md transition">
                                                <div class="flex items-center gap-3">
                                                    <div class="bg-gray-100 p-2 rounded-lg text-gray-500">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">
                                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, H:i') }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">Ref:
                                                            {{ $payment->reference_number ?? '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-bold text-emerald-600">Rp
                                                        {{ number_format($payment->amount, 0, ',', '.') }}</div>

                                                    {{-- Tombol Lihat Bukti --}}
                                                    @if ($payment->proof_file)
                                                        <a href="{{ asset('storage/' . $payment->proof_file) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center mt-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                </path>
                                                            </svg>
                                                            Lihat Bukti
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400 italic">Tanpa Bukti</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: Sidebar Info --}}
                <div class="space-y-6">

                    {{-- 1. Kartu Siswa (Updated with Contact Info) --}}
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                        <div class="relative">
                            @if ($order->student && $order->student->user_photo)
                                <img class="h-24 w-24 rounded-full object-cover mb-4 border-4 border-indigo-50"
                                    src="{{ asset('storage/' . $order->student->user_photo) }}">
                            @else
                                <div
                                    class="h-24 w-24 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl font-bold mb-4 border-4 border-white shadow-sm">
                                    {{ substr($order->student?->student_name ?? 'X', 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $order->student?->student_name ?? 'Siswa Tidak Ditemukan' }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ $order->student?->student_number ?? 'NISN -' }}</p>

                        <div class="mt-4 w-full border-t border-gray-100 pt-4 text-left text-sm space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kelas</span>
                                <span
                                    class="font-medium bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $order->student?->classroom?->name ?? '-' }}</span>
                            </div>

                            {{-- Info Orang Tua (Opsional, jika ada relasi) --}}
                            @if ($order->student && $order->student->parent_name)
                                <div class="flex flex-col gap-1">
                                    <span class="text-gray-500 text-xs uppercase">Wali Murid</span>
                                    <span class="font-medium">{{ $order->student->parent_name }}</span>
                                    @if ($order->student->parent_phone)
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $order->student->parent_phone) }}"
                                            target="_blank"
                                            class="text-green-600 hover:text-green-700 flex items-center gap-1 text-xs font-bold">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                            </svg>
                                            {{ $order->student->parent_phone }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 2. Info Proses & Log --}}
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3
                            class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider flex items-center justify-between">
                            Log Pesanan
                            <span
                                class="text-[10px] py-0.5 px-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-500">History</span>
                        </h3>

                        <div class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-3 space-y-8">

                            {{-- STATUS: DIBUAT (CREATED) --}}
                            <div class="ml-6 relative">
                                {{-- Icon Dot --}}
                                <span
                                    class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-[31px] ring-4 ring-white dark:ring-gray-900 dark:bg-blue-900">
                                    <svg class="w-3 h-3 text-blue-800 dark:text-blue-300" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </span>

                                {{-- Content --}}
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pesanan Dibuat</h3>

                                    {{-- [BARU] Menampilkan Pembuat --}}
                                    <div class="mt-1 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <span>Oleh: <span
                                                class="font-semibold text-gray-800 dark:text-gray-200">{{ $order->user->name ?? 'Admin / Sistem' }}</span></span>
                                    </div>

                                    {{-- Waktu --}}
                                    <time class="block mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </time>
                                </div>
                            </div>

                            {{-- STATUS: DIPROSES (PROCESSOR) --}}
                            @if ($order->processor)
                                <div class="ml-6 relative">
                                    <span
                                        class="absolute flex items-center justify-center w-6 h-6 bg-purple-100 rounded-full -left-[31px] ring-4 ring-white dark:ring-gray-900">
                                        <svg class="w-3 h-3 text-purple-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Diproses</h3>
                                        <div class="mt-1 text-sm text-gray-600">
                                            Oleh: <span
                                                class="font-semibold">{{ $order->processor->name ?? 'Admin' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- STATUS: SELESAI --}}
                            @if ($order->status == 'completed')
                                <div class="ml-6 relative">
                                    <span
                                        class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -left-[31px] ring-4 ring-white dark:ring-gray-900">
                                        <svg class="w-3 h-3 text-green-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Selesai</h3>
                                        <time
                                            class="block mt-1 text-xs text-gray-400">{{ $order->updated_at->format('d M Y, H:i') }}</time>
                                    </div>
                                </div>
                            @endif

                            {{-- STATUS: DITOLAK --}}
                            @if ($order->status == 'rejected')
                                <div class="ml-6 relative">
                                    <span
                                        class="absolute flex items-center justify-center w-6 h-6 bg-red-100 rounded-full -left-[31px] ring-4 ring-white dark:ring-gray-900">
                                        <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-red-600">Ditolak</h3>
                                        <time
                                            class="block mt-1 text-xs text-gray-400">{{ $order->updated_at->format('d M Y, H:i') }}</time>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- 3. Tombol Aksi --}}
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Aksi</h3>
                        <div class="flex flex-col gap-3">

                            {{-- Admin: Konfirmasi --}}
                            @if (
                                $order->status === 'pending_confirmation' &&
                                    Auth::guard('web')->check() &&
                                    Auth::guard('web')->user()->role->role_name == 'admin')
                                <button
                                    onclick="confirmStatusChange('{{ route('orders.update-status', $order->id) }}', 'pending_process', 'Verifikasi?', 'Pesanan akan diproses guru.')"
                                    class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm transition shadow-lg shadow-indigo-200">
                                    Verifikasi Pesanan
                                </button>
                                <button
                                    onclick="confirmStatusChange('{{ route('orders.update-status', $order->id) }}', 'rejected', 'Tolak?', 'Pesanan akan ditolak permanen.')"
                                    class="w-full py-2 px-4 bg-white border border-red-200 text-red-600 hover:bg-red-50 rounded-xl font-semibold text-sm transition">
                                    Tolak Pesanan
                                </button>
                            @endif

                            {{-- Guru/Admin: Selesaikan --}}
                            @if ($order->status === 'pending_process')
                                <a href="{{ route('orders.completion', $order->id) }}"
                                    class="w-full text-center py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm transition shadow-lg shadow-green-200">
                                    Selesaikan Pekerjaan
                                </a>
                            @endif

                            {{-- Hapus --}}
                            @if (
                                !$order->billing_id &&
                                    in_array($order->status, ['pending_payment', 'pending_confirmation', 'cancelled', 'rejected']))
                                <button onclick="confirmDelete('{{ route('orders.destroy', $order) }}')"
                                    class="w-full py-2 px-4 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-semibold text-sm transition border border-red-100">
                                    Hapus Data
                                </button>
                            @endif

                            {{-- Kembali --}}
                            <a href="{{ route('orders.index') }}"
                                class="w-full text-center py-2 px-4 text-gray-500 hover:bg-gray-100 rounded-xl font-medium text-sm transition">
                                Kembali ke Daftar
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Forms & Script (Standard) --}}
    <form id="delete-form" method="POST" style="display: none;"> @csrf @method('DELETE') </form>
    <form id="status-form" method="POST" style="display: none;"> @csrf @method('PATCH') <input type="hidden"
            name="status" id="status-input"> </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Hapus Pesanan?',
                text: "Data tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }

        function confirmStatusChange(url, status, title, text) {
            const color = status === 'rejected' ? '#ef4444' : '#10b981';
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: color,
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('status-form');
                    form.action = url;
                    document.getElementById('status-input').value = status;
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
