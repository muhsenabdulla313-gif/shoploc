<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Premium Cotton T-Shirt',
                'category' => 'men',
                'subcategory' => 't-shirts',
                'price' => 29.99,
                'original_price' => 39.99,
                'stock' => 50,
                'colors' => json_encode(['red', 'blue', 'black']),
                'sizes' => json_encode(['m', 'l', 'xl']),
                'description' => 'Comfortable cotton t-shirt for everyday wear',
                'image' => null,
                'gallery_images' => null,
                'status' => 'active',
                'trend_type' => 'hot-trend',
                'rating' => 4.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Summer Dress',
                'category' => 'women',
                'subcategory' => 'dresses',
                'price' => 49.99,
                'original_price' => 69.99,
                'stock' => 25,
                'colors' => json_encode(['white', 'pink', 'yellow']),
                'sizes' => json_encode(['s', 'm', 'l']),
                'description' => 'Lightweight summer dress perfect for warm weather',
                'image' => null,
                'gallery_images' => null,
                'status' => 'active',
                'trend_type' => 'best-seller',
                'rating' => 4.8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kids Sneakers',
                'category' => 'kids',
                'subcategory' => 'shoes',
                'price' => 39.99,
                'original_price' => null,
                'stock' => 30,
                'colors' => json_encode(['red', 'blue', 'green']),
                'sizes' => json_encode(['3', '4', '5']),
                'description' => 'Comfortable sneakers for active kids',
                'image' => null,
                'gallery_images' => null,
                'status' => 'active',
                'trend_type' => 'featured',
                'rating' => 4.2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}