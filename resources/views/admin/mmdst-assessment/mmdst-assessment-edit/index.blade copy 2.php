<x-app-layout>
    <x-slot:title>Edit Laporan MMDST</x-slot:title>

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
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json($errors->first())
                });
            });
        </script>
    @endif

    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-lg font-semibold">Edit Laporan — {{ $mmdst_assessment->student->student_name }}</h1>
            <p class="text-sm text-gray-500">
                Tanggal: {{ $mmdst_assessment->assessment_date->format('d M Y') }} •
                Usia: {{ $ageInDays }} hari • Status: {{ $studentIsNormal ? 'Normal' : 'Kebutuhan Khusus' }}
            </p>
        </div>

        {{-- Tombol aksi --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('mmdst.history', $mmdst_assessment->student) }}"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 md:px-4 h-10 text-sm font-medium
                      border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800
                      transition shadow-sm hover:shadow focus:outline-none
                      focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                <span class="material-symbols-outlined text-base">history</span>
                <span>Riwayat</span>
            </a>

            <a href="{{ route('mmdst-assessments.show', $mmdst_assessment) }}"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 md:px-4 h-10 text-sm font-medium
                      bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-sm hover:shadow
                      focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                <span class="material-symbols-outlined text-base">visibility</span>
                <span>Detail</span>
            </a>
        </div>
    </div>

    <form id="assessment-form" action="{{ route('mmdst-assessments.update', $mmdst_assessment) }}" method="POST"
        class="space-y-6">
        @csrf @method('PUT')

        <div class="p-4 md:p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <div class="grid md:grid-cols-3 gap-3 md:gap-4">
                <div>
                    <label class="block text-sm mb-1">Tanggal Penilaian</label>
                    <input type="date" name="assessment_date"
                        value="{{ old('assessment_date', $mmdst_assessment->assessment_date->toDateString()) }}"
                        class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1">Catatan</label>
                    <input type="text" name="notes" value="{{ old('notes', $mmdst_assessment->notes) }}"
                        class="w-full h-10 border rounded-lg px-3 dark:bg-gray-900 dark:border-gray-700">
                </div>
            </div>
        </div>

        {{-- KETERANGAN / LEGENDA --}}
        <div class="p-4 bg-white dark:bg-gray-900 rounded-md shadow text-[11px] md:text-xs">
            <div class="space-y-2">
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <span class="font-medium">Hasil Tes:</span>
                    <span class="px-2 py-0.5 rounded bg-green-100 text-green-700">LULUS (P)</span>
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">GAGAL (F)</span>
                    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">ULANG (R)</span>
                    <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700">BELUM (OP)</span>
                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700">TIDAK WAJIB (NR)</span>
                </div>
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <span class="font-medium">Status Usia:</span>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700">Di Garis Usia</span>
                    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">Rentang Usia</span>
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">Lewat Usia</span>
                    <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700">Belum Waktunya</span>
                </div>
                <ul class="list-disc pl-5 text-gray-600">
                    <li><b>NR (Not Required / Tidak Wajib Diujikan)</b>: digunakan untuk parameter yang tidak perlu
                        diujikan pada sesi ini (misal karena konteks klinis, alat tidak tersedia, atau kebijakan
                        penguji).</li>
                    <li>Jika <b>Lewat Usia</b> namun hasil <b>LULUS (P)</b>, dianggap <b>sesuai/normal</b> dan ditandai
                        hijau (<i>Lewat Usia (Lulus)</i>).</li>
                </ul>
            </div>
        </div>

        @php
            $rowIndex = 0;
            function bucketOf($age, $p25, $p100)
            {
                if (is_null($p25) || is_null($p100)) {
                    return 'NOT_YET';
                }
                if ($age < $p25) {
                    return 'NOT_YET';
                }
                if ($age > $p100) {
                    return 'OVERDUE';
                }
                // Di garis usia jika tepat di 25 atau 100
                if ($age == $p25 || $age == $p100) {
                    return 'AT_LINE';
                }
                return 'IN_WINDOW';
            }
        @endphp

        <div class="space-y-6">
            @foreach ($filtered_categories as $categoryName => $params)
                @php $slug = \Illuminate\Support\Str::slug($categoryName, '-'); @endphp

                <div class="bg-white dark:bg-gray-900 rounded-md shadow border dark:border-gray-800">
                    <div
                        class="px-3 md:px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-md flex items-center justify-between">
                        <div class="font-medium text-sm md:text-base">{{ $categoryName }}</div>
                        <label class="text-[11px] md:text-xs flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" class="toggle-all-in-cat" data-target="cat-{{ $slug }}">
                            <span>Pilih semua</span>
                        </label>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-xs md:text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="py-2 px-3 text-left w-16">Nilai</th>
                                    <th class="py-2 px-3 text-left">Unsur</th>
                                    <th class="py-2 px-3 text-left hidden md:table-cell">Deskripsi</th>
                                    <th class="py-2 px-3 text-center">P</th>
                                    <th class="py-2 px-3 text-center">F</th>
                                    <th class="py-2 px-3 text-center">R</th>
                                    <th class="py-2 px-3 text-center">OP</th>
                                    <th class="py-2 px-3 text-center">NR</th>
                                    <th class="py-2 px-3 text-left w-48">Catatan</th>
                                    <th class="py-2 px-3 text-left">Tanda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($params as $p)
                                    @php
                                        $exist = $existing->get($p->id);
                                        $checked = (bool) $exist;
                                        $code = $exist->result_code ?? null;
                                        $note = $exist->note ?? '';

                                        $bucket = bucketOf($ageInDays, $p->percent_25, $p->percent_100);
                                        $bucketBadge = match ($bucket) {
                                            'OVERDUE' => 'bg-red-100 text-red-700',
                                            'AT_LINE' => 'bg-blue-100 text-blue-700',
                                            'IN_WINDOW' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-200 text-gray-700',
                                        };
                                        // Jika lewat usia tapi lulus → hijau
                                        if ($bucket === 'OVERDUE' && $code === 'P') {
                                            $bucketBadge = 'bg-green-100 text-green-700';
                                        }
                                        $bucketText = match ($bucket) {
                                            'OVERDUE' => $code === 'P' ? 'Lewat Usia (Lulus)' : 'Lewat Usia',
                                            'AT_LINE' => 'Di Garis Usia',
                                            'IN_WINDOW' => 'Rentang Usia',
                                            default => 'Belum Waktunya',
                                        };

                                        $resultLabel = match ($code) {
                                            'P' => 'LULUS',
                                            'F' => 'GAGAL',
                                            'R' => 'ULANG',
                                            'OP' => 'BELUM',
                                            'NR' => 'TIDAK WAJIB',
                                            default => 'BELUM',
                                        };
                                        $resultBadge = match ($resultLabel) {
                                            'LULUS' => 'bg-green-100 text-green-700',
                                            'GAGAL' => 'bg-red-100 text-red-700',
                                            'ULANG' => 'bg-yellow-100 text-yellow-700',
                                            'TIDAK WAJIB' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-gray-200 text-gray-700',
                                        };
                                    @endphp

                                    <tr class="border-t border-gray-200 dark:border-gray-800 param-row"
                                        data-cat="cat-{{ $slug }}">
                                        <td class="py-2 px-3 align-top">
                                            <input type="checkbox" class="chk-include" data-row="{{ $rowIndex }}"
                                                @checked($checked)>
                                        </td>

                                        <td class="py-2 px-3 align-top font-medium">
                                            <div class="space-y-1">
                                                <div>{{ $p->test_element_name }}</div>
                                                @if ($p->test_element_description)
                                                    <div class="md:hidden text-[11px] text-gray-500">
                                                        {{ \Illuminate\Support\Str::limit($p->test_element_description, 80) }}
                                                    </div>
                                                @endif
                                                <div class="text-[10px] text-gray-500">
                                                    25/50/75/100:
                                                    {{ $p->percent_25 ?? '—' }}/{{ $p->percent_50 ?? '—' }}/{{ $p->percent_75 ?? '—' }}/{{ $p->percent_100 ?? '—' }}
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                            {{ $p->test_element_description ?? '-' }}
                                        </td>

                                        {{-- Hidden id param (aktif jika dicentang) --}}
                                        <input type="hidden" name="items[{{ $rowIndex }}][parameter_id]"
                                            value="{{ $p->id }}" {{ $checked ? '' : 'disabled' }}>

                                        {{-- Radio hasil: P, F, R, OP, NR --}}
                                        @foreach (['P', 'F', 'R', 'OP', 'NR'] as $opt)
                                            <td class="py-2 px-3 text-center">
                                                <input type="radio" name="items[{{ $rowIndex }}][result_code]"
                                                    value="{{ $opt }}" {{ $checked ? '' : 'disabled' }}
                                                    @checked($code === $opt)>
                                            </td>
                                        @endforeach

                                        {{-- Notes --}}
                                        <td class="py-2 px-3">
                                            <input type="text" name="items[{{ $rowIndex }}][note]"
                                                value="{{ $note }}" {{ $checked ? '' : 'disabled' }}
                                                class="w-full border rounded px-2 py-1 text-xs md:text-sm dark:bg-gray-900 dark:border-gray-700">
                                        </td>

                                        {{-- Badges --}}
                                        <td class="py-2 px-3 text-[11px] md:text-xs">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    class="px-2 py-0.5 rounded {{ $bucketBadge }}">{{ $bucketText }}</span>
                                                <span
                                                    class="px-2 py-0.5 rounded {{ $resultBadge }}">{{ $resultLabel }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $rowIndex++; @endphp
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-4 px-3 text-center text-gray-500">
                                            Tidak ada item yang relevan untuk usia ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tombol tindakan bawah --}}
        <div class="flex flex-col sm:flex-row justify-end gap-2">
            <a href="{{ route('mmdst.history', $mmdst_assessment->student) }}"
                class="inline-flex items-center justify-center gap-1.5 border rounded-lg px-3 md:px-4 h-10 text-sm font-medium
                      border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition shadow-sm hover:shadow
                      focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                <span class="material-symbols-outlined text-base">close</span>
                <span>Batal</span>
            </a>

            <button type="button" onclick="confirmSubmit()"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 md:px-4 h-10 text-sm font-medium
                      bg-blue-600 hover:bg-blue-700 text-white transition shadow-sm hover:shadow
                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                <span class="material-symbols-outlined text-base">save</span>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>

    <script>
        // toggle per baris
        document.querySelectorAll('.chk-include').forEach(chk => {
            const rowIdx = chk.dataset.row;
            const row = chk.closest('tr');
            const toggle = () => {
                row.querySelectorAll(
                    `input[name="items[${rowIdx}][parameter_id]"], input[name="items[${rowIdx}][result_code]"], input[name="items[${rowIdx}][note]"]`
                ).forEach(el => el.disabled = !chk.checked);
            };
            toggle();
            chk.addEventListener('change', toggle);
        });

        // pilih semua di kategori
        document.querySelectorAll('.toggle-all-in-cat').forEach(tg => {
            tg.addEventListener('change', () => {
                const cat = tg.dataset.target;
                document.querySelectorAll(`tr[data-cat="${cat}"] .chk-include`).forEach(chk => {
                    chk.checked = tg.checked;
                    chk.dispatchEvent(new Event('change'));
                });
            });
        });

        // konfirmasi simpan
        function confirmSubmit() {
            const checked = Array.from(document.querySelectorAll('.chk-include')).some(c => c.checked);
            if (!checked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada item',
                    text: 'Pilih minimal 1 item.'
                });
                return;
            }
            Swal.fire({
                title: 'Simpan perubahan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('assessment-form').submit();
                }
            });
        }
    </script>
</x-app-layout>
