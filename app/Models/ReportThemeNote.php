<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportThemeNote extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    // Relasi ke rapor induk
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Relasi ke tema
    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
