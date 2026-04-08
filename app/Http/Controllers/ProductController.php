<?php

namespace App\Http\Controllers;

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

        if ($query) {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('category', 'LIKE', "%{$query}%")
                ->orWhere('subcategory', 'LIKE', "%{$query}%")
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $products = Product::orderBy('id', 'desc')->get();
        }

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
        $category = \App\Models\Category::find($categoryId);

        $categoryIds = [$categoryId];

        // If category has parent → get sibling categories
        if ($category && $category->parent_id) {
            $siblings = \App\Models\Category::where('parent_id', $category->parent_id)
                ->pluck('id')
                ->toArray();

            $categoryIds = $siblings;
        }

        $relatedProducts = Product::where('id', '!=', $excludeId)
            ->where('status', 'active')
            ->whereIn('category_id', $categoryIds)
            ->latest()
            ->limit(10)
            ->get();

        $relatedProducts->transform(function ($p) {
            $p->image = $p->image
                ? asset('storage/' . $p->image)
                : 'https://placehold.co/600x800?text=No+Image';
            return $p;
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
