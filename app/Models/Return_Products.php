<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Return_Products extends Model
{
    protected $table = 'return_products'; 
	protected $fillable = ['product_id', 'quantity', 'reason', 'sale_id', 'return_amount', 'date'];
    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}