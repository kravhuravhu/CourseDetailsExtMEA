<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganisationsCollection extends Model
{
    use HasFactory;

    protected $table = 'ORGANISATIONS_COLLECTION';
    protected $primaryKey = 'ORGANISATIONS_COLLECTION_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [];

    /**
     * Get the ERP organisations for this collection.
     */
    public function erpOrganisations(): HasMany
    {
        return $this->hasMany(ErpOrganisation::class, 'ORGANISATIONS_COLLECTION_ID', 'ORGANISATIONS_COLLECTION_ID');
    }
}