<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian - {{ $dailyReport->activityTransaction->student->student_name }}</title>
    <style>
        /* --- PERBAIKAN UTAMA DI SINI --- */
        @page {
            margin: 20px;
        }

        body {
            /* Menggunakan DejaVu Sans agar simbol terbaca */
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.3;
        }

        /* --- HEADER / KOP SURAT --- */
        .header-table {
            width: 100%;
            border-bottom: 3px double #be185d;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }

        .info-cell {
            width: 70%;
            text-align: center;
            vertical-align: middle;
        }

        .logo-img {
            height: 65px;
            width: auto;
            object-fit: contain;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #be185d;
            text-transform: uppercase;
            margin: 0;
        }

        .school-desc {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 3px;
            color: #4b5563;
            text-transform: uppercase;
        }

        .school-address {
            font-size: 9px;
            margin: 0;
            color: #374151;
        }

        /* --- JUDUL --- */
        .title-section {
            text-align: center;
            margin-bottom: 15px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            color: #111827;
        }

        .report-date {
            font-size: 11px;
            color: #4b5563;
            margin-top: 2px;
        }

        /* --- BIODATA --- */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px;
            vertical-align: top;
        }

        .label-cell {
            font-weight: bold;
            width: 110px;
            color: #374151;
        }

        /* --- SECTION HEADER --- */
        .section-header {
            background-color: #fce7f3;
            color: #9d174d;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 10px;
            border-left: 4px solid #be185d;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        /* --- TABEL DATA --- */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
        }

        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            color: #374151;
        }

        /* Helper Classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }

        /* --- TANDA TANGAN --- */
        .signature-wrapper {
            margin-top: 30px;
            width: 100%;
        }

        .sig-table {
            width: 100%;
            border: none;
        }

        .sig-table td {
            border: none;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-img {
            height: 60px;
            max-width: 150px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
        }

        /* Checkmark Style */
        .check {
            font-family: 'DejaVu Sans', sans-serif;
            /* Wajib untuk simbol */
            color: #16a34a;
            /* Green */
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>

<body>

    {{-- 1. HEADER / KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo2.png') }}" class="logo-img" alt="Logo">
            </td>
            <td class="info-cell">
                <div class="school-name">Al Jannah Preschool & Day Care</div>
                <div class="school-desc">Islamic, Nature, and Science School</div>
                <div class="school-address">
                    Jl. Giok No. 17 Blok B-5 Perumahan Villa Pisma Asri (Berlian)<br>
                    Desa Podo, Kec. Kedungwuni, Kab. Pekalongan 51173<br>
                    Telp/WA: 0856-0276-6027 | Email: info@aljannah.sch.id
                </div>
            </td>
            <td class="logo-cell">
                <img src="{{ public_path('images/barcode.png') }}" class="logo-img" alt="Logo">
            </td>
        </tr>
    </table>

    {{-- 2. JUDUL --}}
    <div class="title-section">
        <div class="report-title">LAPORAN HARIAN SISWA</div>
        <div class="report-date">
            {{ \Carbon\Carbon::parse($dailyReport->period)->locale('id')->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    {{-- 3. BIODATA --}}
    <table class="info-table">
        <tr>
            <td class="label-cell">Nama</td>
            <td width="40%">: <strong>{{ $dailyReport->activityTransaction->student->student_name }}</strong></td>
            <td class="label-cell">Layanan</td>
            <td>: {{ $dailyReport->activityTransaction->service->service_name }}</td>
        </tr>
        <tr>
            <td class="label-cell">NIS</td>
            <td>: {{ $dailyReport->activityTransaction->student->student_number ?? '-' }}</td>
            <td class="label-cell">Suhu Tubuh</td>
            <td>: {{ $dailyReport->body_temperature ?? '-' }} °C</td>
        </tr>
    </table>

    {{-- 4. KESEHATAN --}}
    <div class="section-header">1. KESEHATAN & KONDISI</div>
    <table class="data-table">
        <tr>
            <th width="20%">Kondisi Mood</th>
            <td width="30%">{{ ucfirst($dailyReport->condition) }}</td>
            <th width="20%">Status Kesehatan</th>
            <td width="30%">{{ ucfirst($dailyReport->health_status) }}</td>
        </tr>
        <tr>
            <th>Makan Pagi</th>
            <td>{{ ucfirst($dailyReport->breakfast) }}</td>
            <th>Obat</th>
            <td>{{ ucfirst($dailyReport->medication_status ?? '-') }}</td>
        </tr>
        @if ($dailyReport->health_status === 'sakit' && $dailyReport->sickness_description)
            <tr>
                <th>Keterangan Sakit</th>
                <td colspan="3" style="color: #b91c1c; background-color: #fef2f2;">
                    {{ $dailyReport->sickness_description }}
                </td>
            </tr>
        @endif
    </table>

    {{-- 5. STIMULASI --}}
    <div class="section-header">2. STIMULASI & PEMBELAJARAN</div>
    <div style="border: 1px solid #d1d5db; padding: 8px; font-size: 10px; border-radius: 4px;">
        {!! nl2br(e($dailyReport->stimulation_description ?? 'Tidak ada catatan stimulasi.')) !!}
    </div>

    {{-- 6. DETAIL AKTIVITAS --}}

    {{-- ===== JIKA BAYI ===== --}}
    @if ((int) $dailyReport->service_id === 1 && $dailyReport->babyDetail)
        @php $baby = $dailyReport->babyDetail; @endphp

        <div class="section-header">3. AKTIVITAS BAYI</div>

        {{-- SUSU --}}
        <table class="data-table">
            <thead>
                <tr>
                    {{-- Hapus Emoji 🍼 agar tidak jadi "?" --}}
                    <th colspan="3" style="text-align: left; background-color: #eff6ff;">Konsumsi Susu</th>
                </tr>
                <tr>
                    <th>Jam</th>
                    <th>Takaran</th>
                    <th>ASI</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($baby->asi_formula_items ?? []) as $item)
                    <tr>
                        <td class="text-center font-mono">{{ $item['jam'] ?? '-' }}</td>
                        <td class="text-center">{{ $item['takaran'] ?? 0 }} ml</td>
                        <td class="text-center">{{ isset($item['asi']) && $item['asi'] ? 'Ya' : 'Tidak' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">- Tidak ada data -</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table width="100%" style="margin-top: 5px;">
            <tr>
                <td width="50%" valign="top" style="padding-right: 5px;">
                    {{-- MAKAN --}}
                    <table class="data-table">
                        {{-- Hapus Emoji 🥣 --}}
                        <tr>
                            <th colspan="2" style="text-align: left; background-color: #ecfdf5;">MP-ASI (Makan)</th>
                        </tr>
                        <tr>
                            <td width="30%"><b>Pagi</b></td>
                            <td>{{ $baby->infant_breakfast_text ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><b>Siang</b></td>
                            <td>{{ $baby->infant_lunch_text ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><b>Malam</b></td>
                            <td>{{ $baby->infant_dinner_text ?? '-' }}</td>
                        </tr>
                    </table>

                    @if (!empty($baby->mpasi_items))
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th colspan="2">Log Waktu Makan</th>
                                </tr>
                            </thead>
                            @foreach ($baby->mpasi_items as $m)
                                <tr>
                                    <td class="text-center font-mono" width="30%">{{ $m['jam'] ?? '-' }}</td>
                                    <td>{{ ucfirst($m['jumlah'] ?? '-') }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                </td>

                <td width="50%" valign="top" style="padding-left: 5px;">
                    {{-- TIDUR --}}
                    <table class="data-table">
                        <thead>
                            {{-- Hapus Emoji 💤 --}}
                            <tr>
                                <th colspan="2" style="text-align: left; background-color: #f3e8ff;">Pola Tidur</th>
                            </tr>
                            <tr>
                                <th>Mulai</th>
                                <th>Bangun</th>
                            </tr>
                        </thead>
                        @forelse(($baby->naps ?? []) as $nap)
                            <tr>
                                <td class="text-center font-mono">{{ $nap['tidur'] ?? '-' }}</td>
                                <td class="text-center font-mono">{{ $nap['bangun'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">-</td>
                            </tr>
                        @endforelse
                    </table>

                    {{-- POPOK --}}
                    <table class="data-table">
                        <thead>
                            {{-- Hapus Emoji 🧷 --}}
                            <tr>
                                <th style="text-align: left; background-color: #fefce8;">Popok</th>
                                <th>BAK</th>
                                <th>BAB</th>
                            </tr>
                        </thead>
                        @forelse(($baby->diapers ?? []) as $d)
                            <tr>
                                <td class="text-center font-mono">{{ $d['jam'] ?? '-' }}</td>
                                {{-- Gunakan HTML Entity untuk Centang --}}
                                <td class="text-center check">{!! isset($d['bak']) && $d['bak'] ? '&#10003;' : '' !!}</td>
                                <td class="text-center check">{!! isset($d['bab']) && $d['bab'] ? '&#10003;' : '' !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">-</td>
                            </tr>
                        @endforelse
                    </table>
                </td>
            </tr>
        </table>
    @endif

    {{-- ===== JIKA ANAK (CHILDREN) ===== --}}
    @if ((int) $dailyReport->service_id === 2 && $dailyReport->childrenDetail)
        @php $ch = $dailyReport->childrenDetail; @endphp

        <div class="section-header">3. JADWAL & AKTIVITAS</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="13%">Waktu</th>
                    <th width="37%">Kegiatan</th>
                    <th width="25%">Status</th>
                    <th width="25%">Keterangan / Materi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center font-mono">06:30 - 07:30</td>
                    <td>Salam Penyambutan & Doa</td>
                    <td class="text-center">{{ ucfirst($ch->greeting_and_morning_prayer ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">07:30 - 09:00</td>
                    <td><strong>Belajar Sesi 1</strong></td>
                    <td class="text-center"><strong>{{ $ch->session1_activity ?? '-' }}</strong></td>
                    <td>{{ $ch->session1Material->material_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">09:00 - 09:30</td>
                    <td>Toilet Training & Dhuha</td>
                    <td class="text-center">{{ ucfirst($ch->toilet_training_and_duha_prayer ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">09:30 - 10:00</td>
                    <td><strong>Belajar Sesi 2</strong></td>
                    <td class="text-center"><strong>{{ $ch->session2_activity ?? '-' }}</strong></td>
                    <td>{{ $ch->session2Material->material_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">10:00 - 10:30</td>
                    <td>Snack Pagi</td>
                    <td class="text-center">{{ ucfirst($ch->morning_snack ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">10:30 - 11:15</td>
                    <td>Kerapian & Kemandirian</td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $ch->neatness_and_independence ?? '-')) }}
                    </td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">11:15 - 11:45</td>
                    <td>Makan Siang Ceria</td>
                    <td class="text-center">{{ ucfirst($ch->cheerful_lunch ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">11:45 - 12:00</td>
                    <td>Kebersihan & Gosok Gigi</td>
                    <td class="text-center">{{ ucfirst($ch->cleanliness_and_brushing_training ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">12:00 - 12:30</td>
                    <td>Sholat Dzuhur</td>
                    <td class="text-center">{{ ucfirst($ch->dhuhr_prayer ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">12:30 - 14:00</td>
                    <td>Tidur Sehat</td>
                    <td class="text-center">{{ ucfirst($ch->healthy_sleep ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">14:00 - 14:30</td>
                    <td>Mandi Sore</td>
                    <td class="text-center">{{ ucfirst($ch->afternoon_bath ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">14:30 - 15:00</td>
                    <td>Snack Sore</td>
                    <td class="text-center">{{ ucfirst($ch->afternoon_snack ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">15:00 - 15:30</td>
                    <td>Sholat Ashar</td>
                    <td class="text-center">{{ ucfirst($ch->asr_prayer ?? '-') }}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">15:30 - 16:00</td>
                    <td>Ekstra Stimulasi</td>
                    <td class="text-center"><strong>{{ $ch->extra_stimulation ?? '-' }}</strong></td>
                    <td>{{ $ch->extra_stimulation_description ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center font-mono">16:00 - 17:00</td>
                    <td>Permainan Ceria</td>
                    <td class="text-center"><strong>{{ $ch->cheerful_play ?? '-' }}</strong></td>
                    <td>{{ $ch->cheerful_play_description ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- 7. CATATAN GURU --}}
    <div class="section-header">4. CATATAN GURU</div>
    <div
        style="border: 1px dashed #d97706; background-color: #fffbeb; padding: 10px; border-radius: 4px; font-style: italic; color: #555;">
        "{{ $dailyReport->notes ?? 'Tidak ada catatan khusus hari ini.' }}"
    </div>

    {{-- 8. TANDA TANGAN --}}
    <table class="signature-wrapper sig-table">
        <tr>
            <td width="50%">
                <p>Mengetahui,<br><strong>Orang Tua / Wali</strong></p>
                @if ($dailyReport->parent_guardian_signature)
                    <img src="{{ public_path('storage/' . $dailyReport->parent_guardian_signature) }}"
                        class="sig-img" alt="TTD Ortu">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="sig-line">{{ $dailyReport->parent_guardian_name ?? '( ............................. )' }}
                </div>
            </td>
            <td width="50%">
                <p>Dibuat Oleh,<br><strong>Guru Pendamping</strong></p>
                @if ($dailyReport->teacher_signature)
                    <img src="{{ public_path('storage/' . $dailyReport->teacher_signature) }}" class="sig-img"
                        alt="TTD Guru">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="sig-line">{{ $dailyReport->teacher_name ?? '( ............................. )' }}</div>
            </td>
        </tr>
    </table>

</body>

</html>
