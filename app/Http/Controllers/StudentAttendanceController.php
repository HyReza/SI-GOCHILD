<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    /**
     * Menampilkan riwayat absensi siswa yang sedang login.
     */
    public function index(Request $request)
    {
        // 1. Ambil data siswa yang sedang login
        $student = Auth::guard('student')->user();

        // 2. Ambil parameter filter (Bulan & Tahun), default ke bulan ini
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // 3. Query Dasar: Ambil Attendance milik siswa ini
        // Relasi: Attendance -> ActivityTransaction -> Student
        $query = Attendance::whereHas('activityTransaction', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })
            // Join ke tabel transaksi absensi untuk filter tanggal & sorting
            ->join('attendance_transactions', 'attendances.attendances_transaction_id', '=', 'attendance_transactions.id')
            ->whereMonth('attendance_transactions.date_attendance', $month)
            ->whereYear('attendance_transactions.date_attendance', $year)
            ->select('attendances.*') // Pastikan hanya mengambil kolom attendances agar tidak bentrok id
            ->with(['attendanceTransaction', 'absenceFine']); // Eager load relasi

        // 4. Hitung Ringkasan (Statistik) untuk bulan yang dipilih
        // Kita clone query agar tidak mengganggu pagination nanti
        $statsQuery = clone $query;
        $stats = $statsQuery->get();

        $summary = [
            'present' => $stats->where('check_in_status', 'Present')->count(),
            'sick' => $stats->where('check_in_status', 'Sick')->count(),
            'excused' => $stats->where('check_in_status', 'Excused')->count(),
            'absent' => $stats->where('check_in_status', 'Absent')->count(),
            'late' => $stats->where('check_out_status', 'late')->count(),
            // Hitung total denda jika relasi absenceFine ada
            'total_fine' => $stats->sum(function ($att) {
                return $att->absenceFine ? $att->absenceFine->amount : 0;
            }),
        ];

        // 5. Ambil data untuk Tabel (Pagination) & Sorting terbaru
        $attendances = $query->orderBy('attendance_transactions.date_attendance', 'desc')
            ->paginate(10)
            ->withQueryString(); // Agar parameter filter tetap ada saat ganti halaman

        return view('student.attandance.attandance-index.index', compact('attendances', 'summary', 'month', 'year'));
    }
}
