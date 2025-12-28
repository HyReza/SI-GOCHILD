@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;
    use App\Models\Report; // Model Kurikulum
    use App\Models\StudentDevelopmentReport; // Model Tumbuh Kembang
    use App\Models\Attendance; // Model Absensi

    // --- 1. INISIASI VARIABEL DEFAULT ---
    $student = Auth::guard('student')->user();

    $totalReports = 0;
    $totalAbsence = 0;
    $isSigned = true; // Default aman
    $recentReport = null; // Rapor terbaru (objek)
    $recentType = ''; // 'curriculum' atau 'development'

    $genderText = 'Pengguna';
    $genderIcon = 'fas fa-user';
    $studentPhotoUrl = '';
    $studentName = 'Pengguna Tidak Dikenal';
    $studentNumber = 'N/A';

    // --- 2. SETUP FILTER ---
    // Ambil dari request, jika tidak ada pakai waktu sekarang
    $currentMonth = request('month', Carbon::now()->month);
    $currentYear = request('year', Carbon::now()->year);

    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
    $years = range(Carbon::now()->year, Carbon::now()->year - 4);

    // --- 3. LOGIKA UTAMA ---
    if ($student) {
        $studentName = $student->student_name ?? $studentName;
        $studentNumber = $student->student_number ?? $studentNumber;

        // Gender Logic
        $gender = strtoupper($student->gender ?? 'U');
        if ($gender === '1' || $gender === 'L') {
            $genderText = 'Laki-laki';
            $genderIcon = 'fas fa-mars';
        } elseif ($gender === '2' || $gender === 'P') {
            $genderText = 'Perempuan';
            $genderIcon = 'fas fa-venus';
        }

        // --- FETCH DATA DENGAN ERROR HANDLING ---
        try {
            // A. HITUNG TOTAL LAPORAN (FILTERED)
            // Hitung Rapor Kurikulum di bulan & tahun yang dipilih
            $countKurikulum = Report::where('student_id', $student->id)
                ->whereMonth('report_date', $currentMonth)
                ->whereYear('report_date', $currentYear)
                ->count();

            // Hitung Rapor Tumbuh Kembang di bulan & tahun yang dipilih
            $countDDTK = StudentDevelopmentReport::where('student_id', $student->id)
                ->whereMonth('report_date', $currentMonth)
                ->whereYear('report_date', $currentYear)
                ->count();

            $totalReports = $countKurikulum + $countDDTK;

            // B. HITUNG ABSENSI (PERBAIKAN DISINI)
            // Tambahkan whereMonth dan whereYear agar angka berubah sesuai filter
            // Asumsi menggunakan created_at atau kolom tanggal yang ada di tabel attendances
            $totalAbsence = Attendance::where('activity_transaction_id', $student->id)
                ->whereIn('check_in_status', ['Sick', 'Excused', 'Absent'])
                ->whereMonth('created_at', $currentMonth) // Filter Bulan
                ->whereYear('created_at', $currentYear) // Filter Tahun
                ->count();

            // C. CEK RAPOR TERAKHIR & STATUS TTD (GLOBAL / TIDAK TERFILTER)
            // Bagian ini biasanya tetap global untuk mengingatkan ortu rapor terbaru
            $lastKurikulum = Report::where('student_id', $student->id)->latest('report_date')->first();
            $lastDDTK = StudentDevelopmentReport::where('student_id', $student->id)->latest('report_date')->first();

            if ($lastKurikulum && $lastDDTK) {
                // Bandingkan tanggal
                if ($lastKurikulum->report_date >= $lastDDTK->report_date) {
                    $recentReport = $lastKurikulum;
                    $recentType = 'curriculum';
                } else {
                    $recentReport = $lastDDTK;
                    $recentType = 'development';
                }
            } elseif ($lastKurikulum) {
                $recentReport = $lastKurikulum;
                $recentType = 'curriculum';
            } elseif ($lastDDTK) {
                $recentReport = $lastDDTK;
                $recentType = 'development';
            }

            // Cek status TTD dari pemenang (latest report)
            if ($recentReport) {
                $isSigned = !empty($recentReport->parent_signature);
            }
        } catch (\Exception $e) {
            // Jika tabel belum ada atau error query, set default agar tidak crash
            \Illuminate\Support\Facades\Log::error('Dashboard Error: ' . $e->getMessage());
            $totalReports = 0;
            $totalAbsence = 0;
            $recentReport = null;
            $isSigned = true;
        }

        // --- FOTO PROFIL ---
        $avatarName = urlencode($studentName === 'Pengguna Tidak Dikenal' ? 'User' : $studentName);
        $studentPhotoUrl = "https://ui-avatars.com/api/?name={$avatarName}&background=4f46e5&color=fff&bold=true";

        try {
            if (!empty($student->user_photo) && Storage::disk('public')->exists($student->user_photo)) {
                $studentPhotoUrl = asset('storage/' . $student->user_photo);
            }
        } catch (\Exception $e) {
        }
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center">
            <i class="fas fa-home mr-3 text-indigo-600"></i>
            {{ __('Dashboard Siswa/Wali') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($student)
                {{-- AREA FILTER BULAN & TAHUN --}}
                <div class="flex flex-col sm:flex-row justify-end mb-6 gap-4 sm:gap-0">
                    <form action="{{ route('dashboard') }}" method="GET"
                        class="flex items-center space-x-2 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex items-center text-gray-600 text-sm font-semibold px-2">
                            <i class="fas fa-filter mr-2 text-indigo-500"></i> Filter Periode:
                        </div>

                        {{-- Select Bulan --}}
                        <select name="month" onchange="this.form.submit()"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            @foreach ($months as $key => $val)
                                <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Select Tahun --}}
                        <select name="year" onchange="this.form.submit()"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            @foreach ($years as $y)
                                <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- INFO SISWA --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl mb-8 border-l-4 border-indigo-600">
                    <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img class="h-20 w-20 rounded-full object-cover border-4 border-indigo-100"
                                    src="{{ $studentPhotoUrl }}" alt="Foto Siswa">
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Selamat Datang, Wali dari</p>
                                <h3 class="text-3xl font-extrabold text-gray-900">{{ $studentName }}</h3>
                                <p class="text-sm text-gray-500 font-mono mt-1">NIS: {{ $studentNumber }}</p>
                            </div>
                        </div>

                        <div class="mt-4 md:mt-0 text-left md:text-right">
                            <span
                                class="inline-flex items-center px-3 py-1 text-sm rounded-full bg-indigo-50 text-indigo-700 font-semibold">
                                <i class="{{ $genderIcon }} mr-2"></i> {{ $genderText }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- CARD TOTAL RAPOR (TERFILTER) --}}
                    <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-blue-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Laporan ({{ $months[$currentMonth] }}
                                    {{ $currentYear }})</p>
                                <h4 class="text-3xl font-bold text-gray-900 mt-1">{{ $totalReports }}</h4>
                                <p class="text-xs text-gray-400 mt-1">Gabungan Kurikulum & DDTK</p>
                            </div>
                            <i class="fas fa-book-open text-4xl text-blue-200"></i>
                        </div>
                    </div>

                    {{-- CARD STATUS RAPOR TERAKHIR (GLOBAL) --}}
                    <div
                        class="bg-white p-6 rounded-xl shadow-md border-b-4 {{ $isSigned ? 'border-green-500' : 'border-red-500' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Status Pengesahan Terakhir</p>
                                @if ($recentReport)
                                    @if ($isSigned)
                                        <h4 class="text-2xl font-bold text-green-700 mt-1">SUDAH SAH</h4>
                                        <p class="text-xs text-gray-500">Oleh:
                                            {{ $recentReport->parent_name ?? 'Wali' }}</p>
                                    @else
                                        <h4 class="text-2xl font-bold text-red-700 mt-1">PERLU TTD</h4>
                                        <p class="text-xs text-red-500">Rapor terbaru belum disahkan.</p>
                                    @endif
                                @else
                                    <h4 class="text-2xl font-bold text-gray-700 mt-1">N/A</h4>
                                    <p class="text-xs text-gray-500">Belum ada rapor diterbitkan.</p>
                                @endif
                            </div>
                            <i
                                class="fas fa-file-signature text-4xl {{ $isSigned ? 'text-green-200' : 'text-red-200' }}"></i>
                        </div>
                    </div>

                    {{-- CARD ABSENSI --}}
                    <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-orange-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Absensi ({{ $months[$currentMonth] }}
                                    {{ $currentYear }})</p>
                                <h4 class="text-3xl font-bold text-gray-900 mt-1">{{ $totalAbsence }} <span
                                        class="text-lg text-gray-400">hari</span></h4>
                                <p class="text-xs text-gray-400 mt-1">Izin / Sakit / Alpha</p>
                            </div>
                            <i class="fas fa-calendar-times text-4xl text-orange-200"></i>
                        </div>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Akses Cepat</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- TOMBOL AKSES CEPAT --}}
                    <a href="{{ route('student.report.history') }}"
                        class="bg-white p-6 rounded-xl shadow-md flex items-center space-x-4 border border-gray-100 hover:bg-indigo-50 hover:border-indigo-300 transition-all group">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-indigo-100 rounded-full group-hover:bg-indigo-600 transition">
                            <i class="fas fa-scroll text-indigo-600 text-xl group-hover:text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Riwayat Rapor</h4>
                            <p class="text-xs text-gray-500">Kurikulum & Tumbuh Kembang</p>
                        </div>
                    </a>

                    <a href="{{ route('student.attendance.index') }}"
                        class="bg-white p-6 rounded-xl shadow-md flex items-center space-x-4 border border-gray-100 hover:bg-green-50 hover:border-green-300 transition-all group">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-green-100 rounded-full group-hover:bg-green-600 transition">
                            <i class="fas fa-clipboard-list text-green-600 text-xl group-hover:text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Rekap Absensi</h4>
                            <p class="text-xs text-gray-500">Cek rekap kehadiran harian.</p>
                        </div>
                    </a>

                    <a href="{{ route('student.daily-report.index') }}"
                        class="bg-white p-6 rounded-xl shadow-md flex items-center space-x-4 border border-gray-100 hover:bg-teal-50 hover:border-teal-300 transition-all group">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-teal-100 rounded-full group-hover:bg-teal-600 transition">
                            <i class="fas fa-feather-alt text-teal-600 text-xl group-hover:text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Kegiatan Harian</h4>
                            <p class="text-xs text-gray-500">Laporan aktivitas harian anak.</p>
                        </div>
                    </a>

                    <a href="{{ route('student.measurement.index') }}"
                        class="bg-white p-6 rounded-xl shadow-md flex items-center space-x-4 border border-gray-100 hover:bg-purple-50 hover:border-purple-300 transition-all group">
                        <div
                            class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-purple-100 rounded-full group-hover:bg-purple-600 transition">
                            <i class="fas fa-chart-line text-purple-600 text-xl group-hover:text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Grafik KMS</h4>
                            <p class="text-xs text-gray-500">Monitoring berat & tinggi badan.</p>
                        </div>
                    </a>
                </div>

                {{-- NOTIFIKASI JIKA BELUM TANDA TANGAN --}}
                @if (!$isSigned && $recentReport)
                    <div
                        class="mt-8 p-6 bg-red-100 border border-red-300 rounded-xl shadow-lg flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                            <div>
                                <p class="font-bold text-red-800">Tindakan Diperlukan: Pengesahan Rapor!</p>
                                <p class="text-sm text-red-700">
                                    Laporan terbaru
                                    <strong>({{ $recentType == 'curriculum' ? 'Pembelajaran' : 'Tumbuh Kembang' }})</strong>
                                    pada tanggal {{ $recentReport->report_date->format('d M Y') }} belum Anda sahkan.
                                </p>
                            </div>
                        </div>

                        {{-- TOMBOL DINAMIS BERDASARKAN TIPE RAPOR --}}
                        @if ($recentType == 'curriculum')
                            <a href="{{ route('student.report.show', $recentReport->id) }}"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-red-700 transition w-full md:w-auto text-center shadow-md">
                                Tanda Tangan Rapor Kurikulum
                            </a>
                        @else
                            {{-- Gunakan Route Baru untuk Tumbuh Kembang --}}
                            <a href="{{ route('student.report.development.show', $recentReport->id) }}"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-red-700 transition w-full md:w-auto text-center shadow-md">
                                Tanda Tangan Rapor DDTK
                            </a>
                        @endif
                    </div>
                @endif
            @elseif(Auth::guard('web')->check())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <i class="fas fa-user-shield text-indigo-600 mr-2"></i>
                        {{ __('Anda masuk sebagai Admin/Pengguna Standar.') }}
                        <p class="text-sm text-gray-500 mt-2">Akses dashboard ini hanya untuk pengguna dengan guard
                            'student'.</p>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <i class="fas fa-sign-in-alt text-gray-600 mr-2"></i> {{ __('Anda belum login.') }}
                        <p class="text-sm text-gray-500 mt-2">Silakan login sebagai wali siswa untuk mengakses
                            informasi
                            ini.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
