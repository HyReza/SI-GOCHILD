<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentDevelopmentReport extends Model
{
    use HasFactory;

    protected $table = 'student_development_reports';

    protected $guarded = ['id']; // Membuka mass assignment untuk semua kolom kecuali ID

    /**
     * Casting otomatis JSON ke Array dan Date
     */
    protected $casts = [
        'report_date' => 'date',
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        // Ini penting agar saat dipanggil di view/controller langsung jadi array
        'weight_chart_snapshot' => 'array',
        'height_chart_snapshot' => 'array',
        'head_chart_snapshot'   => 'array',
        'bmi_chart_snapshot'    => 'array',
    ];

    // Relasi ke Siswa
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Relasi ke Detail Kesehatan
    public function healthDetail(): HasOne
    {
        return $this->hasOne(StudentDevelopmentReportHealth::class, 'student_development_report_id');
    }

    // Relasi ke Assessment MMDST Asli (Jika perlu melihat data mentah)
    public function mmdstAssessment(): BelongsTo
    {
        return $this->belongsTo(MmdstAssessment::class, 'mmdst_assessment_id');
    }
}
