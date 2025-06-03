<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $fillable = ['sale_id', 'details'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}