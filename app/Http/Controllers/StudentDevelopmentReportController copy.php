<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Measurement;
use App\Models\MmdstAssessment;
use App\Models\GrowthStandard;
use App\Models\StudentDevelopmentReport;
use App\Models\StudentDevelopmentReportHealth;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan package dompdf sudah terinstall

class StudentDevelopmentReportController extends Controller
{
    // --- 1. INDEX (DAFTAR SISWA) ---
    public function index(Request $request)
    {
        $query = Student::whereHas('activityTransaction', function ($q) {
            $q->where('student_status', true);
        })->with(['activityTransaction.service', 'activityTransaction.program']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }
        $students = $query->orderBy('student_name', 'asc')->paginate(10);
        return view('admin.reports-development.reports-development-index.index', compact('students'));
    }

    // --- 2. HISTORY (RIWAYAT LAPORAN) ---
    public function history(Student $student)
    {
        $reports = StudentDevelopmentReport::where('student_id', $student->id)
            ->latest('report_date')
            ->paginate(10);

        return view('admin.reports-development.reports-development-history.index', compact('student', 'reports'));
    }

    // --- 3. PILIH PERIODE ---
    public function selectPeriod(Student $student)
    {
        return view('admin.reports-development.report-development-periode.index', compact('student'));
    }

    // --- 4. CREATE (HALAMAN FORM) ---
    public function create(Request $request, Student $student)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $academicYear = $request->query('academic_year');
        $semester = $request->query('semester');

        if (!$startDate || !$endDate) {
            return redirect()->route('development-reports.select-period', $student->id)
                ->with('error', 'Silakan pilih periode tanggal terlebih dahulu.');
        }

        // Data Header
        $student->load(['activityTransaction.service', 'activityTransaction.program']);

        // Absensi
        $attendanceData = DB::table('attendances')
            ->join('attendance_transactions', 'attendances.attendances_transaction_id', '=', 'attendance_transactions.id')
            ->join('activity_transactions', 'attendances.activity_transaction_id', '=', 'activity_transactions.id')
            ->where('activity_transactions.student_id', $student->id)
            ->whereBetween('attendance_transactions.date_attendance', [$startDate, $endDate])
            ->select('attendances.check_in_status')
            ->get();

        $attendanceSummary = [
            'Hadir' => $attendanceData->where('check_in_status', 'Present')->count(),
            'Sakit' => $attendanceData->where('check_in_status', 'Sick')->count(),
            'Izin'  => $attendanceData->whereIn('check_in_status', ['Excused', 'Permit'])->count(),
            'Alpha' => $attendanceData->where('check_in_status', 'Absent')->count(),
        ];

        // Snapshot Fisik
        $lastMeasurement = Measurement::whereHas('activityTransaction', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->orderByDesc('date_measurement')->first();

        $prefillPhysical = [
            'weight' => (float)($lastMeasurement->weight ?? 0),
            'height' => (float)($lastMeasurement->height ?? 0),
            'head'   => (float)($lastMeasurement->head_circumference ?? 0),
            'bmi'    => ($lastMeasurement && $lastMeasurement->height > 0)
                ? round($lastMeasurement->weight / pow($lastMeasurement->height / 100, 2), 2)
                : 0,
            'date'   => $lastMeasurement ? Carbon::parse($lastMeasurement->date_measurement)->translatedFormat('d F Y') : null
        ];

        // Chart Data
        $measurements = Measurement::whereHas('activityTransaction', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->orderBy('date_measurement', 'asc')->get();

        $chartData = $measurements->map(function ($m) use ($student) {
            $ageInMonths = Carbon::parse($student->birth_date)->diffInMonths($m->date_measurement);
            $bmi = ($m->height > 0) ? ($m->weight / pow($m->height / 100, 2)) : 0;

            $sd = is_string($m->sd_category) ? json_decode($m->sd_category, true) : ($m->sd_category ?? []);
            $status = is_string($m->calculation_results) ? json_decode($m->calculation_results, true) : ($m->calculation_results ?? []);

            return [
                'age' => $ageInMonths,
                'weight' => (float)$m->weight,
                'height' => (float)$m->height,
                'bmi' => round($bmi, 2),
                'date' => $m->date_measurement,
                'sd_category' => $sd,
                'status_gizi' => $status,
            ];
        })->values();

        // Growth Standard
        $gender = ($student->gender == 1 || $student->gender == 'male' || $student->gender == 'L') ? 1 : 0;
        $standards = GrowthStandard::where('gender', $gender)->where('is_active', true)->get();
        $allStandardCurves = [];
        $params = ['BB/U', 'PB/U', 'TB/U', 'IMT/U', 'PB/BB', 'TB/BB'];

        foreach ($params as $param) {
            $paramData = $standards->where('parameter', $param)->sortBy('id');
            if ($paramData->isNotEmpty()) {
                $xKey = ($param === 'PB/BB') ? 'body_length' : (($param === 'TB/BB') ? 'body_height' : 'age_months');

                $formatCurve = function ($col) use ($paramData, $xKey) {
                    return $paramData->map(function ($d) use ($col, $xKey) {
                        return ['x' => (float)$d->$xKey, 'y' => (float)$d->$col];
                    })->values()->all();
                };

                $allStandardCurves[$param] = [
                    'x_axis_key' => $xKey,
                    'min' => (float)$paramData->min($xKey),
                    'max' => (float)$paramData->max($xKey),
                    'median'    => $formatCurve('median'),
                    'plus_1_sd' => $formatCurve('plus_1_sd'),
                    'plus_2_sd' => $formatCurve('plus_2_sd'),
                    'plus_3_sd' => $formatCurve('plus_3_sd'),
                    'minus_1_sd' => $formatCurve('minus_1_sd'),
                    'minus_2_sd' => $formatCurve('minus_2_sd'),
                    'minus_3_sd' => $formatCurve('minus_3_sd'),
                ];
            }
        }

        // MMDST Mapping
        $latestMmdst = MmdstAssessment::where('student_id', $student->id)
            ->with('sectorSummaries.category')
            ->latest('assessment_date')
            ->first();

        $mmdstResults = [
            'personal_social' => 'UNTESTABLE',
            'fine_motor'      => 'UNTESTABLE',
            'language'        => 'UNTESTABLE',
            'gross_motor'     => 'UNTESTABLE'
        ];

        if ($latestMmdst && $latestMmdst->sectorSummaries) {
            foreach ($latestMmdst->sectorSummaries as $s) {
                $catName = $s->category->category_name ?? $s->category->name ?? '';
                $res = $s->sector_result;

                if (stripos($catName, 'Personal') !== false) $mmdstResults['personal_social'] = $res;
                elseif (stripos($catName, 'Fine') !== false || stripos($catName, 'Halus') !== false) $mmdstResults['fine_motor'] = $res;
                elseif (stripos($catName, 'Language') !== false || stripos($catName, 'Bahasa') !== false) $mmdstResults['language'] = $res;
                elseif (stripos($catName, 'Gross') !== false || stripos($catName, 'Kasar') !== false) $mmdstResults['gross_motor'] = $res;
            }
        }

        $ageInMonthsNow = $student->birth_date ? Carbon::parse($student->birth_date)->diffInMonths(now()) : 0;
        $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kuku'];

        $defaultTeacherName = Auth::user()->name ?? 'Guru Kelas';
        $defaultPrincipalName = 'Kepala Sekolah';
        $defaultConsultantName = 'Psikolog / Konsultan';

        $themes = Theme::with(['subThemes.materials'])->get();

        return view('admin.reports-development.reports-development-create.index', compact(
            'student',
            'startDate',
            'endDate',
            'academicYear',
            'semester',
            'themes',
            'attendanceSummary',
            'lastMeasurement',
            'prefillPhysical',
            'chartData',
            'allStandardCurves',
            'latestMmdst',
            'mmdstResults',
            'ageInMonthsNow',
            'healthItems',
            'defaultTeacherName',
            'defaultPrincipalName',
            'defaultConsultantName'
        ));
    }

    // --- 5. STORE (PROSES SIMPAN) ---
    public function store(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'student_id' => 'required',
            'report_date' => 'required|date',
            'period_start_date' => 'required|date',
            'period_end_date' => 'required|date',
            'age_in_months' => 'required|integer',
        ]);

        DB::transaction(function () use ($request) {

            // 2. SIAPKAN DATA UTAMA
            // Ambil semua input kecuali token dan data kesehatan
            $dataToSave = $request->except(['_token', 'health', 'theme_notes']);

            // --- A. PROSES TANDA TANGAN (Base64 -> File Image) ---
            $signatureFields = [
                'parent_signature',
                'teacher_signature',
                'consultant_signature',
                'principal_signature'
            ];

            foreach ($signatureFields as $field) {
                if ($request->filled($field)) {
                    $base64_image = $request->input($field);

                    // Cek format base64
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $extension = strtolower($type[1]); // biasanya png
                        $decodedData = base64_decode($data);

                        if ($decodedData !== false) {
                            // Nama file unik: signatures/ID_Field_Timestamp.png
                            $filename = 'signatures/' . $request->student_id . '_' . $field . '_' . time() . '_' . Str::random(5) . '.' . $extension;

                            // Simpan file ke storage public
                            Storage::disk('public')->put($filename, $decodedData);

                            // GANTI data base64 dengan PATH file
                            $dataToSave[$field] = $filename;
                        }
                    }
                }
            }

            // --- B. PROSES GAMBAR GRAFIK (Base64 -> File Image) ---
            $chartFields = [
                'chart_bbu_image',
                'chart_tbu_image',
                'chart_bbtb_image',
                'chart_imtu_image'
            ];

            foreach ($chartFields as $field) {
                if ($request->filled($field)) {
                    $base64_image = $request->input($field);

                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $extension = strtolower($type[1]);
                        $decodedData = base64_decode($data);

                        if ($decodedData !== false) {
                            // Nama file: charts/ID_Jenis_Timestamp.png
                            $filename = 'charts/' . $request->student_id . '_' . $field . '_' . time() . '.' . $extension;

                            Storage::disk('public')->put($filename, $decodedData);

                            $dataToSave[$field] = $filename;
                        }
                    }
                }
            }

            // 4. SIMPAN DATA KE DATABASE
            $report = StudentDevelopmentReport::create($dataToSave);

            // 5. SIMPAN DATA KESEHATAN
            if ($request->has('health')) {
                StudentDevelopmentReportHealth::create([
                    'student_development_report_id' => $report->id,
                    'vision' => $request->health['Mata - Penglihatan'] ?? null,
                    'hearing' => $request->health['Telinga - Pendengaran'] ?? null,
                    'teeth' => $request->health['Gigi'] ?? null,
                    'skin' => $request->health['Kulit'] ?? null,
                    'nails' => $request->health['Kuku'] ?? null,
                    'hygiene' => $request->health['Kebersihan'] ?? null,
                    'remarks' => $request->remarks ?? null
                ]);
            }
        });

        return redirect()->route('development-reports.history', $request->student_id)
            ->with('success', 'Laporan berhasil disimpan.');
    }

    // --- 6. PRINT / CETAK PDF (INI YANG ANDA BUTUHKAN) ---
    public function print($id)
    {
        // Ambil data report beserta relasinya
        $report = StudentDevelopmentReport::with(['student', 'healthDetail', 'mmdstAssessment'])
            ->findOrFail($id);

        // Load View PDF (Pastikan file ini ada di folder views)
        // resources/views/admin/development-reports/pdf.blade.php
        $pdf = Pdf::loadView('admin.reports-development.report-development-print.index', compact('report'))
            ->setPaper('a4', 'portrait');

        // Stream (Tampilkan di browser) atau Download
        return $pdf->stream('Raport_' . Str::slug($report->student->student_name) . '_' . $report->semester . '.pdf');
    }

    // --- 7. SHOW (DETAIL) ---
    public function show($id)
    {
        $report = StudentDevelopmentReport::with(['student', 'healthDetail'])->findOrFail($id);

        // Decode JSON snapshot untuk ditampilkan di view show (jika perlu grafik)
        $chartData = [
            'weight' => json_decode($report->weight_chart_snapshot),
            'height' => json_decode($report->height_chart_snapshot),
        ];

        return view('admin.development-reports.show', compact('report', 'chartData'));
    }

    // --- 8. DESTROY (HAPUS) ---
    public function destroy($id)
    {
        $report = StudentDevelopmentReport::findOrFail($id);
        $studentId = $report->student_id;

        // Hapus file tanda tangan jika ada (Opsional, untuk kebersihan server)
        $signatures = [$report->parent_signature, $report->teacher_signature, $report->consultant_signature, $report->principal_signature];
        foreach ($signatures as $sig) {
            if ($sig && Storage::disk('public')->exists($sig)) {
                Storage::disk('public')->delete($sig);
            }
        }

        $report->delete();
        return redirect()->route('development-reports.history', $studentId)->with('success', 'Laporan berhasil dihapus.');
    }

    // --- 9. AI GENERATOR ---
    public function generateAiNarrative(Request $request)
    {
        $model = 'gemini-2.5-flash';
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) return response()->json(['status' => 'error', 'message' => 'API Key Missing']);

        $prompt = "Buatkan narasi raport PAUD perkembangan anak usia dini singkat (3-4 kalimat) dan positif. Nama: {$request->student_name}. Kategori: {$request->category}. Hasil Data: {$request->result_summary}. Gunakan bahasa Indonesia yang formal namun hangat.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $text = $resData['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal generate.';
                return response()->json(['status' => 'success', 'text' => str_replace(['**', '*'], '', $text)]);
            }
            return response()->json(['status' => 'error', 'message' => 'AI Error']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
