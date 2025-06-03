<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'customers';
    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function debts() 
    {
        return $this->hasMany(Debt::class, 'customer_id');
    }

    public function getTotalDebtAttribute()
    {
        return $this->debts->sum('remaining_debt');
    }
}