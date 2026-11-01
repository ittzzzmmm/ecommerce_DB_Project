<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
=======
use Illuminate\Database\Seeder;
use App\Models\Product;
>>>>>>> origin/frontend/shop-pages

class ProductSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        $products = [
            [
                'name' => 'Premium Dog Food',
                'description' => 'Complete and balanced dry food for adult dogs.',
                'price' => 590.00,
                'stock' => 50,
                'image' => 'products/dog-food.jpg',
                'images' => [
                    'products/dog-food-2.jpg',
                    'products/dog-food-3.jpg',
                ],
            ],
            [
                'name' => 'Cat Scratching Post',
                'description' => 'Durable scratching post for cats.',
                'price' => 450.00,
                'stock' => 30,
                'image' => 'products/cat-scratching-post.jpg',
                'images' => [
                    'products/cat-scratching-post-2.jpg',
                    'products/cat-scratching-post-3.jpg',
                ],
            ],
            [
                'name' => 'Pet Feeding Bowl',
                'description' => 'Stainless steel feeding bowl for dogs and cats.',
                'price' => 250.00,
                'stock' => 40,
                'image' => 'products/pet-bowl.jpg',
                'images' => [
                    'products/pet-bowl-2.jpg',
                    'products/pet-bowl-3.jpg',
                ],
            ],
            [
                'name' => 'Interactive Pet Toy',
                'description' => 'Interactive toy designed to keep pets active and entertained.',
                'price' => 320.00,
                'stock' => 35,
                'image' => 'products/pet-toy.jpg',
                'images' => [
                    'products/pet-toy-2.jpg',
                    'products/pet-toy-3.jpg',
                ],
            ],
        ];

        foreach ($products as $data) {
            $images = $data['images'];
            unset($data['images']);

            $product = Product::create($data);

            foreach ($images as $imagePath) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                ]);
            }
=======
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
>>>>>>> origin/frontend/shop-pages
        }
    }
}