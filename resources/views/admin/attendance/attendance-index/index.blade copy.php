<x-app-layout>
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

    <!-- Filter Form -->
    <!-- Tombol Add Attendance -->
    <div class="flex-shrink-0 w-full lg:w-auto">
        <a href="{{ route('attendance.create') }}">
            <x-primary-button class="lg:w-auto px-6 text-base font-semibold">
                Add Attendance
            </x-primary-button>
        </a>
    </div>
    <div class="w-full lg:w-auto flex justify-end">
        <form method="GET" action="{{ route('attendance.index') }}"
            class="flex flex-col md:flex-row flex-wrap gap-4 items-end lg:items-center w-full lg:w-auto">
            <!-- Service Filter -->
            <div class="flex flex-col w-full md:w-auto">
                <label for="service_id" class="text-sm text-gray-700 dark:text-gray-200 mb-1 font-medium">Service</label>
                <select name="service_id" id="service_id"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-6 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">-- All Services --</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}"
                            {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->service_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date -->
            <div class="flex flex-col w-full md:w-auto">
                <label for="start_date" class="text-sm text-gray-700 dark:text-gray-200 mb-1 font-medium">Start
                    Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- End Date -->
            <div class="flex flex-col w-full md:w-auto">
                <label for="end_date" class="text-sm text-gray-700 dark:text-gray-200 mb-1 font-medium">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Filter Button -->
            <div class="flex flex-col">
                <label class="text-sm text-transparent mb-1">Filter</label> {{-- Hidden label for spacing --}}
                <x-primary-button class="w-full sm:w-auto px-6 py-2 h-full">
                    Filter
                </x-primary-button>
            </div>

            {{-- <!-- Button to Download Excel -->
            <a href="{{ route('attendance.export', [
                'service_id' => request('service_id'),
                'start_date' => request('start_date'),
                'end_date' => request('end_date'),
            ]) }}"
                class="px-6 py-2 bg-blue-500 text-white rounded-md {{ !request('start_date') || !request('end_date') ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ !request('start_date') || !request('end_date') ? 'disabled' : '' }}>
                Export to Excel
            </a> --}}
        </form>
    </div>

    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse mb-4">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-200 text-sm leading-normal">
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Date</th>
                        <th class="py-3 px-6 text-left">Service</th>
                        <th class="py-3 px-6 text-left">Present</th>
                        <th class="py-3 px-6 text-left">Excused</th>
                        <th class="py-3 px-6 text-left">Sick</th>
                        <th class="py-3 px-6 text-left">Absent</th>
                        <th class="py-3 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @forelse($attendanceTransactions as $index => $transaction)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ ($attendanceTransactions->currentPage() - 1) * $attendanceTransactions->perPage() + $index + 1 }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $transaction->date_attendance }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $transaction->service ? $transaction->service->service_name : 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $transaction->attendances->where('check_in_status', 'Present')->count() }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $transaction->attendances->where('check_in_status', 'Excused')->count() }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $transaction->attendances->where('check_in_status', 'Sick')->count() }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $transaction->attendances->where('check_in_status', 'Absent')->count() }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('attendance.show', $transaction->id) }}" class="relative group">
                                        <span
                                            class="material-symbols-outlined bg-blue-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                            visibility
                                        </span>
                                        <span
                                            class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Lihat Detail
                                        </span>
                                    </a>
                                    <a href="{{ route('attendance.edit', $transaction->id) }}" class="relative group">
                                        <span
                                            class="material-symbols-outlined bg-green-500 px-2 py-1 rounded-md text-white text-base font-extralight">
                                            edit_square
                                        </span>
                                        <span
                                            class="absolute z-50 left-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Edit Data
                                        </span>
                                    </a>
                                    <form id="delete-form-{{ $transaction->id }}"
                                        action="{{ route('attendance.destroy', $transaction->id) }}" method="POST"
                                        class="relative group delete-form"
                                        data-theme-name="{{ $transaction->date_attendance }}"
                                        data-service-name="{{ $transaction->service->service_name }}"
                                        data-date-attendance="{{ $transaction->date_attendance }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            onclick="confirmDelete({{ $transaction->id }}, '{{ $transaction->date_attendance }}', '{{ $transaction->service->service_name }}')"
                                            class="material-symbols-outlined bg-red-500 px-2 py-1 rounded-md text-white text-base font-extralight delete-button">
                                            delete
                                        </button>
                                        <span
                                            class="absolute z-50 right-0 top-full mt-1 w-max px-2 py-1 rounded-md bg-gray-800 text-white text-xs hidden group-hover:block">
                                            Hapus Data
                                        </span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">No themes
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="mt-4">
            {{ $attendanceTransactions->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        // Fungsi untuk mengonfirmasi penghapusan dan mengirimkan form
        function confirmDelete(id, dateAttendance, serviceName) {
            // Menampilkan konfirmasi penghapusan dengan informasi spesifik
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete attendance for the service '" + serviceName + "' on the date " +
                    dateAttendance + ". This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, kirimkan form penghapusan
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }


        $(document).ready(function() {
            $('#exportExcelBtn').on('click', function() {
                var service_id = $('#service_id').val();
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();

                // Pastikan start_date dan end_date diisi
                if (!start_date || !end_date) {
                    alert('Please provide both start date and end date.');
                    return;
                }

                // Buat URL untuk ekspor Excel dengan parameter yang ada
                var url = "{{ route('attendance.export') }}?service_id=" + service_id + "&start_date=" +
                    start_date + "&end_date=" + end_date;

                // Lakukan AJAX request untuk mendapatkan file Excel
                $.ajax({
                    url: url,
                    method: 'GET',
                    xhr: function() {
                        var xhr = new XMLHttpRequest();
                        xhr.responseType = 'blob'; // Set responseType ke 'blob'
                        return xhr;
                    },
                    success: function(response, status, xhr) {
                        // Pastikan kita dapat file Blob yang valid
                        if (xhr.status === 200) {
                            var blob = response;
                            var link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download =
                                'attendance_report.xlsx'; // Nama file yang akan diunduh
                            link.click();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error details:", error);
                        alert('There was an error generating the report.');
                    }
                });
            });
        });
    </script>

</x-app-layout>
