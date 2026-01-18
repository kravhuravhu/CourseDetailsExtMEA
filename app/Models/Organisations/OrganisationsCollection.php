<?php

namespace App\Models\Organisations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganisationsCollection extends Model
{
    use HasFactory;

    protected $table = 'organisations_collections';
    
    protected $fillable = [];

    public function erpOrganisations()
    {
        return $this->hasMany(ErpOrganisation::class, 'organisations_collection_id');
    }
}