<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_percent',
        'start_at',
        'expire_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'start_at' => 'datetime',
        'expire_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}