<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
 protected $fillable = [
    'assistance_request_id',
    'merchant_id',
    'amount',
    'total_released',
    'remaining_balance',
    'status',
    'settled_at',
    'last_released_at',
];

protected function casts(): array
{
    return [
        'amount' => 'decimal:2',
        'total_released' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'settled_at' => 'datetime',
        'last_released_at' => 'datetime',
    ];
}

public function assistanceRequest()
{
    return $this->belongsTo(\App\Models\AssistanceRequest::class);
}

public function merchant()
{
    return $this->belongsTo(\App\Models\User::class, 'merchant_id');
}

public function payouts()
{
    return $this->hasMany(\App\Models\SettlementPayout::class);
}

public function getComputedTotalReleasedAttribute(): float
{
    return (float) ($this->total_released ?? 0);
}

public function getComputedRemainingBalanceAttribute(): float
{
    if ($this->status === 'Pending' && (float) ($this->total_released ?? 0) === 0.0 && (float) ($this->remaining_balance ?? 0) === 0.0) {
        return (float) $this->amount;
    }

    if ($this->remaining_balance !== null) {
        return max((float) $this->remaining_balance, 0);
    }

    return max((float) $this->amount - $this->computed_total_released, 0);
}

}
