<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function DailyReport()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function billingItems(): MorphMany
    {
        return $this->morphMany(BillingItem::class, 'source');
    }
}
