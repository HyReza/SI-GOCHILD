<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrowthStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'reference_type',
        'age_months',
        'body_length',
        'body_height',
        'minus_3_sd',
        'minus_2_sd',
        'minus_1_sd',
        'median',
        'plus_1_sd',
        'plus_2_sd',
        'plus_3_sd',
        'parameter',
        'measurement_condition',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'age_months'  => 'integer',
        'body_length' => 'decimal:2',
        'body_height' => 'decimal:2',
        'minus_3_sd'  => 'decimal:2',
        'minus_2_sd'  => 'decimal:2',
        'minus_1_sd'  => 'decimal:2',
        'median'      => 'decimal:2',
        'plus_1_sd'   => 'decimal:2',
        'plus_2_sd'   => 'decimal:2',
        'plus_3_sd'   => 'decimal:2',
    ];

    /* Scopes filter */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeGender($q, $g)
    {
        return $g ? $q->where('gender', $g) : $q;
    }
    public function scopeParameter($q, $p)
    {
        return $p ? $q->where('parameter', $p) : $q;
    }
    public function scopeRefType($q, $t)
    {
        return $t ? $q->where('reference_type', $t) : $q;
    }

    /** Helper untuk ambil semua band SD */
    public function sdBands(): array
    {
        return [
            '-3' => (float)$this->minus_3_sd,
            '-2' => (float)$this->minus_2_sd,
            '-1' => (float)$this->minus_1_sd,
            '0' => (float)$this->median,
            '1' => (float)$this->plus_1_sd,
            '2' => (float)$this->plus_2_sd,
            '3' => (float)$this->plus_3_sd,
        ];
    }
}
