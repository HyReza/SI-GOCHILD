<?php

namespace App\Services;

use App\Models\MmdstAssessment;
use App\Models\MmdstAssessmentItem;
use App\Models\MmdstAssessmentSectorSummary;
use App\Models\MmdstParameter;
use Illuminate\Support\Collection;

class MmdstScoringService
{
    /**
     * Hitung ulang status assessment berdasarkan aturan MMDST Revisi.
     */
    public function recalc(MmdstAssessment $assessment): void
    {
        // Load relasi yang dibutuhkan
        $assessment->load(['items.parameter', 'sectorSummaries']);

        $age = (int) $assessment->age_in_days;

        // =================================================================
        // 1. UPDATE STATUS PER ITEM
        // =================================================================
        $items = $assessment->items;

        foreach ($items as $item) {
            $param = $item->parameter;
            if (!$param) continue;

            $p25  = (int) ($param->percent_25 ?? 0);
            $p75  = (int) ($param->percent_75 ?? 0);
            $p100 = (int) ($param->percent_100 ?? PHP_INT_MAX);

            // Is Age Line: Usia anak berada di dalam rentang grafik batang (P25 s/d P100)
            $isAgeLine = ($age >= $p25 && $age <= $p100);

            // Is Delay (Signifikan): Gagal (F) pada saat usia anak sudah >= P75
            $isDelay = ($item->result_code === 'F') && ($age >= $p75);

            // Simpan perubahan ke database jika ada perbedaan
            if ($item->is_age_line !== $isAgeLine || $item->is_delay !== $isDelay) {
                $item->is_age_line = $isAgeLine;
                $item->is_delay    = $isDelay;
                $item->save();
            }
        }

        // =================================================================
        // 2. HITUNG DATA RINGKASAN PER SEKTOR (MEMORI)
        // =================================================================
        // Kita hitung dulu di memori (Collection) sebelum disimpan ke DB
        // agar kita bisa menghitung Overall Result terlebih dahulu.

        $bySector = $items->groupBy('stimulation_category_id');
        $summariesData = collect();

        foreach ($bySector as $catId => $group) {
            $totalItems = $group->count();

            // Hitung jumlah Delay (F >= P75)
            $delaysCount = $group->filter(function ($it) use ($age) {
                return $it->result_code === 'F' && $age >= (int)($it->parameter->percent_75 ?? 0);
            })->count();

            // Hitung Refusal Signifikan (R >= P75)
            $refusalsSigCount = $group->filter(function ($it) use ($age) {
                return $it->result_code === 'R' && $age >= (int)($it->parameter->percent_75 ?? 0);
            })->count();

            // Hitung Lulus di Garis Usia
            $passAtAgeLineCount = $group->filter(function ($it) use ($age) {
                $p25 = (int)($it->parameter->percent_25 ?? 0);
                $p100 = (int)($it->parameter->percent_100 ?? PHP_INT_MAX);
                return $it->result_code === 'P' && ($age >= $p25 && $age <= $p100);
            })->count();

            // Masukkan ke collection sementara
            $summariesData->push([
                'category_id'            => $catId,
                'total_items'            => $totalItems,
                'delays_count'           => $delaysCount,
                'refusals_count'         => $refusalsSigCount,
                'pass_at_age_line_count' => $passAtAgeLineCount,
                // Nama field untuk helper decideOverallResult
                'delays'      => $delaysCount,
                'refusalsSig' => $refusalsSigCount,
                'passAtAge'   => $passAtAgeLineCount,
            ]);
        }

        // =================================================================
        // 3. HITUNG HASIL KESELURUHAN (OVERALL RESULT)
        // =================================================================
        $overallResult = $this->decideOverallResult($summariesData);

        // =================================================================
        // 4. SIMPAN RINGKASAN SEKTOR KE DATABASE
        // =================================================================
        // Sekarang kita simpan ke DB, dengan 'sector_result' mengikuti $overallResult
        foreach ($summariesData as $data) {
            MmdstAssessmentSectorSummary::updateOrCreate(
                ['assessment_id' => $assessment->id, 'stimulation_category_id' => $data['category_id']],
                [
                    'total_items'            => $data['total_items'],
                    'delays_count'           => $data['delays_count'],
                    'refusals_count'         => $data['refusals_count'],
                    'pass_at_age_line_count' => $data['pass_at_age_line_count'],

                    // PERBAIKAN: Isi sector_result dengan hasil Overall (NORMAL/ABNORMAL/dll)
                    'sector_result'          => $overallResult
                ]
            );
        }

        // =================================================================
        // 5. UPDATE ASSESSMENT HEADERS
        // =================================================================
        $assessment->overall_result = $overallResult;

        $assessment->counters = [
            'total_items'            => $items->count(),
            'total_delay_critical'   => $items->where('is_delay', true)->count(),
            'total_refusal_critical' => $items->filter(fn($it) => $it->result_code === 'R' && $age >= (int)($it->parameter->percent_75 ?? 0))->count(),
        ];

        $assessment->save();
    }

    /**
     * Logika Penentuan Hasil Diagnosis MMDST.
     */
    protected function decideOverallResult(Collection $sectors): string
    {
        // Sektor "Berat": Memiliki >= 2 delays
        $sectorsWith2Delays = $sectors->filter(fn($s) => $s['delays'] >= 2);

        // Sektor "Ringan Tanda Tanya": Memiliki 1 delay DAN tidak ada item lulus di garis usia
        $sectorsWith1DelayNoPass = $sectors->filter(fn($s) => $s['delays'] == 1 && $s['passAtAge'] == 0);

        // --- CEK 1: ABNORMAL ---
        if ($sectorsWith2Delays->count() >= 2) {
            return 'ABNORMAL';
        }
        if ($sectorsWith2Delays->count() == 1 && $sectorsWith1DelayNoPass->count() >= 1) {
            return 'ABNORMAL';
        }

        // --- CEK 2: QUESTIONABLE ---
        if ($sectorsWith2Delays->count() == 1) {
            return 'QUESTIONABLE';
        }
        if ($sectorsWith1DelayNoPass->count() >= 1) {
            return 'QUESTIONABLE';
        }

        // --- CEK 3: UNTESTABLE ---
        // Simulasikan jika refusal dianggap sebagai failure
        $totalRefusalsSig = $sectors->sum('refusalsSig');

        if ($totalRefusalsSig > 0) {
            $simulatedSectors = $sectors->map(function ($s) {
                return [
                    'delays'      => $s['delays'] + $s['refusalsSig'], // Anggap R sebagai F
                    'passAtAge'   => $s['passAtAge'],
                    'refusalsSig' => 0
                ];
            });

            $simulatedResult = $this->calculateResultState($simulatedSectors);

            // Jika simulasi menghasilkan BURUK, maka aslinya UNTESTABLE
            if (in_array($simulatedResult, ['ABNORMAL', 'QUESTIONABLE'])) {
                return 'UNTESTABLE';
            }
        }

        return 'NORMAL';
    }

    /**
     * Helper murni untuk menghitung status (ABNORMAL/QUESTIONABLE/NORMAL)
     */
    private function calculateResultState(Collection $sectors): string
    {
        $sectorsWith2Delays = $sectors->filter(fn($s) => $s['delays'] >= 2);
        $sectorsWith1DelayNoPass = $sectors->filter(fn($s) => $s['delays'] == 1 && $s['passAtAge'] == 0);

        if ($sectorsWith2Delays->count() >= 2) return 'ABNORMAL';
        if ($sectorsWith2Delays->count() == 1 && $sectorsWith1DelayNoPass->count() >= 1) return 'ABNORMAL';

        if ($sectorsWith2Delays->count() == 1) return 'QUESTIONABLE';
        if ($sectorsWith1DelayNoPass->count() >= 1) return 'QUESTIONABLE';

        return 'NORMAL';
    }
}
