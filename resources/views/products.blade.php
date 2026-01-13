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
            
            <!-- Category Filter Buttons -->
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <a href="{{ route('products.index') }}" class="btn {{ !isset($selectedCategory) ? 'btn-warning' : 'btn-outline-light' }}" style="border-radius: 25px; padding: 10px 24px; font-weight: 600;">
                    All Products
                </a>
                <a href="{{ route('products.index', ['category' => 'Phones']) }}" class="btn {{ isset($selectedCategory) && $selectedCategory == 'Phones' ? 'btn-warning' : 'btn-outline-light' }}" style="border-radius: 25px; padding: 10px 24px; font-weight: 600;">
                    <i class="fas fa-mobile-screen me-1"></i> Phones
                </a>
                <a href="{{ route('products.index', ['category' => 'Computing']) }}" class="btn {{ isset($selectedCategory) && $selectedCategory == 'Computing' ? 'btn-warning' : 'btn-outline-light' }}" style="border-radius: 25px; padding: 10px 24px; font-weight: 600;">
                    <i class="fas fa-laptop me-1"></i> Computing
                </a>
                <a href="{{ route('products.index', ['category' => 'Accessories']) }}" class="btn {{ isset($selectedCategory) && $selectedCategory == 'Accessories' ? 'btn-warning' : 'btn-outline-light' }}" style="border-radius: 25px; padding: 10px 24px; font-weight: 600;">
                    <i class="fas fa-mouse me-1"></i> Accessories
                </a>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <!-- Admin Add Product Section -->
            @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="col-lg-12 mb-5">
                <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <h4 class="mb-4" style="font-weight: 700; color: #2c3e50;">
                            <i class="fas fa-plus-circle me-2" style="color: #ffc107;"></i>Add New Product
                        </h4>
                        
                        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold" style="color: #2c3e50;">Product Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter product name" required style="border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 16px;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold" style="color: #2c3e50;">Price (₱)</label>
                                    <input type="number" name="price" step="0.01" class="form-control" placeholder="Enter price" required style="border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 16px;">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold" style="color: #2c3e50;">Description</label>
                                    <textarea name="description" rows="3" class="form-control" placeholder="Enter product description" required style="border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 16px; resize: none;"></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold" style="color: #2c3e50;">Product Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" style="border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 16px;">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 700; border-radius: 10px; padding: 14px; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.25); transition: all 0.3s ease;">
                                        <i class="fas fa-plus me-2"></i>Add Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

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
                        <div class="col-md-4">
                            <div class="card product-card h-100 border-0 shadow-sm" style="transition: all 0.3s ease; border-radius: 12px; overflow: hidden; background: #ffffff;">
                                <div class="card-img-wrapper position-relative overflow-hidden view-product" style="height: 280px; background: #ffffff; cursor: pointer; border: 1px solid #f0f0f0;" data-product='@json($product)'>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top w-100 h-100" alt="{{ $product->name ?? $product->product_name }}" style="object-fit: contain; padding: 10px; transition: transform 0.3s ease;">
                                    @elseif($product->image_url)
                                        <img src="{{ $product->image_url }}" class="card-img-top w-100 h-100" alt="{{ $product->product_name }}" style="object-fit: contain; padding: 10px; transition: transform 0.3s ease;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-image" style="font-size: 3rem; color: #ddd;"></i>
                                        </div>
                                    @endif
                                    <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0, 0, 0, 0); transition: background 0.3s ease; pointer-events: none;">
                                        <span class="badge bg-dark text-white px-3 py-2" style="opacity: 0; transition: opacity 0.3s ease;">
                                            <i class="fas fa-eye me-1"></i> Quick View
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column p-4" style="background: #ffffff;">
                                    <h5 class="card-title mb-2" style="font-weight: 600; font-size: 1.1rem; color: #2c3e50;">
                                        {{ $product->name ?? $product->product_name }}
                                    </h5>
                                    <p class="card-text mb-3" style="font-size: 1.25rem; font-weight: 700; color: #000;">₱{{ number_format($product->price, 0) }}</p>
                                    <div class="card-actions mt-auto">
                                        <a href="#" class="btn btn-warning w-100 mb-2 buy-now" data-product='@json($product)' title="Buy now" style="border-radius: 8px; font-weight: 600; padding: 12px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #000; box-shadow: 0 4px 6px rgba(255, 193, 7, 0.3); transition: all 0.3s ease;">
                                            <i class="fas fa-bolt me-2"></i>Buy Now
                                        </a>
                                        <a href="#" class="btn btn-outline-secondary w-100 add-to-cart" data-product='@json($product)' title="Add to cart" style="border-radius: 8px; font-weight: 600; padding: 12px; border: 2px solid #6c757d; transition: all 0.3s ease;">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </a>
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

    <!-- Product View Modal -->
    <div class="modal fade" id="productViewModal" tabindex="-1" aria-labelledby="productModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="productModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="pvImage" src="" class="product-img" alt="Product">
                    </div>
                    <p id="pvDesc" class="mt-3 text-muted"></p>
                    <h4 id="pvPrice" class="text-success mt-3" style="font-size: 1.5rem; font-weight: 700;"></h4>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="pvAddBtn"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    <button type="button" class="btn btn-success" id="pvBuyBtn"><i class="fas fa-bolt"></i> Buy Now</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/cart.js') }}"></script>
        <script src="{{ asset('js/products.js') }}"></script>
        <script>
            // Hover effects for product cards
            document.addEventListener('DOMContentLoaded', function() {
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

                // Button hover effects
                const buyNowBtns = document.querySelectorAll('.buy-now');
                buyNowBtns.forEach(btn => {
                    btn.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 6px 12px rgba(255, 193, 7, 0.4)';
                    });
                    btn.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 4px 6px rgba(255, 193, 7, 0.3)';
                    });
                });

                const addToCartBtns = document.querySelectorAll('.add-to-cart');
                addToCartBtns.forEach(btn => {
                    btn.addEventListener('mouseenter', function() {
                        this.style.background = '#6c757d';
                        this.style.color = 'white';
                        this.style.borderColor = '#6c757d';
                    });
                    btn.addEventListener('mouseleave', function() {
                        this.style.background = 'transparent';
                        this.style.color = '#6c757d';
                        this.style.borderColor = '#6c757d';
                    });
                });
            });
        </script>
    @endpush
@endsection
