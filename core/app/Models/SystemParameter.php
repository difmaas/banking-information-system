<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemParameter extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    public static function value(string $key, mixed $default = null): mixed
    {
        $parameter = static::query()->where('key', $key)->first();

        return $parameter?->value ?? $default;
    }
}
