<x-app-layout>
    @push('head')
        {{-- Library Signature Pad --}}
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    @endpush

    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-4 sm:p-8 rounded-lg shadow-lg space-y-6">

        {{-- HEADER --}}
        <div class="text-center border-b border-gray-200 dark:border-gray-800 pb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Laporan Harian Baru</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $activityTransaction->service_id == 1 ? 'Baby Childhood' : 'Children Daycare' }}
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="material-symbols-outlined text-red-500">error</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan input:</h3>
                        <ul class="list-disc list-inside text-sm text-red-700 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form id="dailyReportForm" action="{{ route('daily-report.store') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="activity_transaction_id" value="{{ $activityTransaction->id }}">

            {{-- 1. IDENTITAS & TANGGAL --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Siswa --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Siswa</label>
                        <input type="text" value="{{ $activityTransaction->student->student_name }}" disabled
                            class="w-full rounded-lg border-gray-300 bg-gray-200 text-gray-600 cursor-not-allowed">

                        {{-- Status Absensi --}}
                        <div class="mt-2 flex items-center gap-2 text-sm" id="attendance-status-box">
                            <span class="material-symbols-outlined text-gray-400 text-lg" id="att-icon">pending</span>
                            <span class="font-medium text-gray-500" id="attendance-status">Mengecek status
                                absensi...</span>
                        </div>
                    </div>

                    {{-- Tanggal & Suhu --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="period"
                                class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                            <input type="date" name="period" id="period"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white focus:ring-indigo-500"
                                value="{{ old('period', now()->toDateString()) }}">
                        </div>
                        <div>
                            <label for="body_temperature"
                                class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Suhu (°C)</label>
                            <input type="number" step="0.1" name="body_temperature" id="body_temperature"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white focus:ring-indigo-500"
                                placeholder="36.5" value="{{ old('body_temperature') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. KONDISI FISIK --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Makan Pagi --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Makan Pagi</label>
                    <div class="flex gap-4">
                        <label
                            class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition w-full">
                            <input type="radio" name="breakfast" value="sudah" @checked(old('breakfast', 'sudah') == 'sudah')
                                class="text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-gray-700">Sudah</span>
                        </label>
                        <label
                            class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition w-full">
                            <input type="radio" name="breakfast" value="belum" @checked(old('breakfast') == 'belum')
                                class="text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-gray-700">Belum</span>
                        </label>
                    </div>
                </div>

                {{-- Kondisi --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kondisi Mood</label>
                    <select name="condition"
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white focus:ring-indigo-500">
                        <option value="tenang" @selected(old('condition') == 'tenang')>Tenang 😊</option>
                        <option value="rewel" @selected(old('condition') == 'rewel')>Rewel 😢</option>
                        <option value="temper tantrum" @selected(old('condition') == 'temper tantrum')>Temper Tantrum 😡</option>
                    </select>
                </div>
            </div>

            {{-- 3. KESEHATAN --}}
            <div class="bg-red-50 dark:bg-red-900/10 p-5 rounded-xl border border-red-100 dark:border-red-800">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status Kesehatan</label>
                <div class="flex gap-6 mb-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="health_status" value="sehat" @checked(old('health_status', 'sehat') == 'sehat')
                            class="text-green-600 focus:ring-green-500" onchange="toggleKesehatanUI(false)">
                        <span class="ml-2 font-medium text-gray-700 dark:text-gray-300">Sehat 💚</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="health_status" value="sakit" @checked(old('health_status') == 'sakit')
                            class="text-red-600 focus:ring-red-500" onchange="toggleKesehatanUI(true)">
                        <span class="ml-2 font-medium text-gray-700 dark:text-gray-300">Sakit 🤒</span>
                    </label>
                </div>

                <div id="deskripsi_kesehatan" class="hidden space-y-4 border-t border-red-200 pt-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Keluhan / Gejala</label>
                        <textarea name="sickness_description" rows="2"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 text-sm"
                            placeholder="Contoh: Batuk, pilek, demam...">{{ old('sickness_description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Penanganan Obat</label>
                        <div class="flex gap-4">
                            <label class="flex items-center text-sm">
                                <input type="radio" name="medication_status" value="disertai obat"
                                    @checked(old('medication_status') == 'disertai obat') class="text-red-600">
                                <span class="ml-2">Disertai Obat 💊</span>
                            </label>
                            <label class="flex items-center text-sm">
                                <input type="radio" name="medication_status" value="tanpa obat"
                                    @checked(old('medication_status') == 'tanpa obat') class="text-red-600">
                                <span class="ml-2">Tanpa Obat</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- FORM KHUSUS: BABY CHILDHOOD (Service ID 1) --}}
            {{-- ======================================================== --}}
            @if ($activityTransaction->service_id == 1)
                @include('admin.daily-report.create-daily.baby-form')
            @endif

            {{-- ======================================================== --}}
            {{-- FORM KHUSUS: CHILDREN DAYCARE (Service ID 2) --}}
            {{-- ======================================================== --}}
            @if ($activityTransaction->service_id == 2)
                @include('admin.daily-report.create-daily.cildren-form')
            @endif

            {{-- 4. STIMULASI & CATATAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Stimulasi (Auto) --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Stimulasi (MMDST)
                        <span class="text-xs font-normal text-gray-400 ml-1">*Terisi otomatis</span>
                    </label>
                    <textarea name="stimulation_description" id="stimulation_description" rows="5" readonly
                        class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-600 text-sm cursor-not-allowed resize-none shadow-inner"></textarea>
                </div>

                {{-- Catatan Manual --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan</label>
                    <textarea name="notes" rows="5" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-sm"
                        placeholder="Catatan khusus untuk orang tua...">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- 5. VALIDASI GURU (TTD) --}}
            <div class="border-t-2 border-dashed border-gray-200 pt-8 mt-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 text-center">Validasi Guru Pendamping</h3>

                <div class="max-w-md mx-auto bg-gray-50 rounded-xl p-6 border border-gray-200">

                    {{-- Input Nama Guru --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Guru</label>
                        <input type="text" name="teacher_name"
                            value="{{ old('teacher_name', Auth::user()->user_name) }}"
                            class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-sm font-medium">
                    </div>

                    {{-- Canvas Tanda Tangan --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan</label>
                        <div
                            class="relative group bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
                            <canvas id="teacher-signature-pad" class="block w-full h-40 cursor-crosshair"></canvas>
                            <div class="absolute top-2 right-2">
                                <button type="button" id="clear-sig-btn"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded transition">
                                    Hapus
                                </button>
                            </div>
                            {{-- Input hidden untuk menyimpan data base64 --}}
                            <input type="hidden" name="teacher_signature" id="teacher-signature-input">
                        </div>
                        <p class="text-xs text-gray-400 mt-1 text-center">Silakan tanda tangan di kotak di atas.</p>
                    </div>

                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('daily-report.index') }}"
                    class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="button" id="submit-btn"
                    class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-700 hover:shadow-xl transition transform hover:-translate-y-0.5">
                    Simpan Laporan
                </button>
            </div>

        </form>
    </div>

    {{-- SCRIPT UTAMA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. INIT TANDA TANGAN ---
            const canvas = document.getElementById('teacher-signature-pad');
            let signaturePad;

            if (canvas) {
                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                }
                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();

                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)'
                });

                document.getElementById('clear-sig-btn').addEventListener('click', () => signaturePad.clear());
            }

            // --- 2. SUBMIT HANDLER ---
            document.getElementById('submit-btn').addEventListener('click', function(e) {
                e.preventDefault();

                // Masukkan data tanda tangan ke input hidden sebelum submit
                if (signaturePad && !signaturePad.isEmpty()) {
                    document.getElementById('teacher-signature-input').value = signaturePad.toDataURL(
                        'image/png');
                }

                Swal.fire({
                    title: 'Simpan Laporan?',
                    text: "Pastikan semua data sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Cek Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            didOpen: () => Swal.showLoading()
                        });
                        document.getElementById('dailyReportForm').submit();
                    }
                });
            });

            // --- 3. LOGIC LAINNYA (ABSENSI, KESEHATAN, DLL) ---
            // (Sama seperti script Anda sebelumnya, saya rapikan sedikit)

            const studentId = {{ $activityTransaction->student->id }};
            const atId = {{ $activityTransaction->id }};
            const periodEl = document.getElementById('period');

            // Initial Load
            checkAttendance(studentId, periodEl.value);
            loadStimulation(atId, periodEl.value);

            // On Date Change
            periodEl.addEventListener('change', function() {
                checkAttendance(studentId, this.value);
                loadStimulation(atId, this.value);
                // Load materi jika ada fungsi loadSubthemes (untuk Children)
                if (typeof loadSubthemes === 'function') loadSubthemes(this.value);
            });

            // Toggle Sakit UI
            window.toggleKesehatanUI = function(isSick) {
                const el = document.getElementById('deskripsi_kesehatan');
                if (isSick) el.classList.remove('hidden');
                else el.classList.add('hidden');
            }
            // Set initial UI state based on old input
            const isSickInit = document.querySelector('input[name="health_status"]:checked')?.value === 'sakit';
            toggleKesehatanUI(isSickInit);

            // --- HELPER FUNCTIONS ---

            function checkAttendance(sid, date) {
                const statusEl = document.getElementById('attendance-status');
                const iconEl = document.getElementById('att-icon');
                const boxEl = document.getElementById('attendance-status-box');

                statusEl.textContent = 'Mengecek...';

                fetch(`{{ route('daily-report.check-attendance', ['student' => '_SID_', 'date' => '_DATE_']) }}`
                        .replace('_SID_', sid).replace('_DATE_', date))
                    .then(r => r.json())
                    .then(data => {
                        statusEl.textContent = data.status || 'Status tidak diketahui';

                        // Visual Feedback
                        boxEl.className =
                            "mt-2 flex items-center gap-2 text-sm px-3 py-2 rounded-lg transition-colors " +
                            (data.status === 'Hadir' ? 'bg-green-100 text-green-700' :
                                'bg-amber-50 text-amber-600');

                        iconEl.textContent = data.status === 'Hadir' ? 'check_circle' : 'warning';
                        iconEl.className = "material-symbols-outlined text-lg " +
                            (data.status === 'Hadir' ? 'text-green-600' : 'text-amber-500');
                    })
                    .catch(() => {
                        statusEl.textContent = 'Gagal koneksi ke server';
                    });
            }

            function loadStimulation(txId, date) {
                const area = document.getElementById('stimulation_description');
                area.value = 'Memuat...';
                fetch(`{{ route('daily-report.stimulation.suggest', ['activityTransaction' => '_AT_', 'date' => '_DATE_']) }}`
                        .replace('_AT_', txId).replace('_DATE_', date))
                    .then(r => r.json())
                    .then(d => area.value = d.text || 'Tidak ada data.')
                    .catch(() => area.value = 'Gagal memuat.');
            }

            // Helper untuk Tablenya (Alpine JS logic sudah ada di partials atau inline)
        });
    </script>
</x-app-layout>
