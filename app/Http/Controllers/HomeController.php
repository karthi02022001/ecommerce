<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['translations', 'primaryImage', 'category'])
            ->active()
            ->featured()
            ->limit(8)
            ->get();

        $latestProducts = Product::with(['translations', 'primaryImage', 'category'])
            ->active()
            ->latest()
            ->limit(8)
            ->get();

        $categories = Category::with('translations')
            ->active()
            ->ordered()
            ->limit(6)
            ->get();

        return view('home', compact('featuredProducts', 'latestProducts', 'categories'));
    }
}
