<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\MmdstParameter;
use App\Models\MmdstAssessment;
use App\Models\MmdstAssessmentItem;
use App\Services\MmdstScoringService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Service;

class StudentMmdstController extends Controller
{
    public function __construct(private MmdstScoringService $scoring) {}

    /** Index: daftar siswa + filter layanan + live search. */
    public function index(Request $request)
    {
        $students = Student::orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'student_number', 'student_name', 'birth_date', 'gender', 'mother_name']);

        $services = Service::orderBy('service_name')->get(['id', 'service_name']);

        // view path kamu: sesuaikan jika berbeda
        return view('admin.mmdst-assessment.mmdst-assessment-index.index', compact('students', 'services'));
    }

    /** AJAX: live search siswa + filter layanan (robust) */
    public function search(Request $request): JsonResponse
    {
        try {
            $q         = trim((string) $request->get('q', ''));
            $serviceId = (int) $request->input('service_id', 0); // pastikan integer

            $query = Student::query();

            if ($q !== '') {
                $query->where(function ($w) use ($q) {
                    $w->where('student_name', 'like', "%{$q}%")
                        ->orWhere('student_number', 'like', "%{$q}%")
                        ->orWhere('mother_name', 'like', "%{$q}%");

                    // hanya tambah filter national_id jika kolomnya ada
                    if (Schema::hasColumn('students', 'national_id')) {
                        $w->orWhere('national_id', 'like', "%{$q}%");
                    }
                });
            }

            // FILTER layanan:
            if ($request->filled('service_id') && $serviceId > 0) {
                // Kalau relasi ada → pakai whereHas
                if (method_exists(Student::class, 'activityTransactions')) {
                    $query->whereHas('activityTransactions', function ($tx) use ($serviceId) {
                        $tx->where('service_id', $serviceId);
                        // ->where('student_status', 1); // kalau mau yang aktif saja
                    });
                } else {
                    // Fallback kalau relasi belum ada → whereExists langsung ke tabel
                    $query->whereExists(function ($sub) use ($serviceId) {
                        $sub->from('activity_transactions as at')
                            ->whereColumn('at.student_id', 'students.id')
                            ->where('at.service_id', $serviceId);
                        // ->where('at.student_status', 1);
                    });
                }
            }

            $students = $query->orderBy('student_name')
                ->limit(50)
                ->get(['id', 'student_number', 'student_name', 'birth_date', 'gender', 'mother_name']);

            return response()->json([
                'data' => $students->map(function ($s) {
                    return [
                        'id'             => $s->id,
                        'student_number' => $s->student_number,
                        'student_name'   => $s->student_name,
                        'mother_name'    => $s->mother_name,
                        'birth_date'     => optional($s->birth_date)->toDateString(), // cast date → string
                        'gender'         => $s->gender,
                    ];
                }),
            ]);
        } catch (\Throwable $e) {
            Log::error('MMDST search error', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(), // tampilkan untuk memudahkan debug
            ], 500);
        }
    }

    /** Buat laporan otomatis lalu arahkan ke edit. */
    public function autoCreateReport(Student $student, Request $request): RedirectResponse|JsonResponse
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        if (!$student->birth_date) {
            $msg = 'Tanggal lahir siswa belum diisi.';
            if ($request->expectsJson()) return response()->json(['ok' => false, 'message' => $msg], 422);
            return back()->withErrors(['student_id' => $msg]);
        }

        $ageInDays = $student->getAgeInDaysAt($date);
        $scope = $request->get('scope', 'age_line_only');

        DB::beginTransaction();
        try {
            $assessment = MmdstAssessment::create([
                'student_id'      => $student->id,
                'assessment_date' => $date->toDateString(),
                'age_in_days'     => $ageInDays,
                'notes'           => null,
                'created_by'      => Auth::id(),
            ]);

            $paramsQuery = MmdstParameter::query()->where('parameter_is_active', 1);

            if ($scope !== 'all') {
                $paramsQuery->where(function ($w) use ($ageInDays) {
                    $w->whereNull('percent_25')
                        ->orWhere('percent_25', '<=', $ageInDays);
                });
            }

            $params = $paramsQuery->orderBy('stimulation_category_id')->orderBy('id')
                ->get(['id', 'stimulation_category_id', 'percent_25', 'percent_100']);

            foreach ($params as $p) {
                $isAgeLine = $ageInDays >= (int) ($p->percent_25 ?? 0);
                MmdstAssessmentItem::create([
                    'assessment_id'           => $assessment->id,
                    'mmdst_parameter_id'      => $p->id,
                    'stimulation_category_id' => $p->stimulation_category_id,
                    'result_code'             => 'OP',
                    'is_delay'                => false,
                    'is_age_line'             => $isAgeLine,
                    'note'                    => null,
                ]);
            }

            $this->scoring->recalc($assessment);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'ok'       => true,
                    'id'       => $assessment->id,
                    'edit_url' => route('mmdst-assessments.edit', $assessment),
                    'show_url' => route('mmdst-assessments.show', $assessment),
                    'message'  => 'Laporan MMDST dibuat. Silakan lengkapi hasil.',
                ]);
            }

            return redirect()->route('mmdst-assessments.edit', $assessment)
                ->with('success', 'Laporan MMDST dibuat. Silakan lengkapi hasil.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Gagal membuat laporan: ' . $e->getMessage()]);
        }
    }

    /** Riwayat siswa. */
    public function history(Student $student)
    {
        return view('admin.mmdst-assessment.mmdst-assessment-history.index', compact('student'));
    }

    /** AJAX data riwayat. */
    public function historyData(Student $student, Request $request): JsonResponse
    {
        $q    = trim((string) $request->get('q', ''));
        $from = $request->get('from');
        $to   = $request->get('to');

        $query = MmdstAssessment::with(['sectorSummaries', 'creator'])
            ->where('student_id', $student->id)
            ->orderBy('assessment_date', 'desc');

        if ($from) $query->whereDate('assessment_date', '>=', $from);
        if ($to)   $query->whereDate('assessment_date', '<=', $to);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('overall_result', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%");
            });
        }

        $rows = $query->limit(100)->get();

        return response()->json([
            'data' => $rows->map(fn($a) => [
                'id'             => $a->id,
                'date'           => $a->assessment_date->toDateString(),
                'age_in_days'    => $a->age_in_days,
                'overall_result' => $a->overall_result,
                'created_by'     => optional($a->creator)->user_name,
                'show_url'       => route('mmdst-assessments.show', $a),
                'edit_url'       => route('mmdst-assessments.edit', $a),
            ]),
        ]);
    }

    /** Status parameter relatif usia / assessment. */
    public function parameterStatus(Request $request): JsonResponse
    {
        $assessmentId = $request->get('assessment_id');
        $studentId    = $request->get('student_id');
        $dateStr      = $request->get('date');

        $ageInDays = null;
        $itemsByParam = collect();

        if ($assessmentId) {
            $assessment = MmdstAssessment::with('items')->findOrFail($assessmentId);
            $ageInDays  = $assessment->age_in_days;
            $itemsByParam = $assessment->items->keyBy('mmdst_parameter_id');
        } else {
            if (!$studentId || !$dateStr) {
                return response()->json(['error' => 'Butuh student_id dan date atau assessment_id.'], 422);
            }
            $student = Student::findOrFail($studentId);
            if (!$student->birth_date) {
                return response()->json(['error' => 'Tanggal lahir siswa belum diisi.'], 422);
            }
            $ageInDays = $student->getAgeInDaysAt(Carbon::parse($dateStr));
        }

        $params = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')
            ->orderBy('id')
            ->get(['id', 'test_element_name', 'stimulation_category_id', 'percent_25', 'percent_50', 'percent_75', 'percent_100']);

        $data = $params->map(function ($p) use ($ageInDays, $itemsByParam) {
            $bucket = $this->classifyAgeBucket(
                $ageInDays,
                (int) ($p->percent_25 ?? 0),
                (int) ($p->percent_50 ?? 0),
                (int) ($p->percent_75 ?? 0),
                (int) ($p->percent_100 ?? 0)
            );

            $tested = false;
            $result = null;
            $passed = false;
            $failed = false;
            if ($itemsByParam->has($p->id)) {
                $tested = true;
                $result = $itemsByParam[$p->id]->result_code;
                $passed = $result === 'P';
                $failed = $result === 'F';
            }

            return [
                'parameter_id' => $p->id,
                'category_id'  => $p->stimulation_category_id,
                'name'         => $p->test_element_name,
                'thresholds'   => [
                    'p25'  => $p->percent_25,
                    'p50'  => $p->percent_50,
                    'p75'  => $p->percent_75,
                    'p100' => $p->percent_100,
                ],
                'age_bucket'  => $bucket,
                'tested'      => $tested,
                'result_code' => $result,
                'passed'      => $passed,
                'failed'      => $failed,
            ];
        });

        return response()->json(['data' => $data]);
    }

    private function classifyAgeBucket(int $age, ?int $p25, ?int $p50, ?int $p75, ?int $p100): string
    {
        $p25  = (int) ($p25  ?? PHP_INT_MAX);
        $p50  = (int) ($p50  ?? -1);
        $p75  = (int) ($p75  ?? -1);
        $p100 = (int) ($p100 ?? PHP_INT_MAX);

        if ($age >= $p100) return 'OVERDUE';
        if ($age === $p25 || $age === $p50 || $age === $p75 || $age === $p100) return 'AT_LINE';
        if ($age >= $p25 && $age < $p100) return 'IN_WINDOW';
        return 'NOT_YET';
    }
}
