<x-app-layout>
    <x-slot:title>Management Students</x-slot:title>

    {{-- SweetAlert for Success Message --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    {{-- SweetAlert for Error Message --}}
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <div class="grid grid-cols-12">
        <div class="col-span-12 lg:col-span-4">
            <a href="{{ route('siswa.create') }}">
                <div
                    class="flex justify-center items-center text-center font-semibold bg-green-500 dark:bg-green-600 h-10 w-36 rounded-lg shadow-md hover:shadow-none hover:bg-green-600 dark:hover:bg-green-700 text-white">
                    <h1 class="flex gap-4 text-xs">Tambah Siswa
                        <span class="flex material-symbols-outlined text-center text-xs">add</span>
                    </h1>
                </div>
            </a>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="rounded-lg mt-4 lg:mt-0">
                <form id="searchForm" method="GET" class="flex items-center">
                    <input type="text" id="searchInput" name="query" placeholder="Cari sesuatu..."
                        class="h-10 w-full border bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500" />
                </form>
            </div>
        </div>
    </div>
    <div class="col-span-12">
        <div
            class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse mb-4">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-500 text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left hidden lg:table-cell">NIS</th>
                            <th class="py-3 px-6 text-left">Nama Anak</th>
                            <th class="py-3 px-6 text-left hidden lg:table-cell">Nama Ibu</th>
                            <th class="py-3 px-6 text-left hidden lg:table-cell">Alamat</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($students as $student)
                            <tr
                                class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    {{ $students->firstItem() + $loop->iteration - 1 }}
                                </td>
                                <td class="py-3 px-6 text-left hidden lg:table-cell">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:gap-4">
                                        <div>
                                            <h1 class="font-medium text-xs">{{ $student->student_number }}</h1>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <div>{{ $student->student_name }}</div>
                                </td>
                                <td class="py-3 px-6 text-left hidden lg:table-cell">
                                    <div>{{ $student->mother_name }}</div>
                                </td>
                                <td class="py-3 px-6 text-left hidden lg:table-cell">
                                    <div>{{ $student->street }}, {{ $student->village }}, {{ $student->subdistrict }},
                                        {{ $student->district }}</div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('siswa.show', $student->id) }}" class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-indigo-400 px-2 py-1 rounded-md text-white text-base font-extralight">visibility</span>
                                            <span
                                                class="absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                                Lihat Detail
                                            </span>
                                        </a>
                                        <a href="{{ route('siswa.edit', $student) }}" class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">edit_square</span>
                                            <span
                                                class="absolute left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">Edit
                                                Data</span>
                                        </a>
                                        <form id="delete-form-{{ $student->id }}"
                                            action="{{ route('siswa.destroy', $student) }}" method="POST"
                                            class="relative group delete-form"
                                            data-student-name="{{ $student->nama }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $student->id }})"
                                                class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">
                                                delete
                                            </button>
                                            <span
                                                class="absolute right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                                Hapus Data
                                            </span>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-3 px-6 text-center text-gray-500">
                                    Tidak ada data siswa yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $students->links('pagination::tailwind') }}
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Select all delete buttons
                const deleteButtons = document.querySelectorAll('.delete-button');

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const form = this.closest('form');
                        const studentName = form.getAttribute('data-student-name');

                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: `Data student ${studentName} akan dihapus!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
</x-app-layout>
