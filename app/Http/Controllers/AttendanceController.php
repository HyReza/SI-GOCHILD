<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Service;
use App\Models\Student;
use App\Models\AbsenceFine;
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
    // 💡 Kritis: Konstanta Tarif Denda (Rp 5.000 per 30 menit)
    private const FINE_AMOUNT_PER_INTERVAL = 5000;
    private const FINE_INTERVAL_MINUTES = 30; // Interval waktu (30 menit)

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AttendanceTransaction::with('attendances.absenceFine', 'service')->orderBy('date_attendance', 'desc');

        if ($request->has('service_id') && $request->input('service_id') != '') {
            $query->where('service_id', $request->input('service_id'));
        }

        if ($request->has('start_date') && $request->has('end_date') && $request->input('start_date') && $request->input('end_date')) {
            $query->whereBetween('date_attendance', [$request->input('start_date'), $request->input('end_date')]);
        }

        $attendanceTransactions = $query->paginate(10);
        $services = Service::all();

        return view('admin.attendance.attendance-index.index', compact('attendanceTransactions', 'services'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all();
        return view('admin.attendance.attendance-create.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validasi input
            $validatedData = $request->validate([
                'date_attendance' => 'required|date',
                'service_id' => 'required|exists:services,id',
                'attendance' => 'required|array',
                'attendance.*.check_in_status' => 'required|in:Present,Excused,Sick,Absent',
                'attendance.*.check_out_status' => 'nullable|in:on_time,late,Absent,sick,not_yet,Excused',
                'attendance.*.check_in_time' => 'nullable|date_format:H:i',
                'attendance.*.check_out_time' => 'nullable|date_format:H:i',
                'attendance.*.note' => 'nullable|string',
                'attendance.*.is_late' => 'required',
                'attendance.*.late_duration' => 'nullable|integer|min:0',
            ]);

            // Cek duplikasi
            $existingAttendance = AttendanceTransaction::where('service_id', $validatedData['service_id'])
                ->where('date_attendance', $validatedData['date_attendance'])
                ->first();

            if ($existingAttendance) {
                DB::rollBack();
                return redirect()->route('attendance.create')
                    ->withErrors(['date_attendance' => 'Attendance for this service and date already exists.'])
                    ->withInput();
            }

            // Buat attendance_transactions
            $attendanceTransaction = AttendanceTransaction::create([
                'service_id' => $validatedData['service_id'],
                'date_attendance' => $validatedData['date_attendance'],
            ]);
            $dateAttendance = $attendanceTransaction->date_attendance;

            // Loop melalui setiap siswa
            foreach ($validatedData['attendance'] as $activityTransactionId => $attendanceData) {

                $checkInStatus = $attendanceData['check_in_status'];
                $checkOutTime = $attendanceData['check_out_time'] ?? null;
                $checkInTime = $attendanceData['check_in_time'] ?? null;
                $note = $attendanceData['note'] ?? null;

                // Validasi waktu check_in_time harus lebih kecil dari check_out_time
                if ($checkInTime && $checkOutTime && $checkInTime > $checkOutTime) {
                    DB::rollBack();
                    return redirect()->route('attendance.create')
                        ->withErrors(['check_in_time' => 'Check-in time cannot be later than check-out time.'])
                        ->withInput();
                }

                $activityTransaction = ActivityTransaction::with('student')->find($activityTransactionId);

                if (!$activityTransaction) {
                    DB::rollBack();
                    Log::error("Activity transaction not found for ID: {$activityTransactionId}");
                    return redirect()->route('attendance.create')->with('error', 'Gagal menemukan data aktivitas siswa.');
                }

                $isLate = filter_var($attendanceData['is_late'], FILTER_VALIDATE_BOOLEAN);
                $lateDuration = $attendanceData['late_duration'] ?? 0;
                $fineId = null;

                // 💡 LOGIC Denda BARU: Ciptakan AbsenceFine jika terlambat
                if ($isLate && $lateDuration > 0) {
                    // Hitung kelipatan 30 menit, lalu kalikan Rp 5.000
                    $lateIntervals = floor($lateDuration / self::FINE_INTERVAL_MINUTES);
                    $fineAmount = $lateIntervals * self::FINE_AMOUNT_PER_INTERVAL;
                    $dateFine = $dateAttendance;

                    if ($fineAmount > 0) {
                        $absenceFine = AbsenceFine::create([
                            'student_id' => $activityTransaction->student_id,
                            'fine_date' => $dateFine,
                            'description' => "Denda Keterlambatan Check-Out {$lateDuration} menit ({$lateIntervals} x 30 menit) pada tanggal {$dateFine}",
                            'amount' => $fineAmount,
                            'is_billed' => false,
                        ]);
                        $fineId = $absenceFine->id;
                    } else {
                        // Jika durasi terlambat kurang dari interval denda (30 menit)
                        $isLate = false;
                        $lateDuration = 0;
                    }
                }

                // Sesuaikan check_out_status
                if (in_array($checkInStatus, ['Excused', 'Sick', 'Absent'])) {
                    $checkOutStatus = $checkInStatus;
                } else {
                    $checkOutStatus = $isLate ? 'late' : ($checkOutTime ? 'on_time' : 'not_yet');
                }

                // Simpan data absensi di tabel attendances
                Attendance::create([
                    'attendances_transaction_id' => $attendanceTransaction->id,
                    'activity_transaction_id' => $activityTransaction->id,
                    'check_in_status' => $checkInStatus,
                    'check_out_status' => $checkOutStatus,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'absence_fine_id' => $fineId, // 💡 Kritis: Kolom baru
                    'late_duration' => $lateDuration,
                    'note' => $note,
                    // HAPUS 'penalty_status' DARI SINI
                ]);
            }

            DB::commit();

            return redirect()->route('attendance.index')->with('success', 'Attendance saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing attendance: ' . $e->getMessage());

            return redirect()->route('attendance.create')
                ->with('error', 'There was an error saving the attendance. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $attendanceTransaction = AttendanceTransaction::with('attendances.absenceFine', 'service')->findOrFail($id);
        return view('admin.attendance.attendance-show.index', compact('attendanceTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $attendanceTransaction = AttendanceTransaction::with([
            'attendances.activityTransaction.student',
            'attendances.activityTransaction.program',
            'attendances.absenceFine',
            'service'
        ])->findOrFail($id);

        $services = Service::all();
        return view('admin.attendance.attendance-edit.index', compact('attendanceTransaction', 'services'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Validasi input
            $validatedData = $request->validate([
                'date_attendance' => 'required|date',
                'attendance' => 'required|array',
                'attendance.*.check_in_status' => 'required|in:Present,Excused,Sick,Absent',
                'attendance.*.check_out_status' => 'nullable|in:on_time,late,Absent,sick,not_yet,Excused',
                // 💡 PERBAIKAN VALIDASI: Menggunakan pipe notation untuk mengatasi string kosong
                'attendance.*.check_in_time' => 'nullable',
                'attendance.*.check_out_time' => 'nullable',
                'attendance.*.note' => 'nullable|string',
                'attendance.*.is_late' => 'required',
                'attendance.*.late_duration' => 'nullable|integer|min:0',
            ]);

            $attendanceTransaction = AttendanceTransaction::findOrFail($id);

            // Cek duplikasi
            $existingAttendance = AttendanceTransaction::where('service_id', $attendanceTransaction->service_id)
                ->where('date_attendance', $validatedData['date_attendance'])
                ->where('id', '!=', $attendanceTransaction->id)
                ->first();

            if ($existingAttendance) {
                DB::rollBack();
                return redirect()->route('attendance.edit', $attendanceTransaction->id)
                    ->withErrors(['date_attendance' => 'Attendance for this service and date already exists.'])
                    ->withInput();
            }

            // Update transaksi absensi
            $attendanceTransaction->update([
                'date_attendance' => $validatedData['date_attendance'],
            ]);
            $dateAttendance = $attendanceTransaction->date_attendance;

            // Loop untuk update setiap siswa
            foreach ($validatedData['attendance'] as $activityTransactionId => $attendanceData) {
                // Cari absensi siswa
                $attendance = Attendance::where('attendances_transaction_id', $attendanceTransaction->id)
                    ->where('activity_transaction_id', $activityTransactionId)
                    ->with('activityTransaction.student', 'absenceFine')
                    ->first();

                if (!$attendance) {
                    Log::error("Attendance data not found for activity transaction ID: {$activityTransactionId}");
                    continue;
                }

                $checkInStatus = $attendanceData['check_in_status'];
                $checkOutTime = $attendanceData['check_out_time'] ?? null;
                $checkInTime = $attendanceData['check_in_time'] ?? null;
                $note = $attendanceData['note'] ?? null;

                // Validasi waktu
                if ($checkInTime && $checkOutTime && $checkInTime > $checkOutTime) {
                    DB::rollBack();
                    return redirect()->route('attendance.edit', $attendanceTransaction->id)
                        ->withErrors(['check_in_time' => 'Check-in time cannot be later than check-out time.'])
                        ->withInput();
                }

                $isLate = filter_var($attendanceData['is_late'], FILTER_VALIDATE_BOOLEAN);
                $lateDuration = $attendanceData['late_duration'] ?? 0;
                $fineId = $attendance->absence_fine_id;

                // 💡 LOGIC Denda BARU: Kelola AbsenceFine
                $lateIntervals = floor($lateDuration / self::FINE_INTERVAL_MINUTES);
                $fineAmount = $lateIntervals * self::FINE_AMOUNT_PER_INTERVAL;
                $fineDescription = "Denda Keterlambatan Check-Out {$lateDuration} menit ({$lateIntervals} x 30 menit) pada tanggal {$dateAttendance}.";

                if (!$isLate || $lateDuration == 0 || $fineAmount == 0) {
                    // Jika tidak terlambat, durasi 0, atau denda < 5000, hapus denda yang ada
                    if ($fineId) {
                        $fine = AbsenceFine::find($fineId);
                        if ($fine && !$fine->is_billed) { // Hapus HANYA jika belum diproses
                            $fine->delete();
                            $fineId = null;
                        }
                    }
                    $isLate = false;
                    $lateDuration = 0;
                } elseif ($isLate && $fineAmount > 0) {
                    // Jika terlambat dan denda harus dikenakan
                    if (!$fineId) {
                        $newFine = AbsenceFine::create([
                            'student_id' => $attendance->activityTransaction->student_id,
                            'fine_date' => $dateAttendance,
                            'description' => $fineDescription,
                            'amount' => $fineAmount,
                            'is_billed' => false,
                        ]);
                        $fineId = $newFine->id;
                    }
                    // Jika denda sudah ada, update rinciannya (hanya jika belum ditagihkan)
                    else {
                        $fine = AbsenceFine::find($fineId);
                        if ($fine && !$fine->is_billed) {
                            $fine->update([
                                'description' => $fineDescription,
                                'amount' => $fineAmount,
                            ]);
                        }
                    }
                }

                // Sesuaikan check_out_status
                if (in_array($checkInStatus, ['Excused', 'Sick', 'Absent'])) {
                    $checkOutStatus = $checkInStatus;
                } else {
                    $checkOutStatus = $isLate ? 'late' : ($checkOutTime ? 'on_time' : 'not_yet');
                }

                // Update absensi siswa
                $attendance->update([
                    'check_in_status' => $checkInStatus,
                    'check_out_status' => $checkOutStatus,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'absence_fine_id' => $fineId, // Kritis: Kolom baru
                    'late_duration' => $lateDuration,
                    'note' => $note,
                ]);
            }

            DB::commit();

            return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating attendance: ' . $e->getMessage());

            return redirect()->route('attendance.edit', $id)
                ->with('error', 'There was an error updating the attendance. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $attendanceTransaction = AttendanceTransaction::with('attendances.absenceFine')->findOrFail($id);

            // Hapus semua denda terkait yang belum ditagihkan/diproses
            foreach ($attendanceTransaction->attendances as $attendance) {
                if ($attendance->absenceFine) {
                    $fine = $attendance->absenceFine;
                    if (!$fine->is_billed) {
                        $fine->delete();
                    }
                }
            }

            // Hapus attendance dan transaction
            $attendanceTransaction->attendances()->delete();
            $attendanceTransaction->delete();

            DB::commit();

            return redirect()->route('attendance.index')
                ->with('success', 'Attendance deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting attendance: ' . $e->getMessage());
            return redirect()->route('attendance.index')
                ->with('error', 'There was an error deleting the attendance. Please try again.');
        }
    }


    public function getAttendanceList(Request $request)
    {
        $serviceId = $request->input('service_id');

        $students = ActivityTransaction::where('service_id', $serviceId)
            ->whereHas('student', function ($query) {
                $query->where('student_status', true);
            })
            ->with('student', 'program')
            ->get()
            ->map(function ($transaction) {
                return [
                    'activity_transaction_id' => $transaction->id,
                    'student_id' => $transaction->student->id,
                    'name' => $transaction->student->student_name,
                    'number' => $transaction->student->student_number,
                    'program_end_time' => $transaction->program->end_time,
                ];
            });

        return response()->json(['students' => $students]);
    }

    // Fungsi untuk mengekspor data ke Excel
    public function exportExcel(Request $request)
    {
        $serviceId = $request->input('service_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Please provide both start date and end date.'], 400);
        }

        try {
            return Excel::download(new AttendanceExport($serviceId, $startDate, $endDate), 'attendance_report.xlsx');
        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());
            return response()->json(['error' => 'Error generating report: ' . $e->getMessage()], 500);
        }
    }
}
