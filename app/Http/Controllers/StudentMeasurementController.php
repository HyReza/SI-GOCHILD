<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Measurement;
use App\Models\GrowthStandard;
use App\Models\ActivityTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMeasurementController extends Controller
{
    /**
     * Menampilkan daftar riwayat pengukuran siswa (Index).
     */
    public function index()
    {
        $student = Auth::guard('student')->user();

        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Sesi habis.');
        }

        // Ambil pengukuran milik siswa ini
        $measurements = Measurement::whereHas('activityTransaction', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })
            ->with('user') // Load info petugas
            ->orderBy('date_measurement', 'desc')
            ->paginate(15);

        return view('student.meansurement.meansurement-index.index', compact('student', 'measurements'));
    }

    /**
     * Menampilkan halaman grafik pertumbuhan (KMS).
     */
    public function chart()
    {
        $student = Auth::guard('student')->user();

        if (!$student) {
            return redirect()->route('student.login');
        }

        // 1. Ambil Data Pengukuran Siswa (Urut dari terlama ke terbaru)
        // Kita cari activity transaction terakhir atau semua yang terkait siswa
        $measurements = Measurement::whereHas('activityTransaction', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })
            ->orderBy('date_measurement', 'asc')
            ->get();

        // 2. Siapkan Data Siswa untuk Chart
        $chartData = $measurements->map(function ($measurement) use ($student) {
            $ageInMonths = Carbon::parse($student->birth_date)->diffInMonths($measurement->date_measurement);
            // Hitung BMI
            $heightM = $measurement->height / 100;
            $bmi = ($heightM > 0) ? ($measurement->weight / ($heightM * $heightM)) : 0;

            // Helper decode JSON
            $getArray = function ($data) {
                if (is_array($data)) return $data;
                if (is_string($data)) return json_decode($data, true) ?: [];
                return [];
            };

            return [
                'age' => $ageInMonths, // Sumbu X (Umur)
                'weight' => (float) $measurement->weight,
                'height' => (float) $measurement->height, // Sumbu X (Tinggi) untuk BB/TB
                'bmi' => round($bmi, 2),
                'date' => Carbon::parse($measurement->date_measurement)->format('Y-m-d'),
                'condition' => $measurement->measurement_condition,
                'sd_category' => $getArray($measurement->sd_category),
                'status_gizi' => $getArray($measurement->calculation_results),
            ];
        });

        // 3. Siapkan Data Standar Pertumbuhan (WHO)
        // Logic ini disalin persis dari Admin MeasurementController agar hasilnya sama
        $gender = ($student->gender == 1 || $student->gender == 'male') ? 'male' : 'female';

        $allStandardsRaw = collect();
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'BB/U')->whereBetween('age_months', [0, 60])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'IMT/U')->whereBetween('age_months', [0, 60])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'PB/U')->whereBetween('age_months', [0, 24])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'TB/U')->whereBetween('age_months', [24, 60])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'PB/BB')->whereBetween('body_length', [45, 110])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'TB/BB')->whereBetween('body_height', [65, 120])->get());

        $allStandardCurves = [];
        $possibleParams = ['BB/U', 'PB/U', 'TB/U', 'IMT/U', 'PB/BB', 'TB/BB'];

        foreach ($possibleParams as $param) {
            $paramData = $allStandardsRaw->where('parameter', $param);

            if ($paramData->isNotEmpty()) {
                $x_axis_key = 'age_months';
                if ($param === 'PB/BB') $x_axis_key = 'body_length';
                if ($param === 'TB/BB') $x_axis_key = 'body_height';

                // Ambil data untuk sumbu X
                $x_values = $paramData->pluck($x_axis_key)->filter()->map(fn($val) => (float)$val);

                if ($x_values->isNotEmpty()) {
                    // Format: ['x_value' => 'y_value'] agar chart bisa mapping otomatis
                    $allStandardCurves[$param] = [
                        'x_axis_key' => $x_axis_key,
                        'min' => $x_values->min(),
                        'max' => $x_values->max(),
                        'median'     => $paramData->pluck('median', $x_axis_key)->all(),
                        'plus_1_sd'  => $paramData->pluck('plus_1_sd', $x_axis_key)->all(),
                        'plus_2_sd'  => $paramData->pluck('plus_2_sd', $x_axis_key)->all(),
                        'plus_3_sd'  => $paramData->pluck('plus_3_sd', $x_axis_key)->all(),
                        'minus_1_sd' => $paramData->pluck('minus_1_sd', $x_axis_key)->all(),
                        'minus_2_sd' => $paramData->pluck('minus_2_sd', $x_axis_key)->all(),
                        'minus_3_sd' => $paramData->pluck('minus_3_sd', $x_axis_key)->all(),
                    ];
                }
            }
        }

        // Activity Transaction diperlukan untuk tombol "Kembali" di view (opsional, ambil yang terakhir)
        $activityTransaction = \App\Models\ActivityTransaction::where('student_id', $student->id)->latest()->first();

        return view('student.meansurement.meansurement-chart.index', compact('student', 'chartData', 'allStandardCurves', 'activityTransaction'));
    }

    /**
     * Menampilkan detail satu pengukuran (Show).
     */
    public function show(Measurement $measurement)
    {
        $student = Auth::guard('student')->user();

        if ($measurement->activityTransaction->student_id !== $student->id) {
            abort(403, 'Akses ditolak.');
        }

        return view('student.meansurement.meansurement-show.index', compact('measurement', 'student'));
    }
}
