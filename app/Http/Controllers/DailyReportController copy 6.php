<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Service;
use App\Models\SubTheme;
use App\Models\Attendance;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceTransaction;
use App\Models\DailyReportBabyDetail;
use Illuminate\Support\Facades\Schema;
use App\Models\DailyReportChildrenDetail;

class DailyReportController extends Controller
{
    // =========================
    // INDEX / LIST
    // =========================
    public function index(Request $request)
    {
        $query   = $request->input('query');
        $service = $request->input('service_id');

        $activityTransactions = ActivityTransaction::with(['student', 'service'])
            ->when($query, function ($q) use ($query) {
                $q->whereHas('student', function ($s) use ($query) {
                    $s->where('student_name', 'like', "%{$query}%")
                        ->orWhere('student_number', 'like', "%{$query}%");
                });
            })
            ->when($service, fn($q) => $q->where('service_id', $service))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $services = Service::all();

        if ($request->ajax() || $request->boolean('partial')) {
            return response()->json([
                'rows'       => view('admin.daily-report.index-daily._rows', compact('activityTransactions'))->render(),
                'pagination' => view('admin.daily-report.index-daily._pagination', compact('activityTransactions'))->render(),
                'total'      => $activityTransactions->total(),
            ]);
        }

        return view('admin.daily-report.index-daily.index', compact('activityTransactions', 'services'));
    }

    // =========================
    // CREATE
    // =========================
    public function create($activityTransactionId, Request $request)
    {
        $activityTransaction = ActivityTransaction::with(['student', 'service'])->findOrFail($activityTransactionId);

        $today = Carbon::today()->toDateString();
        $subthemes = $this->fetchSubthemesCollection($today);

        return view('admin.daily-report.create-daily.index', compact('activityTransaction', 'subthemes'));
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'activity_transaction_id' => ['required', 'integer', 'exists:activity_transactions,id'],
            'period'                  => ['required', 'date'],
        ]);

        $tx = ActivityTransaction::with(['student', 'service'])->findOrFail(
            (int) $request->input('activity_transaction_id')
        );
        $serviceId = (int) $tx->service_id;

        $dailyData = [
            'activity_transaction_id' => $tx->id,
            'service_id'              => $serviceId,
            'period'                  => $request->input('period'),
            'body_temperature'        => $request->input('body_temperature'),
            'arrival_time'            => $request->input('arrival_time'),
            'departure_time'          => $request->input('departure_time'),
            'breakfast'               => $request->input('breakfast'),
            'health_status'           => $request->input('health_status'),
            'sickness_description'    => $request->input('sickness_description'),
            'medication_status'       => $request->input('medication_status'),
            'condition'               => $request->input('condition'),
            'stimulation_description' => $request->input('stimulation_description'),
            'notes'                   => $request->input('notes'),
            // opsional—hanya simpan bila kolom ada:
            'user_id'                 => Auth::id(),
            'student_id'              => $tx->student->id ?? null,
        ];
        $dailyData = $this->onlyTableColumns('daily_reports', $dailyData);

        DB::beginTransaction();
        try {
            /** @var DailyReport $dailyReport */
            $dailyReport = DailyReport::create($dailyData);

            if ($serviceId === 1) {
                // ===== BABY =====
                $baby = $this->buildBabyPayloadFromRequest($request);
                $baby['daily_report_id'] = $dailyReport->id;

                $baby = $this->onlyTableColumns('daily_report_baby_details', $baby);
                DailyReportBabyDetail::create($baby);
            } elseif ($serviceId === 2) {
                // ===== CHILDREN =====
                $children = $this->buildChildrenPayloadFromRequest($request);
                $children['daily_report_id'] = $dailyReport->id;

                $children = $this->onlyTableColumns('daily_report_children_details', $children);
                DailyReportChildrenDetail::create($children);
            }

            DB::commit();
            return redirect()->route('daily-report.index')->with('success', 'Laporan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('daily-report.store failed', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['store' => 'Gagal menyimpan laporan.'])->withInput();
        }
    }

    // =========================
    // SHOW / EDIT
    // =========================
    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load([
            'activityTransaction.student',
            'activityTransaction.service',
            'babyDetail',
            'childrenDetail.session1Material',
            'childrenDetail.session2Material',
        ]);

        return view('admin.daily-report.show-daily.index', compact('dailyReport'));
    }

    public function edit(DailyReport $dailyReport)
    {
        $dailyReport->load([
            'activityTransaction.student',
            'activityTransaction.service',
            'babyDetail',
            'childrenDetail',
        ]);

        $activityTransaction = $dailyReport->activityTransaction;
        $subthemes = collect();
        if ((int)($dailyReport->service_id ?? 0) === 2) {
            $date = $dailyReport->period ? Carbon::parse($dailyReport->period)->toDateString() : Carbon::today()->toDateString();
            $subthemes = $this->fetchSubthemesCollection($date);
        }

        return view('admin.daily-report.edit-daily.index', compact('dailyReport', 'activityTransaction', 'subthemes'));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, DailyReport $dailyReport)
    {
        $request->validate([
            'period' => ['required', 'date'],
        ]);

        $parent = [
            'period'                  => $request->input('period'),
            'body_temperature'        => $request->input('body_temperature'),
            'arrival_time'            => $request->input('arrival_time'),
            'departure_time'          => $request->input('departure_time'),
            'breakfast'               => $request->input('breakfast'),
            'health_status'           => $request->input('health_status'),
            'sickness_description'    => $request->input('sickness_description'),
            'medication_status'       => $request->input('medication_status'),
            'condition'               => $request->input('condition'),
            'stimulation_description' => $request->input('stimulation_description', $dailyReport->stimulation_description),
            'notes'                   => $request->input('notes'),
        ];
        $parent = $this->onlyTableColumns('daily_reports', $parent);

        DB::beginTransaction();
        try {
            $dailyReport->update($parent);

            $serviceId = (int)($dailyReport->service_id ?? 0);

            if ($serviceId === 1) {
                // ===== UPDATE BABY =====
                $payload = $this->buildBabyPayloadFromRequest($request);
                $payload = $this->onlyTableColumns('daily_report_baby_details', $payload);

                /** @var DailyReportBabyDetail|null $detail */
                $detail = $dailyReport->babyDetail;
                if ($detail) {
                    $detail->update($payload);
                } else {
                    $payload['daily_report_id'] = $dailyReport->id;
                    DailyReportBabyDetail::create($payload);
                }
            } elseif ($serviceId === 2) {
                // ===== UPDATE CHILDREN =====
                $payload = $this->buildChildrenPayloadFromRequest($request);
                $payload = $this->onlyTableColumns('daily_report_children_details', $payload);

                /** @var DailyReportChildrenDetail|null $detail */
                $detail = $dailyReport->childrenDetail;
                if ($detail) {
                    $detail->update($payload);
                } else {
                    $payload['daily_report_id'] = $dailyReport->id;
                    DailyReportChildrenDetail::create($payload);
                }
            }

            DB::commit();
            return redirect()->route('daily-report.show', $dailyReport)->with('success', 'Laporan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('daily-report.update failed', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['update' => 'Gagal memperbarui laporan.'])->withInput();
        }
    }

    // =========================
    // HISTORY / DESTROY
    // =========================
    public function history($activityTransactionId, Request $request)
    {
        $tx = ActivityTransaction::with(['student', 'service'])->findOrFail($activityTransactionId);

        $q = DailyReport::with(['babyDetail', 'childrenDetail'])
            ->where('activity_transaction_id', $tx->id);

        if ($request->filled('date'))      $q->whereDate('period', $request->date);
        if ($request->filled('date_from')) $q->whereDate('period', '>=', $request->date_from);
        if ($request->filled('date_to'))   $q->whereDate('period', '<=', $request->date_to);
        if ($request->filled('health_status')) $q->where('health_status', $request->health_status);
        if ($request->filled('condition'))     $q->where('condition', $request->condition);
        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $q->where(function ($w) use ($term) {
                $w->where('notes', 'like', "%{$term}%")
                    ->orWhere('stimulation_description', 'like', "%{$term}%");
            });
        }

        $reports = $q->orderByDesc('period')->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax() || $request->boolean('partial')) {
            return response()->json([
                'rows'       => view('admin.daily-report.history-daily._rows', ['reports' => $reports, 'tx' => $tx])->render(),
                'pagination' => view('admin.daily-report.history-daily._pagination', ['reports' => $reports, 'tx' => $tx])->render(),
                'total'      => $reports->total(),
            ]);
        }

        return view('admin.daily-report.history-daily.index', ['tx' => $tx, 'reports' => $reports]);
    }

    public function destroy(DailyReport $dailyReport, Request $request)
    {
        try {
            $dailyReport->delete();

            if ($request->ajax()) {
                return response()->json(['ok' => true, 'message' => 'Laporan dihapus.'], 200);
            }
            return back()->with('success', 'Laporan dihapus.');
        } catch (\Throwable $e) {
            Log::error('daily-report.destroy failed', ['msg' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json(['ok' => false, 'message' => 'Gagal menghapus laporan.'], 500);
            }
            return back()->with('error', 'Gagal menghapus laporan.');
        }
    }

    // =========================
    // ===== ENDPOINT AJAX =====
    // =========================

    /** /check-attendance/{student}/{date} */
    public function checkAttendance($studentId, $date)
    {
        try {
            $attTxn = AttendanceTransaction::whereDate('date_attendance', $date)->first();

            if (!$attTxn) {
                return response()->json([
                    'status'         => 'Siswa Belum Absen / Data Absen Belum Dibuat',
                    'check_in_time'  => null,
                    'check_out_time' => null,
                ]);
            }

            $attendance = Attendance::where('attendances_transaction_id', $attTxn->id)
                ->whereHas('activityTransaction', fn($q) => $q->where('student_id', $studentId))
                ->first();

            if (!$attendance) {
                return response()->json([
                    'status'         => 'Siswa Belum Absen / Data Absen Belum Dibuat',
                    'check_in_time'  => null,
                    'check_out_time' => null,
                ]);
            }

            $status = match ($attendance->check_in_status) {
                'Present' => 'Siswa Sudah Absen',
                'Excused' => 'Siswa Izin',
                'Sick'    => 'Siswa Sakit',
                'Absent'  => 'Siswa Tidak Hadir Tanpa Keterangan',
                default   => 'Status Absen Tidak Dikenali',
            };

            return response()->json([
                'status'         => $status,
                'check_in_time'  => $attendance->check_in_time ?: 'Belum Check-in',
                'check_out_time' => $attendance->check_out_time ?: 'Belum Check-out',
            ]);
        } catch (\Throwable $e) {
            Log::error('[checkAttendance] ' . $e->getMessage());
            return response()->json(['status' => 'Server Error'], 500);
        }
    }

    /** /get-subthemes/{date} */
    /* ==========================
     *  AJAX: SUBTHEMES BY DATE
     * ==========================*/
    public function getSubthemes(string $periode)
    {
        $subs = $this->subthemesForDate($periode);

        return response()->json([
            'subthemes' => $subs->map(function ($st) {
                return [
                    'id'                     => $st->id,
                    'theme_name'             => $st->theme->theme_name ?? '',
                    'theme_description'      => $st->theme->theme_description ?? '',
                    'sub_theme_name'         => $st->sub_theme_name,
                    'sub_theme_description'  => $st->sub_theme_description,
                    'material'               => $st->material->map(fn($m) => [
                        'id'            => $m->id,
                        'material_name' => $m->material_name,
                    ])->values(),
                ];
            })->values(),
        ]);
    }

    /** Helper ambil subtheme by tanggal (pakai bulan & hari) */
    protected function subthemesForDate(string $periode)
    {
        $date  = Carbon::parse($periode);
        $month = $date->format('m');
        $day   = $date->format('d');

        return SubTheme::with(['theme', 'material'])
            ->whereMonth('sub_theme_start', '<=', $month)
            ->whereMonth('sub_theme_end', '>=', $month)
            ->whereDay('sub_theme_start', '<=', $day)
            ->whereDay('sub_theme_end', '>=', $day)
            ->get();
    }


    /**
     * /stimulation/suggest/{activityTransaction}/{date?}
     * Menggunakan usia HARI (1-based) dan mmdst_parameters + hasil terbaru P/F/R/OP.
     */
    public function suggestStimulation($activityTransactionId, $date = null): JsonResponse
    {
        try {
            $tx = ActivityTransaction::with('student')->findOrFail($activityTransactionId);
            $onDate = $date ? Carbon::parse($date)->startOfDay() : Carbon::today()->startOfDay();
            $dob    = $this->guessStudentDob($tx->student);

            if (!$dob) {
                return response()->json([
                    'text' => 'Tanggal lahir siswa belum diisi, tidak bisa menghitung usia (hari).',
                ], 200);
            }

            $ageDays = $this->ageInDaysOneBased($dob, $onDate);

            [$nowParams, $overParams] = $this->fetchMmdstParametersByAgeDays($ageDays);
            $latestStatuses = $this->fetchLatestStatusesPerParameter($tx->student->id);

            $pickNow  = $this->filterNotPassedByStatuses($nowParams, $latestStatuses);
            $pickOver = $this->filterNotPassedByStatuses($overParams, $latestStatuses);

            if ($pickNow->isEmpty() && empty($latestStatuses)) {
                $pickNow = collect($nowParams);
            }

            if ($pickNow->isEmpty() && $pickOver->isEmpty()) {
                return response()->json([
                    'text'     => "Belum ada saran stimulasi untuk usia {$ageDays} hari.",
                    'age_days' => $ageDays,
                ], 200);
            }

            $lines = [];
            $lines[] = "Saran Stimulasi MMDST (usia ±{$ageDays} hari)";
            $lines[] = "• Prioritas item yang belum lulus / sesuai usia:";

            $take = $pickNow->take(6)->values()->all();
            $take = array_merge($take, $pickOver->take(6)->values()->all());

            $i = 1;
            foreach ($take as $row) {
                $cat   = $row->category_parameter_name ?? 'Kategori';
                $title = $row->test_element_name ?? ("Parameter #" . $row->id);
                $desc  = $row->test_element_description ?? null;

                $line = "{$i}. [{$cat}] {$title}";
                if ($desc) $line .= " — {$desc}";
                $lines[] = $line;
                $i++;
            }

            if (!empty($pickOver) && count($pickOver) > 0) {
                $lines[] = "";
                $lines[] = "*Catatan: ada item yang sudah lewat rentang usia target (>p100) namun belum lulus; prioritaskan.*";
            }

            return response()->json([
                'text'     => implode("\n", $lines),
                'age_days' => $ageDays,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('suggestStimulation error: ' . $e->getMessage());
            return response()->json(['text' => 'Tidak dapat memuat saran stimulasi saat ini.'], 200);
        }
    }

    // =========================
    // ===== HELPERS ===========
    // =========================

    private function fetchSubthemesCollection(string $date)
    {
        if (!class_exists(\App\Models\SubTheme::class)) return collect();

        $q = SubTheme::with([
            'theme:id,theme_name',
            'material:id,sub_theme_id,material_name',
        ]);

        if (Schema::hasTable('sub_themes')) {
            if (Schema::hasColumn('sub_themes', 'start_date') && Schema::hasColumn('sub_themes', 'end_date')) {
                $q->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date);
            } elseif (Schema::hasColumn('sub_themes', 'period_start') && Schema::hasColumn('sub_themes', 'period_end')) {
                $q->whereDate('period_start', '<=', $date)->whereDate('period_end', '>=', $date);
            }
        }

        return $q->orderByDesc('id')->get();
    }

    /** DOB dari berbagai field. */
    private function guessStudentDob($student): ?Carbon
    {
        if (!$student) return null;
        foreach (['birth_date', 'dob', 'date_of_birth', 'tanggal_lahir'] as $f) {
            if (!empty($student->{$f})) {
                try {
                    return Carbon::parse($student->{$f})->startOfDay();
                } catch (\Throwable $e) {
                }
            }
        }
        return null;
    }

    /** Umur (hari) 1-based: selisih hari + 1. */
    private function ageInDaysOneBased(Carbon $dob, Carbon $onDate): int
    {
        $d1 = $dob->copy()->startOfDay();
        $d2 = $onDate->copy()->startOfDay();
        return $d1->diffInDays($d2) + 1;
    }

    /**
     * Ambil parameter relevan berdasar usia HARI dari mmdst_parameters.
     * NOW:  COALESCE(p25,0) <= age <= COALESCE(p100, big)
     * OVER: COALESCE(p100,big) < age
     */
    private function fetchMmdstParametersByAgeDays(int $ageDays): array
    {
        if (!Schema::hasTable('mmdst_parameters')) return [[], []];

        $big = 2147483647;

        $baseSelect = [
            'mmdst_parameters.id',
            'mmdst_parameters.test_element_name',
            'mmdst_parameters.test_element_description',
            'mmdst_parameters.stimulation_category_id',
            'mmdst_parameters.percent_25',
            'mmdst_parameters.percent_50',
            'mmdst_parameters.percent_75',
            'mmdst_parameters.percent_100',
            'category_parameters.category_parameter_name',
        ];

        $now = DB::table('mmdst_parameters')
            ->join('category_parameters', 'category_parameters.id', '=', 'mmdst_parameters.stimulation_category_id')
            ->select($baseSelect)
            ->where('mmdst_parameters.parameter_is_active', 1)
            ->whereRaw('COALESCE(mmdst_parameters.percent_25, 0) <= ?', [$ageDays])
            ->whereRaw('COALESCE(mmdst_parameters.percent_100, ?) >= ?', [$big, $ageDays])
            ->orderBy('mmdst_parameters.stimulation_category_id')
            ->orderBy('mmdst_parameters.id')
            ->limit(120)
            ->get()
            ->all();

        $over = DB::table('mmdst_parameters')
            ->join('category_parameters', 'category_parameters.id', '=', 'mmdst_parameters.stimulation_category_id')
            ->select($baseSelect)
            ->where('mmdst_parameters.parameter_is_active', 1)
            ->whereRaw('COALESCE(mmdst_parameters.percent_100, ?) < ?', [$big, $ageDays])
            ->orderByDesc('mmdst_parameters.percent_100')
            ->limit(120)
            ->get()
            ->all();

        return [$now, $over];
    }

    /** Ambil status terbaru per parameter untuk student. */
    private function fetchLatestStatusesPerParameter(int $studentId): array
    {
        if (!Schema::hasTable('mmdst_assessments') || !Schema::hasTable('mmdst_assessment_items')) return [];

        $rows = DB::table('mmdst_assessment_items as ai')
            ->join('mmdst_assessments as a', 'a.id', '=', 'ai.assessment_id')
            ->where('a.student_id', $studentId)
            ->select([
                'ai.mmdst_parameter_id as param_id',
                'ai.result_code as code',
                'a.assessment_date as dt',
                'ai.id as ai_id',
            ])
            ->orderByDesc('a.assessment_date')
            ->orderByDesc('ai_id')
            ->get();

        $latest = [];
        foreach ($rows as $r) {
            if (!isset($latest[$r->param_id])) {
                $latest[$r->param_id] = strtoupper(trim($r->code));
            }
        }
        return $latest;
    }

    /** Tampilkan semua selain P (Pass). */
    private function filterNotPassedByStatuses(array $params, array $latestStatuses)
    {
        $col = collect($params);
        if (empty($latestStatuses)) return collect();

        return $col->filter(function ($row) use ($latestStatuses) {
            $pid = $row->id ?? null;
            if (!$pid) return true;
            if (!isset($latestStatuses[$pid])) return true; // belum pernah dinilai
            return $latestStatuses[$pid] !== 'P';
        })->values();
    }

    /** Ambil hanya kolom yang beneran ada di tabel. */
    private function onlyTableColumns(string $table, array $data): array
    {
        if (!Schema::hasTable($table)) return [];
        $cols = array_flip(Schema::getColumnListing($table));
        return array_intersect_key($data, $cols);
    }

    /**
     * Terima string JSON / array → selalu array (default []).
     * Dipakai untuk memastikan bisa hapus sampai kosong.
     */
    private function normalizeJsonArray($val): array
    {
        if (is_array($val)) return $val;
        if (is_string($val)) {
            $trim = trim($val);
            if ($trim === '') return [];
            try {
                $d = json_decode($trim, true, 512, JSON_THROW_ON_ERROR);
                return is_array($d) ? $d : [];
            } catch (\Throwable $e) {
                return [];
            }
        }
        return [];
    }

    /**
     * Normalisasi item2 bayi dari Request (coerce ke bentuk yang benar dan filter baris kosong).
     */
    private function buildBabyPayloadFromRequest(Request $request): array
    {
        // ASI/formula
        $asi = $this->normalizeJsonArray($request->input('asi_formula_items'));
        $asi = array_values(array_filter(array_map(function ($r) {
            return [
                'jam'     => isset($r['jam']) ? trim((string)$r['jam']) : '',
                'takaran' => isset($r['takaran']) && $r['takaran'] !== '' ? (int)$r['takaran'] : null,
                'asi'     => (bool)($r['asi'] ?? false),
            ];
        }, $asi), function ($r) {
            // baris dianggap kosong bila semua field kosong/false
            return ($r['jam'] !== '') || ($r['takaran'] !== null) || ($r['asi'] === true);
        }));

        // MPASI
        $mp = $this->normalizeJsonArray($request->input('mpasi_items'));
        $mp = array_values(array_filter(array_map(function ($r) {
            return [
                'jam'    => isset($r['jam']) ? trim((string)$r['jam']) : '',
                'jumlah' => isset($r['jumlah']) ? trim((string)$r['jumlah']) : '',
            ];
        }, $mp), fn($r) => $r['jam'] !== '' || $r['jumlah'] !== ''));

        // Naps
        $naps = $this->normalizeJsonArray($request->input('naps'));
        $naps = array_values(array_filter(array_map(function ($r) {
            return [
                'tidur'  => isset($r['tidur']) ? trim((string)$r['tidur']) : '',
                'bangun' => isset($r['bangun']) ? trim((string)$r['bangun']) : '',
            ];
        }, $naps), fn($r) => $r['tidur'] !== '' || $r['bangun'] !== ''));

        // Diapers
        $diapers = $this->normalizeJsonArray($request->input('diapers'));
        $diapers = array_values(array_filter(array_map(function ($r) {
            return [
                'jam' => isset($r['jam']) ? trim((string)$r['jam']) : '',
                'bak' => (bool)($r['bak'] ?? false),
                'bab' => (bool)($r['bab'] ?? false),
            ];
        }, $diapers), fn($r) => $r['jam'] !== '' || $r['bak'] === true || $r['bab'] === true));

        return [
            'asi_formula_items'      => $asi,           // bisa []
            'mpasi_items'            => $mp,            // bisa []
            'naps'                   => $naps,          // bisa []
            'diapers'                => $diapers,       // bisa []
            'infant_breakfast_text'  => $request->input('infant_breakfast_text'),
            'infant_lunch_text'      => $request->input('infant_lunch_text'),
            'infant_dinner_text'     => $request->input('infant_dinner_text'),
        ];
    }

    /**
     * Normalisasi payload children dari Request.
     */
    private function buildChildrenPayloadFromRequest(Request $request): array
    {
        $s1 = $request->input('session1_material_id') ?? $request->input('session1_description');
        $s2 = $request->input('session2_material_id') ?? $request->input('session2_description');

        return [
            'greeting_and_morning_prayer'       => $request->input('greeting_and_morning_prayer'),
            'session1_material_id'              => $s1 ? (int)$s1 : null,
            'session1_activity'                 => $request->input('session1_activity'),
            'toilet_training_and_duha_prayer'   => $request->input('toilet_training_and_duha_prayer'),
            'session2_material_id'              => $s2 ? (int)$s2 : null,
            'session2_activity'                 => $request->input('session2_activity'),
            'morning_snack'                     => $request->input('morning_snack'),
            'neatness_and_independence'         => $request->input('neatness_and_independence'),
            'cheerful_lunch'                    => $request->input('cheerful_lunch'),
            'cleanliness_and_brushing_training' => $request->input('cleanliness_and_brushing_training'),
            'dhuhr_prayer'                      => $request->input('dhuhr_prayer'),
            'healthy_sleep'                     => $request->input('healthy_sleep'),
            'afternoon_bath'                    => $request->input('afternoon_bath'),
            'afternoon_snack'                   => $request->input('afternoon_snack'),
            'asr_prayer'                        => $request->input('asr_prayer'),
            'extra_stimulation_description'     => $request->input('extra_stimulation_description'),
            'extra_stimulation'                 => $request->input('extra_stimulation'),
            'cheerful_play_description'         => $request->input('cheerful_play_description'),
            'cheerful_play'                     => $request->input('cheerful_play'),
        ];
    }
}
