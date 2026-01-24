@extends('layouts.app')

@section('content')
<div style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); min-height: 100vh; padding: 40px 0 30px;">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-3">
            <div class="col-12">
                <h1 style="color: #fff; font-weight: 700; font-size: 1.75rem; margin-bottom: 5px;">
                    <i class="fas fa-shopping-bag me-2" style="color: #ffc107;"></i>My Orders
                </h1>
                <p style="color: #b0c4de; font-size: 0.9rem;">Track and manage all your purchases</p>
            </div>
        </div>

        @if($orders->isEmpty())
            <!-- Empty State -->
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div style="background: rgba(255, 255, 255, 0.95); border-radius: 12px; padding: 40px 30px; text-align: center; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #ffc107; margin-bottom: 15px; display: block; opacity: 0.7;"></i>
                        <h4 style="color: #06131a; font-weight: 600; margin-bottom: 8px;">No Orders Yet</h4>
                        <p style="color: #666; margin-bottom: 25px; font-size: 0.95rem;">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                        <a href="{{ route('home') }}" class="btn" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; padding: 10px 30px; border-radius: 25px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);">
                            <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Shopee-style quick actions / Requests -->
            <div class="row mb-3">
                <div class="col-12 d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                    <div class="small" style="color: #b0c4de;">Tip: click an order to open its receipt</div>
                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#requestsModal" style="border-radius: 999px; font-weight: 800;">
                        <i class="fas fa-rotate-left me-1" style="color:#ffc107;"></i>Requests
                        @if($openReturnCount > 0)
                            <span class="badge" style="background:#ffc107; color:#111; margin-left: 6px;">{{ $openReturnCount }}</span>
                        @endif
                    </button>
                </div>
            </div>

            <!-- Orders Grid (compact + clickable) -->
            <div class="row">
                @foreach($orders as $order)
                    @php
                        $orderUid = 'o' . $order->id;
                        $isDelivered = in_array((string) $order->status, ['delivered', 'completed'], true) || !empty($order->delivered_at);
                        $isCompleted = !empty($order->completed_at) || (string) $order->status === 'completed';
                        $openReturn = $order->returns->firstWhere('status', 'requested')
                            ?? $order->returns->firstWhere('status', 'approved')
                            ?? $order->returns->firstWhere('status', 'in_transit')
                            ?? $order->returns->firstWhere('status', 'received');
                    @endphp

                    <div class="col-12 col-lg-6 mb-3">
                        <div class="order-summary-card position-relative" style="background: rgba(255, 255, 255, 0.97); border-radius: 10px; overflow: hidden; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.10); border-left: 3px solid #ffc107;">
                            <div style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 12px 14px; color: #fff;">
                                <div class="d-flex justify-content-between align-items-start" style="gap: 10px;">
                                    <div>
                                        <div style="font-weight: 800; font-size: 0.95rem;">
                                            <i class="fas fa-receipt me-1" style="color: #ffc107;"></i>Order {{ $order->display_order_number }}
                                        </div>
                                        <div style="color: #b0c4de; font-size: 0.75rem;">
                                            <i class="fas fa-calendar me-1"></i>{{ $order->created_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-weight: 800; font-size: 0.75rem;
                                        @if($order->status === 'pending')
                                            background: #fff3cd; color: #856404;
                                        @elseif($order->status === 'processing')
                                            background: #cfe2ff; color: #084298;
                                        @elseif($order->status === 'shipped')
                                            background: #cff4fc; color: #055160;
                                        @elseif($order->status === 'delivered')
                                            background: #d1e7dd; color: #0f5132;
                                        @elseif($order->status === 'completed')
                                            background: #d4edda; color: #155724;
                                        @else
                                            background: #f8d7da; color: #721c24;
                                        @endif
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>

                            <div style="padding: 12px 14px;">
                                <div class="d-flex justify-content-between align-items-center" style="gap: 10px;">
                                    <div class="small" style="color:#334155;">
                                        <strong>{{ $order->items->count() }}</strong> item{{ $order->items->count() === 1 ? '' : 's' }}
                                        @if($openReturn)
                                            <span class="badge" style="background:#fde68a; color:#7c2d12; margin-left: 6px;">Return: {{ ucfirst(str_replace('_',' ', $openReturn->status)) }}</span>
                                        @endif
                                    </div>
                                    <div style="font-weight: 900; color:#ffc107;">₱{{ number_format($order->total, 2) }}</div>
                                </div>

                                <div class="d-flex flex-wrap" style="gap: 8px; margin-top: 10px; position: relative; z-index: 2;">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal-{{ $orderUid }}" style="border-radius: 999px; font-weight: 800;">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    <form method="POST" action="{{ route('orders.buy-again', $order) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color:#222; font-weight: 900; border-radius: 999px;">
                                            <i class="fas fa-rotate-left me-1"></i>Buy again
                                        </button>
                                    </form>
                                </div>

                                <a href="#" class="stretched-link" data-bs-toggle="modal" data-bs-target="#orderModal-{{ $orderUid }}" aria-label="Open order details"></a>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details Modal (smaller + scrollable) -->
                    <div class="modal fade order-details-modal" id="orderModal-{{ $orderUid }}" tabindex="-1" aria-labelledby="orderModalLabel-{{ $orderUid }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                                <div class="modal-header" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); color: #fff;">
                                    <div>
                                        <div id="orderModalLabel-{{ $orderUid }}" style="font-weight: 900;">
                                            <i class="fas fa-receipt me-1" style="color:#ffc107;"></i>Order {{ $order->display_order_number }}
                                        </div>
                                        <div class="small" style="color:#b0c4de;">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body" style="background: rgba(255,255,255,0.98);">
                                    <!-- Quick summary -->
                                    <div class="d-flex justify-content-between align-items-start" style="gap: 10px;">
                                        <div class="small" style="color:#334155;">
                                            <i class="fas fa-map-marker-alt me-1" style="color: #ffc107;"></i>{{ $order->shipping_address }}
                                        </div>
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-weight: 900; font-size: 0.75rem;
                                            @if($order->status === 'pending')
                                                background: #fff3cd; color: #856404;
                                            @elseif($order->status === 'processing')
                                                background: #cfe2ff; color: #084298;
                                            @elseif($order->status === 'shipped')
                                                background: #cff4fc; color: #055160;
                                            @elseif($order->status === 'delivered')
                                                background: #d1e7dd; color: #0f5132;
                                            @elseif($order->status === 'completed')
                                                background: #d4edda; color: #155724;
                                            @else
                                                background: #f8d7da; color: #721c24;
                                            @endif
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>

                                    <div class="mt-2" style="background: rgba(255, 193, 7, 0.08); padding: 10px; border-radius: 10px; border: 1px solid rgba(255, 193, 7, 0.35);">
                                        <div class="small" style="color:#06131a; font-weight: 800;">
                                            <i class="fas fa-truck me-1" style="color:#ffc107;"></i>{{ ucfirst($order->shipping_method) }}
                                            <span style="color:#6c757d; font-weight: 700;">•</span>
                                            <i class="fas fa-credit-card me-1" style="color:#ffc107;"></i>{{ ucfirst($order->payment_method) }}
                                        </div>
                                    </div>

                                    <!-- Items Table -->
                                    <div style="margin: 14px 0 10px;">
                                        <div style="font-weight: 900; color:#06131a; margin-bottom: 6px;"><i class="fas fa-box me-1" style="color:#ffc107;"></i>Items</div>
                                        <div class="table-responsive">
                                            <table class="table table-sm" style="font-size: 0.85rem;">
                                                <thead>
                                                    <tr style="background: rgba(255, 193, 7, 0.12);">
                                                        <th style="font-weight: 900;">Product</th>
                                                        <th class="text-end" style="font-weight: 900;">Price</th>
                                                        <th class="text-center" style="font-weight: 900;">Qty</th>
                                                        <th class="text-end" style="font-weight: 900;">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->items as $item)
                                                        <tr>
                                                            <td>
                                                                <div style="font-weight: 800; color:#0f172a;">{{ $item->product->product_name ?? 'Product' }}</div>
                                                                @if(!empty($item->variant_name))
                                                                    <div class="small" style="color:#6c757d;">Variant: {{ $item->variant_name }}</div>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                                            <td class="text-center">{{ $item->quantity }}</td>
                                                            <td class="text-end" style="font-weight: 900;">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Order Summary -->
                                    <div style="background: linear-gradient(135deg, rgba(6, 19, 26, 0.05) 0%, rgba(26, 58, 82, 0.05) 100%); padding: 12px; border-radius: 12px; border: 1px solid rgba(255, 193, 7, 0.6);">
                                        <div class="d-flex justify-content-between"><span class="small" style="color:#334155;">Subtotal</span><span style="font-weight: 900;">₱{{ number_format($order->subtotal, 2) }}</span></div>
                                        <div class="d-flex justify-content-between"><span class="small" style="color:#334155;">Shipping</span><span style="font-weight: 900;">₱{{ number_format($order->shipping_fee, 2) }}</span></div>
                                        <div class="d-flex justify-content-between" style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(0,0,0,0.12);">
                                            <span style="font-weight: 900; color:#06131a;">Total</span>
                                            <span style="font-weight: 900; color:#ffc107; font-size: 1.05rem;">₱{{ number_format($order->total, 2) }}</span>
                                        </div>
                                    </div>

                                    @if($order->order_notes)
                                        <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 10px; border-left: 3px solid #ffc107;">
                                            <div class="small" style="font-weight: 900; color:#06131a;"><i class="fas fa-sticky-note me-1" style="color:#ffc107;"></i>Notes</div>
                                            <div class="small" style="color:#475569; white-space: pre-wrap;">{{ $order->order_notes }}</div>
                                        </div>
                                    @endif

                                    <!-- Interactive tabs -->
                                    <div style="margin-top: 12px; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 10px;">
                                        <ul class="nav nav-tabs" id="tabs-{{ $orderUid }}" role="tablist" style="border-bottom: 2px solid rgba(255, 193, 7, 0.35);">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="receipt-tab-{{ $orderUid }}" data-bs-toggle="tab" data-bs-target="#receipt-{{ $orderUid }}" type="button" role="tab" style="font-weight: 900;">Receipt</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="shipping-tab-{{ $orderUid }}" data-bs-toggle="tab" data-bs-target="#shipping-{{ $orderUid }}" type="button" role="tab" style="font-weight: 900;">Shipping</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="reviews-tab-{{ $orderUid }}" data-bs-toggle="tab" data-bs-target="#reviews-{{ $orderUid }}" type="button" role="tab" style="font-weight: 900;">Reviews</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="returns-tab-{{ $orderUid }}" data-bs-toggle="tab" data-bs-target="#returns-{{ $orderUid }}" type="button" role="tab" style="font-weight: 900;">Returns</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="help-tab-{{ $orderUid }}" data-bs-toggle="tab" data-bs-target="#help-{{ $orderUid }}" type="button" role="tab" style="font-weight: 900;">Help</button>
                                            </li>
                                        </ul>

                                        <div class="tab-content" style="padding-top: 10px;">
                                            <!-- Receipt tab -->
                                            <div class="tab-pane fade show active" id="receipt-{{ $orderUid }}" role="tabpanel">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                                                    <div>
                                                        @if($isCompleted)
                                                            <span class="badge" style="background:#d4edda; color:#155724; padding: 6px 10px; border-radius: 999px; font-weight:900;">Order completed</span>
                                                            <span class="small" style="color:#6c757d; margin-left: 6px;">Returns disabled</span>
                                                        @elseif($isDelivered)
                                                            <span class="badge" style="background:#d1e7dd; color:#0f5132; padding: 6px 10px; border-radius: 999px; font-weight:900;">Delivered</span>
                                                            <span class="small" style="color:#6c757d; margin-left: 6px;">You have 7 days to request a return</span>
                                                        @else
                                                            <span class="badge" style="background:#fff3cd; color:#856404; padding: 6px 10px; border-radius: 999px; font-weight:900;">In progress</span>
                                                            <span class="small" style="color:#6c757d; margin-left: 6px;">Check Shipping tab for updates</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-wrap" style="gap: 8px; position: relative; z-index: 2;">
                                                        <form method="POST" action="{{ route('orders.buy-again', $order) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color:#222; font-weight: 900; border-radius: 999px;">
                                                                <i class="fas fa-rotate-left me-1"></i>Buy again
                                                            </button>
                                                        </form>

                                                        @if($isDelivered && !$isCompleted)
                                                            <form method="POST" action="{{ route('orders.complete', $order) }}" onsubmit="return confirm('Mark this order as complete? This disables returns for this order.');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-success" style="border-radius: 999px; font-weight: 900;">
                                                                    <i class="fas fa-circle-check me-1"></i>Mark complete
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @php
                                                            $canCancel = in_array((string) $order->status, ['pending', 'processing'], true);
                                                        @endphp
                                                        @if($canCancel)
                                                            <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order? This will restock items and cannot be undone.');" style="margin-left:6px;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 999px; font-weight: 900;">
                                                                    <i class="fas fa-ban me-1"></i>Cancel order
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Shipping tab -->
                                            <div class="tab-pane fade" id="shipping-{{ $orderUid }}" role="tabpanel">
                                                @if($order->shippingUpdates->isEmpty())
                                                    <div class="small" style="color:#6c757d;">No shipping updates yet.</div>
                                                @else
                                                    <div class="list-group" style="border-radius: 12px; overflow: hidden;">
                                                        @foreach($order->shippingUpdates as $u)
                                                            <div class="list-group-item" style="border: 0; border-bottom: 1px solid #eee;">
                                                                <div class="d-flex justify-content-between" style="gap: 10px;">
                                                                    <div>
                                                                        <div style="font-weight: 900; color:#06131a;">{{ ucfirst(str_replace('_',' ', $u->status)) }}</div>
                                                                        <div class="small" style="color:#334155;">{{ $u->message }}@if($u->location) <span style="color:#6c757d;">• {{ $u->location }}</span>@endif</div>
                                                                    </div>
                                                                    <div class="small" style="color:#6c757d; white-space: nowrap;">
                                                                        {{ optional($u->occurred_at ?? $u->created_at)->format('M d, Y h:i A') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Reviews tab -->
                                            <div class="tab-pane fade" id="reviews-{{ $orderUid }}" role="tabpanel">
                                                @if(!$isDelivered)
                                                    <div class="small" style="color:#6c757d;">Reviews unlock after delivery.</div>
                                                @else
                                                    @foreach($order->items as $item)
                                                        @php($review = $item->review)
                                                        <div style="border: 1px solid #eee; border-radius: 12px; padding: 12px; margin-bottom: 10px;">
                                                            <div style="font-weight: 900; color:#06131a;">{{ $item->product->product_name ?? 'Product' }}</div>
                                                            @if(!empty($item->variant_name))
                                                                <div class="small" style="color:#6c757d;">Variant: {{ $item->variant_name }}</div>
                                                            @endif

                                                            @if($review)
                                                                <div class="small" style="margin-top: 6px; color:#334155;">
                                                                    <span style="font-weight: 900;">Rating:</span> {{ $review->rating }}/5
                                                                    @if($review->title) <span style="color:#6c757d;">• {{ $review->title }}</span>@endif
                                                                </div>
                                                                @if($review->body)
                                                                    <div class="small" style="margin-top: 4px; color:#475569; white-space: pre-wrap;">{{ $review->body }}</div>
                                                                @endif
                                                            @else
                                                                <form method="POST" action="{{ route('orders.review', $order) }}" style="margin-top: 8px; position: relative; z-index: 2;">
                                                                    @csrf
                                                                    <input type="hidden" name="order_item_id" value="{{ $item->id }}">
                                                                    <div class="row g-2">
                                                                        <div class="col-12 col-md-3">
                                                                            <label class="small" style="font-weight: 900; color:#06131a;">Rating</label>
                                                                            <select name="rating" class="form-select form-select-sm" required>
                                                                                <option value="">Select…</option>
                                                                                @for($r=5;$r>=1;$r--)
                                                                                    <option value="{{ $r }}">{{ $r }} star{{ $r===1?'':'s' }}</option>
                                                                                @endfor
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-12 col-md-9">
                                                                            <label class="small" style="font-weight: 900; color:#06131a;">Title (optional)</label>
                                                                            <input name="title" class="form-control form-control-sm" maxlength="120" placeholder="Quick summary">
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label class="small" style="font-weight: 900; color:#06131a;">Review (optional)</label>
                                                                            <textarea name="body" class="form-control form-control-sm" rows="2" maxlength="5000" placeholder="Tell others what you liked or disliked"></textarea>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius: 999px; font-weight: 900;">
                                                                                <i class="fas fa-star me-1"></i>Submit review
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <!-- Returns tab -->
                                            <div class="tab-pane fade" id="returns-{{ $orderUid }}" role="tabpanel">
                                                @if($isCompleted)
                                                    <div class="small" style="color:#6c757d;">This order is marked complete. Returns are disabled.</div>
                                                @elseif(!$isDelivered)
                                                    <div class="small" style="color:#6c757d;">Returns unlock after delivery.</div>
                                                @elseif($openReturn)
                                                    <div style="border: 1px solid #eee; border-radius: 12px; padding: 12px;">
                                                        <div style="font-weight: 900; color:#06131a;">Return request: {{ ucfirst(str_replace('_',' ', $openReturn->status)) }}</div>
                                                        @if($openReturn->deadline_at)
                                                            <div class="small" style="color:#6c757d;">Return window ends: {{ $openReturn->deadline_at->format('M d, Y') }}</div>
                                                        @endif
                                                        @if($openReturn->reason)
                                                            <div class="small" style="margin-top: 6px; color:#475569; white-space: pre-wrap;">{{ $openReturn->reason }}</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <form method="POST" action="{{ route('orders.return', $order) }}" style="border: 1px solid #eee; border-radius: 12px; padding: 12px; position: relative; z-index: 2;">
                                                        @csrf
                                                        <div class="small" style="color:#6c757d; margin-bottom: 8px;">You have 7 days to return selected items after submitting.</div>
                                                        <label class="small" style="font-weight: 900; color:#06131a;">Reason</label>
                                                        <textarea name="reason" class="form-control form-control-sm" rows="2" required maxlength="2000" placeholder="Why are you returning the item(s)?"></textarea>

                                                        <div style="margin-top: 10px;">
                                                            <div class="small" style="font-weight: 900; color:#06131a; margin-bottom: 6px;">Select items to return</div>
                                                            @foreach($order->items as $idx => $item)
                                                                <div class="d-flex align-items-center" style="gap: 10px; padding: 8px 0; border-bottom: 1px dashed #eee;">
                                                                    <input type="checkbox" class="form-check-input js-return-item" data-target="return-qty-{{ $orderUid }}-{{ $item->id }}" style="margin-top: 0;">
                                                                    <div style="flex: 1;">
                                                                        <div style="font-weight: 800; color:#06131a;">{{ $item->product->product_name ?? 'Product' }}</div>
                                                                        @if(!empty($item->variant_name))
                                                                            <div class="small" style="color:#6c757d;">Variant: {{ $item->variant_name }}</div>
                                                                        @endif
                                                                    </div>
                                                                    <input type="hidden" name="items[{{ $idx }}][order_item_id]" value="{{ $item->id }}" disabled>
                                                                    <input id="return-qty-{{ $orderUid }}-{{ $item->id }}" type="number" name="items[{{ $idx }}][quantity]" class="form-control form-control-sm" value="1" min="1" max="{{ (int) $item->quantity }}" style="width: 90px;" disabled>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 999px; font-weight: 900; margin-top: 10px;">
                                                            <i class="fas fa-arrow-rotate-left me-1"></i>Request return
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>

                                            <!-- Help tab -->
                                            <div class="tab-pane fade" id="help-{{ $orderUid }}" role="tabpanel">
                                                <div class="d-flex flex-wrap" style="gap: 8px; position: relative; z-index: 2;">
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('contact.index') }}" style="border-radius: 999px; font-weight: 900;">
                                                        <i class="fas fa-envelope me-1"></i>Contact
                                                    </a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.index') }}" style="border-radius: 999px; font-weight: 900;">
                                                        <i class="fas fa-bag-shopping me-1"></i>Shop
                                                    </a>
                                                </div>
                                                <div class="small" style="color:#6c757d; margin-top: 10px;">Need help? Send us your order number: <strong>{{ $order->display_order_number }}</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Requests Modal (shows all return requests) -->
            <div class="modal fade" id="requestsModal" tabindex="-1" aria-labelledby="requestsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                        <div class="modal-header" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); color: #fff;">
                            <h5 class="modal-title" id="requestsModalLabel" style="font-weight: 900; margin: 0;">
                                <i class="fas fa-rotate-left me-1" style="color:#ffc107;"></i>Requests
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="background: rgba(255,255,255,0.98);">
                            @if($allReturnsSorted->isEmpty())
                                <div class="small" style="color:#6c757d;">No return requests yet.</div>
                            @else
                                <div class="list-group" style="border-radius: 12px; overflow: hidden;">
                                    @foreach($allReturnsSorted as $r)
                                        @php($rOrderUid = 'o' . (int) $r->order_id)
                                        <div class="list-group-item" style="border: 0; border-bottom: 1px solid #eee;">
                                            <div class="d-flex justify-content-between" style="gap: 10px;">
                                                <div>
                                                    <div style="font-weight: 900; color:#06131a;">
                                                        {{ $orderNumbers[(int) $r->order_id] ?? ('Order #' . $r->order_id) }}
                                                        <span class="badge" style="background:#fde68a; color:#7c2d12; margin-left: 6px;">{{ ucfirst(str_replace('_',' ', $r->status)) }}</span>
                                                    </div>
                                                    @if($r->deadline_at)
                                                        <div class="small" style="color:#6c757d;">Return window ends: {{ $r->deadline_at->format('M d, Y') }}</div>
                                                    @endif
                                                    @if($r->reason)
                                                        <div class="small" style="color:#475569; white-space: pre-wrap;">{{ $r->reason }}</div>
                                                    @endif
                                                </div>
                                                <div style="white-space: nowrap; position: relative; z-index: 2;">
                                                    @if(isset($orderNumbers[(int) $r->order_id]))
                                                        <a href="#" class="btn btn-sm btn-outline-primary" data-open-order-modal="#orderModal-{{ $rOrderUid }}" style="border-radius: 999px; font-weight: 900;">Open</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-return-item').forEach(function (cb) {
            cb.addEventListener('change', function () {
                const qtyId = cb.getAttribute('data-target');
                const qty = qtyId ? document.getElementById(qtyId) : null;
                const hidden = cb.closest('div')?.querySelector('input[type="hidden"]');
                const enable = cb.checked;
                if (qty) qty.disabled = !enable;
                if (hidden) hidden.disabled = !enable;
            });
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-open-order-modal]');
            if (!btn) return;
            e.preventDefault();

            const targetSel = btn.getAttribute('data-open-order-modal');
            const targetEl = targetSel ? document.querySelector(targetSel) : null;
            const reqEl = document.getElementById('requestsModal');

            if (!targetEl || !reqEl || !window.bootstrap) return;

            const reqModal = bootstrap.Modal.getInstance(reqEl) || new bootstrap.Modal(reqEl);
            reqModal.hide();
            window.setTimeout(function () {
                new bootstrap.Modal(targetEl).show();
            }, 250);
        });
    });
</script>

<style>
    .order-summary-card {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .order-summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.15) !important;
    }

    /* Smaller modal (Shopee-ish) */
    .order-details-modal .modal-dialog {
        max-width: 720px;
    }

    @media (max-width: 768px) {
        .order-details-modal .modal-dialog {
            max-width: 96vw;
            margin: 0.75rem auto;
        }
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .col-12 {
        animation: slideInUp 0.5s ease-out forwards;
    }

    .col-12:nth-child(1) { animation-delay: 0.05s; }
    .col-12:nth-child(2) { animation-delay: 0.1s; }
    .col-12:nth-child(3) { animation-delay: 0.15s; }
    .col-12:nth-child(4) { animation-delay: 0.2s; }

    /* Responsive */
    @media (max-width: 991px) {
        .order-summary-card { transform: none; }
    }

    @media (max-width: 768px) {
        h1 {
            font-size: 1.4rem !important;
        }

        table {
            font-size: 0.75rem !important;
        }
    }
</style>
@endsection
