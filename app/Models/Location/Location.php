<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $table = 'LOCATION';
    protected $primaryKey = 'LOCATION_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'LOCATIONS_COLLECTION_ID',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
        'CATEGORY',
        'CODE',
        'DIRECTION',
        'GEO_INFO_REFERENCE',
        'IS_POLYGON',
    ];

    protected $casts = [
        'IS_POLYGON' => 'boolean',
    ];

    /**
     * Get the locations collection that owns this location.
     */
    public function locationsCollection(): BelongsTo
    {
        return $this->belongsTo(LocationsCollection::class, 'LOCATIONS_COLLECTION_ID', 'LOCATIONS_COLLECTION_ID');
    }

    /**
     * Get the postal address for this location.
     */
    public function postalAddress(): HasOne
    {
        return $this->hasOne(PostalAddress::class, 'LOCATION_ID', 'LOCATION_ID');
    }

    /**
     * Get the street address for this location.
     */
    public function streetAddress(): HasOne
    {
        return $this->hasOne(StreetAddress::class, 'LOCATION_ID', 'LOCATION_ID');
    }

    /**
     * Get the personnel associated with this location.
     */
    public function personnel(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Personnel\Personnel::class,
            'PERSONNEL_LOCATION_LINK',
            'LOCATION_ID',
            'PERSONNEL_ID'
        )->withPivot('RELATIONSHIP_TYPE');
    }

    /**
     * Get the organisations associated with this location.
     */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Organisation\ErpOrganisation::class,
            'ORGANISATION_LOCATION_LINK',
            'LOCATION_ID',
            'ORGANISATION_ID'
        )->withPivot('RELATIONSHIP_TYPE');
    }

    /**
     * Scope a query to filter by MRID.
     */
    public function scopeByMrid($query, $mrid)
    {
        return $query->where('MRID', $mrid);
    }

    /**
     * Scope a query to search by name or code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('NAME', 'LIKE', "%{$search}%")
                    ->orWhere('CODE', 'LIKE', "%{$search}%")
                    ->orWhere('CATEGORY', 'LIKE', "%{$search}%");
    }

    /**
     * Get full address as string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];
        
        if ($this->streetAddress) {
            if ($this->streetAddress->STREET_NUMBER) $parts[] = $this->streetAddress->STREET_NUMBER;
            if ($this->streetAddress->STREET_NAME) $parts[] = $this->streetAddress->STREET_NAME;
            if ($this->streetAddress->CITY) $parts[] = $this->streetAddress->CITY;
            if ($this->streetAddress->POSTAL_CODE) $parts[] = $this->streetAddress->POSTAL_CODE;
        } elseif ($this->postalAddress) {
            if ($this->postalAddress->CITY) $parts[] = $this->postalAddress->CITY;
            if ($this->postalAddress->POSTAL_CODE) $parts[] = $this->postalAddress->POSTAL_CODE;
        }

        return implode(', ', array_filter($parts));
    }
}