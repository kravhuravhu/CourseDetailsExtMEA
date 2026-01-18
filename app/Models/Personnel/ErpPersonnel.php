<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Locations\Location;
use App\Models\Organisations\ErpOrganisation;

class ErpPersonnel extends Model
{
    use HasFactory;

    protected $table = 'erp_personnel';
    
    protected $fillable = [
        'personnel_id',
        'administration_indicator',
        'deemed_start_date_time',
        'finish_date',
        'job_code',
        'job_restriction_codes',
        'job_title',
        'key_person_indicator',
        'overtime_eligible_indicator',
        'payment_method',
        'responsibility',
        'start_date',
        'transfer_benefits_payable_indicator',
    ];

    protected $casts = [
        'administration_indicator' => 'boolean',
        'deemed_start_date_time' => 'datetime',
        'finish_date' => 'date',
        'start_date' => 'date',
        'key_person_indicator' => 'boolean',
        'overtime_eligible_indicator' => 'boolean',
        'transfer_benefits_payable_indicator' => 'boolean',
    ];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }

    public function erpPerson()
    {
        return $this->hasOne(ErpPerson::class, 'erp_personnel_id');
    }

    public function accessCards()
    {
        return $this->hasMany(AccessCard::class, 'erp_personnel_id');
    }

    public function crafts()
    {
        return $this->hasMany(Craft::class, 'erp_personnel_id');
    }

    public function competencies()
    {
        return $this->hasMany(ErpCompetency::class, 'erp_personnel_id');
    }

    public function skills()
    {
        return $this->hasMany(Skill::class, 'erp_personnel_id');
    }

    public function organisations()
    {
        return $this->belongsToMany(ErpOrganisation::class,
            'personnel_organisation_roles',
            'erp_personnel_id',
            'erp_organisation_id'
        )->withPivot('role_type', 'start_date', 'end_date')
         ->withTimestamps();
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class,
            'personnel_location_assignments',
            'erp_personnel_id',
            'location_id'
        )->withPivot('assignment_type', 'start_date', 'end_date')
         ->withTimestamps();
    }
}