<?php

namespace App\Models\Organisations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerData extends Model
{
    use HasFactory;

    protected $table = 'customer_data';
    
    protected $fillable = [
        'erp_organisation_id',
        'has_special_needs',
        'kind',
        'puc_number',
        'special_needs',
        'vip',
        'vip_description',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
    ];

    protected $casts = [
        'has_special_needs' => 'boolean',
        'vip' => 'boolean',
    ];

    public function erpOrganisation()
    {
        return $this->belongsTo(ErpOrganisation::class, 'erp_organisation_id');
    }
}