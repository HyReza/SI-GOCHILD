<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Perkembangan - {{ $report->activityTransaction->student->student_name }}</title>
    <style>
        /* CSS Lengkap untuk PDF */
        @page {
            margin: 2cm;
            size: A4;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        thead th {
            background-color: #f2f2f2;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }

        .report-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .report-header h3 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
        }

        .report-header p {
            margin: 2px 0;
            font-size: 10pt;
        }

        .identity-table {
            border: none;
            margin-bottom: 1.5rem;
        }

        .identity-table td {
            border: none;
            padding: 2px 0;
            font-size: 10pt;
        }

        .identity-table .label {
            width: 100px;
            font-weight: bold;
        }

        .assessment-table .theme-row td {
            background-color: #EBF4FF;
            font-weight: bold;
            font-size: 11pt;
        }

        .assessment-table .subtheme-row td {
            background-color: #F7FAFC;
            font-weight: bold;
            padding-left: 20px;
        }

        .assessment-table .material-row td {
            padding-left: 35px;
        }

        .summary-box {
            background-color: #F7FAFC;
            border: 1px solid #E2E8F0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 1.5rem;
        }

        .summary-box h4 {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 0;
            margin-bottom: 8px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 5px;
        }

        .signature-section {
            margin-top: 3rem;
            width: 100%;
        }
    </style>
</head>

<body>
    @php
        $themes = \App\Models\Theme::with('subTheme.material')->orderBy('id')->get();
        $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
        $alphabet = range('a', 'z');
        $scoresByThemeId = $report->details->whereNotNull('theme_id')->keyBy('theme_id');
        $scoresBySubThemeId = $report->details
            ->whereNotNull('sub_theme_id')
            ->whereNull('material_id')
            ->keyBy('sub_theme_id');
        $scoresByMaterialId = $report->details->whereNotNull('material_id')->keyBy('material_id');
        $notesByThemeId = $report->themeNotes->keyBy('theme_id');
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
        $attendance = json_decode($report->attendance_summary, true) ?? ['sick' => 0, 'excused' => 0, 'absent' => 0];
    @endphp

    {{-- ====================================================== --}}
    {{--                     LEMBAR PERTAMA                       --}}
    {{-- ====================================================== --}}
    <div class="report-header">
        <h3>LAPORAN PERKEMBANGAN ANAK</h3>
        <p>AL JANNAH PRESCHOOL AND DAY CARE</p>
        <p style="margin-top: 10px; font-weight: bold;">{{ $report->report_title }}</p>
    </div>

    <table class="identity-table">
        <tr>
            <td class="label">Nama Siswa</td>
            <td>: {{ $report->activityTransaction->student->student_name }}</td>
            <td class="label">No. Induk</td>
            <td>: {{ $report->activityTransaction->student->student_number }}</td>
        </tr>
        <tr>
            <td class="label">Program</td>
            <td>: {{ $report->activityTransaction->program->program_name }}</td>
            <td class="label">Periode</td>
            <td>: {{ \Carbon\Carbon::parse($report->start_date)->isoFormat('D MMM Y') }} -
                {{ \Carbon\Carbon::parse($report->end_date)->isoFormat('D MMM Y') }}</td>
        </tr>
    </table>

    <table class="assessment-table">
        <thead>
            <tr>
                <th style="width: 60%;">ASPEK PERKEMBANGAN</th>
                <th>BB</th>
                <th>MB</th>
                <th>BSH</th>
                <th>BSB</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($themes as $theme)
                {{-- Baris Tema --}}
                <tr class="theme-row">
                    <td>{{ $romanNumerals[$loop->index] }}. {{ $theme->theme_name }}</td>
                    @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                        <td class="text-center">
                            @if ($scoresByThemeId->get($theme->id)?->score === $score)
                                <span style="font-family: DejaVu Sans, sans-serif;">✔</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                {{-- Baris Sub-Tema & Materi --}}
                @foreach ($theme->subTheme as $subTheme)
                    <tr class="subtheme-row">
                        <td>{{ $loop->iteration }}. {{ $subTheme->sub_theme_name }}</td>
                        @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                            <td class="text-center">
                                @if ($scoresBySubThemeId->get($subTheme->id)?->score === $score)
                                    <span style="font-family: DejaVu Sans, sans-serif;">✔</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @foreach ($subTheme->material as $material)
                        <tr class="material-row">
                            <td>{{ $alphabet[$loop->index] }}. {{ $material->material_name }}</td>
                            @foreach (['BB', 'MB', 'BSH', 'BSB'] as $score)
                                <td class="text-center">
                                    @if ($scoresByMaterialId->get($material->id)?->score === $score)
                                        <span style="font-family: DejaVu Sans, sans-serif;">✔</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>

    {{-- ✅ PINDAH KE SINI: CATATAN NARASI, KESIMPULAN, REKOMENDASI --}}
    <div style="margin-top: 1.5rem;">
        <h3 style="font-size: 14pt; margin-top: 0; margin-bottom: 1rem;">CATATAN NARASI PERKEMBANGAN</h3>
        @foreach ($themes as $theme)
            @php $currentThemeNote = $notesByThemeId->get($theme->id); @endphp
            @if ($currentThemeNote && !empty($currentThemeNote->note))
                <div class="summary-box" style="margin-bottom: 1rem;">
                    <p class="font-bold">{{ $romanNumerals[$loop->index] }}. {{ $theme->theme_name }}</p>
                    <p class="text-sm italic" style="margin-top: 4px;">"{{ $currentThemeNote->note }}"</p>
                </div>
            @endif
        @endforeach
    </div>

    <div class="summary-box">
        <h4>KESIMPULAN PERKEMBANGAN</h4>
        <p class="text-sm">{{ $report->overall_summary ?: 'Tidak ada kesimpulan.' }}</p>
    </div>

    <div class="summary-box">
        <h4>CATATAN DAN REKOMENDASI GURU</h4>
        <p class="text-sm">{{ $report->recommendations ?: 'Tidak ada rekomendasi.' }}</p>
    </div>

    <div class="page-break"></div>

    {{-- ====================================================== --}}
    {{--                      LEMBAR KEDUA                      --}}
    {{-- ====================================================== --}}

    <div class="summary-box" style="margin-top: 0;">
        <h4>KETERANGAN TAMBAHAN</h4>
        <table style="border: none;">
            <tr style="border: none;">
                <td style="width: 50%; vertical-align: top; border: none; padding-right: 15px;">
                    <p class="font-bold"
                        style="border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 8px;">1. Keterangan
                        Kesehatan</p>
                    @foreach ($healthItems as $item)
                        <p style="font-size: 9pt; margin: 4px 0; display: flex; justify-content: space-between;">
                            <span>{{ $item }}</span>
                            <span class="font-bold">{{ $healthDetailsMap->get($item)?->item_value ?: '-' }}</span>
                        </p>
                    @endforeach
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding-left: 15px;">
                    <p class="font-bold"
                        style="border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 8px;">2. Keterangan
                        Presensi</p>
                    <p style="font-size: 9pt; margin: 4px 0; display: flex; justify-content: space-between;">
                        <span>Sakit</span>
                        <span class="font-bold">{{ $attendance['sick'] ?? 0 }} hari</span>
                    </p>
                    <p style="font-size: 9pt; margin: 4px 0; display: flex; justify-content: space-between;">
                        <span>Izin</span>
                        <span class="font-bold">{{ $attendance['excused'] ?? 0 }} hari</span>
                    </p>
                    <p style="font-size: 9pt; margin: 4px 0; display: flex; justify-content: space-between;">
                        <span>Tanpa Keterangan</span>
                        <span class="font-bold">{{ $attendance['absent'] ?? 0 }} hari</span>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Tanda Tangan --}}
    <table class="signature-section" style="border: none; font-size: 10pt;">
        <tr style="border: none;">
            <td style="width: 50%; text-align: center; border: none;">
                <p>Mengetahui,</p>
                <p class="font-bold">Orang Tua/Wali</p>
                <div
                    style="margin-top: 70px; border-bottom: 1px solid #333; width: 200px; margin-left: auto; margin-right: auto;">
                </div>
            </td>
            <td style="width: 50%; text-align: center; border: none;">
                <p>Pekalongan, {{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMMM Y') }}</p>
                <p class="font-bold">Wali Kelas</p>
                <div
                    style="margin-top: 70px; border-bottom: 1px solid #333; width: 200px; margin-left: auto; margin-right: auto;">
                </div>
                <p style="margin-top: 5px;">{{ $report->creator->user_name ?? auth()->user()->user_name }}</p>
            </td>
        </tr>
    </table>
    <table class="signature-section" style="border: none; font-size: 10pt;">
        <tr>
            <td colspan="2" style="width: 100%; text-align: center; border: none;">
                <p>Mengetahui,</p>
            </td>
        </tr>
        <tr style="border: none;">
            <td style="width: 50%; text-align: center; border: none;">
                <p class="font-bold">Konsultan</p>
                <p class="font-bold">Tumbuh Kembang Anak</p>
                <div
                    style="margin-top: 70px; border-bottom: 1px solid #333; width: 200px; margin-left: auto; margin-right: auto;">
                </div>
            </td>
            <td style="width: 50%; text-align: center; border: none;">
                <p class="font-bold">Kepala PAUD Non Formal</p>
                <p class="font-bold">Al Jannah Preschool and Daycare</p>
                <div
                    style="margin-top: 70px; border-bottom: 1px solid #333; width: 200px; margin-left: auto; margin-right: auto;">
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
