<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::all();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $category = \App\Models\Category::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description ?? '',
            'status' => $request->status ?? 'active',
        ]);

        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function show($id)
    {
        $category = \App\Models\Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = \App\Models\Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $category->update([
            'name' => $request->name ?? $category->name,
            'slug' => $request->name ? \Illuminate\Support\Str::slug($request->name) : $category->slug,
            'description' => $request->has('description') ? $request->description : $category->description,
            'status' => $request->has('status') ? $request->status : $category->status,
        ]);

        return response()->json(['success' => true, 'data' => $category]);
    }

    public function destroy($id)
    {
        $category = \App\Models\Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }
}
