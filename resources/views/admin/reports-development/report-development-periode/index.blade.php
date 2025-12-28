<x-app-layout>
    <x-slot:title>Pilih Periode Laporan - {{ $student->student_name }}</x-slot:title>

    <div class="min-h-[85vh] flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-900">

        {{-- ALERT ERROR (SERVER SIDE) --}}
        @if (session('error'))
            <div class="w-full max-w-xl mb-4 animate-fade-in-down">
                <div
                    class="flex items-center p-4 text-red-800 rounded-xl bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200 shadow-sm">
                    <span class="material-symbols-outlined flex-shrink-0 w-6 h-6 mr-3">error</span>
                    <div class="text-sm font-medium">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        {{-- TOMBOL KEMBALI --}}
        <div class="w-full max-w-xl mb-6">
            <a href="{{ route('development-reports.index') }}"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 transition-colors font-medium text-sm group px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                <span
                    class="material-symbols-outlined text-lg transition-transform group-hover:-translate-x-1">arrow_back</span>
                Kembali ke Daftar Siswa
            </a>
        </div>

        {{-- MAIN CARD --}}
        <div
            class="w-full max-w-xl bg-white dark:bg-gray-800 rounded-3xl shadow-2xl shadow-indigo-100/50 dark:shadow-none border border-white dark:border-gray-700 overflow-hidden relative">

            {{-- HEADER BACKGROUND --}}
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-indigo-600 to-purple-700">
                {{-- Pattern Overlay (Optional) --}}
                <div class="absolute inset-0 opacity-10"
                    style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;">
                </div>
            </div>

            <div class="relative px-8 pt-12 pb-8">

                {{-- HEADER CONTENT --}}
                <div class="text-center mb-8 relative z-10">
                    <div
                        class="w-20 h-20 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg border-4 border-white dark:border-gray-700">
                        @if ($student->user_photo && file_exists(storage_path('app/public/' . $student->user_photo)))
                            <img src="{{ asset('storage/' . $student->user_photo) }}"
                                class="w-full h-full rounded-full object-cover">
                        @else
                            <div
                                class="w-full h-full rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-2xl">
                                {{ substr($student->student_name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $student->student_name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Nomor Induk: {{ $student->student_number ?? '-' }}
                    </p>
                    <div
                        class="mt-2 inline-block px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider border border-indigo-100">
                        Buat Laporan Baru
                    </div>
                </div>

                {{-- FORM START --}}
                <form action="{{ route('development-reports.create', $student->id) }}" method="GET" id="periodForm">

                    <div class="space-y-6">

                        {{-- INFO BOX --}}
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 flex gap-3 items-start">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 mt-0.5">info</span>
                            <div class="text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                                <strong class="block mb-1 font-bold">Mengapa tanggal ini penting?</strong>
                                Sistem akan menggunakan rentang tanggal ini untuk menghitung total <strong>Kehadiran
                                    (Presensi)</strong> dan mengambil data <strong>Pengukuran Fisik (BB/TB)</strong>
                                terakhir secara otomatis.
                            </div>
                        </div>

                        {{-- ROW 1: AKADEMIK --}}
                        <div class="grid grid-cols-2 gap-5">
                            {{-- Tahun Ajaran --}}
                            <div class="space-y-2">
                                <label
                                    class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider ml-1">
                                    Tahun Ajaran
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span
                                            class="material-symbols-outlined text-gray-400 group-focus-within:text-indigo-500 transition-colors text-lg">school</span>
                                    </div>
                                    <select name="academic_year" required
                                        class="w-full pl-10 pr-8 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-semibold cursor-pointer bg-gray-50/50 hover:bg-white">
                                        @php
                                            $currentY = date('Y');
                                            $currentM = date('n');
                                            // Jika bulan >= Juli (7), berarti tahun ajaran baru (ex: 2024/2025)
                                            // Jika bulan < Juli, berarti masih tahun ajaran lalu (ex: 2023/2024)
                                            $startYear = $currentM >= 7 ? $currentY : $currentY - 1;
                                        @endphp

                                        @for ($i = -1; $i <= 1; $i++)
                                            @php $y = $startYear + $i; @endphp
                                            <option value="{{ $y }}/{{ $y + 1 }}"
                                                {{ $i === 0 ? 'selected' : '' }}>
                                                {{ $y }}/{{ $y + 1 }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Semester --}}
                            <div class="space-y-2">
                                <label
                                    class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider ml-1">
                                    Semester
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span
                                            class="material-symbols-outlined text-gray-400 group-focus-within:text-indigo-500 transition-colors text-lg">filter_list</span>
                                    </div>
                                    <select name="semester" required
                                        class="w-full pl-10 pr-8 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-semibold cursor-pointer bg-gray-50/50 hover:bg-white">
                                        {{-- Semester 1 biasanya Juli - Desember --}}
                                        <option value="1 (Ganjil)" {{ date('n') >= 7 ? 'selected' : '' }}>Semester 1
                                            (Ganjil)</option>
                                        {{-- Semester 2 biasanya Januari - Juni --}}
                                        <option value="2 (Genap)" {{ date('n') < 7 ? 'selected' : '' }}>Semester 2
                                            (Genap)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- ROW 2: TANGGAL --}}
                        <div
                            class="p-5 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-100 dark:border-gray-600">
                            <div class="mb-4 flex items-center justify-between">
                                <label
                                    class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">
                                    Rentang Periode Laporan
                                </label>
                                <div id="durationText"
                                    class="text-[10px] font-medium text-gray-400 bg-white px-2 py-1 rounded border border-gray-200 hidden">
                                    0 Hari
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Tanggal Mulai --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 ml-1">Tanggal
                                        Awal</label>
                                    <div class="relative">
                                        <input type="date" name="start_date" id="start_date"
                                            value="{{ date('Y-m-01') }}" required
                                            class="w-full px-3 py-2 rounded-lg border-gray-300 dark:border-gray-600 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                    </div>
                                </div>

                                {{-- Tanggal Akhir --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 ml-1">Tanggal
                                        Akhir</label>
                                    <div class="relative">
                                        <input type="date" name="end_date" id="end_date"
                                            value="{{ date('Y-m-d') }}" required
                                            class="w-full px-3 py-2 rounded-lg border-gray-300 dark:border-gray-600 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                    </div>
                                </div>
                            </div>

                            {{-- Error Message Place Holder (Client Side) --}}
                            <div id="errorMessage"
                                class="hidden mt-3 text-red-600 text-xs flex items-center animate-pulse">
                                <span class="material-symbols-outlined text-base mr-1">warning</span>
                                <span id="errorText"></span>
                            </div>
                        </div>

                        {{-- SUBMIT BUTTON --}}
                        <div class="pt-2">
                            <button type="submit" id="submitBtn"
                                class="group w-full flex items-center justify-center gap-3 py-3.5 px-4 bg-gray-900 hover:bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 transform active:scale-[0.98]">
                                <span>Lanjut ke Pengisian Data</span>
                                <span
                                    class="material-symbols-outlined text-xl transition-transform group-hover:translate-x-1">arrow_forward</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT VALIDASI --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('periodForm');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const submitBtn = document.getElementById('submitBtn');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            const durationText = document.getElementById('durationText');

            function validateDates() {
                const startVal = startDateInput.value;
                const endVal = endDateInput.value;

                if (!startVal || !endVal) return true;

                const start = new Date(startVal);
                const end = new Date(endVal);

                // Hitung durasi hari (opsional, untuk info)
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                // Reset Tampilan Error
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                submitBtn.classList.add('bg-gray-900', 'hover:bg-indigo-600');

                errorMessage.classList.add('hidden');
                startDateInput.classList.remove('border-red-500', 'bg-red-50', 'text-red-900');
                endDateInput.classList.remove('border-red-500', 'bg-red-50', 'text-red-900');

                // Validasi Logika: Tanggal Awal > Tanggal Akhir
                if (start > end) {
                    // Tampilkan Error
                    errorText.innerText = "Tanggal Akhir tidak boleh lebih kecil dari Tanggal Awal.";
                    errorMessage.classList.remove('hidden');

                    // Matikan Tombol
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('bg-gray-900', 'hover:bg-indigo-600');
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');

                    // Highlight Input Merah
                    startDateInput.classList.add('border-red-500', 'bg-red-50', 'text-red-900');
                    endDateInput.classList.add('border-red-500', 'bg-red-50', 'text-red-900');

                    durationText.classList.add('hidden');
                    return false;
                }

                // Tampilkan Durasi jika valid
                durationText.innerText = (diffDays + 1) + " Hari"; // +1 termasuk hari awal
                durationText.classList.remove('hidden');

                return true;
            }

            // Jalankan validasi saat user mengubah tanggal
            startDateInput.addEventListener('change', validateDates);
            endDateInput.addEventListener('change', validateDates);

            // Jalankan sekali saat load (jika browser autofill)
            validateDates();

            // Cegah submit jika tidak valid
            form.addEventListener('submit', function(e) {
                if (!validateDates()) {
                    e.preventDefault();
                    // Efek getar sederhana jika error
                    form.classList.add('animate-pulse');
                    setTimeout(() => form.classList.remove('animate-pulse'), 500);
                }
            });
        });
    </script>
</x-app-layout>
