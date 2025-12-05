<x-app-layout>
    <x-slot:title>Detail Penilaian MMDST</x-slot:title>

    @php
        /** @var \App\Models\MmdstAssessment $assessment */
        $student = $assessment->student;
        $creator = $assessment->creator;
        $dateISO = optional($assessment->assessment_date)->format('Y-m-d');
        $dateID = optional($assessment->assessment_date)->format('d M Y');
        $ageDays = (int) ($assessment->age_in_days ?? 0);

        // Hitung ringkasan hasil
        $counts = [
            'P' => $assessment->items->where('result_code', 'P')->count(),
            'F' => $assessment->items->where('result_code', 'F')->count(),
            'R' => $assessment->items->where('result_code', 'R')->count(),
            'OP' => $assessment->items->where('result_code', 'OP')->count(),
            'NR' => $assessment->items->where('result_code', 'NR')->count(),
        ];
        $total = array_sum($counts);

        // overall_result bisa: NORMAL/ABNORMAL/QUESTIONABLE/UNTESTABLE (atau null)
        $overall = $assessment->overall_result ?? '—';
        $overallBadge = match ($assessment->overall_result) {
            'NORMAL' => 'bg-green-100 text-green-700',
            'ABNORMAL' => 'bg-red-100 text-red-700',
            'QUESTIONABLE' => 'bg-yellow-100 text-yellow-700',
            'UNTESTABLE' => 'bg-gray-200 text-gray-700',
            default => 'bg-gray-200 text-gray-700',
        };

        function resultText($code)
        {
            return match ($code) {
                'P' => 'LULUS',
                'F' => 'GAGAL',
                'R' => 'ULANG',
                'OP' => 'BELUM',
                'NR' => 'TIDAK WAJIB',
                default => '—',
            };
        }
        function resultClass($code)
        {
            return match ($code) {
                'P' => 'bg-green-100 text-green-700',
                'F' => 'bg-red-100 text-red-700',
                'R' => 'bg-yellow-100 text-yellow-700',
                'OP' => 'bg-gray-200 text-gray-700',
                'NR' => 'bg-purple-100 text-purple-700',
                default => 'bg-gray-200 text-gray-700',
            };
        }
        function bucketClass($bucket, $passed)
        {
            if ($bucket === 'OVERDUE' && $passed) {
                return 'bg-green-100 text-green-700';
            }
            return match ($bucket) {
                'OVERDUE' => 'bg-red-100 text-red-700',
                'AT_LINE' => 'bg-blue-100 text-blue-700',
                'IN_WINDOW' => 'bg-yellow-100 text-yellow-700',
                default => 'bg-gray-200 text-gray-700',
            };
        }
        function bucketText($bucket, $passed)
        {
            if ($bucket === 'OVERDUE' && $passed) {
                return 'Lewat Usia (Lulus)';
            }
            return match ($bucket) {
                'OVERDUE' => 'Lewat Usia',
                'AT_LINE' => 'Di Garis Usia',
                'IN_WINDOW' => 'Rentang Usia',
                default => 'Belum Waktunya',
            };
        }

        // Kelompokkan parameter untuk tampilan per kategori
        // $parameters: semua parameter aktif (punya relation stimulationCategory)
        // $testedMap: [param_id => ['tested'=>bool,'result_code'=>'P/F/...','is_delay'=>bool,'is_age_line'=>bool]]
        // $bucketMap: [param_id => 'OVERDUE'|'AT_LINE'|'IN_WINDOW'|'NOT_YET']
        $grouped = $parameters->groupBy(
            fn($p) => optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori',
        );
    @endphp

    {{-- Header + Action Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Detail Penilaian MMDST</h1>
            <p class="text-sm text-gray-500">
                {{ $student?->student_name ?? '—' }}
                @if ($student?->student_number)
                    <span class="text-gray-400">({{ $student->student_number }})</span>
                @endif
                • Tgl Penilaian: <b>{{ $dateID ?? '—' }}</b> • Usia: <b>{{ $ageDays }}</b> hari
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($student)
                <a href="{{ route('mmdst.history', $student) }}"
                    class="inline-flex items-center gap-1 border rounded-lg px-3 md:px-4 h-10">
                    <span class="material-symbols-outlined text-base">history</span>
                    <span>Riwayat</span>
                </a>
            @endif
            <a href="{{ route('mmdst-assessments.edit', $assessment) }}"
                class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white rounded-lg px-3 md:px-4 h-10">
                <span class="material-symbols-outlined text-base">edit_square</span>
                <span>Edit</span>
            </a>
            <button type="button" onclick="confirmDelete()"
                class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 md:px-4 h-10">
                <span class="material-symbols-outlined text-base">delete</span>
                <span>Hapus</span>
            </button>
            {{-- (Opsional) Cetak --}}
            {{-- <button type="button" onclick="window.print()" class="inline-flex items-center gap-1 border rounded-lg px-3 md:px-4 h-10">
                <span class="material-symbols-outlined text-base">print</span>
                <span>Cetak</span>
            </button> --}}
        </div>
    </div>

    {{-- Ringkasan Atas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="p-4 bg-white dark:bg-gray-900 rounded-md shadow">
            <div class="text-xs text-gray-500 mb-1">Hasil Keseluruhan</div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-1 rounded text-sm font-semibold {{ $overallBadge }}">{{ $overall }}</span>
            </div>
            <div class="mt-2 text-[11px] text-gray-500">
                Petugas: <b>{{ $creator?->name ?? '—' }}</b>
            </div>
        </div>

        <div class="p-4 bg-white dark:bg-gray-900 rounded-md shadow">
            <div class="text-xs text-gray-500 mb-2">Ringkasan Nilai</div>
            <div class="grid grid-cols-5 gap-2 text-center">
                <div>
                    <div class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">P</div>
                    <div class="mt-1 font-semibold">{{ $counts['P'] }}</div>
                </div>
                <div>
                    <div class="px-2 py-1 rounded text-xs bg-red-100 text-red-700">F</div>
                    <div class="mt-1 font-semibold">{{ $counts['F'] }}</div>
                </div>
                <div>
                    <div class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-700">R</div>
                    <div class="mt-1 font-semibold">{{ $counts['R'] }}</div>
                </div>
                <div>
                    <div class="px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">OP</div>
                    <div class="mt-1 font-semibold">{{ $counts['OP'] }}</div>
                </div>
                <div>
                    <div class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-700">NR</div>
                    <div class="mt-1 font-semibold">{{ $counts['NR'] }}</div>
                </div>
            </div>
            <div class="mt-2 text-[11px] text-gray-500 text-right">Total dinilai: <b>{{ $total }}</b></div>
        </div>

        <div class="p-4 bg-white dark:bg-gray-900 rounded-md shadow">
            <div class="text-xs text-gray-500 mb-2">Info Usia & Garis</div>
            <div class="flex flex-wrap gap-2 text-[11px] md:text-xs">
                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700">Di Garis Usia</span>
                <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">Rentang Usia</span>
                <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">Lewat Usia</span>
                <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700">Belum Waktunya</span>
                <span class="px-2 py-0.5 rounded bg-green-100 text-green-700">Lewat Usia (Lulus)</span>
            </div>
        </div>
    </div>

    {{-- Catatan Utama --}}
    @if (filled($assessment->notes))
        <div class="p-5 bg-white dark:bg-gray-900 rounded-md shadow mb-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-semibold">Catatan Utama</h2>
                <span class="text-xs text-gray-500">{{ $dateID }}</span>
            </div>
            <div class="prose dark:prose-invert max-w-none text-sm">
                {!! nl2br(e($assessment->notes)) !!}
            </div>
        </div>
    @endif

    {{-- Tabel Per Kategori --}}
    <div class="space-y-6">
        @foreach ($grouped as $categoryName => $params)
            @php $slug = \Illuminate\Support\Str::slug($categoryName, '-'); @endphp
            <div class="border dark:border-gray-700 rounded-md overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2 bg-gray-100 dark:bg-gray-800">
                    <div class="font-medium">{{ $categoryName }}</div>
                    <button type="button" class="text-xs flex items-center gap-1"
                        onclick="toggleCat('{{ $slug }}')">
                        <span class="material-symbols-outlined text-sm">unfold_more</span>
                        <span>Tutup/Buka</span>
                    </button>
                </div>

                <div id="cat-{{ $slug }}" class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2 px-3 text-left w-52">Nama Unsur</th>
                                <th class="py-2 px-3 text-left hidden md:table-cell">Deskripsi</th>
                                <th class="py-2 px-3 text-left">Usia (25/50/75/100)</th>
                                <th class="py-2 px-3 text-center">Hasil</th>
                                <th class="py-2 px-3 text-left">Catatan</th>
                                <th class="py-2 px-3 text-left">Tanda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($params as $p)
                                @php
                                    $tested = $testedMap[$p->id]['tested'] ?? false;
                                    $code = $testedMap[$p->id]['result_code'] ?? null;
                                    $passed = $code === 'P';
                                    $note = $assessment->items->firstWhere('mmdst_parameter_id', $p->id)?->note ?? '';

                                    $bucket = $bucketMap[$p->id] ?? 'NOT_YET';
                                    $bClass = bucketClass($bucket, $passed);
                                    $bText = bucketText($bucket, $passed);

                                    $rText = resultText($code);
                                    $rClass = resultClass($code);
                                @endphp
                                @if ($tested)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="py-2 px-3 align-top font-medium">
                                            {{ $p->test_element_name }}
                                            <div class="md:hidden text-[11px] text-gray-500">
                                                {{ \Illuminate\Support\Str::limit($p->test_element_description ?? '-', 80) }}
                                            </div>
                                        </td>
                                        <td
                                            class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                            {{ $p->test_element_description ?? '-' }}
                                        </td>
                                        <td class="py-2 px-3 align-top text-xs text-gray-500">
                                            {{ $p->percent_25 ?? '—' }}/{{ $p->percent_50 ?? '—' }}/{{ $p->percent_75 ?? '—' }}/{{ $p->percent_100 ?? '—' }}
                                        </td>
                                        <td class="py-2 px-3 align-top text-center">
                                            <span
                                                class="px-2 py-0.5 rounded {{ $rClass }}">{{ $rText }}</span>
                                        </td>
                                        <td class="py-2 px-3 align-top">
                                            {{ $note ?: '—' }}
                                        </td>
                                        <td class="py-2 px-3 align-top">
                                            <span
                                                class="px-2 py-0.5 rounded text-[11px] md:text-xs {{ $bClass }}">{{ $bText }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            {{-- Bila dalam kategori tidak ada item yang diuji --}}
                            @if ($params->filter(fn($pp) => ($testedMap[$pp->id]['tested'] ?? false) === true)->count() === 0)
                                <tr>
                                    <td colspan="6" class="py-4 px-3 text-center text-gray-500">
                                        Tidak ada item yang diuji pada kategori ini.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Form delete (hidden) --}}
    <form id="form-delete" action="{{ route('mmdst-assessments.destroy', $assessment) }}" method="POST"
        class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function toggleCat(id) {
            const el = document.getElementById('cat-' + id);
            if (!el) return;
            el.style.display = (el.style.display === 'none') ? '' : 'none';
        }

        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Penilaian?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Hapus',
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
        window.confirmDelete = confirmDelete;
    </script>
</x-app-layout>
