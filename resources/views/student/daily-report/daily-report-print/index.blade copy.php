<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian - {{ $dailyReport->activityTransaction->student->student_name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        /* --- HEADER / KOP SURAT --- */
        .header-table {
            width: 100%;
            border-bottom: 3px double #ec4899;
            /* Garis ganda pink */
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .logo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }

        .logo-img {
            height: 80px;
            width: auto;
        }

        .info-cell {
            width: 85%;
            text-align: center;
            vertical-align: middle;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #be185d;
            /* Pink Tua */
            text-transform: uppercase;
            margin: 0;
        }

        .school-desc {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #555;
        }

        .school-address {
            font-size: 10px;
            margin: 2px 0;
        }

        /* --- JUDUL HALAMAN --- */
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            color: #333;
        }

        .report-subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 20px;
            color: #666;
        }

        /* --- TABEL DATA --- */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 120px;
            color: #444;
        }

        /* --- SEKSI & KOTAK --- */
        .section-header {
            background-color: #fce7f3;
            /* Pink sangat muda */
            color: #9d174d;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 12px;
            border-left: 4px solid #ec4899;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
        }

        .data-table th {
            background-color: #f9fafb;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        /* --- SIGNATURE --- */
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .sig-box {
            width: 40%;
            float: left;
            text-align: center;
        }

        .sig-box-right {
            width: 40%;
            float: right;
            text-align: center;
        }

        .sig-img {
            height: 60px;
            margin: 10px auto;
            display: block;
        }

        .sig-line {
            border-top: 1px solid #333;
            width: 80%;
            margin: 5px auto 0;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                {{-- Gunakan public_path untuk gambar di PDF --}}
                <img src="{{ public_path('images/logo2.png') }}" class="logo-img" alt="Logo">
            </td>
            <td class="info-cell">
                <h1 class="school-name">Al Jannah Preschool & Day Care</h1>
                <div class="school-desc">Islamic, Nature, and Science School</div>
                <p class="school-address">
                    Jl. Giok No. 17 Blok B-5 Perumahan Villa Pisma Asri (Berlian)<br>
                    Desa Podo, Kec. Kedungwuni, Kab. Pekalongan 51173<br>
                    Telp/WA: 0856-0276-6027 | Email: info@aljannah.sch.id
                </p>
            </td>
        </tr>
    </table>

    {{-- JUDUL LAPORAN --}}
    <div class="report-title">LAPORAN HARIAN SISWA</div>
    <div class="report-subtitle">
        {{ \Carbon\Carbon::parse($dailyReport->period)->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </div>

    {{-- BIODATA SISWA --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Ananda</td>
            <td width="40%">: <strong>{{ $dailyReport->activityTransaction->student->student_name }}</strong></td>
            <td class="label">Kelas/Layanan</td>
            <td>: {{ $dailyReport->activityTransaction->service->service_name }}</td>
        </tr>
        <tr>
            <td class="label">NIS</td>
            <td>: {{ $dailyReport->activityTransaction->student->student_number }}</td>
            <td class="label">Suhu Tubuh</td>
            <td>: {{ $dailyReport->body_temperature ?? '-' }} °C</td>
        </tr>
    </table>

    {{-- 1. KESEHATAN & KONDISI --}}
    <div class="section-header">KESEHATAN & KONDISI</div>
    <table class="data-table">
        <tr>
            <th width="25%">Kondisi Umum</th>
            <td width="25%">{{ ucfirst($dailyReport->condition) }}</td>
            <th width="25%">Status Kesehatan</th>
            <td width="25%">{{ ucfirst($dailyReport->health_status) }}</td>
        </tr>
        <tr>
            <th>Makan Pagi</th>
            <td>{{ ucfirst($dailyReport->breakfast) }}</td>
            <th>Status Obat</th>
            <td>{{ ucfirst($dailyReport->medication_status ?? '-') }}</td>
        </tr>
        @if ($dailyReport->sickness_description)
            <tr>
                <th>Keluhan</th>
                <td colspan="3" style="color: #b91c1c; background-color: #fef2f2;">
                    {{ $dailyReport->sickness_description }}
                </td>
            </tr>
        @endif
    </table>

    {{-- 2. STIMULASI & PEMBELAJARAN --}}
    <div class="section-header">STIMULASI & PEMBELAJARAN (MMDST)</div>
    <div style="border: 1px solid #e5e7eb; padding: 10px; font-size: 12px; background: #f9fafb; border-radius: 4px;">
        {!! nl2br(e($dailyReport->stimulation_description ?? 'Tidak ada catatan stimulasi.')) !!}
    </div>

    {{-- 3. DETAIL AKTIVITAS (CONDITIONAL) --}}

    {{-- === JIKA BAYI (SERVICE ID 1) === --}}
    @if ((int) $dailyReport->service_id === 1 && $dailyReport->babyDetail)
        @php $baby = $dailyReport->babyDetail; @endphp

        <div class="section-header">AKTIVITAS HARIAN (BAYI)</div>

        {{-- Tabel Susu --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th colspan="4" style="text-align: left; background-color: #eff6ff; color: #1e40af;">🍼 Konsumsi
                        Susu</th>
                </tr>
                <tr>
                    <th width="10%">No</th>
                    <th width="30%">Jam</th>
                    <th width="30%">Takaran</th>
                    <th width="30%">Jenis</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($baby->asi_formula_items ?? []) as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">{{ $item['jam'] ?? '-' }}</td>
                        <td class="text-center">{{ $item['takaran'] ?? '-' }} ml</td>
                        <td class="text-center">{{ $item['asi'] ?? false ? 'ASI' : 'Sufor' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500">- Tidak ada data -</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Tidur & Popok (Side by Side) --}}
        <table width="100%">
            <tr>
                <td width="48%" valign="top" style="padding-right: 5px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th colspan="2" style="text-align: left; background-color: #f3e8ff; color: #6b21a8;">
                                    💤 Tidur</th>
                            </tr>
                            <tr>
                                <th>Mulai</th>
                                <th>Bangun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($baby->naps ?? []) as $item)
                                <tr>
                                    <td class="text-center">{{ $item['tidur'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['bangun'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td width="48%" valign="top" style="padding-left: 5px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th colspan="3" style="text-align: left; background-color: #fefce8; color: #854d0e;">
                                    🧷 Popok</th>
                            </tr>
                            <tr>
                                <th>Jam</th>
                                <th>BAK</th>
                                <th>BAB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($baby->diapers ?? []) as $item)
                                <tr>
                                    <td class="text-center">{{ $item['jam'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['bak'] ?? false ? '✓' : '' }}</td>
                                    <td class="text-center">{{ $item['bab'] ?? false ? '✓' : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">-</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        {{-- MPASI --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th colspan="2" style="text-align: left; background-color: #ecfdf5; color: #065f46;">🥣 MP-ASI
                        (Makan)</th>
                </tr>
            </thead>
            <tr>
                <td width="30%"><strong>Pagi</strong></td>
                <td>{{ $baby->infant_breakfast_text ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Siang</strong></td>
                <td>{{ $baby->infant_lunch_text ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Malam</strong></td>
                <td>{{ $baby->infant_dinner_text ?? '-' }}</td>
            </tr>
        </table>

        {{-- === JIKA CHILDREN (SERVICE ID 2) === --}}
    @elseif((int) $dailyReport->service_id === 2 && $dailyReport->childrenDetail)
        @php $ch = $dailyReport->childrenDetail; @endphp

        <div class="section-header">JADWAL & AKTIVITAS (CHILDREN)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="15%">Jam</th>
                    <th width="35%">Kegiatan</th>
                    <th width="25%">Ket.</th>
                    <th width="25%">Detail</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">07:30</td>
                    <td><strong>Belajar Sesi 1</strong></td>
                    <td>{{ $ch->session1_activity ?? '-' }}</td>
                    <td>{{ $ch->session1Material->material_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">09:30</td>
                    <td><strong>Belajar Sesi 2</strong></td>
                    <td>{{ $ch->session2_activity ?? '-' }}</td>
                    <td>{{ $ch->session2Material->material_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">11:15</td>
                    <td>Makan Siang</td>
                    <td>{{ ucfirst($ch->cheerful_lunch ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center">12:30</td>
                    <td>Tidur Siang</td>
                    <td>{{ ucfirst($ch->healthy_sleep ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center">15:30</td>
                    <td>Ekstra Stimulasi</td>
                    <td>{{ $ch->extra_stimulation ?? '-' }}</td>
                    <td>{{ $ch->extra_stimulation_description ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- 4. CATATAN GURU --}}
    <div class="section-header">CATATAN GURU</div>
    <div style="border: 1px dashed #fbbf24; background-color: #fffbeb; padding: 10px; border-radius: 4px;">
        <i>"{{ $dailyReport->notes ?? 'Tidak ada catatan khusus hari ini.' }}"</i>
    </div>

    {{-- 5. TANDA TANGAN --}}
    <div class="signature-section">
        <div class="sig-box">
            <p>Orang Tua / Wali</p>
            <br>
            @if ($dailyReport->parent_guardian_signature)
                {{-- Pastikan file signature ada di folder storage/app/public/signatures --}}
                <img src="{{ public_path('storage/' . $dailyReport->parent_guardian_signature) }}" class="sig-img"
                    alt="TTD Orang Tua">
            @else
                <div style="height: 60px;"></div>
            @endif
            <div class="sig-line">( ...................................... )</div>
        </div>

        <div class="sig-box-right">
            <p>Guru Pendamping</p>
            <br>
            <div style="height: 60px;"></div> {{-- TTD Guru (Manual) --}}
            <div class="sig-line">( ...................................... )</div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
