<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $table = 'service_orders';

    protected $fillable = [
        'student_id',
        'extra_service_id',
        'order_date',
        'quantity',
        'base_price_at_order',
        'final_price_per_unit',
        'total_final_price',
        'discount_note',
        'payment_method',
        'status',
        'billing_id',
        'processed_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'quantity' => 'integer',
        'base_price_at_order' => 'integer',
        'final_price_per_unit' => 'integer',
        'total_final_price' => 'integer',
    ];

    /**
     * Relasi: Pesanan ini ditujukan untuk siswa mana.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relasi: Pesanan ini merujuk ke item katalog mana.
     */
    public function extraService(): BelongsTo
    {
        return $this->belongsTo(ExtraService::class, 'extra_service_id');
    }

    /**
     * Relasi: Pesanan ini jika sudah dimasukkan ke Tagihan Induk.
     */
    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class, 'billing_id');
    }

    /**
     * Relasi: Pesanan ini sebagai item di BillingItem (Relasi Polimorfik).
     */
    public function billingItems(): MorphMany
    {
        return $this->morphMany(BillingItem::class, 'source');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
