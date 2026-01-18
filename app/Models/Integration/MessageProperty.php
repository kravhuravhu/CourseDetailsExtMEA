<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageProperty extends Model
{
    use HasFactory;

    protected $table = 'message_properties';
    
    protected $fillable = [
        'message_header_id',
        'name',
        'value',
    ];

    public function messageHeader()
    {
        return $this->belongsTo(MessageHeader::class, 'message_header_id');
    }
}