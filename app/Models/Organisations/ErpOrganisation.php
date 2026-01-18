<?php

namespace App\Models\Organisations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Personnel\ErpPersonnel;

class ErpOrganisation extends Model
{
    use HasFactory;

    protected $table = 'erp_organisations';
    
    protected $fillable = [
        'organisations_collection_id',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
        'bee_rating',
        'category',
        'code',
        'company_registration_no',
        'government_id',
        'industry_id',
        'is_cost_center',
        'is_profit_center',
        'mode',
        'opt_out',
        'value_added_tax_id',
    ];

    protected $casts = [
        'is_cost_center' => 'boolean',
        'is_profit_center' => 'boolean',
        'opt_out' => 'boolean',
    ];

    public function organisationsCollection()
    {
        return $this->belongsTo(OrganisationsCollection::class, 'organisations_collection_id');
    }

    public function electronicAddresses()
    {
        return $this->hasMany(ElectronicAddress::class, 'erp_organisation_id');
    }

    public function customerData()
    {
        return $this->hasOne(CustomerData::class, 'erp_organisation_id');
    }

    public function supplierData()
    {
        return $this->hasOne(SupplierData::class, 'erp_organisation_id');
    }

    public function personnel()
    {
        return $this->belongsToMany(ErpPersonnel::class,
            'personnel_organisation_roles',
            'erp_organisation_id',
            'erp_personnel_id'
        )->withPivot('role_type', 'start_date', 'end_date')
         ->withTimestamps();
    }
}