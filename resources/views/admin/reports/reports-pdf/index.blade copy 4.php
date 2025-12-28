<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan - {{ $report->student->student_name }}</title>
    <style>
        /** SETTING HALAMAN & FONT */
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            padding: 1.5cm 1cm;
        }

        /** BORDER HALAMAN */
        .page-border {
            position: fixed;
            left: 0px;
            top: 0px;
            bottom: 0px;
            right: 0px;
            z-index: -1000;
            border: 2px double #333;
            padding: 5px;
        }

        .page-border-inner {
            border: 1px solid #333;
            height: 100%;
        }

        /** UTILITIES */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-justify {
            text-align: justify;
        }

        .bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .italic {
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        /** TABLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        td,
        th {
            vertical-align: top;
            padding: 4px;
        }

        /* Table Bordered */
        .table-bordered {
            border: 1px solid #000;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
            padding: 5px;
        }

        .table-bordered th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }

        /** HEADER KOP SURAT */
        .header-table {
            border-bottom: 3px double #000;
            margin-bottom: 20px;
            width: 100%;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .school-address {
            font-size: 9pt;
            font-style: italic;
        }

        /** COMPONENTS */
        .box-title {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 5px 10px;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 15px;
            font-size: 11pt;
        }

        .photo-box {
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .check-mark {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
        }

        .content-img {
            width: 160px;
            height: 110px;
            object-fit: cover;
            border: 1px solid #ccc;
            padding: 2px;
        }

        /* SIGNATURES */
        .signature-table td {
            text-align: center;
            vertical-align: bottom;
            height: 90px;
        }

        .sig-img {
            max-height: 70px;
            max-width: 120px;
        }
    </style>
</head>

<body>

    <div class="page-border">
        <div class="page-border-inner"></div>
    </div>

    <div class="text-center" style="margin-top: 150px;">
        <img src="{{ public_path('images/logo2.png') }}" style="width: 160px; height: auto;">
        <br><br><br>
        <div style="font-size: 20pt;" class="bold">LAPORAN</div>
        <div style="font-size: 18pt;" class="bold">HASIL BELAJAR PESERTA DIDIK</div>
        <div style="font-size: 16pt;">PENDIDIKAN ANAK USIA DINI</div>
        <br>
        <div class="italic" style="font-size: 12pt;">"Mewujudkan Generasi Sehat, Cerdas, dan Berakhlak Mulia"</div>
        <br><br><br><br>
        <div style="font-size: 12pt;">NAMA PESERTA DIDIK:</div>
        <div style="border: 2px solid #000; padding: 15px; margin: 10px 50px; font-size: 18pt; background-color: #f9f9f9;"
            class="bold uppercase">{{ $report->student->student_name }}</div>
        <div style="margin-top: 15px;">NOMOR INDUK SISWA (NIS):</div>
        <div style="font-size: 16pt;" class="bold">{{ $report->student->student_number }}</div>
        <br><br><br><br><br>
        <div style="font-size: 14pt;" class="bold">AL JANNAH PRESCHOOL AND DAY CARE</div>
        <div style="font-size: 10pt;">Jl. Giok No.17-18 Perumahan Villa Pisma Asri, Desa Podo, Kec.
            Kedungwuni<br>Kabupaten Pekalongan, Jawa Tengah</div>
    </div>

    <div class="page-break"></div>

    <div class="text-center bold" style="font-size: 16pt; margin-bottom: 30px; text-decoration: underline;">DATA DIRI
        ANAK</div>
    <table width="100%">
        <tr>
            <td width="30%" align="center" style="padding-top: 20px;">
                @if ($report->student->photo && file_exists(storage_path('app/public/' . $report->student->photo)))
                    <img src="{{ storage_path('app/public/' . $report->student->photo) }}" class="photo-box">
                @else
                    <div class="photo-box" style="line-height: 4cm; font-size: 10pt; color: #aaa; text-align: center;">
                        FOTO 3x4</div>
                @endif
            </td>
            <td width="70%">
                <table width="100%" style="font-size: 12pt;">
                    <tr>
                        <td width="5%">1.</td>
                        <td width="35%">Nama Lengkap</td>
                        <td width="5%">:</td>
                        <td class="bold">{{ $report->student->student_name }}</td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td>Nama Panggilan</td>
                        <td>:</td>
                        <td>{{ $report->student->nickname ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>Nomor Induk</td>
                        <td>:</td>
                        <td>{{ $report->student->student_number }}</td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $report->student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td>5.</td>
                        <td>Tempat, Tgl Lahir</td>
                        <td>:</td>
                        <td>{{ $report->student->birth_place ?? 'Pekalongan' }},
                            {{ \Carbon\Carbon::parse($report->student->birth_date)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>6.</td>
                        <td>Agama</td>
                        <td>:</td>
                        <td>Islam</td>
                    </tr>
                    <tr>
                        <td>7.</td>
                        <td>Nama Orang Tua</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>a. Ayah</td>
                        <td>:</td>
                        <td>{{ $report->student->father_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>b. Ibu</td>
                        <td>:</td>
                        <td>{{ $report->student->mother_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>8.</td>
                        <td>Alamat Rumah</td>
                        <td>:</td>
                        <td>{{ $report->student->address ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br><br><br><br>
    <table width="100%">
        <tr>
            <td width="55%"></td>
            <td width="45%" class="text-center">
                Pekalongan, {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}<br>Kepala
                Sekolah,<br><br>
                @if ($report->principal_signature && file_exists(storage_path('app/public/' . $report->principal_signature)))
                    <img src="{{ storage_path('app/public/' . $report->principal_signature) }}" style="height: 70px;">
                @else
                    <br><br><br>
                @endif
                <br><span class="bold"
                    style="text-decoration: underline;">{{ $report->principal_name ?? 'NURHIKMAH UMAMI, S.Pd.' }}</span>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <table class="header-table">
        <tr>
            <td width="15%" align="center"><img src="{{ public_path('images/logo2.png') }}"
                    style="height: 70px; width: auto;"></td>
            <td width="70%" align="center">
                <div style="font-size: 12pt; font-weight: bold;">YAYASAN AL JANNAH PEKALONGAN</div>
                <div class="school-name">AL JANNAH PRESCHOOL AND DAY CARE</div>
                <div class="school-address">Jl. Giok No.17 Blok B.5 Perumahan Villa Pisma Asri, Desa Podo, Kec.
                    Kedungwuni<br>Kabupaten Pekalongan - Jawa Tengah 51173<br>Email: ajpreschooldaycare@gmail.com</div>
            </td>
            <td width="15%" align="center"><img src="{{ public_path('images/barcode.png') }}"
                    style="height: 70px; width: auto;" onerror="this.style.display='none'"></td>
        </tr>
    </table>

    <div class="text-center bold" style="margin-bottom: 15px; font-size: 12pt; text-decoration: underline;">LAPORAN
        PERKEMBANGAN ANAK</div>

    <table width="100%" style="margin-bottom: 20px; font-size: 10pt; border: 1px solid #ccc; padding: 5px;">
        <tr>
            <td width="15%" class="bold">Nama Anak</td>
            <td width="45%">: {{ $report->student->student_name }}</td>
            <td width="15%" class="bold">Semester</td>
            <td width="25%">:
                {{ \Carbon\Carbon::parse($report->start_date)->month >= 7 ? 'I (Ganjil)' : 'II (Genap)' }}</td>
        </tr>
        <tr>
            <td class="bold">Nomor Induk</td>
            <td>: {{ $report->student->student_number }}</td>
            <td class="bold">Tahun Ajaran</td>
            <td>: {{ \Carbon\Carbon::parse($report->start_date)->format('Y') }} /
                {{ \Carbon\Carbon::parse($report->start_date)->addYear()->format('Y') }}</td>
        </tr>
    </table>

    <div class="box-title">A. DETAIL PERKEMBANGAN (CHECKLIST)</div>
    <table class="table-bordered" width="100%">
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle; width: 60%;">ASPEK PERKEMBANGAN</th>
                <th colspan="4">PENILAIAN</th>
            </tr>
            <tr>
                <th width="10%">BB</th>
                <th width="10%">MB</th>
                <th width="10%">BSH</th>
                <th width="10%">BSB</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groupedDetails as $themeName => $details)
                <tr style="background-color: #f0f0f0;" class="avoid-break">
                    <td colspan="5" class="bold text-left" style="padding-left: 5px;">{{ strtoupper($themeName) }}
                    </td>
                </tr>
                @php
                    $subThemes = $details->groupBy(function ($item) {
                        return $item->material->subTheme->sub_theme_name ?? 'Umum';
                    });
                @endphp
                @foreach ($subThemes as $subName => $mats)
                    <tr class="avoid-break">
                        <td colspan="5" class="italic text-left bold" style="padding-left: 15px; color: #444;">
                            {{ $subName }}</td>
                    </tr>
                    @foreach ($mats as $det)
                        <tr class="avoid-break">
                            <td style="padding-left: 30px;">- {{ $det->material->material_name }}</td>
                            <td class="check-mark">{{ $det->score == 'BB' ? '✓' : '' }}</td>
                            <td class="check-mark">{{ $det->score == 'MB' ? '✓' : '' }}</td>
                            <td class="check-mark">{{ $det->score == 'BSH' ? '✓' : '' }}</td>
                            <td class="check-mark">{{ $det->score == 'BSB' ? '✓' : '' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
    <div style="font-size: 8pt; margin-top: 5px; margin-bottom: 20px;">*Keterangan: <b>BB</b> (Belum Berkembang),
        <b>MB</b> (Mulai Berkembang), <b>BSH</b> (Berkembang Sesuai Harapan), <b>BSB</b> (Berkembang Sangat Baik)
    </div>

    @if (isset($themeNotes) && count($themeNotes) > 0)
        <div class="box-title">B. DESKRIPSI CAPAIAN PEMBELAJARAN PER TEMA</div>
        @foreach ($groupedDetails as $themeName => $details)
            @php
                $firstItem = $details->first();
                $themeId = $firstItem->material->subTheme->theme_id ?? null;
                $note = $themeNotes[$themeId] ?? null;
            @endphp
            @if ($note)
                <div class="avoid-break" style="margin-bottom: 10px; border: 1px solid #000; padding: 0;">
                    <div class="bold"
                        style="background-color: #f2f2f2; border-bottom: 1px solid #000; padding: 5px 10px;">Tema:
                        {{ $themeName }}</div>
                    <div class="text-justify" style="padding: 10px;">{{ $note }}</div>
                </div>
            @endif
        @endforeach
    @endif
    @php
        $narrations = [
            [
                'title' => '1. NILAI AGAMA & BUDI PEKERTI',
                'text' => $report->religious_values_text,
                'photo' => $report->religious_values_photo,
            ],
            ['title' => '2. JATI DIRI', 'text' => $report->identity_text, 'photo' => $report->identity_photo],
            [
                'title' => '3. DASAR LITERASI, MATEMATIKA, SAINS, TEKNOLOGI (STEAM)',
                'text' => $report->literacy_steam_text,
                'photo' => $report->literacy_steam_photo,
            ],
            [
                'title' => '4. PROJEK PENGUATAN PROFIL PELAJAR PANCASILA',
                'text' => $report->p5_text,
                'photo' => $report->p5_photo,
            ],
        ];
    @endphp
    @foreach ($narrations as $section)
        @if ($section['text'] || $section['photo'])
            <div class="avoid-break" style="margin-bottom: 10px; border: 1px solid #000; padding: 0;">
                <div class="bold"
                    style="background-color: #f2f2f2; border-bottom: 1px solid #000; padding: 5px 10px;">
                    {{ $section['title'] }}</div>
                <table width="100%" style="border: none; margin: 0;">
                    <tr>
                        <td class="text-justify" style="padding: 10px; white-space: pre-line;">
                            {{ $section['text'] ?? 'Belum ada narasi.' }}</td>
                        @if ($section['photo'] && file_exists(storage_path('app/public/' . $section['photo'])))
                            <td width="160" align="center"
                                style="vertical-align: middle; padding: 10px; border-left: 1px solid #000;">
                                <img src="{{ storage_path('app/public/' . $section['photo']) }}" class="content-img">
                                <div style="font-size: 8pt; margin-top: 3px;">Dokumentasi</div>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
        @endif
    @endforeach

    <div class="avoid-break">
        <div class="box-title">D. REFLEKSI & KESIMPULAN</div>
        <div class="bold" style="margin-top: 10px; margin-left: 5px;">Refleksi Orang Tua / Wali:</div>
        <div
            style="border: 1px solid #000; padding: 10px; min-height: 40px; margin-bottom: 10px; text-align: justify; font-style: italic;">
            {{ $report->parent_reflection_text ?? '(Mohon diisi oleh Orang Tua)' }}</div>
        <div class="bold" style="margin-left: 5px;">Kesimpulan Perkembangan Anak (Guru):</div>
        <div style="border: 1px solid #000; padding: 10px; min-height: 40px; text-align: justify;">
            {{ $report->development_info_text ?? '-' }}</div>
    </div>

    <div class="avoid-break">
        <div class="box-title">E. CATATAN DAN REKOMENDASI GURU</div>
        <div style="border: 1px solid #000; padding: 0; margin-bottom: 10px;">
            <div class="bold" style="background-color: #f2f2f2; border-bottom: 1px solid #000; padding: 5px 10px;">
                Catatan Guru</div>
            <div style="padding: 10px; text-align: justify; white-space: pre-line;">
                {{ $report->teacher_notes ?? '-' }}</div>
        </div>
        <div style="border: 1px solid #000; padding: 0;">
            <div class="bold" style="background-color: #f2f2f2; border-bottom: 1px solid #000; padding: 5px 10px;">
                Rekomendasi / Tindak Lanjut</div>
            <div style="padding: 10px; text-align: justify; white-space: pre-line;">
                {{ $report->recommendations ?? '-' }}</div>
        </div>
    </div>

    <br>
    <div class="avoid-break">
        <div class="box-title">F. DATA KESEHATAN & PRESENSI</div>
        <table class="table-bordered" width="100%">
            <tr>
                <th width="50%">PEMERIKSAAN KESEHATAN & FISIK</th>
                <th width="50%">PRESENSI (KEHADIRAN)</th>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table width="100%" style="margin: 0; border: none;">
                        <tr style="border-bottom: 1px solid #ccc;">
                            <td width="50%" style="padding: 4px;">Berat Badan</td>
                            <td class="bold">: {{ $report->weight }} kg</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ccc;">
                            <td style="padding: 4px;">Tinggi Badan</td>
                            <td class="bold">: {{ $report->height }} cm</td>
                        </tr>
                        @foreach ($report->healthDetails as $h)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 4px;">{{ $h->item_name }}</td>
                                <td class="bold">: {{ $h->item_value }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td style="padding: 0; vertical-align: top;">
                    @php $att = json_decode($report->attendance_summary, true) ?? []; @endphp
                    <table width="100%" style="margin: 0; border: none;">
                        <tr>
                            <td width="50%" style="padding: 4px;">Sakit</td>
                            <td class="bold">: {{ $att['Sakit'] ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px;">Izin</td>
                            <td class="bold">: {{ $att['Izin'] ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px;">Tanpa Keterangan</td>
                            <td class="bold">: {{ $att['Alpha'] ?? 0 }} hari</td>
                        </tr>
                        <tr style="background-color: #f0f0f0;">
                            <td style="padding: 4px;" class="bold">Hadir</td>
                            <td class="bold">: {{ $att['Hadir'] ?? 0 }} hari</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <br>

    <div class="avoid-break" style="margin-top: 20px;">
        <table width="100%" class="signature-table">
            <tr>
                <td width="50%">
                    Mengetahui,<br>Orang Tua / Wali<br>
                    @if ($report->parent_signature && file_exists(storage_path('app/public/' . $report->parent_signature)))
                        <img src="{{ storage_path('app/public/' . $report->parent_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <div class="bold" style="text-decoration: underline; margin-top: 5px;">
                        {{ $report->parent_name ?? '(....................)' }}</div>
                </td>

                <td width="50%">
                    Pekalongan, {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}<br>Wali
                    Kelas<br>
                    @if ($report->teacher_signature && file_exists(storage_path('app/public/' . $report->teacher_signature)))
                        <img src="{{ storage_path('app/public/' . $report->teacher_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <div class="bold" style="text-decoration: underline; margin-top: 5px;">
                        {{ $report->teacher_name ?? '(....................)' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="height: 20px;"></td>
            </tr>
            <tr>
                <td>
                    Mengetahui,<br>Konsultan<br>
                    @if ($report->consultant_signature && file_exists(storage_path('app/public/' . $report->consultant_signature)))
                        <img src="{{ storage_path('app/public/' . $report->consultant_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <div class="bold" style="text-decoration: underline; margin-top: 5px;">
                        {{ $report->consultant_name ?? '(....................)' }}</div>
                </td>

                <td>
                    Mengetahui,<br>Kepala Sekolah<br>
                    @if ($report->principal_signature && file_exists(storage_path('app/public/' . $report->principal_signature)))
                        <img src="{{ storage_path('app/public/' . $report->principal_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <div class="bold" style="text-decoration: underline; margin-top: 5px;">
                        {{ $report->principal_name ?? '(....................)' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
