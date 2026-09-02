<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Hasil Pertumbuhan & Perkembangan - {{ $report->student->student_name }}</title>
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

        /** KOP SURAT */
        .header-table {
            border-bottom: 3px double #000;
            margin-bottom: 20px;
            width: 100%;
            padding-bottom: 10px;
        }

        .school-name {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 2px;
            color: #000;
        }

        .school-address {
            font-size: 10pt;
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
        }

        .info-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .info-box td {
            padding: 4px 6px;
        }

        .photo-container {
            width: 110px;
            height: 140px;
            border: 1px solid #000;
            margin: 0 auto;
            text-align: center;
            line-height: 140px;
        }

        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            font-size: 9pt;
            color: #555;
            font-weight: bold;
        }

        .signature-table td {
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            width: 80%;
            margin: 0 auto 5px auto;
        }

        .sig-img {
            max-height: 50px;
            width: auto;
            margin-bottom: 5px;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #000;
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>

    {{-- BINGKAI DEKORATIF --}}
    <div class="page-border"></div>
    <div class="page-border-inner"></div>

    @php
        $s = $report->student;

        // Path foto siswa
        $photoPath = null;
        if ($s && $s->user_photo && file_exists(storage_path('app/public/' . $s->user_photo))) {
            $photoPath = storage_path('app/public/' . $s->user_photo);
        }

        // Handle alamat
        $addressParts = array_filter([
            $s->street ?? null,
            $s->village ? 'Kel. ' . $s->village : null,
            $s->subdistrict ? 'Kec. ' . $s->subdistrict : null,
            $s->district ? 'Kab/Kota ' . $s->district : null,
        ]);
        $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : '-';
    @endphp

    {{-- ========================================== --}}
    {{-- HALAMAN 1: COVER --}}
    {{-- ========================================== --}}
    <div class="text-center" style="margin-top: 140px;">
        {{-- Logo Cover --}}
        <img src="{{ public_path('images/logo2.png') }}" style="width: 130px; height: auto;"
            onerror="this.style.display='none'">

        <br><br><br>
        <div style="font-size: 22pt;" class="bold">HASIL</div>
        <div style="font-size: 18pt;" class="bold">PERTUMBUHAN DAN PERKEMBANGAN ANAK</div>
        <br><br>

        <div style="font-size: 12pt;">NAMA PESERTA DIDIK:</div>
        <div style="border: 2px solid #000; padding: 15px; margin: 10px 40px; font-size: 18pt; background-color: #f9f9f9;"
            class="bold uppercase">
            {{ $s->student_name }}
        </div>

        <div style="margin-top: 15px;">NOMOR INDUK SISWA (NIS):</div>
        <div style="font-size: 16pt;" class="bold">{{ $s->student_number ?? '-' }}</div>

        <br><br><br><br><br>

        <div style="font-size: 20pt;" class="bold">SI-GOCHILD</div>
    </div>

    <div class="page-break"></div>

    {{-- ========================================== --}}
    {{-- HALAMAN 2: DATA DIRI --}}
    {{-- ========================================== --}}

    {{-- JUDUL HALAMAN --}}
    <div class="text-center bold"
        style="font-size: 16pt; margin-top: 40px; margin-bottom: 30px; text-decoration: underline;">
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
                <table width="100%" style="font-size: 11pt; line-height: 1.6;">
                    <tr>
                        <td width="5%">1.</td>
                        <td width="38%">Nama Lengkap</td>
                        <td width="3%">:</td>
                        <td width="54%" class="bold">{{ $s->student_name }}</td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td>Nama Panggilan</td>
                        <td>:</td>
                        <td>{{ $s->nickname ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>Nomor Induk (NIS)</td>
                        <td>:</td>
                        <td>{{ $s->student_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ ($s->gender == 1 || $s->gender == 'male' || $s->gender == 'L') ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td>5.</td>
                        <td>Tempat, Tgl Lahir</td>
                        <td>:</td>
                        <td>{{ $s->birth_place ?? '-' }},
                            {{ $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->translatedFormat('d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>6.</td>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $s->religion ?? 'Islam' }}</td>
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

    <div class="page-break"></div>

    {{-- ========================================== --}}
    {{-- HALAMAN 3 dst: ISI LAPORAN --}}
    {{-- ========================================== --}}

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td width="15%" align="center" style="vertical-align: middle;">
                <img src="{{ public_path('images/logo2.png') }}" style="height: 60px; width: auto;"
                    onerror="this.style.display='none'">
            </td>
            <td width="70%" align="center" style="vertical-align: middle;">
                <div class="school-name">SI-GOCHILD</div>
                <div class="school-address">Hasil Pertumbuhan dan Perkembangan Anak</div>
            </td>
            <td width="15%" align="center" style="vertical-align: middle;">
            </td>
        </tr>
    </table>

    {{-- TABEL INFO LAPORAN --}}
    <table class="info-box" width="100%">
        <tr>
            <td width="20%" class="bold">Nama Anak</td>
            <td width="40%">: {{ $report->student->student_name }}</td>
            <td width="20%" class="bold">Nomor Induk</td>
            <td width="20%">: {{ $report->student->student_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="bold">Tanggal Lahir</td>
            <td>: {{ $report->student->birth_date ? \Carbon\Carbon::parse($report->student->birth_date)->translatedFormat('d F Y') : '-' }}</td>
            <td class="bold">Usia / Umur</td>
            <td>: {{ $report->age_in_months }} Bulan</td>
        </tr>
        <tr>
            <td class="bold">Jenis Kelamin</td>
            <td>: {{ ($report->student->gender == 1 || $report->student->gender == 'male' || $report->student->gender == 'L') ? 'Laki-laki' : 'Perempuan' }}</td>
            <td class="bold">Tgl Pengukuran</td>
            <td>: {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}</td>
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
                <td class="text-center bold">{{ $report->weight_kg }} kg</td>
                <td class="text-center bold">{{ $report->height_cm }} cm</td>
                <td class="text-center bold">{{ $report->head_circumference_cm }} cm</td>
                <td class="text-center bold">{{ $report->bmi }}</td>
            </tr>
        </tbody>
    </table>

    {{-- INDIKATOR & GRAFIK STANDAR WHO PERTUMBUHAN --}}
    @php
        $isBaby = $report->age_in_months < 24;
        $label_TBU = $isBaby ? 'Panjang Badan menurut Umur (PB/U)' : 'Tinggi Badan menurut Umur (TB/U)';
        $label_BBTB = $isBaby ? 'Berat Badan menurut Panjang Badan (BB/PB)' : 'Berat Badan menurut Tinggi Badan (BB/TB)';

        $charts = [
            [
                'title' => '1. Berat Badan menurut Umur (BB/U)',
                'file' => $report->chart_bbu_image,
                'param' => 'BB/U',
                'val' => $report->weight_kg . ' kg',
                'std' => 'Standar WHO Antropometri Anak',
                'desc' => 'Menilai risiko berat badan kurang (underweight) atau sangat kurang.'
            ],
            [
                'title' => '2. ' . $label_TBU,
                'file' => $report->chart_tbu_image,
                'param' => $isBaby ? 'PB/U' : 'TB/U',
                'val' => $report->height_cm . ' cm',
                'std' => 'Standar WHO Antropometri Anak',
                'desc' => 'Menilai status perawakan anak (stunting / pendek / normal / tinggi).'
            ],
            [
                'title' => '3. ' . $label_BBTB,
                'file' => $report->chart_bbtb_image,
                'param' => $isBaby ? 'PB/BB' : 'TB/BB',
                'val' => $report->weight_kg . ' kg / ' . $report->height_cm . ' cm',
                'std' => 'Standar WHO Antropometri Anak',
                'desc' => 'Menilai proporsi berat terhadap tinggi badan (gizi buruk / gizi kurang / gizi baik / obesas).'
            ],
            [
                'title' => '4. Indeks Massa Tubuh menurut Umur (IMT/U)',
                'file' => $report->chart_imtu_image,
                'param' => 'IMT/U',
                'val' => 'BMI: ' . $report->bmi,
                'std' => 'Standar WHO Antropometri Anak',
                'desc' => 'Menilai komposisi massa tubuh menurut kelompok umur anak.'
            ],
        ];
    @endphp

    <div class="bold mb-5" style="font-size: 11pt; text-decoration: underline;">INDIKATOR PERTUMBUHAN STANDAR WHO (Z-SCORE)</div>

    @foreach ($charts as $chart)
        <div class="avoid-break mb-10" style="border: 1px solid #ccc; padding: 10px; background-color: #fafafa;">
            <div class="bold mb-5" style="font-size: 10pt; color: #111;">
                {{ $chart['title'] }}
            </div>
            
            @if ($chart['file'] && file_exists(storage_path('app/public/' . $chart['file'])))
                <div style="text-align: center; margin-top: 5px;">
                    <img src="{{ storage_path('app/public/' . $chart['file']) }}"
                        style="width: 95%; height: auto; border: 1px solid #ddd;">
                </div>
            @else
                <table width="100%" style="font-size: 9.5pt; margin-top: 4px;">
                    <tr>
                        <td width="30%" class="bold">Hasil Pengukuran</td>
                        <td width="70%">: {{ $chart['val'] }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Acuan Parameter</td>
                        <td>: {{ $chart['std'] }} ({{ $chart['param'] }})</td>
                    </tr>
                    <tr>
                        <td class="bold">Analisis Indikator</td>
                        <td>: {{ $chart['desc'] }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Kategori Status</td>
                        <td>: <span class="badge-status">SESUAI STANDAR PERTUMBUHAN WHO</span></td>
                    </tr>
                </table>
            @endif
        </div>
    @endforeach

    {{-- ANALISIS FISIK --}}
    <div class="avoid-break"
        style="background-color: #f9f9f9; border: 1px solid #000; padding: 10px; margin-top: 10px;">
        <span class="bold" style="text-decoration: underline;">Kesimpulan Pertumbuhan Fisik:</span>
        <p class="text-justify" style="margin-top: 5px; margin-bottom: 0;">{{ $report->growth_analysis_desc ?? 'Pertumbuhan fisik anak berjalan sesuai dengan acuan kurva pertumbuhan standar WHO.' }}
        </p>
    </div>

    <div class="page-break"></div>

    {{-- B. MMDST PERKEMBANGAN --}}
    <div class="box-title">B. PERKEMBANGAN (METODE MMDST)</div>

    <div class="mb-10">
        <span class="bold">Diagnosa Akhir Perkembangan: </span>
        <span class="bold uppercase" style="background-color: #e0e0e0; padding: 2px 8px; border: 1px solid #000;">
            {{ $report->mmdst_final_result ?? 'NORMAL' }}
        </span>
    </div>

    <table class="table-bordered" width="100%">
        <thead>
            <tr>
                <th width="28%">ASPEK PERKEMBANGAN</th>
                <th width="16%">HASIL</th>
                <th width="56%">DESKRIPSI CAPAIAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Personal Sosial</strong><br><span style="font-size: 8.5pt; color:#555;">Kemandirian & Sosialisasi</span></td>
                <td class="text-center bold">{{ $report->mmdst_personal_social_result ?? 'NORMAL' }}</td>
                <td class="text-justify">{{ $report->personal_social_desc ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Motorik Halus</strong><br><span style="font-size: 8.5pt; color:#555;">Koordinasi Tangan & Mata</span></td>
                <td class="text-center bold">{{ $report->mmdst_fine_motor_result ?? 'NORMAL' }}</td>
                <td class="text-justify">{{ $report->fine_motor_desc ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Bahasa</strong><br><span style="font-size: 8.5pt; color:#555;">Bicara & Pemahaman</span></td>
                <td class="text-center bold">{{ $report->mmdst_language_result ?? 'NORMAL' }}</td>
                <td class="text-justify">{{ $report->language_desc ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Motorik Kasar</strong><br><span style="font-size: 8.5pt; color:#555;">Gerak Tubuh Besar</span></td>
                <td class="text-center bold">{{ $report->mmdst_gross_motor_result ?? 'NORMAL' }}</td>
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
                    <table width="100%" style="margin: 0; border: none;">
                        <tr>
                            <td width="40%" style="border-bottom: 1px solid #ccc;">Mata</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->vision ?? 'Baik' }}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Telinga</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->hearing ?? 'Baik' }}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Gigi</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->teeth ?? 'Baik' }}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Kulit</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->skin ?? 'Sehat' }}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Kuku</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->healthDetail->nails ?? 'Bersih' }}</td>
                        </tr>
                        <tr>
                            <td>Kebersihan</td>
                            <td>: {{ $report->healthDetail->hygiene ?? 'Baik' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 0; vertical-align: top;">
                    <table width="100%" style="margin: 0; border: none;">
                        <tr>
                            <td width="50%" style="border-bottom: 1px solid #ccc;">Sakit</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->attendance_sick }} Hari</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ccc;">Izin</td>
                            <td style="border-bottom: 1px solid #ccc;">: {{ $report->attendance_permission }} Hari</td>
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

    <br>

    {{-- D. CATATAN & REKOMENDASI --}}
    <div class="box-title">D. CATATAN DAN REKOMENDASI</div>

    <div class="avoid-break" style="border: 1px solid #000; margin-bottom: 15px;">
        <div style="background-color: #f2f2f2; padding: 5px 10px; border-bottom: 1px solid #000;" class="bold">
            Catatan Perkembangan:
        </div>
        <div style="padding: 10px; text-align: justify; min-height: 70px;">
            {{ $report->teacher_notes ?? '-' }}
        </div>
    </div>

    <div class="avoid-break" style="border: 1px solid #000; margin-bottom: 30px;">
        <div style="background-color: #f2f2f2; padding: 5px 10px; border-bottom: 1px solid #000;" class="bold">
            Rekomendasi / Tindak Lanjut:
        </div>
        <div style="padding: 10px; text-align: justify; min-height: 70px;">
            {{ $report->teacher_recommendations ?? '-' }}
        </div>
    </div>

    {{-- TANDA TANGAN (HANYA ORANG TUA & PENDAMPING) --}}
    <div class="avoid-break" style="margin-top: 20px;">
        <div class="text-right mb-10">
            Tanggal: {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }}
        </div>

        <table class="signature-table" width="100%">
            <tr>
                <td width="45%" class="text-center">
                    Orang Tua / Wali
                    <br><br>
                    @if ($report->parent_signature && file_exists(storage_path('app/public/' . $report->parent_signature)))
                        <img src="{{ storage_path('app/public/' . $report->parent_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <div class="sig-line" style="margin: 0 auto; width: 75%;"></div>
                    <div class="bold">{{ $report->parent_name ?? '(....................)' }}</div>
                </td>
                <td width="10%"></td>
                <td width="45%" class="text-center">
                    Pendamping / Petugas
                    <br><br>
                    @if ($report->teacher_signature && file_exists(storage_path('app/public/' . $report->teacher_signature)))
                        <img src="{{ storage_path('app/public/' . $report->teacher_signature) }}" class="sig-img">
                    @else
                        <br><br><br>
                    @endif
                    <div class="sig-line" style="margin: 0 auto; width: 75%;"></div>
                    <div class="bold">{{ $report->teacher_name ?? '(....................)' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
