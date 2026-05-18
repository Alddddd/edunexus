<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistanceProgram extends Model
{
    protected $fillable = [
    'program_name',
    'description',
    'merchant_category',
    'maximum_amount',
    'expiration_days',
    'status',
    'created_by',
];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
