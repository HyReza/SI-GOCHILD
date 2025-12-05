<x-app-layout>
    @push('head')
        {{-- Library Signature Pad --}}
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <style>
            /* Custom scrollbar untuk tabel panjang */
            .table-container::-webkit-scrollbar {
                height: 8px;
            }

            .table-container::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .table-container::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            .dark .table-container::-webkit-scrollbar-track {
                background: #1e293b;
            }

            .dark .table-container::-webkit-scrollbar-thumb {
                background: #475569;
            }
        </style>
    @endpush

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- ... (HEADER, INFO UTAMA, DETAIL LAYANAN TETAP SAMA SEPERTI SEBELUMNYA) ... --}}
        {{-- Langsung ke bagian Tanda Tangan di bawah --}}

        {{-- INFO UTAMA SISWA & DETAIL LAINNYA (COPY DARI KODE SEBELUMNYA DI SINI) --}}
        {{-- Agar tidak terlalu panjang, saya fokuskan pada update bagian SIGNATURE saja --}}

        {{-- ... Konten Detail Laporan ... --}}

        {{-- AREA TANDA TANGAN (SIGNATURE) --}}
        <div
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 p-6 sm:p-8 text-center">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Konfirmasi Orang Tua / Wali</h3>

            @if ($dailyReport->parent_guardian_signature)
                {{-- JIKA SUDAH TTD --}}
                <div class="flex flex-col items-center animate-fade-in">
                    <div class="relative">
                        <div class="absolute -top-3 -right-3 bg-green-500 text-white rounded-full p-1 shadow-md">
                            <span class="material-symbols-outlined text-lg">check</span>
                        </div>
                        <div
                            class="border-2 border-green-400 border-dashed bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                            <img src="{{ asset('storage/' . $dailyReport->parent_guardian_signature) }}"
                                alt="Tanda Tangan" class="h-32 w-auto object-contain filter dark:invert">
                        </div>
                    </div>
                    <p class="mt-4 text-gray-800 dark:text-white font-semibold">{{ $dailyReport->parent_guardian_name }}
                    </p>
                    <p class="text-green-600 font-bold text-sm">Laporan Telah Dikonfirmasi</p>
                    <p class="text-xs text-gray-400 mt-1">Terima kasih Ayah/Bunda atas kerjasamanya.</p>
                </div>
            @else
                {{-- JIKA BELUM TTD (FORM) --}}
                <div class="max-w-md mx-auto text-left">

                    {{-- Input Nama Orang Tua (BARU) --}}
                    <div class="mb-4">
                        <label for="visible_parent_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Orang Tua /
                            Wali</label>
                        <input type="text" id="visible_parent_name"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-pink-500 focus:border-pink-500"
                            placeholder="Masukkan nama lengkap...">
                    </div>

                    <p class="text-sm text-gray-500 mb-2">Tanda tangan di kotak di bawah ini:</p>

                    {{-- Canvas Container --}}
                    <div class="relative group mb-4">
                        <canvas id="signature-pad"
                            class="block w-full bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-crosshair touch-none shadow-inner h-48"></canvas>
                        <div
                            class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20 group-hover:opacity-10 transition-opacity">
                            <span class="text-2xl text-gray-400 font-bold select-none">Area Tanda Tangan</span>
                        </div>
                    </div>

                    <div class="flex justify-center gap-3">
                        <button type="button" id="clear-btn"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Ulangi
                        </button>
                        <button type="button" id="save-btn"
                            class="px-6 py-2 text-sm font-bold text-white bg-pink-500 rounded-lg hover:bg-pink-600 transition shadow-md">
                            Simpan Konfirmasi
                        </button>
                    </div>

                    {{-- Hidden Form --}}
                    <form id="signature-form" action="{{ route('student.daily-report.sign', $dailyReport->id) }}"
                        method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="signature" id="signature-input">
                        {{-- Input hidden untuk nama --}}
                        <input type="hidden" name="parent_name" id="parent-name-input">
                    </form>
                </div>
            @endif
        </div>

    </div>

    {{-- SCRIPT --}}
    @if (!$dailyReport->parent_guardian_signature)
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const canvas = document.getElementById('signature-pad');
                const nameInput = document.getElementById('visible_parent_name'); // Input nama terlihat

                if (canvas) {
                    function resizeCanvas() {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                    }
                    window.addEventListener("resize", resizeCanvas);
                    resizeCanvas();

                    const signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(30, 64, 175)'
                    });

                    document.getElementById('clear-btn').addEventListener('click', () => signaturePad.clear());

                    document.getElementById('save-btn').addEventListener('click', () => {
                        // Validasi Nama
                        if (!nameInput.value.trim()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Nama Kosong',
                                text: 'Mohon masukkan nama orang tua/wali.',
                                confirmButtonColor: '#ec4899'
                            });
                            return;
                        }

                        // Validasi Tanda Tangan
                        if (signaturePad.isEmpty()) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tanda Tangan Kosong',
                                text: 'Mohon tanda tangan terlebih dahulu.',
                                confirmButtonColor: '#ec4899'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Simpan Konfirmasi?',
                            text: 'Laporan akan ditandai sebagai sudah dibaca.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#ec4899',
                            confirmButtonText: 'Ya, Simpan'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Set value ke hidden inputs
                                document.getElementById('signature-input').value = signaturePad
                                    .toDataURL('image/png');
                                document.getElementById('parent-name-input').value = nameInput.value;

                                // Submit form
                                document.getElementById('signature-form').submit();
                                Swal.fire({
                                    title: 'Menyimpan...',
                                    didOpen: () => Swal.showLoading()
                                });
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
