<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleAssetInformation extends Model
{
    use HasFactory;

    protected $table = 'vehicle_asset_information';
    
    protected $fillable = [];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'vehicle_asset_information_id');
    }
}