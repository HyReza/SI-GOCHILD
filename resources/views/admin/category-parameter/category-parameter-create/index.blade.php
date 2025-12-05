<x-app-layout>
    <x-slot:title>Manajemen Parameter Kategori</x-slot:title>

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
        <button onclick="openModalCreate()"
            class="flex items-center justify-center gap-2 font-semibold bg-green-500 dark:bg-green-600 h-10 w-full md:w-48 rounded-lg shadow-md hover:shadow-none hover:bg-green-600 dark:hover:bg-green-700 text-white">
            <span class="text-xs">Tambah Parameter</span>
            <span class="material-symbols-outlined text-xs">add</span>
        </button>

        <form method="GET" action="{{ route('category-parameter.index') }}"
            class="flex gap-2 items-center w-full md:w-1/2">
            <input type="text" name="search" placeholder="Cari parameter..."
                class="h-10 w-full border bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:text-white"
                value="{{ $search ?? '' }}">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white rounded-lg px-4 h-10">
                Cari
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
                        <th class="py-3 px-6 text-left">Nama Parameter</th>
                        <th class="py-3 px-6 text-left">Deskripsi</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 dark:text-gray-300 text-sm">
                    @forelse ($categoryParameters as $param)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800/60">
                            <td class="py-3 px-6 whitespace-nowrap">
                                {{ ($categoryParameters->currentPage() - 1) * $categoryParameters->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3 px-6">
                                {{ $param->category_parameter_name }}
                            </td>
                            <td class="py-3 px-6">
                                {{ \Illuminate\Support\Str::limit($param->category_parameter_description, 60) }}
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex gap-2 justify-center">
                                    <button onclick="viewParameter({{ $param->id }})" class="relative group"
                                        title="Lihat Detail">
                                        <span
                                            class="material-symbols-outlined bg-indigo-500 px-2 py-1 rounded-md text-white text-base">visibility</span>
                                        <span
                                            class="z-50 absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Lihat
                                            Detail</span>
                                    </button>

                                    <button onclick="editParameter({{ $param->id }})" class="relative group"
                                        title="Edit">
                                        <span
                                            class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base">edit_square</span>
                                        <span
                                            class="z-50 absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Edit
                                            Data</span>
                                    </button>

                                    <form id="delete-form-{{ $param->id }}"
                                        action="{{ route('category-parameter.destroy', $param) }}" method="POST"
                                        class="relative group" data-turbo="false">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="js-btn-delete material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base"
                                            title="Hapus" data-id="{{ $param->id }}"
                                            data-name="{{ $param->category_parameter_name }}">
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
                            <td colspan="4" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                Belum ada data parameter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $categoryParameters->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- Modal Create / Edit --}}
    <div id="form-modal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg max-w-lg w-full p-6 md:p-8 relative m-4">
            <button onclick="closeModal()"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>

            <h2 id="modal-title" class="text-lg font-semibold mb-4">Tambah Parameter Baru</h2>

            <form id="param-form" method="POST" action="{{ route('category-parameter.store') }}" data-turbo="false">
                @csrf
                <div id="method-field" class="mb-2"></div>

                <div class="mb-4">
                    <x-input-label for="category_parameter_name" :value="__('Nama Parameter')" />
                    <x-text-input id="category_parameter_name" class="block mt-1 w-full" type="text"
                        name="category_parameter_name" required autofocus />
                    @error('category_parameter_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-input-label for="category_parameter_description" :value="__('Deskripsi Parameter')" />
                    <textarea id="category_parameter_description" name="category_parameter_description"
                        class="block mt-1 w-full p-2 border dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md"
                        rows="4"></textarea>
                    @error('category_parameter_description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-end mt-4 gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-md border dark:border-gray-600">
                        Batal
                    </button>
                    <x-primary-button id="form-submit-button">Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal View --}}
    <div id="view-modal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
        <div
            class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg p-6 md:p-8 max-w-md w-full relative overflow-y-auto max-h-[80vh] m-4">
            <button onclick="toggleViewModal(false)"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-white">&times;</button>
            <h3 class="text-xl font-bold mb-2" id="view-title"></h3>
            <p id="view-desc" class="break-words whitespace-pre-line text-sm"></p>
            <div class="mt-4 text-xs text-gray-500">
                <div id="view-meta"></div>
            </div>
        </div>
    </div>

    <script>
        // ===== Modal helpers =====
        window.openModalCreate = function() {
            const modal = document.getElementById('form-modal');
            document.getElementById('modal-title').textContent = 'Tambah Parameter Baru';
            document.getElementById('param-form').action = @json(route('category-parameter.store'));
            document.getElementById('form-submit-button').textContent = 'Tambah';
            document.getElementById('method-field').innerHTML = '';
            document.getElementById('category_parameter_name').value = '';
            document.getElementById('category_parameter_description').value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
        window.openModalEdit = function() {
            const modal = document.getElementById('form-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
        window.closeModal = function() {
            const modal = document.getElementById('form-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };
        window.toggleViewModal = function(show) {
            const modal = document.getElementById('view-modal');
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        };
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.closeModal();
                window.toggleViewModal(false);
            }
        });

        // ===== Edit (prefill via fetch) =====
        window.editParameter = function(id) {
            fetch(@json(url('category-parameter')) + '/' + id + '/edit')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('category_parameter_name').value = data.category_parameter_name ?? '';
                    document.getElementById('category_parameter_description').value = data
                        .category_parameter_description ?? '';
                    document.getElementById('param-form').action = @json(url('category-parameter')) + '/' + id;
                    document.getElementById('form-submit-button').textContent = 'Simpan Perubahan';
                    document.getElementById('modal-title').textContent = 'Edit Parameter';
                    document.getElementById('method-field').innerHTML =
                        '<input type="hidden" name="_method" value="PUT">';
                    window.openModalEdit();
                })
                .catch(() => {
                    window.Swal ? Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat memuat data.'
                    }) : alert('Tidak dapat memuat data.');
                });
        };

        // ===== View detail (via fetch) =====
        window.viewParameter = function(id) {
            fetch(@json(url('category-parameter')) + '/' + id)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('view-title').textContent = data.category_parameter_name ?? '-';
                    document.getElementById('view-desc').textContent = data.category_parameter_description ?? '-';
                    const meta = [];
                    if (data.created_at) meta.push('Dibuat: ' + new Date(data.created_at).toLocaleString());
                    if (data.updated_at) meta.push('Diubah: ' + new Date(data.updated_at).toLocaleString());
                    document.getElementById('view-meta').textContent = meta.join(' | ');
                    window.toggleViewModal(true);
                })
                .catch(() => {
                    window.Swal ? Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat memuat detail.'
                    }) : alert('Tidak dapat memuat detail.');
                });
        };

        // ===== DELETE: Event Delegation (tanpa onclick inline) =====
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-btn-delete');
            if (!btn) return;

            const id = btn.dataset.id;
            const name = btn.dataset.name || '';

            // Fallback jika Swal belum ada
            if (!window.Swal) {
                if (confirm('Hapus parameter "' + name + '"?')) {
                    const form = document.getElementById('delete-form-' + id);
                    if (form) form.submit();
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
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form-' + id);
                    if (!form) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Form hapus tidak ditemukan.'
                        });
                        return;
                    }
                    Swal.fire({
                        title: 'Menghapus...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        // ===== Tambah/Edit: konfirmasi & loading =====
        const paramForm = document.getElementById('param-form');
        const submitBtn = document.getElementById('form-submit-button');
        let submitting = false;

        function isEditMode() {
            const methodField = document.querySelector('#method-field input[name="_method"]');
            return methodField && methodField.value.toUpperCase() === 'PUT';
        }
        paramForm.addEventListener('submit', function(e) {
            if (submitting) {
                e.preventDefault();
                return;
            }
            e.preventDefault();

            const mode = isEditMode() ? 'edit' : 'tambah';
            const title = mode === 'edit' ? 'Simpan Perubahan?' : 'Simpan Data Baru?';
            const text = mode === 'edit' ? 'Perubahan pada parameter akan disimpan.' :
                'Parameter baru akan ditambahkan.';

            if (!window.Swal) {
                submitting = true;
                submitBtn?.setAttribute('disabled', 'disabled');
                paramForm.submit();
                return;
            }

            Swal.fire({
                title,
                text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    submitting = true;
                    submitBtn?.setAttribute('disabled', 'disabled');
                    Swal.fire({
                        title: mode === 'edit' ? 'Menyimpan Perubahan...' : 'Menyimpan Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    paramForm.submit();
                }
            });
        }, {
            passive: false
        });
    </script>
</x-app-layout>
