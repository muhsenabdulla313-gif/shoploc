<?php

namespace App\Http\Controllers;
use App\Models\Category;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{


    public function products()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return view('products', compact('products'));
    }

    public function shop(Request $request)
    {
        $query = $request->input('search');

        $products = Product::with('category.parent')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'LIKE', "%{$query}%")

                        // Search category name
                        ->orWhereHas('category', function ($q2) use ($query) {
                            $q2->where('name', 'LIKE', "%{$query}%");
                        })

                        // Search parent category name
                        ->orWhereHas('category.parent', function ($q3) use ($query) {
                            $q3->where('name', 'LIKE', "%{$query}%");
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('shop', compact('products'));
    }

    public function trendy()
    {
        $trendyProducts = Product::where('status', 'active')
            ->whereNotNull('trend_type')
            ->orderByRaw("CASE
                WHEN trend_type = 'best-seller' THEN 1
                WHEN trend_type = 'hot-trend' THEN 2
                WHEN trend_type = 'featured' THEN 3
                ELSE 4
            END")
            ->get();

        return view('trendy', compact('trendyProducts'));
    }

    public function show($id)
    {
        $product = Product::with([
            'colors.images',
            'variants'
        ])->findOrFail($id);



        $isLoggedIn = auth()->check();

        return view('product-details', compact('product', 'isLoggedIn'));
    }

    public function women()
    {
        $excludedCategories = ['men', 'recreation', 'Trendy', 'women'];

        $products = Product::whereNotIn('category', $excludedCategories)
            ->orderBy('id', 'desc')
            ->get();

        $categories = Product::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->whereNotIn('category', $excludedCategories)
            ->distinct()
            ->orderBy('category')
            ->get()
            ->map(function ($item) {
                return ['name' => $item->category, 'category' => $item->category];
            });

        return view('women', compact('products', 'categories'));
    }


public function getRelatedProducts($categoryId, $excludeId)
{
    try {
        $category = Category::find($categoryId);

        $categoryIds = [$categoryId];

        if ($category && $category->parent_id) {
            $categoryIds = Category::where('parent_id', $category->parent_id)
                ->pluck('id')
                ->toArray();
        }

        $relatedProducts = Product::where('id', '!=', $excludeId)
            ->where('status', 'active') // ✅ IMPORTANT
            ->where(function ($query) use ($categoryIds, $categoryId) {
                $query->whereIn('category_id', $categoryIds)
                      ->orWhere('category_id', $categoryId);
            })
            ->latest()
            ->take(10)
            ->get();

    $relatedProducts->transform(function ($p) {

    $img = 'https://placehold.co/600x800?text=No+Image';

    if ($p->colors->count()) {
        $firstColor = $p->colors->first();

        $firstImage = $p->images
            ->where('color_id', $firstColor->id)
            ->first();

        if ($firstImage && $firstImage->image) {
            $img = asset('storage/' . $firstImage->image);
        }
    }

    return [
        'id' => $p->id,
        'name' => $p->name,
        'price' => $p->price,
        'image' => $img, // ✅ FIXED
        'category' => $p->category_id,
    ];
});

        return response()->json([
            'success' => true,
            'products' => $relatedProducts
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch related products',
            'error' => $e->getMessage()
        ], 500);
    }
}
}

