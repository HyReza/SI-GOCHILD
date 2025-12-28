<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Log;

class MmdstAssessmentController extends Controller
{
    public function __construct(private MmdstScoringService $scoring) {}

    /** (Opsional) daftar assessment */
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

    /** Start report dari index: kembalikan URL ke halaman CREATE (bukan edit) */
    public function startReport(Student $student)
    {
        // biar JS bisa redirect ke create + preselect student + tanggal hari ini
        $createUrl = route('mmdst-assessments.create', [
            'student_id'      => $student->id,
            'assessment_date' => now()->toDateString(),
        ]);

        return response()->json([
            'ok'        => true,
            'create_url' => $createUrl,
        ]);
    }

    /** Halaman create */
    public function create(Request $request)
    {
        // Data siswa dropdown
        $students = Student::orderBy('student_name')->get(['id', 'student_name', 'student_number', 'birth_date']);

        // Siswa terpilih (opsional via query)
        $selectedStudent = null;
        $studentId = $request->integer('student_id') ?: optional($students->first())->id;
        if ($studentId) {
            $selectedStudent = Student::find($studentId);
        }

        // Tanggal penilaian (default: hari ini)
        $assessmentDate = $request->date('assessment_date') ?: now()->toDateString();

        // Ambil normal status dari activity transaksi terakhir (default true)
        $studentIsNormal = true;
        if ($selectedStudent) {
            $activityTransaction = $selectedStudent->activityTransaction()->latest()->first();
            $studentIsNormal     = $activityTransaction ? (bool)$activityTransaction->student_is_normal : true;
        }

        // Ambil parameter aktif + relasi kategori
        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')
            ->orderBy('id')
            ->get();

        // Hitung umur berdasar tanggal penilaian
        $ageInDays = $selectedStudent
            ? $this->getAgeInDaysFor($selectedStudent, Carbon::parse($assessmentDate))
            : 0;

        // Filter/gabung per kategori (NORMAL pakai filter, NON-NORMAL tampil semua)
        $filtered_categories = $this->filterAndGroupParameters($parameters, $ageInDays, $studentIsNormal);

        // Prefill opsi dari assessment terakhir siswa (jika ada)
        $previousMap = collect();
        if ($selectedStudent) {
            $last = MmdstAssessment::with('items')
                ->where('student_id', $selectedStudent->id)
                ->latest('assessment_date')
                ->first();
            if ($last) {
                $previousMap = $last->items->mapWithKeys(fn($it) => [
                    $it->mmdst_parameter_id => [
                        'result_code' => $it->result_code,
                        'note'        => (string) $it->note,
                        'date'        => optional($last->assessment_date)->toDateString(),
                    ]
                ]);
            }
        }

        // kirim ke view
        return view('admin.mmdst-assessment.mmdst-assessment-create.index', [
            'students'            => $students,
            'parameters'          => $parameters, // kalau mau referensi mentah
            'filtered_categories' => $filtered_categories, // yang dipakai tabel
            'studentIsNormal'     => $studentIsNormal,
            'selectedStudent'     => $selectedStudent,
            'assessmentDate'      => $assessmentDate,
            'ageInDays'           => $ageInDays,
            'previousMap'         => $previousMap, // untuk prefill radio/notes
        ]);
    }

    /** Simpan assessment baru */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'      => ['required', 'exists:students,id'],
            'assessment_date' => ['required', 'date'],
            'notes'           => ['nullable', 'string'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.parameter_id' => ['required', 'exists:mmdst_parameters,id'],
            // HAPUS NR: Validasi hanya P, F, R, OP
            'items.*.result_code'  => ['required', 'in:P,F,R,OP'],
            'items.*.note'         => ['nullable', 'string'],
        ]);

        $student = Student::findOrFail($validated['student_id']);
        if (!$student->birth_date) {
            return back()->withErrors(['student_id' => 'Tanggal lahir siswa belum diisi.'])->withInput();
        }

        $ageInDays = $this->getAgeInDaysFor($student, Carbon::parse($validated['assessment_date']));

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
                $param = $paramMap[$row['parameter_id']] ?? null;
                if (!$param) continue;

                // Logika Garis Usia: P25 <= Usia <= P100
                $p25  = (int) ($param->percent_25 ?? 0);
                $p100 = (int) ($param->percent_100 ?? PHP_INT_MAX);
                $isAgeLine = $ageInDays >= $p25 && $ageInDays <= $p100;

                // Logika DELAY (GAGAL) KRITIS: Result 'F' && Usia >= P75
                // Sesuai Dokumen MMDST Revisi: "Gagal (F) pada usia > 75"
                $p75 = (int) ($param->percent_75 ?? 0);
                $isDelay = ($row['result_code'] === 'F') && ($ageInDays >= $p75);

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

            // hitung ringkasan via service
            $this->scoring->recalc($assessment);
        });

        return redirect()
            ->route('mmdst.history', $student)
            ->with('success', 'Penilaian MMDST berhasil disimpan.');
    }


    /** Detail assessment */
    public function show(MmdstAssessment $mmdst_assessment)
    {
        $mmdst_assessment->load([
            'student',
            'creator',
            'items.parameter.stimulationCategory',
            'sectorSummaries.category',
        ]);

        $age = (int) $mmdst_assessment->age_in_days;

        $allParams = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')->orderBy('id')
            ->get([
                'id',
                'test_element_name',
                'test_element_description',
                'stimulation_category_id',
                'percent_25',
                'percent_50',
                'percent_75',
                'percent_100',
            ]);

        $testedMap = $mmdst_assessment->items
            ->keyBy('mmdst_parameter_id')
            ->map(function ($it) {
                return [
                    'tested'      => true,
                    'result_code' => $it->result_code,
                    'passed'      => $it->result_code === 'P',
                    'failed'      => $it->result_code === 'F',
                    'is_delay'    => (bool) $it->is_delay,
                    'is_age_line' => (bool) $it->is_age_line,
                ];
            })
            ->toArray();

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
            'bucketMap'  => $bucketMap,
            'testedMap'  => $testedMap,
        ]);
    }

    /** Edit assessment */
    public function edit(MmdstAssessment $mmdst_assessment)
    {
        $mmdst_assessment->load(['student', 'creator', 'items']);

        $student  = $mmdst_assessment->student;

        // Ambil normal status dari transaksi terakhir (default true)
        $activityTransaction = $student->activityTransaction()->latest()->first();
        $studentIsNormal     = $activityTransaction ? (bool)$activityTransaction->student_is_normal : true;

        $assessmentDate = $mmdst_assessment->assessment_date ?? now();
        $ageInDays = (int) ($mmdst_assessment->age_in_days
            ?? $this->getAgeInDaysFor($student, $assessmentDate));

        // Ambil parameter aktif
        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')
            ->orderBy('id')
            ->get();

        // Filter/gabung per kategori
        $filtered_categories = $this->filterAndGroupParameters($parameters, $ageInDays, $studentIsNormal);

        // Existing items (untuk pre-check)
        $existing = $mmdst_assessment->items->keyBy('mmdst_parameter_id');

        return view('admin.mmdst-assessment.mmdst-assessment-edit.index', compact(
            'mmdst_assessment',
            'studentIsNormal',
            'ageInDays',
            'filtered_categories',
            'existing'
        ));
    }

    /** Update assessment */
    public function update(Request $request, MmdstAssessment $mmdst_assessment): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_date' => ['required', 'date'],
            'notes'           => ['nullable', 'string'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.parameter_id' => ['required', 'exists:mmdst_parameters,id'],
            // HAPUS NR: Validasi hanya P, F, R, OP
            'items.*.result_code'  => ['required', 'in:P,F,R,OP'],
            'items.*.note'         => ['nullable', 'string'],
        ]);

        $student = $mmdst_assessment->student()->firstOrFail();
        if (!$student->birth_date) {
            return back()->withErrors(['student_id' => 'Tanggal lahir siswa belum diisi.'])->withInput();
        }

        $ageInDays = $this->getAgeInDaysFor($student, Carbon::parse($validated['assessment_date']));

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

                // Logika Garis Usia: P25 <= Usia <= P100
                $p25  = (int) ($param->percent_25 ?? 0);
                $p100 = (int) ($param->percent_100 ?? PHP_INT_MAX);
                $isAgeLine = $ageInDays >= $p25 && $ageInDays <= $p100;

                // Logika DELAY (GAGAL) KRITIS: Result 'F' && Usia >= P75
                $p75 = (int) ($param->percent_75 ?? 0);
                $isDelay = ($row['result_code'] === 'F') && ($ageInDays >= $p75);

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

    /** Hapus assessment */
    public function destroy(MmdstAssessment $mmdst_assessment): RedirectResponse
    {
        $student = $mmdst_assessment->student; // bisa null
        $mmdst_assessment->delete();

        if ($student) {
            return redirect()
                ->route('mmdst.history', $student)
                ->with('success', 'Penilaian MMDST dihapus.');
        }

        return redirect()
            ->route('mmdst-assessments.index')
            ->with('success', 'Penilaian MMDST dihapus.');
    }

    /**
     * AJAX: Filter parameter berdasarkan student_id & assessment_date.
     * - Jika student_is_normal = true → pakai in-range + 2 bawah + 2 atas
     * - Jika false → tampil semua
     * Response: { ok, age_in_days, student_is_normal, data: { "Kategori": [ {id, name, desc, p25..p100} ] } }
     */
    public function filterParams(Request $request)
    {
        $request->validate([
            'student_id'      => ['required', 'exists:students,id'],
            'assessment_date' => ['required', 'date'],
        ]);

        $student = Student::findOrFail($request->integer('student_id'));
        $activityTransaction = $student->activityTransaction()->latest()->first();
        $studentIsNormal     = $activityTransaction ? (bool)$activityTransaction->student_is_normal : true;

        $ageInDays = $this->getAgeInDaysFor($student, Carbon::parse($request->assessment_date));

        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')
            ->orderBy('id')
            ->get();

        $filtered = $this->filterAndGroupParameters($parameters, $ageInDays, $studentIsNormal);

        // bentuk JSON minimalis utk frontend
        $payload = [];
        foreach ($filtered as $cat => $items) {
            $payload[$cat] = $items->map(function ($p) {
                return [
                    'id'           => $p->id,
                    'name'         => $p->test_element_name,
                    'description'  => $p->test_element_description,
                    'percent_25'   => $p->percent_25,
                    'percent_50'   => $p->percent_50,
                    'percent_75'   => $p->percent_75,
                    'percent_100'  => $p->percent_100,
                ];
            })->values();
        }

        return response()->json([
            'ok'                => true,
            'student_is_normal' => $studentIsNormal,
            'age_in_days'       => $ageInDays,
            'data'              => $payload,
        ]);
    }

    /**
     * (Opsional) AJAX: Ambil hasil assessment terakhir siswa sebagai prefill.
     * Response: { ok, last_date, items: { param_id: { result_code, note } } }
     */
    public function lastResults(Student $student)
    {
        $last = MmdstAssessment::with('items')
            ->where('student_id', $student->id)
            ->latest('assessment_date')
            ->first();

        if (!$last) {
            return response()->json([
                'ok'        => true,
                'last_date' => null,
                'items'     => (object) [],
            ]);
        }

        $items = [];
        foreach ($last->items as $it) {
            $items[$it->mmdst_parameter_id] = [
                'result_code' => $it->result_code,
                'note'        => (string) $it->note,
            ];
        }

        return response()->json([
            'ok'        => true,
            'last_date' => optional($last->assessment_date)->toDateString(),
            'items'     => $items,
        ]);
    }

    /* ===================== Helpers (private) ===================== */

    private function getAgeInDaysFor(Student $student, Carbon $onDate): int
    {
        if (method_exists($student, 'getAgeInDaysAt')) {
            return (int) $student->getAgeInDaysAt($onDate);
        }
        if ($student->birth_date) {
            return (int) Carbon::parse($student->birth_date)->diffInDays($onDate);
        }
        return 0;
    }

    /**
     * Filter & group parameter per kategori.
     * - Jika $studentIsNormal = true:
     * inRange: p25 <= age <= p100
     * below  : p100 < age → ambil 2 paling dekat (p100 desc)
     * above  : p25  > age → ambil 2 paling dekat (p25  asc)
     * merged : below + inRange + above (unique id)
     * - Jika false: tampilkan semua by kategori (urut).
     */
    private function filterAndGroupParameters($parameters, int $ageInDays, bool $studentIsNormal)
    {
        // group dulu berdasarkan kategori
        $grouped = $parameters
            ->filter(fn($p) => !is_null($p->percent_25) && !is_null($p->percent_100))
            ->groupBy(fn($p) => optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori');

        $result = collect();

        foreach ($grouped as $categoryName => $paramsInCategory) {
            $sorted = $paramsInCategory
                ->sortBy([
                    ['percent_25', 'asc'],
                    ['percent_100', 'asc'],
                    ['id', 'asc'],
                ])->values();

            if (!$studentIsNormal) {
                // tampilkan semua
                $picked = $sorted->values();
            } else {
                // normal → filter
                $inRange = $sorted->filter(fn($p) => $ageInDays >= $p->percent_25 && $ageInDays <= $p->percent_100)->values();

                $pastClosest2 = $sorted
                    ->filter(fn($p) => $p->percent_100 < $ageInDays)
                    ->sortByDesc('percent_100')
                    ->take(2)
                    ->values();

                $futureClosest2 = $sorted
                    ->filter(fn($p) => $p->percent_25 > $ageInDays)
                    ->sortBy('percent_25')
                    ->take(2)
                    ->values();

                $picked = $pastClosest2
                    ->concat($inRange)
                    ->concat($futureClosest2)
                    ->unique('id')
                    ->values();
            }

            // HAPUS NR DARI DAFTAR OPSI
            $picked->transform(function ($p) {
                $p->result_codes = ['P', 'F', 'R', 'OP'];
                return $p;
            });

            $result->put($categoryName, $picked);
        }

        return $result;
    }

    /**
     * Klasifikasi bucket usia untuk frontend (termasuk CRITICAL_75_100):
     * - OVERDUE   : age >= p100
     * - CRITICAL_75_100: age >= p75 dan < p100
     * - AT_LINE   : age == p25/p50/p75/p100
     * - IN_WINDOW : p25 <= age < p75
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
        if ($age >= $p75 && $age < $p100) {
            return 'CRITICAL_75_100'; // Zona Kritis Revisi
        }
        if ($age === $p25 || $age === $p50 || $age === $p75 || $age === $p100) {
            return 'AT_LINE';
        }
        if ($age >= $p25 && $age < $p75) {
            return 'IN_WINDOW';
        }
        return 'NOT_YET';
    }
}
