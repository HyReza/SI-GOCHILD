<x-app-layout>
    <x-slot:title>Detail Laporan MMDST</x-slot:title>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    @php
        // Aman-null untuk header
        $student = $assessment->student; // bisa null
        $hasStudent = (bool) $student;
        $studentName = $student->student_name ?? '(Siswa tidak ditemukan)';
        $dateLabel = $assessment->assessment_date?->format('d M Y') ?? '—';

        // Peta label hasil keseluruhan
        $overallMap = [
            'NORMAL' => ['label' => 'NORMAL', 'cls' => 'bg-green-100 text-green-700'],
            'ABNORMAL' => ['label' => 'ABNORMAL', 'cls' => 'bg-red-100 text-red-700'],
            'QUESTIONABLE' => ['label' => 'MERAGUKAN', 'cls' => 'bg-yellow-100 text-yellow-700'],
            'UNTESTABLE' => ['label' => 'TIDAK DINILAI', 'cls' => 'bg-gray-200 text-gray-700'],
        ];
        $resKey = $assessment->overall_result ?? '';
        $oLabel = $overallMap[$resKey]['label'] ?? '—';
        $oCls = $overallMap[$resKey]['cls'] ?? 'bg-gray-200 text-gray-700';

        // Peta label bucket usia
        $bucketTextId = [
            'OVERDUE' => 'Lewat Usia',
            'AT_LINE' => 'Di Garis Usia',
            'IN_WINDOW' => 'Rentang Usia',
            'NOT_YET' => 'Belum Waktunya',
        ];
    @endphp

    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-lg font-semibold">Detail — {{ $studentName }}</h1>
            <p class="text-sm text-gray-500">
                Tanggal: {{ $dateLabel }} •
                Usia: {{ $assessment->age_in_days ?? '—' }} hari •
                Dibuat oleh: {{ optional($assessment->creator)->user_name ?? '-' }}
            </p>

            @unless ($hasStudent)
                <div class="mt-1 text-xs px-3 py-2 rounded bg-yellow-50 text-yellow-700 border border-yellow-200">
                    Data siswa tidak tersedia (mungkin sudah dihapus). Beberapa tautan akan dinonaktifkan.
                </div>
            @endunless
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($hasStudent)
                <a href="{{ route('mmdst.history', $assessment->student) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 md:px-4 h-10 text-sm font-medium border border-gray-300 dark:border-gray-700
                          hover:bg-gray-50 dark:hover:bg-gray-800 transition shadow-sm hover:shadow
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <span class="material-symbols-outlined text-base">history</span>
                    <span>Riwayat</span>
                </a>
            @else
                <button type="button" disabled
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 md:px-4 h-10 text-sm font-medium border border-gray-300 opacity-60 cursor-not-allowed">
                    <span class="material-symbols-outlined text-base">history</span>
                    <span>Riwayat</span>
                </button>
            @endif

            <a href="{{ route('mmdst-assessments.edit', ['mmdst_assessment' => $assessment->id]) }}"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 md:px-4 h-10 text-sm font-medium
                      bg-green-600 hover:bg-green-700 text-white transition shadow-sm hover:shadow
                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                <span class="material-symbols-outlined text-base">edit_square</span>
                <span>Edit</span>
            </a>

            <form action="{{ route('mmdst-assessments.destroy', ['mmdst_assessment' => $assessment->id]) }}"
                method="POST">
                @csrf @method('DELETE')
                <button type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 md:px-4 h-10 text-sm font-medium
                               bg-red-600 hover:bg-red-700 text-white transition shadow-sm hover:shadow
                               focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                    onclick="confirmDelete(this)">
                    <span class="material-symbols-outlined text-base">delete</span>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Hasil keseluruhan --}}
    <div class="grid md:grid-cols-2 gap-6">
        <div class="p-4 md:p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <h2 class="font-semibold mb-2">Hasil Keseluruhan</h2>
            <span class="px-3 py-2 rounded text-sm font-semibold {{ $oCls }}">{{ $oLabel }}</span>

            @if ($assessment->counters)
                <div class="mt-3 text-xs text-gray-600 dark:text-gray-300">
                    Total item: {{ $assessment->counters['total_items'] ?? 0 }},
                    Tertinggal (Delay): {{ $assessment->counters['total_delay'] ?? 0 }},
                    Menolak (Refusal): {{ $assessment->counters['total_refusal'] ?? 0 }},
                    Lulus di Garis Usia: {{ $assessment->counters['total_pass_age'] ?? 0 }}
                </div>
            @endif

            <div class="mt-3 text-sm">
                <span class="text-gray-500">Catatan:</span>
                {{ $assessment->notes ?? '—' }}
            </div>
        </div>

        {{-- Keterangan (Legend) --}}
        <div class="p-4 md:p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <h2 class="font-semibold mb-2">Keterangan</h2>
            <div class="flex flex-col gap-2 text-[11px] md:text-xs">
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <span class="font-medium">Hasil Tes:</span>
                    <span class="px-2 py-0.5 rounded bg-green-100 text-green-700">LULUS (P)</span>
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">GAGAL (F)</span>
                    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">ULANG (R)</span>
                    <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700">BELUM (OP)</span>
                </div>
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <span class="font-medium">Status Usia:</span>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700">Di Garis Usia</span>
                    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">Rentang Usia</span>
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">Lewat Usia</span>
                    <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700">Belum Waktunya</span>
                </div>
                <p class="text-gray-500">
                    Catatan: Jika <b>Lewat Usia</b> namun hasil <b>LULUS</b>, dianggap <b>sesuai/normal</b> —
                    akan ditampilkan sebagai <b>Lewat Usia (Lulus)</b> berwarna hijau.
                </p>
            </div>
        </div>
    </div>

    {{-- Ringkasan sektor --}}
    <div class="mt-6 p-4 md:p-6 bg-white dark:bg-gray-900 rounded-md shadow">
        <h2 class="font-semibold mb-3">Ringkasan per Sektor</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-xs md:text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="py-2 px-3 text-left">Sektor</th>
                        <th class="py-2 px-3 text-center">Total</th>
                        <th class="py-2 px-3 text-center">Tertinggal</th>
                        <th class="py-2 px-3 text-center">Menolak</th>
                        <th class="py-2 px-3 text-center">Lulus @ Garis Usia</th>
                        <th class="py-2 px-3 text-left">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assessment->sectorSummaries as $s)
                        @php
                            $sectorMap = [
                                'NORMAL' => ['label' => 'NORMAL', 'cls' => 'bg-green-100 text-green-700'],
                                'ABNORMAL' => ['label' => 'ABNORMAL', 'cls' => 'bg-red-100 text-red-700'],
                                'QUESTIONABLE' => ['label' => 'MERAGUKAN', 'cls' => 'bg-yellow-100 text-yellow-700'],
                                'UNTESTABLE' => ['label' => 'TIDAK DINILAI', 'cls' => 'bg-gray-200 text-gray-700'],
                            ];
                            $sLabel = $sectorMap[$s->sector_result]['label'] ?? '—';
                            $sCls = $sectorMap[$s->sector_result]['cls'] ?? 'bg-gray-200 text-gray-700';
                        @endphp
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <td class="py-2 px-3">{{ $s->category->category_parameter_name ?? '-' }}</td>
                            <td class="py-2 px-3 text-center">{{ $s->total_items }}</td>
                            <td class="py-2 px-3 text-center">{{ $s->delays_count }}</td>
                            <td class="py-2 px-3 text-center">{{ $s->refusals_count }}</td>
                            <td class="py-2 px-3 text-center">{{ $s->pass_at_age_line_count }}</td>
                            <td class="py-2 px-3">
                                <span
                                    class="px-2 py-1 rounded text-xs font-semibold {{ $sCls }}">{{ $sLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Tidak ada ringkasan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail item --}}
    <div class="mt-6 p-4 md:p-6 bg-white dark:bg-gray-900 rounded-md shadow">
        <h2 class="font-semibold mb-3">Detail Item</h2>

        @php
            $all = $parameters->groupBy(
                fn($p) => optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori',
            );
        @endphp

        @foreach ($all as $cat => $params)
            <div class="mb-5 border dark:border-gray-800 rounded-md">
                <div class="px-3 md:px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-md font-medium">
                    {{ $cat }}</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-xs md:text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2 px-3 text-left">Unsur</th>
                                <th class="py-2 px-3 text-left hidden md:table-cell">Deskripsi</th>
                                <th class="py-2 px-3 text-center">Hasil</th>
                                <th class="py-2 px-3 text-center">Tertinggal (Delay)</th>
                                <th class="py-2 px-3 text-center">Di Garis Usia</th>
                                <th class="py-2 px-3 text-left">Status Usia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($params as $p)
                                @php
                                    $it = $testedMap[$p->id] ?? null;
                                    $code = $it['result_code'] ?? null; // P/F/R/OP
                                    $isDelay = (bool) ($it['is_delay'] ?? false);
                                    $isAge = (bool) ($it['is_age_line'] ?? false);
                                    $b = $bucketMap[$p->id] ?? 'NOT_YET';

                                    $resLabel = match ($code) {
                                        'P' => 'LULUS (P)',
                                        'F' => 'GAGAL (F)',
                                        'R' => 'ULANG (R)',
                                        'OP' => 'BELUM (OP)',
                                        null => '—',
                                        default => $code,
                                    };
                                    $resCls = match ($code) {
                                        'F' => 'bg-red-100 text-red-700',
                                        'R' => 'bg-yellow-100 text-yellow-700',
                                        'OP' => 'bg-gray-200 text-gray-700',
                                        'P' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-200 text-gray-700',
                                    };

                                    $bCls = match ($b) {
                                        'OVERDUE' => 'bg-red-100 text-red-700',
                                        'AT_LINE' => 'bg-blue-100 text-blue-700',
                                        'IN_WINDOW' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-gray-200 text-gray-700',
                                    };
                                    $bText = $bucketTextId[$b] ?? $b;

                                    // Lewat usia tapi lulus → tampil hijau
                                    if ($b === 'OVERDUE' && $code === 'P') {
                                        $bCls = 'bg-green-100 text-green-700';
                                        $bText = 'Lewat Usia (Lulus)';
                                    }
                                @endphp

                                <tr class="border-t border-gray-200 dark:border-gray-800">
                                    <td class="py-2 px-3 align-top">
                                        <div class="space-y-1">
                                            <div class="font-medium">{{ $p->test_element_name }}</div>
                                            @if ($p->test_element_description)
                                                <div class="md:hidden text-[11px] text-gray-500">
                                                    {{ \Illuminate\Support\Str::limit($p->test_element_description, 90) }}
                                                </div>
                                            @endif
                                            <div class="text-[10px] text-gray-500">
                                                25/50/75/100:
                                                {{ $p->percent_25 ?? '—' }}/{{ $p->percent_50 ?? '—' }}/{{ $p->percent_75 ?? '—' }}/{{ $p->percent_100 ?? '—' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-3 text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                        {{ $p->test_element_description ?? '—' }}
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <span
                                            class="px-2 py-0.5 rounded text-xs font-semibold {{ $resCls }}">{{ $resLabel }}</span>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        {!! $isDelay ? '<span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">Ya</span>' : '—' !!}
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        {!! $isAge ? '<span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700">Ya</span>' : '—' !!}
                                    </td>
                                    <td class="py-2 px-3">
                                        <span
                                            class="px-2 py-0.5 rounded text-xs {{ $bCls }}">{{ $bText }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function confirmDelete(btn) {
            Swal.fire({
                title: 'Hapus laporan?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(res => {
                if (res.isConfirmed) btn.closest('form').submit();
            });
        }
    </script>
</x-app-layout>
