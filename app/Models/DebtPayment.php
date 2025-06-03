<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{

    protected $fillable = [
        'debt_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    // Payment method types as constants
    const PAYMENT_CASH = 1;
    const PAYMENT_CARD = 2;
    const PAYMENT_TRANSFER = 3;

    // Get payment method as text
    public function getPaymentMethodTextAttribute()
    {
        return match($this->payment_method) {
            self::PAYMENT_CASH => 'Nakit',
            self::PAYMENT_CARD => 'Kart',
            self::PAYMENT_TRANSFER => 'Havale/EFT',
            default => 'Bilinmiyor'
        };
    }
}
