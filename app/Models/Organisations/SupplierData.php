<?php

namespace App\Models\Organisations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierData extends Model
{
    use HasFactory;

    protected $table = 'supplier_data';
    
    protected $fillable = [
        'erp_organisation_id',
        'issuer_identification_number',
        'kind',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
    ];

    public function erpOrganisation()
    {
        return $this->belongsTo(ErpOrganisation::class, 'erp_organisation_id');
    }
}