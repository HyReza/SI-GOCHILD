<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Laporan Perkembangan') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $report->activityTransaction->student->student_name }}
                </p>
            </div>
            {{-- Tombol Aksi Utama --}}
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('reports.history', $report->activityTransaction) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500">
                    Kembali
                </a>
                <a href="{{ route('reports.edit', $report) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                    Edit
                </a>
                <a href="{{ route('reports.downloadPdf', $report) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Unduh PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Blok PHP untuk memproses dan memetakan data agar mudah diakses di view --}}
            @php
                // Mengambil kerangka tema lengkap untuk membangun struktur rapor
                $themes = \App\Models\Theme::with('subTheme.material')->orderBy('id')->get();
                $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                $alphabet = range('a', 'z');

                // Mengelompokkan data penilaian agar mudah dicari
                $scoresByThemeId = $report->details->whereNotNull('theme_id')->keyBy('theme_id');
                $scoresBySubThemeId = $report->details
                    ->whereNotNull('sub_theme_id')
                    ->whereNull('material_id')
                    ->keyBy('sub_theme_id');
                $scoresByMaterialId = $report->details->whereNotNull('material_id')->keyBy('material_id');

                // Mengelompokkan catatan narasi per tema
                $notesByThemeId = $report->themeNotes->keyBy('theme_id');
            @endphp

            {{-- Container Rapor --}}
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 md:p-8">
                    {{-- Header Rapor --}}
                    <div class="text-center mb-8">
                        <h3 class="text-xl font-bold text-gray-800">LAPORAN PERKEMBANGAN ANAK</h3>
                        <p class="text-sm text-gray-600">AL JANNAH PRESCHOOL AND DAY CARE</p>
                        <p class="text-sm font-medium text-gray-700 mt-2">{{ $report->report_title }}</p>
                    </div>

                    {{-- Identitas Siswa --}}
                    <div class="border-t border-b border-gray-200 py-4 mb-8">
                        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <span><strong class="font-medium text-gray-500 w-28 inline-block">Nama</strong>:
                                {{ $report->activityTransaction->student->student_name }}</span>
                            <span><strong class="font-medium text-gray-500 w-28 inline-block">No. Induk</strong>:
                                {{ $report->activityTransaction->student->student_number }}</span>
                            <span><strong class="font-medium text-gray-500 w-28 inline-block">Program</strong>:
                                {{ $report->activityTransaction->program->program_name }}</span>
                            <span><strong class="font-medium text-gray-500 w-28 inline-block">Periode</strong>:
                                {{ \Carbon\Carbon::parse($report->start_date)->isoFormat('D MMM Y') }} -
                                {{ \Carbon\Carbon::parse($report->end_date)->isoFormat('D MMM Y') }}</span>
                        </div>
                    </div>

                    {{-- Tabel Penilaian --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/3 lg:w-1/2">
                                        Aspek Perkembangan</th>
                                    <th
                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        BB</th>
                                    <th
                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        MB</th>
                                    <th
                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        BSH</th>
                                    <th
                                        class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        BSB</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($themes as $theme)
                                    @php $currentThemeNote = $notesByThemeId->get($theme->id); @endphp
                                    {{-- Baris Tema --}}
                                    <tr class="bg-indigo-50">
                                        <td class="px-4 py-3 font-bold text-base text-indigo-800">
                                            {{ $romanNumerals[$loop->index] }}. {{ $theme->theme_name }}</td>
                                        @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                                            <td class="px-2 py-3 text-center">
                                                @if ($scoresByThemeId->get($theme->id)?->score === $score)
                                                    <svg class="h-6 w-6 text-indigo-600 mx-auto" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    {{-- Baris Sub-Tema & Materi --}}
                                    @foreach ($theme->subTheme as $subTheme)
                                        <tr class="bg-gray-50">
                                            <td class="pl-8 pr-4 py-2.5 font-semibold text-sm text-gray-800">
                                                {{ $loop->iteration }}. {{ $subTheme->sub_theme_name }}</td>
                                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                                                <td class="px-2 py-2.5 text-center">
                                                    @if ($scoresBySubThemeId->get($subTheme->id)?->score === $score)
                                                        <svg class="h-5 w-5 text-gray-700 mx-auto" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        @foreach ($subTheme->material as $material)
                                            <tr>
                                                <td class="pl-12 pr-4 py-2.5 text-sm text-gray-700">
                                                    {{ $alphabet[$loop->index] }}. {{ $material->material_name }}</td>
                                                @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                                                    <td class="px-2 py-2.5 text-center">
                                                        @if ($scoresByMaterialId->get($material->id)?->score === $score)
                                                            <svg class="h-5 w-5 text-gray-600 mx-auto" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    {{-- Baris Catatan Narasi --}}
                                    @if ($currentThemeNote)
                                        <tr class="bg-gray-50">
                                            <td colspan="5" class="px-4 py-3">
                                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Catatan
                                                    Narasi:</p>
                                                <p class="text-sm text-gray-700 italic">"{{ $currentThemeNote->note }}"
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Bagian Kesimpulan & Rekomendasi --}}
                    <div class="mt-8 space-y-6">
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <h4 class="font-semibold text-gray-800">VII. KESIMPULAN PERKEMBANGAN</h4>
                            <p class="mt-2 text-sm text-gray-700">
                                {{ $report->overall_summary ?: 'Tidak ada kesimpulan.' }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <h4 class="font-semibold text-gray-800">VIII. CATATAN DAN REKOMENDASI GURU</h4>
                            <p class="mt-2 text-sm text-gray-700">
                                {{ $report->recommendations ?: 'Tidak ada rekomendasi.' }}</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Keterangan Tambahan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                            {{-- Kolom Keterangan Kesehatan --}}
                            <div>
                                <h4 class="font-medium text-gray-800 mb-3">1. Keterangan Kesehatan</h4>
                                <div class="space-y-2">
                                    @php
                                        // Memetakan data kesehatan agar mudah diakses
                                        $healthDetailsMap = $report->healthDetails->keyBy('item_name');
                                        $healthItems = [
                                            'Mata - Penglihatan',
                                            'Telinga - Pendengaran',
                                            'Gigi',
                                            'Kulit',
                                            'Kebersihan',
                                            'Kerapian',
                                            'Rambut',
                                            'Kuku',
                                        ];
                                    @endphp
                                    @foreach ($healthItems as $item)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ $item }}</span>
                                            <span
                                                class="font-semibold text-gray-800">{{ $healthDetailsMap->get($item)?->item_value ?: '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- Kolom Keterangan Presensi --}}
                            <div>
                                <h4 class="font-medium text-gray-800 mb-3">2. Keterangan Presensi</h4>
                                @php
                                    // Mengambil data presensi dari kolom JSON
                                    $attendance = json_decode($report->attendance_summary, true) ?? [
                                        'sick' => 0,
                                        'excused' => 0,
                                        'absent' => 0,
                                    ];
                                @endphp
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Sakit</span>
                                        <span class="font-semibold text-gray-800">{{ $attendance['sick'] ?? 0 }}
                                            hari</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Izin</span>
                                        <span class="font-semibold text-gray-800">{{ $attendance['excused'] ?? 0 }}
                                            hari</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tanpa Keterangan</span>
                                        <span class="font-semibold text-gray-800">{{ $attendance['absent'] ?? 0 }}
                                            hari</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian Tanda Tangan --}}
                    <div class="mt-12 pt-8 text-center text-sm text-gray-700">
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <p>Mengetahui,</p>
                                <p class="font-semibold">Orang Tua/Wali</p>
                                <div class="mt-16 border-b w-48 mx-auto"></div>
                            </div>
                            <div>
                                <p>Pekalongan, {{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMMM Y') }}
                                </p>
                                <p class="font-semibold">Wali Kelas</p>
                                <div class="mt-16 border-b w-48 mx-auto"></div>
                                <p class="mt-1">{{ auth()->user()->user_name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <p>Mengetahui,</p>
                    </div>
                    <div class="pt-8 text-center text-sm text-gray-700">
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <p class="font-semibold">Konsultan</p>
                                <p class="font-semibold">Tumbuh Kembang Anak</p>
                                <div class="mt-16 border-b w-48 mx-auto"></div>
                            </div>
                            <div>
                                <p class="font-semibold">Kepala PAUD Non Formal</p>
                                <p class="font-semibold">Al Jannah Preschool and Daycare</p>
                                <div class="mt-16 border-b w-48 mx-auto"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
