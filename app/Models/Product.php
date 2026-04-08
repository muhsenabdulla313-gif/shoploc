<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

protected $fillable = [
'category_id',
'name',
'slug',
'price',
'original_price',
'badge',
'trend_type',
'rating',
'shipping_charge',
'description',
'status'
];

public function variants()
{
return $this->hasMany(ProductVariant::class);
}

public function images()
{
return $this->hasMany(ProductImage::class);
}

public function category()
{
return $this->belongsTo(Category::class);
}

public function colors()
{
    return $this->belongsToMany(Color::class, 'product_variants')
        ->withPivot('size', 'stock')
        ->distinct();
}
public function imagesByColor($color_id)
{
    return $this->images()->where('color_id', $color_id);
}

}
