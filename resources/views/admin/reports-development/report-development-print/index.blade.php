<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan - {{ $report->student->student_name }}</title>
    <style>
        /** GLOBAL SETTINGS */
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }

        /** BORDER HALAMAN (Bingkai Ganda) */
        .page-border {
            position: fixed;
            left: 0px;
            top: 0px;
            bottom: 0px;
            right: 0px;
            z-index: -1000;
            border: 1px solid #000;
            margin: -25px;
        }

        .page-border-inner {
            position: fixed;
            left: 0px;
            top: 0px;
            bottom: 0px;
            right: 0px;
            z-index: -1000;
            border: 2px double #000;
            margin: -22px;
        }

        /** UTILITIES */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
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

        .mb-5 {
            margin-bottom: 5px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-10 {
            margin-top: 10px;
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
            padding: 3px 5px;
        }

        /* Table Bordered */
        .table-bordered {
            border: 1px solid #000;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
            padding: 6px;
        }

        .table-bordered th {
            background-color: #e0e0e0;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }

        /** KOP SURAT (3 KOLOM) */
        .header-table {
            border-bottom: 3px double #000;
            margin-bottom: 20px;
            width: 100%;
            padding-bottom: 10px;
        }

        .school-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
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
            border-left: 5px solid #333;
        }

        /* FOTO */
        .photo-container {
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0 auto;
        }

        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            line-height: 4cm;
            font-size: 9pt;
            color: #888;
            text-align: center;
            display: block;
        }

        /* INFO BOX (TABEL ATAS) */
        .info-box {
            border: 1px solid #ccc;
            padding: 5px;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        /* SIGNATURES */
        .signature-table td {
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }

        .sig-img {
            max-height: 60px;
            max-width: 120px;
            display: block;
            margin: 0 auto;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            width: 80%;
            margin: 5px auto 0 auto;
        }
    </style>
</head>

<body>

    {{-- BINGKAI HALAMAN --}}
    <div class="page-border"></div>
    <div class="page-border-inner"></div>

    {{-- LOGIKA DATA (PHP) --}}
    @php
        // 1. Logika Tahun Ajaran
        $startDate = \Carbon\Carbon::parse($report->period_start_date); // Ambil dari DB
        $startYear = $startDate->year;
        $endYear = $startYear + 1;
        // Jika mulai report setelah Juni, berarti TA berjalan (misal Juli 2024 -> TA 2024/2025)
        // Jika sebelum Juni, mundur 1 tahun (misal Jan 2025 -> TA 2024/2025)
        if ($startDate->month < 7) {
            $startYear = $startYear - 1;
            $endYear = $startYear + 1;
        }
        $academicYear = $startYear . ' / ' . $endYear;

        // 2. Logika Alamat Lengkap (Agar tidak strip)
        $s = $report->student;
        $addrParts = [];
        if (!empty($s->address)) {
            $addrParts[] = $s->address;
        } // Alamat jalan
        if (!empty($s->village)) {
            $addrParts[] = 'Ds. ' . $s->village;
        }
        if (!empty($s->subdistrict)) {
            $addrParts[] = 'Kec. ' . $s->subdistrict;
        }
        if (!empty($s->district)) {
            $addrParts[] = 'Kab. ' . $s->district;
        }

        $fullAddress = empty($addrParts) ? '-' : implode(', ', $addrParts);

        // 3. Logika Foto
        $photoPath = null;
        if ($s->user_photo) {
            if (file_exists(storage_path('app/public/' . $s->user_photo))) {
                $photoPath = storage_path('app/public/' . $s->user_photo);
            } elseif (file_exists(public_path('storage/' . $s->user_photo))) {
                $photoPath = public_path('storage/' . $s->user_photo);
            }
        }
    @endphp

    {{-- ========================================== --}}
    {{-- HALAMAN 1: COVER --}}
    {{-- ========================================== --}}
    <div class="text-center" style="margin-top: 120px;">
        {{-- Logo Cover --}}
        <img src="{{ public_path('images/logo2.png') }}" style="width: 140px; height: auto;"
            onerror="this.style.display='none'">

        <br><br><br>
        <div style="font-size: 22pt;" class="bold">LAPORAN</div>
        <div style="font-size: 18pt;" class="bold">PENCAPAIAN PERKEMBANGAN ANAK</div>
        <div style="font-size: 16pt;">(RAPORT PAUD)</div>
        <br>
        <div class="italic" style="font-size: 12pt;">"Mewujudkan Generasi Sehat, Cerdas, dan Berakhlak Mulia"</div>

        <br><br><br>

        <div style="font-size: 12pt;">NAMA PESERTA DIDIK:</div>
        <div style="border: 2px solid #000; padding: 15px; margin: 10px 40px; font-size: 18pt; background-color: #f9f9f9;"
            class="bold uppercase">
            {{ $s->student_name }}
        </div>

        <div style="margin-top: 15px;">NOMOR INDUK SISWA (NIS):</div>
        <div style="font-size: 16pt;" class="bold">{{ $s->student_number ?? '-' }}</div>

        <br><br><br><br>

        <div style="font-size: 14pt;" class="bold">AL JANNAH PRESCHOOL AND DAY CARE</div>
        <div style="font-size: 10pt;">
            Jl. Giok No.17-18 Perumahan Villa Pisma Asri, Desa Podo, Kec. Kedungwuni<br>
            Kabupaten Pekalongan, Jawa Tengah
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ========================================== --}}
    {{-- HALAMAN 2: DATA DIRI --}}
    {{-- ========================================== --}}

    {{-- JUDUL HALAMAN (Tanpa Kop) --}}
    <div class="text-center bold"
        style="font-size: 16pt; margin-top: 50px; margin-bottom: 40px; text-decoration: underline;">
        DATA DIRI PESERTA DIDIK
    </div>

    <table width="100%">
        <tr>
            {{-- FOTO --}}
            <td width="30%" align="center" style="vertical-align: top; padding-top: 10px;">
                <div class="photo-container">
                    @if ($photoPath)
                        <img src="{{ $photoPath }}" class="photo-img">
                    @else
                        <span class="photo-placeholder">FOTO 3x4</span>
                    @endif
                </div>
            </td>
            {{-- DATA --}}
            <td width="70%">
                <table width="100%" style="font-size: 12pt; line-height: 1.6;">
                    <tr>
                        <td width="5%">1.</td>
                        <td width="35%">Nama Lengkap</td>
                        <td width="3%">:</td>
                        <td class="bold uppercase">{{ $s->student_name }}</td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td>Nomor Induk</td>
                        <td>:</td>
                        <td>{{ $s->student_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>Kelompok / Layanan</td>
                        <td>:</td>
                        <td class="bold">
                            {{-- Ambil dari relasi --}}
                            {{ $s->activityTransaction->program->program_name ?? 'Daycare / PAUD' }}
                            -
                            {{ $s->activityTransaction->service->service_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $s->gender == 1 || $s->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td>5.</td>
                        <td>Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>
                            {{ $s->birth_place ?? 'Pekalongan' }},
                            {{ \Carbon\Carbon::parse($s->birth_date)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td>6.</td>
                        <td>Usia saat ini</td>
                        <td>:</td>
                        <td>
                            {{ floor($report->age_in_months / 12) }} Tahun {{ $report->age_in_months % 12 }} Bulan
                        </td>
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
                        <td>{{ $s->father_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>b. Ibu</td>
                        <td>:</td>
                        <td>{{ $s->mother_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>8.</td>
                        <td>Alamat Rumah</td>
                        <td>:</td>
                        <td style="text-align: justify;">{{ $fullAddress }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TTD KEPALA SEKOLAH DI BAWAH DATA DIRI --}}
    <br><br><br>
    <table width="100%">
        <tr>
            <td width="55%"></td> {{-- Spacer Kiri --}}
            <td width="45%" class="text-center">
                Pekalongan, {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}<br>
                Kepala PAUD Non Formal<br>Al Jannah Preschool and Day Care
                <br><br><br>
                @if ($report->principal_signature && file_exists(storage_path('app/public/' . $report->principal_signature)))
                    <img src="{{ storage_path('app/public/' . $report->principal_signature) }}" class="sig-img">
                @endif
                <div class="sig-line" style="width: 70%"></div>
                <div class="bold">{{ $report->principal_name ?? '(....................)' }}</div>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- ========================================== --}}
    {{-- HALAMAN 3 dst: ISI LAPORAN --}}
    {{-- ========================================== --}}

    {{-- KOP SURAT 3 KOLOM --}}
    <table class="header-table">
        <tr>
            <td width="15%" align="center" style="vertical-align: middle;">
                {{-- Logo Kiri (Logo Sekolah/Yayasan) --}}
                <img src="{{ public_path('images/logo2.png') }}" style="height: 75px; width: auto;"
                    onerror="this.style.display='none'">
            </td>
            <td width="70%" align="center" style="vertical-align: middle;">
                <div style="font-size: 12pt; font-weight: bold; letter-spacing: 1px;">YAYASAN AL JANNAH PEKALONGAN</div>
                <div class="school-name">PAUD TERPADU DAYCARE AL-JANNAH</div>
                <div class="school-address">
                    Jl. Giok No.17-18 Perumahan Villa Pisma Asri, Desa Podo, Kec. Kedungwuni<br>
                    Kabupaten Pekalongan - Jawa Tengah 51173 | Email: info@aljannah.sch.id
                </div>
            </td>
            <td width="15%" align="center" style="vertical-align: middle;">
                {{-- Logo Kanan (Misal Logo Dinas/Tut Wuri, jika belum ada pakai placeholder transparan atau logo2 juga) --}}
                <img src="{{ public_path('images/barcode.png') }}" style="height: 70px; width: auto;"
                    onerror="this.style.display='none'">
                {{-- Jika tidak ada file logo kanan, kolom ini akan kosong tapi tetap menjaga layout --}}
            </td>
        </tr>
    </table>

    {{-- TABEL INFO LAPORAN (SESUAI REQUEST) --}}
    <table class="info-box" width="100%">
        <tr>
            <td width="15%" class="bold">Nama Anak</td>
            <td width="45%">: {{ $report->student->student_name }}</td>
            <td width="15%" class="bold">Semester</td>
            <td width="25%">: {{ $report->semester ?? 'Ganjil/Genap' }}</td>
        </tr>
        <tr>
            <td class="bold">Nomor Induk</td>
            <td>: {{ $report->student->student_number }}</td>
            <td class="bold">Tahun Ajaran</td>
            <td class="bold">: {{ $academicYear }}</td>
        </tr>
    </table>

    {{-- A. PERTUMBUHAN FISIK --}}
    <div class="box-title">A. PERTUMBUHAN & PERKEMBANGAN FISIK</div>

    <table class="table-bordered mb-20" width="100%">
        <thead>
            <tr>
                <th width="25%">Berat Badan</th>
                <th width="25%">Tinggi Badan</th>
                <th width="25%">Lingkar Kepala</th>
                <th width="25%">BMI (IMT)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $report->weight_kg }} kg</td>
                <td class="text-center">{{ $report->height_cm }} cm</td>
                <td class="text-center">{{ $report->head_circumference_cm }} cm</td>
                <td class="text-center bold">{{ $report->bmi }}</td>
            </tr>
        </tbody>
    </table>

    {{-- GRAFIK (Looping 1 per halaman agar besar) --}}
    @php
        $isBaby = $report->age_in_months < 24;
        $label_TBU = $isBaby ? 'Panjang Badan menurut Umur (PB/U)' : 'Tinggi Badan menurut Umur (TB/U)';
        $label_BBTB = $isBaby ? 'Berat Badan menurut Panjang Badan' : 'Berat Badan menurut Tinggi Badan';

        $charts = [
            ['title' => 'GRAFIK 1: Berat Badan menurut Umur (BB/U)', 'file' => $report->chart_bbu_image],
            ['title' => 'GRAFIK 2: ' . $label_TBU, 'file' => $report->chart_tbu_image],
            ['title' => 'GRAFIK 3: ' . $label_BBTB, 'file' => $report->chart_bbtb_image],
            ['title' => 'GRAFIK 4: Indeks Massa Tubuh menurut Umur (IMT/U)', 'file' => $report->chart_imtu_image],
        ];
    @endphp

    @foreach ($charts as $chart)
        <div class="avoid-break mb-20">
            <div class="bold text-center mb-5" style="border-bottom: 1px dashed #ccc; padding-bottom:5px;">
                {{ $chart['title'] }}</div>
            <div style="text-align: center;">
                @if ($chart['file'] && file_exists(storage_path('app/public/' . $chart['file'])))
                    <img src="{{ storage_path('app/public/' . $chart['file']) }}"
                        style="width: 95%; height: auto; border: 1px solid #eee;">
                @else
                    <div style="border: 2px dashed #ddd; padding: 50px; color: #aaa; margin: 20px;">Grafik Tidak
                        Tersedia</div>
                @endif
            </div>
        </div>

        {{-- Page break kecuali grafik terakhir --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    {{-- ANALISIS FISIK --}}
    <div class="avoid-break"
        style="background-color: #f9f9f9; border: 1px solid #000; padding: 10px; margin-top: 10px;">
        <span class="bold" style="text-decoration: underline;">Kesimpulan Pertumbuhan Fisik:</span>
        <p class="text-justify" style="margin-top: 5px; margin-bottom: 0;">{{ $report->growth_analysis_desc ?? '-' }}
        </p>
    </div>

    <div class="page-break"></div>

    {{-- B. MMDST --}}
    <div class="box-title">B. PERKEMBANGAN (METODE MMDST)</div>

    <div class="mb-10">
        <span class="bold">Diagnosa Akhir: </span>
        <span class="bold uppercase" style="background-color: #e0e0e0; padding: 2px 8px; border: 1px solid #000;">
            {{ $report->mmdst_final_result ?? 'UNTESTABLE' }}
        </span>
    </div>

    <table class="table-bordered" width="100%">
        <thead>
            <tr>
                <th width="25%">ASPEK PERKEMBANGAN</th>
                <th width="15%">HASIL</th>
                <th width="60%">DESKRIPSI CAPAIAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Personal Sosial</strong><br><span style="font-size: 9pt; color:#555;">Kemandirian &
                        Sosialisasi</span></td>
                <td class="text-center bold">{{ $report->mmdst_personal_social_result }}</td>
                <td class="text-justify">{{ $report->personal_social_desc ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Motorik Halus</strong><br><span style="font-size: 9pt; color:#555;">Koordinasi Tangan &
                        Mata</span></td>
                <td class="text-center bold">{{ $report->mmdst_fine_motor_result }}</td>
                <td class="text-justify">{{ $report->fine_motor_desc ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Bahasa</strong><br><span style="font-size: 9pt; color:#555;">Bicara & Pemahaman</span></td>
                <td class="text-center bold">{{ $report->mmdst_language_result }}</td>
                <td class="text-justify">{{ $report->language_desc ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Motorik Kasar</strong><br><span style="font-size: 9pt; color:#555;">Gerak Tubuh
                        Besar</span></td>
                <td class="text-center bold">{{ $report->mmdst_gross_motor_result }}</td>
                <td class="text-justify">{{ $report->gross_motor_desc ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    {{-- C. DATA KESEHATAN & PRESENSI --}}
    <div class="avoid-break">
        <div class="box-title">C. DATA KESEHATAN & PRESENSI</div>
        <table class="table-bordered" width="100%">
            <tr>
                <th width="50%">PEMERIKSAAN KESEHATAN</th>
                <th width="50%">PRESENSI (KEHADIRAN)</th>
            </tr>
            <tr>
                <td style="padding: 0;">
                    {{-- Nested Table Kesehatan --}}
                    <table width="100%" style="margin: 0; border: none;">
                        <tr>
                            <td width="40%" style="border-bottom: 1px solid #ccc;">Mata</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->vision ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Telinga</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->hearing ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Gigi</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->teeth ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Kulit</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->skin ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Kuku</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->nails ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Kebersihan</td>
                            <td>: {{ $report->healthDetail->hygiene ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 0; vertical-align: top;">
                    {{-- Nested Table Absensi --}}
                    <table width="100%" style="margin: 0; border: none;">
                        <tr>
                            <td width="50%" style="border-bottom: 1px solid #ccc;">Sakit</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->attendance_sick }} Hari</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Izin</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->attendance_permission }} Hari
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Tanpa Ket.</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->attendance_alpha }} Hari</td>
                        </tr>
                        <tr style="background-color: #f0f0f0;">
                            <td class="bold">HADIR</td>
                            <td class="bold">: {{ $report->attendance_present }} Hari</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    {{-- D. CATATAN & TANDA TANGAN --}}

    {{-- Header Mini lagi untuk halaman terakhir --}}
    {{-- <table class="info-box" width="100%">
        <tr>
            <td width="15%" class="bold">Nama Anak</td>
            <td width="45%">: {{ $report->student->student_name }}</td>
            <td width="15%" class="bold">Semester</td>
            <td width="25%">: {{ $report->semester }}</td>
        </tr>
    </table> --}}

    <div class="box-title">D. CATATAN DAN REKOMENDASI</div>

    <div class="avoid-break" style="border: 1px solid #000; margin-bottom: 20px;">
        <div style="background-color: #f2f2f2; padding: 5px 10px; border-bottom: 1px solid #000;" class="bold">
            Catatan Guru:
        </div>
        <div style="padding: 10px; text-align: justify; min-height: 80px;">
            {{ $report->teacher_notes ?? '-' }}
        </div>
    </div>

    <div class="avoid-break" style="border: 1px solid #000; margin-bottom: 40px;">
        <div style="background-color: #f2f2f2; padding: 5px 10px; border-bottom: 1px solid #000;" class="bold">
            Rekomendasi / Tindak Lanjut:
        </div>
        <div style="padding: 10px; text-align: justify; min-height: 80px;">
            {{ $report->teacher_recommendations ?? '-' }}
        </div>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="avoid-break">
        <div class="text-right mb-10">
            Pekalongan, {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
        </div>

        <table class="signature-table" width="100%">
            {{-- Baris 1: Ortu & Guru --}}
            <tr>
                <td width="40%">
                    Orang Tua / Wali
                    <br><br>
                    @if ($report->parent_signature && file_exists(storage_path('app/public/' . $report->parent_signature)))
                        <img src="{{ storage_path('app/public/' . $report->parent_signature) }}" class="sig-img">
                    @else
                        <br><br>
                    @endif
                    <div class="sig-line"></div>
                    <div class="bold">{{ $report->parent_name ?? '....................' }}</div>
                </td>
                <td width="20%"></td>
                <td width="40%">
                    Wali Kelas
                    <br><br>
                    @if ($report->teacher_signature && file_exists(storage_path('app/public/' . $report->teacher_signature)))
                        <img src="{{ storage_path('app/public/' . $report->teacher_signature) }}" class="sig-img">
                    @else
                        <br><br>
                    @endif
                    <div class="sig-line"></div>
                    <div class="bold">{{ $report->teacher_name ?? '....................' }}</div>
                </td>
            </tr>

            {{-- Baris Tengah: Mengetahui --}}
            <tr>
                <td colspan="3" style="height: 40px; vertical-align: bottom;">
                    <div class="bold text-center">Mengetahui,</div>
                </td>
            </tr>

            {{-- Baris 2: Konsultan & Kepsek --}}
            <tr>
                <td>
                    Konsultan Tumbuh Kembang Anak
                    <br><br>
                    @if ($report->consultant_signature && file_exists(storage_path('app/public/' . $report->consultant_signature)))
                        <img src="{{ storage_path('app/public/' . $report->consultant_signature) }}" class="sig-img">
                    @else
                        <br><br>
                    @endif
                    <div class="sig-line"></div>
                    <div class="bold">{{ $report->consultant_name ?? '....................' }}</div>
                </td>
                <td></td>
                <td>
                    Kepala PAUD Non Formal<br>Al Jannah Preschool and Day Care
                    <br><br>
                    @if ($report->principal_signature && file_exists(storage_path('app/public/' . $report->principal_signature)))
                        <img src="{{ storage_path('app/public/' . $report->principal_signature) }}" class="sig-img">
                    @else
                        <br><br>
                    @endif
                    <div class="sig-line"></div>
                    <div class="bold">{{ $report->principal_name ?? '....................' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
