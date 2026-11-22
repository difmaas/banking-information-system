<?php

namespace App\Modules\Lns\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanVerification extends Model
{
    protected $fillable = ['loan_id', 'officer_id', 'decision', 'reason'];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
