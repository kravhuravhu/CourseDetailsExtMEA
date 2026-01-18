<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Craft extends Model
{
    use HasFactory;

    protected $table = 'crafts';
    
    protected $fillable = [
        'erp_personnel_id',
        'category',
        'mrid',
        'alias_name',
        'description',
        'local_name',
        'name',
        'path_name',
    ];

    public function erpPersonnel()
    {
        return $this->belongsTo(ErpPersonnel::class, 'erp_personnel_id');
    }
}