<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $baseQuery = Product::query()
            ->withCount(['variants as variants_count' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['variants as variants_unlimited_count' => function ($q) {
                $q->where('is_active', true)->where('stock_unlimited', true);
            }])
            ->withSum(['variants as variants_stock_sum' => function ($q) {
                $q->where('is_active', true);
            }], 'stock');

        $featuredProducts = (clone $baseQuery)
            ->where('is_featured', true)
            ->latest('updated_at')
            ->take(9)
            ->get();

        $newArrivals = (clone $baseQuery)
            ->latest('created_at')
            ->take(9)
            ->get();

        $categories = Category::query()
            ->orderBy('name')
            ->take(8)
            ->get();

        return view('index', compact('featuredProducts', 'newArrivals', 'categories'));
    }
}
