<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleAssetInformation extends Model
{
    use HasFactory;

    protected $table = 'VEHICLE_ASSET_INFORMATION';
    protected $primaryKey = 'VEHICLE_ASSET_INFORMATION_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [];

    /**
     * Get the vehicles for this asset information.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'VEHICLE_ASSET_INFORMATION_ID', 'VEHICLE_ASSET_INFORMATION_ID');
    }
}