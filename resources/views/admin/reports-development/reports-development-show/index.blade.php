<x-app-layout>
    <x-slot:title>Detail Hasil Tumbuh Kembang: {{ $report->student->student_name }}</x-slot:title>

    {{-- HEADER UTAMA (NAVIGASI) --}}

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">


        <div class="max-w-7xl sm:px-6 lg:px-8 space-y-8">
            <a href="{{ route('development-reports.history', $report->student_id) }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('development-reports.edit', $report->id) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-yellow-600 transition">
                <i class="fas fa-pencil-alt mr-2"></i> Edit Data
            </a>
            <a href="{{ route('development-reports.print', $report->id) }}" target="_blank"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-red-700 transition">
                <i class="fas fa-file-pdf mr-2"></i> Cetak Hasil (PDF)
            </a>
        </div>
    </div>


    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. IDENTITAS PESERTA DIDIK & AKADEMIK (UPDATED: SEMESTER & TA ADA DISINI) --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">
                        <i class="fas fa-id-card mr-2 text-indigo-500"></i> Identitas & Akademik
                    </h3>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold {{ $report->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ strtoupper($report->status) }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        {{-- Foto Siswa --}}
                        <div class="flex-shrink-0 mx-auto md:mx-0">
                            <div
                                class="h-40 w-32 rounded-xl overflow-hidden border-2 border-gray-200 shadow-sm relative group bg-gray-100">
                                @if ($report->student->user_photo && file_exists(storage_path('app/public/' . $report->student->user_photo)))
                                    <img class="h-full w-full object-cover"
                                        src="{{ asset('storage/' . $report->student->user_photo) }}" alt="Foto Siswa">
                                @else
                                    <div class="h-full w-full flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-user text-4xl mb-2"></i>
                                        <span class="text-[10px]">FOTO</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Grid Informasi Lengkap --}}
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-8">

                            {{-- Baris 1 --}}
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Nama Lengkap</label>
                                <p class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $report->student->student_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Nomor Induk
                                    (NIS)</label>
                                <p class="text-base font-medium text-gray-800">
                                    {{ $report->student->student_number ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Jenis
                                    Kelamin</label>
                                <p class="text-base font-medium text-gray-800">
                                    <i
                                        class="fas {{ $report->student->gender == 'L' || $report->student->gender == 1 ? 'fa-mars text-blue-500' : 'fa-venus text-pink-500' }} mr-1"></i>
                                    {{ $report->student->gender == 'L' || $report->student->gender == 1 ? 'Laki-laki' : 'Perempuan' }}
                                </p>
                            </div>

                            {{-- Baris 2 (AKADEMIK) --}}
                            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase block mb-1">Tahun
                                    Ajaran</label>
                                <p class="text-base font-bold text-indigo-700">{{ $report->academic_year }}</p>
                            </div>
                            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                <label
                                    class="text-[10px] font-bold text-indigo-400 uppercase block mb-1">Semester</label>
                                <p class="text-base font-bold text-indigo-700">{{ $report->semester }}</p>
                            </div>
                            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase block mb-1">Program /
                                    Layanan</label>
                                <p class="text-sm font-bold text-indigo-700 truncate">
                                    {{ $report->student->activityTransaction->program->program_name ?? '-' }} /
                                    {{ $report->student->activityTransaction->service->service_name ?? '-' }}
                                </p>
                            </div>

                            {{-- Baris 3 --}}
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Usia Saat
                                    Laporan</label>
                                <p class="text-base font-medium text-gray-800">
                                    {{ floor($report->age_in_months / 12) }} Tahun {{ $report->age_in_months % 12 }}
                                    Bulan
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Tanggal
                                    Laporan</label>
                                <p class="text-base font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Periode Data</label>
                                <p class="text-sm font-medium text-gray-600">
                                    {{ \Carbon\Carbon::parse($report->period_start_date)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($report->period_end_date)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. FISIK & GRAFIK (SNAPSHOT GAMBAR) --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="font-bold text-blue-900 text-sm uppercase tracking-wider">
                        <i class="fas fa-chart-line mr-2"></i> A. Pertumbuhan & Perkembangan Fisik
                    </h3>
                </div>
                <div class="p-6">
                    {{-- Data Angka --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="p-4 bg-white border border-gray-200 rounded-xl text-center shadow-sm">
                            <div class="text-xs text-gray-500 uppercase font-bold">Berat Badan</div>
                            <div class="text-2xl font-black text-blue-600 mt-1">{{ $report->weight_kg }} <span
                                    class="text-sm text-gray-400 font-medium">kg</span></div>
                        </div>
                        <div class="p-4 bg-white border border-gray-200 rounded-xl text-center shadow-sm">
                            <div class="text-xs text-gray-500 uppercase font-bold">Tinggi Badan</div>
                            <div class="text-2xl font-black text-blue-600 mt-1">{{ $report->height_cm }} <span
                                    class="text-sm text-gray-400 font-medium">cm</span></div>
                        </div>
                        <div class="p-4 bg-white border border-gray-200 rounded-xl text-center shadow-sm">
                            <div class="text-xs text-gray-500 uppercase font-bold">Lingkar Kepala</div>
                            <div class="text-2xl font-black text-blue-600 mt-1">{{ $report->head_circumference_cm }}
                                <span class="text-sm text-gray-400 font-medium">cm</span>
                            </div>
                        </div>
                        <div class="p-4 bg-white border border-gray-200 rounded-xl text-center shadow-sm">
                            <div class="text-xs text-gray-500 uppercase font-bold">IMT (BMI)</div>
                            <div class="text-2xl font-black text-pink-600 mt-1">{{ $report->bmi }}</div>
                        </div>
                    </div>

                    {{-- Analisis --}}
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8 rounded-r-lg">
                        <h4 class="font-bold text-blue-800 text-sm mb-1">Kesimpulan Pertumbuhan:</h4>
                        <p class="text-sm text-blue-900 text-justify leading-relaxed">
                            {{ $report->growth_analysis_desc ?? 'Belum ada analisis.' }}</p>
                    </div>

                    {{-- Galeri Grafik --}}
                    <h4 class="font-bold text-gray-700 text-sm uppercase mb-4 border-b pb-2 flex items-center">
                        <i class="fas fa-images mr-2 text-gray-400"></i> Dokumentasi Grafik (Snapshot)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        @php
                            $isBaby = $report->age_in_months < 24;
                            $charts = [
                                ['label' => 'Berat Badan / Umur (BB/U)', 'file' => $report->chart_bbu_image],
                                [
                                    'label' => $isBaby ? 'Panjang Badan / Umur (PB/U)' : 'Tinggi Badan / Umur (TB/U)',
                                    'file' => $report->chart_tbu_image,
                                ],
                                [
                                    'label' => $isBaby ? 'BB / Panjang Badan' : 'BB / Tinggi Badan',
                                    'file' => $report->chart_bbtb_image,
                                ],
                                ['label' => 'IMT / Umur', 'file' => $report->chart_imtu_image],
                            ];
                        @endphp

                        @foreach ($charts as $chart)
                            <div
                                class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition group">
                                <div
                                    class="px-4 py-2 bg-white border-b border-gray-200 text-xs font-bold text-gray-600 flex justify-between">
                                    <span>{{ $chart['label'] }}</span>
                                    @if ($chart['file'])
                                        <a href="{{ asset('storage/' . $chart['file']) }}" target="_blank"
                                            class="text-blue-500 hover:underline">Lihat Full</a>
                                    @endif
                                </div>
                                <div class="p-2 h-64 flex items-center justify-center bg-white relative">
                                    @if ($chart['file'] && file_exists(storage_path('app/public/' . $chart['file'])))
                                        <img src="{{ asset('storage/' . $chart['file']) }}"
                                            class="max-h-full max-w-full object-contain">
                                    @else
                                        <div class="text-center text-gray-400">
                                            <i class="far fa-image text-3xl mb-2 block"></i>
                                            <span class="text-xs italic">Grafik Tidak Tersedia</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. PERKEMBANGAN MMDST --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div
                    class="bg-purple-50 dark:bg-gray-700 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
                    <h3 class="font-bold text-purple-900 dark:text-white uppercase tracking-wider text-sm">
                        <i class="fas fa-brain mr-2"></i> B. Perkembangan (Metode MMDST)
                    </h3>
                </div>
                <div class="p-6">
                    {{-- Diagnosa Global --}}
                    <div
                        class="mb-6 inline-flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <span class="text-xs font-bold text-gray-500 uppercase">Diagnosa Akhir:</span>
                        @php
                            $finalRes = $report->mmdst_final_result ?? 'UNTESTABLE';
                            $badgeColor = match ($finalRes) {
                                'NORMAL' => 'bg-green-100 text-green-800 border-green-200',
                                'SUSPECT',
                                'QUESTIONABLE',
                                'CAUTION'
                                    => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'UNTESTABLE', 'REFUSAL' => 'bg-gray-100 text-gray-800 border-gray-200',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded border {{ $badgeColor }}">
                            {{ $finalRes }}
                        </span>
                    </div>

                    {{-- Grid Sektor --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ([['title' => 'Personal Sosial', 'res' => $report->mmdst_personal_social_result, 'desc' => $report->personal_social_desc, 'icon' => 'fa-users'], ['title' => 'Motorik Halus', 'res' => $report->mmdst_fine_motor_result, 'desc' => $report->fine_motor_desc, 'icon' => 'fa-pencil-alt'], ['title' => 'Bahasa', 'res' => $report->mmdst_language_result, 'desc' => $report->language_desc, 'icon' => 'fa-comments'], ['title' => 'Motorik Kasar', 'res' => $report->mmdst_gross_motor_result, 'desc' => $report->gross_motor_desc, 'icon' => 'fa-running']] as $item)
                            <div
                                class="border border-gray-200 rounded-xl p-5 hover:shadow-sm transition bg-white group">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                                            <i class="fas {{ $item['icon'] }} text-xs"></i>
                                        </div>
                                        <h4 class="font-bold text-gray-800 text-sm uppercase">{{ $item['title'] }}
                                        </h4>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold px-2 py-1 rounded border {{ $item['res'] == 'NORMAL' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-orange-50 text-orange-700 border-orange-100' }}">
                                        {{ $item['res'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 text-justify leading-relaxed">
                                    {{ $item['desc'] ?? 'Belum ada deskripsi.' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 4. KESEHATAN & ABSENSI (GRID 2 KOLOM) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Card Kesehatan --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
                    <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
                        <h3 class="font-bold text-green-900 text-sm uppercase"><i class="fas fa-heartbeat mr-2"></i>
                            C. Data Kesehatan</h3>
                    </div>
                    <div class="p-6 flex-grow">
                        @if ($report->healthDetail)
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach (['vision' => 'Mata', 'hearing' => 'Telinga', 'teeth' => 'Gigi', 'skin' => 'Kulit', 'nails' => 'Kuku', 'hygiene' => 'Kebersihan'] as $key => $label)
                                        <div class="flex flex-col border-b border-gray-100 pb-2">
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase">{{ $label }}</span>
                                            <span
                                                class="text-sm font-semibold text-gray-800">{{ $report->healthDetail->$key ?? '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($report->healthDetail->remarks)
                                    <div
                                        class="mt-4 p-3 bg-green-50 rounded-lg text-xs text-green-800 border border-green-100 italic">
                                        <strong>Catatan:</strong> {{ $report->healthDetail->remarks }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="h-full flex items-center justify-center text-gray-400 italic text-sm">Data
                                Kesehatan Belum Diisi</div>
                        @endif
                    </div>
                </div>

                {{-- Card Absensi --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
                    <div class="px-6 py-4 border-b border-gray-100 bg-orange-50">
                        <h3 class="font-bold text-orange-900 text-sm uppercase"><i
                                class="fas fa-calendar-check mr-2"></i> D. Kehadiran</h3>
                    </div>
                    <div class="p-6 flex-grow flex items-center">
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="bg-green-50 p-4 rounded-xl text-center border border-green-100">
                                <div class="text-3xl font-black text-green-600">{{ $report->attendance_present }}
                                </div>
                                <div class="text-[10px] font-bold text-green-800 uppercase mt-1">Hadir</div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-xl text-center border border-blue-100">
                                <div class="text-3xl font-black text-blue-600">{{ $report->attendance_sick }}</div>
                                <div class="text-[10px] font-bold text-blue-800 uppercase mt-1">Sakit</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-xl text-center border border-yellow-100">
                                <div class="text-3xl font-black text-yellow-600">{{ $report->attendance_permission }}
                                </div>
                                <div class="text-[10px] font-bold text-yellow-800 uppercase mt-1">Izin</div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-xl text-center border border-red-100">
                                <div class="text-3xl font-black text-red-600">{{ $report->attendance_alpha }}</div>
                                <div class="text-[10px] font-bold text-red-800 uppercase mt-1">Alpha</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. CATATAN & REKOMENDASI --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 dark:text-white uppercase tracking-wider text-sm">
                        <i class="fas fa-comment-dots mr-2 text-indigo-500"></i> E. Catatan & Rekomendasi
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Catatan Guru:</label>
                            <div
                                class="p-5 bg-gray-50 rounded-xl border border-gray-200 text-gray-700 text-sm leading-relaxed text-justify min-h-[120px]">
                                {{ $report->teacher_notes ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Rekomendasi / Tindak
                                Lanjut:</label>
                            <div
                                class="p-5 bg-gray-50 rounded-xl border border-gray-200 text-gray-700 text-sm leading-relaxed text-justify min-h-[120px]">
                                {{ $report->teacher_recommendations ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. TANDA TANGAN (GALERI) --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-10">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800 text-sm uppercase"><i class="fas fa-signature mr-2"></i> Lembar
                        Pengesahan</h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        @foreach ([['role' => 'Orang Tua / Wali', 'name' => $report->parent_name, 'sig' => $report->parent_signature], ['role' => 'Wali Kelas', 'name' => $report->teacher_name, 'sig' => $report->teacher_signature], ['role' => 'Konsultan', 'name' => $report->consultant_name, 'sig' => $report->consultant_signature], ['role' => 'Kepala Sekolah', 'name' => $report->principal_name, 'sig' => $report->principal_signature]] as $person)
                            <div class="flex flex-col items-center justify-end h-full">
                                <span
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">{{ $person['role'] }}</span>
                                <div class="h-20 w-full flex items-end justify-center mb-2">
                                    @if ($person['sig'] && file_exists(storage_path('app/public/' . $person['sig'])))
                                        <img src="{{ asset('storage/' . $person['sig']) }}"
                                            class="h-20 max-w-full object-contain filter grayscale hover:grayscale-0 transition">
                                    @else
                                        <span
                                            class="text-xs text-gray-300 italic mb-2 bg-gray-100 px-3 py-1 rounded">Belum
                                            TTD</span>
                                    @endif
                                </div>
                                <div class="w-24 border-b border-gray-300 mb-2"></div>
                                <span
                                    class="text-xs font-bold text-gray-800">{{ $person['name'] ?? '................' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
