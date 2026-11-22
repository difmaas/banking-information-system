<?php

namespace App\Modules\Cis\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditHistory extends Model
{
    protected $fillable = [
        'customer_id',
        'institution',
        'loan_type',
        'amount',
        'status',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
