@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    @if(!Auth::check())
        <!-- Auth Modals for guest users -->
        @include('auth.modals')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            });
        </script>
    @endif

    <main class="container py-5 content-with-footer checkout-page">
        <!-- Page Header -->
        <div class="mb-5">
            <h2 class="mb-2" style="font-weight: 700; font-size: 2rem; color: #2c3e50;">Checkout</h2>
            <p class="text-muted mb-0">Review your order and complete the purchase</p>
        </div>

        <div class="row">
            <!-- Items column -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <h5 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 8px;">Items</h5>
                        <p class="text-muted mb-4">Review the items you'll purchase. Quantities are editable.</p>
                        <div id="checkoutItems" class="list-group checkout-items">
                            <!-- rendered by javascript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary & Forms column -->
            <div class="col-lg-4" id="stickyForms">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm card-summary mb-4" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <h5 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 16px; font-size: 1.2rem;">Order Summary</h5>
                        <ul id="summaryList" class="list-unstyled mb-3"></ul>
                        <hr style="margin: 12px 0; border-color: #e9ecef;">
                        <div class="d-flex justify-content-between mb-2" style="padding: 8px 0; font-size: 0.95rem; color: #495057;">
                            <span>Subtotal</span>
                            <strong id="subtotal" style="color: #2c3e50;">₱0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3" style="padding: 8px 0; font-size: 0.95rem; color: #495057;">
                            <span>Shipping</span>
                            <strong id="shippingFee" style="color: #2c3e50;">₱0</strong>
                        </div>
                        <hr style="margin: 12px 0; border-color: #e9ecef;">
                        <div class="d-flex justify-content-between" style="padding: 8px 0;">
                            <span style="font-weight: 600; color: #2c3e50;">Total</span>
                            <strong id="total" style="font-size: 1.2rem; color: #2c3e50;">₱0</strong>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 20px;">
                        <h6 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 12px;">
                            <i class="fas fa-map-marker-alt me-2" style="color: #ffc107;"></i>Shipping Address
                        </h6>

                        @if(Auth::check() && Auth::user()->address)
                            <div class="mb-3" style="padding: 12px; border: 1px solid #e9ecef; border-radius: 10px; background: #fafafa;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="addressSource" id="addressSource_saved" value="saved" checked>
                                    <label class="form-check-label" for="addressSource_saved" style="font-size: 0.95rem; font-weight: 600;">Use my saved address</label>
                                </div>
                                <div class="small text-muted" style="line-height: 1.4;">
                                    {{ Auth::user()->address }}
                                </div>

                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="radio" name="addressSource" id="addressSource_custom" value="custom">
                                    <label class="form-check-label" for="addressSource_custom" style="font-size: 0.95rem; font-weight: 600;">Use a different address</label>
                                </div>
                            </div>
                        @endif

                        <div id="checkoutPhAddressBuilder" class="mb-2" style="display: none;">
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <input type="text" id="checkoutStreet" class="form-control" placeholder="Street / building / unit" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px; font-size: 0.95rem;">
                                </div>
                                <div class="col-12 col-md-6">
                                    <select id="checkoutRegion" class="form-select" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 12px; font-size: 0.95rem;">
                                        <option value="">Select region</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <select id="checkoutProvince" class="form-select" disabled style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 12px; font-size: 0.95rem;">
                                        <option value="">Select province</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <select id="checkoutCity" class="form-select" disabled style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 12px; font-size: 0.95rem;">
                                        <option value="">Select city/municipality</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <select id="checkoutBarangay" class="form-select" disabled style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 12px; font-size: 0.95rem;">
                                        <option value="">Select barangay</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <input type="text" id="checkoutPostal" class="form-control" placeholder="Postal code" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px; font-size: 0.95rem;">
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="small text-muted" style="padding: 10px 0;">Your selected address will be filled below.</div>
                                </div>
                            </div>
                        </div>

                        <textarea id="shippingAddress" class="form-control mb-2" rows="3" placeholder="Full name, street, city, province, postal code" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px; font-size: 0.95rem;"></textarea>
                        <div class="invalid-feedback d-block" id="shippingAddressError" style="display: none !important; color: #dc3545; font-size: 0.85rem;">
                            Please enter your shipping address to continue.
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 20px;">
                        <h6 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 12px;">
                            <i class="fas fa-truck me-2" style="color: #ffc107;"></i>Shipping Method
                        </h6>
                        <select id="shippingOption" class="form-select mb-2" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 12px; font-size: 0.95rem;">
                            <option value="standard" data-fee="0">Standard (3-7 days) — ₱0</option>
                            <option value="express" data-fee="199">Express (1-2 days) — ₱199</option>
                            <option value="nextday" data-fee="399">Next day — ₱399</option>
                        </select>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 20px;">
                        <h6 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 12px;">
                            <i class="fas fa-credit-card me-2" style="color: #ffc107;"></i>Payment Method
                        </h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_card" value="card" checked>
                            <label class="form-check-label" for="payment_card" style="font-size: 0.95rem;">Card (Visa / Mastercard)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_gcash" value="gcash">
                            <label class="form-check-label" for="payment_gcash" style="font-size: 0.95rem;">GCash / e-wallet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment_cod" value="cod">
                            <label class="form-check-label" for="payment_cod" style="font-size: 0.95rem;">Cash on Delivery</label>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 20px;">
                        <h6 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 12px;">
                            <i class="fas fa-sticky-note me-2" style="color: #ffc107;"></i>Order Notes
                        </h6>
                        <textarea id="orderNotes" class="form-control" rows="2" placeholder="Add a note (e.g., leave at reception)" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px; font-size: 0.95rem;"></textarea>
                    </div>
                </div>

                <!-- Place Order Button -->
                <div class="d-grid">
                    <button id="placeOrderBtn" class="btn btn-lg" style="border-radius: 8px; font-weight: 600; padding: 12px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3); transition: all 0.3s ease;">
                        <i class="fas fa-check me-2"></i>Place Order
                    </button>
                </div>

                @if(!empty($buyNowMode))
                    <form method="POST" action="{{ route('buy-now.cancel') }}" class="d-grid mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger" style="border-radius: 8px; font-weight: 600; padding: 12px;">
                            <i class="fas fa-times me-2"></i>Cancel Buy Now
                        </button>
                    </form>
                @endif
                <div id="orderError" class="alert alert-danger mt-3 d-none" style="border-radius: 10px;"></div>
                <!-- Scroll to Top Button -->
                <button id="scrollToTopBtn" class="btn btn-outline-warning w-100 mt-2" style="display: none; font-weight: 600; border-radius: 8px; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-up me-2"></i>Back to Top
                </button>
                <div class="mt-2 text-center"><a href="{{ route('home') }}" class="btn btn-link" style="color: #1565c0; text-decoration: none;">← Continue shopping</a></div>
            </div>
        </div>
    </main>


    <!-- Order success modal -->
    <div class="modal fade" id="orderSuccessModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center p-3">
                <div class="modal-body">
                    <div class="mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-success">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h5 class="mb-1">Order placed</h5>
                    <p id="orderSuccessMessage" class="small text-muted mb-1">Thank you — your order has been placed.</p>
                    <p id="orderSuccessOrderId" class="small text-muted mb-0"></p>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Checkout: summary should scroll with the page (no sticky overlap) */
        .checkout-page .card-summary {
            position: static !important;
            top: auto !important;
        }
        .checkout-page #stickyForms {
            position: static !important;
        }

        .checkout-items .list-group-item {
            border: none;
            border-radius: 12px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 16px;
            transition: all 0.3s ease;
        }
        .checkout-items .list-group-item:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }
        .checkout-items .item-row {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .checkout-items .item-img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            flex-shrink: 0;
            padding: 8px;
        }
        .checkout-items .item-body {
            flex: 1;
            min-width: 0;
        }
        .checkout-items .item-body h6 {
            font-weight: 600;
            margin-bottom: 4px;
            color: #2c3e50;
            font-size: 1.05rem;
        }
        .checkout-items .item-body p {
            margin-bottom: 8px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .checkout-items .muted { color: #6c757d; }

        .checkout-items .stock-pill {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid rgba(0,0,0,0.08);
            background: #f8fafc;
            color: #52606d;
        }
        .checkout-items .stock-pill.stock-low { background: #fff7ed; color: #b45309; border-color: rgba(245, 158, 11, 0.35); }
        .checkout-items .stock-pill.stock-out { background: #fef2f2; color: #b91c1c; border-color: rgba(239, 68, 68, 0.35); }
        .checkout-items .stock-pill.stock-unlimited { background: #eff6ff; color: #1d4ed8; border-color: rgba(59, 130, 246, 0.35); }

        .checkout-desc {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .card-summary {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* Keep sidebar content scrolling with the page (no inner scrollbar). */
        #stickyForms {
            position: static;
            max-height: none;
            overflow: visible;
        }

        /* Optional: keep only the Order Summary visible on desktop without trapping scroll */
        @media (min-width: 992px) {
            .card-summary {
                position: sticky;
                top: 20px;
                z-index: 1;
                background: #fff;
            }
        }
        
        .card-summary li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .card-summary li span:last-child {
            font-weight: 600;
            color: #2c3e50;
        }
        
        #placeOrderBtn {
            transition: all 0.3s ease;
        }
        #placeOrderBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3) !important;
        }
        
        #scrollToTopBtn:hover {
            background-color: #ffc107 !important;
            color: #222 !important;
            transform: translateY(-2px);
        }
        
        @media (max-width: 991.98px) {
            .card-summary {
                position: static;
            }
        }
        /* Mobile-specific adjustments for checkout items */
        @media (max-width: 575.98px) {
            .checkout-items .item-row {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .checkout-items .item-img {
                width: 100%;
                height: auto;
                max-height: 220px;
                object-fit: contain;
                padding: 12px;
            }
            .checkout-items .item-body {
                padding: 0 4px;
            }
            .checkout-items .item-body .d-flex.w-100 {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                gap: 8px;
            }
            .checkout-items .item-body .d-flex.align-items-center {
                flex-wrap: wrap;
                gap: 8px;
            }
            .checkout-items .qty-input {
                width: 84px;
                min-width: 64px;
            }
            .checkout-items .btn-remove {
                margin-left: 0;
                flex: 0 0 auto;
            }
            .checkout-items .item-body h6 {
                font-size: 1rem;
                margin-bottom: 0;
            }
            .checkout-items .item-body small.text-muted {
                font-size: 0.95rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
        <script>
            window.isBuyNowCheckout = @json($buyNowMode ?? false);
        </script>
        @if(Auth::check())
        <script>
            // For logged-in users, set server-side cart data FIRST before loading checkout.js
            window.serverCartItems = {!! json_encode($cartItems) !!};
            console.log('Server cart items loaded:', window.serverCartItems);
            window.savedShippingAddress = @json(Auth::user()->address);
            window.savedShippingAddressCodes = {
                region: @json(Auth::user()->address_region_code),
                province: @json(Auth::user()->address_province_code),
                city: @json(Auth::user()->address_city_code),
                barangay: @json(Auth::user()->address_barangay_code),
                street: @json(Auth::user()->address_street),
                postal: @json(Auth::user()->address_postal_code),
            };
        </script>
        @endif
        <script src="{{ asset('js/ph-address.js') }}"></script>
        <script src="{{ asset('js/checkout.js') }}?v={{ @filemtime(public_path('js/checkout.js')) ?: time() }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const addressTextarea = document.getElementById('shippingAddress');
                const builder = document.getElementById('checkoutPhAddressBuilder');

                const setTextarea = (val) => {
                    if (!addressTextarea) return;
                    addressTextarea.value = val || '';
                };

                const saved = (typeof window.savedShippingAddress === 'string') ? window.savedShippingAddress : '';
                if (saved && addressTextarea) {
                    setTextarea(saved);
                }

                const savedRadio = document.getElementById('addressSource_saved');
                const customRadio = document.getElementById('addressSource_custom');

                const showBuilder = async (show) => {
                    if (!builder) return;
                    builder.style.display = show ? 'block' : 'none';
                    if (!show) return;

                    if (builder.dataset.inited === '1') return;
                    builder.dataset.inited = '1';

                    if (window.PHAddress && window.PHAddress.initSelector) {
                        await window.PHAddress.initSelector({
                            regionSelect: '#checkoutRegion',
                            provinceSelect: '#checkoutProvince',
                            citySelect: '#checkoutCity',
                            barangaySelect: '#checkoutBarangay',
                            streetInput: '#checkoutStreet',
                            postalInput: '#checkoutPostal',
                            previewTextarea: '#shippingAddress',
                            onAnyChange: () => {},
                            initial: {
                                region: window.savedShippingAddressCodes?.region || '',
                                province: window.savedShippingAddressCodes?.province || '',
                                city: window.savedShippingAddressCodes?.city || '',
                                barangay: window.savedShippingAddressCodes?.barangay || '',
                            },
                        });

                        // Pre-fill street/postal if we have it
                        const street = document.getElementById('checkoutStreet');
                        const postal = document.getElementById('checkoutPostal');
                        if (street && !street.value && window.savedShippingAddressCodes?.street) street.value = window.savedShippingAddressCodes.street;
                        if (postal && !postal.value && window.savedShippingAddressCodes?.postal) postal.value = window.savedShippingAddressCodes.postal;
                        // Trigger preview recompute
                        street?.dispatchEvent(new Event('input'));
                        postal?.dispatchEvent(new Event('input'));
                    }
                };

                const onSourceChange = async () => {
                    const useSaved = savedRadio && savedRadio.checked;
                    if (useSaved) {
                        setTextarea(saved);
                        await showBuilder(false);
                        addressTextarea?.setAttribute('readonly', 'readonly');
                    } else {
                        addressTextarea?.removeAttribute('readonly');
                        await showBuilder(true);
                        if (!addressTextarea?.value) {
                            setTextarea('');
                        }
                    }
                };

                savedRadio?.addEventListener('change', onSourceChange);
                customRadio?.addEventListener('change', onSourceChange);

                // If there is no saved-address radio (guest or no saved address), show builder.
                if (!savedRadio && builder) {
                    addressTextarea?.removeAttribute('readonly');
                    showBuilder(true);
                } else {
                    // Default state
                    onSourceChange();
                }
            });
        </script>
        <script>
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            const stickyForms = document.getElementById('stickyForms');
            
            // Show/hide scroll to top button based on items column scroll
            const itemsCol = document.querySelector('.col-lg-8');
            if (itemsCol) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 300) {
                        scrollToTopBtn.style.display = 'block';
                    } else {
                        scrollToTopBtn.style.display = 'none';
                    }
                });
            }
            
            // Scroll to top when button clicked
            if (scrollToTopBtn) {
                scrollToTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        </script>
    @endpush
@endsection
