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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke Layanan (Katalog)
    public function extraService()
    {
        return $this->belongsTo(ExtraService::class);
    }

    // Relasi ke Staff/Admin yang memproses terakhir kali
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Relasi ke Tagihan (Jika Bill Later)
    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    // Relasi Polymorphic ke Payments (Bukti Bayar)
    // public function payments()
    // {
    //     return $this->morphMany(Payment::class, 'paymentable');
    // }
    public function payments()
    {
        // Parameter ke-2 ('paymentable') harus sama dengan nama di migrasi: $table->morphs('paymentable');
        return $this->morphMany(Payment::class, 'paymentable');
    }
    // Relasi ke Bukti Foto Pengerjaan (One to Many)
    public function evidences()
    {
        return $this->hasMany(ServiceOrderEvidence::class);
    }
}
