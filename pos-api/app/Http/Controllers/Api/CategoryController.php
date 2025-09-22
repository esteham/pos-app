<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(
            Category::select('id', 'name')->orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:categories,name'],
            'slug' => ['nullable','string','max:255','unique:categories,slug'],
        ]);

        $name = $data['name'];
        $slug = $data['slug'] ?? Str::slug($name);

        // Ensure slug is unique; if exists, append incrementing suffix
        $baseSlug = $slug;
        $i = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        $category = Category::create([
            'name' => $name,
            'slug' => $slug,
        ]);

        return response()->json([
            'message' => 'Category created',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:categories,name,'.$category->id],
            'slug' => ['nullable','string','max:255','unique:categories,slug,'.$category->id],
        ]);

        $name = $data['name'];
        $slug = $data['slug'] ?? Str::slug($name);

        // Ensure slug unique (excluding current)
        $baseSlug = $slug;
        $i = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        $category->update([
            'name' => $name,
            'slug' => $slug,
        ]);

        return response()->json([
            'message' => 'Category updated',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        // Prevent deletion if category has products
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with existing products.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted'
        ], 200);
    }
}
