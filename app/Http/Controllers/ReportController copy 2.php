<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\ReportHealthDetail;
use App\Models\Student;
use App\Models\Material;
use App\Models\DailyReportChildrenDetail;
use App\Models\ActivityTransaction;
use App\Models\Attendance;
use App\Models\Theme;
use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * HALAMAN UTAMA: List Siswa
     */
    public function index(Request $request)
    {
        $query = Student::whereHas('activityTransaction', function ($q) {
            $q->where('student_status', true);
        });

        $query->with(['activityTransaction.service', 'activityTransaction.program']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('student_name')->paginate(10);

        return view('admin.reports.reports-index.index', compact('students'));
    }

    /**
     * HALAMAN HISTORY RAPORT
     */
    public function history(Request $request, Student $student)
    {
        $query = Report::where('student_id', $student->id)->with('creator');

        // Tambahkan Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('report_title', 'like', '%' . $request->search . '%');
        }

        $reports = $query->latest()->paginate(10);

        return view('admin.reports.reports-history.index', compact('student', 'reports'));
    }

    public function show(Report $report)
    {
        // Eager loading untuk performa query
        $report->load([
            'student',
            'details.material.subTheme.theme', // Mengambil hierarki tema nilai
            'healthDetails',
            'creator'
        ]);

        // Mengelompokkan Nilai berdasarkan Tema agar tampilan rapi
        $groupedDetails = $report->details->groupBy(function ($detail) {
            return $detail->material->subTheme->theme->theme_name ?? 'Lainnya';
        });

        return view('admin.reports.reports-show.index', compact('report', 'groupedDetails'));
    }

    /**
     * LANGKAH 1: Pilih Periode
     */
    public function selectPeriod(Student $student)
    {
        return view('admin.reports.reports-selec-period.index', compact('student'));
    }

    /**
     * LANGKAH 2: Form Penilaian (Create)
     */
    public function create(Request $request, Student $student)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!$startDate || !$endDate) {
            return redirect()->route('reports.selectPeriod', $student->id)
                ->with('error', 'Silakan pilih periode raport terlebih dahulu.');
        }

        // 1. AMBIL TEMA & MATERIAL (Kurikulum)
        $themes = Theme::whereHas('subThemes', function ($q) use ($startDate, $endDate) {
            $q->where('sub_theme_on_report', true)
                ->where('sub_theme_is_active', true)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('sub_theme_start', [$startDate, $endDate])
                        ->orWhereBetween('sub_theme_end', [$startDate, $endDate]);
                });
        })->with(['subThemes' => function ($q) use ($startDate, $endDate) {
            $q->where('sub_theme_on_report', true)
                ->where('sub_theme_is_active', true)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('sub_theme_start', [$startDate, $endDate])
                        ->orWhereBetween('sub_theme_end', [$startDate, $endDate]);
                })
                ->with(['materials' => function ($qMat) {
                    $qMat->where('material_on_report', true);
                }]);
        }])->where('theme_on_report', true)->get();

        // 2. AMBIL DATA HARIAN
        $allDailyDetails = DailyReportChildrenDetail::whereHas('dailyReport', function ($q) use ($student, $startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereHas('activityTransaction', function ($trx) use ($student) {
                    $trx->where('student_id', $student->id);
                });
        })->with('dailyReport')->get();

        // 3. AUTO-CALCULATION SCORES (MODUS)
        $calculatedScores = [];
        foreach ($themes as $theme) {
            foreach ($theme->subThemes as $subTheme) {
                foreach ($subTheme->materials as $material) {
                    $relevantDetails = $allDailyDetails->filter(function ($detail) use ($subTheme, $material) {
                        $reportDate = \Carbon\Carbon::parse($detail->dailyReport->created_at)->format('Y-m-d');
                        $isInRange = ($reportDate >= $subTheme->sub_theme_start) && ($reportDate <= $subTheme->sub_theme_end);
                        if (!$isInRange) return false;
                        return ($detail->session1_material_id == $material->id && $detail->session1_activity) ||
                            ($detail->session2_material_id == $material->id && $detail->session2_activity);
                    });

                    $scores = [];
                    foreach ($relevantDetails as $detail) {
                        if ($detail->session1_material_id == $material->id) $scores[] = $detail->session1_activity;
                        if ($detail->session2_material_id == $material->id) $scores[] = $detail->session2_activity;
                    }

                    if (!empty($scores)) {
                        $counts = array_count_values($scores);
                        arsort($counts);
                        $calculatedScores[$material->id] = array_key_first($counts);
                    } else {
                        $calculatedScores[$material->id] = null;
                    }
                }
            }
        }

        // 4. PRESENSI
        $attendanceData = Attendance::query()
            ->whereHas('activityTransaction', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->whereHas('attendanceTransaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date_attendance', [$startDate, $endDate]);
            })->get();

        $attendanceSummary = [
            'Sakit' => $attendanceData->where('check_in_status', 'Sick')->count(),
            'Izin'  => $attendanceData->where('check_in_status', 'Excused')->count(),
            'Alpha' => $attendanceData->where('check_in_status', 'Absent')->count(),
            'Hadir' => $attendanceData->where('check_in_status', 'Present')->count(),
        ];

        // 5. AMBIL DATA PERTUMBUHAN TERAKHIR
        $userIdToCheck = $student->user_id ?? $student->id;

        $lastMeasurement = Measurement::where('user_id', $userIdToCheck)
            ->orderBy('date_measurement', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $growthNarration = "";
        $prefillPhysical = [
            'weight' => 0,
            'height' => 0,
            'head' => 0,
        ];

        if ($lastMeasurement) {
            $prefillPhysical = [
                'weight' => $lastMeasurement->weight,
                'height' => $lastMeasurement->height,
                'head' => $lastMeasurement->head_circumference,
            ];
            // Generate Narasi Otomatis (Rule Based)
            $growthNarration = $this->generateGrowthRuleBasedNarrative($lastMeasurement);
        }

        $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kerapian', 'Rambut', 'Kuku'];

        // Variable Nama Default
        $defaultTeacherName = auth()->user()->name;
        $defaultPrincipalName = "Kepala Sekolah";
        $defaultConsultantName = "Psikolog Anak";

        return view('admin.reports.reports-create.index', compact(
            'student',
            'startDate',
            'endDate',
            'themes',
            'calculatedScores',
            'attendanceSummary',
            'healthItems',
            'defaultTeacherName',
            'defaultPrincipalName',
            'defaultConsultantName',
            'lastMeasurement',
            'growthNarration',
            'prefillPhysical'
        ));
    }

    /**
     * SIMPAN RAPORT (STORE)
     * [PENTING] Mapping Input View ke Kolom Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'report_date' => 'required|date',
            'report_title' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Handling Activity Transaction (Ambil transaksi terakhir siswa)
            $trx = ActivityTransaction::where('student_id', $request->student_id)->latest()->first();
            // Fallback jika tidak ada trx, gunakan ID 1 (pastikan ID 1 ada di DB Transaction Anda)
            $trxId = $trx ? $trx->id : 1;

            // 2. MAPPING DATA: Input View -> Kolom Tabel Reports
            $reportData = [
                'activity_transaction_id' => $trxId,
                'student_id'   => $request->student_id,
                'created_by'   => auth()->id(),
                'report_title' => $request->report_title,
                'start_date'   => $request->start_date,
                'end_date'     => $request->end_date,
                'report_date'  => $request->report_date,
                'attendance_summary' => $request->attendance_summary, // JSON String dari input hidden

                // Data Fisik
                'weight' => $request->berat_badan,
                'height' => $request->tinggi_badan,
                // Note: Schema 'reports' tidak punya 'head_circumference', jadi tidak disimpan di kolom khusus.

                // Narasi & Teks (MAPPING MANUAL)
                'religious_values_text' => $request->narasi_agama,
                'identity_text'         => $request->narasi_jatidiri,
                'literacy_steam_text'   => $request->narasi_steam,
                'p5_text'               => $request->narasi_p5,
                'parent_reflection_text' => $request->refleksi_ortu,

                // Gabungkan Info Perkembangan + Lingkar Kepala (Agar data LK tidak hilang)
                'development_info_text' => $request->info_perkembangan .
                    ($request->lingkar_kepala ? "\n(Lingkar Kepala: " . $request->lingkar_kepala . " cm)" : ""),

                // Nama Penanda Tangan
                'parent_name'     => $request->nama_ortu,
                'teacher_name'    => $request->nama_guru,
                'consultant_name' => $request->nama_konsultan,
                'principal_name'  => $request->nama_kepsek,
            ];

            // 3. HANDLE UPLOAD FOTO (MAPPING MANUAL)
            // Format: 'nama_input_view' => 'nama_kolom_db'
            $photoMap = [
                'foto_agama' => 'religious_values_photo',
                'foto_jatidiri' => 'identity_photo',
                'foto_steam' => 'literacy_steam_photo',
                'foto_p5' => 'p5_photo',
                'parent_reflection_photo' => 'parent_reflection_photo',
                'development_info_photo' => 'development_info_photo',
            ];

            foreach ($photoMap as $inputName => $dbColumn) {
                if ($request->hasFile($inputName)) {
                    $reportData[$dbColumn] = $request->file($inputName)->store('reports/' . $request->student_id . '/photos', 'public');
                }
            }

            // 4. HANDLE TANDA TANGAN DIGITAL (BASE64)
            // Format: 'nama_input_view' => 'nama_kolom_db'
            $sigMap = [
                'ttd_ortu'      => 'parent_signature',
                'ttd_guru'      => 'teacher_signature',
                'ttd_konsultan' => 'consultant_signature',
                'ttd_kepsek'    => 'principal_signature'
            ];

            foreach ($sigMap as $inputName => $dbColumn) {
                if ($request->filled($inputName)) {
                    // Panggil helper function untuk save base64
                    $reportData[$dbColumn] = $this->saveBase64Image($request->input($inputName), $request->student_id, 'sig_' . $inputName);
                }
            }

            // 5. CREATE REPORT
            $report = Report::create($reportData);

            // 6. SIMPAN DETAIL NILAI (SCORES)
            if ($request->has('scores')) {
                foreach ($request->scores as $materialId => $scoreValue) {
                    if ($scoreValue) {
                        $material = Material::find($materialId);
                        ReportDetail::create([
                            'report_id'    => $report->id,
                            'material_id'  => $materialId,
                            'theme_id'     => $material->subTheme->theme_id ?? null,
                            'sub_theme_id' => $material->sub_theme_id ?? null,
                            'score'        => $scoreValue
                        ]);
                    }
                }
            }

            // 7. SIMPAN DATA KESEHATAN
            if ($request->has('health')) {
                foreach ($request->health as $itemName => $itemValue) {
                    ReportHealthDetail::create([
                        'report_id'  => $report->id,
                        'item_name'  => $itemName,
                        'item_value' => $itemValue
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('reports.history', $request->student_id)->with('success', 'Raport berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Debugging: Tampilkan pesan error lengkap
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage() . ' di baris ' . $e->getLine())->withInput();
        }
    }

    /**
     * FORM EDIT RAPORT
     */
    public function edit(Report $report)
    {
        $student = $report->student;

        // Ambil struktur tema
        $themes = Theme::with(['subThemes.materials' => function ($q) {
            $q->where('material_on_report', true);
        }])->where('theme_on_report', true)->get();

        // Mapping Data Tersimpan
        $savedScores = $report->details->pluck('score', 'material_id')->toArray();
        $healthDetails = $report->healthDetails->pluck('item_value', 'item_name')->toArray();
        $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kerapian', 'Rambut', 'Kuku'];

        return view('admin.reports.reports-edit.index', compact(
            'report',
            'student',
            'themes',
            'savedScores',
            'healthDetails',
            'healthItems'
        ));
    }

    /**
     * UPDATE RAPORT
     */
    public function update(Request $request, Report $report)
    {
        DB::beginTransaction();
        try {
            // 1. MAPPING DATA UTAMA
            $data = [
                'report_title'       => $request->report_title,
                'report_date'        => $request->report_date,
                'attendance_summary' => $request->attendance_summary,

                'weight' => $request->berat_badan,
                'height' => $request->tinggi_badan,

                'religious_values_text'  => $request->narasi_agama,
                'identity_text'          => $request->narasi_jatidiri,
                'literacy_steam_text'    => $request->narasi_steam,
                'p5_text'                => $request->narasi_p5,
                'parent_reflection_text' => $request->refleksi_ortu,

                // Update text perkembangan + Lingkar kepala jika ada input baru
                'development_info_text'  => $request->info_perkembangan .
                    ($request->lingkar_kepala ? "\n(Lingkar Kepala: " . $request->lingkar_kepala . " cm)" : ""),

                'parent_name'     => $request->nama_ortu,
                'teacher_name'    => $request->nama_guru,
                'consultant_name' => $request->nama_konsultan,
                'principal_name'  => $request->nama_kepsek,
            ];

            // 2. UPDATE FOTO (Replace Old)
            $photoMap = [
                'foto_agama' => 'religious_values_photo',
                'foto_jatidiri' => 'identity_photo',
                'foto_steam' => 'literacy_steam_photo',
                'foto_p5' => 'p5_photo',
                'parent_reflection_photo' => 'parent_reflection_photo',
                'development_info_photo' => 'development_info_photo',
            ];

            foreach ($photoMap as $inputName => $dbColumn) {
                if ($request->hasFile($inputName)) {
                    // Hapus lama jika ada
                    if ($report->$dbColumn && Storage::disk('public')->exists($report->$dbColumn)) {
                        Storage::disk('public')->delete($report->$dbColumn);
                    }
                    $data[$dbColumn] = $request->file($inputName)->store('reports/' . $report->student_id . '/photos', 'public');
                }
            }

            // 3. UPDATE TANDA TANGAN (Base64)
            $sigMap = [
                'ttd_ortu'      => 'parent_signature',
                'ttd_guru'      => 'teacher_signature',
                'ttd_konsultan' => 'consultant_signature',
                'ttd_kepsek'    => 'principal_signature'
            ];

            foreach ($sigMap as $inputName => $dbColumn) {
                // Hanya update jika ada input tanda tangan baru (string base64 panjang)
                if ($request->filled($inputName) && strlen($request->input($inputName)) > 100) {
                    // Hapus signature lama
                    if ($report->$dbColumn && Storage::disk('public')->exists($report->$dbColumn)) {
                        Storage::disk('public')->delete($report->$dbColumn);
                    }
                    $data[$dbColumn] = $this->saveBase64Image($request->input($inputName), $report->student_id, 'sig_' . $inputName);
                }
            }

            $report->update($data);

            // 4. UPDATE SCORES
            $report->details()->delete();
            if ($request->has('scores')) {
                foreach ($request->scores as $materialId => $scoreValue) {
                    if ($scoreValue) {
                        $material = Material::find($materialId);
                        ReportDetail::create([
                            'report_id'    => $report->id,
                            'material_id'  => $materialId,
                            'theme_id'     => $material->subTheme->theme_id ?? null,
                            'sub_theme_id' => $material->sub_theme_id ?? null,
                            'score'        => $scoreValue
                        ]);
                    }
                }
            }

            // 5. UPDATE HEALTH
            $report->healthDetails()->delete();
            if ($request->has('health')) {
                foreach ($request->health as $itemName => $itemValue) {
                    ReportHealthDetail::create([
                        'report_id'  => $report->id,
                        'item_name'  => $itemName,
                        'item_value' => $itemValue
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('reports.history', $report->student_id)->with('success', 'Raport berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY
     */
    public function destroy(Report $report)
    {
        $studentId = $report->student_id;

        // Daftar semua kolom file untuk dihapus
        $files = [
            $report->religious_values_photo,
            $report->identity_photo,
            $report->literacy_steam_photo,
            $report->p5_photo,
            $report->parent_reflection_photo,
            $report->development_info_photo,
            $report->parent_signature,
            $report->teacher_signature,
            $report->consultant_signature,
            $report->principal_signature
        ];

        foreach ($files as $f) {
            if ($f && Storage::disk('public')->exists($f)) {
                Storage::disk('public')->delete($f);
            }
        }

        $report->details()->delete();
        $report->healthDetails()->delete();
        $report->delete();

        return redirect()->route('reports.history', $studentId)->with('success', 'Data raport berhasil dihapus.');
    }

    /**
     * Helper: Simpan Base64 Image (Signature)
     */
    private function saveBase64Image($base64String, $studentId, $prefix)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, etc.

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('Tipe gambar tidak valid');
            }

            $image = base64_decode($base64String);
            if ($image === false) {
                throw new \Exception('Gagal decode base64');
            }

            $filename = 'reports/' . $studentId . '/signatures/' . $prefix . '_' . time() . '.' . $type;
            Storage::disk('public')->put($filename, $image);

            return $filename;
        }

        return null;
    }

    /**
     * API GENERATE TEXT AI (Dipanggil via AJAX)
     */
    public function generateNarrative(Request $request)
    {
        $request->validate([
            'student_name' => 'required',
            'category' => 'required',
        ]);

        $studentName = $request->student_name;
        $category = $request->category;
        $scoreData = $request->score_data ?? 'Belum ada nilai spesifik';

        $prompt = "Buatkan narasi deskriptif singkat (maksimal 3 paragraf) dan positif untuk raport PAUD. Jangan ada kalimat pembuka seperti 'Tentu ini draftnya'. Langsung ke isi.
        Nama Anak: {$studentName}.
        Aspek: {$category}.
        Data Nilai: " . json_encode($scoreData) . ".
        Instruksi: Gunakan bahasa Indonesia yang formal namun hangat. Sebutkan nama anak. Fokus pada kemajuan.";

        try {
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                return response()->json(['status' => 'error', 'message' => 'API Key belum dikonfigurasi']);
            }

            $model = 'gemini-2.5-flash';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal generate teks.';

                // Bersihkan formatting markdown (bintang tebal)
                $text = str_replace(['**', '*'], '', $text);

                return response()->json(['status' => 'success', 'text' => $text]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'AI Error: ' . $response->body()]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    /**
     * PRINT PDF
     */
    public function printPdf(Report $report)
    {
        $report->load(['student', 'details.material.subTheme.theme', 'healthDetails']);
        $groupedDetails = $report->details->groupBy('theme_id');

        $pdf = Pdf::loadView('admin.reports.reports-pdf.index', compact('report', 'groupedDetails'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Raport_' . $report->student->student_name . '.pdf');
    }

    /**
     * Helper untuk Narasi Pertumbuhan (Rule Based)
     * Mengolah JSON calculation_results untuk pre-fill form
     */
    private function generateGrowthRuleBasedNarrative($data)
    {
        // Format Tanggal
        $tanggal = Carbon::parse($data->date_measurement)->locale('id')->translatedFormat('d F Y');

        // Ambil data JSON Calculation
        $calc = $data->calculation_results;

        // Pastikan format array (jika tersimpan sebagai string di DB)
        if (is_string($calc)) {
            $calc = json_decode($calc, true);
        }
        $calc = $calc ?? [];

        // 1. Fakta Fisik
        $text = "Berdasarkan pengukuran fisik terakhir pada tanggal $tanggal, Ananda memiliki berat badan {$data->weight} kg, tinggi badan {$data->height} cm, dan lingkar kepala {$data->head_circumference} cm. ";

        // 2. Status Gizi (Ambil dari key JSON yang sesuai: "BB/U", "PB/U")
        $statusGizi = $calc['BB/U'] ?? null;
        $statusTinggi = $calc['PB/U'] ?? $calc['TB/U'] ?? null; // Handle variasi key
        $statusProporsi = $calc['PB/BB'] ?? $calc['BB/TB'] ?? null;

        if ($statusGizi && $statusTinggi) {
            $text .= "Hasil analisis menunjukkan status gizi (BB/U) **{$statusGizi}**, dengan postur tubuh (TB/U) **{$statusTinggi}**. ";
        }

        if ($statusProporsi) {
            $text .= "Proporsi berat terhadap tinggi badan tergolong **{$statusProporsi}**. ";
        }

        // 3. Rekomendasi Sederhana
        $combinedStatus = strtolower(($statusGizi ?? '') . ' ' . ($statusTinggi ?? '') . ' ' . ($statusProporsi ?? ''));

        if (str_contains($combinedStatus, 'kurang') || str_contains($combinedStatus, 'buruk') || str_contains($combinedStatus, 'stunted') || str_contains($combinedStatus, 'pendek')) {
            $text .= "Disarankan untuk meningkatkan asupan nutrisi (terutama protein hewani) dan memantau tumbuh kembang secara rutin.";
        } else if (str_contains($combinedStatus, 'lebih') || str_contains($combinedStatus, 'obesitas') || str_contains($combinedStatus, 'risiko')) {
            $text .= "Disarankan untuk menjaga pola makan seimbang, kurangi gula/lemak berlebih, dan perbanyak aktivitas fisik.";
        } else {
            $text .= "Pertumbuhan Ananda berlangsung dengan baik. Pertahankan pola makan sehat dan stimulasi yang tepat.";
        }

        return $text;
    }
}
