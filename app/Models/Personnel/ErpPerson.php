<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpPerson extends Model
{
    use HasFactory;

    protected $table = 'ERP_PERSON';
    protected $primaryKey = 'ERP_PERSON_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ERP_PERSONNEL_ID',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
        'BIRTH_DATE_TIME',
        'CATEGORY',
        'DEATH_DATE_TIME',
        'ETHNICITY',
        'FIRST_NAME',
        'GENDER',
        'INITIALS',
        'LAST_NAME',
        'MAIDEN_NAME',
        'MARITAL_STATUS',
        'MARRIAGE_TYPE',
        'M_NAME',
        'NATIONALITY',
        'NICKNAME',
        'PREFIX',
        'SPECIAL_NEEDS',
        'SUFFIX',
    ];

    protected $casts = [
        'BIRTH_DATE_TIME' => 'datetime',
        'DEATH_DATE_TIME' => 'datetime',
    ];

    /**
     * Get the ERP personnel that owns the person.
     */
    public function erpPersonnel(): BelongsTo
    {
        return $this->belongsTo(ErpPersonnel::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }

    /**
     * Scope a query to filter by MRID.
     */
    public function scopeByMrid($query, $mrid)
    {
        return $query->where('MRID', $mrid);
    }

    /**
     * Scope a query to search by name.
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where('NAME', 'LIKE', "%{$name}%")
                    ->orWhere('FIRST_NAME', 'LIKE', "%{$name}%")
                    ->orWhere('LAST_NAME', 'LIKE', "%{$name}%");
    }
}