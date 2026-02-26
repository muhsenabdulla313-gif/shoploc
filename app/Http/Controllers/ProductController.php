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
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Apply category filter if provided
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            // Apply status filter if provided
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $products = $query->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',

            // ✅ category must exist in categories table
            'category' => 'required|string|exists:categories,name',

            'subcategory' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'shipping_charge' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',

            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:50',
            'size_prices' => 'nullable|array',
            'size_prices.*' => 'array|nullable',
            'size_prices.*.price' => 'nullable|numeric|min:0',
            'size_prices.*.original_price' => 'nullable|numeric|min:0',

            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'trend_type' => 'nullable|string|in:hot-trend,best-seller,featured',
            'rating' => 'nullable|numeric|min:0|max:5',

            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        $mainImagePath = null;
        $additionalImagePaths = [];

        try {
            // Handle main image
            if ($request->hasFile('main_image')) {
                $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            }

            // Handle additional images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $additionalImagePaths[] = $img->store('products/additional', 'public');
                }
            }

            // Combine main image + additional
            $allImagePaths = [];
            if ($mainImagePath) $allImagePaths[] = $mainImagePath;
            $allImagePaths = array_merge($allImagePaths, $additionalImagePaths);

            // Set first image as main, rest as gallery
            $imagePath = $mainImagePath ?? ($allImagePaths[0] ?? null);
            $galleryPaths = count($allImagePaths) > 1 ? array_slice($allImagePaths, 1) : [];

            $product = Product::create([
                'name' => $request->name,
                'category' => $request->category,
                'subcategory' => $request->subcategory ?? null,
                'price' => $request->price,
                'shipping_charge' => $request->shipping_charge ?? 0.00,
                'original_price' => $request->original_price ?? 0,
                'stock' => $request->stock ?? 0,

                'sizes' => $request->input('sizes', []),
                'colors' => $request->input('colors', []),
                'size_prices' => $this->processSizePrices($request->input('size_prices', [])),
                'description' => $request->description ?? '',
                'status' => $request->status ?? 'active',
                'trend_type' => $request->trend_type,
                'rating' => $request->rating ?? 0.0,

                'image' => $imagePath,
                'gallery_images' => $galleryPaths,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            if (!empty($mainImagePath)) Storage::disk('public')->delete($mainImagePath);
            foreach ($additionalImagePaths as $p) Storage::disk('public')->delete($p);

            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'category' => 'nullable|string|exists:categories,name',
            'subcategory' => 'nullable|string|max:255',
            'price' => 'sometimes|numeric|min:0.01',
            'shipping_charge' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',

            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:50',
            'size_prices' => 'nullable|array',
            'size_prices.*' => 'array|nullable',
            'size_prices.*.price' => 'nullable|numeric|min:0',
            'size_prices.*.original_price' => 'nullable|numeric|min:0',

            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'trend_type' => 'nullable|string|in:hot-trend,best-seller,featured',
            'rating' => 'nullable|numeric|min:0|max:5',

            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Main image replace
            if ($request->hasFile('main_image')) {
                if (!empty($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->image = $request->file('main_image')->store('products/main', 'public');
            }

            // Additional images replace (full replace)
            if ($request->hasFile('images')) {
                $existing = $product->gallery_images ?? [];
                if (is_array($existing)) {
                    foreach ($existing as $old) {
                        if (!empty($old)) Storage::disk('public')->delete($old);
                    }
                }

                $newGallery = [];
                foreach ($request->file('images') as $img) {
                    $newGallery[] = $img->store('products/additional', 'public');
                }
                $product->gallery_images = $newGallery;
            }

            foreach ([
                'name','category','subcategory','price','shipping_charge','original_price','stock',
                'description','status','trend_type','rating'
            ] as $field) {
                if ($request->has($field)) {
                    $product->{$field} = $request->input($field);
                }
            }

            if ($request->has('colors')) $product->colors = $request->input('colors', []);
            if ($request->has('sizes')) $product->sizes = $request->input('sizes', []);
            if ($request->has('size_prices')) $product->size_prices = $this->processSizePrices($request->input('size_prices', []));

            $product->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            if (!empty($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $galleryImages = $product->gallery_images ?? [];
            if (is_array($galleryImages)) {
                foreach ($galleryImages as $img) {
                    if (!empty($img)) Storage::disk('public')->delete($img);
                }
            }

            $product->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Product delete failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateTrendyProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'trend_type' => 'nullable|string|in:hot-trend,best-seller,featured',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::findOrFail($id);
            
            $updateData = [];
            if ($request->has('trend_type')) {
                $updateData['trend_type'] = $request->trend_type;
            }
            if ($request->has('rating')) {
                $updateData['rating'] = $request->rating;
            }
            
            $product->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Trendy product updated successfully',
                'data' => $product
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update trendy product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeTrendyProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'trend_type' => 'required|string|in:hot-trend,best-seller,featured',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::findOrFail($request->product_id);
            
            $product->update([
                'trend_type' => $request->trend_type,
                'rating' => $request->rating ?? $product->rating,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trendy product added successfully',
                'data' => $product
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add trendy product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeFromTrendy(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Remove trendy attributes
            $product->update([
                'trend_type' => null,
                'rating' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from trendy products successfully',
                'data' => $product
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from trendy products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        $q = strtolower(trim($query));

        // Popular categories suggestions (✅ no men/kids here)
        $popularCategories = [
            'saree' => 'Saree Collection',
            'kurta' => 'Kurta Collection',
            'lehenga' => 'Lehenga Collection',
            'salwar' => 'Salwar Suit Collection',
            'dress' => 'Dress Collection',
            'top' => 'Top Collection',
            'jeans' => 'Jeans Collection',
            'shirt' => 'Shirt Collection'
        ];

        $categorySuggestions = [];
        foreach ($popularCategories as $category => $displayName) {
            if (strpos($category, $q) !== false || strpos($q, $category) !== false) {
                $categorySuggestions[] = [
                    'id' => 'category_' . $category,
                    'name' => $displayName,
                    'productType' => 'popular_category',
                    'category' => ucfirst($category),
                    'isCategoryMatch' => true,
                    'isPopularCategory' => true
                ];
            }
        }

        // ✅ FIXED: distinct categories from DB (no groupBy issue)
        $dbCategoryMatches = Product::whereRaw('LOWER(category) LIKE ?', ["%{$q}%"])
            ->selectRaw('DISTINCT category')
            ->limit(3)
            ->get()
            ->map(function ($row) {
                $cat = (string)$row->category;
                return [
                    'id' => 'category_' . strtolower(str_replace(' ', '_', $cat)),
                    'name' => $cat . ' Collection',
                    'productType' => 'category',
                    'category' => $cat,
                    'isCategoryMatch' => true,
                    'isPopularCategory' => false
                ];
            });

        // Product name matches
        $nameProducts = Product::whereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
            ->orWhereRaw('LOWER(subcategory) LIKE ?', ["%{$q}%"])
            ->select('id', 'name', 'category')
            ->limit(7)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'productType' => 'products',
                    'category' => $product->category,
                    'isCategoryMatch' => false,
                    'isPopularCategory' => false
                ];
            });

        $allResults = collect($categorySuggestions)
            ->merge($dbCategoryMatches)
            ->merge($nameProducts);

        return response()->json($allResults->take(10)->values());
    }

    public function listTrendyProducts()
    {
        try {
            $trendyProducts = Product::where(function($query) {
                $query->whereNotNull('trend_type')
                      ->where('trend_type', '!=', '');
            })
            ->orWhere('rating', '>', 4.0)
            ->orderBy('id', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $trendyProducts
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trendy products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Related products returns FULL image URL
    public function getRelatedProducts($category, $excludeId)
    {
        try {
            $relatedProducts = Product::where('category', $category)
                ->where('id', '!=', $excludeId)
                ->where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            $relatedProducts->transform(function($p){
                $p->image = $p->image
                    ? Storage::url($p->image)
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

    private function processSizePrices($sizePrices)
    {
        if (!is_array($sizePrices)) {
            return [];
        }

        $processed = [];
        foreach ($sizePrices as $size => $data) {
            if (is_array($data) && isset($data['price'])) {
                $processed[$size] = [
                    'price' => floatval($data['price']),
                    'original_price' => isset($data['original_price']) ? floatval($data['original_price']) : null
                ];
            } elseif (is_numeric($data)) {
                $processed[$size] = ['price' => floatval($data)];
            }
        }

        return $processed;
    }
}
