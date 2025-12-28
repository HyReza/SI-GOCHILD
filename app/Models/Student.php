<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'gender' => 'boolean',
        'entry_date' => 'date',
        'birth_date' => 'date',
        'password' => 'hashed',
    ];

    public function activityTransaction()
    {
        return $this->hasOne(ActivityTransaction::class);
    }

    // public function getAgeInDaysAt(\DateTimeInterface $date): int
    // {
    //     if (!$this->birth_date) {
    //         // jika birth_date null, anggap 0 hari (atau kamu bisa lempar exception)
    //         return 0;
    //     }
    //     return Carbon::parse($this->birth_date)->diffInDays(Carbon::parse($date));
    // }

    public function getAgeInDaysAt(\Illuminate\Support\Carbon $date): int
    {
        return $this->birth_date ? $this->birth_date->diffInDays($date) : 0;
    }



    public function reports()
    {
        return $this->hasMany(Report::class, 'student_id');
    }

    /**
     * Relasi ke absensi (asumsi Anda memiliki Model Attendance).
     */
    public function attendances()
    {
        // Ganti 'Attendance' jika Model absensi Anda bernama lain
        return $this->hasMany(Attendance::class, 'student_id');
    }

    // --- Tambahkan metode helper untuk mendapatkan data yang Anda butuhkan di view ---

    // Metode helper untuk mendapatkan total absen (Sakit/Izin/Alpha)
    public function getTotalAbsenceAttribute()
    {
        // Asumsi kolom status di tabel attendances: 'Sakit', 'Izin', 'Alpha'
        return $this->attendances()
            ->whereIn('status', ['Sakit', 'Izin', 'Alpha'])
            ->count();
    }

    // Metode helper untuk mendapatkan rapor terbaru
    public function getRecentReportAttribute()
    {
        return $this->reports()->latest()->first();
    }
}
