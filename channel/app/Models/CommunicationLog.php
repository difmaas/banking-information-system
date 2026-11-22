<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'direction',
        'service',
        'method',
        'path',
        'status_code',
        'duration_ms',
        'user_id',
        'idempotency_key',
        'request',
        'response',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'request' => 'array',
        'response' => 'array',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
