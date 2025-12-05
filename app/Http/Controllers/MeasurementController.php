<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Service;
use App\Models\Student;
use App\Models\Measurement;
use Illuminate\Http\Request;
use App\Models\GrowthStandard;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMeasurementRequest;
use App\Http\Requests\UpdateMeasurementRequest;

class MeasurementController extends Controller
{

    public function index()
    {
        // Ambil data transaksi aktivitas yang terhubung dengan siswa dan layanan
        $activityTransactions = ActivityTransaction::with('student', 'service')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Ambil semua layanan untuk dropdown filter
        $services = Service::orderBy('service_name')->get();

        return view('admin.measurement.measurement-index.index', compact('activityTransactions', 'services'));
    }

    /**
     * Menangani permintaan pencarian dan filter secara AJAX.
     */
    public function search(Request $request)
    {
        $q = $request->input('q');
        $service_id = $request->input('service_id');

        $query = ActivityTransaction::with('student', 'service')
            ->whereHas('student', function ($studentQuery) use ($q) {
                $studentQuery->where('student_name', 'like', "%{$q}%")
                    ->orWhere('student_number', 'like', "%{$q}%")
                    ->orWhere('mother_name', 'like', "%{$q}%");
            });

        if ($service_id) {
            $query->where('service_id', $service_id);
        }

        $activityTransactions = $query->orderBy('created_at', 'desc')->get();

        // Mengembalikan data dalam format JSON
        return response()->json(['data' => $activityTransactions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create($id)
    // {
    //     // Ambil activity transaction berdasarkan ID
    //     $activityTransaction = ActivityTransaction::with('student')->findOrFail($id);

    //     // Ambil semua layanan untuk filter jika diperlukan
    //     $services = Service::all();

    //     return view('admin.measurement.measurement-create.index', compact('activityTransaction', 'services'));
    // }

    public function create($id)
    {
        // Ambil activity transaction berdasarkan ID
        $activityTransaction = ActivityTransaction::with('student')->findOrFail($id);

        // Ambil semua layanan untuk filter jika diperlukan
        $services = Service::all();

        return view('admin.measurement.measurement-create.index', compact('activityTransaction', 'services'));
    }


    // Fungsi untuk menyimpan data pengukuran
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'activity_transaction_id' => 'required|exists:activity_transactions,id',
            'date_measurement' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'head_circumference' => 'required|numeric|min:0',
            'arm_circumference' => 'required|numeric|min:0',
            'measurement_condition' => 'required|string|in:berdiri,terlentang',
            'note_measurement' => 'nullable|string',
            'sd_category' => 'required|json',
            'calculation_results' => 'required|json',
            'measurement_results' => 'required|json',
        ]);

        Measurement::create([
            'user_id' => Auth::id(),
            'activity_transaction_id' => $validatedData['activity_transaction_id'],
            'date_measurement' => $validatedData['date_measurement'],
            'weight' => $validatedData['weight'],
            'height' => $validatedData['height'],
            'head_circumference' => $validatedData['head_circumference'],
            'arm_circumference' => $validatedData['arm_circumference'],
            'measurement_condition' => $validatedData['measurement_condition'],
            'note_measurement' => $validatedData['note_measurement'],
            // PERBAIKAN: Hapus json_decode(). Data sudah dalam format JSON string.
            'sd_category' => $validatedData['sd_category'],
            'calculation_results' => $validatedData['calculation_results'],
            'measurement_results' => $validatedData['measurement_results'],
        ]);

        return redirect()->route('measurement.index')->with('success', 'Data pengukuran berhasil disimpan.');
    }

    // Fungsi untuk mengambil data growth standard berdasarkan parameter dan umur
    public function getGrowthStandard(Request $request)
    {
        $request->validate([
            'gender' => 'required|',
            'parameter' => 'required|in:BB/U,TB/U,IMT/U,BB/PB,BB/TB',
            'age_months' => 'required|numeric',
        ]);

        $gender = $request->input('gender') === 'male' ? 1 : 0;

        // Ambil data growth standard berdasarkan parameter, gender, dan umur
        $growthStandard = GrowthStandard::where('gender', $gender)
            ->where('parameter', $request->parameter)
            ->where('age_months', $request->age_months)
            ->first();

        return response()->json($growthStandard);
    }


    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     $measurement = Measurement::with('activityTransaction.student')->findOrFail($id);

    //     return view('admin.measurement.measurement-show.index', compact('measurement'));
    // }
    public function show(Measurement $measurement)
    {
        // Eager load relasi yang dibutuhkan untuk ditampilkan di view
        $measurement->load('activityTransaction.student', 'user');

        return view('admin.measurement.measurement-show.index', compact('measurement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($id)
    // {
    //     $measurement = Measurement::with('activityTransaction.student')->findOrFail($id);
    //     return view('admin.measurement.measurement-edit.index', compact('measurement'));
    // }
    public function edit(Measurement $measurement)
    {
        // Gunakan $measurement->activityTransaction untuk mendapatkan data siswa
        // Kita tidak perlu me-load relasi karena sudah otomatis terhubung
        $activityTransaction = $measurement->activityTransaction;

        return view('admin.measurement.measurement-edit.index', compact('measurement', 'activityTransaction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Measurement $measurement)
    {
        // Validasi sama seperti method store
        $validatedData = $request->validate([
            'date_measurement' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'head_circumference' => 'required|numeric|min:0',
            'arm_circumference' => 'required|numeric|min:0',
            'measurement_condition' => 'required|string|in:berdiri,terlentang',
            'note_measurement' => 'nullable|string',
            'sd_category' => 'required|json',
            'calculation_results' => 'required|json',
            'measurement_results' => 'required|json',
        ]);

        // Update data
        $measurement->update([
            'date_measurement' => $validatedData['date_measurement'],
            'weight' => $validatedData['weight'],
            'height' => $validatedData['height'],
            'head_circumference' => $validatedData['head_circumference'],
            'arm_circumference' => $validatedData['arm_circumference'],
            'measurement_condition' => $validatedData['measurement_condition'],
            'note_measurement' => $validatedData['note_measurement'],
            'sd_category' => $validatedData['sd_category'],
            'calculation_results' => $validatedData['calculation_results'],
            'measurement_results' => $validatedData['measurement_results'],
        ]);

        // Redirect ke halaman detail dengan pesan sukses
        return redirect()->route('measurement.show', $measurement)->with('success', 'Data pengukuran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $measurement = Measurement::findOrFail($id);
            $activityTransactionId = $measurement->activity_transaction_id;

            $measurement->delete();

            return redirect()
                ->route('measurement.history', $activityTransactionId)
                ->with('success', 'Data pengukuran berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function historyMeasurement($id, Request $request)
    {
        $query = Measurement::where('activity_transaction_id', $id);

        if ($request->filled('start_date')) {
            $query->where('date_measurement', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date_measurement', '<=', $request->end_date);
        }

        $measurements = $query->orderByDesc('date_measurement')->get();
        $activityTransaction = ActivityTransaction::with('student')->findOrFail($id);

        return view('admin.measurement.measurement-history.index', compact('measurements', 'activityTransaction'));
    }


    /**
     * API Endpoint untuk mengambil data GrowthStandard.
     * Versi final dengan query yang disempurnakan.
     */

    public function getGrowthStandards(Request $request)
    {
        $validated = $request->validate([
            'gender' => 'required|string|in:male,female',
            'age_months' => 'required|integer|min:0|max:220',
            'height' => 'required|numeric|min:0',
            'measurement_condition' => 'required|string|in:berdiri,terlentang',
        ]);

        $gender = $validated['gender'];
        $ageMonths = $validated['age_months'];
        $height = $validated['height'];
        $condition = $validated['measurement_condition'];
        $finalResults = collect();

        // --- 1. Ambil Parameter berdasarkan Umur ---
        $heightAgeParam = $condition === 'berdiri' ? 'TB/U' : 'PB/U';
        $ageBasedParams = ['BB/U', $heightAgeParam, 'IMT/U'];
        $ageBasedStandards = GrowthStandard::where('gender', $gender)->where('reference_type', 'age')->where('age_months', $ageMonths)->whereIn('parameter', $ageBasedParams)->where('is_active', true)->get()->keyBy('parameter');
        foreach ($ageBasedParams as $param) {
            if (!$ageBasedStandards->has($param)) {
                $closestStandard = GrowthStandard::where('gender', 'like', $gender)->where('reference_type', 'age')->where('parameter', $param)->where('is_active', true)->orderByRaw('ABS(age_months - ?)', [$ageMonths])->first();
                if ($closestStandard) {
                    $ageBasedStandards->put($param, $closestStandard);
                }
            }
        }
        $finalResults = $finalResults->merge($ageBasedStandards);

        // --- 2. Ambil Parameter berdasarkan Tinggi/Panjang Badan ---

        // PERBAIKAN KUNCI: Sesuaikan nama parameter agar cocok dengan data di tabel Anda
        $weightHeightParam = $condition === 'berdiri' ? 'TB/BB' : 'PB/BB'; // <-- Dari BB/TB menjadi TB/BB, dari BB/PB menjadi PB/BB

        $heightReferenceType = $condition === 'berdiri' ? 'height' : 'length';
        $heightColumnName = $condition === 'berdiri' ? 'body_height' : 'body_length';

        $heightBasedStandard = GrowthStandard::query()
            ->where('gender', $gender)
            ->where('reference_type', $heightReferenceType)
            ->where('parameter', $weightHeightParam)
            ->whereNotNull($heightColumnName)
            ->where('is_active', true)
            ->orderByRaw('ABS(' . $heightColumnName . ' - ?)', [$height])
            ->first();

        // Penting: Masukkan kembali ke hasil dengan nama yang diharapkan oleh JavaScript
        $jsExpectedParam = $condition === 'berdiri' ? 'TB/BB' : 'PB/BB';
        if ($heightBasedStandard) {
            $finalResults->put($jsExpectedParam, $heightBasedStandard);
        }

        return response()->json($finalResults);
    }


    /**
     * Menampilkan halaman khusus untuk grafik KMS.
     */
    // app/Http/Controllers/MeasurementController.php


    public function showKmsChart(ActivityTransaction $activityTransaction)
    {
        $student = $activityTransaction->student;
        if (!$student) {
            abort(404, 'Data siswa untuk transaksi ini tidak ditemukan.');
        }

        $measurements = Measurement::where('activity_transaction_id', $activityTransaction->id)
            ->orderBy('date_measurement', 'asc')
            ->get();

        // Menyiapkan data pengukuran anak, termasuk data status gizi untuk tooltip
        // Menyiapkan data pengukuran anak, termasuk data status gizi untuk tooltip
        $chartData = $measurements->map(function ($measurement) use ($student) {
            $ageInMonths = Carbon::parse($student->birth_date)->diffInMonths($measurement->date_measurement);
            $bmi = ($measurement->height > 0) ? ($measurement->weight / pow($measurement->height / 100, 2)) : 0;

            $getArray = function ($data) {
                if (is_array($data)) return $data;
                if (is_string($data)) return json_decode($data, true) ?: [];
                return [];
            };

            return [
                'age' => $ageInMonths,
                'weight' => $measurement->weight,
                'height' => $measurement->height,
                'bmi' => round($bmi, 2),
                'date' => Carbon::parse($measurement->date_measurement)->format('Y-m-d'),
                'condition' => $measurement->measurement_condition,
                'sd_category' => $getArray($measurement->sd_category),
                'status_gizi' => $getArray($measurement->calculation_results),
            ];
        });

        $gender = ($student->gender == 1 || $student->gender == 'male') ? 'male' : 'female';

        // Mengambil semua data standar yang relevan
        $allStandardsRaw = collect();
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'BB/U')->whereBetween('age_months', [0, 60])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'IMT/U')->whereBetween('age_months', [0, 60])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'PB/U')->whereBetween('age_months', [0, 24])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'TB/U')->whereBetween('age_months', [24, 60])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'PB/BB')->whereBetween('body_length', [45, 110])->get());
        $allStandardsRaw = $allStandardsRaw->merge(GrowthStandard::where('gender', $gender)->where('parameter', 'TB/BB')->whereBetween('body_height', [65, 120])->get());

        // Menyusun ulang semua data standar, memastikan semua kolom SD diambil
        $allStandardCurves = [];
        $possibleParams = ['BB/U', 'PB/U', 'TB/U', 'IMT/U', 'PB/BB', 'TB/BB'];
        foreach ($possibleParams as $param) {
            $paramData = $allStandardsRaw->where('parameter', $param);
            if ($paramData->isNotEmpty()) {
                $x_axis_key = 'age_months';
                if ($param === 'PB/BB') $x_axis_key = 'body_length';
                if ($param === 'TB/BB') $x_axis_key = 'body_height';

                $x_values = $paramData->pluck($x_axis_key)->filter()->map(fn($val) => (float)$val);

                if ($x_values->isNotEmpty()) {
                    $allStandardCurves[$param] = [
                        'x_axis_key' => $x_axis_key,
                        'min' => $x_values->min(),
                        'max' => $x_values->max(),
                        'median' => $paramData->pluck('median', $x_axis_key)->all(),
                        'plus_1_sd' => $paramData->pluck('plus_1_sd', $x_axis_key)->all(),
                        'plus_2_sd' => $paramData->pluck('plus_2_sd', $x_axis_key)->all(),
                        'plus_3_sd' => $paramData->pluck('plus_3_sd', $x_axis_key)->all(),
                        'minus_1_sd' => $paramData->pluck('minus_1_sd', $x_axis_key)->all(),
                        'minus_2_sd' => $paramData->pluck('minus_2_sd', $x_axis_key)->all(),
                        'minus_3_sd' => $paramData->pluck('minus_3_sd', $x_axis_key)->all(),
                    ];
                }
            }
        }
        // Ganti nama view Anda jika berbeda
        return view('admin.measurement.measurement-chart.index', compact('activityTransaction', 'student', 'chartData', 'allStandardCurves'));
    }
}
