<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegrationLog extends Model
{
    use HasFactory;

    protected $table = 'integration_logs';
    
    protected $fillable = [
        'message_type',
        'message_id',
        'status',
        'source',
        'payload',
        'processed_data',
        'error_message',
        'retry_count',
        'processed_at',
    ];

    protected $casts = [
        'retry_count' => 'integer',
        'processed_at' => 'datetime',
        'payload' => 'string',
        'processed_data' => 'string',
    ];

    public function scopeAdhoc($query)
    {
        return $query->where('message_type', 'adhoc');
    }

    public function scopeTakeon($query)
    {
        return $query->where('message_type', 'takeon');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}