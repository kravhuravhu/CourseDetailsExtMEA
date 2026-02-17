<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;

class ErrorLogV2 extends Model
{
    protected $table = 'error_log_v2';
    
    protected $fillable = [
        'transaction_id',
        'message_uid',
        'component_name',
        'bus_key_name',
        'bus_key_value',
        'error_message',
        'error_details',
        'source_timestamp',
        'app_server_id',
        'environment',
        'raw_payload'
    ];

    protected $casts = [
        'source_timestamp' => 'datetime',
        'raw_payload' => 'array',
        'created_at' => 'datetime'
    ];

    public function personnel()
    {
        return $this->belongsTo(PersonnelDataV2::class, 'transaction_id', 'message_id');
    }
}