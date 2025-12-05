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

    <nav aria-label="Breadcrumb" class="flex mx-4">
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
                    Edit Absensi Siswa
                </a>
            </li>
        </ol>
    </nav>

    <div class="container mx-auto p-4">
        <!-- Edit Attendance Form -->
        <form id="attendanceForm" method="POST" action="{{ route('attendance.update', $attendanceTransaction->id) }}"
            class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
            @csrf
            @method('PUT')

            <!-- Date Picker -->
            <div class="mb-4">
                <label for="date" class="block text-gray-700 dark:text-gray-200 font-semibold mb-2">Date</label>
                <input type="date" name="date_attendance" id="date"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white p-2 rounded-lg focus:ring focus:ring-blue-200 @error('date_attendance') border-red-500 @enderror"
                    value="{{ old('date_attendance', $attendanceTransaction->date_attendance) }}" required>

                @error('date_attendance')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Service Selector -->
            <div class="mb-4">
                <label for="service" class="block text-gray-700 dark:text-gray-200 font-semibold mb-2">Service</label>
                <select name="service_id" id="service"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white p-2 rounded-lg focus:ring focus:ring-blue-200"
                    disabled>
                    <option value="">Select Service</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected($service->id == $attendanceTransaction->service_id)>{{ $service->service_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto">
                <table id="studentsTable" class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                            <th class="border px-4 py-2 text-left">No</th>
                            <th class="border px-4 py-2 text-left">Student Name</th>
                            <th class="border px-4 py-2 text-left">Student ID</th>
                            <th class="border px-4 py-2 text-center">Status In</th>
                            <th class="border px-4 py-2 text-center">Status Out</th>
                            <th class="border px-4 py-2 text-center">Time In</th>
                            <th class="border px-4 py-2 text-center">Time Out</th>
                            <th class="border px-16 py-2 text-left">Notes</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody"
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-100 text-gray-700 dark:text-gray-300">
                        @foreach ($attendanceTransaction->attendances as $attendance)
                            <tr id="studentRow_{{ $attendance->activityTransaction->student->id }}"
                                data-program-end-time="{{ $attendance->activityTransaction->program->end_time ?? 'N/A' }}">
                                <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-4 py-2">
                                    {{ $attendance->activityTransaction->student->student_name }}</td>
                                <td class="border px-4 py-2">
                                    {{ $attendance->activityTransaction->student->student_number }}
                                    <span>{{ $attendance->activityTransaction->program->end_time ?? 'N/A' }}</span>
                                </td>

                                <!-- Hidden Fields for is_late and late_duration -->
                                <input type="hidden"
                                    name="attendance[{{ $attendance->activityTransaction->student->id }}][is_late]"
                                    value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.is_late', $attendance->is_late ?? 'false') }}">
                                <input type="hidden"
                                    name="attendance[{{ $attendance->activityTransaction->student->id }}][late_duration]"
                                    value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.late_duration', $attendance->late_duration ?? '0') }}">

                                <!-- Status In -->
                                <td class="border px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                value="Present" @checked($attendance->check_in_status == 'Present') required
                                                class="text-blue-600 focus:ring focus:ring-blue-200"
                                                onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})">
                                            <span class="ml-1 text-sm">Present</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                value="Excused" @checked($attendance->check_in_status == 'Excused')
                                                class="text-yellow-500 focus:ring focus:ring-yellow-200"
                                                onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})">
                                            <span class="ml-1 text-sm">Excused</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                value="Sick" @checked($attendance->check_in_status == 'Sick')
                                                class="text-red-500 focus:ring focus:ring-red-200"
                                                onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})">
                                            <span class="ml-1 text-sm">Sick</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                value="Absent" @checked($attendance->check_in_status == 'Absent')
                                                class="text-gray-500 focus:ring focus:ring-gray-200"
                                                onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})">
                                            <span class="ml-1 text-sm">Absent</span>
                                        </label>
                                    </div>
                                </td>

                                <!-- Status Out -->
                                <td id="statusOut_{{ $attendance->activityTransaction->student->id }}"
                                    class="border px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][check_out_status]"
                                                value="not_yet" @checked($attendance->check_out_status == 'not_yet')
                                                class="text-gray-500 focus:ring focus:ring-gray-200"
                                                onchange="toggleTimeOut({{ $attendance->activityTransaction->student->id }}, this)"
                                                @disabled($attendance->check_in_status != 'Present')>
                                            <span class="ml-1 text-sm">Belum Pulang</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][check_out_status]"
                                                value="on_time" @checked($attendance->check_out_status == 'on_time' || $attendance->check_out_status == 'late')
                                                class="text-blue-600 focus:ring focus:ring-blue-200"
                                                onchange="toggleTimeOut({{ $attendance->activityTransaction->student->id }}, this)"
                                                @disabled($attendance->check_in_status != 'Present')>
                                            <span class="ml-1 text-sm">Sudah Pulang</span>
                                        </label>
                                    </div>
                                </td>

                                <!-- Time In -->
                                <td id="timeIn_{{ $attendance->activityTransaction->student->id }}"
                                    class="border px-4 py-2 text-center">
                                    <input type="time"
                                        name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_time]"
                                        class="border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200 disabled:opacity-50   dark:disabled:bg-gray-200 dark:bg-gray-600"
                                        value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.check_in_time', $attendance->check_in_time) }}"
                                        @if ($attendance->check_in_status != 'Present') disabled @endif required>
                                </td>

                                <!-- Time Out -->
                                <td id="timeOut_{{ $attendance->activityTransaction->student->id }}"
                                    class="border px-4 py-2 text-center">
                                    <input type="time"
                                        name="attendance[{{ $attendance->activityTransaction->student->id }}][check_out_time]"
                                        class="border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200 disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-200 dark:bg-gray-600"
                                        value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.check_out_time', $attendance->check_out_time) }}"
                                        onchange="calculateLateStatus(this, {{ $attendance->activityTransaction->student->id }})"
                                        @if ($attendance->check_out_status != 'on_time' && $attendance->check_out_status != 'late') disabled @endif required>
                                </td>

                                <!-- Notes -->
                                <td class="border px-4 py-2">
                                    <textarea name="attendance[{{ $attendance->activityTransaction->student->id }}][note]"
                                        class="w-full border border-gray-300 p-1 rounded-lg focus:ring focus:ring-blue-200 disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-200 dark:bg-gray-600">{{ old('attendance.' . $attendance->activityTransaction->student->id . '.note', $attendance->note) }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="mt-6 bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg w-full md:w-auto">
                Update Attendance
            </button>
        </form>
    </div>


    <script>
        // Function to toggle the display of Time Out input based on Status In
        // Function to toggle the display of Time Out input based on Status In
        function toggleInputs(radio, studentId, programEndTime = null) {
            const checkInStatus = radio.value;
            const statusOutColumn = document.getElementById(`statusOut_${studentId}`);
            const timeInField = document.getElementById(`timeIn_${studentId}`);
            const timeOutField = document.getElementById(`timeOut_${studentId}`);
            const notesField = document.querySelector(`[name="attendance[${studentId}][note]"]`);

            // If Status In is 'Present', enable Time In and Status Out fields
            if (checkInStatus === 'Present') {
                statusOutColumn.querySelectorAll('input').forEach(input => input.disabled = false); // Enable Status Out
                timeInField.querySelector('input').disabled = false; // Enable Time In
                timeOutField.querySelector('input').disabled = true; // Disable Time Out
                notesField.value = ''; // Clear Notes field

                // Set Status Out to 'Belum Pulang' by default when Present is selected
                statusOutColumn.querySelector('input[value="not_yet"]').checked = true;
                timeInField.querySelector('input').value = ''; // Clear Time In field
                timeOutField.querySelector('input').value = ''; // Clear Time Out field
            } else {
                // If Status In is not 'Present', disable Status Out and Time In fields, and clear values
                statusOutColumn.querySelectorAll('input').forEach(input => input.disabled = true); // Disable Status Out
                timeInField.querySelector('input').disabled = true; // Disable Time In
                timeOutField.querySelector('input').disabled = true; // Disable Time Out
                notesField.value = ''; // Clear Notes field

                // Set Status Out to 'Belum Pulang'
                statusOutColumn.querySelector('input[value="not_yet"]').checked = true;
                timeInField.querySelector('input').value = ''; // Clear Time In field
                timeOutField.querySelector('input').value = ''; // Clear Time Out field
            }
        }


        // Function to enable Time Out input when "Sudah Pulang" is selected in Status Out
        function enableTimeOut(studentId) {
            const statusOutColumn = document.getElementById(`statusOut_${studentId}`);
            const timeOutField = document.getElementById(`timeOut_${studentId}`);
            // If "Sudah Pulang" is selected, enable the Time Out field
            if (statusOutColumn.querySelector('input[value="on_time"]').checked) {
                timeOutField.querySelector('input').disabled = false;
            }
        }

        // Function to clear Time Out input when "Belum Pulang" is selected in Status Out
        function clearTimeOut(studentId) {
            const statusOutColumn = document.getElementById(`statusOut_${studentId}`);
            const timeOutField = document.getElementById(`timeOut_${studentId}`);
            // If "Belum Pulang" is selected, disable the Time Out field
            if (statusOutColumn.querySelector('input[value="not_yet"]').checked) {
                timeOutField.querySelector('input').disabled = true;
                timeOutField.querySelector('input').value = ''; // Clear Time Out field
            }
        }

        function toggleTimeOut(studentId, radio) {
            const statusOutColumn = document.getElementById(`statusOut_${studentId}`);
            const timeOutField = document.getElementById(`timeOut_${studentId}`).querySelector('input');

            // Enable time out field if check-out status is on_time or late
            if (radio.value === 'on_time' || radio.value === 'late') {
                timeOutField.disabled = false; // Enable time out input
            } else {
                timeOutField.disabled = true; // Disable time out input if not "on_time" or "late"
                timeOutField.value = ''; // Clear time out field if status is "not_yet"
            }
        }

        // Function to calculate late status dynamically for each student
        function calculateLateStatus(input, studentId) {
            // Temukan row untuk siswa tertentu dengan ID yang sesuai
            const row = document.getElementById(`studentRow_${studentId}`);
            if (!row) return;

            // Ambil waktu akhir dari data atribut 'data-program-end-time' dari row
            const programEndTime = row.getAttribute('data-program-end-time');

            // Pastikan waktu akhir dan waktu keluar valid
            if (programEndTime === 'N/A' || !input.value) return; // Jika data tidak valid, keluar

            const checkOutTime = input.value; // Ambil waktu keluar
            const checkOutTimeParsed = new Date(`1970-01-01T${checkOutTime}:00`); // Parse waktu keluar
            const endTimeParsed = new Date(`1970-01-01T${programEndTime}`); // Parse waktu akhir program

            let lateStatus = 'On Time'; // Status default adalah tepat waktu
            let lateDuration = 0;

            // Membandingkan apakah waktu keluar melebihi waktu akhir
            if (checkOutTimeParsed > endTimeParsed) {
                lateStatus = 'Late'; // Jika terlambat
                lateDuration = Math.floor((checkOutTimeParsed - endTimeParsed) / 60000); // Durasi keterlambatan dalam menit
            }

            // Mengupdate nilai catatan
            const noteField = document.querySelector(`[name="attendance[${studentId}][note]"]`);

            if (lateStatus === 'Late') {
                noteField.value = `Late by ${lateDuration} minutes`; // Isi catatan jika terlambat
            } else {
                noteField.value = ''; // Kosongkan catatan jika tidak terlambat
            }

            // Mengirim nilai keterlambatan ke server jika perlu
            const lateInput = document.querySelector(`input[name="attendance[${studentId}][is_late]"]`);
            if (lateInput) {
                lateInput.value = lateStatus === 'Late'; // Set nilai is_late
            }

            // Kirim durasi keterlambatan jika ada
            const lateDurationInput = document.querySelector(`[name="attendance[${studentId}][late_duration]"]`);
            if (lateDurationInput) {
                lateDurationInput.value = lateDuration;
            }
        }
    </script>
</x-app-layout>
