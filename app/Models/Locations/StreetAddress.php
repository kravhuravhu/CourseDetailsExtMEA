<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StreetAddress extends Model
{
    use HasFactory;

    protected $table = 'street_addresses';
    
    protected $fillable = [
        'location_id',
        'building_name',
        'street_name',
        'street_number',
        'street_prefix',
        'street_suffix',
        'street_type',
        'suite_number',
        'address_general',
        'city',
        'city_sub_division_name',
        'country',
        'country_sub_division_code',
        'postal_code',
        'section',
        'state_or_province',
        'town_code',
        'within_city_limits',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
        'format_code',
        'sequence_number',
    ];

    protected $casts = [
        'within_city_limits' => 'boolean',
        'sequence_number' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}