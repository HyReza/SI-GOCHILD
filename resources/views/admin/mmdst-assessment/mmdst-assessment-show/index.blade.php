<x-app-layout>
    <x-slot:title>Detail Penilaian MMDST</x-slot:title>

    {{-- Style Khusus --}}
    <style>
        /* Transisi halus */
        tr.hover-row {
            transition: background-color 0.2s;
        }

        .bucket-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        /* Animasi Pulse Halus untuk Delay */
        @keyframes soft-pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .animate-soft-pulse {
            animation: soft-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

    @php
        /** @var \App\Models\MmdstAssessment $assessment */
        $student = $assessment->student;
        $creator = $assessment->creator;
        $dateID = optional($assessment->assessment_date)->translatedFormat('d F Y');

        // 1. Usia Detail
        $ageInDays = (int) ($assessment->age_in_days ?? 0);
        $ageDetail = '—';
        if ($student && $student->birth_date && $assessment->assessment_date) {
            $birthDate = \Carbon\Carbon::parse($student->birth_date);
            $assessDate = \Carbon\Carbon::parse($assessment->assessment_date);
            $ageDetail = $birthDate->diff($assessDate)->format('%y Thn, %m Bln, %d Hr');
        }

        // 2. Rekap Hasil (Tanpa NR)
        $items = $assessment->items;
        $counts = [
            'P' => $items->where('result_code', 'P')->count(),
            'F' => $items->where('result_code', 'F')->count(),
            'R' => $items->where('result_code', 'R')->count(),
            'OP' => $items->where('result_code', 'OP')->count(),
        ];
        $totalRated = array_sum($counts);
        $totalDelay = $items->where('is_delay', true)->count();

        // 3. Overall Result
        $overall = $assessment->overall_result ?? '—';
        $overallBadge = match ($overall) {
            'NORMAL' => 'bg-green-100 text-green-700 border-green-200',
            'ABNORMAL' => 'bg-red-100 text-red-700 border-red-200',
            'QUESTIONABLE' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'UNTESTABLE' => 'bg-gray-200 text-gray-700 border-gray-300',
            default => 'bg-gray-100 text-gray-500 border-gray-200',
        };

        // 4. Grouping & Summary Sektor
        $groupedParams = $parameters->groupBy(
            fn($p) => optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori',
        );

        $sectorSummary = [];
        foreach ($items as $it) {
            $cat = optional($it->parameter?->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori';
            if (!isset($sectorSummary[$cat])) {
                $sectorSummary[$cat] = ['P' => 0, 'F' => 0, 'R' => 0, 'OP' => 0, 'total' => 0, 'delay' => 0];
            }
            if (in_array($it->result_code, ['P', 'F', 'R', 'OP'])) {
                $sectorSummary[$cat][$it->result_code]++;
            }
            $sectorSummary[$cat]['total']++;
            if ($it->is_delay) {
                $sectorSummary[$cat]['delay']++;
            }
        }

        // Helper: Persentase
        $pct = fn($num, $den) => $den ? number_format(($num / $den) * 100, 0) . '%' : '0%';

        // Helper: Text & Class
        function resultText($code)
        {
            return match ($code) {
                'P' => 'LULUS',
                'F' => 'GAGAL',
                'R' => 'ULANG',
                'OP' => 'BELUM',
                default => '—',
            };
        }
        function resultClass($code)
        {
            return match ($code) {
                'P' => 'bg-green-100 text-green-700 border-green-200',
                'F' => 'bg-red-100 text-red-700 border-red-200',
                'R' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                'OP' => 'bg-gray-200 text-gray-700 border-gray-300',
                default => 'bg-gray-100 text-gray-400',
            };
        }

        // Helper: Bucket Usia
        function getBucketInfo($age, $p25, $p75, $p100, $passed)
        {
            $p25 = (int) $p25;
            $p75 = (int) $p75;
            $p100 = (int) $p100;

            if ($age > $p100 && $passed) {
                return ['label' => 'Lewat Usia (Lulus)', 'cls' => 'bg-green-100 text-green-700 border-green-200'];
            }
            if ($age < $p25) {
                return ['label' => 'Belum Waktunya', 'cls' => 'bg-gray-100 text-gray-500 border-gray-200'];
            }
            if ($age > $p100) {
                return ['label' => 'Lewat Usia', 'cls' => 'bg-red-100 text-red-700 border-red-200'];
            }
            if ($age >= $p75 && $age <= $p100) {
                return ['label' => 'Zona Kritis', 'cls' => 'bg-orange-100 text-orange-800 border-orange-300 font-bold'];
            }
            if ($age == $p25) {
                return ['label' => 'Di Garis Usia', 'cls' => 'bg-blue-600 text-white font-bold'];
            }

            return ['label' => 'Rentang Usia', 'cls' => 'bg-blue-100 text-blue-700 border-blue-200'];
        }
    @endphp

    {{-- === HEADER & ACTION BAR === --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">assignment_turned_in</span>
                Detail Penilaian MMDST
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Siswa: <b>{{ $student?->student_name ?? '—' }}</b>
                <span class="text-gray-400">({{ $student?->student_number ?? '-' }})</span>
                &bull; Tgl: <b>{{ $dateID }}</b>
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($student)
                <a href="{{ route('mmdst.history', $student) }}"
                    class="inline-flex items-center gap-1 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <span class="material-symbols-outlined text-sm">history</span> Riwayat
                </a>
            @endif
            <a href="{{ route('mmdst-assessments.edit', $assessment) }}"
                class="inline-flex items-center gap-1 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium shadow-md transition">
                <span class="material-symbols-outlined text-sm">edit_square</span> Edit
            </a>
            <button type="button" onclick="confirmDelete()"
                class="inline-flex items-center gap-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium shadow-md transition">
                <span class="material-symbols-outlined text-sm">delete</span> Hapus
            </button>
        </div>
    </div>

    {{-- === RINGKASAN DATA (GRID) === --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Kartu 1: Hasil Overall --}}
        <div
            class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex flex-col justify-center items-center text-center">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Hasil Diagnosis</h3>
            <div class="px-5 py-2 rounded-full text-lg font-bold border-2 {{ $overallBadge }} mb-2">
                {{ $overall }}
            </div>
            @if ($totalDelay > 0)
                <div
                    class="inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-bold border border-red-100 animate-soft-pulse">
                    <span class="material-symbols-outlined text-sm">warning</span>
                    {{ $totalDelay }} Item Delay
                </div>
            @else
                <span class="text-xs text-gray-400 italic">Tidak ada item Delay</span>
            @endif
            <div class="mt-4 pt-3 border-t border-gray-100 w-full text-xs text-gray-500">
                Petugas: <b>{{ $creator?->user_name ?? '—' }}</b>
            </div>
        </div>

        {{-- Kartu 2: Statistik Nilai --}}
        <div
            class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Statistik Nilai</h3>
                <div class="text-right">
                    <span class="text-xs text-gray-500 block">Usia Kronologis</span>
                    <span class="font-bold text-blue-600 text-sm">{{ $ageDetail }}</span>
                    <span class="text-xs text-gray-400">({{ $ageInDays }} hari)</span>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div class="text-center p-3 rounded-lg bg-green-50 border border-green-100">
                    <div class="text-2xl font-bold text-green-600">{{ $counts['P'] }}</div>
                    <div class="text-[10px] font-bold text-green-800 uppercase mt-1">Lulus (P)</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-red-50 border border-red-100">
                    <div class="text-2xl font-bold text-red-600">{{ $counts['F'] }}</div>
                    <div class="text-[10px] font-bold text-red-800 uppercase mt-1">Gagal (F)</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-yellow-50 border border-yellow-100">
                    <div class="text-2xl font-bold text-yellow-600">{{ $counts['R'] }}</div>
                    <div class="text-[10px] font-bold text-yellow-800 uppercase mt-1">Ulang (R)</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-gray-50 border border-gray-200">
                    <div class="text-2xl font-bold text-gray-600">{{ $counts['OP'] }}</div>
                    <div class="text-[10px] font-bold text-gray-800 uppercase mt-1">Belum (OP)</div>
                </div>
            </div>
            <div class="mt-4 text-center text-xs text-gray-500">
                Total item dinilai: <b>{{ $totalRated }}</b>
            </div>
        </div>
    </div>

    {{-- === LEGENDA INFORMATIF === --}}
    <div
        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 mb-6 text-xs">
        <h3
            class="font-bold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide border-b pb-2 dark:border-gray-700">
            Panduan Indikator Usia & Hasil</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <div class="flex items-start gap-2">
                    <span
                        class="px-2 py-0.5 rounded bg-blue-600 text-white font-bold w-24 text-center text-[10px] shrink-0">Di
                        Garis Usia</span>
                    <span class="text-gray-600 dark:text-gray-400">Usia anak <b>tepat sama</b> dengan batas awal (25%).
                        Waktunya mulai belajar.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span
                        class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 border border-blue-200 w-24 text-center text-[10px] shrink-0">Rentang
                        Usia</span>
                    <span class="text-gray-600 dark:text-gray-400">Usia anak di antara <b>25% - 75%</b>. Fase normal
                        perkembangan kemampuan.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span
                        class="px-2 py-0.5 rounded bg-orange-100 text-orange-800 border border-orange-300 font-bold w-24 text-center text-[10px] shrink-0">Zona
                        Kritis</span>
                    <span class="text-gray-600 dark:text-gray-400">Usia <b>75% - 100%</b>. Seharusnya sudah bisa. Jika
                        Gagal, dianggap <b>Keterlambatan (Delay)</b>.</span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-start gap-2">
                    <span
                        class="px-2 py-0.5 rounded bg-red-100 text-red-700 border border-red-200 w-24 text-center text-[10px] shrink-0">Lewat
                        Usia</span>
                    <span class="text-gray-600 dark:text-gray-400">Usia <b>> 100%</b>. Sudah melewati batas maksimal.
                        Terlambat jika belum bisa.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span
                        class="px-2 py-0.5 rounded bg-green-100 text-green-700 border border-green-200 w-24 text-center text-[10px] shrink-0">Lewat
                        (Lulus)</span>
                    <span class="text-gray-600 dark:text-gray-400">Usia lewat, tapi anak <b>Lulus (P)</b>. Perkembangan
                        normal/sesuai.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span
                        class="px-2 py-0.5 rounded bg-gray-100 text-gray-500 border border-gray-200 w-24 text-center text-[10px] shrink-0">Belum
                        Waktunya</span>
                    <span class="text-gray-600 dark:text-gray-400">Usia anak masih di bawah batas awal (25%). Tidak
                        wajib bisa.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- === CATATAN UTAMA === --}}
    @if (filled($assessment->notes))
        <div
            class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-gray-500">description</span>
                Catatan Utama
            </h3>
            <div
                class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line border border-gray-200 dark:border-gray-700">
                {{ $assessment->notes }}
            </div>
        </div>
    @endif

    {{-- === TABEL RINGKASAN PER SEKTOR === --}}
    <div
        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm uppercase">Ringkasan per Sektor</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead
                    class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-[10px] tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-4 text-left">Sektor</th>
                        <th class="py-3 px-4 text-center">Total Item</th>
                        <th class="py-3 px-4 text-center text-green-600">P</th>
                        <th class="py-3 px-4 text-center text-red-600">F</th>
                        <th class="py-3 px-4 text-center text-yellow-600">R</th>
                        <th class="py-3 px-4 text-center text-gray-500">OP</th>
                        <th class="py-3 px-4 text-center font-bold text-red-700">Delay</th>
                        <th class="py-3 px-4 text-center">Persentase Lulus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sectorSummary as $catName => $s)
                        <tr class="hover-row hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-3 px-4 font-medium">{{ $catName }}</td>
                            <td class="py-3 px-4 text-center">{{ $s['total'] }}</td>
                            <td class="py-3 px-4 text-center"><span
                                    class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-bold border border-green-200">{{ $s['P'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center"><span
                                    class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-bold border border-red-200">{{ $s['F'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center"><span
                                    class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700 font-bold border border-yellow-200">{{ $s['R'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center"><span
                                    class="px-2 py-0.5 rounded bg-gray-200 text-gray-700 font-bold border border-gray-300">{{ $s['OP'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center"><span
                                    class="px-2 py-0.5 rounded {{ $s['delay'] > 0 ? 'bg-red-100 text-red-700 font-bold border border-red-200' : 'bg-gray-50 text-gray-400' }}">{{ $s['delay'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-center"><span
                                    class="text-blue-600 font-bold">{{ $pct($s['P'], max($s['total'], 1)) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-500 italic">Belum ada data sektor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- === DETAIL ITEM PER SEKTOR (ACCORDION) === --}}
    <div class="space-y-6">
        @foreach ($groupedParams as $categoryName => $params)
            @php
                $slug = \Illuminate\Support\Str::slug($categoryName, '-');
                // Cek apakah ada delay di sektor ini untuk ditampilkan di header
                $delayInCat = $sectorSummary[$categoryName]['delay'] ?? 0;
            @endphp

            <div
                class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Header Sektor --}}
                <div class="flex items-center justify-between px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-100 transition"
                    onclick="toggleCat('{{ $slug }}')">
                    <div class="flex items-center gap-3">
                        <h3
                            class="font-bold text-gray-800 dark:text-gray-100 uppercase text-sm tracking-wide border-l-4 border-blue-500 pl-3">
                            {{ $categoryName }}
                        </h3>
                        @if ($delayInCat > 0)
                            <span
                                class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold border border-red-200 animate-soft-pulse">
                                DELAY: {{ $delayInCat }}
                            </span>
                        @endif
                    </div>
                    <button type="button" class="text-gray-500 hover:text-blue-600 transition">
                        <span class="material-symbols-outlined transform transition-transform"
                            id="icon-{{ $slug }}">expand_more</span>
                    </button>
                </div>

                {{-- Tabel Item --}}
                <div id="cat-{{ $slug }}" class="overflow-x-auto transition-all duration-300">
                    <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        <thead
                            class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase text-[10px] tracking-wider font-semibold">
                            <tr>
                                <th class="py-3 px-4 text-left w-64">Item Tes</th>
                                <th class="py-3 px-4 text-left hidden md:table-cell">Deskripsi</th>
                                <th class="py-3 px-4 text-left w-48">Rentang Usia (25-50-75-100%)</th>
                                <th class="py-3 px-4 text-center w-24">Hasil</th>
                                <th class="py-3 px-4 text-left w-48">Indikator Usia</th>
                                <th class="py-3 px-4 text-left w-64">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @php $hasData = false; @endphp
                            @foreach ($params as $p)
                                @php
                                    $item = $assessment->items->firstWhere('mmdst_parameter_id', $p->id);
                                    if (!$item || !$item->result_code) {
                                        continue;
                                    }

                                    $hasData = true;
                                    $code = $item->result_code;
                                    $passed = $code === 'P';
                                    $isDelay = $item->is_delay;
                                    $note = $item->note;

                                    $rClass = resultClass($code);
                                    $rText = resultText($code);
                                    $bucketInfo = getBucketInfo(
                                        $ageInDays,
                                        $p->percent_25,
                                        $p->percent_75,
                                        $p->percent_100,
                                        $passed,
                                    );
                                    $bClass = $bucketInfo['cls'];
                                    $bLabel = $bucketInfo['label'];
                                @endphp
                                <tr class="hover-row hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="py-3 px-4 align-top font-medium text-gray-900 dark:text-gray-100">
                                        {{ $p->test_element_name }}
                                        <div
                                            class="md:hidden text-[10px] text-gray-500 mt-1 italic border-l-2 pl-2 border-gray-300">
                                            {{ \Illuminate\Support\Str::limit($p->test_element_description, 50) }}
                                        </div>
                                    </td>
                                    <td
                                        class="py-3 px-4 align-top hidden md:table-cell text-xs text-gray-500 leading-relaxed">
                                        {{ $p->test_element_description ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 align-top font-mono text-[10px] text-gray-500">
                                        <div class="flex flex-col">
                                            <span>25%: <b>{{ $p->percent_25 }}</b></span>
                                            <span>50%: <b>{{ $p->percent_50 }}</b></span>
                                            <span>75%: <b class="text-orange-600">{{ $p->percent_75 }}</b></span>
                                            <span>100%: <b>{{ $p->percent_100 }}</b></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 align-top text-center">
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded text-[10px] font-bold border shadow-sm {{ $rClass }}">
                                            {{ $rText }}
                                        </span>
                                        @if ($isDelay)
                                            <div
                                                class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-600 border border-red-200 animate-pulse">
                                                <span class="material-symbols-outlined text-[10px]">
                                                    warning
                                                </span>
                                                <span class="text-[10px] font-bold uppercase tracking-wider">
                                                    Delay
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 align-top">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] uppercase font-bold border tracking-wide whitespace-normal text-center leading-tight {{ $bClass }}">
                                            {{ $bLabel }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 align-top text-xs text-gray-600 italic">
                                        {{ $note ?: '—' }}
                                    </td>
                                </tr>
                            @endforeach

                            @if (!$hasData)
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 italic">Tidak ada item
                                        yang dinilai pada sektor ini.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Form Delete Hidden --}}
    <form id="form-delete" action="{{ route('mmdst-assessments.destroy', $assessment) }}" method="POST"
        class="hidden">
        @csrf @method('DELETE')
    </form>

    <script>
        function toggleCat(id) {
            const el = document.getElementById('cat-' + id);
            const icon = document.getElementById('icon-' + id);
            if (el) {
                el.classList.toggle('hidden');
                if (el.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(-90deg)';
                } else {
                    icon.style.transform = 'rotate(0deg)';
                }
            }
        }

        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Penilaian?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('form-delete').submit();
                }
            });
        }
    </script>
</x-app-layout>
