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
        // dd($request->all());
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!$startDate || !$endDate) {
            return redirect()->route('reports.selectPeriod', $student->id)
                ->with('error', 'Silakan pilih periode raport terlebih dahulu.');
        }

        $rStart = Carbon::parse($startDate);
        $rEnd = Carbon::parse($endDate);

        // ---------------------------------------------------------
        // 1. AMBIL NILAI HARIAN TERLEBIH DAHULU (REAL DATA - STRICT TAHUN)
        // ---------------------------------------------------------
        // Menggunakan with('dailyReport') sangat PENTING agar relasi tanggal terbaca
        $dailyData = DailyReportChildrenDetail::with(['dailyReport'])
            ->whereHas('dailyReport', function ($q) use ($student, $startDate, $endDate) {
                // Filter tanggal sesuai inputan (Tahun diperhatikan/Strict)
                $q->whereBetween('period', [$startDate, $endDate])
                    ->whereHas('activityTransaction', function ($trx) use ($student) {
                        $trx->where('student_id', $student->id);
                    });
            })->get();

        // ---------------------------------------------------------
        // 2. AMBIL STRUKTUR KURIKULUM & FILTER (IGNORE YEAR)
        // ---------------------------------------------------------
        $allThemes = Theme::with(['subThemes' => function ($q) {
            $q->where('sub_theme_on_report', true)
                ->where('sub_theme_is_active', true)
                ->with(['materials' => function ($qMat) {
                    $qMat->where('material_on_report', true)
                        ->where('material_is_active', true);
                }]);
        }])
            ->where('theme_on_report', true)
            ->where('theme_is_active', true)
            ->get();

        $filteredThemes = $allThemes->map(function ($theme) use ($rStart, $rEnd) {
            // Filter Sub Tema: Cek apakah bulan & tanggalnya beririsan dengan periode raport
            $validSubThemes = $theme->subThemes->filter(function ($subTheme) use ($rStart, $rEnd) {
                $sStart = Carbon::parse($subTheme->sub_theme_start);
                $sEnd   = Carbon::parse($subTheme->sub_theme_end);

                // Buat tanggal "Proyeksi" sub tema di tahun periode raport agar bisa dibandingkan
                // Kita buat 2 kemungkinan proyeksi untuk menangani lintas tahun
                $checkYears = [$rStart->year, $rEnd->year];
                $isOverlap = false;

                foreach (array_unique($checkYears) as $year) {
                    // Set tahun sub tema ke tahun yang sedang dicek
                    $projStart = $sStart->copy()->year($year);
                    $projEnd   = $sEnd->copy()->year($year);

                    // Jika di database sub tema nyebrang tahun (misal Nov - Feb)
                    // Maka end year harus +1 dari start year
                    if ($sEnd->month < $sStart->month) {
                        $projEnd->addYear();
                    }

                    // Cek Irisan (Overlap)
                    // (StartA <= EndB) and (EndA >= StartB)
                    if ($projStart->lte($rEnd) && $projEnd->gte($rStart)) {
                        $isOverlap = true;
                        break;
                    }
                }

                return $isOverlap;
            });

            $theme->setRelation('subThemes', $validSubThemes);
            return $theme;
        })->filter(function ($theme) {
            // Hanya ambil tema yang punya sub tema valid
            return $theme->subThemes->isNotEmpty();
        });

        // Definisi ulang variabel themes untuk View
        $themes = $filteredThemes;

        // ---------------------------------------------------------
        // 3. OLAH NILAI (CALCULATE SCORE)
        // ---------------------------------------------------------
        $calculatedScores = [];

        foreach ($themes as $theme) {
            foreach ($theme->subThemes as $subTheme) {
                foreach ($subTheme->materials as $material) {

                    // Filter data harian khusus material ini
                    // Pastikan session activity tidak kosong/null
                    $scoresForMaterial = $dailyData->filter(function ($detail) use ($material) {
                        $s1 = ($detail->session1_material_id == $material->id && !empty($detail->session1_activity));
                        $s2 = ($detail->session2_material_id == $material->id && !empty($detail->session2_activity));
                        return $s1 || $s2;
                    });

                    if ($scoresForMaterial->isEmpty()) {
                        $calculatedScores[$material->id] = [
                            'score' => null,
                            'dates' => [],
                            'info'  => 'Belum ternilai'
                        ];
                    } else {
                        $scoreList = [];
                        $history   = [];

                        foreach ($scoresForMaterial as $d) {
                            // Ambil tanggal dari relasi dailyReport
                            if ($d->dailyReport) {
                                $dateObj = Carbon::parse($d->dailyReport->period);
                                $formattedDate = $dateObj->format('d/m');

                                if ($d->session1_material_id == $material->id && $d->session1_activity) {
                                    $scoreList[] = $d->session1_activity;
                                    $history[] = "$formattedDate ({$d->session1_activity})";
                                }
                                if ($d->session2_material_id == $material->id && $d->session2_activity) {
                                    $scoreList[] = $d->session2_activity;
                                    $history[] = "$formattedDate ({$d->session2_activity})";
                                }
                            }
                        }

                        // Hitung Modus
                        if (!empty($scoreList)) {
                            $counts = array_count_values($scoreList);
                            arsort($counts);
                            $finalScore = array_key_first($counts);

                            $calculatedScores[$material->id] = [
                                'score' => $finalScore,
                                'dates' => $history,
                                'info'  => 'Ternilai: ' . implode(', ', array_unique($history))
                            ];
                        } else {
                            $calculatedScores[$material->id] = ['score' => null, 'info' => 'Data tidak valid'];
                        }
                    }
                }
            }
        }

        // ---------------------------------------------------------
        // 4. DATA LAINNYA (PRESENSI & FISIK)
        // ---------------------------------------------------------
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

        // Data Fisik
        $userIdToCheck = $student->user_id ?? $student->id;
        $lastMeasurement = Measurement::where('user_id', $userIdToCheck)
            ->whereBetween('date_measurement', [$startDate, $endDate])
            ->orderBy('date_measurement', 'desc')
            ->first();

        if (!$lastMeasurement) {
            $lastMeasurement = Measurement::where('user_id', $userIdToCheck)
                ->orderBy('date_measurement', 'desc')
                ->first();
        }

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
        // dd($request->all());
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
                'head_circumference' => $request->lingkar_kepala,

                // 2. Narasi Per Aspek (CP)
                'religious_values_text' => $request->narasi_agama,
                'identity_text'         => $request->narasi_jatidiri,
                'literacy_steam_text'   => $request->narasi_steam,
                'p5_text'               => $request->narasi_p5,
                'parent_reflection_text' => $request->refleksi_ortu,

                // 3. Info Perkembangan (Kesimpulan Fisik + Lingkar Kepala)
                'development_info_text' => $request->info_perkembangan,

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
            'report_date'  => 'required|date',
            'semester'     => 'nullable|string',
            'class_name'   => 'required|string',
            'attendance_summary' => 'nullable', // JSON string
        ]);

        DB::beginTransaction();
        try {
            // 1. MAPPING DATA UTAMA
            // Kita siapkan array data yang akan diupdate
            $data = [
                'report_title'          => $request->report_title,
                'report_date'           => $request->report_date,
                'semester'              => $request->semester,
                'class_name'            => $request->class_name,
                'attendance_summary'    => $request->attendance_summary,

                // Data Fisik
                'weight'                => $request->berat_badan,
                'height'                => $request->tinggi_badan,
                'head_circumference'    => $request->lingkar_kepala,

                // Narasi text
                'religious_values_text' => $request->narasi_agama,
                'identity_text'         => $request->narasi_jatidiri,
                'literacy_steam_text'   => $request->narasi_steam,
                'p5_text'               => $request->narasi_p5,
                'parent_reflection_text' => $request->refleksi_ortu,
                'development_info_text' => $request->info_perkembangan,
                'teacher_notes'         => $request->teacher_notes,
                'recommendations'       => $request->recommendations,

                // Nama Penanda Tangan (Text)
                'parent_name'           => $request->nama_ortu,
                'teacher_name'          => $request->nama_guru,
                'consultant_name'       => $request->nama_konsultan,
                'principal_name'        => $request->nama_kepsek,
            ];

            // 2. HANDLE FOTO DOKUMENTASI (DELETE OLD FILE & UPLOAD NEW)
            $photoMap = [
                'foto_agama'            => 'religious_values_photo',
                'foto_jatidiri'         => 'identity_photo',
                'foto_steam'            => 'literacy_steam_photo',
                'foto_p5'               => 'p5_photo',
                'parent_reflection_photo' => 'parent_reflection_photo',
                'development_info_photo'  => 'development_info_photo'
            ];

            foreach ($photoMap as $inputName => $dbColumn) {
                // Cek apakah user mengupload file baru untuk field ini
                if ($request->hasFile($inputName)) {

                    // [LOGIKA HAPUS FILE LAMA]
                    // Jika di database sudah ada path file lama, dan filenya fisik ada di storage
                    if ($report->$dbColumn && Storage::disk('public')->exists($report->$dbColumn)) {
                        Storage::disk('public')->delete($report->$dbColumn);
                    }

                    // Upload file baru
                    $data[$dbColumn] = $request->file($inputName)->store('reports/' . $report->student_id . '/photos', 'public');
                }
            }

            // 3. HANDLE TANDA TANGAN (BASE64) (DELETE OLD FILE & SAVE NEW)
            $sigMap = [
                'ttd_ortu'      => 'parent_signature',
                'ttd_guru'      => 'teacher_signature',
                'ttd_konsultan' => 'consultant_signature',
                'ttd_kepsek'    => 'principal_signature'
            ];

            foreach ($sigMap as $inputName => $dbColumn) {
                // Cek apakah ada input hidden ttd dan isinya cukup panjang (indikasi base64 image valid)
                if ($request->filled($inputName) && strlen($request->input($inputName)) > 100) {

                    // [LOGIKA HAPUS FILE LAMA]
                    if ($report->$dbColumn && Storage::disk('public')->exists($report->$dbColumn)) {
                        Storage::disk('public')->delete($report->$dbColumn);
                    }

                    // Simpan file baru dari Base64
                    $data[$dbColumn] = $this->saveBase64Image($request->input($inputName), $report->student_id, 'sig_' . $inputName);
                }
            }

            // 4. UPDATE RECORD UTAMA
            $report->update($data);

            // 5. UPDATE NILAI CHECKLIST
            // Strategi: Hapus semua nilai lama untuk report ini, lalu insert yang baru (Clean Slate)
            // Ini lebih aman untuk memastikan uncheck radio button terhandle (walaupun radio button sulit di uncheck, tapi jika ganti materi)
            $report->details()->delete();

            if ($request->has('scores')) {
                foreach ($request->scores as $materialId => $scoreValue) {
                    if ($scoreValue) {
                        $material = Material::find($materialId);
                        ReportDetail::create([
                            'report_id'    => $report->id,
                            'material_id'  => $materialId,
                            'theme_id'     => $material->subTheme->theme_id ?? null,
                            'sub_theme_id' => $material->subTheme->id ?? null,
                            'score'        => $scoreValue
                        ]);
                    }
                }
            }

            // 6. UPDATE NARASI TEMA
            // Strategi: Hapus lama, insert baru
            DB::table('report_theme_notes')->where('report_id', $report->id)->delete();

            if ($request->has('theme_notes')) {
                foreach ($request->theme_notes as $themeId => $note) {
                    if (!empty($note)) {
                        DB::table('report_theme_notes')->insert([
                            'report_id' => $report->id,
                            'theme_id'  => $themeId,
                            'note'      => $note
                        ]);
                    }
                }
            }

            // 7. UPDATE KESEHATAN
            // Strategi: Hapus lama, insert baru
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

            // Redirect kembali ke halaman history atau detail dengan pesan sukses
            return redirect()->route('reports.show', $report->id)->with('success', 'Raport berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error untuk developer
            \Illuminate\Support\Facades\Log::error("Update Raport Error: " . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * FORM EDIT (LENGKAP)
     */
    public function edit(Report $report)
    {
        // Load relasi yang dibutuhkan
        $report->load(['student', 'details.material.subTheme', 'healthDetails']);

        $student = $report->student;

        // [PERBAIKAN] Ambil Tanggal Periode dari Data Report yang tersimpan
        $rStart = Carbon::parse($report->start_date);
        $rEnd = Carbon::parse($report->end_date);

        // 1. Ambil Struktur Kurikulum (Tema -> Sub Tema -> Materi)
        $allThemes = Theme::with(['subThemes' => function ($q) {
            $q->where('sub_theme_on_report', true)
                ->where('sub_theme_is_active', true)
                ->with(['materials' => function ($qMat) {
                    $qMat->where('material_on_report', true)
                        ->where('material_is_active', true);
                }]);
        }])
            ->where('theme_on_report', true)
            ->where('theme_is_active', true)
            ->get();

        // [PERBAIKAN] Terapkan Filter yang SAMA dengan method CREATE
        $filteredThemes = $allThemes->map(function ($theme) use ($rStart, $rEnd) {
            // Filter Sub Tema: Cek apakah bulan & tanggalnya beririsan dengan periode raport
            $validSubThemes = $theme->subThemes->filter(function ($subTheme) use ($rStart, $rEnd) {
                $sStart = Carbon::parse($subTheme->sub_theme_start);
                $sEnd   = Carbon::parse($subTheme->sub_theme_end);

                // Buat tanggal "Proyeksi" sub tema di tahun periode raport agar bisa dibandingkan
                $checkYears = [$rStart->year, $rEnd->year];
                $isOverlap = false;

                foreach (array_unique($checkYears) as $year) {
                    $projStart = $sStart->copy()->year($year);
                    $projEnd   = $sEnd->copy()->year($year);

                    if ($sEnd->month < $sStart->month) {
                        $projEnd->addYear();
                    }

                    // Cek Irisan (Overlap)
                    if ($projStart->lte($rEnd) && $projEnd->gte($rStart)) {
                        $isOverlap = true;
                        break;
                    }
                }

                return $isOverlap;
            });

            $theme->setRelation('subThemes', $validSubThemes);
            return $theme;
        })->filter(function ($theme) {
            // Hanya ambil tema yang punya sub tema valid
            return $theme->subThemes->isNotEmpty();
        });

        // Gunakan hasil filter untuk ditampilkan di View
        $themes = $filteredThemes;

        // 2. Mapping Nilai yang Sudah Tersimpan (agar radio button terpilih)
        // Format: [material_id => 'BSB']
        $savedScores = $report->details->pluck('score', 'material_id')->toArray();

        // 3. Mapping Data Kesehatan
        // Format: ['Mata' => 'Baik']
        $healthDetails = $report->healthDetails->pluck('item_value', 'item_name')->toArray();

        // 4. Ambil Catatan Tema (Theme Notes)
        // Format: [theme_id => 'Narasi...']
        $savedThemeNotes = DB::table('report_theme_notes')
            ->where('report_id', $report->id)
            ->pluck('note', 'theme_id')
            ->toArray();

        // 5. Item Kesehatan Default
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
        $prompt = "Buatkan narasi deskriptif singkat (maksimal 3 paragraf) dan positif untuk raport PAUD. Jangan ada kalimat pembuka seperti 'Tentu ini draftnya'. Langsung ke isi. gunakan Ananda sebelum menyebut nama anak.
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
        $text = "Berdasarkan pengukuran fisik terakhir pada tanggal $tanggal, Ananda memiliki berat badan {$data->weight} kg, tinggi badan {$data->height} cm, dan lingkar kepala {$data->head_circumference} cm. gunakan Ananda sebelum menyebut nama anak.";

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
