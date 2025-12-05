<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MmdstAssessmentSectorSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'stimulation_category_id',
        'total_items',
        'delays_count',
        'refusals_count',
        'pass_at_age_line_count',
        'sector_result',
    ];

    public function assessment()
    {
        return $this->belongsTo(MmdstAssessment::class, 'assessment_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryParameter::class, 'stimulation_category_id');
    }
}
