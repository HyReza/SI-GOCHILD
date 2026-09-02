<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Student;
use App\Models\StudentDevelopmentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StudentReportController extends Controller
{
    /**
     * Helper untuk mendapatkan ID Siswa yang terkait dengan user yang login (Parent/Student)
     */
    private function getCurrentStudentId()
    {
        // ASUMSI: User yang login di 'auth:student' memiliki ID yang sama dengan ID Student
        $user = auth()->user();
        return $user->id;
    }

    /**
     * 1. Menampilkan Riwayat Rapor (Gabungan Kurikulum & Tumbuh Kembang).
     * Route: student.report.history
     */
    public function history(Request $request)
    {
        $studentId = $this->getCurrentStudentId();

        // if (!$studentId) {
        //     abort(403, 'Akses ditolak. Akun Anda tidak terasosiasi dengan data siswa.');
        // }

        $student = Student::findOrFail($studentId);

        // --- A. DATA RAPOR KURIKULUM ---
        $kurikulumQuery = Report::where('student_id', $student->id)->latest();

        if ($request->has('search') && $request->search != '') {
            $kurikulumQuery->where('report_title', 'like', '%' . $request->search . '%');
        }

        // Paginasi Kurikulum (Variable page name: 'report_page')
        $reports = $kurikulumQuery->paginate(10, ['*'], 'report_page');


        // --- B. DATA RAPOR TUMBUH KEMBANG (DDTK) ---
        $devQuery = StudentDevelopmentReport::where('student_id', $student->id)
            ->latest('report_date');

        if ($request->has('search') && $request->search != '') {
            // Pencarian berdasarkan Tahun Ajaran atau Semester untuk tumbuh kembang
            $devQuery->where(function ($q) use ($request) {
                $q->where('academic_year', 'like', '%' . $request->search . '%')
                    ->orWhere('semester', 'like', '%' . $request->search . '%');
            });
        }

        // Paginasi Tumbuh Kembang (Variable page name: 'dev_page')
        $developmentReports = $devQuery->paginate(10, ['*'], 'dev_page');

        // Kembalikan ke view history dengan kedua data
        return view('student.report.report-history.index', compact('student', 'reports', 'developmentReports'));
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN RAPOR KURIKULUM (Laporan Pembelajaran)
    |--------------------------------------------------------------------------
    */

    /**
     * 2. Menampilkan Detail Rapor Kurikulum (Untuk dilihat dan ditanda tangani).
     * Route: student.report.show
     */
    public function show(Report $report)
    {
        $studentId = $this->getCurrentStudentId();

        // Security check: Pastikan rapor ini milik siswa yang sedang login
        // if ($report->student_id !== $studentId) {
        //     abort(403, 'Anda tidak diizinkan melihat laporan ini.');
        // }

        $report->load(['student', 'details.material.subTheme.theme', 'healthDetails']);

        $groupedDetails = $report->details->groupBy(function ($detail) {
            return $detail->material->subTheme->theme->theme_name ?? 'Lainnya';
        });

        $themeNotes = DB::table('report_theme_notes')
            ->where('report_id', $report->id)
            ->pluck('note', 'theme_id')
            ->toArray();

        // Kalkulasi Tahun Ajaran untuk tampilan
        $startDate = \Carbon\Carbon::parse($report->start_date);
        $startYear = $startDate->month >= 7 ? $startDate->year : $startDate->year - 1;
        $endYear = $startYear + 1;
        $academicYear = $startYear . ' / ' . $endYear;

        return view('student.report.report-show.index', compact('report', 'groupedDetails', 'themeNotes', 'academicYear'));
    }

    /**
     * 3. Menyimpan Tanda Tangan Orang Tua / Wali (Untuk Rapor Kurikulum).
     * Route: student.report.sign
     */
    public function signReport(Request $request, Report $report)
    {
        $studentId = $this->getCurrentStudentId();

        // if ($report->student_id !== $studentId) {
        //     return back()->with('error', 'Akses ditolak.');
        // }

        if ($report->parent_signature) {
            return back()->with('error', 'Laporan ini sudah ditandatangani.');
        }

        $request->validate([
            'parent_name' => 'required|string|max:255',
            'ttd_ortu' => 'required|string', // Base64 Tanda Tangan
        ]);

        try {
            $parentName = $request->input('parent_name');
            $signatureBase64 = $request->input('ttd_ortu');

            // Menyimpan Base64 Signature
            $signaturePath = $this->saveBase64Image($signatureBase64, $studentId, 'parent_sig');

            $report->update([
                'parent_name' => $parentName,
                'parent_signature' => $signaturePath,
            ]);

            return redirect()->route('student.report.show', $report->id)
                ->with('success', 'Tanda tangan dan nama Anda berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan tanda tangan: ' . $e->getMessage());
        }
    }

    /**
     * 4. Download PDF Laporan Akhir (Kurikulum).
     * Route: student.report.pdf
     */
    public function downloadPdf(Report $report)
    {
        $studentId = $this->getCurrentStudentId();
        if ($report->student_id !== $studentId) {
            abort(403, 'Akses ditolak.');
        }

        $report->load(['student', 'details.material.subTheme.theme', 'healthDetails']);

        $groupedDetails = $report->details->groupBy(function ($detail) {
            return $detail->material->subTheme->theme->theme_name ?? 'Lainnya';
        });

        $themeNotes = DB::table('report_theme_notes')
            ->where('report_id', $report->id)
            ->pluck('note', 'theme_id')
            ->toArray();

        $startDate = \Carbon\Carbon::parse($report->start_date);
        $startYear = $startDate->month >= 7 ? $startDate->year : $startDate->year - 1;
        $endYear = $startYear + 1;
        $academicYear = $startYear . ' / ' . $endYear;

        $pdf = Pdf::loadView('admin.reports.reports-pdf.index', compact('report', 'groupedDetails', 'themeNotes', 'academicYear'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('Laporan_' . $report->student->student_name . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN RAPOR TUMBUH KEMBANG (DDTK)
    |--------------------------------------------------------------------------
    */

    /**
     * 5. Menampilkan Detail Rapor Tumbuh Kembang & Form TTD
     * Route: student.report.development.show
     */
    public function showDevelopment($id)
    {
        $studentId = $this->getCurrentStudentId();

        // Ambil data report dengan relasinya
        $report = StudentDevelopmentReport::with(['student', 'healthDetail', 'mmdstAssessment'])
            ->findOrFail($id);

        // Security Check: Pastikan milik siswa yang login
        // if ($report->student_id !== $studentId) {
        //     abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        // }

        // Tampilkan View khusus untuk Orang Tua
        return view('student.report.report-development-show.index', compact('report'));
    }

    /**
     * 6. Proses Simpan Tanda Tangan Orang Tua (Rapor Tumbuh Kembang)
     * Route: student.report.development.sign
     */
    public function signDevelopment(Request $request, $id)
    {
        $studentId = $this->getCurrentStudentId();
        $report = StudentDevelopmentReport::findOrFail($id);

        // Security Check
        // if ($report->student_id !== $studentId) {
        //     abort(403, 'Akses ditolak.');
        // }

        if ($report->parent_signature) {
            return back()->with('error', 'Laporan ini sudah ditandatangani.');
        }

        $request->validate([
            'parent_name' => 'required|string|max:255',
            'signature'   => 'required|string', // Base64
        ]);

        try {
            $parentName = $request->input('parent_name');
            $signatureBase64 = $request->input('signature');

            // Simpan gambar tanda tangan
            $signaturePath = $this->saveBase64Image($signatureBase64, $studentId, 'parent_dev_sig');

            $report->update([
                'parent_name' => $parentName,
                'parent_signature' => $signaturePath,
            ]);

            return redirect()->route('student.report.development.show', $report->id)
                ->with('success', 'Terima kasih, tanda tangan berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan tanda tangan: ' . $e->getMessage());
        }
    }

    /**
     * 7. Download PDF Laporan Tumbuh Kembang (DDTK).
     * Route: student.report.development.pdf
     */
    public function downloadDevelopmentPdf($id)
    {
        $studentId = $this->getCurrentStudentId();

        // Cari Report Tumbuh Kembang berdasarkan ID
        $report = StudentDevelopmentReport::with(['student', 'healthDetail', 'mmdstAssessment'])
            ->findOrFail($id);

        // Security Check: Pastikan milik siswa yang login
        // if ($report->student_id !== $studentId) {
        //     abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        // }

        // Generate PDF menggunakan View Admin yang sudah ada (reuse view)
        // Path view: admin.reports-development.report-development-print.index
        $pdf = Pdf::loadView('admin.reports-development.report-development-print.index', compact('report'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Hasil_Pertumbuhan_Perkembangan_' . Str::slug($report->student->student_name) . '_' . $report->semester . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Helper: Simpan Base64 Image (Signature)
     */
    private function saveBase64Image($base64String, $studentId, $prefix)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) throw new \Exception('Tipe gambar tidak valid');
            $image = base64_decode($base64String);
            if ($image === false) throw new \Exception('Gagal decode base64');

            // Simpan di folder signatures spesifik siswa
            // Path: public/storage/reports/{studentId}/signatures/
            $filename = 'reports/' . $studentId . '/signatures/' . $prefix . '_' . time() . '.' . $type;
            Storage::disk('public')->put($filename, $image);
            return $filename;
        }
        return null;
    }
}
