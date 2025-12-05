<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'attendance_summary' => 'json', // Otomatis cast ke/dari JSON
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relasi ke siswa pemilik rapor

    public function activityTransaction()
    {
        return $this->belongsTo(ActivityTransaction::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke guru yang membuat rapor
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke detail-detail penilaian
    public function details()
    {
        return $this->hasMany(ReportDetail::class);
    }

    // Relasi ke catatan per tema
    public function themeNotes()
    {
        return $this->hasMany(ReportThemeNote::class);
    }

    // Relasi ke data kesehatan
    public function healthDetails()
    {
        return $this->hasMany(ReportHealthDetail::class);
    }
}
