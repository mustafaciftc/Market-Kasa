<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $dates = ['created_at'];

    protected $fillable = [
        'basket', 'customer_id', 'user_id', 'discount', 'sub_total', 'total_price',
        'pay_type', 'discount_total', 'entry_date', 'expiration_date', 'stock_quantity', 'payment_details',
    ];

    protected $casts = [
        'basket' => 'array',
        'discount' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'price' => 'decimal:2',
        'entry_date' => 'date',
        'expiration_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statistic()
    {
        return $this->hasOne(SaleStatistic::class, 'sale_id');
    }

    public function debt()
    {
        return $this->hasOne(Debt::class, 'sale_id');
    }
	
	public function paymentDetail()
{
    return $this->hasOne(PaymentDetail::class, 'sale_id');
}
}