<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHealthDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke rapor induk
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
