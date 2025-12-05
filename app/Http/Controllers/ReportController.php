<?php

namespace App\Http\Controllers;

use App\Models\ActivityTransaction;
use App\Models\Attendance;
use App\Models\DailyReportChildrenDetail;
use App\Models\Report;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan package barryvdh/laravel-dompdf sudah diinstal

class ReportController extends Controller
{
    /**
     * Menampilkan halaman utama fitur rapor.
     * Berisi daftar semua transaksi aktivitas siswa yang aktif.
     */
    public function index(Request $request) // Tambahkan Request $request
    {
        // Ambil keyword pencarian dari request
        $search = $request->input('search');

        // Mulai query builder
        $query = ActivityTransaction::query()
            ->where('student_status', true)
            ->with('student', 'program');

        // Jika ada keyword pencarian, tambahkan kondisi where
        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        // Eksekusi query dengan paginasi
        $active_transactions = $query->latest('start_date')->paginate(15);

        // Kirim data ke view
        return view('admin.reports.reports-index.index', compact('active_transactions'));
    }

    /**
     * Menampilkan riwayat rapor untuk satu transaksi aktivitas.
     */
    public function history(ActivityTransaction $activity_transaction)
    {
        $reports = Report::where('activity_transaction_id', $activity_transaction->id)
            ->latest()
            ->paginate(10);

        return view('admin.reports.reports-history.index', compact('activity_transaction', 'reports'));
    }

    /**
     * Menampilkan halaman untuk memilih periode rapor.
     */
    public function selectPeriod(ActivityTransaction $activity_transaction)
    {
        return view('admin.reports.reports-selec-period.index', compact('activity_transaction'));
    }

    /**
     * Menampilkan form pembuatan rapor dengan data yang sudah diisi otomatis.
     */
    public function create(Request $request, ActivityTransaction $activity_transaction)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('reports.selectPeriod', $activity_transaction)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $autoFilledData = $this->generateReportData($activity_transaction, $validated['start_date'], $validated['end_date']);

        if ($request->wantsJson()) {
            return response()->json($autoFilledData);
        }

        return view('admin.reports.reports-create.index', [
            'data' => $autoFilledData,
            'activityTransaction' => $activity_transaction,
            'period' => $validated,
        ]);
    }

    /**
     * Menyimpan rapor baru ke database.
     */
    public function store(Request $request, ActivityTransaction $activity_transaction)
    {
        // dd($request->all());
        // Validasi data yang masuk, termasuk skor untuk tema, subtema, dan materi
        $validated = $request->validate([
            'report_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'overall_summary' => 'nullable|string',
            'recommendations' => 'nullable|string',

            // Validasi untuk semua skor (akan jadi array numerik)
            'scores' => 'nullable|array',
            'scores.*.score' => 'required|in:BB,MB,BSH,BSB',
            'scores.*.type' => 'required|in:theme,subtheme,material',
            'scores.*.id' => 'required|integer',
            'scores.*.sub_theme_id' => 'nullable|integer',

            // Validasi untuk catatan tema (akan jadi array numerik)
            'theme_notes' => 'nullable|array',
            'theme_notes.*.theme_id' => 'required|exists:themes,id',
            'theme_notes.*.note' => 'nullable|string',

            'health_details' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($validated, $activity_transaction) {
                // 1. Buat Rapor Utama
                $report = Report::create([
                    'activity_transaction_id' => $activity_transaction->id,
                    'report_title' => $validated['report_title'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'overall_summary' => $validated['overall_summary'],
                    'recommendations' => $validated['recommendations'],
                    'created_by' => auth()->id(),
                ]);

                // 2. Simpan semua jenis penilaian
                foreach (($validated['scores'] ?? []) as $scoreData) {
                    if ($scoreData['type'] === 'theme') {
                        $report->details()->create(['theme_id' => $scoreData['id'], 'score' => $scoreData['score']]);
                    } elseif ($scoreData['type'] === 'subtheme') {
                        $report->details()->create(['sub_theme_id' => $scoreData['id'], 'score' => $scoreData['score']]);
                    } elseif ($scoreData['type'] === 'material') {
                        $report->details()->create([
                            'material_id' => $scoreData['id'],
                            'sub_theme_id' => $scoreData['sub_theme_id'],
                            'score' => $scoreData['score']
                        ]);
                    }
                }

                // 3. Simpan Catatan per Tema
                foreach (($validated['theme_notes'] ?? []) as $noteData) {
                    $report->themeNotes()->create([
                        'theme_id' => $noteData['theme_id'],
                        'note' => $noteData['note']
                    ]);
                }

                // 4. Simpan Detail Kesehatan
                foreach (($validated['health_details'] ?? []) as $healthData) {
                    $report->healthDetails()->create($healthData);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan rapor: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('reports.history', $activity_transaction)
            ->with('success', 'Rapor berhasil disimpan!');
    }

    /**
     * Menampilkan detail satu rapor yang sudah jadi.
     */
    public function show(Request $request, Report $report)
    {
        $report->load([
            'activityTransaction.student',
            'activityTransaction.program',
            'details.subTheme.theme',
            'details.material',
            'themeNotes.theme',
            'healthDetails'
        ]);

        if ($request->wantsJson()) {
            return response()->json($report);
        }

        return view('admin.reports.reports-show.index', compact('report'));
    }

    /**
     * Menampilkan form untuk mengedit rapor.
     */
    public function edit(Report $report)
    {
        $report->load(['activityTransaction.student', 'details', 'themeNotes', 'healthDetails']);
        $themes = Theme::with('subTheme.material')->orderBy('id')->get();

        return view('admin.reports.reports-edit.index', compact('report', 'themes'));
    }

    /**
     * Mengupdate rapor yang ada di database.
     */
    public function update(Request $request, Report $report)
    {
        // 1. Lakukan validasi yang sama persis seperti di 'store'
        $validated = $request->validate([
            'report_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'overall_summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'scores' => 'nullable|array',
            'scores.*.score' => 'required|in:BB,MB,BSH,BSB',
            'scores.*.type' => 'required|in:theme,subtheme,material',
            'scores.*.id' => 'required|integer',
            'scores.*.sub_theme_id' => 'nullable|integer',
            'theme_notes' => 'nullable|array',
            'theme_notes.*.theme_id' => 'required|exists:themes,id',
            'theme_notes.*.note' => 'nullable|string',
            'health_details' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($validated, $report) {
                // 2. Update data utama di tabel 'reports'
                $report->update([
                    'report_title' => $validated['report_title'],
                    // start_date dan end_date tidak diupdate karena periode rapor tetap
                    'overall_summary' => $validated['overall_summary'],
                    'recommendations' => $validated['recommendations'],
                ]);

                // 3. Hapus semua detail lama untuk diganti dengan yang baru
                $report->details()->delete();
                $report->themeNotes()->delete();
                $report->healthDetails()->delete();

                // 4. ✅ BUAT KEMBALI SEMUA DETAIL BARU (Bagian yang hilang sebelumnya)
                // Logika ini disalin dari fungsi store()
                foreach (($validated['scores'] ?? []) as $scoreData) {
                    if ($scoreData['type'] === 'theme') {
                        $report->details()->create(['theme_id' => $scoreData['id'], 'score' => $scoreData['score']]);
                    } elseif ($scoreData['type'] === 'subtheme') {
                        $report->details()->create(['sub_theme_id' => $scoreData['id'], 'score' => $scoreData['score']]);
                    } elseif ($scoreData['type'] === 'material') {
                        $report->details()->create([
                            'material_id' => $scoreData['id'],
                            'sub_theme_id' => $scoreData['sub_theme_id'],
                            'score' => $scoreData['score']
                        ]);
                    }
                }

                foreach (($validated['theme_notes'] ?? []) as $noteData) {
                    $report->themeNotes()->create([
                        'theme_id' => $noteData['theme_id'],
                        'note' => $noteData['note']
                    ]);
                }

                foreach (($validated['health_details'] ?? []) as $healthData) {
                    $report->healthDetails()->create($healthData);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate rapor: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'Rapor berhasil diupdate!');
    }


    /**
     * Menghapus rapor dari database.
     */
    public function destroy(Report $report)
    {
        $activityTransaction = $report->activityTransaction;
        try {
            $report->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus rapor: ' . $e->getMessage());
        }

        return redirect()->route('reports.history', $activityTransaction)
            ->with('success', 'Rapor berhasil dihapus!');
    }

    /**
     * Membuat dan mengunduh rapor dalam format PDF.
     */
    public function downloadPdf(Report $report)
    {
        // Load semua relasi yang dibutuhkan oleh view PDF
        $report->load([
            'activityTransaction.student',
            'activityTransaction.program',
            'details.theme',
            'details.subTheme',
            'details.material',
            'themeNotes.theme',
            'healthDetails',
            'creator'
        ]);

        $pdf = Pdf::loadView('admin.reports.reports-pdf.index', ['report' => $report]);

        // Menggunakan stream() untuk menampilkan PDF di browser
        // Nama file akan digunakan jika pengguna memilih "Save As..."
        $fileName = 'Rapor-' . Str::slug($report->activityTransaction->student->student_name) . '-' . $report->end_date . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Helper method untuk mengkalkulasi dan menyusun data rapor otomatis.
     */
    private function generateReportData(ActivityTransaction $transaction, $startDate, $endDate)
    {
        $studentId = $transaction->student_id;
        $themes = Theme::with('subTheme.material')->orderBy('id')->get();

        $dailyData = DailyReportChildrenDetail::whereHas('dailyReport', function ($query) use ($transaction, $startDate, $endDate) {
            $query->where('activity_transaction_id', $transaction->id)
                ->whereBetween('period', [$startDate, $endDate]);
        })->get();

        $assessmentResults = [];
        foreach ($themes as $theme) {
            foreach ($theme->subTheme as $subTheme) {
                if ($subTheme->material->isEmpty()) {
                    // Penilaian di level sub-tema (jika ada) bisa ditambahkan di sini
                } else {
                    foreach ($subTheme->material as $material) {
                        $scores = $dailyData->where('session1_material_id', $material->id)->pluck('session1_activity')
                            ->merge($dailyData->where('session2_material_id', $material->id)->pluck('session2_activity'))
                            ->filter();

                        $mode = $scores->count() ? $scores->mode()[0] : null;

                        $assessmentResults[] = [
                            'theme_id' => $theme->id,
                            'sub_theme_id' => $subTheme->id,
                            'material_id' => $material->id,
                            'score' => $mode ?? 'MB' // Default ke 'Mulai Berkembang'
                        ];
                    }
                }
            }
        }

        // ✅ QUERY ABSENSI YANG DIPERBAIKI DAN LEBIH SEDERHANA
        $attendanceCounts = Attendance::where('activity_transaction_id', $transaction->id) // Langsung filter ke transaksi aktivitas
            ->whereHas('attendanceTransaction', function ($query) use ($startDate, $endDate) {
                // Filter tanggal di tabel relasi
                $query->whereBetween('date_attendance', [$startDate, $endDate]);
            })
            ->selectRaw('check_in_status, count(*) as total') // selectRaw lebih aman
            ->groupBy('check_in_status')
            ->pluck('total', 'check_in_status');

        $attendanceSummary = [
            'present' =>  $attendanceCounts->get('Present', 0),
            'sick' => $attendanceCounts->get('Sick', 0),
            'excused' => $attendanceCounts->get('Excused', 0),
            'absent' => $attendanceCounts->get('Absent', 0),
        ];

        return [
            'themes' => $themes,
            'assessments' => $assessmentResults,
            'attendance' => $attendanceSummary,
            'student' => $transaction->student,
        ];
    }
}
