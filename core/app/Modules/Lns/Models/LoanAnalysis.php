<?php

namespace App\Modules\Lns\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanAnalysis extends Model
{
    protected $fillable = [
        'loan_id',
        'analyst_id',
        'collectibility_ok',
        'employment_ok',
        'income_ok',
        'credit_history_ok',
        'survey_result',
        'collateral',
        'recommendation',
    ];

    protected $casts = [
        'collectibility_ok' => 'boolean',
        'employment_ok' => 'boolean',
        'income_ok' => 'boolean',
        'credit_history_ok' => 'boolean',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
