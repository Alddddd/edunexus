<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
 protected $fillable = [
    'assistance_request_id',
    'merchant_id',
    'amount',
    'status',
    'settled_at',
];

protected function casts(): array
{
    return [
        'settled_at' => 'datetime',
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

}
