<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;

class AuditLogV2 extends Model
{
    protected $table = 'audit_log_v2';
    
    protected $fillable = [
        'transaction_id',
        'message_uid',
        'component_name',
        'bus_key_name',
        'bus_key_value',
        'description',
        'source_timestamp',
        'app_server_id',
        'environment',
        'audit_type',
        'message_data'
    ];

    protected $casts = [
        'source_timestamp' => 'datetime',
        'created_at' => 'datetime'
    ];
    
    public function personnel()
    {
        return $this->belongsTo(PersonnelDataV2::class, 'transaction_id', 'message_id');
    }
}