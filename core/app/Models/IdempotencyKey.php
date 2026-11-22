<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    protected $fillable = [
        'service',
        'key',
        'method',
        'path',
        'status_code',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];
}
