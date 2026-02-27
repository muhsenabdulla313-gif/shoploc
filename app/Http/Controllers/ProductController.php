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
        $product = Product::findOrFail($id);

        $sizePrices = [];
        if ($product->size_prices && is_array($product->size_prices)) {
            $sizePrices = $product->size_prices;
        } elseif (is_string($product->size_prices)) {
            $sizePrices = json_decode($product->size_prices, true) ?: [];
        }

        $isLoggedIn = auth()->check();

        return view('product-details', compact('product', 'sizePrices', 'isLoggedIn'));
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


}
