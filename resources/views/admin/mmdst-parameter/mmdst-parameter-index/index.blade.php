<x-app-layout>
    <x-slot:title>Manajemen Parameter MMDST</x-slot:title>

    @php
        // Fallback bila controller belum mengirim $categories
        $categories =
            $categories ??
            \App\Models\CategoryParameter::orderBy('category_parameter_name')->get(['id', 'category_parameter_name']);
    @endphp

    {{-- SweetAlert: Sukses --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: @json(session('success')),
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            });
        </script>
    @endif

    {{-- SweetAlert: Error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: @json($errors->first()),
                        showConfirmButton: true
                    });
                }
            });
        </script>
    @endif

    {{-- Header Aksi + Pencarian --}}
    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <!-- Form: di mobile di atas, full width -->
        <form method="GET" action="{{ route('mmdst-parameter.index') }}"
            class="order-1 w-full md:order-1 md:flex-1 grid grid-cols-1 sm:grid-cols-12 gap-2">
            <label for="search" class="sr-only">Cari</label>
            <input id="search" type="text" name="search" placeholder="Cari unsur / deskripsi / kategori..."
                class="h-10 sm:h-11 w-full min-w-0 border bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:text-white sm:col-span-8"
                value="{{ $search ?? '' }}">

            <label for="active" class="sr-only">Status</label>
            <select id="active" name="active"
                class="h-10 sm:h-11 w-full border bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:text-white sm:col-span-3">
                <option value="" {{ ($active ?? '') === '' ? 'selected' : '' }}>Semua Status</option>
                <option value="1" {{ ($active ?? '') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ ($active ?? '') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit"
                class="h-10 sm:h-11 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white rounded-lg px-4 sm:px-3 sm:col-span-1">
                Cari
            </button>
        </form>

        <!-- Aksi: di mobile di bawah, tombol bisa wrap & lebar sama -->
        <div class="order-2 w-full md:order-2 md:w-auto flex flex-wrap gap-2">
            <button onclick="openModalCreate()"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 font-semibold bg-green-500 dark:bg-green-600 h-10 px-4 rounded-lg shadow-md hover:shadow-none hover:bg-green-600 dark:hover:bg-green-700 text-white">
                <span class="text-sm md:text-xs">Tambah Parameter</span>
                <span class="material-symbols-outlined text-sm md:text-xs">add</span>
            </button>

            <button onclick="openImportModal()"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 font-semibold bg-amber-500 dark:bg-amber-600 h-10 px-4 rounded-lg shadow-md hover:bg-amber-600 dark:hover:bg-amber-700 text-white">
                <span class="text-sm md:text-xs">Import Excel</span>
                <span class="material-symbols-outlined text-sm md:text-xs">upload_file</span>
            </button>
        </div>
    </div>



    {{-- Tabel Data --}}
    <div
        class="my-6 p-6 md:p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse mb-4">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm">
                    <tr>
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Unsur / Deskripsi</th>
                        <th class="py-3 px-6 text-left">25%</th>
                        <th class="py-3 px-6 text-left">50%</th>
                        <th class="py-3 px-6 text-left">75%</th>
                        <th class="py-3 px-6 text-left">100%</th>
                        <th class="py-3 px-6 text-left">Kategori Stimulasi</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 dark:text-gray-300 text-sm">
                    @forelse ($mmdstParameters as $row)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800/60">
                            <td class="py-3 px-6 whitespace-nowrap">
                                {{ ($mmdstParameters->currentPage() - 1) * $mmdstParameters->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3 px-6">
                                <div class="font-medium">{{ $row->test_element_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit($row->test_element_description, 70) }}
                                </div>
                            </td>
                            <td class="py-3 px-6">{{ $row->percent_25 ?? '-' }}</td>
                            <td class="py-3 px-6">{{ $row->percent_50 ?? '-' }}</td>
                            <td class="py-3 px-6">{{ $row->percent_75 ?? '-' }}</td>
                            <td class="py-3 px-6">{{ $row->percent_100 ?? '-' }}</td>
                            <td class="py-3 px-6">
                                {{ optional($row->stimulationCategory)->category_parameter_name ?? '-' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if ($row->parameter_is_active)
                                    <span
                                        class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Aktif</span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex gap-2 justify-center">
                                    <button onclick="viewData({{ $row->id }})" class="relative group"
                                        title="Lihat Detail">
                                        <span
                                            class="material-symbols-outlined bg-indigo-500 px-2 py-1 rounded-md text-white text-base">visibility</span>
                                        <span
                                            class="z-50 absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Lihat
                                            Detail</span>
                                    </button>
                                    <button onclick="editData({{ $row->id }})" class="relative group"
                                        title="Edit">
                                        <span
                                            class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base">edit_square</span>
                                        <span
                                            class="z-50 absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Edit
                                            Data</span>
                                    </button>
                                    <form id="delete-form-{{ $row->id }}"
                                        action="{{ route('mmdst-parameter.destroy', $row) }}" method="POST"
                                        class="relative group" data-turbo="false">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="js-btn-delete material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base"
                                            title="Hapus" data-id="{{ $row->id }}"
                                            data-name="{{ $row->test_element_name }}">
                                            delete
                                        </button>
                                        <span
                                            class="z-50 absolute right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Hapus
                                            Data</span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data parameter MMDST.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $mmdstParameters->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- Modal Create / Edit --}}
    <div id="form-modal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg max-w-2xl w-full p-6 md:p-8 relative m-4">
            <button onclick="closeModal()"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>

            <h2 id="modal-title" class="text-lg font-semibold mb-4">Tambah Parameter Baru</h2>

            <form id="main-form" method="POST" action="{{ route('mmdst-parameter.store') }}" data-turbo="false">
                @csrf
                <div id="method-field" class="mb-2"></div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="mb-2">
                        <x-input-label for="test_element_name" :value="__('Nama Unsur / Elemen')" />
                        <x-text-input id="test_element_name" name="test_element_name" type="text"
                            class="block mt-1 w-full" required autofocus />
                        @error('test_element_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <x-input-label for="stimulation_category_id" :value="__('Kategori Stimulasi')" />
                        <select id="stimulation_category_id" name="stimulation_category_id"
                            class="block mt-1 w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->category_parameter_name }}</option>
                            @endforeach
                        </select>
                        @error('stimulation_category_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2 mb-2">
                        <x-input-label for="test_element_description" :value="__('Deskripsi Unsur / Elemen')" />
                        <textarea id="test_element_description" name="test_element_description"
                            class="block mt-1 w-full p-2 border dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md"
                            rows="3" required></textarea>
                        @error('test_element_description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <x-input-label for="percent_25" :value="__('Nilai 25%')" />
                        <x-text-input id="percent_25" name="percent_25" type="number" min="0"
                            max="100" class="block mt-1 w-full" required />
                        @error('percent_25')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="percent_50" :value="__('Nilai 50%')" />
                        <x-text-input id="percent_50" name="percent_50" type="number" min="0"
                            max="100" class="block mt-1 w-full" required />
                        @error('percent_50')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="percent_75" :value="__('Nilai 75%')" />
                        <x-text-input id="percent_75" name="percent_75" type="number" min="0"
                            max="100" class="block mt-1 w-full" required />
                        @error('percent_75')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="percent_100" :value="__('Nilai 100%')" />
                        <x-text-input id="percent_100" name="percent_100" type="number" min="0"
                            max="100" class="block mt-1 w-full" required />
                        @error('percent_100')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex items-center gap-2 mt-2">
                        {{-- kirim 0 saat unchecked --}}
                        <input type="hidden" name="parameter_is_active" value="0">
                        <input id="parameter_is_active" name="parameter_is_active" type="checkbox" value="1"
                            class="rounded" checked>
                        <label for="parameter_is_active" class="text-sm">Aktif?</label>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-md border dark:border-gray-600">
                        Batal
                    </button>
                    <x-primary-button id="btn-submit-main">Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal View --}}
    <div id="view-modal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg p-6 md:p-8 max-w-xl w-full relative overflow-y-auto max-h-[80vh] m-4">
            <button onclick="toggleViewModal(false)"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>
            <h3 class="text-xl font-bold mb-2" id="v-title"></h3>
            <div class="text-sm mb-2">
                <span class="text-gray-500">Kategori:</span> <span id="v-cat" class="font-medium"></span>
            </div>
            <div class="grid grid-cols-4 gap-2 text-sm mb-4">
                <div><span class="text-gray-500">25%:</span> <span id="v-25"></span></div>
                <div><span class="text-gray-500">50%:</span> <span id="v-50"></span></div>
                <div><span class="text-gray-500">75%:</span> <span id="v-75"></span></div>
                <div><span class="text-gray-500">100%:</span> <span id="v-100"></span></div>
            </div>
            <div class="mb-2 text-sm">
                <span class="text-gray-500">Status:</span>
                <span id="v-status" class="ml-1"></span>
            </div>
            <p id="v-desc" class="break-words whitespace-pre-line text-sm border-t pt-3 mt-3"></p>
            <div class="mt-4 text-xs text-gray-500" id="v-meta"></div>
        </div>
    </div>

    {{-- Modal Import Excel --}}
    <div id="import-modal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg p-6 md:p-8 max-w-lg w-full relative m-4">
            <button onclick="closeImportModal()"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Import Parameter MMDST dari Excel</h3>
            <div class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                Format header (baris pertama) yang diharapkan:
                <code>nama_unsur_test | deskripsi_unsur_test | 25% | 50% | 75% | 100% | kategori_stimulasi</code>
            </div>
            <form id="import-form" method="POST" action="{{ route('mmdst-parameter.import') }}"
                enctype="multipart/form-data" data-turbo="false">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv"
                    class="block w-full border rounded-md p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-4"
                    required>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="closeImportModal()"
                        class="px-4 py-2 rounded-md border dark:border-gray-600">
                        Batal
                    </button>
                    <x-primary-button id="btn-submit-import">Import</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ===== Modal helpers =====
        function openModalCreate() {
            const m = document.getElementById('form-modal');
            document.getElementById('modal-title').textContent = 'Tambah Parameter Baru';
            document.getElementById('main-form').action = @json(route('mmdst-parameter.store'));
            document.getElementById('btn-submit-main').textContent = 'Tambah';
            document.getElementById('method-field').innerHTML = '';
            // reset fields
            document.getElementById('test_element_name').value = '';
            document.getElementById('test_element_description').value = '';
            ['percent_25', 'percent_50', 'percent_75', 'percent_100'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('stimulation_category_id').value = '';
            document.getElementById('parameter_is_active').checked = true;
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function openModalEdit() {
            const m = document.getElementById('form-modal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModal() {
            const m = document.getElementById('form-modal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        function toggleViewModal(show) {
            const m = document.getElementById('view-modal');
            if (show) {
                m.classList.remove('hidden');
                m.classList.add('flex');
            } else {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }
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
                toggleViewModal(false);
                closeImportModal();
            }
        });

        // ===== View detail =====
        function viewData(id) {
            fetch(@json(url('mmdst-parameter')) + '/' + id)
                .then(r => r.json())
                .then(d => {
                    document.getElementById('v-title').textContent = d.test_element_name ?? '-';
                    document.getElementById('v-desc').textContent = d.test_element_description ?? '-';
                    document.getElementById('v-cat').textContent = d.stimulation_category_name ?? '-';
                    document.getElementById('v-25').textContent = d.percent_25 ?? '-';
                    document.getElementById('v-50').textContent = d.percent_50 ?? '-';
                    document.getElementById('v-75').textContent = d.percent_75 ?? '-';
                    document.getElementById('v-100').textContent = d.percent_100 ?? '-';
                    document.getElementById('v-status').innerHTML = (d.parameter_is_active) ?
                        '<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Aktif</span>' :
                        '<span class="px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs">Nonaktif</span>';
                    const meta = [];
                    if (d.created_at) meta.push('Dibuat: ' + new Date(d.created_at).toLocaleString());
                    if (d.updated_at) meta.push('Diubah: ' + new Date(d.updated_at).toLocaleString());
                    document.getElementById('v-meta').textContent = meta.join(' | ');
                    toggleViewModal(true);
                })
                .catch(() => {
                    window.Swal ?
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Tidak dapat memuat detail.'
                        }) :
                        alert('Tidak dapat memuat detail.');
                });
        }

        // ===== Edit (prefill) =====
        function editData(id) {
            fetch(@json(url('mmdst-parameter')) + '/' + id + '/edit')
                .then(r => r.json())
                .then(d => {
                    document.getElementById('test_element_name').value = d.test_element_name ?? '';
                    document.getElementById('test_element_description').value = d.test_element_description ?? '';
                    document.getElementById('percent_25').value = d.percent_25 ?? '';
                    document.getElementById('percent_50').value = d.percent_50 ?? '';
                    document.getElementById('percent_75').value = d.percent_75 ?? '';
                    document.getElementById('percent_100').value = d.percent_100 ?? '';
                    document.getElementById('stimulation_category_id').value = d.stimulation_category_id ?? '';
                    document.getElementById('parameter_is_active').checked = !!d.parameter_is_active;

                    document.getElementById('main-form').action = @json(url('mmdst-parameter')) + '/' + id;
                    document.getElementById('btn-submit-main').textContent = 'Simpan Perubahan';
                    document.getElementById('modal-title').textContent = 'Edit Parameter';
                    document.getElementById('method-field').innerHTML =
                        '<input type="hidden" name="_method" value="PUT">';

                    openModalEdit();
                })
                .catch(() => {
                    window.Swal ?
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Tidak dapat memuat data.'
                        }) :
                        alert('Tidak dapat memuat data.');
                });
        }

        // ===== Hapus (delegation) =====
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-btn-delete');
            if (!btn) return;
            const id = btn.dataset.id,
                name = btn.dataset.name || '';
            if (!window.Swal) {
                if (confirm('Hapus parameter "' + name + '"?')) {
                    document.getElementById('delete-form-' + id)?.submit();
                }
                return;
            }
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Yakin ingin menghapus parameter "' + name + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        html: 'Mohon tunggu',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });
                    document.getElementById('delete-form-' + id)?.submit();
                }
            });
        });

        // ===== Submit (Tambah/Edit) konfirmasi + loading =====
        const mainForm = document.getElementById('main-form');
        const btnMain = document.getElementById('btn-submit-main');
        let mainSubmitting = false;

        function isEditMode() {
            const mf = document.querySelector('#method-field input[name="_method"]');
            return mf && mf.value.toUpperCase() === 'PUT';
        }

        mainForm.addEventListener('submit', function(e) {
            if (mainSubmitting) {
                e.preventDefault();
                return;
            }
            e.preventDefault();
            const mode = isEditMode() ? 'edit' : 'tambah';
            const title = mode === 'edit' ? 'Simpan Perubahan?' : 'Simpan Data Baru?';
            const text = mode === 'edit' ? 'Perubahan akan disimpan.' : 'Parameter baru akan ditambahkan.';

            if (!window.Swal) {
                mainSubmitting = true;
                btnMain?.setAttribute('disabled', 'disabled');
                mainForm.submit();
                return;
            }

            Swal.fire({
                    title,
                    text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                })
                .then((res) => {
                    if (res.isConfirmed) {
                        mainSubmitting = true;
                        btnMain?.setAttribute('disabled', 'disabled');
                        Swal.fire({
                            title: mode === 'edit' ? 'Menyimpan Perubahan...' : 'Menyimpan Data...',
                            html: 'Mohon tunggu',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });
                        mainForm.submit();
                    }
                });
        }, {
            passive: false
        });

        // ===== Import Excel (konfirmasi + loading) =====
        const importForm = document.getElementById('import-form');
        const btnImport = document.getElementById('btn-submit-import');
        let importSubmitting = false;

        function openImportConfirm() {
            return window.Swal ?
                Swal.fire({
                    title: 'Import Excel?',
                    text: 'Data pada file akan ditambahkan ke database.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Import',
                    cancelButtonText: 'Batal'
                }) :
                Promise.resolve({
                    isConfirmed: confirm('Import Excel?')
                });
        }

        importForm.addEventListener('submit', function(e) {
            if (importSubmitting) {
                e.preventDefault();
                return;
            }
            e.preventDefault();
            openImportConfirm().then((res) => {
                if (res.isConfirmed) {
                    importSubmitting = true;
                    btnImport?.setAttribute('disabled', 'disabled');
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Mengimpor...',
                            html: 'Mohon tunggu',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });
                    }
                    importForm.submit();
                }
            });
        }, {
            passive: false
        });
    </script>
</x-app-layout>
