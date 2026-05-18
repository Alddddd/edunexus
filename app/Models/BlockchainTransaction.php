<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockchainTransaction extends Model
{
    
   protected $fillable = [
    'transaction_type',
    'reference_id',
    'reference_code',
    'transaction_hash',
    'blockchain_status',
    'payload',
    'recorded_at',
];

protected function casts(): array
{
    return [
        'recorded_at' => 'datetime',
    ];
}

}
