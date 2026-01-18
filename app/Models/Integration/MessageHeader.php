<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageHeader extends Model
{
    use HasFactory;

    protected $table = 'message_headers';
    
    protected $fillable = [
        'verb',
        'noun',
        'revision',
        'context',
        'original_event_date_time',
        'source',
        'destination',
        'async_reply_flag',
        'reply_address',
        'ack_required',
        'message_id',
        'comment',
    ];

    protected $casts = [
        'original_event_date_time' => 'datetime',
        'async_reply_flag' => 'boolean',
        'ack_required' => 'boolean',
    ];

    public function properties()
    {
        return $this->hasMany(MessageProperty::class, 'message_header_id');
    }

    public function userInfos()
    {
        return $this->hasMany(UserInfo::class, 'message_header_id');
    }
}