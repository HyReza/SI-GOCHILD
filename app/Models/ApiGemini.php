<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiGemini extends Model
{
    use HasFactory;

    protected $table = 'api_geminis';

    protected $fillable = [
        'name',
        'api_key',
        'model',
        'is_active',
    ];
}
