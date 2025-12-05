<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Service;
use App\Models\Student;
use App\Models\ActivityTransaction;
use App\Models\AttendanceTransaction;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AttendanceTransaction::with('attendances', 'service')->orderBy('date_attendance', 'desc');

        // Memfilter berdasarkan service_id jika parameter ada
        if ($request->has('service_id') && $request->input('service_id') != '') {
            $query->where('service_id', $request->input('service_id'));
        }

        // Memfilter berdasarkan rentang tanggal jika parameter ada
        if ($request->has('start_date') && $request->has('end_date') && $request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('date_attendance', [$request->input('start_date'), $request->input('end_date')]);
        }

        // Mengambil data dengan pagination
        $attendanceTransactions = $query->paginate(10);

        // Mengambil semua layanan untuk dropdown filter
        $services = Service::all();

        return view('admin.attendance.attendance-index.index', compact('attendanceTransactions', 'services'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all(); // Fetch all services
        return view('admin.attendance.attendance-create.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // Memulai transaksi
        DB::beginTransaction();

        try {
            // Validasi input
            $validatedData = $request->validate([
                'date_attendance' => 'required|date',
                'service_id' => 'required|exists:services,id',
                'attendance' => 'required|array',
                'attendance.*.check_in_status' => 'required|in:Present,Excused,Sick,Absent',
                'attendance.*.check_out_status' => 'nullable|in:on_time,late,Absent,sick,not_yet,Excused', // Optional
                'attendance.*.check_in_time' => 'nullable|date_format:H:i', // Validate check_in_time
                'attendance.*.check_out_time' => 'nullable|date_format:H:i', // Validate check_out_time
                'attendance.*.note' => 'nullable|string',
                'attendance.*.is_late' => 'required', // Validasi is_late harus boolean
                'attendance.*.late_duration' => 'nullable|integer', // Validasi late_duration
            ]);

            // Cek jika sudah ada absensi dengan tanggal dan service_id yang sama
            $existingAttendance = AttendanceTransaction::where('service_id', $validatedData['service_id'])
                ->where('date_attendance', $validatedData['date_attendance'])
                ->first();

            if ($existingAttendance) {
                // Rollback jika sudah ada absensi yang sama
                DB::rollBack();
                return redirect()->route('attendance.create')
                    ->withErrors(['date_attendance' => 'Attendance for this service and date already exists.'])
                    ->withInput(); // Menambahkan withInput() untuk mempertahankan input sebelumnya
            }

            // Buat entry baru di tabel attendance_transactions
            $attendanceTransaction = AttendanceTransaction::create([
                'service_id' => $validatedData['service_id'],
                'date_attendance' => $validatedData['date_attendance'],
            ]);

            // Loop melalui setiap siswa dalam array attendance
            foreach ($validatedData['attendance'] as $studentId => $attendanceData) {
                // Ambil data per siswa dengan nilai default jika tidak ada
                $checkInStatus = $attendanceData['check_in_status'];
                $checkOutStatus = $attendanceData['check_out_status'] ?? 'not_yet'; // Set default jika kosong
                $checkInTime = $attendanceData['check_in_time'] ?? null;
                $checkOutTime = $attendanceData['check_out_time'] ?? null;
                $note = $attendanceData['note'] ?? null;

                // Validasi waktu check_in_time harus lebih kecil dari check_out_time
                if ($checkInTime && $checkOutTime && $checkInTime > $checkOutTime) {
                    DB::rollBack(); // Rollback jika ada error pada validasi waktu
                    return redirect()->route('attendance.create')
                        ->withErrors(['check_in_time' => 'Check-in time cannot be later than check-out time.'])
                        ->withInput(); // Menambahkan withInput() untuk mempertahankan input sebelumnya
                }

                // Konversi is_late menjadi boolean
                $isLate = filter_var($attendanceData['is_late'], FILTER_VALIDATE_BOOLEAN); // Mengonversi menjadi boolean (true/false)
                $lateDuration = $attendanceData['late_duration'] ?? 0; // Durasi keterlambatan

                // Tentukan penalty_status berdasarkan is_late
                $penaltyStatus = $isLate ? 'not_paid' : null; // Jika terlambat, set penalty status ke 'not_paid', jika tidak set null

                // Sesuaikan check_out_status dengan check_in_status
                if (in_array($checkInStatus, ['Excused', 'Sick', 'Absent'])) {
                    $checkOutStatus = $checkInStatus; // Jika check_in_status adalah Excused, Sick, atau Absent, maka check_out_status ikut disesuaikan
                } else {
                    // Tentukan check_out_status berdasarkan isLate dan checkOutTime
                    $checkOutStatus = $isLate ? 'late' : ($checkOutTime ? 'on_time' : 'not_yet');
                }

                // Cari activity_transaction yang sesuai
                $activityTransaction = ActivityTransaction::where('student_id', $studentId)
                    ->where('service_id', $validatedData['service_id'])
                    ->first();

                if (!$activityTransaction) {
                    DB::rollBack(); // Rollback jika activity transaction tidak ditemukan
                    Log::error("Activity transaction not found for student_id: {$studentId}");
                    continue; // Lewatkan siswa ini jika activity transaction tidak ditemukan
                }

                // Simpan data absensi di tabel attendances
                Attendance::create([
                    'attendances_transaction_id' => $attendanceTransaction->id, // ID transaksi absensi
                    'activity_transaction_id' => $activityTransaction->id, // ID transaksi aktivitas siswa
                    'check_in_status' => $checkInStatus,
                    'check_out_status' => $checkOutStatus, // Sesuaikan check_out_status dengan status 'late' jika terlambat
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'penalty_status' => $penaltyStatus, // Set 'not_paid' jika terlambat, else null
                    'late_duration' => $lateDuration, // Durasi keterlambatan
                    'note' => $note,
                ]);
            }

            // Commit transaksi jika semuanya berhasil
            DB::commit(); // Commit transaksi setelah semua berhasil

            return redirect()->route('attendance.index')->with('success', 'Attendance saved successfully.');
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan dan rollback transaksi
            DB::rollBack(); // Rollback transaksi jika terjadi error
            Log::error('Error storing attendance: ' . $e->getMessage());

            return redirect()->route('attendance.create')->with('error', 'There was an error saving the attendance. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Cari transaksi absensi berdasarkan ID
        $attendanceTransaction = AttendanceTransaction::with('attendances', 'service')->findOrFail($id);

        // Menampilkan halaman view attendance dengan data yang diambil
        return view('admin.attendance.attendance-show.index', compact('attendanceTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Cari transaksi absensi berdasarkan ID dan sertakan relasi program
        $attendanceTransaction = AttendanceTransaction::with([
            'attendances.activityTransaction.student',
            'attendances.activityTransaction.program', // Sertakan program untuk mendapatkan end_time
            'service'
        ])->findOrFail($id); // Menyertakan data absensi dan service terkait

        // Ambil data layanan untuk dropdown filter
        $services = Service::all();

        // Menampilkan halaman edit dengan data absensi
        return view('admin.attendance.attendance-edit.index', compact('attendanceTransaction', 'services'));
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Memulai transaksi
        DB::beginTransaction();

        try {
            // Validasi input tanpa service_id
            $validatedData = $request->validate([
                'date_attendance' => 'required|date',
                'attendance' => 'required|array',
                'attendance.*.check_in_status' => 'required|in:Present,Excused,Sick,Absent',
                'attendance.*.check_out_status' => 'nullable|in:on_time,late,Absent,sick,not_yet,Excused', // Optional
                'attendance.*.check_in_time' => 'nullable', // Validate check_in_time
                'attendance.*.check_out_time' => 'nullable', // Validate check_out_time
                'attendance.*.note' => 'nullable|string',
                'attendance.*.is_late' => 'required', // Validasi is_late harus boolean
                'attendance.*.late_duration' => 'nullable|integer', // Validasi late_duration
            ]);

            // Cari transaksi absensi berdasarkan ID
            $attendanceTransaction = AttendanceTransaction::findOrFail($id);

            // Cek jika ada perubahan date_attendance yang sama dengan transaksi absensi lain
            $existingAttendance = AttendanceTransaction::where('service_id', $attendanceTransaction->service_id) // Pastikan service_id tetap sama
                ->where('date_attendance', $validatedData['date_attendance'])
                ->where('id', '!=', $attendanceTransaction->id)
                ->first();

            if ($existingAttendance) {
                DB::rollBack();
                return redirect()->route('attendance.edit', $attendanceTransaction->id)
                    ->withErrors(['date_attendance' => 'Attendance for this service and date already exists.'])
                    ->withInput();
            }

            // Update transaksi absensi tanpa mengubah service_id
            $attendanceTransaction->update([
                'date_attendance' => $validatedData['date_attendance'], // hanya update date_attendance
            ]);

            // Loop untuk update setiap siswa dalam array attendance
            foreach ($validatedData['attendance'] as $studentId => $attendanceData) {
                // Cari absensi siswa
                $attendance = Attendance::where('attendances_transaction_id', $attendanceTransaction->id)
                    ->where('activity_transaction_id', $studentId)
                    ->first();

                if (!$attendance) {
                    DB::rollBack();
                    return redirect()->route('attendance.edit', $attendanceTransaction->id)
                        ->withErrors(['attendance' => 'Attendance data not found for student ID ' . $studentId])
                        ->withInput();
                }

                // Ambil data per siswa
                $checkInStatus = $attendanceData['check_in_status'];
                $checkOutStatus = $attendanceData['check_out_status'] ?? 'not_yet'; // Set default jika kosong
                $checkInTime = $attendanceData['check_in_time'] ?? null;
                $checkOutTime = $attendanceData['check_out_time'] ?? null;
                $note = $attendanceData['note'] ?? null;

                // Validasi waktu check_in_time harus lebih kecil dari check_out_time
                if ($checkInTime && $checkOutTime && $checkInTime > $checkOutTime) {
                    DB::rollBack();
                    return redirect()->route('attendance.edit', $attendanceTransaction->id)
                        ->withErrors(['check_in_time' => 'Check-in time cannot be later than check-out time.'])
                        ->withInput();
                }

                // Konversi is_late menjadi boolean
                $isLate = filter_var($attendanceData['is_late'], FILTER_VALIDATE_BOOLEAN); // Mengonversi menjadi boolean (true/false)
                $lateDuration = $attendanceData['late_duration'] ?? 0; // Durasi keterlambatan

                // Tentukan penalty_status berdasarkan is_late
                $penaltyStatus = $isLate ? 'not_paid' : null;

                // Sesuaikan check_out_status dengan check_in_status
                if (in_array($checkInStatus, ['Excused', 'Sick', 'Absent'])) {
                    $checkOutStatus = $checkInStatus; // Jika check_in_status adalah Excused, Sick, atau Absent, maka check_out_status ikut disesuaikan
                } else {
                    $checkOutStatus = $isLate ? 'late' : ($checkOutTime ? 'on_time' : 'not_yet');
                }

                // Update absensi siswa
                $attendance->update([
                    'check_in_status' => $checkInStatus,
                    'check_out_status' => $checkOutStatus,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'penalty_status' => $penaltyStatus,
                    'late_duration' => $lateDuration,
                    'note' => $note,
                ]);
            }

            // Commit transaksi jika semuanya berhasil
            DB::commit();

            return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            Log::error('Error updating attendance: ' . $e->getMessage());

            return redirect()->route('attendance.edit', $id)
                ->with('error', 'There was an error updating the attendance. Please try again.');
        }
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction(); // Memulai transaksi untuk menghindari perubahan yang tidak konsisten

        try {
            // Cari attendance transaction berdasarkan ID
            $attendanceTransaction = AttendanceTransaction::findOrFail($id);

            // Hapus attendance yang terkait dengan transaction ini
            $attendanceTransaction->attendances()->delete();  // Menghapus semua data attendance terkait dengan transaction ini

            // Hapus transaction itu sendiri
            $attendanceTransaction->delete();

            DB::commit(); // Commit jika berhasil

            return redirect()->route('attendance.index')
                ->with('success', 'Attendance deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika ada error

            Log::error('Error deleting attendance: ' . $e->getMessage());

            return redirect()->route('attendance.index')
                ->with('error', 'There was an error deleting the attendance. Please try again.');
        }
    }


    public function getAttendanceList(Request $request)
    {
        // Ambil service_id dari request
        $serviceId = $request->input('service_id');

        // Query data siswa yang terdaftar di service tersebut dan statusnya aktif
        $students = ActivityTransaction::where('service_id', $serviceId)
            ->whereHas('student', function ($query) {
                $query->where('student_status', true); // Hanya ambil siswa yang aktif
            })
            ->with('student', 'program') // Mengambil data siswa dan program
            ->get()
            ->map(function ($transaction) {
                return [
                    'student_id' => $transaction->student->id,
                    'name' => $transaction->student->student_name,
                    'number' => $transaction->student->student_number,
                    'program_end_time' => $transaction->program->end_time,  // Format program end time as HH:mm:ss
                ];
            });

        // Return data in JSON format to be used in the frontend
        return response()->json(['students' => $students]);
    }

    // Fungsi untuk mengekspor data ke Excel
    public function exportExcel(Request $request)
    {
        $serviceId = $request->input('service_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Pastikan start_date dan end_date diisi
        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Please provide both start date and end date.'], 400);
        }

        try {
            // Ekspor data ke Excel dan mengembalikan file untuk diunduh langsung
            return Excel::download(new AttendanceExport($serviceId, $startDate, $endDate), 'attendance_report.xlsx');
        } catch (\Exception $e) {
            // Tangkap error dan log error untuk debugging
            Log::error('Error generating report: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating report: ' . $e->getMessage()], 500);
        }
    }
}
