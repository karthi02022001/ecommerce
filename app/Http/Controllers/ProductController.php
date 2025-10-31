<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['translations', 'primaryImage', 'category'])
            ->active();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $locale = app()->getLocale();
            
            $query->whereHas('translations', function($q) use ($search, $locale) {
                $q->where('locale', $locale)
                  ->where('name', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('id');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::with('translations')->active()->ordered()->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with(['translations', 'images', 'category.translations'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Related products from same category
        $relatedProducts = Product::with(['translations', 'primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function category($slug)
    {
        $category = Category::with('translations')
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $products = Product::with(['translations', 'primaryImage'])
            ->where('category_id', $category->id)
            ->active()
            ->paginate(12);

        $categories = Category::with('translations')->active()->ordered()->get();

        return view('products.category', compact('category', 'products', 'categories'));
    }
}
