<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SubTheme;
use App\Models\Material;
use App\Models\Attendance;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use App\Models\ActivityTransaction;
use Illuminate\Support\Facades\Log;
use App\Models\AttendanceTransaction;
use App\Models\DailyReportBabyDetail;
use App\Models\DailyReportChildrenDetail; // <-- ganti ke Children
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

    // public function checkAttendance($studentId, $date)
    // {
    //     try {
    //         $attendanceTransaction = AttendanceTransaction::whereDate('date_attendance', $date)->first();

    //         if (!$attendanceTransaction) {
    //             return response()->json(['status' => 'Siswa Belum Absen / Data Absen Belum Dibuat']);
    //         }

    //         $attendance = Attendance::where('attendances_transaction_id', $attendanceTransaction->id)
    //             ->whereHas('activityTransaction', function ($q) use ($studentId) {
    //                 $q->where('student_id', $studentId);
    //             })
    //             ->first();

    //         if (!$attendance) {
    //             return response()->json(['status' => 'Siswa Belum Absen / Data Absen Belum Dibuat']);
    //         }

    //         $statusMessage = match ($attendance->check_in_status) {
    //             'Present' => 'Siswa Sudah Absen',
    //             'Excused' => 'Siswa Izin',
    //             'Sick'    => 'Siswa Sakit',
    //             'Absent'  => 'Siswa Tidak Hadir Tanpa Keterangan',
    //             default   => 'Status Absen Tidak Dikenali',
    //         };

    //         return response()->json([
    //             'status'         => $statusMessage,
    //             'check_in_time'  => $attendance->check_in_time ?: 'Belum Check-in',
    //             'check_out_time' => $attendance->check_out_time ?: 'Belum Check-out',
    //         ]);
    //     } catch (\Throwable $e) {
    //         Log::error('checkAttendance error: ' . $e->getMessage());
    //         return response()->json(['status' => 'Server Error'], 500);
    //     }
    // }

    // public function getSubthemes($periode)
    // {
    //     $month = date('m', strtotime($periode));
    //     $day   = date('d', strtotime($periode));

    //     $subthemes = SubTheme::with(['material', 'theme'])
    //         ->whereMonth('sub_theme_start', '<=', $month)
    //         ->whereMonth('sub_theme_end', '>=', $month)
    //         ->whereDay('sub_theme_start', '<=', $day)
    //         ->whereDay('sub_theme_end', '>=', $day)
    //         ->get();

    //     $response = [
    //         'subthemes' => $subthemes->map(function ($sub) {
    //             return [
    //                 'id'                    => $sub->id,
    //                 'theme_name'            => $sub->theme->theme_name,
    //                 'theme_description'     => $sub->theme->theme_description,
    //                 'sub_theme_name'        => $sub->sub_theme_name,
    //                 'sub_theme_description' => $sub->sub_theme_description,
    //                 'material'              => $sub->material->map(fn($m) => [
    //                     'id'            => $m->id,
    //                     'material_name' => $m->material_name,
    //                     'selected'      => false,
    //                 ]),
    //             ];
    //         }),
    //     ];

    //     return response()->json($response);
    // }

    // public function storeDailyReport(Request $request)
    // {
    //     $at = ActivityTransaction::with(['service', 'student'])->findOrFail($request->input('activity_transaction_id'));
    //     $serviceId = (int) $at->service_id;

    //     // umum
    //     $commonRules = [
    //         'activity_transaction_id'   => ['required', 'exists:activity_transactions,id'],
    //         'period'                    => ['required', 'date'],
    //         'body_temperature'          => ['nullable', 'numeric'],
    //         'breakfast'                 => ['nullable', Rule::in(['sudah', 'belum'])],
    //         'health_status'             => ['nullable', Rule::in(['sehat', 'sakit'])],
    //         'sickness_description'      => ['nullable', 'string'],
    //         'medication_status'         => ['nullable', Rule::in(['disertai obat', 'tanpa obat'])],
    //         'arrival_time'              => ['nullable', 'date_format:H:i'],
    //         'departure_time'            => ['nullable', 'date_format:H:i'],
    //         'condition'                 => ['nullable', Rule::in(['tenang', 'rewel', 'temper tantrum'])],
    //         'stimulation_description'   => ['nullable', 'string'],
    //         'notes'                     => ['nullable', 'string'],
    //         'parent_guardian_signature' => ['nullable', 'string'],
    //     ];

    //     // khusus per service
    //     if ($serviceId === 1) {
    //         $specificRules = [
    //             'asi_formula_items'     => ['nullable', 'string'], // JSON string
    //             'mpasi_items'           => ['nullable', 'string'], // JSON string
    //             'infant_breakfast_text' => ['nullable', 'string'],
    //             'infant_lunch_text'     => ['nullable', 'string'],
    //             'infant_dinner_text'    => ['nullable', 'string'],
    //             'naps'                  => ['nullable', 'string'], // JSON string
    //             'diapers'               => ['nullable', 'string'], // JSON string
    //         ];
    //     } else {
    //         $specificRules = [
    //             'greeting_and_morning_prayer'        => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
    //             'session1_material_id'               => ['nullable', 'exists:materials,id'],
    //             'session1_activity'                  => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
    //             'toilet_training_and_duha_prayer'    => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
    //             'session2_material_id'               => ['nullable', 'exists:materials,id'],
    //             'session2_activity'                  => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
    //             'morning_snack'                      => ['nullable', Rule::in(['habis', 'tidak habis'])],
    //             'neatness_and_independence'          => ['nullable', Rule::in(['mandiri', 'kurang mandiri', 'tidak mandiri'])],
    //             'cheerful_lunch'                     => ['nullable', Rule::in(['habis', 'sisa sedikit', 'sisa banyak'])],
    //             'cleanliness_and_brushing_training'  => ['nullable', Rule::in(['kurang', 'cukup', 'baik'])],
    //             'dhuhr_prayer'                       => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
    //             'healthy_sleep'                      => ['nullable', Rule::in(['tidur', 'tidur sebentar', 'tidak tidur'])],
    //             'afternoon_bath'                     => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
    //             'afternoon_snack'                    => ['nullable', Rule::in(['habis', 'tidak habis'])],
    //             'asr_prayer'                         => ['nullable', Rule::in(['mengikuti', 'tidak mengikuti'])],
    //             'extra_stimulation_description'      => ['nullable', 'string'],
    //             'extra_stimulation'                  => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
    //             'cheerful_play_description'          => ['nullable', 'string'],
    //             'cheerful_play'                      => ['nullable', Rule::in(['BB', 'MB', 'BSH', 'BSB'])],
    //         ];
    //     }

    //     $data = $request->validate($commonRules + $specificRules);

    //     $base = [
    //         'activity_transaction_id'   => $data['activity_transaction_id'],
    //         'service_id'                => $serviceId,
    //         'period'                    => $data['period'],
    //         'body_temperature'          => $data['body_temperature'] ?? null,
    //         'breakfast'                 => $data['breakfast'] ?? null,
    //         'health_status'             => $data['health_status'] ?? null,
    //         'sickness_description'      => $data['sickness_description'] ?? null,
    //         'medication_status'         => $data['medication_status'] ?? null,
    //         'arrival_time'              => $data['arrival_time'] ?? null,
    //         'departure_time'            => $data['departure_time'] ?? null,
    //         'condition'                 => $data['condition'] ?? null,
    //         'stimulation_description'   => $data['stimulation_description'] ?? null,
    //         'notes'                     => $data['notes'] ?? null,
    //         'parent_guardian_signature' => $data['parent_guardian_signature'] ?? null,
    //     ];

    //     DB::beginTransaction();
    //     try {
    //         $report = DailyReport::create($base);

    //         if ($serviceId === 1) {
    //             // parse JSON
    //             $jsonKeys = ['asi_formula_items', 'mpasi_items', 'naps', 'diapers'];
    //             $payload  = [];
    //             foreach ($jsonKeys as $k) {
    //                 if (!empty($data[$k])) {
    //                     try {
    //                         $payload[$k] = json_decode($data[$k], true, 512, JSON_THROW_ON_ERROR);
    //                     } catch (\Throwable $e) {
    //                         $payload[$k] = null;
    //                     }
    //                 } else {
    //                     $payload[$k] = null;
    //                 }
    //             }

    //             DailyReportBabyDetail::create([
    //                 'daily_report_id'       => $report->id,
    //                 'asi_formula_items'     => $payload['asi_formula_items'],
    //                 'mpasi_items'           => $payload['mpasi_items'],
    //                 'infant_breakfast_text' => $data['infant_breakfast_text'] ?? null,
    //                 'infant_lunch_text'     => $data['infant_lunch_text'] ?? null,
    //                 'infant_dinner_text'    => $data['infant_dinner_text'] ?? null,
    //                 'naps'                  => $payload['naps'],
    //                 'diapers'               => $payload['diapers'],
    //             ]);
    //         } else {
    //             DailyReportChildrenDetail::create([
    //                 'daily_report_id'                     => $report->id,
    //                 'greeting_and_morning_prayer'         => $data['greeting_and_morning_prayer'] ?? null,
    //                 'session1_material_id'                => $data['session1_material_id'] ?? null,
    //                 'session1_activity'                   => $data['session1_activity'] ?? null,
    //                 'toilet_training_and_duha_prayer'     => $data['toilet_training_and_duha_prayer'] ?? null,
    //                 'session2_material_id'                => $data['session2_material_id'] ?? null,
    //                 'session2_activity'                   => $data['session2_activity'] ?? null,
    //                 'morning_snack'                       => $data['morning_snack'] ?? null,
    //                 'neatness_and_independence'           => $data['neatness_and_independence'] ?? null,
    //                 'cheerful_lunch'                      => $data['cheerful_lunch'] ?? null,
    //                 'cleanliness_and_brushing_training'   => $data['cleanliness_and_brushing_training'] ?? null,
    //                 'dhuhr_prayer'                        => $data['dhuhr_prayer'] ?? null,
    //                 'healthy_sleep'                       => $data['healthy_sleep'] ?? null,
    //                 'afternoon_bath'                      => $data['afternoon_bath'] ?? null,
    //                 'afternoon_snack'                     => $data['afternoon_snack'] ?? null,
    //                 'asr_prayer'                          => $data['asr_prayer'] ?? null,
    //                 'extra_stimulation_description'       => $data['extra_stimulation_description'] ?? null,
    //                 'extra_stimulation'                   => $data['extra_stimulation'] ?? null,
    //                 'cheerful_play_description'           => $data['cheerful_play_description'] ?? null,
    //                 'cheerful_play'                       => $data['cheerful_play'] ?? null,
    //             ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('daily-report.index')->with('success', 'Laporan Harian Berhasil Disimpan!');
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Error storeDailyReport: ' . $e->getMessage());
    //         return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan laporan!']);
    //     }
    // }

    // public function historyDailyReport($activityTransactionId, Request $request)
    // {
    //     $q = DailyReport::where('activity_transaction_id', $activityTransactionId);

    //     if ($request->filled('start_date')) $q->where('period', '>=', $request->start_date);
    //     if ($request->filled('end_date'))   $q->where('period', '<=', $request->end_date);

    //     $dailyReports = $q->orderByDesc('period')->paginate(10);

    //     return view('admin.daily-report.history-daily.index', [
    //         'dailyReports' => $dailyReports,
    //         'id'           => $activityTransactionId,
    //     ]);
    // }

    // public function show($id)
    // {
    //     $report = DailyReport::with([
    //         'activityTransaction.student',
    //         'babyDetail',
    //         'childrenDetail',                 // <-- renamed
    //         'childrenDetail.session1Material',
    //         'childrenDetail.session2Material',
    //     ])->findOrFail($id);

    //     return view('admin.daily-report.show-daily.index', [
    //         'dailyReport'      => $report,
    //         'session1Material' => optional($report->childrenDetail?->session1Material),
    //         'session2Material' => optional($report->childrenDetail?->session2Material),
    //     ]);
    // }
}
