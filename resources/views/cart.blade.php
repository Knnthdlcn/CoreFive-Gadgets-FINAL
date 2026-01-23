@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
    @if(!Auth::check())
        @include('auth.modals')
    @endif

    <main class="container py-5 content-with-footer">
        <!-- Page Header -->
        <div class="mb-5">
            <h2 class="mb-2" style="font-weight: 700; font-size: 2rem; color: #2c3e50;">Shopping Cart</h2>
            <p class="text-muted mb-0">Review and manage your items before checkout</p>
        </div>

        @if(empty($cartItems))
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-5x mb-4" style="color: #dee2e6;"></i>
                <h4 class="mb-3" style="color: #6c757d;">Your cart is empty</h4>
                <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet</p>
                <a href="{{ route('products.index') }}" class="btn btn-warning" style="border-radius: 12px; font-weight: 600; padding: 12px 32px;">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
        @else
            <form id="cartCheckoutForm" method="GET" action="{{ route('checkout.index') }}">
            <div class="row">
            <!-- Items list -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <div class="d-flex align-items-center justify-content-between mb-3" style="gap: 12px;">
                            <label class="d-flex align-items-center" style="gap: 10px; cursor: pointer; user-select: none;">
                                <input id="cartSelectAll" type="checkbox" class="form-check-input" style="width: 22px; height: 22px; border-radius: 6px; accent-color: #ff6a00; cursor: pointer;">
                                <span style="font-weight: 700; color: #2c3e50;">Select all</span>
                            </label>
                            <span class="small text-muted" id="cartSelectAllHint"></span>
                        </div>

                        <div class="cart-items">
                            @foreach($cartItems as $item)
                                @php
                                    $variant = $item['variant'] ?? null;
                                    $variantId = $item['product_variant_id'] ?? null;
                                @endphp
                                <div class="list-group-item mb-3" style="border: 1px solid #e9ecef; border-radius: 12px; padding: 16px; background: #fff;">
                                <div class="cart-item-row">
                                        <div class="cart-select-wrap">
                                            <input
                                                class="cart-select-item"
                                                type="checkbox"
                                                name="selected_items[]"
                                                value="{{ $item['cart_item_id'] ?? '' }}"
                                                data-line-total="{{ ((float)($item['unit_price'] ?? ($item['product']->price ?? 0))) * (int) ($item['quantity'] ?? 0) }}"
                                                aria-label="Select {{ $item['product']->product_name }}"
                                            />
                                        </div>
                                     <img src="{{ $item['product']->image_url }}" 
                                         alt="{{ $item['product']->product_name }}" 
                                         class="cart-item-img"
                                         onerror="this.onerror=null; this.src='/images/'+this.src.split('/').pop();">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1" style="font-weight: 600; color: #2c3e50;">
                                                    <a href="{{ route('product.show', $item['product']->product_id) }}" style="color: #2c3e50; text-decoration: none;">
                                                        {{ $item['product']->product_name }}
                                                    </a>
                                                </h6>
                                                @if(!empty($item['variant_name']))
                                                    <div class="small text-muted" style="margin-top:-2px;">Variant: {{ $item['variant_name'] }}</div>
                                                @endif
                                                @if($item['product']->category)
                                                    <span class="badge" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); font-size: 0.75rem;">
                                                        {{ $item['product']->category }}
                                                    </span>
                                                @endif

                                                @php
                                                    // Defaults (prevents undefined variable edge-cases)
                                                    $stockState = 'ok';
                                                    $stockText = 'Stock: -';

                                                    $p = $item['product'] ?? null;
                                                    $isUnlimited = (bool) ($variant?->stock_unlimited ?? $p?->stock_unlimited ?? false);
                                                    $qty = (int) ($variant?->stock ?? $p?->stock ?? 0);
                                                    $stockState = $isUnlimited ? 'unlimited' : ($qty <= 0 ? 'out' : ($qty <= 5 ? 'low' : 'ok'));
                                                    $stockText = $isUnlimited ? 'Stock: ∞' : ('Stock: ' . $qty);
                                                @endphp
                                                <span class="stock-pill stock-{{ $stockState ?? 'ok' }} ms-2">{{ $stockText ?? 'Stock: -' }}</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" 
                                                    data-product-id="{{ $item['product']->product_id }}"
                                                    data-variant-id="{{ $variantId ?? '' }}"
                                                    data-line-key="{{ (string) data_get($item, 'product.product_id', '') . ':' . (string) ($variantId ?? 0) }}"
                                                    style="border-radius: 8px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.4;">
                                            {{ Str::limit($item['product']->description, 100) }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary decrease-qty" 
                                                        data-product-id="{{ $item['product']->product_id }}"
                                                        data-variant-id="{{ $variantId ?? '' }}"
                                                        data-line-key="{{ (string) data_get($item, 'product.product_id', '') . ':' . (string) ($variantId ?? 0) }}"
                                                        style="border-radius: 8px; width: 32px;">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="form-control text-center quantity-input" 
                                                       value="{{ $item['quantity'] }}" 
                                                       min="1" 
                                                       @if(!($variant?->stock_unlimited ?? $item['product']->stock_unlimited)) max="{{ (int)($variant?->stock ?? $item['product']->stock ?? 0) }}" @endif
                                                       data-product-id="{{ $item['product']->product_id }}"
                                                       data-variant-id="{{ $variantId ?? '' }}"
                                                       data-line-key="{{ (string) data_get($item, 'product.product_id', '') . ':' . (string) ($variantId ?? 0) }}"
                                                       style="width: 70px; border-radius: 8px;">
                                                <button type="button" class="btn btn-sm btn-outline-secondary increase-qty" 
                                                        data-product-id="{{ $item['product']->product_id }}"
                                                        data-variant-id="{{ $variantId ?? '' }}"
                                                        data-line-key="{{ (string) data_get($item, 'product.product_id', '') . ':' . (string) ($variantId ?? 0) }}"
                                                        data-max-stock="{{ ($variant?->stock_unlimited ?? $item['product']->stock_unlimited) ? 999999 : (int)($variant?->stock ?? $item['product']->stock ?? 0) }}"
                                                        style="border-radius: 8px; width: 32px;">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <div>
                                                <strong style="font-size: 1.2rem; color: #ffc107;">₱{{ number_format(((float)($item['unit_price'] ?? ($item['product']->price ?? 0))) * (int)($item['quantity'] ?? 0), 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-start align-items-center mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight: 600; padding: 10px 20px;">
                                <i class="fas fa-arrow-left me-2"></i>Continue shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; position: sticky; top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 20px; font-size: 1.2rem;">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2" style="padding: 8px 0; font-size: 0.95rem; color: #495057;">
                            <span>Subtotal (<span id="cartSelectedCount">0</span> items)</span>
                            <strong id="cartSubtotal" style="color: #2c3e50;">₱0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3" style="padding: 8px 0; font-size: 0.95rem; color: #495057;">
                            <span>Shipping</span>
                            <span class="text-muted">Calculated at checkout</span>
                        </div>
                        <hr style="margin: 12px 0; border-color: #e9ecef;">
                        <div class="d-flex justify-content-between mb-3" style="padding: 8px 0;">
                            <span style="font-weight: 600; color: #2c3e50;">Total</span>
                            <strong id="cartTotal" style="font-size: 1.4rem; color: #ffc107;">₱0.00</strong>
                        </div>

                        @if(Auth::check())
                            <button type="submit" id="proceedCheckoutBtn" class="btn btn-warning w-100 mb-2" disabled style="border-radius: 12px; font-weight: 600; padding: 14px; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);">
                                <i class="fas fa-credit-card me-2"></i><span id="proceedCheckoutLabel">Proceed to Checkout</span>
                            </button>
                            <div id="cartSelectError" class="small" style="display:none; color:#dc3545; font-weight: 600; margin-top: -4px; margin-bottom: 8px;">
                                Please select at least one item to checkout.
                            </div>
                        @else
                            <a href="#" class="btn btn-warning w-100 mb-2" onclick="event.preventDefault(); showLoginModal();" style="border-radius: 12px; font-weight: 600; padding: 14px; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                        @endif

                        <button class="btn btn-outline-danger w-100" 
                                id="clearCartBtn"
                                style="border-radius: 12px; font-weight: 600; padding: 12px;">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        @endif
    </main>

    @push('styles')
    <style>
        .cart-item-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .cart-select-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 6px;
        }
        .cart-select-item {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            accent-color: #ff6a00;
            cursor: pointer;
        }

        .cart-item-img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
            padding: 8px;
        }

        @media (max-width: 575.98px) {
            .cart-item-img {
                width: 92px;
                height: 92px;
            }
            .cart-item-row {
                gap: 10px;
            }
        }

        .stock-pill {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid rgba(0,0,0,0.08);
            background: #f8fafc;
            color: #52606d;
            vertical-align: middle;
            margin-top: 4px;
        }
        .stock-pill.stock-low { background: #fff7ed; color: #b45309; border-color: rgba(245, 158, 11, 0.35); }
        .stock-pill.stock-out { background: #fef2f2; color: #b91c1c; border-color: rgba(239, 68, 68, 0.35); }
        .stock-pill.stock-unlimited { background: #eff6ff; color: #1d4ed8; border-color: rgba(59, 130, 246, 0.35); }
    </style>
    @endpush

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllEl = document.getElementById('cartSelectAll');
        const selectAllHintEl = document.getElementById('cartSelectAllHint');

        function formatMoney(n) {
            const num = Number(n) || 0;
            return '₱' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function syncSelectAllState() {
            if (!selectAllEl) return;
            const checkboxes = Array.from(document.querySelectorAll('.cart-select-item'));
            if (checkboxes.length === 0) {
                selectAllEl.checked = false;
                selectAllEl.indeterminate = false;
                selectAllEl.disabled = true;
                if (selectAllHintEl) selectAllHintEl.textContent = '';
                return;
            }

            const checkedCount = checkboxes.filter(cb => cb.checked).length;
            selectAllEl.disabled = false;
            selectAllEl.checked = checkedCount === checkboxes.length;
            selectAllEl.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            if (selectAllHintEl) {
                selectAllHintEl.textContent = checkedCount > 0 ? (checkedCount + ' selected') : '';
            }
        }

        function recalcSelectedSummary() {
            const checkboxes = Array.from(document.querySelectorAll('.cart-select-item'));
            const selected = checkboxes.filter(cb => cb.checked);
            const selectedCount = selected.length;
            const selectedSubtotal = selected.reduce((sum, cb) => sum + (Number(cb.dataset.lineTotal) || 0), 0);

            const countEl = document.getElementById('cartSelectedCount');
            const subtotalEl = document.getElementById('cartSubtotal');
            const totalEl = document.getElementById('cartTotal');
            const proceedBtn = document.getElementById('proceedCheckoutBtn');
            const proceedLabel = document.getElementById('proceedCheckoutLabel');
            const errEl = document.getElementById('cartSelectError');

            if (countEl) countEl.textContent = String(selectedCount);
            if (subtotalEl) subtotalEl.textContent = formatMoney(selectedSubtotal);
            if (totalEl) totalEl.textContent = formatMoney(selectedSubtotal);

            if (proceedLabel) {
                proceedLabel.textContent = selectedCount > 0
                    ? ('Proceed to Checkout (' + selectedCount + ')')
                    : 'Proceed to Checkout';
            }
            if (proceedBtn) {
                proceedBtn.disabled = selectedCount === 0;
            }
            if (errEl) {
                errEl.style.display = 'none';
            }

            syncSelectAllState();
        }

        document.querySelectorAll('.cart-select-item').forEach(cb => {
            cb.addEventListener('change', recalcSelectedSummary);
        });

        if (selectAllEl) {
            selectAllEl.addEventListener('change', function() {
                const shouldSelect = Boolean(selectAllEl.checked);
                document.querySelectorAll('.cart-select-item').forEach(cb => {
                    cb.checked = shouldSelect;
                });
                selectAllEl.indeterminate = false;
                recalcSelectedSummary();
            });
        }

        const checkoutForm = document.getElementById('cartCheckoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                const selected = Array.from(document.querySelectorAll('.cart-select-item')).some(cb => cb.checked);
                const errEl = document.getElementById('cartSelectError');
                if (!selected) {
                    e.preventDefault();
                    if (errEl) errEl.style.display = 'block';
                }
            });
        }

        recalcSelectedSummary();

        // Remove item from cart
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const variantId = this.dataset.variantId || null;

                fetch('{{ route("cart.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId, product_variant_id: variantId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        });

        // Update quantity
        function updateQuantity(productId, variantId, quantity) {
            fetch('{{ route("cart.update") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_variant_id: variantId || null,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Increase quantity
        document.querySelectorAll('.increase-qty').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const variantId = this.dataset.variantId || null;
                const maxStock = parseInt(this.dataset.maxStock);
                const lineKey = this.dataset.lineKey;
                const input = lineKey
                    ? document.querySelector(`.quantity-input[data-line-key="${lineKey}"]`)
                    : document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                const currentQty = parseInt(input.value);
                
                if (currentQty < maxStock) {
                    updateQuantity(productId, variantId, currentQty + 1);
                }
            });
        });

        // Decrease quantity
        document.querySelectorAll('.decrease-qty').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const variantId = this.dataset.variantId || null;
                const lineKey = this.dataset.lineKey;
                const input = lineKey
                    ? document.querySelector(`.quantity-input[data-line-key="${lineKey}"]`)
                    : document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
                const currentQty = parseInt(input.value);
                
                if (currentQty > 1) {
                    updateQuantity(productId, variantId, currentQty - 1);
                }
            });
        });

        // Manual input change
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const variantId = this.dataset.variantId || null;
                const quantity = parseInt(this.value);
                
                if (quantity > 0) {
                    updateQuantity(productId, variantId, quantity);
                }
            });
        });

        // Clear cart
        const clearCartBtn = document.getElementById('clearCartBtn');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', function() {
                fetch('{{ route("cart.clear") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        }
    });
    </script>
    @endpush
@endsection
