<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpCompetency extends Model
{
    use HasFactory;

    protected $table = 'ERP_COMPETENCY';
    protected $primaryKey = 'ERP_COMPETENCY_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ERP_PERSONNEL_ID',
        'PROFICIENCY_LEVEL',
        'RELATED_EXPERIENCE_YEARS',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
    ];

    protected $casts = [
        'RELATED_EXPERIENCE_YEARS' => 'decimal:2',
    ];

    /**
     * Get the ERP personnel that owns the competency.
     */
    public function erpPersonnel(): BelongsTo
    {
        return $this->belongsTo(ErpPersonnel::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }
}