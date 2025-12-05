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
                    <input type="text" id="searchInput" name="search" placeholder="Cari sesuatu..."
                        value="{{ old('search', request()->input('search')) }}"
                        class="h-10 w-full border bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500" />
                </form>
            </div>
        </div>
    </div>

    <div class="col-span-12">
        <div
            class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse mb-4" id="studentsTable">
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
                    <tbody class="text-gray-600 text-sm font-light" id="studentRows">
                        @foreach ($students as $student)
                            <tr
                                class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    {{ $students->firstItem() + $loop->iteration - 1 }}</td>
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
                                        </a>
                                        <a href="{{ route('siswa.edit', $student) }}" class="relative group">
                                            <span
                                                class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">edit_square</span>
                                        </a>
                                        <!-- Delete Button -->
                                        <button type="button"
                                            class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button"
                                            data-student-id="{{ $student->id }}"
                                            data-student-name="{{ $student->student_name }}">
                                            delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4" id="paginationLinks">
                {{ $students->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Konfirmasi Penghapusan</h2>
            <p>Masukkan password Anda untuk menghapus data siswa <span id="studentName"></span></p>
            <form id="deleteForm" method="POST" action="{{ route('siswa.destroy', 0) }}">
                @csrf
                @method('DELETE')
                <div class="relative mb-4">
                    <input type="password" name="password" id="password"
                        class="w-full h-10 border bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500"
                        placeholder="Masukkan password" required>
                    <span toggle="#password" class="absolute right-3 top-3 cursor-pointer">
                        <i class="material-icons text-gray-500">visibility_off</i>
                    </span>
                </div>
                <div class="flex gap-4">
                    <button type="button" id="cancelDelete" class="bg-gray-300 px-4 py-2 rounded-md">Batal</button>
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Show/Hide password
            $("span[toggle='#password']").click(function() {
                var input = $("#password");
                var icon = $(this).find('i');
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                    icon.text("visibility");
                } else {
                    input.attr("type", "password");
                    icon.text("visibility_off");
                }
            });

            // Tampilkan modal konfirmasi penghapusan
            $('.delete-button').on('click', function() {
                const studentId = $(this).data('student-id');
                const studentName = $(this).data('student-name');

                $('#studentName').text(studentName); // Tampilkan nama siswa di modal
                $('#deleteForm').attr('action', `/siswa/${studentId}`); // Set action form untuk hapus siswa
                $('#deleteModal').removeClass('hidden'); // Tampilkan modal
            });

            // Batalkan penghapusan
            $('#cancelDelete').on('click', function() {
                $('#deleteModal').addClass('hidden'); // Sembunyikan modal
            });
        });
    </script>
</x-app-layout>
