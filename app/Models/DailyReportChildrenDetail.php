<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportChildrenDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    // public function report()
    // {
    //     return $this->belongsTo(DailyReport::class, 'daily_report_id');
    // }

    // public function session1Material()
    // {
    //     return $this->belongsTo(Material::class, 'session1_material_id');
    // }

    // public function session2Material()
    // {
    //     return $this->belongsTo(Material::class, 'session2_material_id');
    // }

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
    public function session1Material()
    {
        return $this->belongsTo(Material::class, 'session1_material_id');
    }
    public function session2Material()
    {
        return $this->belongsTo(Material::class, 'session2_material_id');
    }
}
