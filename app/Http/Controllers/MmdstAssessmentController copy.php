<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\MmdstAssessment;
use App\Models\MmdstAssessmentItem;
use App\Models\MmdstParameter;
use App\Models\CategoryParameter;
use App\Services\MmdstScoringService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MmdstAssessmentController extends Controller
{
    public function __construct(private MmdstScoringService $scoring) {}

    /**
     * (Opsional) daftar assessment.
     * Flow utama index siswa ada di StudentMmdstController.
     */
    public function index(Request $request)
    {
        $q = MmdstAssessment::query()
            ->with(['student', 'creator'])
            ->latest('assessment_date');

        if ($search = trim((string)$request->get('search', ''))) {
            $q->whereHas('student', function ($s) use ($search) {
                $s->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date')) {
            $q->whereDate('assessment_date', $request->date);
        }

        $assessments = $q->paginate(10)->withQueryString();
        return view('admin.mmdst-assessment.mmdst-assessment-history.index', compact('assessments'));
    }

    /** (Opsional) create manual; umumnya pakai autoCreateReport di StudentMmdstController. */
    public function create()
    {
        $students   = Student::orderBy('student_name')->get(['id', 'student_name', 'student_number', 'birth_date']);
        $categories = CategoryParameter::orderBy('category_parameter_name')->get(['id', 'category_parameter_name']);
        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')->orderBy('id')->get();

        return view('admin.mmdst-assessment.mmdst-assessment-create.index', compact('students', 'categories', 'parameters'));
    }

    /** Simpan assessment baru (manual). */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'      => ['required', 'exists:students,id'],
            'assessment_date' => ['required', 'date'],
            'notes'           => ['nullable', 'string'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.parameter_id' => ['required', 'exists:mmdst_parameters,id'],
            'items.*.result_code'  => ['required', 'in:P,F,R,OP'],
            'items.*.note'         => ['nullable', 'string'],
        ], [
            'items.required' => 'Minimal 1 item dinilai.',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        if (!$student->birth_date) {
            return back()->withErrors(['student_id' => 'Tanggal lahir siswa belum diisi.'])->withInput();
        }

        $ageInDays = $student->getAgeInDaysAt(Carbon::parse($validated['assessment_date']));

        DB::transaction(function () use ($validated, $ageInDays) {
            $assessment = MmdstAssessment::create([
                'student_id'      => $validated['student_id'],
                'assessment_date' => $validated['assessment_date'],
                'age_in_days'     => $ageInDays,
                'notes'           => $validated['notes'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            $paramMap = MmdstParameter::whereIn('id', collect($validated['items'])->pluck('parameter_id'))
                ->get()->keyBy('id');

            foreach ($validated['items'] as $row) {
                $param = $paramMap[$row['parameter_id']];
                $isAgeLine = $ageInDays >= (int) ($param->percent_25 ?? 0);
                $isDelay   = $row['result_code'] === 'F' && $ageInDays >= (int) ($param->percent_100 ?? PHP_INT_MAX);

                MmdstAssessmentItem::create([
                    'assessment_id'           => $assessment->id,
                    'mmdst_parameter_id'      => $param->id,
                    'stimulation_category_id' => $param->stimulation_category_id,
                    'result_code'             => $row['result_code'],
                    'is_delay'                => $isDelay,
                    'is_age_line'             => $isAgeLine,
                    'note'                    => $row['note'] ?? null,
                ]);
            }

            $this->scoring->recalc($assessment);
        });

        return redirect()->route('mmdst-assessments.index')->with('success', 'Penilaian MMDST berhasil disimpan.');
    }

    /** Detail assessment + penanda status untuk UI. */
    public function show(MmdstAssessment $mmdst_assessment)
    {
        $mmdst_assessment->load([
            'student',
            'creator',
            'items.parameter.stimulationCategory',
            'sectorSummaries.category'
        ]);

        $age = $mmdst_assessment->age_in_days;

        $allParams = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')->orderBy('id')
            ->get(['id', 'test_element_name', 'stimulation_category_id', 'percent_25', 'percent_50', 'percent_75', 'percent_100']);

        $testedMap = $mmdst_assessment->items->keyBy('mmdst_parameter_id')->map(function ($it) {
            return [
                'tested'      => true,
                'result_code' => $it->result_code,  // P/F/R/OP
                'passed'      => $it->result_code === 'P',
                'failed'      => $it->result_code === 'F',
                'is_delay'    => (bool) $it->is_delay,
                'is_age_line' => (bool) $it->is_age_line,
            ];
        })->toArray();

        $bucketMap = [];
        foreach ($allParams as $p) {
            $bucketMap[$p->id] = $this->classifyAgeBucket(
                $age,
                (int) ($p->percent_25 ?? 0),
                (int) ($p->percent_50 ?? 0),
                (int) ($p->percent_75 ?? 0),
                (int) ($p->percent_100 ?? 0)
            );
        }

        return view('admin.mmdst-assessment.mmdst-assessment-show.index', [
            'assessment' => $mmdst_assessment,
            'parameters' => $allParams,
            'bucketMap'  => $bucketMap, // param_id => NOT_YET | AT_LINE | IN_WINDOW | OVERDUE
            'testedMap'  => $testedMap, // param_id => { tested, result_code, passed, failed, is_delay, is_age_line }
        ]);
    }

    /** Edit assessment + penanda status untuk pewarnaan UI. */
    public function edit(MmdstAssessment $mmdst_assessment)
    {
        $mmdst_assessment->load(['student', 'items.parameter']);

        $students   = Student::orderBy('student_name')->get(['id', 'student_name', 'student_number', 'birth_date']);
        $categories = CategoryParameter::orderBy('category_parameter_name')->get(['id', 'category_parameter_name']);
        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')->orderBy('id')->get([
                'id',
                'test_element_name',
                'test_element_description',
                'stimulation_category_id',
                'percent_25',
                'percent_50',
                'percent_75',
                'percent_100'
            ]);

        $age = $mmdst_assessment->age_in_days;

        $existing = $mmdst_assessment->items->keyBy('mmdst_parameter_id');

        $bucketMap = [];
        foreach ($parameters as $p) {
            $bucketMap[$p->id] = $this->classifyAgeBucket(
                $age,
                (int) ($p->percent_25 ?? 0),
                (int) ($p->percent_50 ?? 0),
                (int) ($p->percent_75 ?? 0),
                (int) ($p->percent_100 ?? 0)
            );
        }

        $testedMap = $existing->map(function ($it) {
            return [
                'tested'      => true,
                'result_code' => $it->result_code,
                'passed'      => $it->result_code === 'P',
                'failed'      => $it->result_code === 'F',
                'is_delay'    => (bool) $it->is_delay,
                'is_age_line' => (bool) $it->is_age_line,
            ];
        })->toArray();

        return view('admin.mmdst-assessment.mmdst-assessment-edit.index', compact(
            'mmdst_assessment',
            'students',
            'categories',
            'parameters',
            'bucketMap',
            'testedMap',
            'existing'
        ));
    }

    /** Update assessment + hitung ulang ringkasan. */
    public function update(Request $request, MmdstAssessment $mmdst_assessment): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_date' => ['required', 'date'],
            'notes'           => ['nullable', 'string'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.parameter_id' => ['required', 'exists:mmdst_parameters,id'],
            'items.*.result_code'  => ['required', 'in:P,F,R,OP'],
            'items.*.note'         => ['nullable', 'string'],
        ]);

        $student = $mmdst_assessment->student()->firstOrFail();
        if (!$student->birth_date) {
            return back()->withErrors(['student_id' => 'Tanggal lahir siswa belum diisi.'])->withInput();
        }

        $ageInDays = $student->getAgeInDaysAt(Carbon::parse($validated['assessment_date']));

        DB::transaction(function () use ($validated, $ageInDays, $mmdst_assessment) {
            $mmdst_assessment->update([
                'assessment_date' => $validated['assessment_date'],
                'age_in_days'     => $ageInDays,
                'notes'           => $validated['notes'] ?? null,
                'overall_result'  => null,
                'counters'        => null,
            ]);

            // replace items
            $mmdst_assessment->items()->delete();

            $paramMap = MmdstParameter::whereIn('id', collect($validated['items'])->pluck('parameter_id'))
                ->get()->keyBy('id');

            foreach ($validated['items'] as $row) {
                $param = $paramMap[$row['parameter_id']];
                $isAgeLine = $ageInDays >= (int) ($param->percent_25 ?? 0);
                $isDelay   = $row['result_code'] === 'F' && $ageInDays >= (int) ($param->percent_100 ?? PHP_INT_MAX);

                MmdstAssessmentItem::create([
                    'assessment_id'           => $mmdst_assessment->id,
                    'mmdst_parameter_id'      => $param->id,
                    'stimulation_category_id' => $param->stimulation_category_id,
                    'result_code'             => $row['result_code'],
                    'is_delay'                => $isDelay,
                    'is_age_line'             => $isAgeLine,
                    'note'                    => $row['note'] ?? null,
                ]);
            }

            $this->scoring->recalc($mmdst_assessment);
        });

        return redirect()->route('mmdst-assessments.show', $mmdst_assessment)
            ->with('success', 'Penilaian MMDST berhasil diperbarui.');
    }

    /** Hapus assessment. */
    public function destroy(MmdstAssessment $mmdst_assessment): RedirectResponse
    {
        $mmdst_assessment->delete();
        return redirect()->route('mmdst-assessments.index')->with('success', 'Penilaian MMDST dihapus.');
    }

    /**
     * Klasifikasi bucket usia untuk badge/ikon:
     * - OVERDUE   : age >= p100
     * - AT_LINE   : age == salah satu titik p25/p50/p75/p100
     * - IN_WINDOW : p25 <= age < p100 dan bukan AT_LINE
     * - NOT_YET   : age < p25
     */
    private function classifyAgeBucket(int $age, ?int $p25, ?int $p50, ?int $p75, ?int $p100): string
    {
        $p25  = $p25  ?? 0;
        $p50  = $p50  ?? -1;
        $p75  = $p75  ?? -1;
        $p100 = $p100 ?? PHP_INT_MAX;

        if ($age >= $p100) {
            return 'OVERDUE';
        }
        if ($age === $p25 || $age === $p50 || $age === $p75 || $age === $p100) {
            return 'AT_LINE';
        }
        if ($age >= $p25 && $age < $p100) {
            return 'IN_WINDOW';
        }
        return 'NOT_YET';
    }
}
