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
                    'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?w=600',
                    'https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=600',
                ],
            ],
            [
                'Cat Scratching Post',
                'Durable scratching post for cats.', 450, 30, 4.6, 85, 260,
                [
                    'https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=600',
                    'https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=600',
                ],
            ],
            [
                'Pet Feeding Bowl',
                'Stainless steel feeding bowl for dogs and cats.', 250, 40, 4.5, 64, 512,
                [
                    'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?w=600',
                ],
            ],
            [
                'Interactive Pet Toy',
                'Interactive toy designed to keep pets active and entertained.', 320, 35, 4.9, 210, 980,
                [
                    'https://images.unsplash.com/photo-1535294435445-d7249524ef2e?w=600',
                ],
            ],
            [
                'Cozy Pet Bed',
                'Soft and washable bed for small to medium pets.', 890, 12, 4.7, 98, 302,
                [
                    'https://images.unsplash.com/photo-1541599540903-216a46ca1dc0?w=600',
                ],
            ],
            [
                'Cat Litter 10L',
                'Clumping, low-dust cat litter with odor control.',199, 80, 4.4, 150, 1240,
                [
                    'https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=600',
                ],
            ],
            [
                'Dog Leash & Collar Set',
                'Adjustable nylon leash and collar set.', 350, 4, 4.3, 41, 188,
                [
                    'https://images.unsplash.com/photo-1551717743-49959800b1f6?w=600',
                ],
            ],
            [
                'Pet Shampoo',
                'Gentle shampoo for sensitive skin.', 180, 60, 4.2, 37, 220,
                [
                    'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=600',
                ],
            ],
            [
                'Bird Cage Large',
                'Spacious cage with perches and feeding cups.', 1500, 6, 4.6, 22, 54,
                [
                    'https://images.unsplash.com/photo-1552728089-57bdde30beb3?w=600',
                ],
            ],
            [
                'Aquarium Starter Kit',
                '20L tank with filter and LED light.', 1290, 0, 4.8, 19, 47,
                [
                    'https://images.unsplash.com/photo-1524704654690-b56c05c78a00?w=600',
                ],
            ],
            [
                'Cat Treats Tuna',
                'Healthy tuna treats for cats.', 89, 120, 4.9, 302, 2150,
                [
                    'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=600',
                ],
            ],
            [
                'Hamster Wheel',
                'Silent spinner wheel for small pets.', 159, 25, 4.1, 28, 133,
                [
                    'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?w=600',
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
