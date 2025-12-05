<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendance extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attendanceTransaction()
    {
        return $this->belongsTo(AttendanceTransaction::class, 'attendances_transaction_id');
    }

    public function activityTransaction()
    {
        return $this->belongsTo(ActivityTransaction::class, 'activity_transaction_id');
    }

    public function absenceFine()
    {
        return $this->belongsTo(AbsenceFine::class, 'absence_fine_id');
    }
}
