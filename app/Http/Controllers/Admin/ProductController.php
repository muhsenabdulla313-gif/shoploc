<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Color;
class ProductController extends Controller
{



    public function display()
    {

        $products = Product::with(['category', 'variants', 'images'])->latest()->paginate(10);
        return view('admin.products', compact('products'));

    }
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',

            'variants' => 'required|array',
            'variants.size.*' => 'nullable|string',
            'variants.color_id.*' => 'nullable|exists:colors,id',
            'variants.stock.*' => 'required|integer|min:0',
            'subcategory' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'shipping_charge' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',

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
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'slug' => Str::slug($request->name),

                'price' => $request->price,
                'shipping_charge' => $request->shipping_charge ?? 0,
                'original_price' => $request->original_price ?? 0,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // variants
            foreach ($request->variants['stock'] as $i => $stock) {
                $product->variants()->create([
                    'size' => $request->variants['size'][$i] ?? null,
                    'color_id' => $request->variants['color_id'][$i] ?? null,
                    'stock' => $stock,
                ]);
            }



            if ($request->has('color_images')) {
                foreach ($request->color_images as $colorBlock) {

                    $colorId = $colorBlock['color_id'] ?? null;

                    if (!$colorId || !isset($colorBlock['images']))
                        continue;

                    foreach ($colorBlock['images'] as $img) {

                        $path = $img->store('products', 'public');

                        $product->images()->create([
                            'image' => $path,
                            'color_id' => $colorId
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Product create failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Product creation failed',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function show($id)
    {
        try {
            $product = Product::with([
                'category.parent',
                'variants.color',
                'images.color'
            ])->findOrFail($id);

            $colorImages = $product->images
                ->groupBy('color_id')
                ->map(function ($images) {
                    return [
                        'color_name' => optional($images->first()->color)->name ?? '',
                        'images' => $images->pluck('image')->toArray()
                    ];
                })
                ->values();

            $variants = $product->variants->map(function ($v) {
                return [
                    'size' => $v->size,
                    'color_name' => optional($v->color)->name ?? '',
                    'stock' => $v->stock
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $product->name,
                    'category' => $product->category->parent
                        ? $product->category->parent->name
                        : ($product->category->name ?? null),

                    'subcategory' => $product->category->parent
                        ? $product->category->name
                        : null,
                    'price' => $product->price,
                    'original_price' => $product->original_price,
                    'status' => $product->status,
                    'description' => $product->description,

                    'color_images' => $colorImages,
                    'variants' => $variants
                ]
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

    // ✅ VALIDATION
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'price' => 'sometimes|numeric|min:0.01',
        'shipping_charge' => 'nullable|numeric|min:0',
        'original_price' => 'nullable|numeric|min:0',

        // ✅ VARIANTS
        'variants' => 'nullable|array',
        'variants.size.*' => 'nullable|string',
        'variants.color_id.*' => 'required_with:variants|exists:colors,id',
        'variants.stock.*' => 'required_with:variants|integer|min:0',

        'description' => 'nullable|string',
        'status' => 'nullable|string|in:active,inactive',

        // ✅ COLOR IMAGES
        'color_images' => 'nullable|array',
        'color_images.*.color_id' => 'required_with:color_images|exists:colors,id',
        'color_images.*.images' => 'nullable|array',
        'color_images.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        // ✅ UPDATE BASIC PRODUCT
        $product->update($request->only([
            'name',
            'category_id',
            'price',
            'shipping_charge',
            'original_price',
            'description',
            'status'
        ]));

        // ✅ HANDLE VARIANTS
        if ($request->has('variants')) {

            // delete old
            $product->variants()->delete();

            $sizes  = $request->variants['size'] ?? [];
            $colors = $request->variants['color_id'] ?? [];
            $stocks = $request->variants['stock'] ?? [];

            foreach ($stocks as $i => $stock) {
                $product->variants()->create([
                    'size' => $sizes[$i] ?? null,
                    'color_id' => $colors[$i] ?? null,
                    'stock' => $stock,
                ]);
            }
        }

        // ✅ HANDLE COLOR IMAGES
        if ($request->has('color_images') && count($request->color_images)) {

            // delete old images from storage
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image);
            }

            // delete DB records
            $product->images()->delete();

            // insert new
            foreach ($request->color_images as $colorBlock) {

                $colorId = $colorBlock['color_id'] ?? null;

                if (!$colorId || !isset($colorBlock['images'])) continue;

                foreach ($colorBlock['images'] as $img) {

                    $path = $img->store('products', 'public');

                    $product->images()->create([
                        'image' => $path,
                        'color_id' => $colorId
                    ]);
                }
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error('Product update failed', [
            'product_id' => $id,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Product update failed',
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
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image);
            }

            $product->images()->delete();

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

        $dbCategoryMatches = Product::whereHas('category', function ($q2) use ($q) {
            $q2->whereRaw('LOWER(name) LIKE ?', ["%{$q}%"]);
        })
            ->with('category')
            ->limit(3)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => 'category_' . $product->category->id,
                    'name' => $product->category->name . ' Collection',
                    'productType' => 'category',
                    'category' => $product->category->name ?? null,
                    'isCategoryMatch' => true,
                ];
            });
        ;

        // Product name matches
        $nameProducts = Product::whereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
            ->orWhereRaw('LOWER(subcategory) LIKE ?', ["%{$q}%"])
            ->select('id', 'name', 'category_id')
            ->with('category')
            ->limit(7)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'productType' => 'products',
                    'category' => $product->category->name ?? null,
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
            $trendyProducts = Product::where(function ($query) {
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
            $relatedProducts = Product::with('images')
                ->where('category_id', $category)
                ->where('id', '!=', $excludeId)
                ->where('status', 'active')
                ->latest()
                ->limit(10)
                ->get();

            $relatedProducts->transform(function ($p) {
                $p->image = $p->images->first()
                    ? Storage::url($p->images->first()->image)
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

    public function colorindex()
    {
        return response()->json([
            'success' => true,
            'data' => Color::all()
        ]);
    }

    public function colorstore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20'
        ]);

        $color = Color::create([
            'name' => $request->name,
            'code' => $request->code
        ]);

        return response()->json([
            'success' => true,
            'data' => $color
        ]);
    }




}
