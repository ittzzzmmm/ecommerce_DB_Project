<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'discount_percent' => 10.00,
            'start_at' => now(),
            'expire_at' => now()->addMonths(3),
            'usage_limit' => 100,
            'used_count' => 0,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SALE20',
            'discount_percent' => 20.00,
            'start_at' => now(),
            'expire_at' => now()->addMonth(),
            'usage_limit' => 50,
            'used_count' => 0,
            'is_active' => true,
        ]);
    }
}