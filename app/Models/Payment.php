<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'paymentable_id',
        'paymentable_type',
        'amount',
        'proof_image',
        'bank_destination',
        'sender_name',
        'sender_bank',
        'status',
        'rejection_note',
        'verified_by',
    ];

    public function paymentable()
    {
        return $this->morphTo();
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
