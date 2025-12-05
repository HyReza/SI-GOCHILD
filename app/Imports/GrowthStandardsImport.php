<?php

namespace App\Imports;

use App\Models\GrowthStandard;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;

class GrowthStandardsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    use SkipsFailures, SkipsErrors;

    private int $rows = 0;

    public function model(array $row)
    {
        $this->rows++;

        // Normalisasi kosong string ke null
        $ageMonths   = $row['age_months']   ?? null;
        $bodyLength  = $row['body_length']  ?? null;
        $bodyHeight  = $row['body_height']  ?? null;
        $measCond    = $row['measurement_condition'] ?? null;

        return new GrowthStandard([
            'gender'               => strtolower(trim($row['gender'])),
            'reference_type'       => strtolower(trim($row['reference_type'])),
            'age_months'           => $ageMonths !== '' ? (int) $ageMonths : null,
            'body_length'          => $bodyLength !== '' ? (float) $bodyLength : null,
            'body_height'          => $bodyHeight !== '' ? (float) $bodyHeight : null,

            'minus_3_sd'           => (float) $row['minus_3_sd'],
            'minus_2_sd'           => (float) $row['minus_2_sd'],
            'minus_1_sd'           => (float) $row['minus_1_sd'],
            'median'               => (float) $row['median'],
            'plus_1_sd'            => (float) $row['plus_1_sd'],
            'plus_2_sd'            => (float) $row['plus_2_sd'],
            'plus_3_sd'            => (float) $row['plus_3_sd'],

            'parameter'            => strtoupper(trim($row['parameter'])),
            'measurement_condition' => $measCond !== '' ? strtolower(trim($measCond)) : null,
            'is_active'            => isset($row['is_active']) ? (bool) $row['is_active'] : true,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.gender' => ['required', Rule::in(['male', 'female'])],
            '*.reference_type' => ['required', Rule::in(['age', 'length', 'height'])],

            '*.age_months' => ['nullable', 'integer', 'min:0', 'max:216'],
            '*.body_length' => ['nullable', 'numeric', 'min:0', 'max:200'],
            '*.body_height' => ['nullable', 'numeric', 'min:0', 'max:200'],

            '*.minus_3_sd' => ['required', 'numeric'],
            '*.minus_2_sd' => ['required', 'numeric'],
            '*.minus_1_sd' => ['required', 'numeric'],
            '*.median'     => ['required', 'numeric'],
            '*.plus_1_sd'  => ['required', 'numeric'],
            '*.plus_2_sd'  => ['required', 'numeric'],
            '*.plus_3_sd'  => ['required', 'numeric'],

            '*.parameter'  => ['required', 'string', 'max:20'],
            '*.measurement_condition' => ['nullable', 'string', 'max:20'],
            '*.is_active'  => ['nullable', 'boolean'],
        ];
    }

    /** ringkasan hasil import */
    public function summary(): array
    {
        return [
            'rows_processed' => $this->rows,
            'failures'       => collect($this->failures())->map(function ($f) {
                return [
                    'row' => $f->row(),
                    'attribute' => $f->attribute(),
                    'errors' => $f->errors(),
                    'values' => $f->values(),
                ];
            })->values(),
            'errors'         => collect($this->errors())->map(fn($e) => (string)$e)->values(),
        ];
    }
}
