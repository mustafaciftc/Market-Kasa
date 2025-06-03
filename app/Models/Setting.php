<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'website',
        'vergi_number',
        'vergi_dairesi',
        'light_logo',
        'dark_logo',
        'favicon',
        'perm_option',
        'register_module',
        'theme',
        'menu',
		'key', 
		'value'
    ];

    /**
     * Get settings as a key-value array.
     *
     * @return array
     */
    public static function getSettings()
    {
        $settings = self::first();
        if (!$settings) {
            return [];
        }
        
        return $settings->toArray();
    }

    /**
     * Update a setting by key
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function updateSetting($key, $value)
    {
        $settings = self::first();
        if (!$settings) {
            $settings = new self();
        }
        
        if (in_array($key, (new self())->fillable)) {
            $settings->$key = $value;
            return $settings->save();
        }
        
        return false;
    }
}