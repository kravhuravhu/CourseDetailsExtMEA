<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ErpPerson extends Model
{
    use HasFactory;

    protected $table = 'erp_persons';
    
    protected $fillable = [
        'erp_personnel_id',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
        'birth_date_time',
        'category',
        'death_date_time',
        'ethnicity',
        'first_name',
        'gender',
        'initials',
        'last_name',
        'maiden_name',
        'marital_status',
        'marriage_type',
        'm_name',
        'nationality',
        'nickname',
        'prefix',
        'special_needs',
        'suffix',
    ];

    protected $casts = [
        'birth_date_time' => 'datetime',
        'death_date_time' => 'datetime',
    ];

    public function erpPersonnel()
    {
        return $this->belongsTo(ErpPersonnel::class, 'erp_personnel_id');
    }
}