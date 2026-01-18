<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessCard extends Model
{
    use HasFactory;

    protected $table = 'access_cards';
    
    protected $fillable = [
        'erp_personnel_id',
        'access_type',
        'application_date',
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
        'end_date_time',
        'scope_information',
        'sign_date',
        'start_date_time',
    ];

    protected $casts = [
        'application_date' => 'date',
        'created_date_time' => 'datetime',
        'doc_status_date' => 'datetime',
        'last_modified_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'sign_date' => 'date',
        'start_date_time' => 'datetime',
    ];

    public function erpPersonnel()
    {
        return $this->belongsTo(ErpPersonnel::class, 'erp_personnel_id');
    }
}