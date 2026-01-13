@extends('layouts.app')

@section('title', 'E-Shop - Welcome')

@section('content')
    <!-- Error Message Modal Bubble -->
    @if(session('error'))
        <div class="alert notification-modal-container" style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 90%; max-width: 500px; animation: slideDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); border: none; background: transparent; padding: 0; margin: 0;">
            <div class="notification-bubble" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); border-radius: 20px; padding: 28px; box-shadow: 0 10px 40px rgba(13, 71, 161, 0.3); position: relative; overflow: hidden;">
                <!-- Background accent -->
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255, 193, 7, 0.15); border-radius: 50%;"></div>
                
                <div class="d-flex align-items-flex-start gap-3" style="position: relative; z-index: 1;">
                    <!-- Icon -->
                    <div style="flex-shrink: 0; padding-top: 4px;">
                        <i class="fas fa-lock-open" style="font-size: 2.5rem; color: #ffc107;"></i>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-grow-1">
                        <h5 style="margin: 0; color: white; font-weight: 700; font-size: 1.2rem;">Sign In Required</h5>
                        <p style="margin: 10px 0 0 0; color: rgba(255, 255, 255, 0.9); font-size: 0.95rem; line-height: 1.5;">{{ session('error') }}</p>
                    </div>
                    
                    <!-- Close button -->
                    <button type="button" class="close-bubble" data-bs-dismiss="alert" aria-label="Close" style="background: rgba(255, 193, 7, 0.2); border: 2px solid #ffc107; color: #ffc107; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; font-size: 1.2rem; padding: 0; flex-shrink: 0;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <style>
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }

            .close-bubble:hover {
                background: #ffc107 !important;
                color: #1565c0 !important;
                box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
            }

            .notification-modal-container {
                animation: slideDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
        </style>
    @endif

    <!-- HERO -->
    <header class="site-hero text-center text-white py-5" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%);">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Welcome to CoreFive Gadgets</h1>
            <p class="lead mb-4">Discover premium products for your business and lifestyle</p>
        </div>
    </header>

    <!-- PRODUCTS -->
    <section class="py-5" id="featured-products">
        <div class="container">
            <h2 class="text-center mb-2" style="font-size: 2.5rem; font-weight: 700;">Featured Products</h2>
            <p class="text-center text-muted mb-5">Handpicked gadgets and accessories for tech enthusiasts</p>
            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm product-card" style="transition: all 0.3s ease; border-radius: 12px; overflow: hidden; background: #ffffff;">
                            <div class="card-img-wrapper position-relative overflow-hidden view-product" style="height: 280px; background: #ffffff; cursor: pointer; border: 1px solid #f0f0f0;" data-product='@json($product)'>
                                <img src="{{ $product->image_url }}" class="card-img-top w-100 h-100" alt="{{ $product->product_name }}" style="object-fit: contain; padding: 10px; transition: transform 0.3s ease;">
                                <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0, 0, 0, 0); transition: background 0.3s ease; pointer-events: none;">
                                    <span class="badge bg-dark text-white px-3 py-2" style="opacity: 0; transition: opacity 0.3s ease;">
                                        <i class="fas fa-eye me-1"></i> Quick View
                                    </span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column p-4" style="background: #ffffff;">
                                <h5 class="card-title mb-2" style="font-weight: 600; font-size: 1.1rem; color: #2c3e50;">
                                    {{ $product->product_name }}
                                </h5>
                                <p class="card-text mb-3" style="font-size: 1.25rem; font-weight: 700; color: #000;">₱{{ number_format($product->price, 0) }}</p>
                                <div class="card-actions mt-auto">
                                    <a href="#" class="btn btn-warning w-100 mb-2 buy-now" data-product='@json($product)' title="Buy now" style="border-radius: 8px; font-weight: 600; padding: 12px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #000; box-shadow: 0 4px 6px rgba(255, 193, 7, 0.3); transition: all 0.3s ease;">
                                        <i class="fas fa-bolt me-2"></i>Buy Now
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary w-100 add-to-cart" data-product-id="{{ $product->product_id }}" title="Add to your cart" style="border-radius: 8px; font-weight: 600; padding: 12px; border: 2px solid #6c757d; color: #6c757d; transition: all 0.3s ease;">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No products available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

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
            function NewsletterSignup() {
                const email = document.getElementById('newsletterEmail').value || '';
                if (!email) return;
 else {
                    alert('Thanks — we will send updates to ' + email);
                }
                document.getElementById('newsletterEmail').value = '';
            }

            // Add hover effects to product cards
            document.addEventListener('DOMContentLoaded', function() {
                const productCards = document.querySelectorAll('.product-card');
                
                productCards.forEach(card => {
                    const img = card.querySelector('.card-img-top');
                    const overlay = card.querySelector('.overlay');
                    const badge = overlay?.querySelector('.badge');
                    const buyBtn = card.querySelector('.buy-now');
                    const addBtn = card.querySelector('.add-to-cart');
                    
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px)';
                        this.style.boxShadow = '0 12px 24px rgba(0, 0, 0, 0.15)';
                        if (img) img.style.transform = 'scale(1.05)';
                        if (overlay) overlay.style.background = 'rgba(0, 0, 0, 0.05)';
                        if (badge) badge.style.opacity = '1';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
                        if (img) img.style.transform = 'scale(1)';
                        if (overlay) overlay.style.background = 'rgba(0, 0, 0, 0)';
                        if (badge) badge.style.opacity = '0';
                    });

                    if (buyBtn) {
                        buyBtn.addEventListener('mouseenter', function() {
                            this.style.transform = 'translateY(-2px)';
                            this.style.boxShadow = '0 6px 12px rgba(255, 193, 7, 0.4)';
                        });
                        buyBtn.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = '0 4px 6px rgba(255, 193, 7, 0.3)';
                        });
                    }

                    if (addBtn) {
                        addBtn.addEventListener('mouseenter', function() {
                            this.style.background = '#6c757d';
                            this.style.color = '#fff';
                            this.style.transform = 'translateY(-2px)';
                            this.style.boxShadow = '0 6px 12px rgba(108, 117, 125, 0.3)';
                        });
                        addBtn.addEventListener('mouseleave', function() {
                            this.style.background = 'transparent';
                            this.style.color = '#6c757d';
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = 'none';
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
