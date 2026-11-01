<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,   // ต้องมาก่อน ReviewSeeder (รีวิวได้เฉพาะคนที่ซื้อแล้ว)
            ReviewSeeder::class,
        ]);
    }
}
