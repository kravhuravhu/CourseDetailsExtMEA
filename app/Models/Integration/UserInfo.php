<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserInfo extends Model
{
    use HasFactory;

    protected $table = 'user_infos';
    
    protected $fillable = [
        'message_header_id',
        'user_id',
        'organization',
    ];

    public function messageHeader()
    {
        return $this->belongsTo(MessageHeader::class, 'message_header_id');
    }
}