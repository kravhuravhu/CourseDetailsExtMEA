<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ErpCompetency extends Model
{
    use HasFactory;

    protected $table = 'erp_competencies';
    
    protected $fillable = [
        'erp_personnel_id',
        'proficiency_level',
        'related_experience_years',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
    ];

    protected $casts = [
        'related_experience_years' => 'decimal:2',
    ];

    public function erpPersonnel()
    {
        return $this->belongsTo(ErpPersonnel::class, 'erp_personnel_id');
    }
}