<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementPayout extends Model
{
    protected $fillable = [
        'settlement_id',
        'settlement_reference',
        'payout_type',
        'amount',
        'settlement_total',
        'total_released_after',
        'remaining_balance_after',
        'payout_channel',
        'settlement_rail',
        'network',
        'payout_account_name_used',
        'payout_account_number_used',
        'payout_qr_used',
        'payout_notes_used',
        'transaction_hash',
        'blockchain_status',
        'proof_hash',
        'metadata',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'settlement_total' => 'decimal:2',
            'total_released_after' => 'decimal:2',
            'remaining_balance_after' => 'decimal:2',
            'metadata' => 'array',
            'released_at' => 'datetime',
        ];
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }
}
