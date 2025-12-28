<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    // CASTING TIPE DATA
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'report_date' => 'date',
        'attendance_summary' => 'array', // PENTING: Otomatis ubah JSON di DB jadi Array PHP
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    // RELASI
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function activityTransaction()
    {
        return $this->belongsTo(ActivityTransaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Detail Checklist Materi
    public function details()
    {
        return $this->hasMany(ReportDetail::class);
    }

    // Detail Kesehatan
    public function healthDetails()
    {
        return $this->hasMany(ReportHealthDetail::class);
    }
}
