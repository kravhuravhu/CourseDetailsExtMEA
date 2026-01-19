<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpTelephoneNumber extends Model
{
    use HasFactory;

    protected $table = 'ERP_TELEPHONE_NUMBER';
    protected $primaryKey = 'ERP_TELEPHONE_NUMBER_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ERP_ORGANISATION_ID',
        'AREA_CODE',
        'CITY_CODE',
        'COUNTRY_CODE',
        'EXTENSION',
        'LOCAL_NUMBER',
        'SEQUENCE_NUMBER',
        'TRANSMISSION_TYPE',
        'USAGE',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
    ];

    /**
     * Get the ERP organisation that owns the telephone number.
     */
    public function erpOrganisation(): BelongsTo
    {
        return $this->belongsTo(ErpOrganisation::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }

    /**
     * Get the formatted telephone number.
     */
    public function getFormattedNumberAttribute(): string
    {
        $parts = [];
        if ($this->COUNTRY_CODE) $parts[] = "+{$this->COUNTRY_CODE}";
        if ($this->AREA_CODE) $parts[] = "({$this->AREA_CODE})";
        if ($this->LOCAL_NUMBER) $parts[] = $this->LOCAL_NUMBER;
        if ($this->EXTENSION) $parts[] = "ext. {$this->EXTENSION}";
        
        return implode(' ', $parts);
    }
}