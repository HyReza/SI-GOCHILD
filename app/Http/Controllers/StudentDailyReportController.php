<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StudentDailyReportController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();

        $query = DailyReport::whereHas('activityTransaction', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        });

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('period', $request->date);
        }

        $reports = $query->with(['service'])
            ->orderBy('period', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('student.daily-report.daily-report-index.index', compact('reports'));
    }

    public function show($id)
    {
        $dailyReport = $this->getDailyReportIfOwner($id);

        if (!$dailyReport) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $dailyReport->load([
            'activityTransaction.service',
            'babyDetail',
            'childrenDetail.session1Material',
            'childrenDetail.session2Material'
        ]);

        return view('student.daily-report.daily-report-show.index', compact('dailyReport'));
    }

    /**
     * UPDATE: Menyimpan Tanda Tangan DAN Nama Orang Tua
     */
    public function sign(Request $request, $id)
    {
        $dailyReport = $this->getDailyReportIfOwner($id);

        if (!$dailyReport) {
            abort(403, 'Akses ditolak.');
        }

        // 1. Validasi input (tambah parent_name)
        $request->validate([
            'signature' => 'required|string',
            'parent_name' => 'required|string|max:255', // <--- BARU
        ]);

        try {
            $base64_image = $request->input('signature');

            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
                $base64_image = substr($base64_image, strpos($base64_image, ',') + 1);
                $type = strtolower($type[1]);

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('Tipe file gambar tidak valid.');
                }

                $image = base64_decode($base64_image);

                if ($image === false) {
                    throw new \Exception('Gagal mendecode gambar.');
                }
            } else {
                throw new \Exception('Format data signature tidak valid.');
            }

            $fileName = 'signatures/parent_' . $dailyReport->id . '_' . time() . '.' . $type;

            Storage::disk('public')->put($fileName, $image);

            // 2. Simpan ke Database (tambah parent_guardian_name)
            $dailyReport->update([
                'parent_guardian_signature' => $fileName,
                'parent_guardian_name' => $request->input('parent_name') // <--- BARU
            ]);

            return redirect()->back()->with('success', 'Tanda tangan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Signature Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan tanda tangan. Silakan coba lagi.');
        }
    }

    public function downloadPdf($id)
    {
        $dailyReport = $this->getDailyReportIfOwner($id);

        if (!$dailyReport) {
            abort(403, 'Akses ditolak.');
        }

        $dailyReport->load([
            'activityTransaction.student',
            'activityTransaction.service',
            'babyDetail',
            'childrenDetail.session1Material',
            'childrenDetail.session2Material',
        ]);

        $pdf = Pdf::loadView('student.daily-report.daily-report-print.index', [
            'dailyReport' => $dailyReport,
            'is_student_view' => true
        ]);

        $pdf->setPaper('A4', 'portrait');

        $fileName = 'Laporan_Harian_' . $dailyReport->activityTransaction->student->student_name . '_' . $dailyReport->period . '.pdf';

        return $pdf->download($fileName);
    }

    private function getDailyReportIfOwner($dailyReportId)
    {
        $student = Auth::guard('student')->user();

        $report = DailyReport::with('activityTransaction')
            ->where('id', $dailyReportId)
            ->first();

        if ($report && $report->activityTransaction->student_id == $student->id) {
            return $report;
        }

        return null;
    }
}
