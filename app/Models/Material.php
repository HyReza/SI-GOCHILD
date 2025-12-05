<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function subTheme()
    {
        return $this->belongsTo(SubTheme::class);
    }
}
