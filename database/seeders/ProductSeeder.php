<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Premium Dog Food',
            'description' => 'Complete and balanced dry food for adult dogs.',
            'price' => 590.00,
            'stock' => 50,
            'image' => 'products/dog-food.jpg',
        ]);

        Product::create([
            'name' => 'Cat Scratching Post',
            'description' => 'Durable scratching post for cats.',
            'price' => 450.00,
            'stock' => 30,
            'image' => 'products/cat-scratching-post.jpg',
        ]);

        Product::create([
            'name' => 'Pet Feeding Bowl',
            'description' => 'Stainless steel feeding bowl for dogs and cats.',
            'price' => 250.00,
            'stock' => 40,
            'image' => 'products/pet-bowl.jpg',
        ]);

        Product::create([
            'name' => 'Interactive Pet Toy',
            'description' => 'Interactive toy designed to keep pets active and entertained.',
            'price' => 320.00,
            'stock' => 35,
            'image' => 'products/pet-toy.jpg',
        ]);
    }
}
