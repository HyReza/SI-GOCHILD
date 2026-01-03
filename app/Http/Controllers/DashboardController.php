<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\MmdstAssessment;
use App\Models\Measurement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Cek guard student
        if (Auth::guard('student')->check()) {
            return view('dashboard-student');
        }

        // Cek guard web (Admin/Guru)
        elseif (Auth::guard('web')->check()) {
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));

            // 1. Statistik Utama
            $stats = [
                'total_students'     => Student::count(),
                'total_teachers'     => User::where('role_id', 2)->count(),
                'assessments_count'  => MmdstAssessment::whereMonth('assessment_date', $month)
                    ->whereYear('assessment_date', $year)->count(),
                'measurements_count' => Measurement::whereMonth('date_measurement', $month)
                    ->whereYear('date_measurement', $year)->count(),
            ];

            // 2. Summary MMDST
            // Tips: Menggunakan query scope di model akan lebih rapi, tapi begini juga oke.
            $mmdst_summary = [
                'NORMAL'       => MmdstAssessment::whereMonth('assessment_date', $month)->whereYear('assessment_date', $year)->where('overall_result', 'NORMAL')->count(),
                'QUESTIONABLE' => MmdstAssessment::whereMonth('assessment_date', $month)->whereYear('assessment_date', $year)->where('overall_result', 'QUESTIONABLE')->count(),
                'ABNORMAL'     => MmdstAssessment::whereMonth('assessment_date', $month)->whereYear('assessment_date', $year)->where('overall_result', 'ABNORMAL')->count(),
                'UNTESTABLE'   => MmdstAssessment::whereMonth('assessment_date', $month)->whereYear('assessment_date', $year)->where('overall_result', 'UNTESTABLE')->count(),
            ];

            // 3. Summary Antropometri
            $growth_data = [
                'Sangat Kurang' => Measurement::whereMonth('date_measurement', $month)->whereYear('date_measurement', $year)->where('measurement_condition', 'Sangat Kurang')->count(),
                'Kurang'        => Measurement::whereMonth('date_measurement', $month)->whereYear('date_measurement', $year)->where('measurement_condition', 'Kurang')->count(),
                'Normal'        => Measurement::whereMonth('date_measurement', $month)->whereYear('date_measurement', $year)->where('measurement_condition', 'Normal')->count(),
                'Risiko Lebih'  => Measurement::whereMonth('date_measurement', $month)->whereYear('date_measurement', $year)->where('measurement_condition', 'Risiko Lebih')->count(),
            ];

            // 4. Riwayat Gabungan (Pertumbuhan & Perkembangan)
            // Pastikan relasi 'activityTransaction.student' benar ada di Model.
            // Jika error, ganti menjadi 'student' saja.
            $recent_mmdst = MmdstAssessment::with(['activityTransaction.student'])
                ->latest('assessment_date')
                ->take(3)
                ->get();

            $recent_growth = Measurement::with(['activityTransaction.student'])
                ->latest('date_measurement')
                ->take(3)
                ->get();

            // Gabungkan dan urutkan ulang berdasarkan tanggal terbaru
            $recent_activities = $recent_mmdst->concat($recent_growth)
                ->sortByDesc(function ($item) {
                    // Pastikan null coalescing ini menangkap kolom yang benar
                    return $item->assessment_date ?? $item->date_measurement;
                })
                ->take(6);

            return view('dashboard', compact('stats', 'mmdst_summary', 'growth_data', 'recent_activities', 'month', 'year'));
        }

        // Redirect jika belum login
        return redirect()->route('login')->with('error', 'Please log in first.');
    }
}
