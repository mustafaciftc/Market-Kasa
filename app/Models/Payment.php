<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['debt_id', 'amount', 'date', 'description'];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}