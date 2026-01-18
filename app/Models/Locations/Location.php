<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Personnel\ErpPersonnel;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    
    protected $fillable = [
        'locations_collection_id',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
        'category',
        'code',
        'direction',
        'geo_info_reference',
        'is_polygon',
    ];

    protected $casts = [
        'is_polygon' => 'boolean',
    ];

    public function locationsCollection()
    {
        return $this->belongsTo(LocationsCollection::class, 'locations_collection_id');
    }

    public function postalAddress()
    {
        return $this->hasOne(PostalAddress::class, 'location_id');
    }

    public function streetAddress()
    {
        return $this->hasOne(StreetAddress::class, 'location_id');
    }

    public function personnel()
    {
        return $this->belongsToMany(ErpPersonnel::class,
            'personnel_location_assignments',
            'location_id',
            'erp_personnel_id'
        )->withPivot('assignment_type', 'start_date', 'end_date')
         ->withTimestamps();
    }
}