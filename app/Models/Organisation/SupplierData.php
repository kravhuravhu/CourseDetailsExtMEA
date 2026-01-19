<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierData extends Model
{
    use HasFactory;

    protected $table = 'SUPPLIER_DATA';
    protected $primaryKey = 'SUPPLIER_DATA_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ERP_ORGANISATION_ID',
        'ISSUER_IDENTIFICATION_NUMBER',
        'KIND',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
    ];

    /**
     * Get the ERP organisation that owns the supplier data.
     */
    public function erpOrganisation(): BelongsTo
    {
        return $this->belongsTo(ErpOrganisation::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }
}