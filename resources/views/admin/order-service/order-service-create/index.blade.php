<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Pesanan Layanan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700 relative">

                {{-- Tombol Kembali --}}
                <div class="absolute top-6 left-6">
                    <a href="{{ route('service-orders.index') }}"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>

                <div class="p-8 pt-16">
                    <div class="mb-8 text-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Formulir Pemesanan</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Isi detail layanan yang akan dipesan
                            oleh siswa.</p>
                    </div>

                    <form id="createOrderForm" action="{{ route('service-orders.store') }}" method="POST"
                        class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Pilih Siswa --}}
                            <div class="col-span-2">
                                <x-input-label for="student_id" value="Pilih Siswa" />
                                <select name="student_id" id="student_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                    required>
                                    <option value="">-- Cari Siswa --</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->student_name }} ({{ $student->student_number ?? 'No ID' }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                            </div>

                            {{-- Pilih Layanan --}}
                            <div class="col-span-2 md:col-span-1">
                                <x-input-label for="extra_service_id" value="Pilih Layanan" />
                                <select name="extra_service_id" id="extra_service_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                    required onchange="updatePricePreview()">
                                    <option value="" data-price="0">-- Pilih Layanan --</option>
                                    @foreach ($extraServices as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->base_price }}"
                                            {{ old('extra_service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} - Rp
                                            {{ number_format($service->base_price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('extra_service_id')" class="mt-2" />
                            </div>

                            {{-- Tanggal & Jumlah --}}
                            <div>
                                <x-input-label for="order_date" value="Tanggal Pesan" />
                                <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full"
                                    :value="old('order_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="quantity" value="Jumlah (Qty)" />
                                <x-text-input id="quantity" name="quantity" type="number" min="1"
                                    class="mt-1 block w-full" :value="old('quantity', 1)" required onchange="updatePricePreview()"
                                    onkeyup="updatePricePreview()" />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Opsi Gratis --}}
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="is_free" name="is_free" type="checkbox" value="1"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-5 w-5"
                                        onchange="toggleFreeOption()" {{ old('is_free') ? 'checked' : '' }}>
                                    <label for="is_free"
                                        class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Gratiskan Pesanan Ini?
                                        <p class="text-xs text-gray-500 font-normal">Centang jika layanan ini diberikan
                                            cuma-cuma (Diskon 100%).</p>
                                    </label>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Estimasi</p>
                                    <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400" id="priceDisplay">
                                        Rp 0</p>
                                </div>
                            </div>

                            {{-- Catatan Diskon (Hidden by default) --}}
                            <div id="discountNoteContainer" class="mt-4 hidden">
                                <x-input-label for="discount_note" value="Alasan Gratis / Catatan Diskon" />
                                <textarea name="discount_note" id="discount_note" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300"
                                    placeholder="Contoh: Promo Ulang Tahun Siswa">{{ old('discount_note') }}</textarea>
                            </div>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div>
                            <x-input-label value="Metode Pembayaran" class="mb-2" />
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label
                                    class="relative flex items-center p-4 rounded-xl border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition group has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/20">
                                    <input type="radio" name="payment_method" value="bill_later"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" checked>
                                    <div class="ml-3">
                                        <span
                                            class="block text-sm font-medium text-gray-900 dark:text-white group-has-[:checked]:text-indigo-700 dark:group-has-[:checked]:text-indigo-300">
                                            Masuk Tagihan (Bill Later)
                                        </span>
                                        <span
                                            class="block text-xs text-gray-500 group-has-[:checked]:text-indigo-600/70">
                                            Akan ditotal di akhir bulan.
                                        </span>
                                    </div>
                                </label>

                                <label
                                    class="relative flex items-center p-4 rounded-xl border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition group has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-900/20">
                                    <input type="radio" name="payment_method" value="pay_now"
                                        class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                    <div class="ml-3">
                                        <span
                                            class="block text-sm font-medium text-gray-900 dark:text-white group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300">
                                            Bayar Langsung (Lunas)
                                        </span>
                                        <span
                                            class="block text-xs text-gray-500 group-has-[:checked]:text-emerald-600/70">
                                            Tidak masuk tagihan bulanan.
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit"
                                class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-lg hover:bg-indigo-700 transition transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Buat Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Format Rupiah
            const formatRupiah = (money) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(money);
            }

            // Update Preview Harga
            function updatePricePreview() {
                const serviceSelect = document.getElementById('extra_service_id');
                const qtyInput = document.getElementById('quantity');
                const isFreeCheckbox = document.getElementById('is_free');
                const priceDisplay = document.getElementById('priceDisplay');

                // Ambil harga dari attribute data-price option yang dipilih
                const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                const basePrice = selectedOption.getAttribute('data-price') ? parseInt(selectedOption.getAttribute(
                    'data-price')) : 0;
                const qty = qtyInput.value ? parseInt(qtyInput.value) : 0;

                let total = basePrice * qty;

                if (isFreeCheckbox.checked) {
                    total = 0;
                    priceDisplay.classList.add('text-green-600');
                    priceDisplay.classList.remove('text-indigo-600');
                    priceDisplay.innerText = 'GRATIS (Rp 0)';
                } else {
                    priceDisplay.classList.remove('text-green-600');
                    priceDisplay.classList.add('text-indigo-600');
                    priceDisplay.innerText = formatRupiah(total);
                }
            }

            // Toggle Tampilan Catatan Diskon
            function toggleFreeOption() {
                const isFree = document.getElementById('is_free').checked;
                const noteContainer = document.getElementById('discountNoteContainer');
                const noteInput = document.getElementById('discount_note');

                if (isFree) {
                    noteContainer.classList.remove('hidden');
                    noteInput.setAttribute('required', 'required'); // Wajib isi alasan jika gratis (opsional, bisa dihapus)
                } else {
                    noteContainer.classList.add('hidden');
                    noteInput.removeAttribute('required');
                }
                updatePricePreview();
            }

            // SweetAlert Submit
            document.getElementById('createOrderForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Pesanan',
                    text: "Pastikan data siswa dan layanan sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Buat Pesanan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            didOpen: () => Swal.showLoading()
                        });
                        this.submit();
                    }
                });
            });

            // Init state
            toggleFreeOption();
        </script>
    @endpush
</x-app-layout>
