<?php

namespace App\Modules\Lns\Models;

use App\Modules\Cis\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $fillable = [
        'customer_id',
        'loan_product_id',
        'amount',
        'tenor_months',
        'interest_rate',
        'status',
        'outstanding',
        'applied_at',
        'approved_at',
        'disbursed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'outstanding' => 'decimal:2',
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }

    public function akad(): HasOne
    {
        return $this->hasOne(Akad::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(LoanVerification::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(LoanAnalysis::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(LoanApproval::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
