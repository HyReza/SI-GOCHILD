<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtraService extends Model
{
    use HasFactory;

    protected $table = 'extra_services';

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'image_url',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'base_price' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: Layanan ini memiliki banyak pesanan.
     */
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'extra_service_id');
    }

    /**
     * Relasi: Admin/Pengajar yang membuat katalog ini.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
