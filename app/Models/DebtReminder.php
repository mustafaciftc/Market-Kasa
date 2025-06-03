<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DebtReminder extends Model
{
    protected $fillable = [
        'debt_id',
        'customer_id',
        'reminder_type',
        'message',
        'reminder_date',
        'status',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'reminder_date' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // due_date için varsayılan değer
    protected $attributes = [
        'due_date' => null,
    ];

    // due_date null ise otomatik doldur
    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = $value ?? now();
    }
}