<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $guarded = [];

    // public function subtheme()
    // {
    //     return $this->hasMany(SubTheme::class);
    // }

    public function subThemes()
    {
        return $this->hasMany(SubTheme::class, 'theme_id');
    }
}
