<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrderEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_order_id',
        'file_path',
        'description',
        'uploaded_by',
    ];

    // Relasi balik ke Order
    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    // Relasi ke User pengupload (Guru/Admin)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
