<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'products';
    
    protected $fillable = [
        'name', 'category_id', 'image', 'barcode', 'buy_price', 'sell_price',
        'quantity', 'active', 'description', 'entry_date', 'expiry_date', 'stock_quantity'
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'expiry_date' => 'datetime',
        'active' => 'boolean',
    ];
	
	
	public function category()
	{
		return $this->belongsTo(Category::class);
	}

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}