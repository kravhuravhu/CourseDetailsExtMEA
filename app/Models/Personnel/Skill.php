<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'SKILL';
    protected $primaryKey = 'SKILL_ID';

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
        'CATEGORY',
        'CREATED_DATE_TIME',
        'DOC_REMARKS',
        'DOC_STATUS',
        'DOC_STATUS_DATE',
        'LAST_MODIFIED_DATE_TIME',
        'REVISION_NUMBER',
        'SUBJECT',
        'TITLE',
    ];

    protected $casts = [
        'CREATED_DATE_TIME' => 'datetime',
        'DOC_STATUS_DATE' => 'date',
        'LAST_MODIFIED_DATE_TIME' => 'datetime',
    ];

    /**
     * Get the ERP personnel that owns the skill.
     */
    public function erpPersonnel(): BelongsTo
    {
        return $this->belongsTo(ErpPersonnel::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }
}