<x-app-layout>
    <x-slot:title>Tambah Penilaian MMDST</x-slot:title>

    {{-- SweetAlert Error --}}
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json($errors - > first())
            });
        });
    </script>
    @endif

    <form id="assessment-form" action="{{ route('mmdst-assessments.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Data Dasar --}}
        <div class="p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <h2 class="font-semibold mb-4">Data Penilaian</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1">Siswa</label>
                    <select name="student_id" id="student_id"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700" required>
                        <option value="">— Pilih Siswa —</option>
                        @foreach ($students as $s)
                        <option value="{{ $s->id }}" @selected(old('student_id')==$s->id)>
                            {{ $s->student_name }} ({{ $s->student_number }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Tanggal Penilaian</label>
                    <input type="date" name="assessment_date"
                        value="{{ old('assessment_date', now()->toDateString()) }}"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700" required>
                </div>
                <div>
                    <label class="block text-sm mb-1">Catatan (opsional)</label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                        class="w-full border rounded-lg px-3 h-10 dark:bg-gray-900 dark:border-gray-700">
                </div>
            </div>
        </div>

        {{-- Parameter Penilaian --}}
        <div class="p-6 bg-white dark:bg-gray-900 rounded-md shadow">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">Parameter MMDST</h2>
                <div class="flex gap-2">
                    <input id="filter-input" type="text" placeholder="Cari parameter..."
                        class="border rounded-lg px-3 h-9 dark:bg-gray-900 dark:border-gray-700">
                </div>
            </div>

            @php
            $grouped = $parameters->groupBy(
            fn($p) => optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori',
            );
            $rowIndex = 0;
            @endphp

            <div class="space-y-6" id="param-container">
                @foreach ($grouped as $categoryName => $params)
                <div class="border dark:border-gray-700 rounded-md">
                    <div
                        class="flex items-center justify-between px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-t-md">
                        <div class="font-medium">{{ $categoryName }}</div>
                        <div class="flex items-center gap-3">
                            <label class="text-xs flex items-center gap-1 cursor-pointer">
                                <input type="checkbox" class="toggle-all-in-cat"
                                    data-target="cat-{{ \Illuminate\Support\Str::slug($categoryName, '-') }}">
                                <span>Pilih semua</span>
                            </label>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="py-2 px-3 text-left">Nilai</th>
                                    <th class="py-2 px-3 text-left">Nama Unsur</th>
                                    <th class="py-2 px-3 text-left">Deskripsi</th>
                                    <th class="py-2 px-3 text-center">P</th>
                                    <th class="py-2 px-3 text-center">F</th>
                                    <th class="py-2 px-3 text-center">R</th>
                                    <th class="py-2 px-3 text-center">OP</th>
                                    <th class="py-2 px-3 text-left">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($params as $param)
                                @php $slugCat = \Illuminate\Support\Str::slug($categoryName,'-'); @endphp
                                <tr class="border-t border-gray-200 dark:border-gray-700 param-row"
                                    data-cat="{{ $slugCat }}"
                                    data-text="{{ \Illuminate\Support\Str::lower($param->test_element_name . ' ' . ($param->test_element_description ?? '')) }}">
                                    <td class="py-2 px-3 align-top">
                                        <input type="checkbox" class="chk-include"
                                            data-row="{{ $rowIndex }}" data-group="{{ $slugCat }}">
                                    </td>
                                    <td class="py-2 px-3 align-top font-medium">{{ $param->test_element_name }}
                                    </td>
                                    <td class="py-2 px-3 align-top text-xs text-gray-600 dark:text-gray-300">
                                        {{ $param->test_element_description ?? '-' }}
                                        <div class="mt-1 text-[10px] text-gray-500">
                                            Usia: 25/50/75/100 =
                                            {{ $param->percent_25 ?? '—' }}/{{ $param->percent_50 ?? '—' }}/{{ $param->percent_75 ?? '—' }}/{{ $param->percent_100 ?? '—' }}
                                        </div>
                                    </td>

                                    {{-- Hidden parameter_id (akan di-enable saat dicentang) --}}
                                    <input type="hidden" name="items[{{ $rowIndex }}][parameter_id]"
                                        value="{{ $param->id }}" disabled>

                                    @foreach (['P', 'F', 'R', 'OP'] as $code)
                                    <td class="py-2 px-3 text-center">
                                        <input type="radio"
                                            name="items[{{ $rowIndex }}][result_code]"
                                            value="{{ $code }}" disabled
                                            class="rc-{{ $slugCat }}">
                                    </td>
                                    @endforeach

                                    <td class="py-2 px-3">
                                        <input type="text" name="items[{{ $rowIndex }}][note]" disabled
                                            class="w-full border rounded px-2 py-1 text-sm dark:bg-gray-900 dark:border-gray-700">
                                    </td>
                                </tr>
                                @php $rowIndex++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-500 mt-2">Tips: Centang kolom “Nilai” untuk item yang ingin dinilai, lalu pilih
                P/F/R/OP.</p>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('mmdst-assessments.index') }}"
                class="border rounded-lg px-4 h-10 flex items-center">Batal</a>
            <button type="button" onclick="confirmSubmit()"
                class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 h-10">Simpan</button>
        </div>
    </form>

    <script>
        // Enable/disable inputs berdasarkan checkbox "Nilai"
        document.querySelectorAll('.chk-include').forEach(chk => {
            chk.addEventListener('change', () => {
                const rowIdx = chk.dataset.row;
                const row = chk.closest('tr');
                row.querySelectorAll(
                        `input[name="items[${rowIdx}][parameter_id]"], input[name="items[${rowIdx}][result_code]"], input[name="items[${rowIdx}][note]"]`
                    )
                    .forEach(el => el.disabled = !chk.checked);
            });
        });

        // Toggle semua di kategori
        document.querySelectorAll('.toggle-all-in-cat').forEach(tg => {
            tg.addEventListener('change', () => {
                const cat = tg.dataset.target;
                document.querySelectorAll(`tr[data-cat="${cat}"] .chk-include`).forEach(chk => {
                    chk.checked = tg.checked;
                    chk.dispatchEvent(new Event('change'));
                });
            });
        });

        // Filter pencarian di tabel
        const filterInput = document.getElementById('filter-input');
        if (filterInput) {
            filterInput.addEventListener('input', () => {
                const q = filterInput.value.toLowerCase();
                document.querySelectorAll('.param-row').forEach(row => {
                    const txt = row.getAttribute('data-text') || '';
                    row.style.display = txt.includes(q) ? '' : 'none';
                });
            });
        }

        // Konfirmasi submit + loading
        function confirmSubmit() {
            // Cek minimal 1 item dipilih
            const anyChecked = Array.from(document.querySelectorAll('.chk-include')).some(c => c.checked);
            if (!anyChecked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada item',
                    text: 'Silakan pilih minimal 1 item untuk dinilai.'
                });
                return;
            }
            Swal.fire({
                title: 'Simpan Penilaian?',
                text: 'Pastikan data sudah benar.',
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