<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportBabyDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'asi_formula_items' => 'array',
        'mpasi_items'       => 'array',
        'naps'              => 'array',
        'diapers'           => 'array',
    ];

    // ✅ dipakai kalau sewaktu-waktu perlu eager-load balik
    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    // (opsional) alias ke nama lamamu
    public function report()
    {
        return $this->dailyReport();
    }
}
