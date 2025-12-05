<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MmdstAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_date',
        'age_in_days',
        'overall_result',
        'counters',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'assessment_date' => 'date',   // <— penting
        'age_in_days'     => 'integer',
        'counters'        => 'array',  // kalau pakai kolom JSON counters
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function activityTransaction()
    {
        return $this->belongsTo(ActivityTransaction::class);
    }

    public function items()
    {
        return $this->hasMany(MmdstAssessmentItem::class, 'assessment_id');
    }

    public function sectorSummaries()
    {
        return $this->hasMany(MmdstAssessmentSectorSummary::class, 'assessment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
