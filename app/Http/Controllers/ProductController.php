<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');
        
        if ($category) {
            $products = Product::where('category', $category)->get();
        } else {
            $products = Product::all();
        }
        
        return view('products', [
            'products' => $products,
            'selectedCategory' => $category
        ]);
    }

    public function store(Request $request)
    {
        // Only admin users can add products
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }

    public function show($productId): View
    {
        $product = Product::findOrFail($productId, 'product_id');
        return view('product-detail', ['product' => $product]);
    }

    public function list(): \Illuminate\Http\JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }
}
