<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\ProductReview;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function buyNow(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,product_id',
            'product_variant_id' => 'nullable|integer',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::where('product_id', (int) $validated['product_id'])->firstOrFail();
        $variantId = isset($validated['product_variant_id']) ? (int) $validated['product_variant_id'] : null;

        $hasVariants = $product->variants()->where('is_active', true)->exists();
        $variant = null;
        if ($hasVariants) {
            if (!$variantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a variant for this product.',
                ], 422);
            }

            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $product->product_id)
                ->where('is_active', true)
                ->first();
            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected variant is not available.',
                ], 404);
            }
        }

        $request->session()->put('buy_now', [
            'product_id' => (int) $product->product_id,
            'product_variant_id' => $variant?->id,
            'variant_name' => $variant?->name,
            'quantity' => (int) ($validated['quantity'] ?? 1),
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('checkout.index'),
        ]);
    }

    public function cancelBuyNow(Request $request)
    {
        $request->session()->forget('buy_now');

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cart.index');
    }

    public function checkout(Request $request): View
    {
        $cartItems = [];

        $buyNow = session('buy_now');
        if (is_array($buyNow) && isset($buyNow['product_id'])) {
            $product = Product::where('product_id', (int) $buyNow['product_id'])->first();
            if ($product) {
                $variant = null;
                if (!empty($buyNow['product_variant_id'])) {
                    $variant = ProductVariant::where('id', (int) $buyNow['product_variant_id'])
                        ->where('product_id', $product->product_id)
                        ->where('is_active', true)
                        ->first();
                }

                $unitPrice = (float) ($variant?->effective_price ?? $product->price);
                $stockUnlimited = (bool) ($variant?->stock_unlimited ?? $product->stock_unlimited ?? false);
                $stockQty = (int) ($variant?->stock ?? $product->stock ?? 0);

                $cartItems[] = [
                    'product_id' => $product->product_id,
                    'product_variant_id' => $variant?->id,
                    'variant_name' => $variant?->name,
                    'title' => $product->product_name,
                    'description' => $product->description,
                    'price' => $unitPrice,
                    'qty' => (int) max(1, (int) ($buyNow['quantity'] ?? 1)),
                    'image' => asset($product->image_path ?? 'images/placeholder.png'),
                    'stock' => $stockQty,
                    'stock_unlimited' => $stockUnlimited,
                ];
            }

            return view('checkout', ['cartItems' => $cartItems, 'buyNowMode' => true]);
        }
        
        // Optional: checkout only selected cart items (from /cart selection)
        $selectedItemsRaw = $request->query('selected_items', []);
        $selectedCartItemIds = collect(is_array($selectedItemsRaw) ? $selectedItemsRaw : [$selectedItemsRaw])
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        if (Auth::check()) {
            // Get cart from database for logged-in users
            $dbCartQuery = CartItem::where('user_id', Auth::id())
                ->with(['product', 'variant']);

            if (!empty($selectedCartItemIds)) {
                $dbCartQuery->whereIn('id', $selectedCartItemIds);
            } elseif ($request->has('selected_items')) {
                // User explicitly selected none; send back to cart.
                return redirect()->route('cart.index')->with('error', 'Please select at least one item to checkout.');
            }

            $dbCartItems = $dbCartQuery->get();

            foreach ($dbCartItems as $item) {
                if ($item->product) {
                    $unitPrice = (float) ($item->variant?->effective_price ?? $item->product->price);
                    $stockUnlimited = (bool) ($item->variant?->stock_unlimited ?? $item->product->stock_unlimited ?? false);
                    $stockQty = (int) ($item->variant?->stock ?? $item->product->stock ?? 0);

                    $cartItems[] = [
                        'product_id' => $item->product->product_id,
                        'cart_item_id' => $item->id,
                        'product_variant_id' => $item->product_variant_id,
                        'variant_name' => $item->variant?->name,
                        'title' => $item->product->product_name,
                        'description' => $item->product->description,
                        'price' => $unitPrice,
                        'qty' => $item->quantity,
                        'image' => asset($item->product->image_path ?? 'images/placeholder.png'),
                        'stock' => $stockQty,
                        'stock_unlimited' => $stockUnlimited,
                    ];
                }
            }
        }
        
        return view('checkout', ['cartItems' => $cartItems, 'buyNowMode' => false]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,product_id',
            'items.*.product_variant_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => [
                'required',
                'string',
                'min:10',
                'max:1000',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $address = trim((string) $value);
                    if ($address === '') {
                        $fail('Please select your shipping address.');
                        return;
                    }

                    // Prevent placeholder text from the PH address builder being submitted.
                    if (preg_match('/\bSelect\b/i', $address)) {
                        $fail('Please select your full shipping address (region, province, city, barangay).');
                    }
                },
            ],
            'shipping_method' => 'required|string',
            'shipping_fee' => 'required|numeric',
            'payment_method' => 'required|string',
            'order_notes' => 'nullable|string',
        ]);

        $buyNow = $request->session()->get('buy_now');
        $isBuyNow = is_array($buyNow) && isset($buyNow['product_id']);
        if ($isBuyNow) {
            // Enforce buy-now checkout product/variant from session,
            // but allow the checkout page payload to control quantity.
            // (Users can change qty on the checkout page; session quantity may still be 1.)
            $submitted = $validated['items'][0] ?? null;
            $submittedQty = is_array($submitted) ? (int) ($submitted['quantity'] ?? 0) : 0;
            $qty = $submittedQty > 0 ? $submittedQty : (int) ($buyNow['quantity'] ?? 1);

            $validated['items'] = [[
                'product_id' => (int) $buyNow['product_id'],
                'product_variant_id' => !empty($buyNow['product_variant_id']) ? (int) $buyNow['product_variant_id'] : null,
                'quantity' => (int) max(1, $qty),
            ]];
        }

        try {
            return DB::transaction(function () use ($validated, $isBuyNow) {
                $requestedLines = collect($validated['items'])
                    ->map(function ($row) {
                        return [
                            'product_id' => (int) $row['product_id'],
                            'product_variant_id' => array_key_exists('product_variant_id', $row) && $row['product_variant_id'] !== null
                                ? (int) $row['product_variant_id']
                                : null,
                            'quantity' => (int) $row['quantity'],
                        ];
                    })
                    ->groupBy(function ($row) {
                        return (string) $row['product_id'] . ':' . (string) ($row['product_variant_id'] ?? 0);
                    })
                    ->map(function ($rows) {
                        $first = $rows->first();
                        return [
                            'product_id' => (int) $first['product_id'],
                            'product_variant_id' => $first['product_variant_id'],
                            'quantity' => (int) $rows->sum('quantity'),
                        ];
                    })
                    ->values();

                $productIds = $requestedLines->pluck('product_id')->unique()->values()->all();
                $products = Product::whereIn('product_id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('product_id');

                $variantIds = $requestedLines
                    ->pluck('product_variant_id')
                    ->filter(fn ($v) => $v !== null)
                    ->unique()
                    ->values()
                    ->all();
                $variants = empty($variantIds)
                    ? collect()
                    : ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get()->keyBy('id');

                // Validate stock & variant correctness
                foreach ($requestedLines as $line) {
                    $productId = (int) $line['product_id'];
                    $variantId = $line['product_variant_id'] !== null ? (int) $line['product_variant_id'] : null;
                    $qty = (int) $line['quantity'];

                    $product = $products->get($productId);
                    if (!$product) {
                        throw new \RuntimeException('Product not found', 404);
                    }

                    $hasVariants = $product->variants()->where('is_active', true)->exists();
                    if ($hasVariants && !$variantId) {
                        throw new \RuntimeException('Variant is required for ' . $product->product_name, 409);
                    }

                    if (!$hasVariants && $variantId) {
                        throw new \RuntimeException('Variant is not applicable for ' . $product->product_name, 409);
                    }

                    $variant = null;
                    if ($variantId) {
                        $variant = $variants->get($variantId);
                        if (!$variant || (int) $variant->product_id !== (int) $productId || !(bool) $variant->is_active) {
                            throw new \RuntimeException('Selected variant is not available for ' . $product->product_name, 404);
                        }
                    }

                    $isUnlimited = (bool) ($variant?->stock_unlimited ?? $product->stock_unlimited ?? false);
                    $available = (int) ($variant?->stock ?? $product->stock ?? 0);
                    if (!$isUnlimited && $available < $qty) {
                        $msg = $available <= 0
                            ? ($product->product_name . ' is out of stock')
                            : ('Only ' . $available . ' left for ' . $product->product_name);
                        throw new \RuntimeException($msg, 409);
                    }
                }

                // Totals
                $subtotal = 0;
                foreach ($requestedLines as $line) {
                    $product = $products->get((int) $line['product_id']);
                    $variantId = $line['product_variant_id'] !== null ? (int) $line['product_variant_id'] : null;
                    $variant = $variantId ? $variants->get($variantId) : null;

                    $unitPrice = (float) ($variant?->effective_price ?? $product->price);
                    $subtotal += $unitPrice * (int) $line['quantity'];
                }
                $shippingFee = (float) $validated['shipping_fee'];
                $total = $subtotal + $shippingFee;

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'total' => $total,
                    'shipping_address' => $validated['shipping_address'],
                    'shipping_method' => $validated['shipping_method'],
                    'payment_method' => $validated['payment_method'],
                    'order_notes' => $validated['order_notes'] ?? null,
                    'status' => 'pending',
                ]);

                // Generate an e-commerce style order number (stable, not "#24")
                if (empty($order->order_number)) {
                    $date = $order->created_at ? $order->created_at->format('Ymd') : now()->format('Ymd');
                    $order->order_number = 'CFG-' . $date . '-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
                    $order->save();
                }

                $orderId = $order->id;

                foreach ($requestedLines as $line) {
                    $productId = (int) $line['product_id'];
                    $variantId = $line['product_variant_id'] !== null ? (int) $line['product_variant_id'] : null;
                    $qty = (int) $line['quantity'];

                    $product = $products->get($productId);
                    $variant = $variantId ? $variants->get($variantId) : null;

                    $unitPrice = (float) ($variant?->effective_price ?? $product->price);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'product_variant_id' => $variant?->id,
                        'variant_name' => $variant?->name,
                        'quantity' => $qty,
                        'price' => $unitPrice,
                    ]);

                    if ($variant) {
                        if (!(bool) ($variant->stock_unlimited ?? false)) {
                            $variant->stock = (int) ($variant->stock ?? 0) - $qty;
                            $variant->save();
                        }
                    } else {
                        if (!(bool) ($product->stock_unlimited ?? false)) {
                            $product->stock = (int) ($product->stock ?? 0) - $qty;
                            $product->stock_updated_at = now();
                            $product->save();
                        }
                    }
                }

                // Clear cart after successful order placement
                if ($isBuyNow) {
                    session()->forget('buy_now');
                } else {
                    if (Auth::check()) {
                        // Partial checkout: remove only purchased items from cart
                        foreach ($requestedLines as $line) {
                            $q = CartItem::where('user_id', Auth::id())
                                ->where('product_id', (int) $line['product_id']);
                            if ($line['product_variant_id'] !== null) {
                                $q->where('product_variant_id', (int) $line['product_variant_id']);
                            } else {
                                $q->whereNull('product_variant_id');
                            }
                            $q->delete();
                        }
                    } else {
                        // Fallback (checkout is typically auth-only)
                        $cart = session()->get('cart', []);
                        foreach ($requestedLines as $line) {
                            $key = (string) ((int) $line['product_id']) . ':' . (string) ((int) ($line['product_variant_id'] ?? 0));
                            unset($cart[$key]);
                        }
                        session()->put('cart', $cart);
                    }
                }

                DB::afterCommit(function () use ($orderId) {
                    $order = Order::with(['user', 'items.product'])->find($orderId);
                    if (!$order || !$order->user || empty($order->user->email)) {
                        return;
                    }

                    Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
                });

                return response()->json([
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'message' => 'Order placed successfully',
                ], 201);
            });
        } catch (\RuntimeException $e) {
            $code = (int) $e->getCode();
            $status = in_array($code, [404, 409], true) ? $code : 400;
            return response()->json([
                'message' => $e->getMessage(),
            ], $status);
        }
    }

    public function myOrders(): View
    {
        $orders = auth()->user()->orders()
            ->with([
                'items.product',
                'items.variant',
                'items.review',
                'shippingUpdates.adminUser',
                'returns.items.orderItem.product',
                'reviews',
            ])
            ->latest()
            ->get();

        // Prepare Shopee-style "Requests" summary data for the view.
        $orderNumbers = $orders
            ->mapWithKeys(fn ($o) => [(int) $o->id => (string) $o->display_order_number])
            ->all();

        $allReturns = $orders->pluck('returns')->flatten(1);
        $activeStatuses = ['requested', 'approved', 'in_transit', 'received'];
        $openReturnCount = $allReturns->whereIn('status', $activeStatuses)->count();
        $allReturnsSorted = $allReturns
            ->sortByDesc(fn ($r) => $r->requested_at ?? $r->created_at)
            ->values();

        return view('my-orders', [
            'orders' => $orders,
            'orderNumbers' => $orderNumbers,
            'openReturnCount' => $openReturnCount,
            'allReturnsSorted' => $allReturnsSorted,
        ]);
    }

    public function buyAgain(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $added = 0;
        $skipped = 0;

        foreach ($order->items as $item) {
            $product = Product::where('product_id', (int) $item->product_id)->first();
            if (!$product) {
                $skipped++;
                continue;
            }

            $variantId = $item->product_variant_id ? (int) $item->product_variant_id : null;
            $variant = null;
            if ($variantId) {
                $variant = ProductVariant::where('id', $variantId)
                    ->where('product_id', $product->product_id)
                    ->where('is_active', true)
                    ->first();
                if (!$variant) {
                    $skipped++;
                    continue;
                }
            }

            $qty = max(1, (int) $item->quantity);
            $isUnlimited = (bool) ($variant?->stock_unlimited ?? $product->stock_unlimited ?? false);
            $available = (int) ($variant?->stock ?? $product->stock ?? 0);
            if (!$isUnlimited && $available <= 0) {
                $skipped++;
                continue;
            }

            $cartItemQuery = CartItem::where('user_id', auth()->id())
                ->where('product_id', $product->product_id);
            if ($variant?->id) {
                $cartItemQuery->where('product_variant_id', $variant->id);
            } else {
                $cartItemQuery->whereNull('product_variant_id');
            }
            $cartItem = $cartItemQuery->first();

            if ($cartItem) {
                $newQty = (int) $cartItem->quantity + $qty;
                if (!$isUnlimited && $newQty > $available) {
                    $cartItem->quantity = $available;
                } else {
                    $cartItem->quantity = $newQty;
                }
                $cartItem->save();
            } else {
                $createQty = (!$isUnlimited && $qty > $available) ? $available : $qty;
                if ($createQty <= 0) {
                    $skipped++;
                    continue;
                }
                CartItem::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->product_id,
                    'product_variant_id' => $variant?->id,
                    'quantity' => $createQty,
                ]);
            }

            $added++;
        }

        if ($added > 0) {
            $msg = $skipped > 0
                ? "Added {$added} item(s). Skipped {$skipped} unavailable item(s)."
                : "Added {$added} item(s) to your cart.";
            return redirect()->route('cart.index')->with('success', $msg);
        }

        return redirect()->back()->with('error', 'No items could be added (items may be unavailable).');
    }

    public function requestReturn(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        // If user already marked complete, do not allow returns.
        if ($order->completed_at) {
            return redirect()->back()->with('error', 'This order is already marked as complete and cannot be returned.');
        }

        $isReturnable = in_array((string) $order->status, ['delivered', 'completed'], true) || (bool) $order->delivered_at;
        if (!$isReturnable) {
            return redirect()->back()->with('error', 'Returns are available after the order is delivered.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:5|max:2000',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order->loadMissing('items');
        $byId = $order->items->keyBy('id');

        // Prevent multiple active return requests for the same order.
        $hasOpenReturn = $order->returns()
            ->whereIn('status', ['requested', 'approved', 'in_transit', 'received'])
            ->exists();
        if ($hasOpenReturn) {
            return redirect()->back()->with('error', 'A return request for this order is already in progress.');
        }

        $now = now();
        $deadline = $now->copy()->addDays(7);

        // Log the incoming return request for debugging
        Log::info('Requesting return', ['order_id' => $order->id, 'user_id' => auth()->id(), 'items' => $validated['items']]);

        $createdReturn = null;
        try {
            DB::transaction(function () use ($order, $validated, $byId, $now, $deadline, &$createdReturn) {
                $ret = OrderReturn::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'status' => 'requested',
                    'reason' => $validated['reason'],
                    'requested_at' => $now,
                    'deadline_at' => $deadline,
                ]);

                foreach ($validated['items'] as $row) {
                    $orderItemId = (int) ($row['order_item_id'] ?? 0);
                    $qty = (int) ($row['quantity'] ?? 0);
                    $oi = $byId->get($orderItemId);
                    if (!$oi) {
                        continue;
                    }
                    $maxQty = max(1, (int) $oi->quantity);
                    $qty = max(1, min($qty, $maxQty));

                    OrderReturnItem::create([
                        'order_return_id' => $ret->id,
                        'order_item_id' => $oi->id,
                        'quantity' => $qty,
                    ]);
                }

                $createdReturn = $ret;
            });
        } catch (\Throwable $e) {
            Log::error('Failed to create return request', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to submit return request. Please try again later.');
        }

        if ($createdReturn) {
            Log::info('Return request created', ['order_id' => $order->id, 'return_id' => $createdReturn->id]);
        }

        return redirect()->back()->with('success', 'Return request submitted. You have 7 days to return the item(s).');
    }

    public function submitReview(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $isReviewable = in_array((string) $order->status, ['delivered', 'completed'], true) || (bool) $order->delivered_at;
        if (!$isReviewable) {
            return redirect()->back()->with('error', 'You can write reviews after the order is delivered.');
        }

        $validated = $request->validate([
            'order_item_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:120',
            'body' => 'nullable|string|max:5000',
        ]);

        $order->loadMissing('items');
        /** @var OrderItem|null $item */
        $item = $order->items->firstWhere('id', (int) $validated['order_item_id']);
        if (!$item) {
            return redirect()->back()->with('error', 'Invalid order item.');
        }

        ProductReview::updateOrCreate(
            [
                'order_item_id' => $item->id,
                'user_id' => auth()->id(),
            ],
            [
                'order_id' => $order->id,
                'product_id' => (int) $item->product_id,
                'product_variant_id' => $item->product_variant_id ? (int) $item->product_variant_id : null,
                'rating' => (int) $validated['rating'],
                'title' => $validated['title'] ?? null,
                'body' => $validated['body'] ?? null,
                'is_public' => true,
            ]
        );

        return redirect()->back()->with('success', 'Review saved.');
    }

    public function markComplete(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($order->completed_at || (string) $order->status === 'completed') {
            return redirect()->back()->with('success', 'Order is already completed.');
        }

        $isCompletable = in_array((string) $order->status, ['delivered'], true) || (bool) $order->delivered_at;
        if (!$isCompletable) {
            return redirect()->back()->with('error', 'You can mark the order as complete after it is delivered.');
        }

        $hasOpenReturn = $order->returns()
            ->whereIn('status', ['requested', 'approved', 'in_transit', 'received'])
            ->exists();
        if ($hasOpenReturn) {
            return redirect()->back()->with('error', 'You have an active return request. Please complete that first.');
        }

        $order->status = 'completed';
        $order->completed_at = now();
        $order->save();

        return redirect()->back()->with('success', 'Order marked as complete. Returns are now disabled for this order.');
    }

    public function cancel(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403);
        }

        // Do not allow cancellation for completed, delivered or already cancelled orders
        if ($order->completed_at || in_array((string) $order->status, ['delivered', 'completed', 'cancelled'], true)) {
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        // Allow cancellation only for pending or processing orders
        $cancellable = in_array((string) $order->status, ['pending', 'processing'], true);
        if (!$cancellable) {
            return redirect()->back()->with('error', 'This order cannot be cancelled at its current status.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Restock inventory for each item
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::where('id', $item->product_variant_id)->first();
                        if ($variant && !(bool) ($variant->stock_unlimited ?? false)) {
                            $variant->stock = (int) ($variant->stock ?? 0) + (int) $item->quantity;
                            $variant->save();
                        }
                    } else {
                        $product = Product::where('product_id', (int) $item->product_id)->first();
                        if ($product && !(bool) ($product->stock_unlimited ?? false)) {
                            $product->stock = (int) ($product->stock ?? 0) + (int) $item->quantity;
                            $product->stock_updated_at = now();
                            $product->save();
                        }
                    }
                }

                $order->status = 'cancelled';
                // Set cancelled_at if column exists
                if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'cancelled_at')) {
                    $order->cancelled_at = now();
                }
                $order->save();
            });
        } catch (\Throwable $e) {
            Log::error('Failed to cancel order', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to cancel the order. Please try again later.');
        }

        return redirect()->back()->with('success', 'Order cancelled successfully.');
    }
}
