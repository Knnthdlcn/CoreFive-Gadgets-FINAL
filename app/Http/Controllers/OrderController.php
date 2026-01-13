<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout(): View
    {
        return view('checkout');
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'shipping_address' => 'required|string',
            'shipping_method' => 'required|string',
            'shipping_fee' => 'required|numeric',
            'payment_method' => 'required|string',
            'order_notes' => 'nullable|string',
        ]);

        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $validated['items']));
        $total = $subtotal + $validated['shipping_fee'];

        $order = Order::create([
            'user_id' => auth()->id(),
            'subtotal' => $subtotal,
            'shipping_fee' => $validated['shipping_fee'],
            'total' => $total,
            'shipping_address' => $validated['shipping_address'],
            'shipping_method' => $validated['shipping_method'],
            'payment_method' => $validated['payment_method'],
            'order_notes' => $validated['order_notes'] ?? null,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return response()->json([
            'id' => $order->id,
            'message' => 'Order placed successfully',
        ], 201);
    }

    public function myOrders(): View
    {
        $orders = auth()->user()->orders()->with('items.product')->latest()->get();
        return view('my-orders', ['orders' => $orders]);
    }
}
