<?php $__env->startSection('title', 'Your Cart'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!Auth::check()): ?>
        <?php echo $__env->make('auth.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <main class="container py-5 content-with-footer">
        <!-- Page Header -->
        <div class="mb-5">
            <h2 class="mb-2" style="font-weight: 700; font-size: 2rem; color: #2c3e50;">Shopping Cart</h2>
            <p class="text-muted mb-0">Review and manage your items before checkout</p>
        </div>

        <div class="row">
            <!-- Items list -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <div id="cartItems" class="list-group cart-items">
                            <!-- cart items render here by javascript -->
                        </div>

                        <div class="d-flex justify-content-start align-items-center mt-4">
                            <a id="continueShoppingBtn" href="<?php echo e(route('home')); ?>" class="btn btn-outline-secondary btn-continue" style="border-radius: 8px; font-weight: 600; padding: 10px 20px;">
                                <i class="fas fa-arrow-left me-2"></i>Continue shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm card-summary" style="border-radius: 12px;">
                    <div class="card-body">
                        <h5 class="card-title" style="font-weight: 700; color: #2c3e50; margin-bottom: 20px; font-size: 1.2rem;">Order Summary</h5>
                        <ul id="summaryList" class="list-unstyled mb-3">
                            <!-- summary lines injected by javascript -->
                        </ul>
                        <div class="d-flex justify-content-between mb-2" style="padding: 8px 0; font-size: 0.95rem; color: #495057;">
                            <span>Subtotal</span>
                            <strong id="cartSubtotal" style="color: #2c3e50;">₱0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3" style="padding: 8px 0; font-size: 0.95rem; color: #495057;">
                            <span>Shipping</span>
                            <span class="muted">Calculated at checkout</span>
                        </div>
                        <hr style="margin: 12px 0; border-color: #e9ecef;">
                        <div class="d-flex justify-content-between mb-3" style="padding: 8px 0;">
                            <span style="font-weight: 600; color: #2c3e50;">Total</span>
                            <strong id="cartTotal" style="font-size: 1.2rem; color: #2c3e50;">₱0</strong>
                        </div>

                        <a id="checkoutSummaryBtn" href="<?php echo e(Auth::check() ? route('checkout.index') : '#'); ?>" class="btn btn-success w-100" <?php if(!Auth::check()): ?> onclick="event.preventDefault(); showLoginModal();" <?php endif; ?> style="border-radius: 8px; font-weight: 600; padding: 12px; background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); border: none; box-shadow: 0 4px 12px rgba(13, 71, 161, 0.2); transition: all 0.3s ease;">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php $__env->startPush('styles'); ?>
    <style>
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .cart-items .list-group-item {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 16px;
            transition: all 0.3s ease;
            background: #fff;
        }
        .cart-items .list-group-item:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
            background: #fafbfc;
        }
        .cart-item-row {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .cart-item-img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            flex-shrink: 0;
            padding: 8px;
        }
        .cart-item-body {
            flex: 1;
            min-width: 0;
        }
        .cart-item-body h6 {
            font-weight: 600;
            margin-bottom: 4px;
            color: #2c3e50;
            font-size: 1.05rem;
        }
        .cart-item-body p { 
            margin-bottom: 8px; 
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .cart-items .muted { color: #6c757d; }
        .cart-item-body .d-flex {
            gap: 8px;
        }
        .cart-item-body .qty-input {
            width: 70px !important;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 4px 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .cart-item-body .qty-input:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        }
        .cart-item-body .btn-remove {
            border-radius: 6px;
            border-color: #dc3545;
            color: #dc3545;
            font-size: 0.85rem;
            padding: 4px 12px;
            transition: all 0.2s ease;
            font-weight: 600;
        }
        .cart-item-body .btn-remove:hover {
            background-color: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        }
        .card-summary {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 1rem;
        }
        .card-summary .card-body {
            padding: 24px;
        }
        .card-summary .card-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        .card-summary ul {
            padding-bottom: 12px;
        }
        .card-summary li {
            font-size: 0.95rem;
            margin-bottom: 8px;
            color: #495057;
            display: flex;
            justify-content: space-between;
        }
        .card-summary li span:last-child {
            font-weight: 600;
            color: #2c3e50;
        }
        .card-summary hr {
            margin: 12px 0;
            border-color: #e9ecef;
        }
        .btn-continue {
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 2px solid #6c757d;
            color: #6c757d;
        }
        .btn-continue:hover {
            background-color: #6c757d;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
        }
        #checkoutSummaryBtn {
            transition: all 0.3s ease;
        }
        #checkoutSummaryBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 71, 161, 0.3) !important;
        }
        @media (max-width: 576px) {
            .cart-item-img { width: 110px; height: 110px; }
            .card-summary {
                position: static;
                top: auto;
                margin-top: 24px;
            }
        }
    </style>
    <?php $__env->stopPush(); ?>

        <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/cart.js')); ?>"></script>
        <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\E-Commerce-Laravel-main\resources\views/cart.blade.php ENDPATH**/ ?>