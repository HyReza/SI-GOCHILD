<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Perkembangan - {{ $report->student->student_name }}</title>
    <style>
        /** ATURAN HALAMAN & FONT **/
        @page {
            margin: 2cm 2cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        /** HELPER CLASSES **/
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        /** PAGE BREAK **/
        .page-break {
            page-break-after: always;
        }

        /** TABLE STYLES (Agar Rapi) **/
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            vertical-align: top;
            padding: 4px;
        }

        /* Tabel Data (Borders) */
        .table-data th,
        .table-data td {
            border: 1px solid #000;
            padding: 6px;
        }

        .table-data th {
            background-color: #f0f0f0;
            text-align: center;
        }

        /** HEADER / KOP SURAT **/
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-kop h1 {
            margin: 0;
            font-size: 20px;
            color: #4a148c;
            /* Ungu Al-Jannah */
        }

        .header-kop h2 {
            margin: 2px 0;
            font-size: 14px;
        }

        .header-kop p {
            margin: 0;
            font-size: 10px;
            font-style: italic;
        }

        /** COVER PAGE STYLES **/
        .cover-wrapper {
            text-align: center;
            padding-top: 50px;
        }

        .cover-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 50px;
        }

        .cover-logo {
            width: 150px;
            height: auto;
            margin: 40px auto;
        }

        .cover-student {
            margin-top: 60px;
            font-size: 16px;
        }

        .cover-box {
            border: 2px solid #333;
            padding: 20px;
            display: inline-block;
            margin-top: 10px;
            width: 80%;
        }

        /** SECTION TITLES **/
        .section-header {
            background-color: #e0e7ff;
            color: #333;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 13px;
            border-left: 5px solid #4f46e5;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        /** SIGNATURES **/
        .signature-table td {
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }

        .sig-img {
            max-height: 70px;
            max-width: 100px;
        }
    </style>
</head>

<body>

    {{-- ========================================== --}}
    {{-- HALAMAN 1: COVER --}}
    {{-- ========================================== --}}
    <div class="cover-wrapper">
        <h1 class="cover-title">LAPORAN<br>HASIL BELAJAR PESERTA DIDIK<br>PENDIDIKAN ANAK USIA DINI</h1>

        {{-- Logo Sekolah (Gunakan path absolut atau public_path) --}}
        {{-- Pastikan ada file logo.png di public/images/ atau ganti src nya --}}
        {{-- <img src="{{ public_path('images/logo.png') }}" class="cover-logo" alt="Logo"> --}}
        <div style="height: 150px; display: flex; align-items: center; justify-content: center;">
            <h3>[ LOGO SEKOLAH ]</h3>
        </div>

        <div class="cover-student">
            <p>NAMA PESERTA DIDIK:</p>
            <div class="cover-box text-bold uppercase">{{ $report->student->student_name }}</div>

            <br><br>

            <p>NOMOR INDUK SISWA (NIS):</p>
            <div class="cover-box text-bold">{{ $report->student->student_number ?? '-' }}</div>
        </div>

        <div style="margin-top: 100px;">
            <h3 class="uppercase">Al Jannah Preschool and Day Care</h3>
            <p>Jl. Giok No.17-18 Perumahan Villa Pisma Asri<br>Kabupaten Pekalongan, Jawa Tengah</p>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ========================================== --}}
    {{-- HALAMAN 2: DATA DIRI --}}
    {{-- ========================================== --}}

    {{-- KOP SURAT --}}
    <div class="header-kop">
        <h1>AL JANNAH PRESCHOOL AND DAY CARE</h1>
        <h2>YAYASAN AL JANNAH PEKALONGAN</h2>
        <p>Jl. Giok No.17-18 Perumahan Villa Pisma Asri, Desa Podo, Kec. Kedungwuni</p>
        <p>Kabupaten Pekalongan - Jawa Tengah 51173 | Email: info@aljannah.sch.id</p>
    </div>

    <h2 class="text-center" style="text-decoration: underline; margin-bottom: 20px;">DATA DIRI ANAK</h2>

    <table class="mb-20" style="width: 100%; font-size: 13px;">
        <tr>
            <td width="5%">1.</td>
            <td width="30%">Nama Lengkap</td>
            <td width="2%">:</td>
            <td class="text-bold">{{ $report->student->student_name }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Nomor Induk</td>
            <td>:</td>
            <td>{{ $report->student->student_number ?? '-' }}</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $report->student->gender == 1 || $report->student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
            </td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Tempat, Tgl Lahir</td>
            <td>:</td>
            <td>
                {{ $report->student->birth_place ?? 'Pekalongan' }},
                {{ \Carbon\Carbon::parse($report->student->birth_date)->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Usia saat ini</td>
            <td>:</td>
            <td>
                {{ floor($report->age_in_months / 12) }} Tahun {{ $report->age_in_months % 12 }} Bulan
            </td>
        </tr>
        <tr>
            <td>6.</td>
            <td>Program Layanan</td>
            <td>:</td>
            <td>{{ $report->student->activityTransaction->program->program_name ?? '-' }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- ========================================== --}}
    {{-- HALAMAN 3: ISI LAPORAN --}}
    {{-- ========================================== --}}

    {{-- Header Mini di tiap halaman baru --}}
    <div style="border-bottom: 2px solid #ccc; margin-bottom: 15px; padding-bottom: 5px;">
        <table style="width: 100%;">
            <tr>
                <td width="15%"><strong>Nama</strong></td>
                <td width="40%">: {{ $report->student->student_name }}</td>
                <td width="15%"><strong>Semester</strong></td>
                <td>: {{ $report->semester }} {{ $report->academic_year }}</td>
            </tr>
        </table>
    </div>

    {{-- A. PERTUMBUHAN --}}
    <div class="section-header">A. PERTUMBUHAN & PERKEMBANGAN FISIK</div>

    <table class="table-data mb-10" style="width: 100%;">
        <thead>
            <tr>
                <th>Berat Badan (Kg)</th>
                <th>Tinggi Badan (cm)</th>
                <th>Lingkar Kepala (cm)</th>
                <th>BMI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $report->weight_kg }}</td>
                <td class="text-center">{{ $report->height_cm }}</td>
                <td class="text-center">{{ $report->head_circumference_cm }}</td>
                <td class="text-center">{{ $report->bmi }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Keterangan Pertumbuhan --}}
    <div style="border: 1px dashed #999; padding: 10px; margin-bottom: 15px; background: #fff;">
        <strong>Analisis Pertumbuhan:</strong><br>
        <p style="margin-top: 5px; text-align: justify;">
            {{ $report->growth_analysis_desc ?? 'Tidak ada catatan analisis pertumbuhan.' }}
        </p>
    </div>

    {{-- Tabel Riwayat Grafik (Pengganti Chart JS) --}}
    {{-- Karena PDF tidak bisa render ChartJS, kita tampilkan datanya dalam tabel --}}
    @php
        $weightHistory = json_decode($report->weight_chart_snapshot, true) ?? [];
        $heightHistory = json_decode($report->height_chart_snapshot, true) ?? [];
        // Ambil 5 data terakhir
        $recentW = array_slice($weightHistory, -6);
    @endphp

    @if (!empty($recentW))
        <div style="margin-bottom: 10px;">
            <strong>Riwayat Data Pertumbuhan (Grafik):</strong>
            <table class="table-data" style="width: 100%; margin-top: 5px; font-size: 10px;">
                <thead>
                    <tr>
                        @foreach ($recentW as $w)
                            <th>{{ number_format($w['x'], 1) }} Bln</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($recentW as $w)
                            <td class="text-center">{{ $w['y'] }} kg</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif


    {{-- B. PERKEMBANGAN (MMDST) --}}
    <div class="section-header">B. KETERANGAN PERKEMBANGAN (MMDST)</div>

    <div class="mb-10">
        <strong>Kesimpulan Diagnosa: </strong>
        <span style="font-weight:bold; color: {{ $report->mmdst_final_result == 'NORMAL' ? '#16a34a' : '#ea580c' }}">
            {{ $report->mmdst_final_result ?? 'UNTESTABLE' }}
        </span>
    </div>

    <table class="table-data" style="width: 100%;">
        <thead>
            <tr>
                <th width="25%">Aspek Perkembangan</th>
                <th width="15%">Hasil</th>
                <th width="60%">Deskripsi Capaian</th>
            </tr>
        </thead>
        <tbody>
            {{-- Personal Sosial --}}
            <tr>
                <td><strong>Personal Sosial</strong></td>
                <td class="text-center">{{ $report->mmdst_personal_social_result }}</td>
                <td style="text-align: justify;">{{ $report->personal_social_desc ?? '-' }}</td>
            </tr>
            {{-- Motorik Halus --}}
            <tr>
                <td><strong>Motorik Halus</strong></td>
                <td class="text-center">{{ $report->mmdst_fine_motor_result }}</td>
                <td style="text-align: justify;">{{ $report->fine_motor_desc ?? '-' }}</td>
            </tr>
            {{-- Bahasa --}}
            <tr>
                <td><strong>Bahasa</strong></td>
                <td class="text-center">{{ $report->mmdst_language_result }}</td>
                <td style="text-align: justify;">{{ $report->language_desc ?? '-' }}</td>
            </tr>
            {{-- Motorik Kasar --}}
            <tr>
                <td><strong>Motorik Kasar</strong></td>
                <td class="text-center">{{ $report->mmdst_gross_motor_result }}</td>
                <td style="text-align: justify;">{{ $report->gross_motor_desc ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div> {{-- Pindah Halaman --}}

    {{-- C. KESEHATAN & ABSENSI --}}
    <div style="width: 100%;">
        {{-- Kiri: Kesehatan --}}
        <div style="width: 48%; float: left; margin-right: 2%;">
            <div class="section-header">C. PEMERIKSAAN KESEHATAN</div>
            @if ($report->healthDetail)
                <table class="table-data">
                    <tr>
                        <td width="40%">Mata</td>
                        <td>{{ $report->healthDetail->vision ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Telinga</td>
                        <td>{{ $report->healthDetail->hearing ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Gigi</td>
                        <td>{{ $report->healthDetail->teeth ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kulit</td>
                        <td>{{ $report->healthDetail->skin ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kuku</td>
                        <td>{{ $report->healthDetail->nails ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kebersihan</td>
                        <td>{{ $report->healthDetail->hygiene ?? '-' }}</td>
                    </tr>
                </table>
            @else
                <p class="text-center">- Data Belum Diisi -</p>
            @endif
        </div>

        {{-- Kanan: Absensi --}}
        <div style="width: 48%; float: right;">
            <div class="section-header">D. PRESENSI (KEHADIRAN)</div>
            <table class="table-data">
                <tr>
                    <td width="60%">Sakit</td>
                    <td class="text-center">{{ $report->attendance_sick }} Hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td class="text-center">{{ $report->attendance_permission }} Hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td class="text-center">{{ $report->attendance_alpha }} Hari</td>
                </tr>
                <tr style="background-color: #f0fdf4;">
                    <td><strong>Hadir</strong></td>
                    <td class="text-center"><strong>{{ $report->attendance_present }} Hari</strong></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    {{-- E. CATATAN GURU --}}
    <div class="section-header">E. CATATAN DAN REKOMENDASI</div>

    <div style="border: 1px solid #333; padding: 10px; margin-bottom: 10px;">
        <strong>Catatan Guru:</strong><br>
        <p style="margin-top: 5px; text-align: justify; min-height: 40px;">
            {{ $report->teacher_notes ?? '-' }}
        </p>
    </div>

    <div style="border: 1px solid #333; padding: 10px; margin-bottom: 20px;">
        <strong>Rekomendasi / Tindak Lanjut:</strong><br>
        <p style="margin-top: 5px; text-align: justify; min-height: 40px;">
            {{ $report->teacher_recommendations ?? '-' }}
        </p>
    </div>

    {{-- F. TANDA TANGAN --}}
    <div style="margin-top: 30px;">
        <table class="signature-table" style="width: 100%; border: none;">
            <tr>
                <td width="33%">
                    Mengetahui,<br>Orang Tua / Wali
                    <br>
                    @if ($report->parent_signature && file_exists(public_path('storage/' . $report->parent_signature)))
                        <img src="{{ public_path('storage/' . $report->parent_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <br>
                    <strong>( {{ $report->parent_name ?? '....................' }} )</strong>
                </td>
                <td width="33%">
                    <br>Konsultan / Psikolog
                    <br>
                    @if ($report->consultant_signature && file_exists(public_path('storage/' . $report->consultant_signature)))
                        <img src="{{ public_path('storage/' . $report->consultant_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <br>
                    <strong>( {{ $report->consultant_name ?? '....................' }} )</strong>
                </td>
                <td width="33%">
                    Pekalongan, {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}<br>
                    Wali Kelas
                    <br>
                    @if ($report->teacher_signature && file_exists(public_path('storage/' . $report->teacher_signature)))
                        <img src="{{ public_path('storage/' . $report->teacher_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <br>
                    <strong>( {{ $report->teacher_name ?? '....................' }} )</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 20px;">
                    Mengetahui,<br>Kepala Sekolah
                    <br>
                    @if ($report->principal_signature && file_exists(public_path('storage/' . $report->principal_signature)))
                        <img src="{{ public_path('storage/' . $report->principal_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <br>
                    <strong>( {{ $report->principal_name ?? '....................' }} )</strong>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
