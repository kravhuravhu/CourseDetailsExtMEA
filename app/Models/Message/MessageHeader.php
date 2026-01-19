<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageHeader extends Model
{
    use HasFactory;

    protected $table = 'MESSAGE_HEADER';
    protected $primaryKey = 'MESSAGE_HEADER_ID';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'VERB',
        'NOUN',
        'REVISION',
        'CONTEXT',
        'ORIGINAL_EVENT_DATE_TIME',
        'SOURCE',
        'DESTINATION',
        'ASYNC_REPLY_FLAG',
        'REPLY_ADDRESS',
        'ACK_REQUIRED',
        'MESSAGE_ID',
        'COMMENT',
    ];

    protected $casts = [
        'ORIGINAL_EVENT_DATE_TIME' => 'datetime',
        'ASYNC_REPLY_FLAG' => 'boolean',
        'ACK_REQUIRED' => 'boolean',
    ];

    /**
     * Get the message properties for this header.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(MessageProperty::class, 'MESSAGE_HEADER_ID', 'MESSAGE_HEADER_ID');
    }

    /**
     * Scope a query to filter by message ID.
     */
    public function scopeByMessageId($query, $messageId)
    {
        return $query->where('MESSAGE_ID', $messageId);
    }

    /**
     * Scope a query to filter by noun.
     */
    public function scopeByNoun($query, $noun)
    {
        return $query->where('NOUN', $noun);
    }

    /**
     * Scope a query to filter by verb.
     */
    public function scopeByVerb($query, $verb)
    {
        return $query->where('VERB', $verb);
    }
}