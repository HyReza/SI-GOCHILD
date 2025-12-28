<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Theme;
use App\Models\Report;
use App\Models\Student;
use App\Models\Material;
use App\Models\ApiGemini;
use App\Models\Attendance;
use App\Models\Measurement;
use App\Models\ReportDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReportHealthDetail;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\DailyReportChildrenDetail;

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

        if ($request->has('search') && $request->search != '') {
            $query->where('report_title', 'like', '%' . $request->search . '%');
        }

        $reports = $query->latest()->paginate(10);

        return view('admin.reports.reports-history.index', compact('student', 'reports'));
    }

    /**
     * HALAMAN DETAIL (SHOW)
     */
    public function show(Report $report)
    {
        $report->load([
            'student',
            'details.material.subTheme.theme',
            'healthDetails',
            'creator'
        ]);

        // Mengelompokkan Nilai berdasarkan Tema
        $groupedDetails = $report->details->groupBy(function ($detail) {
            return $detail->material->subTheme->theme->theme_name ?? 'Lainnya';
        });

        // Mengambil Deskripsi Per Tema (Theme Notes)
        // Kita ambil manual via DB Query agar tidak error jika Model relasi belum dibuat
        $themeNotes = DB::table('report_theme_notes')
            ->where('report_id', $report->id)
            ->pluck('note', 'theme_id')
            ->toArray();

        return view('admin.reports.reports-show.index', compact('report', 'groupedDetails', 'themeNotes'));
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

        // 2. AMBIL DATA HARIAN UNTUK PERHITUNGAN SKOR OTOMATIS
        $allDailyDetails = DailyReportChildrenDetail::whereHas('dailyReport', function ($q) use ($student, $startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereHas('activityTransaction', function ($trx) use ($student) {
                    $trx->where('student_id', $student->id);
                });
        })->with('dailyReport')->get();

        // 3. LOGIKA HITUNG SKOR (MODUS / TERBANYAK MUNCUL)
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
                        arsort($counts); // Urutkan dari yang terbanyak
                        $calculatedScores[$material->id] = array_key_first($counts);
                    } else {
                        $calculatedScores[$material->id] = null;
                    }
                }
            }
        }

        // 4. DATA PRESENSI
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

        // 5. AMBIL DATA PENGUKURAN (PERTUMBUHAN) TERAKHIR
        $userIdToCheck = $student->user_id ?? $student->id;
        $lastMeasurement = Measurement::where('user_id', $userIdToCheck)
            ->orderBy('date_measurement', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $growthNarration = "";
        $prefillPhysical = ['weight' => 0, 'height' => 0, 'head' => 0];

        if ($lastMeasurement) {
            $prefillPhysical = [
                'weight' => $lastMeasurement->weight,
                'height' => $lastMeasurement->height,
                'head' => $lastMeasurement->head_circumference,
            ];
            $growthNarration = $this->generateGrowthRuleBasedNarrative($lastMeasurement);
        }

        $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kerapian', 'Rambut', 'Kuku'];

        // Variable Default
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
     * SIMPAN RAPORT (STORE) - SUDAH TERMASUK THEME NOTES & TEACHER NOTES
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'report_date' => 'required|date',
            'report_title' => 'required|string',
            'semester' => 'nullable|string', // Validasi input semester
            'class_name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // A. Ambil Transaksi Aktivitas Terakhir
            $trx = ActivityTransaction::where('student_id', $request->student_id)->latest()->first();
            $trxId = $trx ? $trx->id : 1;

            // B. MAPPING DATA UTAMA KE TABEL REPORTS
            $reportData = [
                'activity_transaction_id' => $trxId,
                'student_id'   => $request->student_id,
                'created_by'   => auth()->id(),
                'report_title' => $request->report_title,

                // [PENTING] Simpan input semester
                'semester'     => $request->semester,
                'class_name'   => $request->class_name,

                'start_date'   => $request->start_date,
                'end_date'     => $request->end_date,
                'report_date'  => $request->report_date,
                'attendance_summary' => $request->attendance_summary,

                // 1. Data Fisik
                'weight' => $request->berat_badan,
                'height' => $request->tinggi_badan,

                // 2. Narasi Per Aspek (CP)
                'religious_values_text' => $request->narasi_agama,
                'identity_text'         => $request->narasi_jatidiri,
                'literacy_steam_text'   => $request->narasi_steam,
                'p5_text'               => $request->narasi_p5,
                'parent_reflection_text' => $request->refleksi_ortu,

                // 3. Info Perkembangan (Kesimpulan Fisik + Lingkar Kepala)
                'development_info_text' => $request->info_perkembangan .
                    ($request->lingkar_kepala ? "\n(Lingkar Kepala: " . $request->lingkar_kepala . " cm)" : ""),

                // 4. [PENTING] CATATAN & REKOMENDASI GURU
                'teacher_notes'   => $request->teacher_notes,
                'recommendations' => $request->recommendations,

                // 5. Nama Penanda Tangan
                'parent_name'     => $request->nama_ortu,
                'teacher_name'    => $request->nama_guru,
                'consultant_name' => $request->nama_konsultan,
                'principal_name'  => $request->nama_kepsek,
            ];

            // C. HANDLE UPLOAD FOTO
            $photoMap = [
                'foto_agama' => 'religious_values_photo',
                'foto_jatidiri' => 'identity_photo',
                'foto_steam' => 'literacy_steam_photo',
                'foto_p5' => 'p5_photo',
                'parent_reflection_photo' => 'parent_reflection_photo',
                'development_info_photo' => 'development_info_photo'
            ];
            foreach ($photoMap as $inputName => $dbColumn) {
                if ($request->hasFile($inputName)) {
                    $reportData[$dbColumn] = $request->file($inputName)->store('reports/' . $request->student_id . '/photos', 'public');
                }
            }

            // D. HANDLE TANDA TANGAN (Base64)
            $sigMap = ['ttd_ortu' => 'parent_signature', 'ttd_guru' => 'teacher_signature', 'ttd_konsultan' => 'consultant_signature', 'ttd_kepsek' => 'principal_signature'];
            foreach ($sigMap as $inputName => $dbColumn) {
                if ($request->filled($inputName)) {
                    $reportData[$dbColumn] = $this->saveBase64Image($request->input($inputName), $request->student_id, 'sig_' . $inputName);
                }
            }

            // E. SIMPAN RECORD UTAMA REPORT
            $report = Report::create($reportData);

            // F. SIMPAN NILAI CHECKLIST (DETAIL)
            if ($request->has('scores')) {
                foreach ($request->scores as $materialId => $scoreValue) {
                    if ($scoreValue) {
                        $material = Material::find($materialId);
                        ReportDetail::create([
                            'report_id' => $report->id,
                            'material_id' => $materialId,
                            'theme_id' => $material->subTheme->theme_id ?? null,
                            'sub_theme_id' => $material->sub_theme_id ?? null,
                            'score' => $scoreValue
                        ]);
                    }
                }
            }

            // G. [PENTING] SIMPAN DESKRIPSI PER TEMA (THEME NOTES)
            if ($request->has('theme_notes')) {
                foreach ($request->theme_notes as $themeId => $note) {
                    if (!empty($note)) {
                        DB::table('report_theme_notes')->insert([
                            'report_id' => $report->id,
                            'theme_id' => $themeId,
                            'note' => $note
                        ]);
                    }
                }
            }

            // H. SIMPAN DATA KESEHATAN
            if ($request->has('health')) {
                foreach ($request->health as $itemName => $itemValue) {
                    ReportHealthDetail::create([
                        'report_id' => $report->id,
                        'item_name' => $itemName,
                        'item_value' => $itemValue
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('reports.history', $request->student_id)->with('success', 'Raport berhasil dibuat dan semua data tersimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage() . ' Line: ' . $e->getLine())->withInput();
        }
    }

    /**
     * UPDATE RAPORT (EDIT) - SUDAH TERMASUK UPDATE THEME NOTES & TEACHER NOTES
     */
    public function update(Request $request, Report $report)
    {
        $request->validate([
            'report_title' => 'required|string',
            'report_date' => 'required|date',
            'semester' => 'nullable|string', // Validasi input semester
            'class_name'   => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. MAPPING DATA UTAMA
            $data = [
                'report_title' => $request->report_title,
                'report_date' => $request->report_date,
                // [PENTING] Update Semester
                'semester' => $request->semester,
                'class_name'   => $request->class_name,
                'attendance_summary' => $request->attendance_summary,
                'weight' => $request->berat_badan,
                'height' => $request->tinggi_badan,
                'religious_values_text' => $request->narasi_agama,
                'identity_text' => $request->narasi_jatidiri,
                'literacy_steam_text' => $request->narasi_steam,
                'p5_text' => $request->narasi_p5,
                'parent_reflection_text' => $request->refleksi_ortu,
                'development_info_text' => $request->info_perkembangan . ($request->lingkar_kepala ? "\n(Lingkar Kepala: " . $request->lingkar_kepala . " cm)" : ""),
                'teacher_notes' => $request->teacher_notes,
                'recommendations' => $request->recommendations,
                'parent_name' => $request->nama_ortu,
                'teacher_name' => $request->nama_guru,
                'consultant_name' => $request->nama_konsultan,
                'principal_name' => $request->nama_kepsek,
            ];

            // 2. UPDATE FOTO
            $photoMap = [
                'foto_agama' => 'religious_values_photo',
                'foto_jatidiri' => 'identity_photo',
                'foto_steam' => 'literacy_steam_photo',
                'foto_p5' => 'p5_photo',
                'parent_reflection_photo' => 'parent_reflection_photo',
                'development_info_photo' => 'development_info_photo'
            ];
            foreach ($photoMap as $inputName => $dbColumn) {
                if ($request->hasFile($inputName)) {
                    if ($report->$dbColumn && Storage::disk('public')->exists($report->$dbColumn)) {
                        Storage::disk('public')->delete($report->$dbColumn);
                    }
                    $data[$dbColumn] = $request->file($inputName)->store('reports/' . $report->student_id . '/photos', 'public');
                }
            }

            // 3. UPDATE TANDA TANGAN
            $sigMap = ['ttd_ortu' => 'parent_signature', 'ttd_guru' => 'teacher_signature', 'ttd_konsultan' => 'consultant_signature', 'ttd_kepsek' => 'principal_signature'];
            foreach ($sigMap as $inputName => $dbColumn) {
                if ($request->filled($inputName) && strlen($request->input($inputName)) > 100) {
                    if ($report->$dbColumn && Storage::disk('public')->exists($report->$dbColumn)) {
                        Storage::disk('public')->delete($report->$dbColumn);
                    }
                    $data[$dbColumn] = $this->saveBase64Image($request->input($inputName), $report->student_id, 'sig_' . $inputName);
                }
            }

            $report->update($data);

            // 4. UPDATE NILAI (Hapus Lama, Insert Baru)
            $report->details()->delete();
            if ($request->has('scores')) {
                foreach ($request->scores as $materialId => $scoreValue) {
                    if ($scoreValue) {
                        $material = Material::find($materialId);
                        ReportDetail::create([
                            'report_id' => $report->id,
                            'material_id' => $materialId,
                            'theme_id' => $material->subTheme->theme_id ?? null,
                            'sub_theme_id' => $material->subTheme->id ?? null,
                            'score' => $scoreValue
                        ]);
                    }
                }
            }

            // 5. UPDATE DESKRIPSI PER TEMA (Hapus Lama, Insert Baru)
            DB::table('report_theme_notes')->where('report_id', $report->id)->delete();
            if ($request->has('theme_notes')) {
                foreach ($request->theme_notes as $themeId => $note) {
                    if (!empty($note)) {
                        DB::table('report_theme_notes')->insert([
                            'report_id' => $report->id,
                            'theme_id' => $themeId,
                            'note' => $note
                        ]);
                    }
                }
            }

            // 6. UPDATE KESEHATAN
            $report->healthDetails()->delete();
            if ($request->has('health')) {
                foreach ($request->health as $itemName => $itemValue) {
                    ReportHealthDetail::create([
                        'report_id' => $report->id,
                        'item_name' => $itemName,
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
     * FORM EDIT (LENGKAP)
     */
    public function edit(Report $report)
    {
        $student = $report->student;
        $themes = Theme::with(['subThemes.materials' => function ($q) {
            $q->where('material_on_report', true);
        }])->where('theme_on_report', true)->get();

        $savedScores = $report->details->pluck('score', 'material_id')->toArray();
        $healthDetails = $report->healthDetails->pluck('item_value', 'item_name')->toArray();

        // Ambil saved theme notes
        $savedThemeNotes = DB::table('report_theme_notes')
            ->where('report_id', $report->id)
            ->pluck('note', 'theme_id')
            ->toArray();

        $healthItems = ['Mata - Penglihatan', 'Telinga - Pendengaran', 'Gigi', 'Kulit', 'Kebersihan', 'Kerapian', 'Rambut', 'Kuku'];

        return view('admin.reports.reports-edit.index', compact(
            'report',
            'student',
            'themes',
            'savedScores',
            'healthDetails',
            'healthItems',
            'savedThemeNotes'
        ));
    }

    /**
     * DESTROY
     */
    public function destroy(Report $report)
    {
        $studentId = $report->student_id;

        // Hapus Foto
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

        // Hapus Relasi
        $report->details()->delete();
        $report->healthDetails()->delete();
        DB::table('report_theme_notes')->where('report_id', $report->id)->delete(); // Hapus catatan tema

        $report->delete();

        return redirect()->route('reports.history', $studentId)->with('success', 'Data dihapus.');
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

        // Prompt tetap sama
        $prompt = "Buatkan narasi deskriptif singkat (maksimal 3 paragraf) dan positif untuk raport PAUD. Jangan ada kalimat pembuka seperti 'Tentu ini draftnya'. Langsung ke isi.
        Nama Anak: {$studentName}.
        Aspek: {$category}.
        Data Nilai: " . json_encode($scoreData) . ".
        Instruksi: Gunakan bahasa Indonesia yang formal namun hangat. Sebutkan nama anak. Fokus pada kemajuan.";

        try {
            // PERUBAHAN DISINI: Mengambil konfigurasi aktif dari Database
            $aiConfig = ApiGemini::where('is_active', true)->first();

            // Cek apakah konfigurasi ditemukan di database
            if (!$aiConfig || empty($aiConfig->api_key)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Konfigurasi API Key & Model belum diatur di database.'
                ]);
            }

            $apiKey = $aiConfig->api_key;
            $model = $aiConfig->model; // Ambil model dari DB, misal: 'gemini-1.5-flash'

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

    // public function generateNarrative(Request $request)
    // {
    //     $request->validate([
    //         'student_name' => 'required',
    //         'category' => 'required',
    //     ]);

    //     $studentName = $request->student_name;
    //     $category = $request->category;
    //     $scoreData = $request->score_data ?? 'Belum ada nilai spesifik';

    //     // 1. Cek API Key
    //     $apiKey = env('GEMINI_API_KEY');

    //     // [MODIFIKASI] FALLBACK JIKA API KEY KOSONG/ERROR
    //     // Agar aplikasi tidak error saat presentasi jika internet mati atau lupa set env
    //     if (!$apiKey) {
    //         // Simulasi loading sedikit agar terasa seperti AI
    //         sleep(1);

    //         // Buat narasi dummy sederhana
    //         $dummyText = "Ananda {$studentName} menunjukkan perkembangan yang baik pada aspek {$category}. ";
    //         $dummyText .= "Berdasarkan data observasi, ananda terlihat antusias mengikuti kegiatan. ";
    //         $dummyText .= "(Catatan: Ini adalah teks otomatis mode offline karena API Key Gemini belum disetting di .env).";

    //         return response()->json(['status' => 'success', 'text' => $dummyText]);
    //     }

    //     // 2. Lanjut ke Logika AI jika API Key ada
    //     $prompt = "Buatkan narasi deskriptif singkat (maksimal 3 paragraf) dan positif untuk raport PAUD. Jangan ada kalimat pembuka seperti 'Tentu ini draftnya'. Langsung ke isi.
    //     Nama Anak: {$studentName}.
    //     Aspek: {$category}.
    //     Data Nilai: " . json_encode($scoreData) . ".
    //     Instruksi: Gunakan bahasa Indonesia yang formal namun hangat. Sebutkan nama anak. Fokus pada kemajuan.";

    //     try {
    //         $model = 'gemini-2.5-flash';

    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //         ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
    //             'contents' => [
    //                 ['parts' => [['text' => $prompt]]]
    //             ]
    //         ]);

    //         if ($response->successful()) {
    //             $result = $response->json();
    //             $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal generate teks.';
    //             $text = str_replace(['**', '*'], '', $text); // Bersihkan format markdown

    //             return response()->json(['status' => 'success', 'text' => $text]);
    //         } else {
    //             // Jika Google menolak (misal kuota habis), kembalikan pesan error yang jelas
    //             return response()->json(['status' => 'error', 'message' => 'Gagal menghubungi Google: ' . $response->body()]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => 'Server Error: ' . $e->getMessage()]);
    //     }
    // }

    /**
     * PRINT PDF
     */
    public function printPdf(Report $report)
    {
        $report->load(['student', 'details.material.subTheme.theme', 'healthDetails']);

        // Grouping detail nilai berdasarkan ID Tema
        $groupedDetails = $report->details->groupBy('theme_id');

        // Ambil Deskripsi Per Tema (theme_notes) untuk ditampilkan di PDF
        // Dicari berdasarkan report_id dan dikelompokkan berdasarkan theme_id
        $themeNotes = DB::table('report_theme_notes')
            ->where('report_id', $report->id)
            ->pluck('note', 'theme_id')
            ->toArray();

        // Mengirimkan data report, detail nilai, dan themeNotes ke view PDF
        $pdf = Pdf::loadView('admin.reports.reports-pdf.index', compact('report', 'groupedDetails', 'themeNotes'));
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
