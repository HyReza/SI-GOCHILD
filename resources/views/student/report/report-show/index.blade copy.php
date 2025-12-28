<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Laporan Pembelajaran') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @php
        // --- PRE-PROCESSING DATA ---
        $startDate = \Carbon\Carbon::parse($report->start_date);
        $startYear = $startDate->month >= 7 ? $startDate->year : $startDate->year - 1;
        $academicYear = $startYear . ' / ' . ($startYear + 1);
        $semester = $report->semester ?? '-';
        $isSigned = !empty($report->parent_signature);

        // Parsing Presensi (JSON)
        $att = json_decode($report->attendance_summary, true) ?? [];
        // Handle format huruf besar/kecil key array
        $sakit = $att['Sakit'] ?? ($att['sakit'] ?? 0);
        $izin = $att['Izin'] ?? ($att['izin'] ?? 0);
        $alpha = $att['Alpha'] ?? ($att['alpha'] ?? 0);
        $hadir = $att['Hadir'] ?? ($att['hadir'] ?? 0);

        // Gender & Kelas
        $genderText = $report->student->gender == 'L' || $report->student->gender == 1 ? 'Laki-laki' : 'Perempuan';
        $className = $report->class_name ?? ($report->student->group_name ?? 'Belum Diatur');
    @endphp

    <style>
        .signature-canvas {
            border: 2px dashed #cbd5e1;
            /* slate-300 */
            width: 100%;
            height: 200px;
            background-color: #f8fafc;
            /* slate-50 */
            border-radius: 0.75rem;
            cursor: crosshair;
            touch-action: none;
            /* Cegah scroll saat tanda tangan di HP */
        }

        /* Memperbaiki tampilan list bullet pada narasi */
        .prose ul {
            list-style-type: disc;
            padding-left: 1.5rem;
        }

        .prose ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
        }
    </style>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- === BAGIAN 1: HEADER & NAVIGASI === --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <a href="{{ route('student.report.history') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>

                @if ($isSigned)
                    <a href="{{ route('student.report.pdf', $report->id) }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-md transition ease-in-out duration-150">
                        <i class="fas fa-print mr-2"></i> Cetak PDF
                    </a>
                @endif
            </div>

            {{-- Flash Message --}}
            @if (session('success'))
                <div
                    class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            {{-- === BAGIAN 2: IDENTITAS SISWA (CARD HERO) === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                <div class="h-32 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600"></div>

                <div class="px-8 pb-8">
                    <div class="flex flex-col md:flex-row items-end -mt-12 mb-6">
                        <div class="relative z-10">
                            <div
                                class="w-32 h-32 rounded-2xl border-4 border-white shadow-lg overflow-hidden bg-gray-200">
                                @if ($report->student->user_photo && Storage::disk('public')->exists($report->student->user_photo))
                                    <img src="{{ asset('storage/' . $report->student->user_photo) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ $report->student->student_name }}&background=e0e7ff&color=4f46e5&size=128&bold=true"
                                        class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>

                        <div class="md:ml-6 mt-4 md:mt-0 flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $report->student->student_name }}</h1>
                            <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                <span
                                    class="bg-gray-100 px-3 py-1 rounded-full border border-gray-200 flex items-center">
                                    <i class="fas fa-id-card text-indigo-500 mr-2"></i>
                                    {{ $report->student->student_number ?? 'NIS Kosong' }}
                                </span>
                                <span
                                    class="bg-gray-100 px-3 py-1 rounded-full border border-gray-200 flex items-center">
                                    <i class="fas fa-venus-mars text-blue-500 mr-2"></i> {{ $genderText }}
                                </span>
                                <span
                                    class="bg-green-50 px-3 py-1 rounded-full border border-green-200 text-green-700 font-semibold flex items-center">
                                    <i class="fas fa-chalkboard-teacher mr-2"></i> {{ $className }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 md:mt-0 text-right">
                            <div
                                class="inline-block px-5 py-3 rounded-xl bg-indigo-50 border border-indigo-100 text-right shadow-sm">
                                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1">Tahun Ajaran
                                    {{ $academicYear }}</p>
                                <p class="text-xl font-extrabold text-indigo-800">{{ $semester }}</p>
                                <p class="text-[10px] text-indigo-500 mt-1">
                                    {{ \Carbon\Carbon::parse($report->start_date)->translatedFormat('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($report->end_date)->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === BAGIAN 3: DATA FISIK & KEHADIRAN (GRID) === --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Kiri: Statistik Fisik & Absen --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3 text-blue-600">
                            <i class="fas fa-ruler-combined"></i>
                        </span>
                        Data Pertumbuhan & Kehadiran
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                        <div
                            class="p-4 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-100 text-center shadow-sm">
                            <p class="text-xs text-blue-500 uppercase font-bold mb-1">Berat Badan</p>
                            <p class="text-2xl font-black text-blue-700">{{ $report->weight + 0 }} <span
                                    class="text-sm font-medium text-gray-500">kg</span></p>
                        </div>
                        <div
                            class="p-4 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-100 text-center shadow-sm">
                            <p class="text-xs text-green-500 uppercase font-bold mb-1">Tinggi Badan</p>
                            <p class="text-2xl font-black text-green-700">{{ $report->height + 0 }} <span
                                    class="text-sm font-medium text-gray-500">cm</span></p>
                        </div>
                        <div
                            class="p-4 bg-gradient-to-br from-yellow-50 to-white rounded-xl border border-yellow-100 text-center shadow-sm">
                            <p class="text-xs text-yellow-600 uppercase font-bold mb-1">Total Hadir</p>
                            <p class="text-2xl font-black text-yellow-700">{{ $hadir }} <span
                                    class="text-sm font-medium text-gray-500">Hari</span></p>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 rounded-xl p-4 border border-gray-200 flex justify-between text-center divide-x divide-gray-200">
                        <div class="flex-1 px-2">
                            <span class="block font-bold text-lg text-gray-800">{{ $sakit }}</span>
                            <span class="text-xs text-gray-500 uppercase">Sakit</span>
                        </div>
                        <div class="flex-1 px-2">
                            <span class="block font-bold text-lg text-gray-800">{{ $izin }}</span>
                            <span class="text-xs text-gray-500 uppercase">Izin</span>
                        </div>
                        <div class="flex-1 px-2">
                            <span class="block font-bold text-lg text-red-600">{{ $alpha }}</span>
                            <span class="text-xs text-red-500 uppercase">Alpha</span>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Legend Nilai --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center mr-3 text-indigo-600">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        Keterangan Nilai
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <span
                                class="w-10 flex-shrink-0 px-2 py-1 bg-blue-100 text-blue-800 text-[10px] font-bold rounded text-center border border-blue-200">BSB</span>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Berkembang Sangat Baik</p>
                                <p class="text-xs text-gray-500 leading-snug">Anak mandiri & konsisten tanpa bantuan.
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="w-10 flex-shrink-0 px-2 py-1 bg-green-100 text-green-800 text-[10px] font-bold rounded text-center border border-green-200">BSH</span>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Berkembang Sesuai Harapan</p>
                                <p class="text-xs text-gray-500 leading-snug">Anak mampu melakukan kegiatan sendiri.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="w-10 flex-shrink-0 px-2 py-1 bg-yellow-100 text-yellow-800 text-[10px] font-bold rounded text-center border border-yellow-200">MB</span>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Mulai Berkembang</p>
                                <p class="text-xs text-gray-500 leading-snug">Anak mulai melakukan, perlu bantuan.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="w-10 flex-shrink-0 px-2 py-1 bg-red-100 text-red-800 text-[10px] font-bold rounded text-center border border-red-200">BB</span>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-800">Belum Berkembang</p>
                                <p class="text-xs text-gray-500 leading-snug">Anak belum mampu, perlu bimbingan penuh.
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- === BAGIAN 4: DETAIL PENILAIAN (CHECKLIST) === --}}
            @if ($groupedDetails->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"
                    x-data="{ open: true }">
                    <div class="px-8 py-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition"
                        @click="open = !open">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-list-check mr-3 text-indigo-600"></i> Detail Capaian Kurikulum
                        </h3>
                        <div class="text-gray-500">
                            <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </div>
                    </div>

                    <div x-show="open" x-transition class="p-8">
                        @foreach ($groupedDetails as $themeName => $details)
                            <div class="mb-10 last:mb-0">
                                <div class="flex items-center mb-4">
                                    <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
                                    <h4 class="text-lg font-bold text-gray-800">{{ $themeName }}</h4>
                                </div>

                                @php
                                    $subThemes = $details->groupBy(function ($item) {
                                        return $item->material->subTheme->sub_theme_name ?? 'Materi Umum';
                                    });
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach ($subThemes as $subThemeName => $materials)
                                        <div
                                            class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition">
                                            <h5
                                                class="font-bold text-indigo-700 text-sm uppercase tracking-wide mb-3 border-b border-gray-100 pb-2">
                                                {{ $subThemeName }}
                                            </h5>
                                            <ul class="space-y-3">
                                                @foreach ($materials as $det)
                                                    <li class="flex justify-between items-start text-sm">
                                                        <span
                                                            class="text-gray-700 w-3/4 leading-snug">{{ $det->material->material_name }}</span>
                                                        @php
                                                            $badgeClass = match ($det->score) {
                                                                'BSB' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                                'BSH' => 'bg-green-100 text-green-800 border-green-200',
                                                                'MB'
                                                                    => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                                'BB' => 'bg-red-100 text-red-700 border-red-200',
                                                                default => 'bg-gray-100 text-gray-600',
                                                            };
                                                        @endphp
                                                        <span
                                                            class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeClass }}">
                                                            {{ $det->score }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- === BAGIAN 5: NARASI & CATATAN === --}}
            <div class="grid grid-cols-1 gap-8">
                {{-- Narasi Per Tema --}}
                @if (isset($themeNotes) && count($themeNotes) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-pencil-alt mr-3 text-indigo-600"></i> Catatan Capaian per Tema
                            </h3>
                        </div>
                        <div class="p-8 grid grid-cols-1 gap-6">
                            @foreach ($groupedDetails as $themeName => $details)
                                @php
                                    $firstItem = $details->first();
                                    $themeId = $firstItem->material->subTheme->theme_id ?? null;
                                    $note = $themeNotes[$themeId] ?? null;
                                @endphp

                                @if ($note)
                                    <div
                                        class="bg-white rounded-xl border border-gray-200 p-6 relative shadow-sm hover:shadow-md transition">
                                        <div class="absolute top-4 left-0 w-1 h-12 bg-indigo-500 rounded-r"></div>
                                        <h4 class="font-bold text-gray-800 mb-2 pl-3">{{ $themeName }}</h4>
                                        <div
                                            class="prose prose-sm text-gray-600 leading-relaxed text-justify whitespace-pre-line pl-3">
                                            {{ $note }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Catatan Guru & Kesimpulan --}}
                @if ($report->teacher_notes || $report->development_info_text)
                    <div class="bg-indigo-50 rounded-2xl border border-indigo-100 overflow-hidden p-8">
                        <h3
                            class="text-lg font-bold text-indigo-900 mb-6 flex items-center border-b border-indigo-200 pb-3">
                            <i class="fas fa-comment-dots mr-3"></i> Catatan Akhir & Kesimpulan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @if ($report->teacher_notes)
                                <div class="bg-white p-5 rounded-xl border border-indigo-100 shadow-sm">
                                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Catatan
                                        Wali Kelas</h4>
                                    <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                                        {{ $report->teacher_notes }}
                                    </div>
                                </div>
                            @endif
                            @if ($report->development_info_text)
                                <div class="bg-white p-5 rounded-xl border border-indigo-100 shadow-sm">
                                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Kesimpulan
                                        Perkembangan</h4>
                                    <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                                        {{ $report->development_info_text }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- === BAGIAN 6: LEMBAR PENGESAHAN (TANDA TANGAN) === --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-12">
                <div class="px-8 py-6 bg-gray-900 text-white text-center">
                    <h3 class="text-xl font-bold uppercase tracking-widest">Lembar Pengesahan</h3>
                    <p class="text-xs text-gray-400 mt-1">Dokumen ini telah ditandatangani secara digital</p>
                </div>

                <div class="p-10 bg-white">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        {{-- LOOPING SEMUA PIHAK YANG TANDA TANGAN (LENGKAP) --}}
                        @foreach ([['role' => 'Orang Tua / Wali', 'name' => $report->parent_name, 'sig' => $report->parent_signature, 'is_parent' => true], ['role' => 'Wali Kelas', 'name' => $report->teacher_name, 'sig' => $report->teacher_signature, 'is_parent' => false], ['role' => 'Konsultan', 'name' => $report->consultant_name, 'sig' => $report->consultant_signature, 'is_parent' => false], ['role' => 'Kepala Sekolah', 'name' => $report->principal_name, 'sig' => $report->principal_signature, 'is_parent' => false]] as $person)
                            <div class="flex flex-col items-center justify-end h-full">
                                <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-4">
                                    {{ $person['role'] }}</h4>

                                <div class="h-24 w-full flex items-end justify-center mb-2">
                                    @if ($person['sig'] && Storage::disk('public')->exists($person['sig']))
                                        <img src="{{ asset('storage/' . $person['sig']) }}"
                                            class="max-h-24 object-contain filter drop-shadow-sm hover:scale-105 transition">
                                    @else
                                        {{-- Khusus Kolom Orang Tua jika belum TTD --}}
                                        @if ($person['is_parent'] && !$isSigned)
                                            <span
                                                class="text-xs text-red-500 italic bg-red-50 px-2 py-1 rounded border border-red-100">
                                                Menunggu Tanda Tangan Anda
                                            </span>
                                        @else
                                            <span
                                                class="text-xs text-gray-300 italic mb-2 bg-gray-100 px-3 py-1 rounded">Belum
                                                Tanda Tangan</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="w-full border-b-2 border-gray-300 mb-1"></div>
                                <p class="text-xs font-bold text-gray-900 min-w-[100px] break-words">
                                    {{ $person['name'] ?? '( ........................ )' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- === BAGIAN 7: FORM TANDA TANGAN (JIKA BELUM TTD) === --}}
            @if (!$isSigned)
                <div
                    class="bg-yellow-50 border-2 border-dashed border-yellow-400 rounded-xl p-8 shadow-sm max-w-2xl mx-auto">
                    <h3 class="text-xl font-bold text-yellow-800 mb-2 flex items-center justify-center">
                        <i class="fas fa-file-signature mr-2"></i> Formulir Pengesahan Orang Tua
                    </h3>
                    <p class="text-sm text-yellow-700 text-center mb-6">
                        Mohon konfirmasi dan bubuhkan tanda tangan digital Anda untuk mengesahkan laporan ini.
                    </p>

                    <form action="{{ route('student.report.sign', $report->id) }}" method="POST" id="signForm">
                        @csrf
                        <input type="hidden" name="ttd_ortu" id="input_ttd_ortu">

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Nama Lengkap Orang
                                Tua</label>
                            <input type="text" name="parent_name" id="parent_name" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3"
                                value="{{ old('parent_name', $report->student->father_name ?? $report->student->mother_name) }}"
                                placeholder="Contoh: Budi Santoso">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Tanda Tangan</label>
                            <div
                                class="relative bg-white rounded-xl overflow-hidden border-2 border-gray-300 shadow-inner">
                                <canvas id="signatureCanvas" class="signature-canvas w-full h-48"></canvas>
                                <button type="button" id="clearSigBtn"
                                    class="absolute top-2 right-2 text-xs bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 px-3 py-1.5 rounded-lg transition font-medium border border-gray-200">
                                    <i class="fas fa-eraser mr-1"></i> Hapus
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i> Gunakan jari (di HP) atau mouse (di Laptop)
                                untuk tanda tangan.
                            </p>
                        </div>

                        <button type="submit" onclick="submitSignature(event)"
                            class="w-full inline-flex justify-center items-center px-6 py-3.5 bg-indigo-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 shadow-lg transition ease-in-out duration-150 transform hover:-translate-y-0.5">
                            <i class="fas fa-check-double mr-2"></i> Simpan & Sahkan Laporan
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    @if (!$isSigned)
        <script>
            let pad;
            const canvas = document.getElementById('signatureCanvas');

            if (canvas) {
                // Konfigurasi Signature Pad
                pad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)', // Transparan
                    penColor: "rgb(30, 41, 59)", // Warna gelap
                    velocityFilterWeight: 0.7,
                    minWidth: 1.0,
                    maxWidth: 2.5,
                    throttle: 16 // Smoothness
                });

                // Resize canvas agar responsif tanpa blur
                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    pad.clear(); // Reset canvas saat resize
                }

                window.addEventListener('resize', resizeCanvas);
                // Resize awal
                setTimeout(resizeCanvas, 100);

                // Tombol Hapus
                document.getElementById('clearSigBtn').addEventListener('click', (e) => {
                    e.preventDefault();
                    pad.clear();
                });
            }

            // Fungsi Submit dengan Validasi
            function submitSignature(e) {
                e.preventDefault();

                const nameInput = document.getElementById('parent_name');

                if (!nameInput.value || nameInput.value.trim() === "") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'Silakan isi Nama Lengkap Orang Tua terlebih dahulu.',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                if (pad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Kosong',
                        text: 'Mohon bubuhkan tanda tangan pada kotak yang tersedia.',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                // Masukkan data base64 ke input hidden
                document.getElementById('input_ttd_ortu').value = pad.toDataURL('image/png');

                // Konfirmasi Akhir
                Swal.fire({
                    title: 'Konfirmasi Pengesahan',
                    text: `Saya menyatakan bahwa laporan ini telah saya terima dan setujui atas nama "${nameInput.value}".`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Sahkan!',
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        document.getElementById('signForm').submit();
                    }
                });
            }
        </script>
    @endif
</x-app-layout>
