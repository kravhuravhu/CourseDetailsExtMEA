<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'AUDIT_LOG';
    protected $primaryKey = 'AUDIT_LOG_ID';

    public $timestamps = false;
    const CREATED_AT = 'CREATED_AT';

    protected $fillable = [
        'TRANSACTION_ID',
        'COMPONENT_NAME',
        'DESCRIPTION',
        'AUDIT_TYPE',
        'SOURCE_TIMESTAMP',
        'MESSAGE_UID',
        'APP_SERVER_ID',
        'ENVIRONMENT',
    ];

    protected $casts = [
        'SOURCE_TIMESTAMP' => 'datetime',
        'CREATED_AT' => 'datetime',
    ];

    /**
     * Scope a query to filter by transaction ID.
     */
    public function scopeByTransactionId($query, $transactionId)
    {
        return $query->where('TRANSACTION_ID', $transactionId);
    }

    /**
     * Scope a query to filter by audit type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('AUDIT_TYPE', $type);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('CREATED_AT', [$startDate, $endDate]);
    }
}