<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ออเดอร์ตัวอย่าง (สถานะ completed) — ต้องรันก่อน ReviewSeeder
 * เพราะกติการีวิวคือ "ต้องเคยซื้อสินค้านั้นก่อน" และยอด sold คำนวณจาก order_items
 * ⇒ ทุกคู่ (user, สินค้า) ใน ReviewSeeder ต้องมีอยู่ในรายการนี้ ไม่งั้นจะได้ sold < จำนวนรีวิว
 */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // seed ครั้งเดียว (ใช้กับ migrate:fresh --seed) กันออเดอร์ซ้ำถ้ารันซ้ำ
        if (Order::query()->exists()) {
            return;
        }

        // [email, วันที่สั่ง (กี่วันก่อน), ที่อยู่, [[ชื่อสินค้า, จำนวน], ...]]
        $orders = [
            ['admin1@example.com', 20, '12 Sukhumvit Rd, Khlong Toei, Bangkok 10110', [['Premium Dog Food', 2], ['Pet Shampoo', 1]]],
            ['ten@example.com', 18, '88 Nimmanhaemin Rd, Suthep, Chiang Mai 50200', [['Premium Dog Food', 1], ['Cat Litter 10L', 3]]],
            ['ten@example.com', 6, '88 Nimmanhaemin Rd, Suthep, Chiang Mai 50200', [['Cat Treats Tuna', 4]]],
            ['yu@example.com', 15, '5 Huay Kaew Rd, Mueang, Chiang Mai 50300', [['Cat Scratching Post', 1], ['Cat Treats Tuna', 2]]],
            ['beam@example.com', 12, '199 Rama IX Rd, Huai Khwang, Bangkok 10310', [['Pet Feeding Bowl', 2], ['Cozy Pet Bed', 1]]],
            ['beam@example.com', 4, '199 Rama IX Rd, Huai Khwang, Bangkok 10310', [['Hamster Wheel', 1]]],
            ['it@example.com', 10, '7 Mittraphap Rd, Mueang, Khon Kaen 40000', [['Interactive Pet Toy', 2], ['Dog Leash & Collar Set', 1]]],
            ['admin2@example.com', 8, '45 Charoen Krung Rd, Bang Rak, Bangkok 10500', [['Interactive Pet Toy', 1], ['Bird Cage Large', 1]]],
        ];

        $products = Product::all()->keyBy('name');

        // เรียงจากเก่าสุด → ใหม่สุด ให้เลขออเดอร์ (id) เพิ่มตามเวลาเหมือนร้านจริง
        $orders = collect($orders)->sortByDesc(fn ($order) => $order[1])->values()->all();

        foreach ($orders as [$email, $daysAgo, $address, $items]) {
            $user = User::where('email', $email)->first();
            if (! $user) {
                continue;
            }

            $lines = collect($items)
                ->filter(fn ($item) => $products->has($item[0]))
                ->map(fn ($item) => [
                    'product' => $products[$item[0]],
                    'quantity' => $item[1],
                    'subtotal' => round((float) $products[$item[0]]->price * $item[1], 2),
                ]);
            if ($lines->isEmpty()) {
                continue;
            }

            $total = $lines->sum('subtotal');
            $date = now()->subDays($daysAgo);

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'discount_amount' => 0,
                'final_amount' => $total,
                'status' => 'completed',
                'shipping_address' => $address,
            ]);
            $order->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['product']->price,
                    'subtotal' => $line['subtotal'],
                ]);
            }
        }
    }
}
