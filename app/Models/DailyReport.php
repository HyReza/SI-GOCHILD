<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{

    use HasFactory;
    protected $guarded = [];

    public function activityTransaction()
    {
        return $this->belongsTo(ActivityTransaction::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function babyDetail()
    {
        return $this->hasOne(\App\Models\DailyReportBabyDetail::class, 'daily_report_id');
    }
    public function childrenDetail()
    {
        return $this->hasOne(\App\Models\DailyReportChildrenDetail::class, 'daily_report_id');
    }
}
