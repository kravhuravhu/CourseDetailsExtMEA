<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectronicAddress extends Model
{
    use HasFactory;

    protected $table = 'ELECTRONIC_ADDRESS';
    protected $primaryKey = 'ELECTRONIC_ADDRESS_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ERP_ORGANISATION_ID',
        'EMAIL',
        'LAN',
        'PASSWORD',
        'RADIO',
        'SEQUENCE_NUMBER',
        'USAGE',
        'USER_ID',
        'WEB',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
    ];

    /**
     * Get the ERP organisation that owns the electronic address.
     */
    public function erpOrganisation(): BelongsTo
    {
        return $this->belongsTo(ErpOrganisation::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }
}