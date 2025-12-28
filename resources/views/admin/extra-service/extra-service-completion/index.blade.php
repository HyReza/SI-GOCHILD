<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}"
                class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Penyelesaian Layanan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- ALERT INFORMATIF --}}
            <div
                class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-100 dark:bg-blue-900/20 dark:border-blue-800 flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-bold text-blue-800 dark:text-blue-300 text-sm">Penting</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                        Informasi yang Anda masukkan di bawah ini (Catatan & Foto) akan tersimpan sebagai
                        <strong>Laporan Kegiatan</strong> dan dapat dilihat oleh Wali Murid/Orang Tua.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: FORMULIR INPUT --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div
                            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 dark:text-white">Formulir Laporan</h3>
                            <span class="text-xs font-mono text-gray-400">ORDER
                                #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="p-6">
                            <form action="{{ route('orders.store-completion', $order->id) }}" method="POST"
                                enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                {{-- Input Catatan --}}
                                <div>
                                    <label for="completion_note"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                        Catatan Hasil Pengerjaan (Opsional)
                                    </label>
                                    <div class="relative">
                                        <textarea id="completion_note" name="completion_note" rows="5"
                                            class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm leading-relaxed p-4"
                                            placeholder="Tuliskan hasil pengamatan Anda terhadap siswa selama layanan berlangsung...&#10;&#10;Contoh:&#10;- Anak mengikuti instruksi dengan baik.&#10;- Suhu tubuh 36.5°C setelah treatment.">{{ old('completion_note') }}</textarea>
                                    </div>
                                </div>

                                {{-- Upload Foto Bukti --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                        Foto Dokumentasi / Bukti (Opsional)
                                    </label>

                                    <div class="mt-2">
                                        {{-- Area Klik Upload --}}
                                        <label
                                            class="group relative flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 dark:hover:bg-gray-700 dark:hover:border-gray-600 transition-all duration-300">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <div
                                                    class="p-3 bg-gray-100 rounded-full mb-3 group-hover:bg-white group-hover:scale-110 transition-transform shadow-sm">
                                                    <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-600"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <p class="mb-1 text-sm text-gray-500"><span
                                                        class="font-bold text-indigo-600">Klik untuk upload</span> atau
                                                    drag & drop</p>
                                                <p class="text-xs text-gray-400">Bisa pilih lebih dari 1 foto (JPG, PNG)
                                                </p>
                                            </div>

                                            {{-- INPUT FILE --}}
                                            <input type="file" id="evidence_input" name="evidence_photos[]" multiple
                                                class="hidden" accept="image/*" onchange="handleFileSelect(event)" />
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('evidence_photos')" class="mt-2" />
                                    <x-input-error :messages="$errors->get('evidence_photos.*')" class="mt-2" />

                                    {{-- Preview Area --}}
                                    <div id="evidence-preview-container"
                                        class="mt-4 grid grid-cols-3 sm:grid-cols-4 gap-3 empty:hidden">
                                        {{-- Javascript will populate this --}}
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div
                                    class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                    <a href="{{ route('orders.index') }}"
                                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition shadow-sm">
                                        Batal
                                    </a>
                                    <button type="submit"
                                        class="px-6 py-2.5 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Selesaikan & Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: INFO PESANAN (Sticky) --}}
                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 sticky top-6">
                        <div class="p-4 bg-indigo-600 text-center">
                            <h3 class="text-white font-bold text-lg">Detail Tugas</h3>
                            <p class="text-indigo-100 text-xs">Informasi Pesanan</p>
                        </div>

                        <div class="p-6 space-y-6">

                            {{-- Info Siswa --}}
                            <div class="text-center">
                                @if ($order->student->user_photo)
                                    <img src="{{ asset('storage/' . $order->student->user_photo) }}"
                                        class="w-20 h-20 rounded-full mx-auto object-cover border-4 border-indigo-50 shadow-sm mb-3">
                                @else
                                    <div
                                        class="w-20 h-20 rounded-full mx-auto bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold border-4 border-indigo-50 mb-3">
                                        {{ substr($order->student->student_name, 0, 1) }}
                                    </div>
                                @endif
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $order->student->student_name }}
                                </h4>
                                <p class="text-sm text-gray-500">
                                    {{ $order->student->student_number ?? 'NISN Tidak Ada' }}</p>
                                <span
                                    class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                                    {{ $order->student->classroom->name ?? 'Kelas -' }}
                                </span>
                            </div>

                            <hr class="border-gray-100 dark:border-gray-700">

                            {{-- Info Layanan --}}
                            <div>
                                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Layanan yang
                                    Dikerjakan</h5>
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-gray-200">
                                            {{ $order->extraService->name }}</p>
                                        <p class="text-sm text-gray-500 mt-0.5">
                                            {{ $order->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Awal (Jika Ada) --}}
                            @if ($order->notes)
                                <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                                    <h5
                                        class="text-xs font-bold text-yellow-800 uppercase tracking-wider mb-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                            </path>
                                        </svg>
                                        Catatan Pemesan
                                    </h5>
                                    <p class="text-sm text-yellow-900 italic">"{{ $order->notes }}"</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- JAVASCRIPT: Logic Upload & Hapus Foto --}}
    <script>
        // Array global untuk menyimpan file yang dipilih
        let selectedFiles = [];

        function handleFileSelect(event) {
            const input = event.target;
            const newFiles = Array.from(input.files);

            // Tambahkan file baru ke array global (cek duplikasi sederhana)
            newFiles.forEach(file => {
                if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    selectedFiles.push(file);
                }
            });

            // Update tampilan dan input
            updatePreviewAndInput();
        }

        function removeFile(index) {
            // Hapus file dari array berdasarkan index
            selectedFiles.splice(index, 1);

            // Update tampilan dan input
            updatePreviewAndInput();
        }

        function updatePreviewAndInput() {
            const container = document.getElementById('evidence-preview-container');
            const input = document.getElementById('evidence_input');

            // 1. Update Preview UI
            container.innerHTML = ''; // Reset

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className =
                        'relative group aspect-square rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-100';

                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">

                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>

                        <button type="button" onclick="removeFile(${index})"
                            class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-md transform scale-0 group-hover:scale-100 transition-transform duration-200"
                            title="Hapus Foto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });

            // 2. Update Nilai Input (PENTING: Agar form mengirim data yang benar)
            // Kita menggunakan DataTransfer untuk memanipulasi file list input
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }
    </script>
</x-app-layout>
