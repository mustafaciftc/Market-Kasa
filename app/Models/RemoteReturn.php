<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteReturn extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'customer_id', 'quantity', 'reason', 'return_amount', 'status'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}