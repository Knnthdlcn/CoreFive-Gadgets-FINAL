<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $q = trim((string) $request->query('q', ''));
        $categories = Category::query()
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        if (empty($categories)) {
            // Backward compatible: derive categories directly from products if the categories table is empty.
            $categories = Product::query()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->toArray();
        }

        $productsQuery = Product::query()
            ->withCount(['variants as variants_count' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['variants as variants_unlimited_count' => function ($q) {
                $q->where('is_active', true)->where('stock_unlimited', true);
            }])
            ->withSum(['variants as variants_stock_sum' => function ($q) {
                $q->where('is_active', true);
            }], 'stock');

        if ($category) {
            $productsQuery->where('category', $category);
        }

        if ($q !== '') {
            $productsQuery->where(function ($query) use ($q) {
                $query->where('product_name', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%')
                    ->orWhere('category', 'like', '%' . $q . '%');
            });
        }

        // Shuffle products on every reload (after applying filters/search)
        $products = $productsQuery->inRandomOrder()->get();
        
        return view('products', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $category,
            'searchQuery' => $q,
        ]);
    }

    public function store(Request $request)
    {
        // Only admin users can add products
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
        'product_name' => 'required|string|max:255',
        'description'  => 'required|string',
        'price'        => 'required|numeric|min:0',
        'category'     => 'nullable|string|max:255',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
]);

if ($request->hasFile('image')) {
  $validated['image_path'] = $request->file('image')->store('products', 'public');
}

Product::create($validated);


        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }

    public function show($id): View
    {
        $product = Product::with(['variants' => function ($q) {
            $q->where('is_active', true)->orderBy('name');
        }])
            ->withCount(['variants as variants_count' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['variants as variants_unlimited_count' => function ($q) {
                $q->where('is_active', true)->where('stock_unlimited', true);
            }])
            ->withSum(['variants as variants_stock_sum' => function ($q) {
                $q->where('is_active', true);
            }], 'stock')
            ->where('product_id', $id)
            ->firstOrFail();
        
        // Get related products from the same category (excluding current product)
        $relatedProducts = Product::query()
            ->withCount(['variants as variants_count' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['variants as variants_unlimited_count' => function ($q) {
                $q->where('is_active', true)->where('stock_unlimited', true);
            }])
            ->withSum(['variants as variants_stock_sum' => function ($q) {
                $q->where('is_active', true);
            }], 'stock')
            ->where('category', $product->category)
            ->where('product_id', '!=', $id)
            ->limit(4)
            ->get();
        
        // If there aren't enough related products, fill with random products
        if ($relatedProducts->count() < 4) {
            $additionalProducts = Product::query()
                ->where('product_id', '!=', $id)
                ->whereNotIn('product_id', $relatedProducts->pluck('product_id'))
                ->withCount(['variants as variants_count' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->withCount(['variants as variants_unlimited_count' => function ($q) {
                    $q->where('is_active', true)->where('stock_unlimited', true);
                }])
                ->withSum(['variants as variants_stock_sum' => function ($q) {
                    $q->where('is_active', true);
                }], 'stock')
                ->inRandomOrder()
                ->limit(4 - $relatedProducts->count())
                ->get();
            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }
        
        return view('product-detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }

    public function list(): \Illuminate\Http\JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }
}
