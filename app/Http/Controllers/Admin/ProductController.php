<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'products', 'Viewed products list');

        $query = Product::with(['category', 'primaryImage', 'translations']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })->orWhere('sku', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        // Filter by stock status
        if ($request->filled('stock')) {
            if ($request->stock === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->stock === 'low_stock') {
                $query->whereBetween('stock_quantity', [1, 10]);
            } elseif ($request->stock === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $admin = auth('admin')->user();
        $admin->logActivity('view', 'products', 'Accessed create product form');

        $categories = Category::where('is_active', true)->get();
        $locales = ['en', 'es'];

        return view('admin.products.create', compact('categories', 'locales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|unique:products,sku|max:100',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gte:price',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'name_en' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'name_es' => 'nullable|string|max:255',
            'description_es' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Generate slug from English name
            $slug = Str::slug($validated['name_en']);
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Create product
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'slug' => $slug,
                'sku' => $validated['sku'] ?? 'SKU-' . strtoupper(Str::random(8)),
                'price' => $validated['price'],
                'compare_price' => $validated['compare_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'is_active' => $request->has('is_active') ? 1 : 0,
                'is_featured' => $request->has('is_featured') ? 1 : 0,
            ]);

            // Create translations
            $product->translations()->create([
                'locale' => 'en',
                'name' => $validated['name_en'],
                'description' => $validated['description_en'] ?? null,
            ]);

            if ($request->filled('name_es')) {
                $product->translations()->create([
                    'locale' => 'es',
                    'name' => $validated['name_es'],
                    'description' => $validated['description_es'] ?? null,
                ]);
            }

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('images/products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0 ? 1 : 0,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            auth('admin')->user()->logActivity('create', 'products', "Created product: {$product->name()}");

            return redirect()->route('admin.products.index')
                ->with('success', __('Product created successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', __('Error creating product: ') . $e->getMessage());
        }
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images', 'translations'])->findOrFail($id);

        auth('admin')->user()->logActivity('view', 'products', "Viewed product: {$product->name()}");

        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with(['translations', 'images'])->findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        $locales = ['en', 'es'];

        auth('admin')->user()->logActivity('view', 'products', "Accessed edit form for product: {$product->name()}");

        return view('admin.products.edit', compact('product', 'categories', 'locales'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|max:100|unique:products,sku,' . $id,
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gte:price',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'name_en' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'name_es' => 'nullable|string|max:255',
            'description_es' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_images' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Update product
            $product->update([
                'category_id' => $validated['category_id'],
                'sku' => $validated['sku'] ?? $product->sku,
                'price' => $validated['price'],
                'compare_price' => $validated['compare_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'is_active' => $request->has('is_active') ? 1 : 0,
                'is_featured' => $request->has('is_featured') ? 1 : 0,
            ]);

            // Update translations
            $product->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'name' => $validated['name_en'],
                    'description' => $validated['description_en'] ?? null,
                ]
            );

            if ($request->filled('name_es')) {
                $product->translations()->updateOrCreate(
                    ['locale' => 'es'],
                    [
                        'name' => $validated['name_es'],
                        'description' => $validated['description_es'] ?? null,
                    ]
                );
            }

            // Remove selected images
            if ($request->filled('remove_images')) {
                $imagesToRemove = ProductImage::whereIn('id', $request->remove_images)
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($imagesToRemove as $image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }
            }

            // Add new images
            if ($request->hasFile('images')) {
                $existingImagesCount = $product->images()->count();

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('images/products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => ($existingImagesCount === 0 && $index === 0) ? 1 : 0,
                        'sort_order' => $existingImagesCount + $index + 1,
                    ]);
                }
            }

            DB::commit();

            auth('admin')->user()->logActivity('update', 'products', "Updated product: {$product->name()}");

            return redirect()->route('admin.products.index')
                ->with('success', __('Product updated successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', __('Error updating product: ') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->name();

            // Delete images from storage
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            $product->delete();

            auth('admin')->user()->logActivity('delete', 'products', "Deleted product: {$productName}");

            return redirect()->route('admin.products.index')
                ->with('success', __('Product deleted successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting product: ') . $e->getMessage());
        }
    }
}
