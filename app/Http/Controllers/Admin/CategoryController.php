<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'categories', 'Viewed categories list');

        $categories = Category::with(['translations'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $locales = ['en', 'es'];

        return view('admin.categories.index', compact('categories', 'locales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'name_es' => 'nullable|string|max:255',
            'description_es' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate slug
            $slug = Str::slug($validated['name_en']);
            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images/categories', 'public');
            }

            // Create category
            $category = Category::create([
                'slug' => $slug,
                'image' => $imagePath,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            // Create translations
            $category->translations()->create([
                'locale' => 'en',
                'name' => $validated['name_en'],
                'description' => $validated['description_en'] ?? null,
            ]);

            if ($request->filled('name_es')) {
                $category->translations()->create([
                    'locale' => 'es',
                    'name' => $validated['name_es'],
                    'description' => $validated['description_es'] ?? null,
                ]);
            }

            DB::commit();

            auth('admin')->user()->logActivity('create', 'categories', "Created category: {$category->name()}");

            return redirect()->route('admin.categories.index')
                ->with('success', __('Category created successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', __('Error creating category: ') . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'name_es' => 'nullable|string|max:255',
            'description_es' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Handle image
            $imagePath = $category->image;

            if ($request->has('remove_image') && $request->remove_image) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = null;
            }

            if ($request->hasFile('image')) {
                // Delete old image
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('images/categories', 'public');
            }

            // Update category
            $category->update([
                'image' => $imagePath,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'sort_order' => $validated['sort_order'] ?? $category->sort_order,
            ]);

            // Update translations
            $category->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'name' => $validated['name_en'],
                    'description' => $validated['description_en'] ?? null,
                ]
            );

            if ($request->filled('name_es')) {
                $category->translations()->updateOrCreate(
                    ['locale' => 'es'],
                    [
                        'name' => $validated['name_es'],
                        'description' => $validated['description_es'] ?? null,
                    ]
                );
            }

            DB::commit();

            auth('admin')->user()->logActivity('update', 'categories', "Updated category: {$category->name()}");

            return redirect()->route('admin.categories.index')
                ->with('success', __('Category updated successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', __('Error updating category: ') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $categoryName = $category->name();

            // Check if category has products
            $productsCount = $category->products()->count();
            if ($productsCount > 0) {
                return back()->with('error', __('Cannot delete category with :count products. Please reassign or delete products first.', ['count' => $productsCount]));
            }

            // Delete image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            auth('admin')->user()->logActivity('delete', 'categories', "Deleted category: {$categoryName}");

            return redirect()->route('admin.categories.index')
                ->with('success', __('Category deleted successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting category: ') . $e->getMessage());
        }
    }
}
