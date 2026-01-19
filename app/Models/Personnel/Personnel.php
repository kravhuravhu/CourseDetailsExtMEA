<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'PERSONNEL';
    protected $primaryKey = 'PERSONNEL_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [];

    /**
     * Get the ERP personnel record associated with the personnel.
     */
    public function erpPersonnel(): HasOne
    {
        return $this->hasOne(ErpPersonnel::class, 'PERSONNEL_ID', 'PERSONNEL_ID');
    }

    /**
     * Get the organisations associated with the personnel.
     */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Organisation\ErpOrganisation::class,
            'PERSONNEL_ORGANISATION_LINK',
            'PERSONNEL_ID',
            'ORGANISATION_ID'
        )->withPivot('RELATIONSHIP_TYPE');
    }

    /**
     * Get the locations associated with the personnel.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Location\Location::class,
            'PERSONNEL_LOCATION_LINK',
            'PERSONNEL_ID',
            'LOCATION_ID'
        )->withPivot('RELATIONSHIP_TYPE');
    }
}