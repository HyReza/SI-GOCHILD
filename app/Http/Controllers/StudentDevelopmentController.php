<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MmdstAssessment;
use App\Models\MmdstParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDevelopmentController extends Controller
{
    /**
     * Menampilkan daftar riwayat laporan perkembangan (MMDST).
     */
    public function index()
    {
        $student = Auth::guard('student')->user();

        // Mengambil data assessment milik siswa, urut dari yang terbaru
        // Variabel dikirim sebagai 'reports' agar sesuai dengan konteks view umum
        $reports = MmdstAssessment::with(['creator'])
            ->where('student_id', $student->id)
            ->orderBy('assessment_date', 'desc')
            ->paginate(10);

        return view('student.development.development-index.index', compact('student', 'reports'));
    }

    /**
     * Menampilkan detail satu laporan perkembangan.
     * Note: Parameter route adalah {report}, kita binding ke model MmdstAssessment.
     */
    public function show(MmdstAssessment $report)
    {
        // 1. Validasi Keamanan: Pastikan laporan ini milik siswa yang sedang login
        if ($report->student_id !== Auth::guard('student')->id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        // 2. Load relasi yang dibutuhkan
        $report->load(['items.parameter.stimulationCategory', 'creator', 'student']);

        // 3. Ambil semua parameter MMDST (Aktif) untuk referensi tampilan
        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')
            ->orderBy('id')
            ->get();

        // 4. Persiapkan Data Mapping untuk View
        $testedMap = [];
        $bucketMap = [];
        $ageInDays = $report->age_in_days;

        // A. Map Hasil Tes yang ada di database (items)
        foreach ($report->items as $item) {
            $testedMap[$item->mmdst_parameter_id] = [
                'tested'      => true,
                'result_code' => $item->result_code,
                'is_delay'    => $item->is_delay,
                'note'        => $item->note
            ];
        }

        // B. Hitung "Bucket Usia" untuk setiap parameter
        // Ini menentukan apakah anak "Lewat Usia", "Di Garis Usia", atau "Belum Waktunya"
        foreach ($parameters as $p) {
            $bucketMap[$p->id] = $this->classifyAgeBucket(
                $ageInDays,
                $p->percent_25,
                $p->percent_50,
                $p->percent_75,
                $p->percent_100
            );
        }

        // Kita kirim variabel sebagai $assessment agar konsisten dengan view detail MMDST
        return view('student.development.development-show.index', [
            'assessment'  => $report,
            'parameters'  => $parameters,
            'testedMap'   => $testedMap,
            'bucketMap'   => $bucketMap,
            'student'     => $report->student
        ]);
    }

    /**
     * Helper: Klasifikasi posisi parameter tes terhadap usia anak saat pemeriksaan.
     */
    private function classifyAgeBucket(int $age, ?int $p25, ?int $p50, ?int $p75, ?int $p100): string
    {
        // Konversi null ke integer yang aman
        $p25  = (int) ($p25  ?? PHP_INT_MAX);
        $p50  = (int) ($p50  ?? -1);
        $p75  = (int) ($p75  ?? -1);
        $p100 = (int) ($p100 ?? PHP_INT_MAX);

        // Logika Klasifikasi
        if ($age >= $p100) return 'OVERDUE'; // Anak sudah melewati usia maksimal kemampuan ini (Harusnya sudah bisa)
        if ($age === $p25 || $age === $p50 || $age === $p75 || $age === $p100) return 'AT_LINE'; // Tepat di garis
        if ($age >= $p25 && $age < $p100) return 'IN_WINDOW'; // Sedang dalam masa perkembangan kemampuan ini
        return 'NOT_YET'; // Belum waktunya
    }
}
