<?php

namespace Database\Seeders;

use App\Http\Controllers\ReviewController;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            ['admin1@example.com', 'Premium Dog Food', 5, 'My dog loves this food, and the ingredients look great.'],
            ['ten@example.com', 'Premium Dog Food', 4, 'Good quality and arrived in good condition.'],
            ['yu@example.com', 'Cat Scratching Post', 5, 'Sturdy, easy to assemble, and my cat uses it every day.'],
            ['beam@example.com', 'Pet Feeding Bowl', 4, 'A solid bowl that is easy to clean.'],
            ['it@example.com', 'Interactive Pet Toy', 5, 'Keeps my pet busy and active.'],
            ['admin2@example.com', 'Interactive Pet Toy', 4, 'Fun toy and good value for the price.'],
            ['ten@example.com', 'Cat Litter 10L', 5, 'Clumps well and controls odor.'],
            ['beam@example.com', 'Cozy Pet Bed', 5, 'Soft, comfortable, and easy to wash.'],
        ];

        foreach ($reviews as [$email, $productName, $rating, $comment]) {
            $user = User::where('email', $email)->first();
            $product = Product::where('name', $productName)->first();

            if (!$user || !$product) {
                continue;
            }

            // กติกาเดียวกับหน้าเว็บ: ต้องเคยซื้อก่อนถึงรีวิวได้ (ออเดอร์มาจาก OrderSeeder)
            // ⇒ sold ของสินค้าจะไม่มีทางน้อยกว่าจำนวนรีวิว
            if (!ReviewController::hasPurchased($user, $product)) {
                $this->command?->warn("Skip review: {$email} has not purchased {$productName} (add it to OrderSeeder).");
                continue;
            }

            Review::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ],
                [
                    'rating' => $rating,
                    'comment' => $comment,
                ],
            );
        }
    }
}
