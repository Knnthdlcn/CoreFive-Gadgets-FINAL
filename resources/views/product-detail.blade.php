@extends('layouts.app')

@section('title', $product->product_name)

@section('content')
<div class="container-fluid py-4">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-5">
                <div style="position: sticky; top: 70px;">
                    <div style="background: #f8f9fa; border-radius: 12px; padding: 20px;">
                                <img src="{{ $product->image_url }}" 
                                    alt="{{ $product->product_name }}" 
                                    class="img-fluid rounded"
                                    style="width: 100%; height: auto; display: block;"
                                    onerror="this.onerror=null; this.src='/images/'+this.src.split('/').pop();">
                    </div>
                </div>
            </div>

        <!-- Product Details -->
        <div class="col-md-7">
            <div class="product-details">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb" style="background: none; padding: 0; font-size: 0.9rem;">
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}" style="color: #ffc107; text-decoration: none;">Products</a></li>
                        @if($product->category)
                            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category]) }}" style="color: #ffc107; text-decoration: none;">{{ $product->category }}</a></li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
                    </ol>
                </nav>

                <!-- Product Title -->
                <h1 class="mb-2" style="font-weight: 700; color: #2c3e50; font-size: 1.75rem;">{{ $product->product_name }}</h1>

                <!-- Product Rating -->
                <div class="mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stars" style="color: #ffc107; font-size: 1rem;">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span style="color: #6c757d; font-size: 0.85rem;">(4.5/5 - 128 reviews)</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="mb-3">
                    @php($priceDisplay = $product->has_variants ? (data_get($product->price_range, 'display') ?: ('₱' . number_format($product->price, 2))) : ('₱' . number_format($product->price, 2)))
                    <h2 id="priceText" class="mb-0" style="color: #ffc107; font-weight: 700; font-size: 1.75rem;">{{ $priceDisplay }}</h2>
                    @php($state = $product->stock_state)
                    <p id="stockText" class="mb-0 mt-1" data-product-id="{{ $product->product_id }}"
                       style="font-weight: 600; font-size: 0.9rem; color: #6c757d;">
                        @if($product->has_variants)
                            Select a variant to see stock
                        @elseif($state === 'unlimited')
                            In stock
                        @elseif($state === 'out_of_stock')
                            Out of stock
                        @elseif($state === 'low_stock')
                            <span style="color:#5f6368;">Only {{ (int)($product->effective_stock ?? 0) }} left</span>
                        @else
                            In stock ({{ (int)($product->effective_stock ?? 0) }})
                        @endif
                    </p>
                </div>

                <!-- Category Badge -->
                @if($product->category)
                    <div class="mb-2">
                        <span class="badge" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 6px 12px; font-size: 0.85rem;">
                            {{ $product->category }}
                        </span>
                    </div>
                @endif

                @if($product->has_variants && $product->variants->isNotEmpty())
                    <div class="mb-3" id="variantPicker" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0" style="font-weight: 800; color: #2c3e50;">Choose a variant</h6>
                            <span class="badge" style="background: #fff7ed; color: #9a3412; border: 1px solid rgba(234,88,12,0.25);">Required</span>
                        </div>
                        <select id="variantSelect" class="form-select" style="border-radius: 10px; border: 1px solid #dee2e6; padding: 10px 12px;" aria-label="Select variant">
                            <option value="">Select an option…</option>
                            @foreach($product->variants as $v)
                                <option
                                    value="{{ $v->id }}"
                                    data-price="{{ (float) $v->effective_price }}"
                                    data-stock="{{ (int) ($v->stock ?? 0) }}"
                                    data-unlimited="{{ $v->stock_unlimited ? 1 : 0 }}"
                                >
                                    {{ $v->name }}
                                    @if($v->price !== null)
                                        — ₱{{ number_format((float)$v->price, 2) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="small text-muted mt-2">Select a variant to enable Add to Cart / Buy Now.</div>
                    </div>
                @endif

                <!-- Description -->
                <div class="mb-3">
                    <h5 style="font-weight: 700; color: #2c3e50; margin-bottom: 10px; font-size: 1.1rem;">Product Description</h5>
                    <p style="color: #6c757d; line-height: 1.6; text-align: justify; font-size: 0.95rem;">{{ $product->description }}</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <button id="addToCartBtn" class="btn btn-warning flex-fill" style="border-radius: 10px; font-weight: 600; padding: 10px 18px; font-size: 0.95rem;" {{ ($product->is_out_of_stock || $product->has_variants) ? 'disabled' : '' }} data-product-id="{{ $product->product_id }}">
                        <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                    </button>
                    <button id="buyNowBtn" class="btn flex-fill" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); color: white; border-radius: 10px; font-weight: 600; padding: 10px 18px; font-size: 0.95rem;" {{ ($product->is_out_of_stock || $product->has_variants) ? 'disabled' : '' }} data-product-id="{{ $product->product_id }}">
                        <i class="fas fa-bolt me-2"></i> Buy Now
                    </button>
                </div>

                <!-- Chat Now Button -->
                <div class="mb-3">
                    <button class="btn btn-outline-dark w-100" style="border-radius: 10px; font-weight: 600; padding: 10px 18px; font-size: 0.95rem;">
                        <i class="fas fa-comments me-2"></i> Chat Now
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="card mb-3" style="border: 1px solid #dee2e6; border-radius: 10px;">
                    <div class="card-body" style="padding: 15px;">
                        <h6 style="font-weight: 700; color: #2c3e50; margin-bottom: 12px; font-size: 1rem;">Why Shop With Us?</h6>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-shipping-fast" style="color: #ffc107;"></i>
                                <span style="color: #6c757d; font-size: 0.85rem;">Fast & Free Shipping</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-shield-alt" style="color: #ffc107;"></i>
                                <span style="color: #6c757d; font-size: 0.85rem;">Secure Payment</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-undo-alt" style="color: #ffc107;"></i>
                                <span style="color: #6c757d; font-size: 0.85rem;">30-Day Returns</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-headset" style="color: #ffc107;"></i>
                                <span style="color: #6c757d; font-size: 0.85rem;">24/7 Customer Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    @if($relatedProducts->isNotEmpty())
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-3" style="font-weight: 700; color: #2c3e50; font-size: 1.5rem;">You Might Also Like</h3>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
                @foreach($relatedProducts as $relatedProduct)
                <div class="col">
                    <a href="{{ route('product.show', $relatedProduct->product_id) }}" class="text-decoration-none">
                        <div class="card h-100 product-card" style="border: 1px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease; cursor: pointer;">
                            <div class="position-relative" style="overflow: hidden; border-radius: 10px 10px 0 0;">
                                  <img src="{{ $relatedProduct->image_url }}" 
                                     class="card-img-top" 
                                     alt="{{ $relatedProduct->product_name }}"
                                      onerror="this.onerror=null; this.src='/images/'+this.src.split('/').pop();"
                                     style="height: 180px; object-fit: cover; transition: transform 0.3s ease;">
                                @if($relatedProduct->is_out_of_stock)
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-danger" style="font-size: 0.75rem;">Out of Stock</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column" style="background: white; padding: 12px;">
                                <h6 class="card-title mb-2" style="font-weight: 600; color: #2c3e50; font-size: 0.9rem; height: 38px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    {{ $relatedProduct->product_name }}
                                </h6>
                                <p class="card-text text-muted mb-2" style="font-size: 0.8rem; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    {{ $relatedProduct->description }}
                                </p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold" style="color: #ffc107; font-size: 1.1rem;">₱{{ number_format($relatedProduct->price, 2) }}</span>
                                        <span class="badge" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); font-size: 0.75rem;">{{ $relatedProduct->category ?? 'Uncategorized' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Customer Reviews Section -->
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-3" style="font-weight: 700; color: #2c3e50; font-size: 1.5rem;">Customer Reviews</h3>
            
            <!-- Review Summary -->
            <div class="card mb-3" style="border: 1px solid #dee2e6; border-radius: 10px;">
                <div class="card-body" style="padding: 15px;">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center border-end">
                            <h1 class="display-4 mb-0" style="font-weight: 700; color: #2c3e50;">4.5</h1>
                            <div class="stars mb-2" style="color: #ffc107; font-size: 1.2rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">Based on 128 reviews</p>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <span style="width: 60px; font-size: 0.85rem;">5 stars</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar" style="width: 70%; background-color: #ffc107;"></div>
                                    </div>
                                    <span style="width: 40px; font-size: 0.85rem; text-align: right;">70%</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span style="width: 60px; font-size: 0.85rem;">4 stars</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar" style="width: 20%; background-color: #ffc107;"></div>
                                    </div>
                                    <span style="width: 40px; font-size: 0.85rem; text-align: right;">20%</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span style="width: 60px; font-size: 0.85rem;">3 stars</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar" style="width: 7%; background-color: #ffc107;"></div>
                                    </div>
                                    <span style="width: 40px; font-size: 0.85rem; text-align: right;">7%</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span style="width: 60px; font-size: 0.85rem;">2 stars</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar" style="width: 2%; background-color: #ffc107;"></div>
                                    </div>
                                    <span style="width: 40px; font-size: 0.85rem; text-align: right;">2%</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span style="width: 60px; font-size: 0.85rem;">1 star</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar" style="width: 1%; background-color: #ffc107;"></div>
                                    </div>
                                    <span style="width: 40px; font-size: 0.85rem; text-align: right;">1%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual Reviews -->
            <div class="card mb-2" style="border: 1px solid #dee2e6; border-radius: 10px;">
                <div class="card-body" style="padding: 12px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1" style="font-weight: 600; color: #2c3e50; font-size: 0.95rem;">John Doe</h6>
                            <div class="stars mb-1" style="color: #ffc107; font-size: 0.85rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 0.8rem;">2 days ago</small>
                    </div>
                    <p style="color: #6c757d; margin-bottom: 0; font-size: 0.9rem;">Excellent product! Works exactly as described. Fast shipping and great customer service.</p>
                </div>
            </div>

            <div class="card mb-2" style="border: 1px solid #dee2e6; border-radius: 10px;">
                <div class="card-body" style="padding: 12px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1" style="font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Jane Smith</h6>
                            <div class="stars mb-1" style="color: #ffc107; font-size: 0.85rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 0.8rem;">1 week ago</small>
                    </div>
                    <p style="color: #6c757d; margin-bottom: 0; font-size: 0.9rem;">Very good quality for the price. Highly recommend this seller!</p>
                </div>
            </div>

            <div class="card mb-2" style="border: 1px solid #dee2e6; border-radius: 10px;">
                <div class="card-body" style="padding: 12px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1" style="font-weight: 600; color: #2c3e50; font-size: 0.95rem;">Mike Johnson</h6>
                            <div class="stars mb-1" style="color: #ffc107; font-size: 0.85rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 0.8rem;">2 weeks ago</small>
                    </div>
                    <p style="color: #6c757d; margin-bottom: 0; font-size: 0.9rem;">Great value for money. Product arrived in perfect condition.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.product-card:hover img {
    transform: scale(1.05);
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

@keyframes cartBounce {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(0.9); }
    75% { transform: scale(1.2); }
}

.animate-cart {
    animation: cartBounce 0.6s ease;
    color: #ffc107 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.getElementById('addToCartBtn');
    const buyNowBtn = document.getElementById('buyNowBtn');
    const stockText = document.getElementById('stockText');
    const priceText = document.getElementById('priceText');
    const variantSelect = document.getElementById('variantSelect');
    const requiresVariant = Boolean(variantSelect);
    const initialPriceDisplay = priceText ? priceText.textContent : '';

    function getSelectedVariantId() {
        if (!variantSelect) return null;
        const v = variantSelect.value;
        const n = v ? Number(v) : NaN;
        return Number.isFinite(n) && n > 0 ? n : null;
    }

    function setButtonsEnabled(enabled) {
        if (addToCartBtn) addToCartBtn.disabled = !enabled;
        if (buyNowBtn) buyNowBtn.disabled = !enabled;
    }

    function setPriceDisplay(value) {
        if (!priceText) return;
        priceText.textContent = value;
    }

    // Add to Cart functionality
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const variantId = getSelectedVariantId();

            if (window.isAuthenticated === false) {
                showLoginModal();
                return;
            }

            if (requiresVariant && !variantId) {
                showToast('Error', 'Please select a variant first.', 'error');
                return;
            }
            
                        fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_variant_id: variantId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Success', data.message, 'success');
                    updateCartCount();
                } else {
                    if (data.requires_login) {
                        showLoginModal();
                        return;
                    }
                    showToast('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', 'Failed to add to cart', 'error');
            });
        });
    }

    // Buy Now functionality (go straight to checkout for this item only)
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const variantId = getSelectedVariantId();

            if (requiresVariant && !variantId) {
                showToast('Error', 'Please select a variant first.', 'error');
                return;
            }

            buyNowBtn.disabled = true;

            fetch('/buy-now', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_variant_id: variantId,
                    quantity: 1
                })
            })
            .then(async (response) => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }
                let data = null;
                try { data = await response.json(); } catch (err) { /* ignore */ }

                if (!response.ok) {
                    const message = data?.message || 'Unable to proceed to checkout.';
                    showToast('Error', message, 'error');
                    return;
                }

                if (data && data.success) {
                    window.location.href = data.redirect || '/checkout';
                } else {
                    showToast('Error', data?.message || 'Failed to add to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', 'Unable to proceed to checkout', 'error');
            })
            .finally(() => {
                buyNowBtn.disabled = false;
            });
        });
    }

    function showToast(title, message, type) {
        // Animate cart icon
        const cartIcon = document.querySelector('.fa-shopping-cart');
        if (cartIcon && type === 'success') {
            cartIcon.classList.add('animate-cart');
            setTimeout(() => {
                cartIcon.classList.remove('animate-cart');
            }, 600);
        }
    }

    function updateCartCount() {
        fetch('/cart/get')
            .then(response => response.json())
            .then(data => {
                if (data.cart_count === undefined) return;
                const count = Number(data.cart_count) || 0;
                document.querySelectorAll('[data-cart-count-badge]').forEach((badge) => {
                    badge.textContent = String(count);
                    badge.style.display = count > 0 ? 'inline-block' : 'none';
                });
            });
    }

    function applyStockUi(state, quantity) {
        const out = (state === 'out_of_stock');
        const hasVariantSelected = !requiresVariant || Boolean(getSelectedVariantId());
        setButtonsEnabled(hasVariantSelected && !out);
        if (!stockText) return;

        if (state === 'unlimited') {
            stockText.textContent = 'In stock';
        } else if (out) {
            stockText.textContent = 'Out of stock';
        } else if (state === 'low_stock') {
            stockText.textContent = `Only ${quantity} left`;
            stockText.style.color = '#5f6368';
        } else {
            stockText.textContent = `In stock (${quantity})`;
        }
    }

    function pollStock() {
        if (!stockText) return;
        const productId = stockText.dataset.productId;
        if (!productId) return;
        const variantId = getSelectedVariantId();
        const qs = variantId ? `?variant_id=${encodeURIComponent(String(variantId))}` : '';
        fetch(`/api/products/${productId}/stock${qs}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data) return;
                if (variantId && priceText && typeof data.price === 'number') {
                    setPriceDisplay('₱' + Number(data.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                } else if (!variantId && requiresVariant) {
                    setPriceDisplay(initialPriceDisplay);
                }
                applyStockUi(data.state, data.quantity);
            })
            .catch(() => {});
    }

    if (variantSelect) {
        variantSelect.addEventListener('change', () => {
            const variantId = getSelectedVariantId();
            if (!variantId) {
                setButtonsEnabled(false);
                if (stockText) stockText.textContent = 'Select a variant to see stock';
                setPriceDisplay(initialPriceDisplay);
                return;
            }
            pollStock();
        });
        // Ensure disabled until variant selected
        setButtonsEnabled(false);
    }

    // near-real-time updates (simple polling)
    if (!requiresVariant) {
        pollStock();
    }
    setInterval(() => {
        if (document.hidden) return;
        if (!requiresVariant || getSelectedVariantId()) {
            pollStock();
        }
    }, 20000);
});
</script>
@endsection
