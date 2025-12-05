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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-1 sm:col-span-2 lg:col-span-2">
            <div class="rounded-lg mt-4 lg:mt-0">
                <form id="searchForm" method="GET"
                    class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" id="searchInput" name="query" placeholder="Cari siswa..."
                        class="h-10 w-full sm:w-full lg:w-full max-w-4xl border bg-white dark:bg-gray-900 dark:text-gray-300 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500"
                        value="{{ request()->query('query') }}" />

                    <select name="service_id"
                        class="h-10 w-full sm:w-auto px-6 border bg-white dark:bg-gray-900 dark:text-gray-300 border-gray-300 dark:border-gray-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-pbg-pink-500">
                        <option value="">Service All</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}"
                                {{ request()->query('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->service_name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md w-full sm:w-auto">
                        Search
                    </button>
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
                            <th class="py-3 px-6 text-left">NIS</th>
                            <th class="py-3 px-6 text-left">Nama Anak</th>
                            <th class="py-3 px-6 text-left">Nama Ibu</th>
                            <th class="py-3 px-6 text-left hidden lg:table-cell">Alamat</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($activityTransactions as $index => $activityTransaction)
                            <tr
                                class="border-b border-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:bg-opacity-50">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    {{ ($activityTransactions->currentPage() - 1) * $activityTransactions->perPage() + $index + 1 }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $activityTransaction->student->student_number }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $activityTransaction->student->student_name }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $activityTransaction->student->mother_name }}
                                </td>
                                <td class="py-3 px-6 text-left hidden lg:table-cell">
                                    {{ $activityTransaction->student->street }},
                                    {{ $activityTransaction->student->village }},
                                    {{ $activityTransaction->student->subdistrict }},
                                    {{ $activityTransaction->student->district }}
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('measurement.create', $activityTransaction->id) }}"
                                            class="relative group">
                                            <div
                                                class="flex bg-green-500 px-3 py-1 text-white justify-center items-center rounded-md">
                                                <span
                                                    class="material-symbols-outlined text-base font-extralight mr-2">edit_square</span>
                                                <p class="text-xs">Create Laporan</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('measurement.history', $activityTransaction->id) }}"
                                            class="relative group">
                                            <div
                                                class="flex bg-indigo-400 px-3 py-1 text-white justify-center items-center rounded-md">
                                                <span
                                                    class="material-symbols-outlined text-white text-base font-extralight mr-2">visibility</span>
                                                <p class="text-xs">History Laporan</p>
                                            </div>
                                        </a>
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

            <div class="mt-4">
                {{ $activityTransactions->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</x-app-layout>
