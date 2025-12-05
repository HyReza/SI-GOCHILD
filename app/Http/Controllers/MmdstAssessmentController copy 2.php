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
use Illuminate\Support\Facades\Log;

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
    // public function create()
    // {
    //     $students   = Student::orderBy('student_name')->get(['id', 'student_name', 'student_number', 'birth_date']);
    //     $categories = CategoryParameter::orderBy('category_parameter_name')->get(['id', 'category_parameter_name']);
    //     $parameters = MmdstParameter::with('stimulationCategory')
    //         ->where('parameter_is_active', 1)
    //         ->orderBy('stimulation_category_id')->orderBy('id')->get();

    //     return view('admin.mmdst-assessment.mmdst-assessment-create.index', compact('students', 'categories', 'parameters'));
    // }

    public function create()
    {
        // Ambil data siswa
        $students = Student::orderBy('student_name')->get(['id', 'student_name', 'student_number', 'birth_date']);

        // Ambil kategori parameter
        $categories = CategoryParameter::orderBy('category_parameter_name')->get(['id', 'category_parameter_name']);

        // Ambil parameter yang aktif
        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')->orderBy('id')->get();

        // Ambil status normal siswa yang pertama (siswa yang dipilih)
        $studentIsNormal = false;
        $student = $students->first(); // Cek siswa pertama (bisa diganti dengan siswa yang dipilih)
        if ($student) {
            $studentIsNormal = $student->student_is_normal;
        }

        // Kelompokkan parameter berdasarkan kategori
        $grouped = $parameters->groupBy(
            fn($p) => optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori'
        );

        // Jika student_is_normal = true, filter parameter sesuai rentang usia
        if ($studentIsNormal) {
            foreach ($grouped as $category => &$params) {
                $params = $params->filter(function ($param) use ($student) {
                    $ageInDays = $student->getAgeInDaysAt(now()); // hitung umur siswa berdasarkan tanggal sekarang
                    return ($ageInDays >= $param->percent_25 && $ageInDays <= $param->percent_100);
                });

                // Tambahkan 2 parameter sebelumnya dan 2 parameter setelahnya berdasarkan usia
                $params = $params->sortBy('percent_25')->values();
                foreach ($params as $index => $param) {
                    $nextParams = $params->slice($index + 1, 2); // Ambil 2 parameter setelahnya
                    $prevParams = $params->slice($index - 2, 2); // Ambil 2 parameter sebelumnya
                    $param->next = $nextParams;
                    $param->prev = $prevParams;
                }
            }
        }

        // Menambahkan opsi NR untuk parameter yang tidak wajib diujikan
        foreach ($grouped as $category => &$params) {
            foreach ($params as &$param) {
                $param->result_codes = ['P', 'F', 'R', 'OP', 'NR']; // Menambahkan opsi NR
            }
        }

        return view('admin.mmdst-assessment.mmdst-assessment-create.index', compact('students', 'categories', 'parameters', 'grouped', 'studentIsNormal'));
    }



    /** Simpan assessment baru (manual). */
    // public function store(Request $request): RedirectResponse
    // {
    //     $validated = $request->validate([
    //         'student_id'      => ['required', 'exists:students,id'],
    //         'assessment_date' => ['required', 'date'],
    //         'notes'           => ['nullable', 'string'],
    //         'items'           => ['required', 'array', 'min:1'],
    //         'items.*.parameter_id' => ['required', 'exists:mmdst_parameters,id'],
    //         'items.*.result_code'  => ['required', 'in:P,F,R,OP'],
    //         'items.*.note'         => ['nullable', 'string'],
    //     ], [
    //         'items.required' => 'Minimal 1 item dinilai.',
    //     ]);

    //     $student = Student::findOrFail($validated['student_id']);
    //     if (!$student->birth_date) {
    //         return back()->withErrors(['student_id' => 'Tanggal lahir siswa belum diisi.'])->withInput();
    //     }

    //     $ageInDays = $student->getAgeInDaysAt(Carbon::parse($validated['assessment_date']));

    //     DB::transaction(function () use ($validated, $ageInDays) {
    //         $assessment = MmdstAssessment::create([
    //             'student_id'      => $validated['student_id'],
    //             'assessment_date' => $validated['assessment_date'],
    //             'age_in_days'     => $ageInDays,
    //             'notes'           => $validated['notes'] ?? null,
    //             'created_by'      => Auth::id(),
    //         ]);

    //         $paramMap = MmdstParameter::whereIn('id', collect($validated['items'])->pluck('parameter_id'))
    //             ->get()->keyBy('id');

    //         foreach ($validated['items'] as $row) {
    //             $param = $paramMap[$row['parameter_id']];
    //             $isAgeLine = $ageInDays >= (int) ($param->percent_25 ?? 0);
    //             $isDelay   = $row['result_code'] === 'F' && $ageInDays >= (int) ($param->percent_100 ?? PHP_INT_MAX);

    //             MmdstAssessmentItem::create([
    //                 'assessment_id'           => $assessment->id,
    //                 'mmdst_parameter_id'      => $param->id,
    //                 'stimulation_category_id' => $param->stimulation_category_id,
    //                 'result_code'             => $row['result_code'],
    //                 'is_delay'                => $isDelay,
    //                 'is_age_line'             => $isAgeLine,
    //                 'note'                    => $row['note'] ?? null,
    //             ]);
    //         }

    //         $this->scoring->recalc($assessment);
    //     });

    //     return redirect()->route('mmdst-assessments.index')->with('success', 'Penilaian MMDST berhasil disimpan.');
    // }

    /** Detail assessment + penanda status untuk UI. */
    public function show(\App\Models\MmdstAssessment $mmdst_assessment)
    {
        $mmdst_assessment->load([
            'student',
            'creator',
            'items.parameter.stimulationCategory',
            'sectorSummaries.category',
        ]);

        $age = (int) $mmdst_assessment->age_in_days;

        // ⬅️ tambahkan 'test_element_description' di select
        $allParams = \App\Models\MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')->orderBy('id')
            ->get([
                'id',
                'test_element_name',
                'test_element_description', // <-- WAJIB agar deskripsi muncul
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
                    'result_code' => $it->result_code, // P/F/R/OP
                    'passed'      => $it->result_code === 'P',
                    'failed'      => $it->result_code === 'F',
                    'is_delay'    => (bool) $it->is_delay,
                    'is_age_line' => (bool) $it->is_age_line,
                ];
            })
            ->toArray();

        // klasifikasi bucket usia
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



    // /** Edit assessment + penanda status untuk pewarnaan UI. */
    // public function edit(\App\Models\MmdstAssessment $mmdst_assessment)
    // {
    //     $mmdst_assessment->load(['student', 'creator', 'items']);

    //     $parameters = \App\Models\MmdstParameter::with('stimulationCategory')
    //         ->where('parameter_is_active', 1)
    //         ->orderBy('stimulation_category_id')
    //         ->orderBy('id')
    //         ->get([
    //             'id',
    //             'test_element_name',
    //             'test_element_description', // <-- sertakan deskripsi
    //             'stimulation_category_id',
    //             'percent_25',
    //             'percent_50',
    //             'percent_75',
    //             'percent_100'
    //         ]);

    //     $existing = $mmdst_assessment->items->keyBy('mmdst_parameter_id');

    //     $ageInDays = (int)$mmdst_assessment->age_in_days;
    //     $bucketMap = $parameters->mapWithKeys(function ($p) use ($ageInDays) {
    //         $p25  = (int)($p->percent_25  ?? PHP_INT_MAX);
    //         $p50  = (int)($p->percent_50  ?? -1);
    //         $p75  = (int)($p->percent_75  ?? -1);
    //         $p100 = (int)($p->percent_100 ?? PHP_INT_MAX);

    //         $bucket = 'NOT_YET';
    //         if ($ageInDays >= $p100) {
    //             $bucket = 'OVERDUE';
    //         } elseif ($ageInDays === $p25 || $ageInDays === $p50 || $ageInDays === $p75 || $ageInDays === $p100) {
    //             $bucket = 'AT_LINE';
    //         } elseif ($ageInDays >= $p25 && $ageInDays < $p100) {
    //             $bucket = 'IN_WINDOW';
    //         }

    //         return [$p->id => $bucket];
    //     });

    //     // testedMap untuk Edit
    //     $testedMap = $mmdst_assessment->items->mapWithKeys(function ($it) {
    //         return [$it->mmdst_parameter_id => [
    //             'tested'      => true,
    //             'result_code' => $it->result_code,
    //             'is_delay'    => (bool)$it->is_delay,
    //             'is_age_line' => (bool)$it->is_age_line,
    //             'passed'      => $it->result_code === 'P',
    //             'failed'      => $it->result_code === 'F',
    //         ]];
    //     });

    //     return view('admin.mmdst-assessment.mmdst-assessment-edit.index', compact(
    //         'mmdst_assessment',
    //         'parameters',
    //         'existing',
    //         'bucketMap',
    //         'testedMap'
    //     ));
    // }


    public function edit(MmdstAssessment $mmdst_assessment)
    {
        // Ambil data siswa & info normal/tdk
        $students = Student::orderBy('student_name')->get(['id', 'student_name', 'student_number', 'birth_date']);
        $student  = $mmdst_assessment->student;

        // Ambil normal status dari activity_transactions terakhir (default: true)
        $activityTransaction = $student->activityTransaction()->latest()->first();
        $studentIsNormal     = $activityTransaction ? (bool)$activityTransaction->student_is_normal : true;

        // Tentukan umur (hari) — pakai age_in_days penilaian, jatuh ke perhitungan manual jika tidak ada
        $assessmentDate = $mmdst_assessment->assessment_date ?? now();
        $ageInDays = $mmdst_assessment->age_in_days
            ?? (method_exists($student, 'getAgeInDaysAt') ? $student->getAgeInDaysAt($assessmentDate) : $student->birth_date?->diffInDays($assessmentDate));

        Log::info('MMDST edit — context', [
            'student_id'      => $student->id,
            'student_name'    => $student->student_name,
            'student_is_normal' => $studentIsNormal,
            'assessment_date' => $assessmentDate?->toDateString(),
            'age_in_days'     => $ageInDays,
        ]);

        // Ambil parameter aktif + relasi kategori
        $parameters = MmdstParameter::with('stimulationCategory')
            ->where('parameter_is_active', 1)
            ->orderBy('stimulation_category_id')
            ->orderBy('id')
            ->get();

        // Kelompokkan per kategori (nama kategori), hanya consider param yg punya percent_25 & percent_100
        $grouped = $parameters
            ->filter(fn($p) => !is_null($p->percent_25) && !is_null($p->percent_100))
            ->groupBy(fn($p) => optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori');

        Log::info('Kategori ditemukan', ['categories' => $grouped->keys()->values()->all()]);

        // ============ LOGIKA FILTER ============
        // Untuk setiap kategori:
        // - sort by percent_25 ASC, percent_100 ASC (stable)
        // - inRange        : umur di antara [25..100]
        // - pastClosest2   : percent_100 < umur, ambil 2 yang percent_100 paling besar
        // - futureClosest2 : percent_25  > umur, ambil 2 yang percent_25  paling kecil
        // - merge unik
        $filtered_categories = collect();

        foreach ($grouped as $categoryName => $paramsInCategory) {
            $sorted = $paramsInCategory
                ->sortBy([
                    ['percent_25', 'asc'],
                    ['percent_100', 'asc'],
                    ['id', 'asc'],
                ])
                ->values();

            // In-range
            $inRange = $sorted->filter(function ($p) use ($ageInDays) {
                Log::info('Cek param', [
                    'category'    => optional($p->stimulationCategory)->category_parameter_name,
                    'parameter'   => $p->test_element_name,
                    'age'         => $ageInDays,
                    'p25'         => $p->percent_25,
                    'p100'        => $p->percent_100,
                    'in_range'    => ($ageInDays >= $p->percent_25 && $ageInDays <= $p->percent_100),
                ]);
                return $ageInDays >= $p->percent_25 && $ageInDays <= $p->percent_100;
            })->values();

            // 2 terdekat sebelum (p100 < age) — ambil yg paling mendekati dari bawah
            $pastClosest2 = $sorted
                ->filter(fn($p) => $p->percent_100 < $ageInDays)
                ->sortByDesc('percent_100')
                ->take(2)
                ->values();

            // 2 terdekat sesudah (p25 > age) — ambil yg paling mendekati dari atas
            $futureClosest2 = $sorted
                ->filter(fn($p) => $p->percent_25 > $ageInDays)
                ->sortBy('percent_25')
                ->take(2)
                ->values();

            Log::info('Ringkasan kategori', [
                'category'        => $categoryName,
                'total_sorted'    => $sorted->count(),
                'in_range_count'  => $inRange->count(),
                'past_count'      => $pastClosest2->count(),
                'future_count'    => $futureClosest2->count(),
                'past_names'      => $pastClosest2->pluck('test_element_name')->all(),
                'future_names'    => $futureClosest2->pluck('test_element_name')->all(),
            ]);

            // Jika siswa NORMAL → pakai filter; jika tidak normal → tampilkan semua (atau tetap pakai filter jika diinginkan)
            if ($studentIsNormal) {
                $picked = $inRange
                    ->concat($pastClosest2)
                    ->concat($futureClosest2)
                    ->unique('id')
                    // urutkan lagi biar enak dibaca: p25 asc → p100 asc
                    ->sortBy([
                        ['percent_25', 'asc'],
                        ['percent_100', 'asc'],
                        ['id', 'asc'],
                    ])
                    ->values();
            } else {
                // Jika ingin non-normal tetap semua, pakai $sorted
                $picked = $sorted->values();
            }

            Log::info('Final picked (per kategori)', [
                'category' => $categoryName,
                'count'    => $picked->count(),
                'names'    => $picked->pluck('test_element_name')->all(),
            ]);

            // Tambahkan kode hasil (radio)
            $picked->transform(function ($p) {
                $p->result_codes = ['P', 'F', 'R', 'OP', 'NR'];
                return $p;
            });

            $filtered_categories->put($categoryName, $picked);
        }

        // Data assessment yang sudah ada (untuk pre-check radio/notes)
        $existing = $mmdst_assessment->items->keyBy('mmdst_parameter_id');
        Log::info('Existing assessment items', ['existing_count' => $existing->count()]);

        // Kirim ke view — PENTING: pakai $filtered_categories supaya tidak tampil semua
        return view('admin.mmdst-assessment.mmdst-assessment-edit.index', compact(
            'students',
            'mmdst_assessment',
            'studentIsNormal',
            'ageInDays',
            'filtered_categories',
            'existing'
        ));
    }



    // public function edit(MmdstAssessment $mmdst_assessment)
    // {
    //     // === Data dasar siswa ===
    //     $students = Student::orderBy('student_name')->get(['id', 'student_name', 'student_number', 'birth_date']);
    //     $student  = $mmdst_assessment->student;

    //     // Ambil status normal dari activity_transactions terbaru
    //     $activityTransaction = $student->activityTransaction()->latest()->first();
    //     $studentIsNormal = $activityTransaction ? (bool) $activityTransaction->student_is_normal : true;

    //     // Umur acuan: gunakan tanggal penilaian (lebih akurat dibanding now())
    //     $ageInDays = method_exists($student, 'getAgeInDaysAt')
    //         ? $student->getAgeInDaysAt($mmdst_assessment->assessment_date)
    //         : ($student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->diffInDays($mmdst_assessment->assessment_date) : 0);

    //     Log::info('Editing MMDST Assessment', [
    //         'student_id'      => $student->id,
    //         'student_name'    => $student->student_name,
    //         'student_is_normal' => $studentIsNormal,
    //         'assessment_date' => $mmdst_assessment->assessment_date?->toDateString(),
    //         'age_in_days'     => $ageInDays,
    //     ]);

    //     // === Ambil semua parameter aktif + kategori ===
    //     $parameters = MmdstParameter::with('stimulationCategory')
    //         ->where('parameter_is_active', 1)
    //         ->orderBy('stimulation_category_id')
    //         ->orderBy('id')
    //         ->get();

    //     Log::info('Raw parameters loaded', [
    //         'total' => $parameters->count(),
    //     ]);

    //     // Kelompokkan per kategori (nama kategori)
    //     $grouped = $parameters->groupBy(function ($p) {
    //         return optional($p->stimulationCategory)->category_parameter_name ?? 'Tanpa Kategori';
    //     });

    //     Log::info('Categories detected', [
    //         'categories' => $grouped->keys()->toArray(),
    //     ]);

    //     // === Filter final per kategori yang akan dipakai VIEW ===
    //     // Struktur: [ 'Nama Kategori' => Collection<Param> (sudah gabungan inRange + 2 bawah + 2 atas) ]
    //     $filtered_categories = collect();

    //     foreach ($grouped as $categoryName => $params) {
    //         // Sort stabil: pakai percent_25 ASC, lalu percent_100 ASC untuk konsistensi
    //         $sorted = $params->sortBy([
    //             ['percent_25', 'asc'],
    //             ['percent_100', 'asc'],
    //             ['id', 'asc'],
    //         ])->values();

    //         // Bersihkan nilai null agar perbandingan aman (tetap biarkan null melewati filter di bawah/atas dengan hati-hati)
    //         $withValid = $sorted->filter(function ($p) {
    //             return !is_null($p->percent_25) && !is_null($p->percent_100);
    //         })->values();

    //         // 1) IN-RANGE: age between 25%..100%
    //         $inRange = $withValid->filter(function ($p) use ($ageInDays) {
    //             return $ageInDays >= $p->percent_25 && $ageInDays <= $p->percent_100;
    //         })->values();

    //         // 2) BELOW (LEWAT/DI BAWAH UMUR): percent_100 < age → ambil 2 terdekat berdasarkan percent_100 DESC
    //         $below = $withValid->filter(function ($p) use ($ageInDays) {
    //             return $p->percent_100 < $ageInDays;
    //         })
    //             ->sortByDesc('percent_100')
    //             ->take(2)
    //             ->values();

    //         // 3) ABOVE (BELUM WAKTU/DI ATAS UMUR): percent_25 > age → ambil 2 terdekat berdasarkan percent_25 ASC
    //         $above = $withValid->filter(function ($p) use ($ageInDays) {
    //             return $p->percent_25 > $ageInDays;
    //         })
    //             ->sortBy('percent_25')
    //             ->take(2)
    //             ->values();

    //         Log::info('Per-category split', [
    //             'category'        => $categoryName,
    //             'total_in_cat'    => $params->count(),
    //             'with_valid'      => $withValid->count(),
    //             'in_range_count'  => $inRange->count(),
    //             'below_count'     => $below->count(),
    //             'above_count'     => $above->count(),
    //             'in_range_names'  => $inRange->pluck('test_element_name')->toArray(),
    //             'below_names'     => $below->pluck('test_element_name')->toArray(),
    //             'above_names'     => $above->pluck('test_element_name')->toArray(),
    //         ]);

    //         // Gabungkan: urutan yang enak dibaca (2 bawah terdekat → in-range → 2 atas terdekat)
    //         // lalu unique by id, untuk berjaga-jaga.
    //         $combined = $below->concat($inRange)->concat($above)
    //             ->unique('id')
    //             ->values();

    //         // Tambahkan kode hasil ke setiap parameter (P/F/R/OP/NR)
    //         $combined->each(function ($p) {
    //             $p->result_codes = ['P', 'F', 'R', 'OP', 'NR'];
    //         });

    //         // Simpan untuk view
    //         $filtered_categories->put($categoryName, $combined);

    //         Log::info('Final combined per category (for view)', [
    //             'category'    => $categoryName,
    //             'final_count' => $combined->count(),
    //             'final_names' => $combined->pluck('test_element_name')->toArray(),
    //         ]);
    //     }

    //     // Jika murid TIDAK normal (studentIsNormal = false), kamu bisa pilih:
    //     // - tampilkan SEMUA (kembalikan $grouped apa adanya), atau
    //     // - tetap pakai filter di atas.
    //     //
    //     // Di sini: kalau TIDAK normal → pakai SEMUA (sesuai beberapa use case).
    //     if (!$studentIsNormal) {
    //         $filtered_categories = $grouped->map(function ($params) {
    //             return $params->values()->each(function ($p) {
    //                 $p->result_codes = ['P', 'F', 'R', 'OP', 'NR'];
    //             });
    //         });
    //         Log::info('Student not normal => show all params per category', [
    //             'categories' => $filtered_categories->keys()->toArray(),
    //         ]);
    //     }

    //     // Ambil assessment items yang sudah ada (untuk set radio/check yang sudah pernah dinilai)
    //     $existing = $mmdst_assessment->items->keyBy('mmdst_parameter_id');
    //     Log::info('Existing assessment items', ['count' => $existing->count()]);

    //     // Kirim ke view — PENTING: kita kirim $filtered_categories (ini yang dipakai di Blade)
    //     return view('admin.mmdst-assessment.mmdst-assessment-edit.index', compact(
    //         'students',
    //         'mmdst_assessment',
    //         'studentIsNormal',
    //         'ageInDays',
    //         'filtered_categories',
    //         'existing'
    //     ));
    // }

























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
    public function destroy(\App\Models\MmdstAssessment $mmdst_assessment): \Illuminate\Http\RedirectResponse
    {
        $student = $mmdst_assessment->student; // bisa null
        $mmdst_assessment->delete();

        if ($student) {
            // kembali ke riwayat siswa yg bersangkutan
            return redirect()
                ->route('mmdst.history', $student)
                ->with('success', 'Penilaian MMDST dihapus.');
        }

        // fallback: ke index umum
        return redirect()
            ->route('mmdst-assessments.index')
            ->with('success', 'Penilaian MMDST dihapus.');
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
