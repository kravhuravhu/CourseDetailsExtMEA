<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageProperty extends Model
{
    use HasFactory;

    protected $table = 'MESSAGE_PROPERTY';
    protected $primaryKey = 'MESSAGE_PROPERTY_ID';

    public $timestamps = false;
    const CREATED_AT = 'CREATED_AT';

    protected $fillable = [
        'MESSAGE_HEADER_ID',
        'NAME',
        'VALUE',
    ];

    /**
     * Get the message header that owns the property.
     */
    public function messageHeader(): BelongsTo
    {
        return $this->belongsTo(MessageHeader::class, 'MESSAGE_HEADER_ID', 'MESSAGE_HEADER_ID');
    }
}