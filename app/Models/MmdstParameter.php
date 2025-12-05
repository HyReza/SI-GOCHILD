<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MmdstParameter extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stimulationCategory()
    {
        return $this->belongsTo(CategoryParameter::class, 'stimulation_category_id');
    }
}
