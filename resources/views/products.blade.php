@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <!-- Error/Success Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert" style="border-radius: 8px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert" style="border-radius: 8px;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <section class="py-5" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%);">
        <div class="container text-center text-white">
            <h1 class="display-4 fw-bold mb-3">{{ isset($selectedCategory) ? $selectedCategory : 'All Products' }}</h1>
            <p class="lead mb-4">{{ isset($selectedCategory) ? 'Browse our ' . strtolower($selectedCategory) . ' collection' : 'Browse our complete collection of premium gadgets and accessories' }}</p>

            <!-- Search (works with q=... on this page) -->
            <form action="{{ route('products.index') }}" method="GET" class="mx-auto" style="max-width: 720px;">
                @if(isset($selectedCategory) && $selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}" />
                @endif
                <div class="input-group" style="border-radius: 14px; overflow: hidden;">
                    <span class="input-group-text" style="background: rgba(255,255,255,0.14); border: 1px solid rgba(255,255,255,0.22); color: #fff;">
                        <i class="fas fa-magnifying-glass"></i>
                    </span>
                    <input type="search" name="q" value="{{ $searchQuery ?? '' }}" class="form-control" placeholder="Search products" style="border: 1px solid rgba(255,255,255,0.22); background: rgba(255,255,255,0.92);" />
                    <button class="btn btn-warning" type="submit" style="font-weight: 800;">Search</button>
                </div>
            </form>
            
            <!-- Category Filter Buttons -->
            <div class="d-flex justify-content-start justify-content-lg-center gap-2 mt-4 flex-nowrap overflow-auto category-scroll">
                <a href="{{ route('products.index') }}" class="btn {{ !isset($selectedCategory) ? 'btn-warning' : 'btn-outline-light' }}" style="border-radius: 25px; padding: 10px 24px; font-weight: 600;">
                    All Products
                </a>
                @forelse($categories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat, 'q' => ($searchQuery ?? null)]) }}" class="btn {{ isset($selectedCategory) && $selectedCategory == $cat ? 'btn-warning' : 'btn-outline-light' }}" style="border-radius: 25px; padding: 10px 24px; font-weight: 600;">
                        {{ $cat }}
                    </a>
                @empty
                    <p class="text-white">No categories available</p>
                @endforelse
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <!-- Products Grid -->
            <div class="col-12">
                <h3 class="mb-4" style="font-weight: 700; color: #2c3e50;">Products Catalog</h3>
                
                @if($products->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x mb-3" style="color: #ccc;"></i>
                        <p class="text-muted">No products available yet.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($products as $product)
                        <div class="col-6 col-md-4">
                            <div class="card product-card h-100 border-0 shadow-sm" style="transition: all 0.3s ease; border-radius: 12px; overflow: hidden; background: #ffffff;">
                                <a href="{{ route('product.show', $product->product_id) }}" class="text-decoration-none">
                                    <div class="card-img-wrapper position-relative overflow-hidden" style="height: 280px; background: #ffffff; cursor: pointer; border: 1px solid #f0f0f0;">
                                        <img src="{{ $product->image_url }}" class="card-img-top w-100 h-100" alt="{{ $product->product_name }}" style="object-fit: contain; padding: 10px; transition: transform 0.3s ease;" onerror="this.onerror=null; this.src='/images/'+(this.getAttribute('data-filename')||this.src.split('/').pop());" data-filename="{{ basename($product->image_path ?? ($product->image ?? '')) }}">
                                        <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0, 0, 0, 0); transition: background 0.3s ease; pointer-events: none;">
                                            <span class="badge bg-dark text-white px-3 py-2" style="opacity: 0; transition: opacity 0.3s ease;">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                <div class="card-body d-flex flex-column p-4" style="background: #ffffff;">
                                    <a href="{{ route('product.show', $product->product_id) }}" class="text-decoration-none">
                                        <h5 class="card-title mb-2" style="font-weight: 600; font-size: 1.1rem; color: #2c3e50;">
                                            {{ $product->name ?? $product->product_name }}
                                        </h5>
                                    </a>
                                    @php($priceDisplay = $product->has_variants ? (data_get($product->price_range, 'display') ?: ('₱' . number_format($product->price, 0))) : ('₱' . number_format($product->price, 0)))
                                    <p class="card-text mb-3" style="font-size: 1.25rem; font-weight: 700; color: #000;">{{ $priceDisplay }}</p>

                                    <div class="mb-3" style="margin-top: -8px;">
                                        @php($state = $product->stock_state)
                                        <span style="font-size: 0.9rem; color: #6c757d;">
                                            @if($product->has_variants)
                                                Multiple options
                                            @elseif($state === 'unlimited')
                                                In stock
                                            @elseif($state === 'out_of_stock')
                                                Out of stock
                                            @elseif($state === 'low_stock')
                                                <span style="color:#5f6368; font-weight: 600;">Only {{ (int)($product->effective_stock ?? 0) }} left</span>
                                            @else
                                                In stock ({{ (int)($product->effective_stock ?? 0) }})
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <!-- Category and Buttons -->
                                    <div class="d-flex justify-content-between align-items-center mt-auto product-actions">
                                        @if($product->category)
                                            <span class="badge" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); font-size: 0.75rem; padding: 4px 8px;">{{ $product->category }}</span>
                                        @else
                                            <span></span>
                                        @endif
                                        <div class="d-flex gap-2 product-actions-buttons">
                                            <button class="btn btn-outline-warning add-to-cart-btn" 
                                                    data-product-id="{{ $product->product_id }}"
                                                    data-has-variants="{{ $product->has_variants ? 1 : 0 }}"
                                                    style="border-radius: 8px; padding: 10px 14px; border: 2px solid #ffc107; transition: all 0.3s ease;"
                                                    {{ $product->is_out_of_stock ? 'disabled' : '' }}>
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                            <button class="btn btn-warning buy-now-btn" 
                                                    data-product-id="{{ $product->product_id }}"
                                                    data-has-variants="{{ $product->has_variants ? 1 : 0 }}"
                                                    style="border-radius: 8px; font-weight: 600; padding: 10px 20px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; transition: all 0.3s ease;"
                                                    {{ $product->is_out_of_stock ? 'disabled' : '' }}>
                                                <i class="fas fa-bolt me-1"></i>{{ $product->has_variants ? 'Select Options' : 'Buy Now' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Hover effects for product cards
            document.addEventListener('DOMContentLoaded', function() {
                const productUrlBase = @json(url('/product'));
                const cards = document.querySelectorAll('.product-card');
                cards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-5px)';
                        this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.15)';
                        const img = this.querySelector('.card-img-top');
                        if (img) img.style.transform = 'scale(1.05)';
                        const overlay = this.querySelector('.overlay');
                        if (overlay) {
                            overlay.style.background = 'rgba(0, 0, 0, 0.1)';
                            overlay.querySelector('.badge').style.opacity = '1';
                        }
                    });
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
                        const img = this.querySelector('.card-img-top');
                        if (img) img.style.transform = 'scale(1)';
                        const overlay = this.querySelector('.overlay');
                        if (overlay) {
                            overlay.style.background = 'rgba(0, 0, 0, 0)';
                            overlay.querySelector('.badge').style.opacity = '0';
                        }
                    });
                });

                // Add to Cart functionality
                document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const productId = this.dataset.productId;
                        const hasVariants = this.dataset.hasVariants === '1';

                        if (window.isAuthenticated === false) {
                            showLoginModal();
                            return;
                        }

                        if (hasVariants) {
                            window.location.href = `${productUrlBase}/${productId}`;
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

                    // Hover effect
                    btn.addEventListener('mouseenter', function() {
                        this.style.background = '#ffc107';
                        this.style.color = '#000';
                    });
                    btn.addEventListener('mouseleave', function() {
                        this.style.background = 'transparent';
                        this.style.color = '#ffc107';
                    });
                });

                // Buy Now functionality
                document.querySelectorAll('.buy-now-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const productId = this.dataset.productId;
                        const hasVariants = this.dataset.hasVariants === '1';

                        if (hasVariants) {
                            window.location.href = `${productUrlBase}/${productId}`;
                            return;
                        }
                        
                        fetch('/buy-now', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                quantity: 1
                            })
                        })
                        .then(async (response) => {
                            if (response.redirected) {
                                window.location.href = response.url;
                                return null;
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                window.location.href = data.redirect || '/checkout';
                            } else {
                                showToast('Error', data?.message || 'Unable to proceed to checkout', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Error', 'Unable to proceed to checkout', 'error');
                        });
                    });

                    // Hover effect
                    btn.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 6px 12px rgba(255, 193, 7, 0.4)';
                    });
                    btn.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    });
                });
            });

            function showToast(title, message, type) {
                // Animate cart icon in navbar
                const cartIcon = document.querySelector('nav .fa-shopping-cart');
                if (cartIcon && type === 'success') {
                    cartIcon.classList.add('cart-bounce');
                    setTimeout(() => {
                        cartIcon.classList.remove('cart-bounce');
                    }, 600);
                }
            }
            
            // Add cart animation CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes cartBounce {
                    0%, 100% { transform: scale(1); }
                    25% { transform: scale(1.4); }
                    50% { transform: scale(0.9); }
                    75% { transform: scale(1.3); }
                }
                .cart-bounce {
                    animation: cartBounce 0.6s ease;
                    color: #ffc107 !important;
                }
            `;
            document.head.appendChild(style);

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
        </script>
    @endpush
@endsection
