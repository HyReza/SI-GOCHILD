<x-app-layout>
    {{-- SweetAlert for Success Message --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ $errors->first() }}",
                    showConfirmButton: true
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    showConfirmButton: true
                });
            });
        </script>
    @endif


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
                    Form Absensi Siswa
                </a>
            </li>
        </ol>
    </nav>

    <div class="container mx-auto p-4">
        <!-- Attendance Form -->
        <form id="attendanceForm" method="POST" action="{{ route('attendance.store') }}"
            class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
            @csrf

            <!-- Date Picker -->
            <div class="mb-4">
                <label for="date" class="block text-gray-700 dark:text-gray-200 font-semibold mb-2">Date</label>
                <input type="date" name="date_attendance" id="date"
                    class="w-full border border-gray-300 dark:border-gray-600 p-2 rounded-lg focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white @error('date_attendance') border-red-500 @enderror"
                    value="{{ old('date_attendance') }}" required>

                @error('date_attendance')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Service Selector -->
            <div class="mb-4">
                <label for="service" class="block text-gray-700 dark:text-gray-200 font-semibold mb-2">Service</label>
                <select name="service_id" id="service"
                    class="w-full border border-gray-300 dark:border-gray-600 p-2 rounded-lg focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:text-white">
                    <option value="">Select Service</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto">
                <table id="studentsTable" class="w-full border-collapse hidden">
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
                    </tbody>
                </table>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitFormButton"
                class="mt-6 bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg w-full md:w-auto">
                Submit Attendance
            </button>
        </form>
    </div>

    <script>
        // JavaScript to populate the students table and calculate late status
        document.getElementById('service').addEventListener('change', function() {
            const serviceId = this.value;

            fetch(`{{ route('attendance.list') }}?service_id=${serviceId}`)
                .then(response => response.json())
                .then(data => {
                    const studentTableBody = document.getElementById('studentsTableBody');
                    studentTableBody.innerHTML = ''; // Clear the table

                    // Display the table only if there are students
                    if (data.students.length > 0) {
                        document.getElementById('studentsTable').classList.remove('hidden');
                    } else {
                        document.getElementById('studentsTable').classList.add('hidden');
                    }

                    // Add students to the table
                    data.students.forEach((student, index) => {
                        const row = `
                            <tr class="dark:bg-gray-800">
                                <td class="border px-4 py-2">${index + 1}</td>
                                <td class="border px-4 py-2">${student.name || ''}</td>
                                <td class="border px-4 py-2">${student.number || ''} <span>${student.program_end_time || 'N/A'}</span></td>
                                <input type="hidden" name="attendance[${student.student_id}][is_late]" value="false">
                                <input type="hidden" name="attendance[${student.student_id}][late_duration]" value="">
                                <td class="border px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Present" required class="text-blue-600 focus:ring focus:ring-blue-200" onchange="toggleInputs(this, ${student.student_id}, '${student.program_end_time}')">
                                            <span class="ml-1 text-sm">Present</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Excused" class="text-yellow-500 focus:ring focus:ring-yellow-200" onchange="toggleInputs(this, ${student.student_id})">
                                            <span class="ml-1 text-sm">Excused</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Sick" class="text-red-500 focus:ring focus:ring-red-200" onchange="toggleInputs(this, ${student.student_id})">
                                            <span class="ml-1 text-sm">Sick</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Absent" class="text-gray-500 focus:ring focus:ring-gray-200" onchange="toggleInputs(this, ${student.student_id})">
                                            <span class="ml-1 text-sm">Absent</span>
                                        </label>
                                    </div>
                                </td>

                                <td id="statusOut_${student.student_id}" class="border px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="attendance[${student.student_id}][check_out_status]" value="not_yet" class="text-gray-500 focus:ring focus:ring-gray-200" onchange="clearTimeOut(${student.student_id})" disabled>
                                            <span class="ml-1 text-sm">Belum Pulang</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="attendance[${student.student_id}][check_out_status]" value="on_time" class="text-blue-600 focus:ring focus:ring-blue-200" onchange="enableTimeOut(${student.student_id})" disabled>
                                            <span class="ml-1 text-sm">Sudah Pulang</span>
                                        </label>
                                    </div>
                                </td>

                                <td id="timeIn_${student.student_id}" class="border px-4 py-2 text-center">
                                    <input type="time" name="attendance[${student.student_id}][check_in_time]" class="border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200 disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-200 dark:bg-gray-600"  disabled required>
                                </td>

                                <td id="timeOut_${student.student_id}" class="border px-4 py-2 text-center">
                                    <input type="time" name="attendance[${student.student_id}][check_out_time]" class="border border-gray-300 p-2 rounded-lg focus:ring focus:ring-blue-200 disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-200 dark:bg-gray-600"  disabled required onchange="calculateLateStatus(this, ${student.student_id}, '${student.program_end_time}')">
                                </td>

                                <td class="border px-4 py-2">
                                    <textarea name="attendance[${student.student_id}][note]" class="w-full border border-gray-300 dark:bg-gray-600 p-1 rounded-lg focus:ring focus:ring-blue-200 disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-200"></textarea>
                                </td>
                            </tr>
                        `;
                        studentTableBody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Error:', error));
        });

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

        function calculateLateStatus(input, studentId, programEndTime) {
            const checkOutTime = input.value; // Get the time entered in the Time Out field
            const checkOutTimeParsed = new Date(
                `1970-01-01T${checkOutTime}:00`); // Parse the Time Out value to Date (using a dummy date)

            // Parse the program's end time (this comes dynamically from the backend or JSON data)
            const endTimeParsed = new Date(
                `1970-01-01T${programEndTime}`); // Convert program end time to Date object (using a dummy date)

            let lateStatus = 'On Time'; // Default status is On Time
            let lateDuration = 0;

            // Check if Time Out is later than the program's end time
            if (checkOutTimeParsed > endTimeParsed) {
                lateStatus = 'Late'; // Status becomes "Late"
                lateDuration = Math.floor((checkOutTimeParsed - endTimeParsed) /
                    60000); // Calculate late duration in minutes
            }

            // Update the Note based on late status
            const noteField = document.querySelector(`[name="attendance[${studentId}][note]"]`);

            // If the student is late, show the late duration in the note field
            if (lateStatus === 'Late') {
                noteField.value = `Late by ${lateDuration} minutes`; // Set note as late
                const lateInput = document.querySelector(`input[name="attendance[${studentId}][is_late]"]`);
                if (lateInput) {
                    lateInput.value = true; // Set the value of is_late to true (boolean)
                }

                // Send the late duration to the server as well
                const lateDurationInput = document.querySelector(`[name="attendance[${studentId}][late_duration]"]`);
                if (lateDurationInput) {
                    lateDurationInput.value = lateDuration; // Set the late_duration input value
                }
            } else {
                // If not late, set the hidden input value to false (boolean)
                const lateInput = document.querySelector(`input[name="attendance[${studentId}][is_late]"]`);
                if (lateInput) {
                    lateInput.value = false; // Set the value of is_late to false (boolean)
                }
                noteField.value = ''; // Clear note field

                // Clear late_duration
                const lateDurationInput = document.querySelector(`[name="attendance[${studentId}][late_duration]"]`);
                if (lateDurationInput) {
                    lateDurationInput.value = ''; // Clear late_duration input field
                }
            }
        }

        // Handle form submission confirmation
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: 'Konfirmasi Pengiriman Absensi',
                text: "Apakah Anda yakin ingin mengirim absensi ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Tunggu sebentar',
                        allowOutsideClick: false, // Disable clicking outside to close
                        didOpen: () => {
                            Swal.showLoading(); // Show the loading animation
                        }
                    });

                    // Submit the form after the user confirms
                    document.getElementById('attendanceForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
