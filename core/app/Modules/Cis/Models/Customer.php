<?php

namespace App\Modules\Cis\Models;

use App\Modules\Lns\Models\Loan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'channel_user_id',
        'nik',
        'name',
        'email',
        'phone',
        'address',
        'occupation',
        'monthly_income',
        'collectibility',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function creditHistories(): HasMany
    {
        return $this->hasMany(CreditHistory::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
