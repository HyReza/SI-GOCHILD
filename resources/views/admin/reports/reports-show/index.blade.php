<x-app-layout>
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
            <div class="flex space-x-3">
                <a href="{{ route('reports.history', $report->student_id) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>

                <a href="{{ route('reports.print', $report->id) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-md transition ease-in-out duration-150">
                    <i class="fas fa-print mr-2"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>



    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 relative">
                <div class="h-32 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600"></div>

                <div class="px-8 pb-8">
                    <div class="flex flex-col md:flex-row items-end -mt-12 mb-6">
                        <div class="relative z-10">
                            <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-lg overflow-hidden bg-white">
                                @if ($report->student->user_photo && Storage::disk('public')->exists($report->student->user_photo))
                                    <img src="{{ asset('storage/' . $report->student->user_photo) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ $report->student->student_name }}&background=random&size=128&bold=true"
                                        class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>

                        <div class="md:ml-6 mt-4 md:mt-0 flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $report->student->student_name }}</h1>
                            <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                <span class="bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                                    <i class="fas fa-id-card text-indigo-500 mr-2"></i>
                                    {{ $report->student->student_number ?? '-' }}
                                </span>
                                <span class="bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                                    <i class="fas fa-birthday-cake text-pink-500 mr-2"></i>
                                    {{ $report->student->birth_date ? \Carbon\Carbon::parse($report->student->birth_date)->translatedFormat('d F Y') : '-' }}
                                    ({{ $report->student->birth_date ? \Carbon\Carbon::parse($report->student->birth_date)->age . ' Thn' : '' }})
                                </span>
                                <span class="bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                                    <i class="fas fa-venus-mars text-blue-500 mr-2"></i>
                                    {{ $report->student->gender == 'L' || $report->student->gender == 1 ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 md:mt-0 text-right">
                            @php
                                $startDate = \Carbon\Carbon::parse($report->start_date);
                                $startYear = $startDate->year;
                                $endYear = $startYear + 1;
                                if ($startDate->month < 7) {
                                    $startYear = $startYear - 1;
                                    $endYear = $startYear + 1;
                                }
                                $academicYear = $startYear . ' / ' . $endYear;
                                $semester = $report->semester ?? 'Tidak Diketahui';
                            @endphp
                            <div
                                class="inline-block px-4 py-2 rounded-lg bg-indigo-50 border border-indigo-100 text-right">
                                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-1">
                                    Tahun Ajaran {{ $academicYear }}
                                </p>
                                <p class="text-lg font-bold text-indigo-800">{{ $semester }}</p>
                                <p class="text-xs text-indigo-600 mt-1 font-semibold">
                                    <i class="fas fa-door-open mr-1"></i>
                                    {{ $report->class_name ?? 'Kelas Tidak Diketahui' }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($report->start_date)->translatedFormat('d M Y') }} s.d.
                                    {{ \Carbon\Carbon::parse($report->end_date)->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <strong>Judul Laporan:</strong> {{ $report->report_title }}
                        </div>
                        <div class="text-sm text-gray-500">
                            <strong>Tanggal Raport:</strong>
                            {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
                        <span
                            class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3 text-blue-600">
                            <i class="fas fa-child"></i>
                        </span>
                        Pertumbuhan & Perkembangan Fisik
                    </h3>

                    {{-- @php
                        // Ekstrak LK dari development_info_text jika ada
                        $lk_value = null;
                        if ($report->development_info_text) {
                            preg_match(
                                '/\((Lingkar Kepala: )([\d\.]+) cm\)/',
                                $report->development_info_text,
                                $matches,
                            );
                            $lk_value = $matches[2] ?? null;
                        }
                    @endphp --}}

                    <div class="grid grid-cols-3 gap-6 mb-6">
                        <div
                            class="p-5 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-100 text-center shadow-sm">
                            <p class="text-xs text-blue-500 uppercase font-bold mb-2">Berat Badan</p>
                            <p class="text-3xl font-bold text-blue-700">{{ $report->weight + 0 }} <span
                                    class="text-sm text-gray-500 font-medium">kg</span></p>
                        </div>
                        <div
                            class="p-5 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-100 text-center shadow-sm">
                            <p class="text-xs text-green-500 uppercase font-bold mb-2">Tinggi Badan</p>
                            <p class="text-3xl font-bold text-green-700">{{ $report->height + 0 }} <span
                                    class="text-sm text-gray-500 font-medium">cm</span></p>
                        </div>
                        <div
                            class="p-5 bg-gradient-to-br from-indigo-50 to-white rounded-xl border border-indigo-100 text-center shadow-sm">
                            <p class="text-xs text-indigo-500 uppercase font-bold mb-2">Lingkar Kepala</p>
                            <p class="text-3xl font-bold text-indigo-700">{{ $report->head_circumference + 0 }}<span
                                    class="text-sm text-gray-500 font-medium">cm</span></p>
                        </div>
                    </div>

                    @if ($report->development_info_text)
                        <div class="flex-1 bg-gray-50 rounded-xl p-6 border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-comment-medical text-indigo-500 mr-2"></i>Kesimpulan & Catatan
                                Perkembangan:
                            </h4>
                            <div
                                class="prose prose-sm max-w-none text-gray-700 leading-relaxed whitespace-pre-line text-justify font-normal">
                                {{ $report->development_info_text }}
                            </div>
                        </div>
                    @endif

                    @if ($report->development_info_photo)
                        <div class="mt-6">
                            <p class="text-xs font-bold text-gray-400 uppercase mb-2">Lampiran Grafik Pertumbuhan (KMS)
                            </p>
                            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm cursor-pointer group relative"
                                onclick="window.open('{{ asset('storage/' . $report->development_info_photo) }}', '_blank')">
                                <img src="{{ asset('storage/' . $report->development_info_photo) }}"
                                    class="w-full h-64 object-cover object-top hover:scale-105 transition-transform duration-500">
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                    <span
                                        class="opacity-0 group-hover:opacity-100 bg-white text-gray-800 px-4 py-2 rounded-full text-xs font-bold shadow-lg transform translate-y-2 group-hover:translate-y-0 transition">
                                        <i class="fas fa-search-plus mr-2"></i>Lihat Gambar Penuh
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b pb-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center mr-3 text-red-600">
                                <i class="fas fa-heartbeat"></i>
                            </span>
                            Kesehatan
                        </h3>
                        <div class="space-y-4">
                            @foreach ($report->healthDetails as $health)
                                <div class="flex flex-col pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                                    <span
                                        class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-1">{{ $health->item_name }}</span>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-gray-800">{{ $health->item_value }}</span>
                                        @if ($health->item_value == 'Baik' || $health->item_value == 'Normal')
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        @elseif($health->item_value == 'Cukup')
                                            <i class="fas fa-minus-circle text-yellow-500"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-red-500"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b pb-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center mr-3 text-orange-600">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            Presensi
                        </h3>
                        @php
                            $att = json_decode($report->attendance_summary, true) ?? [];
                            $sakit = $att['Sakit'] ?? ($att['sakit'] ?? 0);
                            $izin = $att['Izin'] ?? ($att['izin'] ?? 0);
                            $alpha = $att['Alpha'] ?? ($att['alpha'] ?? 0);
                            $hadir = $att['Hadir'] ?? ($att['hadir'] ?? 0);
                        @endphp
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-blue-50 p-3 rounded-xl text-center border border-blue-100">
                                <div class="text-xl font-bold text-blue-700">{{ $hadir }}</div>
                                <div class="text-[10px] text-blue-500 uppercase font-bold">Hadir</div>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-xl text-center border border-yellow-100">
                                <div class="text-xl font-bold text-yellow-700">{{ $sakit + $izin }}</div>
                                <div class="text-[10px] text-yellow-500 uppercase font-bold">Sakit/Izin</div>
                            </div>
                            <div
                                class="col-span-2 bg-red-50 p-2 rounded-lg text-center border border-red-100 flex justify-between px-4 items-center">
                                <span class="text-xs text-red-500 font-bold uppercase">Tanpa Keterangan</span>
                                <span class="text-lg font-bold text-red-700">{{ $alpha }} Hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($groupedDetails->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200 cursor-pointer flex justify-between items-center group"
                        onclick="document.getElementById('checklistContainer').classList.toggle('hidden')">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-list-check mr-3 text-indigo-600"></i>Detail Penilaian Kurikulum
                            (Checklist)
                        </h3>
                        <div class="flex items-center text-sm text-gray-500 group-hover:text-indigo-600 transition">
                            <span class="mr-2">Tampilkan/Sembunyikan</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div id="checklistContainer" class="p-8">
                        @foreach ($groupedDetails as $themeName => $details)
                            <div class="mb-10 last:mb-0">
                                <div class="flex items-center mb-6">
                                    <div class="w-1.5 h-8 bg-indigo-600 rounded-r mr-3"></div>
                                    <h4 class="text-xl font-bold text-gray-800">{{ $themeName }}</h4>
                                </div>

                                @php
                                    $subThemes = $details->groupBy(function ($item) {
                                        return $item->material->subTheme->sub_theme_name ?? 'Materi Umum';
                                    });
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach ($subThemes as $subThemeName => $materials)
                                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 h-full">
                                            <h5
                                                class="font-bold text-indigo-700 mb-3 text-sm uppercase tracking-wide border-b border-gray-200 pb-2">
                                                {{ $subThemeName }}
                                            </h5>

                                            <ul class="space-y-3">
                                                @foreach ($materials as $det)
                                                    <li class="flex justify-between items-start text-sm">
                                                        <span
                                                            class="text-gray-700 leading-snug w-2/3">{{ $det->material->material_name }}</span>

                                                        @php
                                                            $badgeClass = match ($det->score) {
                                                                'BSB' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                                'BSH' => 'bg-green-100 text-green-800 border-green-200',
                                                                'MB'
                                                                    => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                                'BB' => 'bg-red-100 text-red-700 border-red-200',
                                                                default => 'bg-gray-100 text-gray-600',
                                                            };
                                                            $fullText = match ($det->score) {
                                                                'BSB' => 'Berkembang Sangat Baik',
                                                                'BSH' => 'Berkembang Sesuai Harapan',
                                                                'MB' => 'Mulai Berkembang',
                                                                'BB' => 'Belum Berkembang',
                                                                default => '-',
                                                            };
                                                        @endphp

                                                        <span
                                                            class="flex-shrink-0 px-2.5 py-0.5 rounded text-[10px] font-bold border {{ $badgeClass }}"
                                                            title="{{ $fullText }}">
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

                        <div class="mt-8 pt-6 border-t border-gray-200 bg-blue-50/50 p-4 rounded-xl">
                            <p class="text-xs font-bold text-gray-500 uppercase mb-3">Keterangan Penilaian:</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                                <div class="flex items-center"><span
                                        class="w-3 h-3 bg-red-500 rounded-full mr-2"></span> <span
                                        class="font-bold text-gray-700">BB:</span> Belum Berkembang</div>
                                <div class="flex items-center"><span
                                        class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span> <span
                                        class="font-bold text-gray-700">MB:</span> Mulai Berkembang</div>
                                <div class="flex items-center"><span
                                        class="w-3 h-3 bg-green-500 rounded-full mr-2"></span> <span
                                        class="font-bold text-gray-700">BSH:</span> Berkembang Sesuai Harapan</div>
                                <div class="flex items-center"><span
                                        class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span> <span
                                        class="font-bold text-gray-700">BSB:</span> Berkembang Sangat Baik</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($themeNotes) && count($themeNotes) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-pencil-alt mr-3 text-indigo-600"></i>Deskripsi Capaian Pembelajaran per
                            Tema
                        </h3>
                    </div>

                    <div class="p-8 space-y-6">
                        @foreach ($groupedDetails as $themeName => $details)
                            @php
                                $firstItem = $details->first();
                                $themeId = $firstItem->material->subTheme->theme_id ?? null;
                                $note = $themeNotes[$themeId] ?? null;
                            @endphp

                            @if ($note)
                                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                                    <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                                        {{ $themeName }}
                                    </h4>
                                    <div
                                        class="prose prose-sm max-w-none text-gray-700 leading-relaxed text-justify whitespace-pre-line">
                                        {{ $note }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="px-8 py-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-book-reader mr-3 text-indigo-600"></i>Capaian Perkembangan (Elemen CP)
                    </h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @php
                        $narrations = [
                            [
                                'title' => 'Nilai Agama & Budi Pekerti',
                                'icon' => 'fas fa-praying-hands',
                                'color' => 'text-yellow-500',
                                'bg' => 'bg-yellow-50',
                                'text' => $report->religious_values_text,
                                'photo' => $report->religious_values_photo,
                            ],
                            [
                                'title' => 'Jati Diri',
                                'icon' => 'fas fa-user',
                                'color' => 'text-blue-500',
                                'bg' => 'bg-blue-50',
                                'text' => $report->identity_text,
                                'photo' => $report->identity_photo,
                            ],
                            [
                                'title' => 'Dasar Literasi & STEAM',
                                'icon' => 'fas fa-flask',
                                'color' => 'text-green-500',
                                'bg' => 'bg-green-50',
                                'text' => $report->literacy_steam_text,
                                'photo' => $report->literacy_steam_photo,
                            ],
                            [
                                'title' => 'Projek Penguatan Profil Pancasila',
                                'icon' => 'fas fa-flag',
                                'color' => 'text-red-500',
                                'bg' => 'bg-red-50',
                                'text' => $report->p5_text,
                                'photo' => $report->p5_photo,
                            ],
                            [
                                'title' => 'Refleksi Orang Tua',
                                'icon' => 'fas fa-comments',
                                'color' => 'text-indigo-500',
                                'bg' => 'bg-indigo-50',
                                'text' => $report->parent_reflection_text,
                                'photo' => $report->parent_reflection_photo,
                                'is_special' => true,
                            ],
                        ];
                    @endphp

                    @foreach ($narrations as $section)
                        @if ($section['text'] || $section['photo'])
                            <div class="p-8 {{ isset($section['is_special']) ? 'bg-indigo-50/30' : '' }}">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="w-10 h-10 rounded-full {{ $section['bg'] }} flex items-center justify-center mr-3 shadow-sm">
                                        <i class="{{ $section['icon'] }} {{ $section['color'] }}"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-800">{{ $section['title'] }}</h4>
                                </div>

                                <div class="flex flex-col lg:flex-row gap-8">
                                    <div class="flex-1">
                                        <div
                                            class="prose prose-sm max-w-none text-gray-700 leading-relaxed text-justify whitespace-pre-line font-normal">
                                            {{ $section['text'] ?? 'Tidak ada narasi.' }}
                                        </div>
                                    </div>
                                    @if ($section['photo'])
                                        <div class="flex-shrink-0 lg:w-72">
                                            <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100">
                                                <img src="{{ asset('storage/' . $section['photo']) }}"
                                                    class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-95 transition"
                                                    onclick="window.open(this.src, '_blank')">
                                                <div class="text-center mt-2">
                                                    <span
                                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Dokumentasi</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            @if ($report->teacher_notes || $report->recommendations)
                <div class="bg-indigo-50 rounded-2xl border border-indigo-100 overflow-hidden mb-8 p-8">
                    <h3
                        class="text-lg font-bold text-indigo-900 mb-6 flex items-center border-b border-indigo-200 pb-3">
                        <i class="fas fa-chalkboard-teacher mr-3"></i>Catatan & Rekomendasi Guru
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-white p-5 rounded-xl border border-indigo-100 shadow-sm">
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Catatan Guru</h4>
                            <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                                {{ $report->teacher_notes ?? 'Tidak ada catatan khusus.' }}
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-xl border border-indigo-100 shadow-sm">
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Rekomendasi /
                                Tindak Lanjut</h4>
                            <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                                {{ $report->recommendations ?? 'Tidak ada rekomendasi khusus.' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-12">
                <div class="px-8 py-6 bg-gray-900 text-white text-center">
                    <h3 class="text-xl font-bold uppercase tracking-widest">Lembar Pengesahan</h3>
                    <p class="text-sm text-gray-400 mt-1">Dokumen ini telah ditandatangani secara digital</p>
                </div>
                <div class="p-10 bg-white">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-10 text-center">
                        @foreach ([['role' => 'Orang Tua / Wali', 'name' => $report->parent_name, 'sig' => $report->parent_signature], ['role' => 'Wali Kelas', 'name' => $report->teacher_name, 'sig' => $report->teacher_signature], ['role' => 'Konsultan', 'name' => $report->consultant_name, 'sig' => $report->consultant_signature], ['role' => 'Kepala Sekolah', 'name' => $report->principal_name, 'sig' => $report->principal_signature]] as $person)
                            <div class="flex flex-col items-center">
                                <div
                                    class="h-32 w-full flex items-center justify-center mb-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                                    @if ($person['sig'] && Storage::disk('public')->exists($person['sig']))
                                        <img src="{{ asset('storage/' . $person['sig']) }}"
                                            class="max-h-28 object-contain">
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum Tanda Tangan</span>
                                    @endif
                                </div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                                    {{ $person['role'] }}</p>
                                <p
                                    class="text-sm font-bold text-gray-900 border-b-2 border-gray-200 pb-1 min-w-[120px]">
                                    {{ $person['name'] ?? '(....................)' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pb-12 text-center">
                <a href="{{ route('reports.history', $report->student_id) }}"
                    class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-full font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-lg hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
