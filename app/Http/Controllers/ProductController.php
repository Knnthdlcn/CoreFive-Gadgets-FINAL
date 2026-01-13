<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
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
