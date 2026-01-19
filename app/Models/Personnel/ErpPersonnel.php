<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpPersonnel extends Model
{
    use HasFactory;

    protected $table = 'ERP_PERSONNEL';
    protected $primaryKey = 'ERP_PERSONNEL_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'PERSONNEL_ID',
        'ADMINISTRATION_INDICATOR',
        'DEEMED_START_DATE_TIME',
        'FINISH_DATE',
        'JOB_CODE',
        'JOB_RESTRICTION_CODES',
        'JOB_TITLE',
        'KEY_PERSON_INDICATOR',
        'OVERTIME_ELIGIBLE_INDICATOR',
        'PAYMENT_METHOD',
        'RESPONSIBILITY',
        'START_DATE',
        'TRANSFER_BENEFITS_PAYABLE_INDICATOR',
    ];

    protected $casts = [
        'ADMINISTRATION_INDICATOR' => 'boolean',
        'KEY_PERSON_INDICATOR' => 'boolean',
        'OVERTIME_ELIGIBLE_INDICATOR' => 'boolean',
        'TRANSFER_BENEFITS_PAYABLE_INDICATOR' => 'boolean',
        'DEEMED_START_DATE_TIME' => 'datetime',
        'FINISH_DATE' => 'date',
        'START_DATE' => 'date',
    ];

    /**
     * Get the personnel record associated with the ERP personnel.
     */
    public function personnel(): HasOne
    {
        return $this->hasOne(Personnel::class, 'PERSONNEL_ID', 'PERSONNEL_ID');
    }

    /**
     * Get the ERP person details.
     */
    public function erpPerson(): HasOne
    {
        return $this->hasOne(ErpPerson::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }

    /**
     * Get the access cards for this personnel.
     */
    public function accessCards(): HasMany
    {
        return $this->hasMany(AccessCard::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }

    /**
     * Get the employee benefits for this personnel.
     */
    public function employeeBenefits(): HasMany
    {
        return $this->hasMany(EmployeeBenefit::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }

    /**
     * Get the skills for this personnel.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }

    /**
     * Get the competencies for this personnel.
     */
    public function competencies(): HasMany
    {
        return $this->hasMany(ErpCompetency::class, 'ERP_PERSONNEL_ID', 'ERP_PERSONNEL_ID');
    }
}