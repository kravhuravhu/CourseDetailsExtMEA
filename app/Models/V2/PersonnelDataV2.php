<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;

class PersonnelDataV2 extends Model
{
    protected $table = 'personnel_data_v2';
    
    protected $fillable = [
        'mrid',
        'first_name',
        'initials',
        'last_name',
        'nickname',
        'skill_description',
        'skill_status',
        'to_document_roles_mrid',
        'certified_date',
        'source_system',
        'message_id',
        'original_event_datetime',
        'raw_payload'
    ];

    protected $casts = [
        'certified_date' => 'datetime',
        'original_event_datetime' => 'datetime',
        'raw_payload' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function auditLogs()
    {
        return $this->hasMany(AuditLogV2::class, 'transaction_id', 'message_id')
            ->orWhere('bus_key_value', 'like', '%' . $this->mrid . '%');
    }

    public function errorLogs()
    {
        return $this->hasMany(ErrorLogV2::class, 'transaction_id', 'message_id')
            ->orWhere('bus_key_value', 'like', '%' . $this->mrid . '%');
    }
}