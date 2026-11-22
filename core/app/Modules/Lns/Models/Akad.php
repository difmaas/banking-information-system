<?php

namespace App\Modules\Lns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Akad extends Model
{
    protected $fillable = [
        'loan_id',
        'signer_name',
        'signed_at',
        'signature_data',
        'note',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
