<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Theme;
use App\Models\Student;
use App\Models\ApiGemini;
use App\Models\Attendance;
use App\Models\Measurement;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GrowthStandard;
use App\Models\MmdstAssessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\StudentDevelopmentReport;
use App\Models\StudentDevelopmentReportHealth;
use Illuminate\Support\Facades\Log; // WAJIB: Untuk logging

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
        if ($student->gender == 1 || strtolower($student->gender) == 'male' || strtoupper($student->gender) == 'L' || strtolower($student->gender) == 'laki-laki') {
            $targetGender = 'male';
        } else {
            $targetGender = 'female';
        }

        $standards = GrowthStandard::where('gender', $targetGender)
            ->where('is_active', true)
            ->get();
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
                // Menggunakan stimulation_category_id untuk pencocokan yang akurat
                if ($s->stimulation_category_id == 1) {
                    $mmdstResults['personal_social'] = $s->sector_result;
                } elseif ($s->stimulation_category_id == 2) {
                    $mmdstResults['fine_motor'] = $s->sector_result;
                } elseif ($s->stimulation_category_id == 3) {
                    $mmdstResults['language'] = $s->sector_result;
                } elseif ($s->stimulation_category_id == 4) {
                    $mmdstResults['gross_motor'] = $s->sector_result;
                }
            }
        }

        $ageInMonthsNow = $student->birth_date ? Carbon::parse($student->birth_date)->diffInMonths(now()) : 0;
        $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kuku'];

        $defaultTeacherName = Auth::user()->name ?? 'Guru Kelas';
        $defaultPrincipalName = 'Kepala Sekolah';
        $defaultConsultantName = 'Konsultan Tumbuh Kembang Anak';

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

    // --- 5. STORE (PROSES SIMPAN DENGAN LOGGING) ---
    public function store(Request $request)
    {
        Log::info('--- MEMULAI PROSES STORE RAPORT ---');
        Log::info('Data Request Awal:', $request->all());

        try {
            // 1. VALIDASI
            $validated = $request->validate([
                'student_id' => 'required',
                'report_date' => 'required|date',
                'period_start_date' => 'required|date',
                'period_end_date' => 'required|date',
                'age_in_months' => 'required',
            ]);
            Log::info('Validasi Sukses.');

            DB::beginTransaction();

            // 2. SIAPKAN DATA UTAMA
            $dataToSave = $request->except(['_token', 'health', 'theme_notes']);

            // --- A. PROSES TANDA TANGAN ---
            $signatureFields = [
                'parent_signature',
                'teacher_signature',
                'consultant_signature',
                'principal_signature'
            ];

            foreach ($signatureFields as $field) {
                if ($request->filled($field)) {
                    $base64_image = $request->input($field);
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $extension = strtolower($type[1]);
                        $decodedData = base64_decode($data);

                        if ($decodedData !== false) {
                            $filename = 'signatures/' . $request->student_id . '_' . $field . '_' . time() . '_' . Str::random(5) . '.' . $extension;
                            Storage::disk('public')->put($filename, $decodedData);
                            $dataToSave[$field] = $filename;
                            Log::info("Signature saved: {$field} -> {$filename}");
                        }
                    }
                }
            }

            // --- B. PROSES GAMBAR GRAFIK ---
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
                            $filename = 'charts/' . $request->student_id . '_' . $field . '_' . time() . '.' . $extension;
                            Storage::disk('public')->put($filename, $decodedData);
                            $dataToSave[$field] = $filename;
                            Log::info("Chart saved: {$field} -> {$filename}");
                        }
                    }
                }
            }

            // --- D. SIMPAN LAPORAN UTAMA ---
            Log::info('Menyimpan Data ke Tabel Reports...', $dataToSave);
            $report = StudentDevelopmentReport::create($dataToSave);
            Log::info('Berhasil Simpan Report ID: ' . $report->id);

            // --- E. SIMPAN DETAIL KESEHATAN ---
            if ($request->has('health')) {
                Log::info('Menyimpan Data Kesehatan...', $request->health);
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

            DB::commit();
            Log::info('--- TRANSAKSI SELESAI ---');

            return redirect()->route('development-reports.history', $request->student_id)
                ->with('success', 'Laporan berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // ERROR VALIDASI
            Log::error('VALIDATION ERROR:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // ERROR UMUM / SQL
            DB::rollBack();
            Log::error('CRITICAL ERROR SAAT STORE: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // TAMPILKAN KE LAYAR (DEBUGGING ONLY)
            dd([
                'MESSAGE' => $e->getMessage(),
                'FILE' => $e->getFile(),
                'LINE' => $e->getLine(),
                'REQUEST_DATA' => $request->all()
            ]);
        }
    }

    // --- 6. PRINT / CETAK PDF ---
    public function print($id)
    {
        $report = StudentDevelopmentReport::with(['student', 'healthDetail', 'mmdstAssessment'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.reports-development.report-development-print.index', compact('report'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Hasil_Pertumbuhan_Perkembangan_' . Str::slug($report->student->student_name) . '_' . $report->semester . '.pdf');
    }

    // --- 6b. PRINT PDF BY STUDENT (Otomatis ambil report terbaru atau buatkan preview PDF) ---
    public function printByStudent(Student $student)
    {
        $report = StudentDevelopmentReport::where('student_id', $student->id)
            ->latest('report_date')
            ->with(['student', 'healthDetail', 'mmdstAssessment'])
            ->first();

        if (!$report) {
            $lastMeasurement = Measurement::whereHas('activityTransaction', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })->orderByDesc('date_measurement')->first();

            $latestMmdst = MmdstAssessment::where('student_id', $student->id)
                ->with('sectorSummaries')
                ->latest('assessment_date')
                ->first();

            $weight = (float)($lastMeasurement->weight ?? 0);
            $height = (float)($lastMeasurement->height ?? 0);
            $head = (float)($lastMeasurement->head_circumference ?? 0);
            $bmi = ($height > 0) ? round($weight / pow($height / 100, 2), 2) : 0;
            $ageInMonths = $student->birth_date ? Carbon::parse($student->birth_date)->diffInMonths(now()) : 0;

            $report = new StudentDevelopmentReport([
                'student_id' => $student->id,
                'report_date' => date('Y-m-d'),
                'period_start_date' => date('Y-m-d', strtotime('-6 months')),
                'period_end_date' => date('Y-m-d'),
                'academic_year' => date('Y') . ' / ' . (date('Y') + 1),
                'semester' => 'Semester 1 (Ganjil)',
                'age_in_months' => $ageInMonths,
                'weight_kg' => $weight,
                'height_cm' => $height,
                'head_circumference_cm' => $head,
                'bmi' => $bmi,
                'growth_analysis_desc' => 'Pertumbuhan fisik berdasarkan pengukuran terakhir pada ' . ($lastMeasurement ? Carbon::parse($lastMeasurement->date_measurement)->translatedFormat('d F Y') : date('d F Y')) . '.',
                'mmdst_final_result' => $latestMmdst->final_result ?? 'NORMAL',
                'mmdst_personal_social_result' => 'NORMAL',
                'personal_social_desc' => 'Perkembangan personal sosial tumbuh sesuai usia.',
                'mmdst_fine_motor_result' => 'NORMAL',
                'fine_motor_desc' => 'Koordinasi motorik halus berkembang dengan baik.',
                'mmdst_language_result' => 'NORMAL',
                'language_desc' => 'Kemampuan komunikasi dan bahasa berkembang sesuai usia.',
                'mmdst_gross_motor_result' => 'NORMAL',
                'gross_motor_desc' => 'Gerak motorik kasar berkembang lancar.',
                'attendance_present' => 0,
                'attendance_sick' => 0,
                'attendance_permission' => 0,
                'attendance_alpha' => 0,
                'teacher_notes' => 'Catatan perkembangan anak.',
                'teacher_recommendations' => 'Rekomendasi pertumbuhan dan perkembangan.',
                'parent_name' => 'Orang Tua / Wali',
                'teacher_name' => Auth::user()->name ?? 'Pendamping / Guru',
                'consultant_name' => 'Konsultan Tumbuh Kembang',
                'principal_name' => 'Kepala / Pimpinan Daycare',
            ]);

            $report->setRelation('student', $student);
            $report->setRelation('healthDetail', new StudentDevelopmentReportHealth([
                'vision' => 'Baik',
                'hearing' => 'Baik',
                'teeth' => 'Baik',
                'skin' => 'Sehat',
                'nails' => 'Bersih',
                'hygiene' => 'Baik'
            ]));
        }

        $pdf = Pdf::loadView('admin.reports-development.report-development-print.index', compact('report'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Hasil_Pertumbuhan_Perkembangan_' . Str::slug($student->student_name) . '.pdf');
    }

    // --- 7. SHOW (DETAIL) ---
    public function show($id)
    {
        $report = StudentDevelopmentReport::with(['student', 'healthDetail'])->findOrFail($id);

        // Decode JSON snapshot (opsional, jika masih dipakai di view show)
        $chartData = [
            'weight' => json_decode($report->weight_chart_snapshot ?? '[]'),
            'height' => json_decode($report->height_chart_snapshot ?? '[]'),
        ];

        return view('admin.reports-development.reports-development-show.index', compact('report', 'chartData'));
    }

    // --- 8. DESTROY (HAPUS) ---
    // --- 8. DESTROY (HAPUS DATA & FILE) ---
    public function destroy($id)
    {
        $report = StudentDevelopmentReport::findOrFail($id);
        $studentId = $report->student_id;

        // 1. Daftar Kolom File yang Harus Dihapus
        $fileColumns = [
            // Tanda Tangan
            'parent_signature',
            'teacher_signature',
            'consultant_signature',
            'principal_signature',
            // Grafik
            'chart_bbu_image',
            'chart_tbu_image',
            'chart_bbtb_image',
            'chart_imtu_image'
        ];

        // 2. Loop & Hapus File Fisik
        foreach ($fileColumns as $col) {
            if ($report->$col && Storage::disk('public')->exists($report->$col)) {
                Storage::disk('public')->delete($report->$col);
            }
        }

        // 3. Hapus Data Database
        $report->delete();

        return redirect()->route('development-reports.history', $studentId)
            ->with('success', 'Laporan dan semua file terkait berhasil dihapus.');
    }

    // --- 9. AI GENERATOR (UPDATED) ---
    public function generateAiNarrative(Request $request)
    {
        // 1. Ambil konfigurasi aktif dari database
        $aiConfig = ApiGemini::where('is_active', true)->first();

        // 2. Validasi apakah config ada
        if (!$aiConfig || empty($aiConfig->api_key)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Key/Model belum dikonfigurasi di database.'
            ]);
        }

        // 3. Set variabel dari database
        $apiKey = $aiConfig->api_key;
        $model = $aiConfig->model; // Contoh: 'gemini-1.5-flash' (sesuai isi DB)

        $prompt = "Buatkan narasi raport PAUD perkembangan anak usia dini singkat (3-4 kalimat) dan positif. Nama: {$request->student_name}. Kategori: {$request->category}. Hasil Data: {$request->result_summary}. Gunakan bahasa Indonesia yang formal namun hangat. Jangan ada kalimat pembuka seperti 'Tentu ini draftnya'. Langsung ke isi. saat menyebutkan namanya gunakan Ananda terlebih dahulu.";

        try {
            // 4. Request HTTP menggunakan variabel dinamis $model dan $apiKey
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $text = $resData['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal generate.';

                // Bersihkan formatting markdown
                return response()->json(['status' => 'success', 'text' => str_replace(['**', '*'], '', $text)]);
            }

            // Tampilkan pesan error dari response body agar lebih jelas debugging-nya
            return response()->json(['status' => 'error', 'message' => 'AI Error: ' . $response->body()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    // --- EDIT (TAMPILKAN FORM EDIT) ---
    public function edit($id)
    {
        $report = StudentDevelopmentReport::with(['student', 'healthDetail'])->findOrFail($id);
        $student = $report->student;

        // 1. DATA GRAFIK (PERBAIKAN: Tambahkan SD Category & Status Gizi)
        $measurements = Measurement::whereHas('activityTransaction', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->orderBy('date_measurement', 'asc')->get();

        $chartData = $measurements->map(function ($m) use ($student) {
            $ageInMonths = Carbon::parse($student->birth_date)->diffInMonths($m->date_measurement);
            $bmi = ($m->height > 0) ? ($m->weight / pow($m->height / 100, 2)) : 0;

            // Decode JSON jika tersimpan sebagai string
            $sd = is_string($m->sd_category) ? json_decode($m->sd_category, true) : ($m->sd_category ?? []);
            $status = is_string($m->calculation_results) ? json_decode($m->calculation_results, true) : ($m->calculation_results ?? []);

            return [
                'age' => $ageInMonths,
                'weight' => (float)$m->weight,
                'height' => (float)$m->height,
                'bmi' => round($bmi, 2),
                'date' => $m->date_measurement,
                'sd_category' => $sd,         // <--- INI WAJIB ADA
                'status_gizi' => $status,     // <--- INI WAJIB ADA
            ];
        })->values();

        // 2. STANDARD KURVA (Sama seperti create)
        $targetGender = ($student->gender == 1 || strtolower($student->gender) == 'male' || $student->gender == 'L') ? 'male' : 'female';
        $standards = GrowthStandard::where('gender', $targetGender)->where('is_active', true)->get();

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

        $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kuku'];

        // Attendance Summary dari data tersimpan
        $attendanceSummary = [
            'Hadir' => $report->attendance_present,
            'Sakit' => $report->attendance_sick,
            'Izin' => $report->attendance_permission,
            'Alpha' => $report->attendance_alpha,
        ];

        return view('admin.reports-development.report-development-edit.index', compact(
            'report',
            'student',
            'chartData',
            'allStandardCurves',
            'healthItems',
            'attendanceSummary'
        ));
    }

    // --- UPDATE (SIMPAN PERUBAHAN) ---
    public function update(Request $request, $id)
    {
        Log::info('--- MEMULAI PROSES UPDATE RAPORT --- ID: ' . $id);

        try {
            $report = StudentDevelopmentReport::findOrFail($id);

            // Validasi
            $request->validate([
                'report_date' => 'required|date',
                'age_in_months' => 'required|integer',
            ]);

            DB::beginTransaction();

            $dataToUpdate = $request->except(['_token', '_method', 'health']);

            // --- A. PROSES TANDA TANGAN (Hapus Lama, Simpan Baru, atau Hapus Total) ---
            $signatureFields = ['parent_signature', 'teacher_signature', 'consultant_signature', 'principal_signature'];

            foreach ($signatureFields as $field) {
                $inputValue = $request->input($field);

                // KASUS 1: USER KLIK HAPUS (Input = 'DELETE')
                if ($inputValue === 'DELETE') {
                    // Hapus file fisik lama
                    if ($report->$field && Storage::disk('public')->exists($report->$field)) {
                        Storage::disk('public')->delete($report->$field);
                    }
                    $dataToUpdate[$field] = null; // Set DB jadi NULL
                }
                // KASUS 2: ADA GAMBAR BARU (Base64)
                elseif ($request->filled($field) && preg_match('/^data:image\/(\w+);base64,/', $inputValue, $type)) {
                    $data = substr($inputValue, strpos($inputValue, ',') + 1);
                    $extension = strtolower($type[1]);
                    $decodedData = base64_decode($data);

                    if ($decodedData !== false) {
                        // Hapus file fisik lama sebelum ganti
                        if ($report->$field && Storage::disk('public')->exists($report->$field)) {
                            Storage::disk('public')->delete($report->$field);
                        }

                        // Simpan file baru
                        $filename = 'signatures/' . $report->student_id . '_' . $field . '_' . time() . '_' . Str::random(5) . '.' . $extension;
                        Storage::disk('public')->put($filename, $decodedData);
                        $dataToUpdate[$field] = $filename;
                    }
                }
                // KASUS 3: TIDAK ADA PERUBAHAN (Input Kosong/Null)
                else {
                    // Jangan update field ini di DB (biarkan path lama)
                    unset($dataToUpdate[$field]);
                }
            }

            // --- B. PROSES GAMBAR GRAFIK (Selalu Update karena JS Generate Ulang) ---
            $chartFields = ['chart_bbu_image', 'chart_tbu_image', 'chart_bbtb_image', 'chart_imtu_image'];

            foreach ($chartFields as $field) {
                if ($request->filled($field)) {
                    $base64_image = $request->input($field);
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $extension = strtolower($type[1]);
                        $decodedData = base64_decode($data);

                        if ($decodedData !== false) {
                            // Hapus file grafik lama (PENTING: Biar gak numpuk)
                            if ($report->$field && Storage::disk('public')->exists($report->$field)) {
                                Storage::disk('public')->delete($report->$field);
                            }

                            $filename = 'charts/' . $report->student_id . '_' . $field . '_' . time() . '.' . $extension;
                            Storage::disk('public')->put($filename, $decodedData);
                            $dataToUpdate[$field] = $filename;
                        }
                    }
                }
            }

            // --- C. UPDATE DATABASE ---
            $report->update($dataToUpdate);

            // --- D. UPDATE DETAIL KESEHATAN ---
            if ($request->has('health')) {
                StudentDevelopmentReportHealth::updateOrCreate(
                    ['student_development_report_id' => $report->id],
                    [
                        'vision' => $request->health['Mata - Penglihatan'] ?? null,
                        'hearing' => $request->health['Telinga - Pendengaran'] ?? null,
                        'teeth' => $request->health['Gigi'] ?? null,
                        'skin' => $request->health['Kulit'] ?? null,
                        'nails' => $request->health['Kuku'] ?? null,
                        'hygiene' => $request->health['Kebersihan'] ?? null,
                        'remarks' => $request->remarks ?? null
                    ]
                );
            }

            DB::commit();
            return redirect()->route('development-reports.show', $report->id)
                ->with('success', 'Raport berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR UPDATE RAPORT: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }
}
