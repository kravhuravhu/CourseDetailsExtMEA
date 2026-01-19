<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;

    protected $table = 'ERROR_LOG';
    protected $primaryKey = 'ERROR_LOG_ID';

    public $timestamps = false;
    const CREATED_AT = 'CREATED_AT';

    protected $fillable = [
        'TRANSACTION_ID',
        'COMPONENT_NAME',
        'DESCRIPTION',
        'EXCEPTION',
        'CRITICALITY',
        'CATEGORY',
        'MESSAGE_UID',
        'APP_SERVER_ID',
        'ENVIRONMENT',
        'SOURCE_TIMESTAMP',
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
     * Scope a query to filter by criticality.
     */
    public function scopeByCriticality($query, $criticality)
    {
        return $query->where('CRITICALITY', $criticality);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('CATEGORY', $category);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('CREATED_AT', [$startDate, $endDate]);
    }
}