<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationsCollection extends Model
{
    use HasFactory;

    protected $table = 'locations_collections';
    
    protected $fillable = [];

    public function locations()
    {
        return $this->hasMany(Location::class, 'locations_collection_id');
    }
}