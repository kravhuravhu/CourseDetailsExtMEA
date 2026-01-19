<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostalAddress extends Model
{
    use HasFactory;

    protected $table = 'POSTAL_ADDRESS';
    protected $primaryKey = 'POSTAL_ADDRESS_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'LOCATION_ID',
        'PO_BOX',
        'PO_BOX_TYPE',
        'ADDRESS_GENERAL',
        'CITY',
        'CITY_SUB_DIVISION_NAME',
        'COUNTRY',
        'COUNTRY_SUB_DIVISION_CODE',
        'POSTAL_CODE',
        'SECTION',
        'STATE_OR_PROVINCE',
        'TOWN_CODE',
        'WITHIN_CITY_LIMITS',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
        'FORMAT_CODE',
        'SEQUENCE_NUMBER',
    ];

    protected $casts = [
        'WITHIN_CITY_LIMITS' => 'boolean',
    ];

    /**
     * Get the location that owns the postal address.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'LOCATION_ID', 'LOCATION_ID');
    }
}