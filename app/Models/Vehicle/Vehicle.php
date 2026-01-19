<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'VEHICLE';
    protected $primaryKey = 'VEHICLE_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'VEHICLE_ASSET_INFORMATION_ID',
        'CATEGORY',
        'CREW_REQUIREMENT',
        'ETAG',
        'FUEL_TYPE',
        'ODOMETER_READING',
        'ODOMETER_UNIT',
        'ODOMETER_MULTIPLIER',
        'USAGE_KIND',
        'VEHICLE_MAKE',
        'VEHICLE_MODEL',
        'VEHICLE_TYPE',
        'YEAR',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
        'APPLICATION',
        'CATEGORY_ASSET',
        'CODE',
        'CRITICAL',
        'GUARANTEE_EXPIRY_DATE',
        'INITIAL_CONDITION',
        'INSTALLATION_DATE',
        'LOT_NUMBER',
        'MANUFACTURED_DATE',
        'SERIAL_NUMBER',
        'TEST_DATE',
        'TEST_STATUS',
        'TEST_TYPE',
        'UTC_NUMBER',
    ];

    protected $casts = [
        'CRITICAL' => 'boolean',
        'YEAR' => 'integer',
        'ODOMETER_READING' => 'decimal:2',
        'GUARANTEE_EXPIRY_DATE' => 'date',
        'INSTALLATION_DATE' => 'date',
        'MANUFACTURED_DATE' => 'date',
        'TEST_DATE' => 'date',
    ];

    /**
     * Get the vehicle asset information that owns the vehicle.
     */
    public function vehicleAssetInformation(): BelongsTo
    {
        return $this->belongsTo(VehicleAssetInformation::class, 'VEHICLE_ASSET_INFORMATION_ID', 'VEHICLE_ASSET_INFORMATION_ID');
    }

    /**
     * Scope a query to filter by MRID.
     */
    public function scopeByMrid($query, $mrid)
    {
        return $query->where('MRID', $mrid);
    }

    /**
     * Scope a query to search by make, model, or serial number.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('VEHICLE_MAKE', 'LIKE', "%{$search}%")
                    ->orWhere('VEHICLE_MODEL', 'LIKE', "%{$search}%")
                    ->orWhere('SERIAL_NUMBER', 'LIKE', "%{$search}%")
                    ->orWhere('NAME', 'LIKE', "%{$search}%");
    }

    /**
     * Get the vehicle's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->VEHICLE_MAKE} {$this->VEHICLE_MODEL} ({$this->YEAR})";
    }
}