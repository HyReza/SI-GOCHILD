<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentDevelopmentReport;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentDevelopmentReportHealth extends Model
{
    use HasFactory;

    protected $table = 'student_development_report_healths';

    protected $fillable = [
        'student_development_report_id',
        'vision',
        'hearing',
        'teeth',
        'nails',
        'skin',
        'hygiene',
        'remarks',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(StudentDevelopmentReport::class, 'student_development_report_id');
    }
}
