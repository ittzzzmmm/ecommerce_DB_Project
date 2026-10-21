<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
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
        }
    }
}