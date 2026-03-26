<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'status' => 'nullable|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',

        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'status' => $request->status ?? 'active'
        ]);

        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $id,
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update([
            'name' => $request->name ?? $category->name,
            'slug' => $request->name ? \Illuminate\Support\Str::slug($request->name) : $category->slug,
            'parent_id' => $request->parent_id ?? $category->parent_id,
        ]);

        return response()->json(['success' => true, 'data' => $category]);
    }
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }
}
