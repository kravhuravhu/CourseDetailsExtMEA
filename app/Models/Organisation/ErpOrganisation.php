<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ErpOrganisation extends Model
{
    use HasFactory;

    protected $table = 'ERP_ORGANISATION';
    protected $primaryKey = 'ERP_ORGANISATION_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'ORGANISATIONS_COLLECTION_ID',
        'MRID',
        'ALIAS_NAME',
        'DESCRIPTION',
        'LOCAL_NAME',
        'NAME',
        'PATH_NAME',
        'BEE_RATING',
        'CATEGORY',
        'CODE',
        'COMPANY_REGISTRATION_NO',
        'GOVERNMENT_ID',
        'INDUSTRY_ID',
        'IS_COST_CENTER',
        'IS_PROFIT_CENTER',
        'MODE',
        'OPT_OUT',
        'VALUE_ADDED_TAX_ID',
    ];

    protected $casts = [
        'IS_COST_CENTER' => 'boolean',
        'IS_PROFIT_CENTER' => 'boolean',
        'OPT_OUT' => 'boolean',
    ];

    /**
     * Get the organisations collection that owns this ERP organisation.
     */
    public function organisationsCollection(): BelongsTo
    {
        return $this->belongsTo(OrganisationsCollection::class, 'ORGANISATIONS_COLLECTION_ID', 'ORGANISATIONS_COLLECTION_ID');
    }

    /**
     * Get the customer data associated with the organisation.
     */
    public function customerData(): HasOne
    {
        return $this->hasOne(CustomerData::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }

    /**
     * Get the supplier data associated with the organisation.
     */
    public function supplierData(): HasOne
    {
        return $this->hasOne(SupplierData::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }

    /**
     * Get the electronic addresses for this organisation.
     */
    public function electronicAddresses(): HasMany
    {
        return $this->hasMany(ElectronicAddress::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }

    /**
     * Get the telephone numbers for this organisation.
     */
    public function telephoneNumbers(): HasMany
    {
        return $this->hasMany(ErpTelephoneNumber::class, 'ERP_ORGANISATION_ID', 'ERP_ORGANISATION_ID');
    }

    /**
     * Get the personnel associated with this organisation.
     */
    public function personnel(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Personnel\Personnel::class,
            'PERSONNEL_ORGANISATION_LINK',
            'ORGANISATION_ID',
            'PERSONNEL_ID'
        )->withPivot('RELATIONSHIP_TYPE');
    }

    /**
     * Get the locations associated with this organisation.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Location\Location::class,
            'ORGANISATION_LOCATION_LINK',
            'ORGANISATION_ID',
            'LOCATION_ID'
        )->withPivot('RELATIONSHIP_TYPE');
    }

    /**
     * Scope a query to filter by MRID.
     */
    public function scopeByMrid($query, $mrid)
    {
        return $query->where('MRID', $mrid);
    }

    /**
     * Scope a query to search by name or code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('NAME', 'LIKE', "%{$search}%")
                    ->orWhere('CODE', 'LIKE', "%{$search}%")
                    ->orWhere('COMPANY_REGISTRATION_NO', 'LIKE', "%{$search}%");
    }
}