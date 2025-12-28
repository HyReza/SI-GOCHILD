<x-app-layout>
    <x-slot:title>Manajemen Growth Standards</x-slot:title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal && Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        </script>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal && Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json($errors->first()),
                    showConfirmButton: true
                });
            });
        </script>
    @endif

    {{-- Header Aksi + Filter --}}
    <div
        class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white/50 dark:bg-slate-900/50 p-4 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">

        {{-- Kelompok Tombol Aksi (Kiri) --}}
        <div class="flex flex-wrap gap-2 order-2 lg:order-1">
            <button onclick="openModalCreate()"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 h-11 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                <span>Tambah Data</span>
                <span class="material-symbols-outlined text-sm">add_circle</span>
            </button>

            <button onclick="openImportModal()"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 h-11 bg-amber-500 hover:bg-amber-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                <span>Import</span>
                <span class="material-symbols-outlined text-sm">upload_file</span>
            </button>

            <a href="{{ route('growth-standards.index') }}"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 h-11 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                <span class="material-symbols-outlined text-sm">filter_alt_off</span>
            </a>
        </div>

        {{-- Kelompok Form Filter (Kanan) --}}
        <form method="GET" action="{{ route('growth-standards.index') }}"
            class="flex-1 grid grid-cols-2 md:flex md:flex-wrap lg:justify-end gap-2 order-1 lg:order- order-2 w-full">

            <div class="relative flex-1 md:min-w-[140px]">
                <select name="parameter"
                    class="w-full h-11 pl-4 pr-10 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 border appearance-none cursor-pointer transition-all">
                    <option value="">Semua Parameter</option>
                    @foreach (['BB/U', 'TB/U', 'PB/U', 'IMT/U', 'PB/BB', 'TB/BB'] as $p)
                        <option value="{{ $p }}" {{ request('parameter') === $p ? 'selected' : '' }}>
                            {{ $p }}</option>
                    @endforeach
                </select>
                <span
                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-sm">expand_more</span>
            </div>

            <div class="relative flex-1 md:min-w-[120px]">
                <select name="gender"
                    class="w-full h-11 pl-4 pr-10 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 border appearance-none cursor-pointer transition-all">
                    <option value="">Semua Gender</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
                <span
                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-sm">expand_more</span>
            </div>

            <div class="relative flex-1 md:min-w-[140px] col-span-2 md:col-span-1">
                <select name="reference_type"
                    class="w-full h-11 pl-4 pr-10 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 border appearance-none cursor-pointer transition-all">
                    <option value="">Semua Referensi</option>
                    <option value="age" {{ request('reference_type') === 'age' ? 'selected' : '' }}>Age (Umur)
                    </option>
                    <option value="length" {{ request('reference_type') === 'length' ? 'selected' : '' }}>Length
                        (Panjang)</option>
                    <option value="height" {{ request('reference_type') === 'height' ? 'selected' : '' }}>Height
                        (Tinggi)</option>
                </select>
                <span
                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-sm">expand_more</span>
            </div>

            <button type="submit"
                class="h-11 px-6 bg-slate-900 dark:bg-emerald-500 text-white dark:text-slate-900 text-xs font-black uppercase tracking-[0.2em] rounded-xl hover:scale-105 active:scale-95 transition-all shadow-lg col-span-2 md:col-span-1">
                Terapkan
            </button>
        </form>
    </div>

    {{-- Tabel Data --}}
    <div
        class="my-6 p-6 md:p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse mb-4">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm">
                    <tr>
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Parameter</th>
                        <th class="py-3 px-6 text-left">Jenis Kelamin</th>
                        <th class="py-3 px-6 text-left">Tipe Referensi</th>
                        <th class="py-3 px-6 text-left">Referensi</th>
                        <th class="py-3 px-6 text-left">Kondisi</th>
                        <th class="py-3 px-6 text-left">Satuan</th>
                        <th class="py-3 px-6 text-left">-3 SD</th>
                        <th class="py-3 px-6 text-left">-2 SD</th>
                        <th class="py-3 px-6 text-left">-1 SD</th>
                        <th class="py-3 px-6 text-left">Median</th>
                        <th class="py-3 px-6 text-left">+1 SD</th>
                        <th class="py-3 px-6 text-left">+2 SD</th>
                        <th class="py-3 px-6 text-left">+3 SD</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 dark:text-gray-300 text-sm">
                    @forelse ($rows as $row)
                        @php
                            // Satuan otomatis berdasar parameter Excel
                            $unit = (str_contains($row->parameter, 'IMT')
                                    ? 'kg/m²'
                                    : (str_contains($row->parameter, 'BB') && !str_contains($row->parameter, '/U')) ||
                                        str_starts_with($row->parameter, 'BB'))
                                ? 'kg'
                                : 'cm';
                            // Referensi tampil
                            $refStr =
                                $row->reference_type === 'age'
                                    ? $row->age_months . ' bln'
                                    : ($row->reference_type === 'length'
                                        ? number_format($row->body_length, 2) . ' cm'
                                        : number_format($row->body_height, 2) . ' cm');
                        @endphp
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800/60">
                            <td class="py-3 px-6 whitespace-nowrap">
                                {{ ($rows->currentPage() - 1) * $rows->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3 px-6 font-medium">{{ $row->parameter }}</td>
                            <td class="py-3 px-6 capitalize">{{ $row->gender }}</td>
                            <td class="py-3 px-6">{{ $row->reference_type }}</td>
                            <td class="py-3 px-6">{{ $refStr }}</td>
                            <td class="py-3 px-6">{{ $row->measurement_condition ?: '-' }}</td>
                            <td class="py-3 px-6">{{ $unit }}</td>
                            <td class="py-3 px-6">{{ $row->minus_3_sd }}</td>
                            <td class="py-3 px-6">{{ $row->minus_2_sd }}</td>
                            <td class="py-3 px-6">{{ $row->minus_1_sd }}</td>
                            <td class="py-3 px-6 font-semibold">{{ $row->median }}</td>
                            <td class="py-3 px-6">{{ $row->plus_1_sd }}</td>
                            <td class="py-3 px-6">{{ $row->plus_2_sd }}</td>
                            <td class="py-3 px-6">{{ $row->plus_3_sd }}</td>
                            <td class="py-3 px-6 text-center">
                                <form action="{{ route('growth-standards.toggle-active', $row->id) }}" method="POST"
                                    class="inline toggle-active-form">
                                    @csrf
                                    <button type="submit"
                                        class="px-2 py-1 rounded {{ $row->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                        {{ $row->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex gap-2 justify-center">
                                    <button type="button" class="relative group btn-edit"
                                        data-row='@json($row)'>
                                        <span
                                            class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base">edit_square</span>
                                        <span
                                            class="z-50 absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Edit Data
                                        </span>
                                    </button>

                                    <form id="delete-form-{{ $row->id }}"
                                        action="{{ route('growth-standards.destroy', $row->id) }}" method="POST"
                                        class="relative group delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base">
                                            delete
                                        </button>
                                        <span
                                            class="z-50 absolute right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Hapus Data
                                        </span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data growth standards.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $rows->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- Modal Tambah / Edit --}}
    <div id="form-modal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg max-w-3xl w-full p-6 md:p-8 relative m-4">
            <button onclick="closeModal()"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>

            <h2 id="modal-title" class="text-lg font-semibold mb-4">Tambah Growth Standard</h2>

            <form id="main-form" method="POST" action="{{ route('growth-standards.store') }}">
                @csrf
                <div id="method-field" class="mb-2"></div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Jenis Kelamin</label>
                        <select id="gender" name="gender"
                            class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                            <option value="male">male</option>
                            <option value="female">female</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Parameter (sesuai Excel)</label>
                        <select id="parameter" name="parameter"
                            class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                            @foreach (['BB/U', 'TB/U', 'PB/U', 'IMT/U', 'PB/BB', 'TB/BB'] as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Tipe Referensi</label>
                        <select id="reference_type" name="reference_type"
                            class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                            {{-- opsi akan diisi otomatis sesuai parameter --}}
                        </select>
                        <p class="text-xs text-gray-500 mt-1" id="ref_hint">
                            BB/U, TB/U, PB/U &amp; IMT/U: age (umur/bulan) • PB/BB: length (panjang) • TB/BB: height
                            (tinggi)
                        </p>
                    </div>

                    {{-- Field referensi dinamis --}}
                    <div id="field_age" class="">
                        <label class="block text-sm mb-1">Umur (bulan)</label>
                        <input id="age_months" name="age_months" type="number" min="0" max="216"
                            class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div id="field_length" class="hidden">
                        <label class="block text-sm mb-1">Panjang Badan (cm)</label>
                        <input id="body_length" name="body_length" type="number" step="0.01"
                            class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div id="field_height" class="hidden">
                        <label class="block text-sm mb-1">Tinggi Badan (cm)</label>
                        <input id="body_height" name="body_height" type="number" step="0.01"
                            class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    @php
                        $sdFields = [
                            'minus_3_sd' => '-3 SD',
                            'minus_2_sd' => '-2 SD',
                            'minus_1_sd' => '-1 SD',
                            'median' => 'Median',
                            'plus_1_sd' => '+1 SD',
                            'plus_2_sd' => '+2 SD',
                            'plus_3_sd' => '+3 SD',
                        ];
                    @endphp
                    @foreach ($sdFields as $name => $label)
                        <div>
                            <label class="block text-sm mb-1">{{ $label }}</label>
                            <input id="{{ $name }}" name="{{ $name }}" type="number"
                                step="0.01" required
                                class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    @endforeach

                    <div class="md:col-span-2">
                        <label class="block text-sm mb-1">Kondisi Pengukuran (opsional)</label>
                        <select id="measurement_condition" name="measurement_condition"
                            class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">(Kosongkan)</option>
                            <option value="terlentang">terlentang</option>
                            <option value="berdiri">berdiri</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-center gap-2 mt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded"
                            checked>
                        <label for="is_active" class="text-sm">Aktif?</label>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-md border dark:border-gray-600">Batal</button>
                    <button type="button" id="btn-submit-main"
                        class="px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Import Excel --}}
    <div id="import-modal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg p-6 md:p-8 max-w-lg w-full relative m-4">
            <button onclick="closeImportModal()"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Import Growth Standards (Excel/CSV)</h3>
            <div class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                Header yang diterima (sesuai Excel PMK):<br>
                <code>gender, reference_type, age_months, body_length, body_height, minus_3_sd, minus_2_sd, minus_1_sd,
                    median, plus_1_sd, plus_2_sd, plus_3_sd, parameter, measurement_condition, is_active</code>
            </div>
            <form id="import-form" method="POST" action="{{ route('growth-standards.import') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv"
                    class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-4"
                    required>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="closeImportModal()"
                        class="px-4 py-2 rounded-md border dark:border-gray-600">Batal</button>
                    <button type="button" id="btn-submit-import"
                        class="px-4 py-2 rounded-md bg-amber-500 hover:bg-amber-600 text-white">Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ===== Modal helpers =====
        function openModalCreate() {
            const m = document.getElementById('form-modal');
            document.getElementById('modal-title').textContent = 'Tambah Growth Standard';
            const form = document.getElementById('main-form');
            form.action = @json(route('growth-standards.store'));
            document.getElementById('method-field').innerHTML = '';
            form.reset();
            document.getElementById('parameter').value = 'BB/U';
            syncRefTypeByParameter(); // isi reference_type sesuai parameter
            toggleRefFields();
            document.getElementById('is_active').checked = true;
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModal() {
            const m = document.getElementById('form-modal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        function openImportModal() {
            const m = document.getElementById('import-modal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeImportModal() {
            const m = document.getElementById('import-modal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeImportModal();
            }
        });

        // ===== Field referensi dinamis
        const refType = document.getElementById('reference_type');
        const fAge = document.getElementById('field_age');
        const fLen = document.getElementById('field_length');
        const fHei = document.getElementById('field_height');

        function toggleRefFields() {
            fAge.classList.add('hidden');
            fLen.classList.add('hidden');
            fHei.classList.add('hidden');
            if (refType.value === 'age') fAge.classList.remove('hidden');
            if (refType.value === 'length') fLen.classList.remove('hidden');
            if (refType.value === 'height') fHei.classList.remove('hidden');
        }
        refType.addEventListener('change', toggleRefFields);

        // ===== Binding Parameter ↔ Reference Type (sesuai Excel)
        const paramSelect = document.getElementById('parameter');

        function allowedRefTypesFor(param) {
            switch (param) {
                case 'BB/U': // berat menurut umur
                case 'TB/U': // tinggi menurut umur
                case 'PB/U': // panjang menurut umur
                case 'IMT/U': // imt menurut umur
                    return ['age'];
                case 'PB/BB': // berat menurut panjang badan
                    return ['length'];
                case 'TB/BB': // berat menurut tinggi badan
                    return ['height'];
                default:
                    return ['age'];
            }
        }

        function syncRefTypeByParameter() {
            const allowed = allowedRefTypesFor(paramSelect.value);
            refType.innerHTML = '';
            allowed.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v;
                opt.textContent = v;
                refType.appendChild(opt);
            });
            toggleRefFields();
        }
        paramSelect.addEventListener('change', syncRefTypeByParameter);

        // ===== Prefill Edit
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const r = JSON.parse(btn.getAttribute('data-row'));
                const form = document.getElementById('main-form');
                form.reset();
                form.action = @json(route('growth-standards.update', 0)).replace('/0', '/' + r.id);
                document.getElementById('method-field').innerHTML =
                    '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('modal-title').textContent = 'Edit Growth Standard';

                document.getElementById('gender').value = r.gender;
                document.getElementById('parameter').value = r.parameter;
                syncRefTypeByParameter();
                const ops = Array.from(refType.options).map(o => o.value);
                if (ops.includes(r.reference_type)) refType.value = r.reference_type;
                toggleRefFields();

                document.getElementById('age_months').value = r.age_months ?? '';
                document.getElementById('body_length').value = r.body_length ?? '';
                document.getElementById('body_height').value = r.body_height ?? '';

                document.getElementById('minus_3_sd').value = r.minus_3_sd;
                document.getElementById('minus_2_sd').value = r.minus_2_sd;
                document.getElementById('minus_1_sd').value = r.minus_1_sd;
                document.getElementById('median').value = r.median;
                document.getElementById('plus_1_sd').value = r.plus_1_sd;
                document.getElementById('plus_2_sd').value = r.plus_2_sd;
                document.getElementById('plus_3_sd').value = r.plus_3_sd;

                document.getElementById('measurement_condition').value = r.measurement_condition ?? '';
                document.getElementById('is_active').checked = !!r.is_active;

                document.getElementById('form-modal').classList.remove('hidden');
                document.getElementById('form-modal').classList.add('flex');
            });
        });

        // ===== Hapus (konfirmasi)
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Hapus'
                }).then((res) => {
                    if (res.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });
                        form.submit();
                    }
                });
            });
        });

        // ===== Toggle Aktif (konfirmasi)
        document.querySelectorAll('.toggle-active-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                Swal.fire({
                    icon: 'question',
                    title: 'Ubah Status?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ubah',
                    cancelButtonText: 'Batal'
                }).then((res) => {
                    if (res.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });
                        form.submit();
                    }
                });
            });
        });

        // ===== Submit (Create/Update)
        document.getElementById('btn-submit-main').addEventListener('click', () => {
            const isEdit = !!document.querySelector('#method-field input[name="_method"]');
            Swal.fire({
                icon: 'question',
                title: isEdit ? 'Simpan Perubahan?' : 'Simpan Data Baru?',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('main-form').submit();
                }
            });
        });

        // ===== Import
        document.getElementById('btn-submit-import').addEventListener('click', () => {
            const fi = document.querySelector('#import-form input[type=file]');
            if (!fi.files.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File belum dipilih'
                });
                return;
            }
            Swal.fire({
                icon: 'question',
                title: 'Import data?',
                text: 'Data standar akan ditambahkan.',
                showCancelButton: true,
                confirmButtonText: 'Ya, import',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Mengimpor...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('import-form').submit();
                }
            });
        });

        // Inisialisasi awal (kalau halaman dibuka & modal belum dipanggil)
        // Pastikan select reference_type terisi saat user langsung memilih parameter.
        if (document.getElementById('parameter')) {
            syncRefTypeByParameter();
            toggleRefFields();
        }
    </script>
</x-app-layout>
