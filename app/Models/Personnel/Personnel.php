<?php

namespace App\Models\Personnel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnel';
    
    protected $fillable = [];

    public function erpPersonnel()
    {
        return $this->hasOne(ErpPersonnel::class, 'personnel_id');
    }
}