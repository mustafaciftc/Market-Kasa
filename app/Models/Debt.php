<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'amount', 'description', 'term', 'date', 'sale_id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reminders()
    {
        return $this->hasMany(DebtReminder::class);
    }

    // Calculate total payments made for this debt
    public function getTotalPaymentsAttribute()
    {
        return $this->payments->sum('amount');
    }

    // Calculate remaining debt
    public function getRemainingDebtAttribute()
    {
        return max(0, $this->amount - $this->total_payments);
    }

    // Check if debt is fully paid
  	public function isFullyPaid()
    {
        return $this->remaining_debt <= 0;
    }
    
}