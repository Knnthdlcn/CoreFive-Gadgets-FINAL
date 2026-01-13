<?php $__env->startSection('title', 'Checkout'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!Auth::check()): ?>
        <!-- Auth Modals for guest users -->
        <?php echo $__env->make('auth.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            });
        </script>
    <?php endif; ?>

    <main class="container py-5 content-with-footer">
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
            <div class="col-lg-4" id="stickyForms" style="position: sticky; top: 20px; max-height: calc(100vh - 150px); overflow-y: auto;">
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
                <!-- Scroll to Top Button -->
                <button id="scrollToTopBtn" class="btn btn-outline-warning w-100 mt-2" style="display: none; font-weight: 600; border-radius: 8px; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-up me-2"></i>Back to Top
                </button>
                <div class="mt-2 text-center"><a href="<?php echo e(route('home')); ?>" class="btn btn-link" style="color: #1565c0; text-decoration: none;">← Continue shopping</a></div>
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

    <?php $__env->startPush('styles'); ?>
    <style>
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
        
        .card-summary {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
            #stickyForms {
                position: static !important;
                max-height: none !important;
            }
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/cart.js')); ?>"></script>
        <script src="<?php echo e(asset('js/checkout.js')); ?>"></script>
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
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\E-Commerce-Laravel-main\resources\views/checkout.blade.php ENDPATH**/ ?>