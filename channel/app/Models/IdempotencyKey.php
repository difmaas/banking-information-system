<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdempotencyKey extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'key',
        'method',
        'path',
        'status_code',
        'response',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
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
