<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(): View
    {
        return view('cart');
    }

    public function getCart(Request $request): \Illuminate\Http\JsonResponse
    {
        // Cart is managed client-side with localStorage
        return response()->json(['message' => 'Cart is managed client-side']);
    }
}
