<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Offer;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Handle referral tracking
        $referralCode = $request->query('ref');
        if ($referralCode) {
            session(['referral_code' => $referralCode]);
        }

        // Fetch all products
        $products = Product::orderBy('id', 'desc')->get();

        // Fetch trendy products
        $trendyProducts = Product::where('status', 'active')
            ->whereNotNull('trend_type')
            ->orderByRaw("CASE
                WHEN trend_type = 'best-seller' THEN 1
                WHEN trend_type = 'hot-trend' THEN 2
                WHEN trend_type = 'featured' THEN 3
                ELSE 4
            END")
            ->limit(8)
            ->get();

        // Fetch offers
        try {
            $offers = Offer::where('active', true)->get();
        } catch (\Exception $e) {
            $offers = collect();
        }

        return view('index', compact('products', 'trendyProducts', 'offers'));
    }

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

        // Prepare size prices data for the view
        $sizePrices = [];
        if ($product->size_prices && is_array($product->size_prices)) {
            $sizePrices = $product->size_prices;
        } elseif (is_string($product->size_prices)) {
            $sizePrices = json_decode($product->size_prices, true) ?: [];
        }

        $isLoggedIn = auth()->check();
        
        return view('product-details', compact('product', 'sizePrices', 'isLoggedIn'));
    }

    // ✅ FIXED: WOMEN (show all products from products table)
    public function women()
    {
        // Exclude unwanted categories: men, recreation, Trendy, women
        $excludedCategories = ['men', 'recreation', 'Trendy', 'women'];
        
        $products = Product::whereNotIn('category', $excludedCategories)
            ->orderBy('id', 'desc')
            ->get();
        
        // Get unique categories from products table for the category filter
        $categories = Product::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->whereNotIn('category', $excludedCategories)
            ->distinct()
            ->orderBy('category')
            ->get()
            ->map(function($item) {
                return ['name' => $item->category, 'category' => $item->category];
            });

        return view('women', compact('products', 'categories'));
    }

}
