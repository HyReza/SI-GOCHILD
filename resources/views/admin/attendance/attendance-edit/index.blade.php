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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <nav aria-label="Breadcrumb" class="flex mb-8">
                <ol
                    class="flex overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                    <li class="flex items-center">
                        <a href="{{ route('attendance.index') }}"
                            class="flex h-10 items-center gap-1.5 bg-gray-100 dark:bg-gray-800/50 px-4 transition hover:text-gray-900 dark:hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="ms-1.5 text-xs font-medium"> Daftar Absensi </span>
                        </a>
                    </li>
                    <li class="relative flex items-center">
                        <span
                            class="absolute inset-y-0 -start-px h-10 w-4 bg-gray-100 dark:bg-gray-800/50 [clip-path:_polygon(0_0,_0%_100%,_100%_50%)] rtl:rotate-180"></span>
                        <a href="#"
                            class="flex h-10 items-center bg-white dark:bg-gray-800 pe-4 ps-8 text-xs font-medium transition text-blue-600 dark:text-blue-400">
                            Edit Absensi Siswa
                        </a>
                    </li>
                </ol>
            </nav>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        📝 Edit Absensi Siswa
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Anda sedang mengubah data absensi untuk tanggal <strong
                            class="text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($attendanceTransaction->date_attendance)->isoFormat('D MMMM YYYY') }}</strong>.
                    </p>
                </div>

                <div class="p-6 lg:p-8">
                    <form id="attendanceForm" method="POST"
                        action="{{ route('attendance.update', $attendanceTransaction->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                                    Absensi</label>
                                <input type="date" name="date_attendance" id="date"
                                    class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 @error('date_attendance') border-red-500 @enderror"
                                    value="{{ old('date_attendance', $attendanceTransaction->date_attendance) }}"
                                    required>
                                @error('date_attendance')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="service"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Layanan</label>
                                <select name="service_id" id="service"
                                    class="w-full bg-gray-200 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg p-2.5 cursor-not-allowed"
                                    disabled>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" @selected($service->id == $attendanceTransaction->service_id)>
                                            {{ $service->service_name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Layanan tidak dapat diubah pada halaman edit.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg border dark:border-gray-700">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">No</th>
                                        <th scope="col" class="px-6 py-3">Nama Siswa</th>
                                        <th scope="col" class="px-6 py-3">ID Siswa & Jadwal</th>
                                        <th scope="col" class="px-6 py-3 text-center">Status Masuk</th>
                                        <th scope="col" class="px-6 py-3 text-center">Status Pulang</th>
                                        <th scope="col" class="px-6 py-3 text-center">Jam Masuk</th>
                                        <th scope="col" class="px-6 py-3 text-center">Jam Pulang</th>
                                        <th scope="col" class="px-6 py-3">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendanceTransaction->attendances as $attendance)
                                        <tr id="studentRow_{{ $attendance->activityTransaction->student->id }}"
                                            data-program-end-time="{{ $attendance->activityTransaction->program->end_time ?? 'N/A' }}"
                                            class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                {{ $loop->iteration }}</td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                {{ $attendance->activityTransaction->student->student_name }}</td>
                                            <td class="px-6 py-4">
                                                {{ $attendance->activityTransaction->student->student_number }}
                                                <span class="block text-xs text-gray-500 dark:text-gray-400">Pulang:
                                                    {{ $attendance->activityTransaction->program->end_time ?? 'N/A' }}</span>
                                            </td>

                                            {{-- Input tersembunyi untuk menyimpan status keterlambatan --}}
                                            <input type="hidden"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][is_late]"
                                                value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.is_late', $attendance->is_late ? 'true' : 'false') }}">
                                            <input type="hidden"
                                                name="attendance[{{ $attendance->activityTransaction->student->id }}][late_duration]"
                                                value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.late_duration', $attendance->late_duration ?? '') }}">

                                            {{-- Kolom Status Masuk --}}
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4">
                                                    <label class="inline-flex items-center"><input type="radio"
                                                            name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                            value="Present" @checked($attendance->check_in_status == 'Present') required
                                                            class="form-radio text-blue-600"
                                                            onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})"><span
                                                            class="ml-2 text-sm">Hadir</span></label>
                                                    <label class="inline-flex items-center"><input type="radio"
                                                            name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                            value="Excused" @checked($attendance->check_in_status == 'Excused')
                                                            class="form-radio text-yellow-500"
                                                            onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})"><span
                                                            class="ml-2 text-sm">Izin</span></label>
                                                    <label class="inline-flex items-center"><input type="radio"
                                                            name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                            value="Sick" @checked($attendance->check_in_status == 'Sick')
                                                            class="form-radio text-red-500"
                                                            onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})"><span
                                                            class="ml-2 text-sm">Sakit</span></label>
                                                    <label class="inline-flex items-center"><input type="radio"
                                                            name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_status]"
                                                            value="Absent" @checked($attendance->check_in_status == 'Absent')
                                                            class="form-radio text-gray-500"
                                                            onchange="toggleInputs(this, {{ $attendance->activityTransaction->student->id }})"><span
                                                            class="ml-2 text-sm">Alpa</span></label>
                                                </div>
                                            </td>

                                            {{-- Kolom Status Pulang --}}
                                            <td class="px-6 py-4 text-center"
                                                id="statusOut_{{ $attendance->activityTransaction->student->id }}">
                                                <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4">
                                                    <label class="inline-flex items-center"><input type="radio"
                                                            name="attendance[{{ $attendance->activityTransaction->student->id }}][check_out_status]"
                                                            value="not_yet" @checked($attendance->check_out_status == 'not_yet')
                                                            class="form-radio text-gray-500"
                                                            onchange="toggleTimeOut({{ $attendance->activityTransaction->student->id }}, this)"><span
                                                            class="ml-2 text-sm">Belum</span></label>
                                                    <label class="inline-flex items-center"><input type="radio"
                                                            name="attendance[{{ $attendance->activityTransaction->student->id }}][check_out_status]"
                                                            value="on_time" @checked($attendance->check_out_status == 'on_time' || $attendance->check_out_status == 'late')
                                                            class="form-radio text-green-600"
                                                            onchange="toggleTimeOut({{ $attendance->activityTransaction->student->id }}, this)"><span
                                                            class="ml-2 text-sm">Sudah</span></label>
                                                </div>
                                            </td>

                                            {{-- Kolom Jam Masuk --}}
                                            <td class="px-6 py-4">
                                                <input type="time"
                                                    name="attendance[{{ $attendance->activityTransaction->student->id }}][check_in_time]"
                                                    value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.check_in_time', $attendance->check_in_time) }}"
                                                    class="w-full text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 disabled:opacity-50 disabled:bg-gray-200 dark:disabled:bg-gray-700/50"
                                                    required>
                                            </td>

                                            {{-- Kolom Jam Pulang --}}
                                            <td class="px-6 py-4">
                                                <input type="time"
                                                    name="attendance[{{ $attendance->activityTransaction->student->id }}][check_out_time]"
                                                    value="{{ old('attendance.' . $attendance->activityTransaction->student->id . '.check_out_time', $attendance->check_out_time) }}"
                                                    class="w-full text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 disabled:opacity-50 disabled:bg-gray-200 dark:disabled:bg-gray-700/50"
                                                    onchange="calculateLateStatus(this, {{ $attendance->activityTransaction->student->id }})">
                                            </td>

                                            {{-- Kolom Catatan --}}
                                            <td class="px-6 py-4">
                                                <textarea name="attendance[{{ $attendance->activityTransaction->student->id }}][note]"
                                                    class="w-full text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2"
                                                    rows="1">{{ old('attendance.' . $attendance->activityTransaction->student->id . '.note', $attendance->note) }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button type="submit" id="submitFormButton"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                                Perbarui Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


    {{-- =================================================================================== --}}
    {{-- BAGIAN JAVASCRIPT --}}
    {{-- Kode ini mengatur semua interaktivitas di halaman edit. --}}
    {{-- =================================================================================== --}}
    <script>
        /**
         * Fungsi ini dijalankan saat halaman selesai dimuat.
         * Tujuannya adalah untuk mengatur kondisi awal semua input form (enable/disable)
         * berdasarkan data yang sudah ada.
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua baris siswa dari tabel
            const studentRows = document.querySelectorAll('tr[id^="studentRow_"]');

            studentRows.forEach(row => {
                // Ekstrak ID siswa dari ID baris
                const studentId = row.id.split('_')[1];

                // Temukan radio button 'Status Masuk' yang terpilih untuk siswa ini
                const checkedInStatusRadio = document.querySelector(
                    `input[name="attendance[${studentId}][check_in_status]"]:checked`);
                if (checkedInStatusRadio) {
                    toggleInputs(checkedInStatusRadio, studentId);
                }

                // Temukan radio button 'Status Pulang' yang terpilih untuk siswa ini
                const checkedOutStatusRadio = document.querySelector(
                    `input[name="attendance[${studentId}][check_out_status]"]:checked`);
                if (checkedOutStatusRadio) {
                    toggleTimeOut(studentId, checkedOutStatusRadio);
                }
            });
        });

        /**
         * Fungsi untuk mengatur input Jam Masuk dan Status Pulang
         * berdasarkan pilihan Status Masuk. Dipanggil saat halaman dimuat dan saat ada perubahan.
         */
        function toggleInputs(radio, studentId) {
            const checkInStatus = radio.value;
            const statusOutRadios = document.querySelectorAll(`#statusOut_${studentId} input[type="radio"]`);
            const timeInField = document.querySelector(`input[name="attendance[${studentId}][check_in_time]"]`);
            const timeOutField = document.querySelector(`input[name="attendance[${studentId}][check_out_time]"]`);

            if (checkInStatus === 'Present') {
                timeInField.disabled = false;
                statusOutRadios.forEach(input => input.disabled = false);
            } else {
                timeInField.disabled = true;
                // timeInField.value = ''; // Sebaiknya jangan dikosongkan saat edit, biarkan data lama

                statusOutRadios.forEach(input => {
                    input.disabled = true;
                    // input.checked = false; // Jangan di-uncheck, biarkan data lama
                });

                timeOutField.disabled = true;
                // timeOutField.value = ''; // Biarkan data lama
            }
        }

        /**
         * Fungsi untuk mengatur input Jam Pulang berdasarkan pilihan Status Pulang.
         * Dipanggil saat halaman dimuat dan saat ada perubahan.
         */
        function toggleTimeOut(studentId, radio) {
            const timeOutField = document.querySelector(`input[name="attendance[${studentId}][check_out_time]"]`);

            if (radio.value === 'on_time') { // 'on_time' mencakup 'late' juga
                timeOutField.disabled = false;
            } else {
                timeOutField.disabled = true;
                // timeOutField.value = ''; // Sebaiknya jangan dikosongkan saat edit
            }
        }

        /**
         * Fungsi untuk menghitung keterlambatan secara dinamis.
         * Jika jam pulang > jadwal pulang, maka dianggap terlambat.
         */
        function calculateLateStatus(input, studentId) {
            const row = document.getElementById(`studentRow_${studentId}`);
            if (!row) return;

            const programEndTime = row.getAttribute('data-program-end-time');
            if (programEndTime === 'N/A' || !input.value) return;

            const checkOutTime = input.value;
            const noteField = document.querySelector(`textarea[name="attendance[${studentId}][note]"]`);
            const isLateField = document.querySelector(`input[name="attendance[${studentId}][is_late]"]`);
            const lateDurationField = document.querySelector(`input[name="attendance[${studentId}][late_duration]"]`);

            const endTimeParsed = new Date(`1970-01-01T${programEndTime}`);
            const checkOutTimeParsed = new Date(`1970-01-01T${checkOutTime}:00`);

            if (checkOutTimeParsed > endTimeParsed) {
                const lateDuration = Math.floor((checkOutTimeParsed - endTimeParsed) / 60000);
                noteField.value = `Terlambat dijemput ${lateDuration} menit.`;
                isLateField.value = 'true';
                lateDurationField.value = lateDuration;
            } else {
                noteField.value = '';
                isLateField.value = 'false';
                lateDurationField.value = '';
            }
        }

        // Event listener untuk tombol submit form, memberikan konfirmasi sebelum update
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah form dikirim secara langsung

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: "Apakah Anda yakin ingin menyimpan perubahan pada absensi ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan Perubahan...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('attendanceForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
