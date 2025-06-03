<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
	
	public const ROLE_ADMIN = 'admin';
    public const ROLE_PERSONNEL = 'personel';
	public const ROLE_CUSTOMER = 'customer';

	public static function getValidRoles(): array
{
    return [
        self::ROLE_ADMIN,
        self::ROLE_PERSONNEL,
        self::ROLE_CUSTOMER
    ];
}
    protected $fillable = [
        'name', 'username', 'email', 'password', 'perm', 'demo', 'role',
        'phone', 'active', 'website', 'company', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $casts = [
        'admin' => 'boolean',
        'perm' => 'array',
    ];

    protected $dates = ['deleted_at'];

    // Kullanıcının satışlarını getir
    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    // Kullanıcının borç (veresiye) kayıtlarını getir
    public function debts()
    {
        return $this->hasManyThrough(Debt::class, Sale::class, 'user_id', 'sale_id');
    }
	
	   protected $attributes = [
        'role' => self::ROLE_CUSTOMER , 
    ];
}