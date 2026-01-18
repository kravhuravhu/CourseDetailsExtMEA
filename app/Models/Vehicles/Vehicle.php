<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';
    
    protected $fillable = [
        'vehicle_asset_information_id',
        'category',
        'crew_requirement',
        'etag',
        'fuel_type',
        'odometer_reading',
        'odometer_unit',
        'odometer_multiplier',
        'usage_kind',
        'vehicle_make',
        'vehicle_model',
        'vehicle_type',
        'year',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
        'application',
        'category_asset',
        'code',
        'critical',
        'guarantee_expiry_date',
        'initial_condition',
        'installation_date',
        'lot_number',
        'manufactured_date',
        'serial_number',
        'test_date',
        'test_status',
        'test_type',
        'utc_number',
    ];

    protected $casts = [
        'odometer_reading' => 'decimal:2',
        'year' => 'integer',
        'critical' => 'boolean',
        'guarantee_expiry_date' => 'date',
        'installation_date' => 'date',
        'manufactured_date' => 'date',
        'test_date' => 'date',
    ];

    public function vehicleAssetInformation()
    {
        return $this->belongsTo(VehicleAssetInformation::class, 'vehicle_asset_information_id');
    }
}