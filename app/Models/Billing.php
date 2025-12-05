<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billings';

    protected $fillable = [
        'invoice_number',
        'student_id',
        'billing_date',
        'due_date',
        'total_base_amount',
        'total_discount_amount',
        'final_amount',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'total_base_amount' => 'integer',
        'total_discount_amount' => 'integer',
        'final_amount' => 'integer',
        // payment_status adalah ENUM, tidak perlu di-cast jika diakses sebagai string
    ];

    /**
     * Relasi: Tagihan ini ditujukan untuk siswa mana.
     */
    public function student(): BelongsTo
    {
        // Asumsi model Student ada
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relasi: Tagihan ini memiliki banyak rincian item.
     */
    public function items(): HasMany
    {
        return $this->hasMany(BillingItem::class, 'billing_id');
    }

    /**
     * Relasi: Tagihan ini terkait dengan pesanan layanan mana.
     */
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'billing_id');
    }
}
