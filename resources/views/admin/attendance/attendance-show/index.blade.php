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

    <nav aria-label="Breadcrumb" class="flex">
        <ol
            class="flex overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
            <li class="flex items-center">
                <a href="{{ route('attendance.index') }}"
                    class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800 px-4 transition hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>

                    <span class="ms-1.5 text-xs font-medium dark:text-gray-300"> Daftar Absensi Siswa </span>
                </a>
            </li>

            <li class="relative flex items-center">
                <span
                    class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180">
                </span>

                <a href="#"
                    class="flex h-10 items-center bg-white dark:bg-gray-900 pe-4 ps-8 text-xs font-medium transition hover:text-gray-900 dark:hover:text-white">
                    Detail Absensi Siswa
                </a>
            </li>
        </ol>
    </nav>

    <div
        class="my-6 p-8 bg-white dark:bg-gray-900 drop-shadow-lg rounded-md hover:drop-shadow-none duration-300 ease-in">
        <div class="overflow-x-auto">
            <h1 class="text-xl font-semibold mb-6 text-gray-700 dark:text-gray-200">Attendance Details for
                {{ $attendanceTransaction->service->service_name }} - {{ $attendanceTransaction->date_attendance }}</h1>

            {{-- Displaying Attendance --}}
            <table class="min-w-full table-auto border-collapse mb-4">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-200 text-sm leading-normal">
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Student ID</th>
                        <th class="py-3 px-6 text-left">Student Name</th>
                        <th class="py-3 px-6 text-left">Check-in Status</th>
                        <th class="py-3 px-6 text-left">Check-out Status</th>
                        <th class="py-3 px-6 text-left">Check-in Time</th>
                        <th class="py-3 px-6 text-left">Check-out Time</th>
                        <th class="py-3 px-6 text-left">Late Duration</th>
                        <th class="py-3 px-6 text-left">Note</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @foreach ($attendanceTransaction->attendances as $index => $attendance)
                        <tr
                            class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="py-3 px-6 text-left">{{ $index + 1 }}</td>
                            <td class="py-3 px-6 text-left">
                                {{ $attendance->activityTransaction->student->student_number }}</td>
                            <td class="py-3 px-6 text-left">
                                {{ $attendance->activityTransaction->student->student_name }}</td>
                            <td class="py-3 px-6 text-left">{{ $attendance->check_in_status }}</td>
                            <td class="py-3 px-6 text-left">{{ $attendance->check_out_status }}</td>
                            <td class="py-3 px-6 text-left">{{ $attendance->check_in_time }}</td>
                            <td class="py-3 px-6 text-left">{{ $attendance->check_out_time }}</td>
                            <td class="py-3 px-6 text-left">{{ $attendance->late_duration }} minutes</td>
                            <td class="py-3 px-6 text-left">{{ $attendance->note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Action Buttons --}}
            <div class="mt-4 m-2 flex justify-between">
                <a href="{{ route('attendance.index') }}">
                    <x-primary-button>Back to Attendance List</x-primary-button>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
