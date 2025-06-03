<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    // Tablo adı
    protected $table = 'gelirgider';

    // Doldurulabilir alanlar (mass assignment)
    protected $fillable = [
        'name',
        'price',
        'type',
        'personel_id',
        'product_id',
        'firma_id',
        'customer_id',
    ];

    // Tarih alanları
    protected $dates = ['deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}