<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skills';
    
    protected $fillable = [
        'erp_personnel_id',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
        'category',
        'created_date_time',
        'doc_remarks',
        'doc_status',
        'doc_status_date',
        'last_modified_date_time',
        'revision_number',
        'subject',
        'title',
    ];

    protected $casts = [
        'created_date_time' => 'datetime',
        'doc_status_date' => 'datetime',
        'last_modified_date_time' => 'datetime',
    ];

    public function erpPersonnel()
    {
        return $this->belongsTo(ErpPersonnel::class, 'erp_personnel_id');
    }
}