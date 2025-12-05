<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MmdstAssessmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'mmdst_parameter_id',
        'stimulation_category_id',
        'result_code',
        'is_delay',
        'is_age_line',
        'note',
    ];

    protected $casts = [
        'is_delay'   => 'boolean',
        'is_age_line' => 'boolean',
    ];

    public function assessment()
    {
        return $this->belongsTo(MmdstAssessment::class, 'assessment_id');
    }

    public function parameter()
    {
        return $this->belongsTo(MmdstParameter::class, 'mmdst_parameter_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryParameter::class, 'stimulation_category_id');
    }
}
