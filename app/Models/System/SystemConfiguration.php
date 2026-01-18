<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemConfiguration extends Model
{
    use HasFactory;

    protected $table = 'system_configurations';
    
    protected $fillable = [
        'config_key',
        'config_value',
        'description',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    public static function getValue($key, $default = null)
    {
        $config = self::where('config_key', $key)->first();
        
        if (!$config) {
            return $default;
        }

        if ($config->is_encrypted && !empty($config->config_value)) {
            try {
                return decrypt($config->config_value);
            } catch (\Exception $e) {
                return $default;
            }
        }

        return $config->config_value ?? $default;
    }

    public static function setValue($key, $value, $description = null, $encrypt = false)
    {
        $config = self::where('config_key', $key)->first();
        
        if (!$config) {
            $config = new self();
            $config->config_key = $key;
        }
        
        if ($description) {
            $config->description = $description;
        }
        
        $config->config_value = $encrypt ? encrypt($value) : $value;
        $config->is_encrypted = $encrypt;
        
        return $config->save();
    }
}