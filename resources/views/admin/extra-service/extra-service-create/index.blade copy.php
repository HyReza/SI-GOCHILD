<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.catalog', $student->id) }}"
                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <span
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold shadow-sm">3</span>
                {{ __('Checkout Pesanan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Ringkasan Item (Kiri) --}}
                <div class="md:col-span-1 space-y-6">
                    {{-- Card Siswa --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Detail Siswa</h3>
                        <div class="flex items-center gap-4">
                            <div
                                class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg">
                                {{ substr($student->student_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $student->student_name }}</p>
                                <p class="text-xs text-gray-500">{{ $student->student_number }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Card Layanan --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Layanan Dipilih</h3>
                        @if ($service->image_url)
                            <img src="{{ asset('storage/' . $service->image_url) }}"
                                class="w-full h-32 object-cover rounded-lg mb-4">
                        @endif
                        <h4 class="font-bold text-lg text-gray-900 dark:text-white">{{ $service->name }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($service->description, 80) }}</p>
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-400">Harga Satuan</p>
                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Rp
                                {{ number_format($service->base_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Form Checkout (Kanan) --}}
                <div class="md:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-indigo-50 dark:border-gray-700 p-8 relative overflow-hidden">

                        {{-- Background Pattern --}}
                        <div
                            class="absolute top-0 right-0 -mt-10 -mr-10 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/20 rounded-full blur-2xl pointer-events-none">
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 relative z-10">Konfirmasi
                            Pesanan</h2>

                        <form id="checkoutForm" action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="hidden" name="extra_service_id" value="{{ $service->id }}">

                            <div class="space-y-6 relative z-10">

                                {{-- Baris 1 --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="order_date" value="Tanggal Pesanan" />
                                        <x-text-input id="order_date" name="order_date" type="date"
                                            class="mt-1 block w-full" :value="old('order_date', date('Y-m-d'))" required />
                                        <x-input-error :messages="$errors->get('order_date')" />
                                    </div>
                                    <div>
                                        <x-input-label for="quantity" value="Jumlah (Unit/Sesi)" />
                                        <x-text-input id="quantity" name="quantity" type="number" min="1"
                                            class="mt-1 block w-full" :value="old('quantity', 1)" required
                                            oninput="calculateTotal()" />
                                        <x-input-error :messages="$errors->get('quantity')" />
                                    </div>
                                </div>

                                {{-- Opsi Gratis --}}
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600 transition-colors"
                                    id="priceBox">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <input id="is_free" name="is_free" type="checkbox" value="1"
                                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                onchange="toggleFree()">
                                            <label for="is_free"
                                                class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 select-none cursor-pointer">
                                                Gratiskan Pesanan?
                                                <span class="block text-xs text-gray-500 font-normal">Centang untuk
                                                    memberi diskon 100%</span>
                                            </label>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500 uppercase">Total Bayar</p>
                                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400"
                                                id="totalDisplay">Rp 0</p>
                                        </div>
                                    </div>

                                    <div id="noteContainer"
                                        class="mt-4 hidden border-t border-gray-200 dark:border-gray-600 pt-4">
                                        <x-input-label for="discount_note" value="Alasan Gratis (Opsional)" />
                                        <input type="text" name="discount_note" id="discount_note"
                                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm"
                                            placeholder="Contoh: Hadiah Prestasi">
                                    </div>
                                </div>

                                {{-- Metode Bayar --}}
                                <div>
                                    <x-input-label value="Metode Pembayaran" class="mb-2" />
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_method" value="bill_later"
                                                class="peer sr-only" checked>
                                            <div
                                                class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 transition-all">
                                                <div class="flex items-center mb-1">
                                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                    <span class="font-bold text-gray-900 dark:text-white text-sm">Masuk
                                                        Tagihan</span>
                                                </div>
                                                <p class="text-xs text-gray-500">Ditotal di akhir bulan (Invoice).</p>
                                            </div>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_method" value="pay_now"
                                                class="peer sr-only">
                                            <div
                                                class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all">
                                                <div class="flex items-center mb-1">
                                                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    <span class="font-bold text-gray-900 dark:text-white text-sm">Bayar
                                                        Langsung</span>
                                                </div>
                                                <p class="text-xs text-gray-500">Lunas sekarang (Cash/Transfer).</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                <button type="submit" id="submitBtn"
                                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                                    Buat Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT JS LANGSUNG (BUKAN DALAM PUSH) UNTUK MEMASTIKAN EKSEKUSI --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const basePrice = {{ $service->base_price }};

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }

        function calculateTotal() {
            const qtyInput = document.getElementById('quantity');
            const qty = qtyInput && qtyInput.value ? qtyInput.value : 0;
            const isFree = document.getElementById('is_free').checked;
            const totalDisplay = document.getElementById('totalDisplay');
            const priceBox = document.getElementById('priceBox');
            const noteContainer = document.getElementById('noteContainer');

            let total = basePrice * qty;

            if (isFree) {
                total = 0;
                totalDisplay.innerText = 'GRATIS (Rp 0)';
                totalDisplay.classList.add('text-green-600');
                totalDisplay.classList.remove('text-indigo-600');
                priceBox.classList.add('bg-green-50', 'border-green-200');
                priceBox.classList.remove('bg-gray-50', 'border-gray-200');
                noteContainer.classList.remove('hidden');
            } else {
                totalDisplay.innerText = formatRupiah(total);
                totalDisplay.classList.remove('text-green-600');
                totalDisplay.classList.add('text-indigo-600');
                priceBox.classList.remove('bg-green-50', 'border-green-200');
                priceBox.classList.add('bg-gray-50', 'border-gray-200');
                noteContainer.classList.add('hidden');
            }
        }

        function toggleFree() {
            calculateTotal();
        }

        // Event listener untuk form submit
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah submit langsung
            let form = this;

            // Jika SweetAlert tidak termuat, gunakan konfirmasi biasa
            if (typeof Swal === 'undefined') {
                if (confirm("Apakah Anda yakin ingin membuat pesanan ini?")) {
                    form.submit();
                }
                return;
            }

            // Menggunakan SweetAlert
            Swal.fire({
                title: 'Konfirmasi',
                text: "Pesanan akan dibuat dan masuk ke sistem.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Pesan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        // Inisialisasi perhitungan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</x-app-layout>
