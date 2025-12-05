<x-app-layout>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-2xl mx-auto p-4 sm:p-6">
        <h1 class="text-2xl font-semibold mb-4">Import Growth Standards</h1>

        <form id="importForm" action="{{ route('growth-standards.import') }}" method="POST" enctype="multipart/form-data"
            class="space-y-4">
            @csrf
            <input type="file" name="file" accept=".xlsx,.csv,.xls" class="border rounded w-full">
            <button type="button" id="btnImport" class="px-4 py-2 bg-indigo-600 text-white rounded">Import</button>
        </form>
    </div>

    <script>
        document.getElementById('btnImport').addEventListener('click', () => {
            const form = document.getElementById('importForm');
            const file = form.querySelector('input[type=file]');
            if (!file.files.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File belum dipilih'
                });
                return;
            }
            Swal.fire({
                    icon: 'question',
                    title: 'Import data?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, import',
                    cancelButtonText: 'Batal'
                })
                .then(res => {
                    if (res.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            didOpen: () => {
                                Swal.showLoading();
                                form.submit();
                            }
                        });
                    }
                });
        });
    </script>
</x-app-layout>
