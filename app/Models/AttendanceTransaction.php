<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attendances()
    {
        return $this->hasMany(Attendance::class,  'attendances_transaction_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id'); // 'service_id' adalah foreign key
    }
}
