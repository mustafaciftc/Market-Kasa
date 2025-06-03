<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleStatistic extends Model
{

    
    protected $table = 'sale_statistic'; 
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'sale_id',
        'customer_id',
        'total_sell',
        'total_buy',
        'total_sale',
        'total_discount',
        'total_nakit',
        'total_kart',
        'total_veresiye',
    ];

    protected $casts = [
        'total_sell' => 'float',
        'total_buy' => 'float',
        'total_sale' => 'integer',
        'total_discount' => 'float',
        'total_nakit' => 'float',
        'total_kart' => 'float',
        'total_veresiye' => 'float',
    ];

    // Relationship with Sale model
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id'); 
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
	
	public $timestamps = true;
}