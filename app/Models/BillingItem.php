<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingItem extends Model
{
    use HasFactory;

    protected $table = 'billing_items';

    protected $fillable = [
        'billing_id',
        'item_type', // program_fee, extra_service, absence_fine, manual_charge, manual_discount
        'item_description',
        'source_id',
        'source_type',
        'amount',
        'sign', // debit atau credit
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    /**
     * Relasi: Item ini milik dokumen tagihan mana.
     */
    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class, 'billing_id');
    }

    /**
     * Relasi Polimorfik: Menunjukkan sumber dari item ini.
     * Dapat merujuk ke ServiceOrder, AbsenceFine, ActivityTransaction, dll.
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
