<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'contact' => '+1234567890',
            'address' => '123 Main Street, City',
            'role' => 'customer',
        ]);

        // Create sample products
        $products = [
            [
                'product_name' => 'iPhone 15 Pro Max',
                'description' => 'Latest Apple flagship phone with advanced camera system',
                'price' => 119999,
                'image_path' => 'https://via.placeholder.com/300?text=iPhone+15+Pro+Max',
                'stock' => 50,
            ],
            [
                'product_name' => 'iPhone 15 Pro',
                'description' => 'Premium iPhone with Pro features',
                'price' => 99999,
                'image_path' => 'https://via.placeholder.com/300?text=iPhone+15+Pro',
                'stock' => 40,
            ],
            [
                'product_name' => 'iPhone 15',
                'description' => 'Standard iPhone 15 model',
                'price' => 79999,
                'image_path' => 'https://via.placeholder.com/300?text=iPhone+15',
                'stock' => 60,
            ],
            [
                'product_name' => 'Logitech G304',
                'description' => 'Lightweight gaming mouse with excellent tracking',
                'price' => 2999,
                'image_path' => 'https://via.placeholder.com/300?text=Logitech+G304',
                'stock' => 100,
            ],
            [
                'product_name' => 'RK61 Keyboard',
                'description' => 'Mechanical keyboard with RGB lighting',
                'price' => 4999,
                'image_path' => 'https://via.placeholder.com/300?text=RK61+Keyboard',
                'stock' => 75,
            ],
            [
                'product_name' => 'Ergonomic Chair',
                'description' => 'Comfortable office chair for long work sessions',
                'price' => 12999,
                'image_path' => 'https://via.placeholder.com/300?text=Ergonomic+Chair',
                'stock' => 30,
            ],
            [
                'product_name' => 'Standing Desk',
                'description' => 'Adjustable height standing desk',
                'price' => 24999,
                'image_path' => 'https://via.placeholder.com/300?text=Standing+Desk',
                'stock' => 25,
            ],
            [
                'product_name' => 'Desk Lamp',
                'description' => 'LED desk lamp with adjustable brightness',
                'price' => 1999,
                'image_path' => 'https://via.placeholder.com/300?text=Desk+Lamp',
                'stock' => 80,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
