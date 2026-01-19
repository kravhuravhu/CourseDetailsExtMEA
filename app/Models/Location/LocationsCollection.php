<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationsCollection extends Model
{
    use HasFactory;

    protected $table = 'LOCATIONS_COLLECTION';
    protected $primaryKey = 'LOCATIONS_COLLECTION_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [];

    /**
     * Get the locations for this collection.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'LOCATIONS_COLLECTION_ID', 'LOCATIONS_COLLECTION_ID');
    }
}