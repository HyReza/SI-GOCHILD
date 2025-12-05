<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceFine extends Model
{
    use HasFactory;

    protected $table = 'absence_fines';

    protected $fillable = [
        'student_id',
        'fine_date',
        'description',
        'amount',
        'is_billed', // Digunakan untuk menandai apakah denda sudah diproses ke tagihan/dibayar
    ];

    protected $casts = [
        'fine_date' => 'date',
        'amount' => 'integer',
        'is_billed' => 'boolean',
    ];

    /**
     * Relasi: Denda ini milik Siswa mana.
     */
    public function student(): BelongsTo
    {
        // Asumsi model Student ada
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relasi: Catatan absensi mana yang memicu denda ini.
     * Dalam desain ini, satu denda hanya terhubung ke satu catatan absensi harian.
     */
    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class, 'absence_fine_id');
    }

    /**
     * Relasi: Item tagihan mana yang memasukkan denda ini (jika sudah ditagihkan).
     * (Relasi ini akan aktif jika tabel BillingItem dibuat)
     */
    // public function billingItem(): HasOne
    // {
    //     return $this->hasOne(BillingItem::class, 'source_id')->where('source_type', self::class);
    // }
}
