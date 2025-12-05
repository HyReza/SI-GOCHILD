<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDetail extends Model
{
    use HasFactory;

    public $timestamps = false; // Tabel ini tidak memerlukan timestamps

    protected $guarded = [];

    // Relasi ke rapor induk
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Relasi ke sub tema yang dinilai
    public function subTheme()
    {
        return $this->belongsTo(SubTheme::class);
    }

    // Relasi ke materi yang dinilai (bisa null)
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
