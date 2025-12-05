<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory;

    protected $guarded  = [];

    protected $casts = [
        'date_measurement' => 'date',
        'sd_category' => 'array',
        'calculation_results' => 'array',
        'measurement_results' => 'array',
    ];

    public function students()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ActivityTransaction()
    {
        return $this->belongsTo(ActivityTransaction::class);
    }
}
