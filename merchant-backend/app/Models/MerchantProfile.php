<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantProfile extends Model
{
    protected $fillable = [
        'user_id',
        'merchant_name',
        'username',
        'contact_name',
        'contact_phone',
        'shop_name',
        'invite_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}