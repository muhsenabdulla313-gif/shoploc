<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = [
            ['name' => 'Trendy', 'slug' => 'trendy'],
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Clothing', 'slug' => 'clothing'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden'],
            ['name' => 'Sports', 'slug' => 'sports'],
            ['name' => 'Books', 'slug' => 'books'],
            ['name' => 'Beauty', 'slug' => 'beauty'],
            ['name' => 'Toys', 'slug' => 'toys'],
            ['name' => 'Health', 'slug' => 'health'],
            ['name' => 'Automotive', 'slug' => 'automotive'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $categoryNames = ['Trendy', 'Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books', 'Beauty', 'Toys', 'Health', 'Automotive'];
        
        foreach ($categoryNames as $name) {
            \App\Models\Category::where('name', $name)->delete();
        }
    }
};
