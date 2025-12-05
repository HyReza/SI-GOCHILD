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
     * Hitung flag item (is_age_line, is_delay), ringkasan per sektor, dan overall_result.
     */
    public function recalc(MmdstAssessment $assessment): void
    {
        $assessment->load(['items.parameter', 'sectorSummaries']);

        // 1) Update is_age_line & is_delay tiap item
        /** @var \Illuminate\Support\Collection<int,MmdstAssessmentItem> $items */
        $items = $assessment->items;

        foreach ($items as $item) {
            /** @var MmdstParameter $param */
            $param = $item->parameter;

            $isAgeLine = $assessment->age_in_days >= (int) ($param->percent_25 ?? 0);
            $isDelay   = $item->result_code === 'F'
                && $assessment->age_in_days >= (int) ($param->percent_100 ?? PHP_INT_MAX);

            if ($item->is_age_line !== $isAgeLine || $item->is_delay !== $isDelay) {
                $item->is_age_line = $isAgeLine;
                $item->is_delay    = $isDelay;
                $item->save();
            }
        }

        // 2) Ringkasan per sektor
        $bySector  = $items->groupBy('stimulation_category_id');
        $summaries = [];

        foreach ($bySector as $catId => $group) {
            $total     = $group->count();
            $delays    = $group->where('is_delay', true)->count();
            $refusals  = $group->where('result_code', 'R')->count();
            $passAtAge = $group->filter(fn($it) => $it->is_age_line && $it->result_code === 'P')->count();

            $sectorResult = $this->decideSectorResult($delays, $refusals);

            $summary = MmdstAssessmentSectorSummary::updateOrCreate(
                ['assessment_id' => $assessment->id, 'stimulation_category_id' => $catId],
                [
                    'total_items'             => $total,
                    'delays_count'            => $delays,
                    'refusals_count'          => $refusals,
                    'pass_at_age_line_count'  => $passAtAge,
                    'sector_result'           => $sectorResult,
                ]
            );

            $summaries[] = $summary;
        }

        // 3) Overall
        $overall = $this->decideOverallResult(collect($summaries));
        $assessment->overall_result = $overall;
        $assessment->counters = [
            'total_items'    => $items->count(),
            'total_delay'    => $items->where('is_delay', true)->count(),
            'total_refusal'  => $items->where('result_code', 'R')->count(),
            'total_pass_age' => $items->filter(fn($it) => $it->is_age_line && $it->result_code === 'P')->count(),
        ];
        $assessment->save();
    }

    /** Aturan sektor (ringkas dari panduan). */
    protected function decideSectorResult(int $delays, int $refusals): string
    {
        if ($refusals >= 2 && $delays === 0) return 'UNTESTABLE';
        if ($delays >= 2) return 'ABNORMAL';
        if ($delays === 1) return 'QUESTIONABLE';
        return 'NORMAL';
    }

    /**
     * Aturan keseluruhan (global) merujuk pada panduan:
     * - >=2 sektor dgn >=2 delay -> ABNORMAL
     * - 1 sektor >=2 delay + (>=1 sektor >=1 delay) & sektor tsb tak ada pass di garis usia -> ABNORMAL
     * - 1 sektor >=2 delay -> QUESTIONABLE
     * - >=1 sektor 1 delay & sektor tsb tak ada pass di garis usia -> QUESTIONABLE
     * - ada sektor UNTESTABLE (dan aturan lain tidak memutuskan) -> UNTESTABLE
     * - selain itu -> NORMAL
     */
    protected function decideOverallResult(Collection $sectorSummaries): string
    {
        $twoOrMoreDelaySectors = $sectorSummaries->filter(fn($s) => $s->delays_count >= 2);
        $untestableSectors     = $sectorSummaries->filter(fn($s) => $s->sector_result === 'UNTESTABLE');

        if ($twoOrMoreDelaySectors->count() >= 2) return 'ABNORMAL';

        if ($twoOrMoreDelaySectors->count() === 1) {
            $hasOtherSectorWith1DelayNoPass = $sectorSummaries
                ->filter(fn($s) => $s->delays_count >= 1 && $s->pass_at_age_line_count === 0)
                ->isNotEmpty();

            if ($hasOtherSectorWith1DelayNoPass) return 'ABNORMAL';
            return 'QUESTIONABLE';
        }

        $hasOneDelayNoPass = $sectorSummaries
            ->filter(fn($s) => $s->delays_count === 1 && $s->pass_at_age_line_count === 0)
            ->isNotEmpty();

        if ($hasOneDelayNoPass) return 'QUESTIONABLE';

        if ($untestableSectors->isNotEmpty()) return 'UNTESTABLE';

        return 'NORMAL';
    }
}
