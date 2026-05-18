<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\AssistanceProgram;

class AssistanceRequest extends Model
{
   protected $fillable = [
    'member_id',
    'program_id',
    'requested_amount',
    'approved_amount',
    'status',
    'approval_date',
    'expiration_date',
    'reference_code',
    'qr_code',
    'approved_by',
    'reason',
    'is_claimed',
    'claimed_at',
    'claimed_by',
    'claim_status',
];

public function member()
{
    return $this->belongsTo(User::class, 'member_id');
}

public function program()
{
    return $this->belongsTo(AssistanceProgram::class, 'program_id');
}

protected function casts(): array
{
    return [
        'approval_date' => 'datetime',
        'expiration_date' => 'datetime',
        'claimed_at' => 'datetime',
        'is_claimed' => 'boolean',
    ];
}

}
