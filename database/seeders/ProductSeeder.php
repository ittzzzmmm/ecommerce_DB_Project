<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Premium Dog Food', 'Complete and balanced dry food for adult dogs.', 590, 50, 4.8, 120, 431],
            ['Cat Scratching Post', 'Durable scratching post for cats.', 450, 30, 4.6, 85, 260],
            ['Pet Feeding Bowl', 'Stainless steel feeding bowl for dogs and cats.', 250, 40, 4.5, 64, 512],
            ['Interactive Pet Toy', 'Interactive toy designed to keep pets active and entertained.', 320, 35, 4.9, 210, 980],
            ['Cozy Pet Bed', 'Soft and washable bed for small to medium pets.', 890, 12, 4.7, 98, 302],
            ['Cat Litter 10L', 'Clumping, low-dust cat litter with odor control.', 199, 80, 4.4, 150, 1240],
            ['Dog Leash & Collar Set', 'Adjustable nylon leash and collar set.', 350, 4, 4.3, 41, 188],
            ['Pet Shampoo', 'Gentle shampoo for sensitive skin.', 180, 60, 4.2, 37, 220],
            ['Bird Cage Large', 'Spacious cage with perches and feeding cups.', 1500, 6, 4.6, 22, 54],
            ['Aquarium Starter Kit', '20L tank with filter and LED light.', 1290, 0, 4.8, 19, 47],
            ['Cat Treats Tuna', 'Healthy tuna treats for cats.', 89, 120, 4.9, 302, 2150],
            ['Hamster Wheel', 'Silent spinner wheel for small pets.', 159, 25, 4.1, 28, 133],
        ];

        foreach ($items as $p) {
            Product::create([
                'name' => $p[0],
                'description' => $p[1],
                'price' => $p[2],
                'stock' => $p[3],
            ]);
        }
    }
}