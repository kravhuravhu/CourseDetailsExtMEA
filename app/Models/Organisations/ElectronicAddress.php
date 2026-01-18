<?php

namespace App\Models\Organisations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectronicAddress extends Model
{
    use HasFactory;

    protected $table = 'electronic_addresses';
    
    protected $fillable = [
        'erp_organisation_id',
        'email',
        'lan',
        'password',
        'radio',
        'sequence_number',
        'usage',
        'user_id',
        'web',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
    ];

    protected $casts = [
        'sequence_number' => 'integer',
    ];

    public function erpOrganisation()
    {
        return $this->belongsTo(ErpOrganisation::class, 'erp_organisation_id');
    }
}