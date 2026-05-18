<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'title',
        'description',
        'reference_type',
        'reference_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}