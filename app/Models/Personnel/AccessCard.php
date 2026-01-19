<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessCard extends Model
{
    use HasFactory;

    protected $table = 'ACCESS_CARD';
    protected $primaryKey = 'ACCESS_CARD_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ERP_PERSONNEL_ID',
        'ACCESS_TYPE',
        'APPLICATION_DATE',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
        'CATEGORY',
        'CREATED_DATE_TIME',
        'DOC_REMARKS',
        'DOC_STATUS',
        'DOC_STATUS_DATE',
        'LAST_MODIFIED_DATE_TIME',
        'REVISION_NUMBER',
        'SUBJECT',
        'TITLE',
        'END_DATE_TIME',
        'SCOPE_INFORMATION',
        'SIGN_DATE',
        'START_DATE_TIME',
    ];

    protected $casts = [
        'APPLICATION_DATE' => 'date',
        'CREATED_DATE_TIME' => 'datetime',
        'DOC_STATUS_DATE' => 'date',
        'LAST_MODIFIED_DATE_TIME' => 'datetime',
        'END_DATE_TIME' => 'datetime',
        'SIGN_DATE' => 'date',
        'START_DATE_TIME' => 'datetime',
    ];

    /**
     * Get the ERP personnel that owns the access card.
     */
    public function erpPersonnel(): BelongsTo
    {
        return $this->belongsTo(ErpPersonnel::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }

    /**
     * Get the access control areas for this access card.
     */
    public function accessControlAreas(): HasMany
    {
        return $this->hasMany(AccessControlArea::class, 'ACCESS_CARD_ID', 'ACCESS_CARD_ID');
    }
}