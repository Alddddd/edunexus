<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantProfile extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'merchant_category',
        'contact_number',
        'address',
        'payout_account_name',
        'payout_account_number',
        'payout_qr',
        'payout_notes',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
