<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    // ✅ DB has created_at & updated_at (snake_case)
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // ✅ MUST be true so Laravel auto fills createdAt / updatedAt
    public $timestamps = true;

    protected $fillable = [
        'name',
        'category',
        'subcategory',
        'price',
        'shipping_charge',
        'original_price',
        'stock',
        'colors',
        'sizes',
        'size_prices',
        'description',
        'image',
        'gallery_images',
        'badge',
        'status',
        'trend_type',
        'rating',
    ];

    protected $casts = [
        'colors' => 'array',
        'sizes' => 'array',
        'size_prices' => 'array',
        'gallery_images' => 'array',
        'price' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'original_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
