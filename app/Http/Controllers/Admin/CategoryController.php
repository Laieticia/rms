<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $restaurantId = $this->getRestaurantId();
        
        $categories = Category::where('restaurant_id', $restaurantId)
            ->withCount('products')
            ->with('parent')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::where('restaurant_id', $this->getRestaurantId())
            ->whereNull('parent_id')
            ->get();

        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['restaurant_id'] = $this->getRestaurantId();
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Category $category)
    {
        $categories = Category::where('restaurant_id', $category->restaurant_id)
            ->where('id', '!=', $category->id)
            ->whereNull('parent_id')
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée.');
    }

    private function getRestaurantId(): int
    {
        $user = auth()->user();
        
        if ($user->isAdmin() && request()->filled('restaurant_id')) {
            return request()->restaurant_id;
        }

        return $user->restaurants()->first()?->id ?? 1;
    }
}