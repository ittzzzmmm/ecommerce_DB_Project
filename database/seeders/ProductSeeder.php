<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'Premium Dog Food',
                'Complete and balanced dry food for adult dogs.', 590, 50, 4.8, 120, 431,
                [
                    'https://tailybuddy.com/products/1325/chickenliver_02.jpg',
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQgpuA-SeLpwVCqvmPL9d8n8JfSXKSt0SY9OFCQW_hc1g&s=10',
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTcKJasKgOwUXwbqU8jg-1ZXCZD7tiKYbUj6DsWgpVd4Q&s=10',
                ],
            ],
            [
                'Cat Scratching Post',
                'Durable scratching post for cats.', 450, 30, 4.6, 85, 260,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS_OrGSbc97pmRiaYBp1wYv0dVVkVruyg228CO99XMF-A&s=10',
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTwygbJBjWiOegFtpkLm-B-XiJB1kZqtiJe66ZeFQ2MVQ&s=10',
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9EFGMa0Y9MIprFvlCa3NRcY7_aR33We6G0Hl7JkrTnQ&s=10',
                ],
            ],
            [
                'Pet Feeding Bowl',
                'Stainless steel feeding bowl for dogs and cats.', 250, 40, 4.5, 64, 512,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkqV9kaI0W9wD7_LekCzMbubk0XJbUWFv5XlstoXAeNQ&s=10',
                ],
            ],
            [
                'Interactive Pet Toy',
                'Interactive toy designed to keep pets active and entertained.', 320, 35, 4.9, 210, 980,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcReMBkvLuaoqh6ty72-ugkjYDWcBXsM1kcCnaXWuCKDzA&s=10',
                ],
            ],
            [
                'Cozy Pet Bed',
                'Soft and washable bed for small to medium pets.', 890, 12, 4.7, 98, 302,
                [
                    'https://www.onlinehomeshop.com/media/catalog/product/cache/1242x1242/p/e/pe000144_1s.jpg',
                ],
            ],
            [
                'Cat Litter 10L',
                'Clumping, low-dust cat litter with odor control.',199, 80, 4.4, 150, 1240,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcREXSdxVEt3ZojIRsAhPnQ4v5XUiOxIO34KozRHE4QY7g&s=10',
                ],
            ],
            [
                'Dog Leash & Collar Set',
                'Adjustable nylon leash and collar set.', 350, 4, 4.3, 41, 188,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpsAAxXiXOgNh6l2UXcQaPV1hGYOh0JaqDzfEIu6cdcA&s=10',
                ],
            ],
            [
                'Pet Shampoo',
                'Gentle shampoo for sensitive skin.', 180, 60, 4.2, 37, 220,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ1bdOnV2DsdV1-1CU3WQ8pJqYZtmTvmrISvs9tIqtjtg&s=10',
                ],
            ],
            [
                'Bird Cage Large',
                'Spacious cage with perches and feeding cups.', 1500, 6, 4.6, 22, 54,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQNzj3Jojf3Dsk0dWM4OsyiiIq4BTyqy2CIqKvZcS2L-w&s=10',
                ],
            ],
            [
                'Aquarium Starter Kit',
                '20L tank with filter and LED light.', 1290, 0, 4.8, 19, 47,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9TMhIgyLaOQDnlHWa9-fwxFSlf3sp0vQnfq2ix3Es4Q&s=10',
                ],
            ],
            [
                'Cat Treats Tuna',
                'Healthy tuna treats for cats.', 89, 120, 4.9, 302, 2150,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTKlHByzlxT5lJ_9FB7N8356Cg--s51qc4VZC7jskMMsA6jZHBgALXNje_&s=10',
                ],
            ],
            [
                'Hamster Wheel',
                'Silent spinner wheel for small pets.', 159, 25, 4.1, 28, 133,
                [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjevuE3vTPcKgm1UbGSzyUKOuQkWyLuxJO5qNupHUuZg&s=10',
                ],
            ],
        ];

        foreach ($items as $p) {
            $product = Product::create([
                'name' => $p[0],
                'description' => $p[1],
                'price' => $p[2],
                'stock' => $p[3],
                'image' => $p[7][0],
            ]);

            foreach (array_slice($p[7], 1) as $imageUrl) {
                $product->images()->create([
                    'image_path' => $imageUrl,
                ]);
            }
        }
    }
}