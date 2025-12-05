<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}"
                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <span
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-bold shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                {{ __('Pembayaran') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Info --}}
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Pesanan berhasil dibuat dengan ID <strong>#{{ $order->id }}</strong>. Silakan selesaikan
                            pembayaran agar pesanan dapat diproses.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- KOLOM KIRI: Informasi Transfer --}}
                <div class="space-y-6">
                    {{-- 1. Total Tagihan --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider mb-1">Total Harus Dibayar
                        </p>
                        <h1 class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-400">
                            Rp {{ number_format($order->total_final_price, 0, ',', '.') }}
                        </h1>
                        <div
                            class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-300">
                            Order ID: #{{ $order->id }}
                        </div>
                    </div>

                    {{-- 2. Detail Rekening (Sesuai Gambar) --}}
                    <div
                        class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl p-6 shadow-lg border border-emerald-100 dark:border-gray-700 relative overflow-hidden">
                        {{-- Background Pattern --}}
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-200 dark:bg-emerald-900 rounded-full blur-2xl opacity-50">
                        </div>

                        <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Transfer Ke Rekening
                        </h3>

                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between border-b border-emerald-200 dark:border-gray-600 pb-2">
                                <span class="text-gray-500 dark:text-gray-400">Bank</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">BSI (Bank Syariah
                                    Indonesia)</span>
                            </div>
                            <div class="flex justify-between border-b border-emerald-200 dark:border-gray-600 pb-2">
                                <span class="text-gray-500 dark:text-gray-400">Kode Bank</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">451</span>
                            </div>
                            <div class="flex flex-col border-b border-emerald-200 dark:border-gray-600 pb-2">
                                <span class="text-gray-500 dark:text-gray-400 mb-1">Nomor Rekening</span>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-2xl font-mono font-bold text-gray-900 dark:text-white tracking-wider"
                                        id="rekText">1000-800-119</span>
                                    <button onclick="copyRekening()"
                                        class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold flex items-center gap-1 bg-white dark:bg-gray-700 px-2 py-1 rounded shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Salin
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col pt-1">
                                <span class="text-gray-500 dark:text-gray-400 mb-1">Atas Nama</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200 uppercase">ALJANNAH PRESCHOOL
                                    AND DAYCARE</span>
                            </div>
                        </div>

                        {{-- Kontak WhatsApp --}}
                        <div class="mt-6 bg-white dark:bg-gray-700 rounded-xl p-3 flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-full text-green-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Konfirmasi via WhatsApp</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">0813-1000-3450</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: Form Upload --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 flex flex-col h-full">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Konfirmasi Pembayaran</h2>

                    <form action="{{ route('orders.process-payment', $order->id) }}" method="POST"
                        enctype="multipart/form-data" class="flex-1 flex flex-col">
                        @csrf

                        <div class="space-y-5 flex-1">
                            {{-- Info Pengirim (Opsional) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="sender_name" value="Nama Pengirim (Optional)" />
                                    <x-text-input id="sender_name" name="sender_name" type="text"
                                        class="mt-1 block w-full" placeholder="Contoh: Budi Santoso" />
                                </div>
                                <div>
                                    <x-input-label for="bank_destination" value="Dari Bank (Optional)" />
                                    <x-text-input id="bank_destination" name="bank_destination" type="text"
                                        class="mt-1 block w-full" placeholder="Contoh: BCA / Mandiri" />
                                </div>
                            </div>

                            {{-- Upload Area --}}
                            <div>
                                <x-input-label for="proof_image" value="Upload Bukti Transfer" />
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl hover:border-emerald-500 dark:hover:border-emerald-500 transition-colors relative"
                                    id="drop-area">
                                    <div class="space-y-1 text-center" id="upload-placeholder">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                            fill="none" viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                            <label for="proof_image"
                                                class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                                <span>Upload file</span>
                                                <input id="proof_image" name="proof_image" type="file"
                                                    class="sr-only" accept="image/*" required
                                                    onchange="previewImage(event)">
                                            </label>
                                            <p class="pl-1">atau drag & drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                                    </div>

                                    {{-- Image Preview --}}
                                    <div id="image-preview"
                                        class="hidden absolute inset-0 w-full h-full bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center p-2">
                                        <img src=""
                                            class="max-h-full max-w-full object-contain rounded-lg shadow-sm"
                                            alt="Preview">
                                        <button type="button" onclick="removeImage()"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('proof_image')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">

                            {{-- Tombol Utama (Primary - GREEN) --}}
                            <button type="submit" id="submitBtn"
                                class="group relative w-full flex justify-center items-center gap-3 py-4 px-6 rounded-xl
               text-white font-bold text-lg
               bg-green-600 {{-- Warna Dasar (Fallback) --}}
               bg-gradient-to-r from-green-500 to-green-700
               hover:from-green-600 hover:to-green-800
               shadow-xl shadow-green-500/20 hover:shadow-green-500/40
               focus:outline-none focus:ring-4 focus:ring-green-500/30
               transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98]">

                                {{-- Ikon Paper Plane --}}
                                <svg class="w-6 h-6 transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>

                                <span>Kirim Bukti Pembayaran</span>
                            </button>

                            {{-- Tombol Sekunder (Secondary) --}}
                            <button type="button" onclick="window.location='{{ route('orders.index') }}'"
                                class="mt-4 w-full py-3.5 px-6 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 font-semibold hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200">
                                Simpan & Upload Nanti
                            </button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Script untuk Preview Image & Copy --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Copy Rekening
        function copyRekening() {
            const rek = "1000800119";
            navigator.clipboard.writeText(rek).then(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'No. Rekening disalin!'
                });
            });
        }

        // Preview Image Upload
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    const img = preview.querySelector('img');
                    const placeholder = document.getElementById('upload-placeholder');

                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                    // placeholder.classList.add('opacity-0'); // Hide placeholder text visually but keep layout
                }
                reader.readAsDataURL(file);
            }
        }

        // Remove Selected Image
        function removeImage() {
            const input = document.getElementById('proof_image');
            const preview = document.getElementById('image-preview');

            input.value = ''; // Reset input
            preview.classList.add('hidden'); // Hide preview
        }
    </script>
</x-app-layout>
