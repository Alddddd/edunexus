<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    public function merchants()
    {
        return $this->hasMany(MerchantProfile::class);
    }

    public function assistancePrograms()
    {
        return $this->hasMany(AssistanceProgram::class);
    }
}
