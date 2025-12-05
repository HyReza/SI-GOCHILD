<?php

namespace App\Http\Controllers;

use App\Models\ActivityTransaction;
use App\Models\Attendance;
use App\Models\AttendanceTransaction;
use App\Models\DailyReport;
use App\Models\DailyReportBabyDetail;
use App\Models\DailyReportChildrenDetail;
use App\Models\Material;
use App\Models\MmdstAssessment;
use App\Models\MmdstParameter;
use App\Models\Student;
use App\Models\SubTheme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Service;

class DailyReportController extends Controller
{

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
            ->paginate(10);

        $services = Service::all();

        return view('admin.daily-report.index-daily.index', compact('activityTransactions', 'services'));
    }

    // public function createDailyReport($activityTransactionId)
    // {
    //     $activityTransaction = ActivityTransaction::with(['service', 'student'])->findOrFail($activityTransactionId);

    //     $period = now()->toDateString();
    //     $selectedMonth = now()->format('m');
    //     $selectedDay   = now()->format('d');

    //     $subthemes = SubTheme::with(['material', 'theme'])
    //         ->whereMonth('sub_theme_start', '<=', $selectedMonth)
    //         ->whereMonth('sub_theme_end', '>=', $selectedMonth)
    //         ->whereDay('sub_theme_start', '<=', $selectedDay)
    //         ->whereDay('sub_theme_end', '>=', $selectedDay)
    //         ->get();

    //     return view('admin.daily-report.create-daily.index', compact('activityTransaction', 'subthemes'));
    // }


    /** Konstanta id service */
    private const SERVICE_BABY     = 1; // Baby Childhood
    private const SERVICE_CHILDREN = 2; // Children Daycare

    /* ==========================
     *  FORM CREATE (DATA AWAL)
     * ==========================*/
    public function create($activityTransactionId, Request $request)
    {
        $activityTransaction = ActivityTransaction::with(['student', 'service'])->findOrFail($activityTransactionId);

        // Tanggal periode default = hari ini, bisa override via ?period=YYYY-MM-DD
        $period = $request->query('period', now()->toDateString());

        // Dropdown subtheme/material utk CHILDREN (berdasar tanggal)
        $subthemes = $this->subthemesForDate($period);

        // Stimulasi otomatis awal (readonly di view; saat store juga dihitung ulang)
        $autoStimulation = $this->buildStimulationForStudent($activityTransaction->student, $period);

        return view('admin.daily-report.create-daily.index', [
            'activityTransaction' => $activityTransaction,
            'subthemes'           => $subthemes,
            'period'              => $period,
            'autoStimulation'     => $autoStimulation,
        ]);
    }

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

    /* ==========================
     *  AJAX: CHECK ATTENDANCE
     * ==========================*/
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

    /* ===============
     *  STORE (SIMPAN)
     * ===============*/
    public function store(Request $request)
    {
        // Validasi umum
        $baseRules = [
            'activity_transaction_id' => ['required', 'exists:activity_transactions,id'],
            'period'                  => ['required', 'date'],
            'body_temperature'        => ['nullable', 'numeric'],
            'breakfast'               => ['nullable', Rule::in(['sudah', 'belum'])],
            'health_status'           => ['nullable', Rule::in(['sehat', 'sakit'])],
            'sickness_description'    => ['nullable', 'string'],
            'medication_status'       => ['nullable', Rule::in(['disertai obat', 'tanpa obat'])],
            'arrival_time'            => ['nullable', 'date_format:H:i'],
            'departure_time'          => ['nullable', 'date_format:H:i'],
            'condition'               => ['nullable', Rule::in(['tenang', 'rewel', 'temper tantrum'])],
            'notes'                   => ['nullable', 'string'],
        ];

        $activityTransaction = ActivityTransaction::with(['student', 'service'])
            ->findOrFail($request->input('activity_transaction_id'));

        // Validasi detail per service
        if ((int) $activityTransaction->service_id === self::SERVICE_BABY) {
            // BABY
            $detailRules = [
                'asi_formula_items'     => ['nullable', 'json'],
                'mpasi_items'           => ['nullable', 'json'],
                'infant_breakfast_text' => ['nullable', 'string', 'max:255'],
                'infant_lunch_text'     => ['nullable', 'string', 'max:255'],
                'infant_dinner_text'    => ['nullable', 'string', 'max:255'],
                'naps'                  => ['nullable', 'json'],
                'diapers'               => ['nullable', 'json'],
            ];
        } else {
            // CHILDREN
            $detailRules = [
                'greeting_and_morning_prayer'        => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
                'session1_material_id'               => ['nullable', 'exists:materials,id'],
                'session1_description'               => ['nullable', 'exists:materials,id'],
                'session1_activity'                  => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
                'toilet_training_and_duha_prayer'    => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
                'session2_material_id'               => ['nullable', 'exists:materials,id'],
                'session2_description'               => ['nullable', 'exists:materials,id'],
                'session2_activity'                  => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
                'morning_snack'                      => ['nullable', Rule::in(['habis', 'tidak habis'])],
                'neatness_and_independence'          => ['nullable', Rule::in(['mandiri', 'kurang mandiri', 'tidak mandiri'])],
                'cheerful_lunch'                     => ['nullable', Rule::in(['habis', 'sisa sedikit', 'sisa banyak'])],
                'cleanliness_and_brushing_training'  => ['nullable', Rule::in(['kurang', 'cukup', 'baik'])],
                'dhuhr_prayer'                       => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
                'healthy_sleep'                      => ['nullable', Rule::in(['tidur', 'tidur sebentar', 'tidak tidur'])],
                'afternoon_bath'                     => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
                'afternoon_snack'                    => ['nullable', Rule::in(['habis', 'tidak habis'])],
                'asr_prayer'                         => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
                'extra_stimulation_description'      => ['nullable', 'string'],
                'extra_stimulation'                  => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
                'cheerful_play_description'          => ['nullable', 'string'],
                'cheerful_play'                      => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
            ];
        }

        $validated = $request->validate($baseRules + $detailRules);

        // Stimulasi otomatis (SERVER-SIDE override)
        $period          = $validated['period'];
        $autoStimulation = $this->buildStimulationForStudent($activityTransaction->student, $period);

        // Simpan DAILY_REPORT (kolom umum)
        $dailyReport = new DailyReport();
        $dailyReport->activity_transaction_id = $validated['activity_transaction_id'];
        $dailyReport->period                  = $period;
        $dailyReport->body_temperature        = $validated['body_temperature'] ?? null;
        $dailyReport->breakfast               = $validated['breakfast'] ?? null;
        $dailyReport->health_status           = $validated['health_status'] ?? null;
        $dailyReport->sickness_description    = $validated['sickness_description'] ?? null;
        $dailyReport->medication_status       = $validated['medication_status'] ?? null;
        $dailyReport->arrival_time            = $validated['arrival_time'] ?? null;
        $dailyReport->departure_time          = $validated['departure_time'] ?? null;
        $dailyReport->condition               = $validated['condition'] ?? null;
        $dailyReport->stimulation_description = $autoStimulation; // selalu auto
        $dailyReport->notes                   = $validated['notes'] ?? null;
        $dailyReport->save();

        // Simpan DETAIL per service
        if ((int) $activityTransaction->service_id === self::SERVICE_BABY) {
            // BABY
            $detail = new DailyReportBabyDetail();
            $detail->daily_report_id      = $dailyReport->id;
            $detail->asi_formula_items     = $this->safeJson($request->input('asi_formula_items'));
            $detail->mpasi_items           = $this->safeJson($request->input('mpasi_items'));
            $detail->infant_breakfast_text = $validated['infant_breakfast_text'] ?? null;
            $detail->infant_lunch_text     = $validated['infant_lunch_text'] ?? null;
            $detail->infant_dinner_text    = $validated['infant_dinner_text'] ?? null;
            $detail->naps                  = $this->safeJson($request->input('naps'));
            $detail->diapers               = $this->safeJson($request->input('diapers'));
            $detail->save();
        } else {
            // CHILDREN
            $children = new DailyReportChildrenDetail();
            $children->daily_report_id                    = $dailyReport->id;
            $children->greeting_and_morning_prayer        = $validated['greeting_and_morning_prayer'] ?? null;

            // mapping session 1/2 (boleh kirim session*_material_id atau session*_description)
            $children->session1_material_id               = $validated['session1_material_id']
                ?? $validated['session1_description'] ?? null;
            $children->session1_activity                  = $validated['session1_activity'] ?? null;

            $children->toilet_training_and_duha_prayer    = $validated['toilet_training_and_duha_prayer'] ?? null;

            $children->session2_material_id               = $validated['session2_material_id']
                ?? $validated['session2_description'] ?? null;
            $children->session2_activity                  = $validated['session2_activity'] ?? null;

            $children->morning_snack                      = $validated['morning_snack'] ?? null;
            $children->neatness_and_independence          = $validated['neatness_and_independence'] ?? null;
            $children->cheerful_lunch                     = $validated['cheerful_lunch'] ?? null;
            $children->cleanliness_and_brushing_training  = $validated['cleanliness_and_brushing_training'] ?? null;
            $children->dhuhr_prayer                       = $validated['dhuhr_prayer'] ?? null;
            $children->healthy_sleep                      = $validated['healthy_sleep'] ?? null;
            $children->afternoon_bath                     = $validated['afternoon_bath'] ?? null;
            $children->afternoon_snack                    = $validated['afternoon_snack'] ?? null;
            $children->asr_prayer                         = $validated['asr_prayer'] ?? null;

            $children->extra_stimulation_description      = $validated['extra_stimulation_description'] ?? null;
            $children->extra_stimulation                  = $validated['extra_stimulation'] ?? null;
            $children->cheerful_play_description          = $validated['cheerful_play_description'] ?? null;
            $children->cheerful_play                      = $validated['cheerful_play'] ?? null;
            $children->save();
        }

        return redirect()
            ->route('daily-report.show', $dailyReport->id)
            ->with('success', 'Laporan Harian Berhasil Disimpan.');
    }

    /* ==========================
     *  SHOW / HISTORY (opsional)
     * ==========================*/
    public function show($id)
    {
        $report = DailyReport::with([
            'activityTransaction.student',
            'childrenDetail.session1Material',
            'childrenDetail.session2Material',
            'babyDetail',
        ])->findOrFail($id);

        return view('admin.daily-report.show-daily.index', [
            'dailyReport' => $report,
        ]);
    }

    public function historyDailyReport($activityTransactionId, Request $request)
    {
        $q = DailyReport::where('activity_transaction_id', $activityTransactionId);
        if ($request->filled('start_date')) $q->where('period', '>=', $request->start_date);
        if ($request->filled('end_date'))   $q->where('period', '<=', $request->end_date);

        $dailyReports = $q->orderBy('period', 'desc')->paginate(10);

        return view('admin.daily-report.history-daily.index', [
            'dailyReports' => $dailyReports,
            'id'           => $activityTransactionId,
        ]);
    }

    /* =========================================
     *  ============  UTIL & DOMAIN  ============
     * =========================================*/
    protected function buildStimulationForStudent(Student $student, ?string $periodDate = null): string
    {
        $assessment = MmdstAssessment::with(['items', 'items.parameter.stimulationCategory'])
            ->where('student_id', $student->id)
            ->latest('assessment_date')
            ->first();

        if (!$assessment) {
            return "Belum ada data penilaian MMDST untuk siswa ini.";
        }

        // Hitung usia (hari) berdasar period jika bisa
        $ageDays = null;
        if ($periodDate && $student->birth_date) {
            $ageDays = Carbon::parse($student->birth_date)->diffInDays(Carbon::parse($periodDate));
        }
        if (!$ageDays) {
            $ageDays = (int) ($assessment->age_in_days ?? 0);
        }

        // Map hasil yang sudah dites
        $testedMap = $assessment->items->keyBy('mmdst_parameter_id')->map(fn($it) => [
            'result_code' => $it->result_code,
            'passed'      => $it->result_code === 'P',
        ]);

        // Ambil semua parameter aktif
        $params = MmdstParameter::with('stimulationCategory')
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
                'percent_100'
            ]);

        // Pilih parameter relevan (IN_WINDOW/AT_LINE/OVERDUE) dan belum lulus
        $chosen = [];
        foreach ($params as $p) {
            $bucket = $this->classifyAgeBucket(
                $ageDays,
                (int) ($p->percent_25 ?? 0),
                (int) ($p->percent_50 ?? 0),
                (int) ($p->percent_75 ?? 0),
                (int) ($p->percent_100 ?? 0),
            );
            $it     = $testedMap->get($p->id);
            $passed = (bool) ($it['passed'] ?? false);

            if (in_array($bucket, ['IN_WINDOW', 'AT_LINE', 'OVERDUE'], true) && !$passed) {
                $chosen[] = [
                    'cat'     => optional($p->stimulationCategory)->category_parameter_name ?? 'Lainnya',
                    'name'    => $p->test_element_name,
                    'desc'    => $p->test_element_description,
                    'bucket'  => $bucket,
                ];
            }
        }

        if (empty($chosen)) {
            return "Tidak ada fokus stimulasi prioritas untuk rentang usia saat ini (semua item utama sudah lulus).";
        }

        // Kelompokkan per kategori, susun teks
        $grouped = collect($chosen)->groupBy('cat');

        $lines = [];
        $lines[] = "Fokus Stimulasi Otomatis (berdasarkan MMDST terbaru):";
        foreach ($grouped as $cat => $items) {
            $lines[] = "- {$cat}:";
            foreach ($items as $x) {
                $bucketId = match ($x['bucket']) {
                    'AT_LINE'   => 'Di Garis Usia',
                    'IN_WINDOW' => 'Rentang Usia',
                    'OVERDUE'   => 'Lewat Usia',
                    default     => $x['bucket'],
                };
                $desc = trim((string) $x['desc']);
                $descText = $desc !== '' ? " {$desc}" : '';
                $lines[] = "  • {$x['name']} — {$bucketId}.{$descText}";
            }
        }
        $lines[] = '';
        $lines[] = 'Catatan: daftar ini dibuat otomatis agar pendidik dapat memberi stimulasi sesuai prioritas usia & capaian.';

        return implode("\n", $lines);
    }

    /** Klasifikasi bucket usia sederhana */
    protected function classifyAgeBucket(int $ageDays, int $p25, int $p50, int $p75, int $p100): string
    {
        $p25 = max(0, $p25);
        $p50 = max(0, $p50);
        $p75 = max(0, $p75);
        $p100 = max(0, $p100);

        if ($p100 > 0 && $ageDays >= $p100) return 'OVERDUE';
        if ($p50 > 0 && abs($ageDays - $p50) <= 7) return 'AT_LINE';
        if ($p25 > 0 && $p75 > 0 && $ageDays >= $p25 && $ageDays <= $p75) return 'IN_WINDOW';
        if ($p25 > 0 && $ageDays < $p25) return 'NOT_YET';
        return 'NOT_YET';
    }

    /** Decode JSON aman → array; invalid => null */
    protected function safeJson(?string $json): ?array
    {
        if ($json === null || $json === '') return null;
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            return is_array($data) ? $data : null;
        } catch (\Throwable $e) {
            Log::warning('[safeJson] JSON invalid: ' . substr($json, 0, 200));
            return null;
        }
    }
}
