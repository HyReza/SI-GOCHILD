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
                            Formulir Absensi Siswa
                        </a>
                    </li>
                </ol>
            </nav>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        📝 Formulir Absensi Siswa
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Pilih tanggal dan layanan untuk memulai pengisian absensi siswa. Data siswa akan muncul secara
                        otomatis.
                    </p>
                </div>

                <div class="p-6 lg:p-8">
                    <form id="attendanceForm" method="POST" action="{{ route('attendance.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal
                                    Absensi</label>
                                <input type="date" name="date_attendance" id="date"
                                    class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 @error('date_attendance') border-red-500 @enderror"
                                    value="{{ old('date_attendance', now()->format('Y-m-d')) }}" required>
                                @error('date_attendance')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="service"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih
                                    Layanan</label>
                                <select name="service_id" id="service"
                                    class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                                    <option value="">-- Pilih Layanan --</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="studentTableContainer" class="mt-6 hidden">
                            <div class="overflow-x-auto relative shadow-md sm:rounded-lg border dark:border-gray-700">
                                <table id="studentsTable"
                                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
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
                                            <th scope="col" class="px-20 py-3">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTableBody">
                                    </tbody>
                                </table>
                                <div id="noStudentsMessage" class="text-center p-8 bg-white dark:bg-gray-800 hidden">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak Ada Siswa</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada siswa yang
                                        terdaftar pada layanan ini.</p>
                                </div>
                                <div id="loadingIndicator" class="text-center p-8 bg-white dark:bg-gray-800 hidden">
                                    <p class="text-gray-500 dark:text-gray-400">Memuat data siswa...</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8">
                            <button type="submit" id="submitFormButton"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                Kirim Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


    {{-- =================================================================================== --}}
    {{-- BAGIAN JAVASCRIPT --}}
    {{-- Kode ini mengatur semua interaktivitas di halaman ini. --}}
    {{-- =================================================================================== --}}
    <script>
        // Event listener saat pilihan 'Layanan' diubah
        document.getElementById('service').addEventListener('change', function() {
            const serviceId = this.value; // Ambil ID layanan yang dipilih
            const studentTableContainer = document.getElementById('studentTableContainer');
            const studentsTable = document.getElementById('studentsTable');
            const studentTableBody = document.getElementById('studentsTableBody');
            const noStudentsMessage = document.getElementById('noStudentsMessage');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const submitButton = document.getElementById('submitFormButton');

            // Kosongkan tabel dan sembunyikan semua elemen
            studentTableBody.innerHTML = '';
            studentTableContainer.classList.remove('hidden');
            studentsTable.classList.add('hidden');
            noStudentsMessage.classList.add('hidden');
            loadingIndicator.classList.add('hidden');
            submitButton.disabled = true; // Nonaktifkan tombol submit saat data baru dimuat

            if (!serviceId) {
                studentTableContainer.classList.add('hidden'); // Sembunyikan jika tidak ada layanan dipilih
                return;
            }

            // Tampilkan indikator loading
            loadingIndicator.classList.remove('hidden');

            // Ambil data siswa dari server berdasarkan service_id
            fetch(`{{ route('attendance.list') }}?service_id=${serviceId}`)
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.classList.add('hidden'); // Sembunyikan loading

                    // Jika ada siswa, tampilkan tabel dan isi datanya
                    if (data.students && data.students.length > 0) {
                        studentsTable.classList.remove('hidden'); // Tampilkan tabel
                        submitButton.disabled = false; // Aktifkan kembali tombol submit

                        data.students.forEach((student, index) => {
                            // Membuat satu baris (row) untuk setiap siswa
                            const row = `
                                <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">${index + 1}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">${student.name || ''}</td>
                                    <td class="px-6 py-4">${student.number || ''}
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Pulang: ${student.program_end_time || 'N/A'}</span>
                                    </td>

                                    {{-- Input tersembunyi untuk menyimpan status keterlambatan --}}
                                    <input type="hidden" name="attendance[${student.student_id}][is_late]" value="false">
                                    <input type="hidden" name="attendance[${student.student_id}][late_duration]" value="">

                                    {{-- Kolom Status Masuk (Radio Button) --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4">
                                            <label class="inline-flex items-center"><input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Present" required class="form-radio text-blue-600" onchange="toggleInputs(this, ${student.student_id})"><span class="ml-2 text-sm">Hadir</span></label>
                                            <label class="inline-flex items-center"><input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Excused" class="form-radio text-yellow-500" onchange="toggleInputs(this, ${student.student_id})"><span class="ml-2 text-sm">Izin</span></label>
                                            <label class="inline-flex items-center"><input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Sick" class="form-radio text-red-500" onchange="toggleInputs(this, ${student.student_id})"><span class="ml-2 text-sm">Sakit</span></label>
                                            <label class="inline-flex items-center"><input type="radio" name="attendance[${student.student_id}][check_in_status]" value="Absent" class="form-radio text-gray-500" onchange="toggleInputs(this, ${student.student_id})"><span class="ml-2 text-sm">Alpa</span></label>
                                        </div>
                                    </td>

                                    {{-- Kolom Status Pulang (Radio Button) --}}
                                    <td class="px-6 py-4 text-center" id="statusOut_${student.student_id}">
                                        <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4">
                                            <label class="inline-flex items-center"><input type="radio" name="attendance[${student.student_id}][check_out_status]" value="not_yet" class="form-radio text-gray-500" onchange="clearTimeOut(${student.student_id})" disabled><span class="ml-2 text-sm">Belum</span></label>
                                            <label class="inline-flex items-center"><input type="radio" name="attendance[${student.student_id}][check_out_status]" value="on_time" class="form-radio text-green-600" onchange="enableTimeOut(${student.student_id})" disabled><span class="ml-2 text-sm">Sudah</span></label>
                                        </div>
                                    </td>

                                    {{-- Kolom Jam Masuk --}}
                                    <td class="px-6 py-4" id="timeIn_${student.student_id}">
                                        <input type="time" name="attendance[${student.student_id}][check_in_time]" class="w-full text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 disabled:opacity-50 disabled:bg-gray-200 dark:disabled:bg-gray-700/50" disabled required>
                                    </td>

                                    {{-- Kolom Jam Pulang --}}
                                    <td class="px-6 py-4" id="timeOut_${student.student_id}">
                                        <input type="time" name="attendance[${student.student_id}][check_out_time]" class="w-full text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 disabled:opacity-50 disabled:bg-gray-200 dark:disabled:bg-gray-700/50" disabled onchange="calculateLateStatus(this, ${student.student_id}, '${student.program_end_time}')">
                                    </td>

                                    {{-- Kolom Catatan --}}
                                    <td class="px-5 py-4">
                                        <textarea name="attendance[${student.student_id}][note]" class="w-full text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2" rows="1"></textarea>
                                    </td>
                                </tr>
                            `;
                            studentTableBody.innerHTML += row; // Tambahkan baris ke dalam tabel
                        });
                    } else {
                        // Jika tidak ada siswa, tampilkan pesan
                        noStudentsMessage.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching student data:', error);
                    loadingIndicator.classList.add('hidden');
                    // Tampilkan pesan error jika fetch gagal
                    noStudentsMessage.innerHTML =
                        '<p class="text-red-500">Gagal memuat data siswa. Silakan coba lagi.</p>';
                    noStudentsMessage.classList.remove('hidden');
                });
        });

        /**
         * Fungsi untuk mengatur input Jam Masuk, Status Pulang, dan Jam Pulang
         * berdasarkan pilihan Status Masuk.
         */
        function toggleInputs(radio, studentId) {
            const checkInStatus = radio.value;
            const statusOutRadios = document.querySelectorAll(`#statusOut_${studentId} input[type="radio"]`);
            const timeInField = document.querySelector(`#timeIn_${studentId} input[type="time"]`);
            const timeOutField = document.querySelector(`#timeOut_${studentId} input[type="time"]`);

            // Jika statusnya 'Hadir'
            if (checkInStatus === 'Present') {
                timeInField.disabled = false; // Aktifkan input jam masuk
                statusOutRadios.forEach(input => input.disabled = false); // Aktifkan radio status pulang

                // Set 'Belum Pulang' sebagai default
                const notYetRadio = document.querySelector(`#statusOut_${studentId} input[value="not_yet"]`);
                if (notYetRadio) notYetRadio.checked = true;

                // Pastikan jam pulang nonaktif
                timeOutField.disabled = true;
                timeOutField.value = '';
            } else {
                // Jika statusnya bukan 'Hadir' (Sakit, Izin, Alpa)
                timeInField.disabled = true;
                timeInField.value = '';
                statusOutRadios.forEach(input => {
                    input.disabled = true;
                    input.checked = false;
                });
                timeOutField.disabled = true;
                timeOutField.value = '';
            }
        }

        // Fungsi untuk mengaktifkan input Jam Pulang jika 'Sudah Pulang' dipilih
        function enableTimeOut(studentId) {
            const timeOutField = document.querySelector(`#timeOut_${studentId} input[type="time"]`);
            timeOutField.disabled = false;
        }

        // Fungsi untuk menonaktifkan dan mengosongkan Jam Pulang jika 'Belum Pulang' dipilih
        function clearTimeOut(studentId) {
            const timeOutField = document.querySelector(`#timeOut_${studentId} input[type="time"]`);
            timeOutField.disabled = true;
            timeOutField.value = '';
        }

        /**
         * Fungsi untuk menghitung keterlambatan saat jam pulang diisi.
         * Jika jam pulang > jadwal pulang, maka dianggap terlambat.
         */
        function calculateLateStatus(input, studentId, programEndTime) {
            // Jika jadwal pulang tidak ada, hentikan fungsi
            if (!programEndTime) return;

            const checkOutTime = input.value;
            const noteField = document.querySelector(`textarea[name="attendance[${studentId}][note]"]`);
            const isLateField = document.querySelector(`input[name="attendance[${studentId}][is_late]"]`);
            const lateDurationField = document.querySelector(`input[name="attendance[${studentId}][late_duration]"]`);

            // Buat objek Date untuk membandingkan waktu (tanggal diabaikan)
            const endTimeParsed = new Date(`1970-01-01T${programEndTime}`);
            const checkOutTimeParsed = new Date(`1970-01-01T${checkOutTime}:00`);

            // Bandingkan waktu
            if (checkOutTimeParsed > endTimeParsed) {
                // Hitung durasi keterlambatan dalam menit
                const lateDuration = Math.floor((checkOutTimeParsed - endTimeParsed) / 60000);

                // Isi field catatan, is_late, dan late_duration
                noteField.value = `Terlambat dijemput ${lateDuration} menit.`;
                isLateField.value = 'true';
                lateDurationField.value = lateDuration;
            } else {
                // Jika tidak terlambat, kosongkan field terkait
                noteField.value = '';
                isLateField.value = 'false';
                lateDurationField.value = '';
            }
        }

        // Event listener untuk tombol submit form
        document.getElementById('submitFormButton').addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah form dikirim secara langsung

            // Tampilkan dialog konfirmasi dengan SweetAlert
            Swal.fire({
                title: 'Konfirmasi Pengiriman',
                text: "Apakah Anda yakin semua data absensi sudah benar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                // Jika pengguna menekan tombol 'Ya, Kirim!'
                if (result.isConfirmed) {
                    // Tampilkan loading sebelum mengirim form
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim form
                    document.getElementById('attendanceForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
