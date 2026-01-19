<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerData extends Model
{
    use HasFactory;

    protected $table = 'CUSTOMER_DATA';
    protected $primaryKey = 'CUSTOMER_DATA_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ERP_ORGANISATION_ID',
        'HAS_SPECIAL_NEEDS',
        'KIND',
        'PUC_NUMBER',
        'SPECIAL_NEEDS',
        'VIP',
        'VIP_DESCRIPTION',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
    ];

    protected $casts = [
        'HAS_SPECIAL_NEEDS' => 'boolean',
        'VIP' => 'boolean',
    ];

    /**
     * Get the ERP organisation that owns the customer data.
     */
    public function erpOrganisation(): BelongsTo
    {
        return $this->belongsTo(ErpOrganisation::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }
}