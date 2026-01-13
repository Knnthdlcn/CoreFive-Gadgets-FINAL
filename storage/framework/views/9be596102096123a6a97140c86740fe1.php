<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="CoreFive Gadgets - Premium tech products and accessories">
    <meta name="keywords" content="gadgets, electronics, products">
    <meta name="author" content="CoreFive Gadgets">
    <meta property="og:title" content="CoreFive Gadgets">
    <meta property="og:description" content="Discover premium products for your business and lifestyle">
    <meta property="og:type" content="website">
    <title><?php echo $__env->yieldContent('title', 'CoreFive Gadgets - Shop Premium Tech Products'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90' fill='%23ffd900'>⚙</text></svg>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite assets (enables HMR for CSS) -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    
    <!-- App scripts (legacy, working) -->
    <script src="<?php echo e(asset('js/toast.js')); ?>"></script>
    
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
        
        /* Header hover effects */
        .navbar .nav-link:hover {
            color: #ffc107 !important;
            transform: translateY(-1px);
        }
        
        .navbar-brand:hover {
            opacity: 0.9;
        }
        
        #navLoginBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4) !important;
        }
        
        /* Footer hover effects */
        footer a:not(.btn):hover {
            color: #ffc107 !important;
            padding-left: 5px;
        }
        
        footer .btn-outline-light:hover {
            background: #ffc107;
            border-color: #ffc107;
            color: #222;
        }

        footer input::placeholder {
            color: rgba(255, 255, 255, 0.9) !important;
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo e(route('home')); ?>" style="font-weight: 700; font-size: 1.4rem; letter-spacing: 0.5px;">
                <i class="fas fa-microchip me-2" style="color: #ffc107;"></i>
                <span>CoreFive Gadgets</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?php echo e(route('home')); ?>" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 position-relative" href="<?php echo e(route('cart.index')); ?>" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?php echo e(route('contact.index')); ?>" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-envelope me-1"></i>Contact Us
                        </a>
                    </li>
                    <?php if(auth()->guard()->check()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3 d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" style="font-weight: 500;">
                                <?php if(Auth::user()->profile_photo): ?>
                                    <img src="<?php echo e(asset('storage/' . Auth::user()->profile_photo)); ?>" alt="Profile" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #ffc107;">
                                <?php else: ?>
                                    <i class="fas fa-user-circle me-1" style="font-size: 1.1rem;"></i>
                                <?php endif; ?>
                                <?php echo e(Auth::user()->first_name); ?>

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                                <li><a class="dropdown-item" href="<?php echo e(route('profile')); ?>"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <button class="btn btn-sm px-4 ms-2" id="navLoginBtn" data-bs-toggle="modal" data-bs-target="#loginModal" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; border-radius: 20px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </button>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- FOOTER -->
    <footer class="text-white" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);">
        <div class="container py-5">
            <div class="row">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3" style="font-weight: 700; color: #ffc107;">
                        <i class="fas fa-microchip me-2"></i>About CoreFive Gadgets
                    </h5>
                    <p class="mb-3" style="font-size: 0.95rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8);">
                        CoreFive Gadgets curates the best tech and accessories with competitive prices and reliable service. We carefully select products that bring value and performance.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-light btn-sm" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="mb-3" style="font-weight: 700; color: #ffc107;">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo e(route('home')); ?>" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Home
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo e(route('cart.index')); ?>" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Cart
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo e(route('contact.index')); ?>" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Contact Us
                            </a>
                        </li>
                        <?php if(auth()->guard()->check()): ?>
                        <li class="mb-2">
                            <a href="<?php echo e(route('profile')); ?>" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>My Profile
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3" style="font-weight: 700; color: #ffc107;">Customer Service</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Shipping & Delivery
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Returns & Refunds
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>FAQs
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Privacy Policy
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Terms & Conditions
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3" style="font-weight: 700; color: #ffc107;">Join Our Newsletter</h5>
                    <p class="mb-3" style="font-size: 0.9rem; color: rgba(255, 255, 255, 0.8);">Get product launches and exclusive offers — 1 email a week.</p>
                    <form action="#" method="POST" class="d-flex gap-2">
                        <?php echo csrf_field(); ?>
                        <input type="email" name="email" class="form-control" placeholder="you@gmail.com" required style="border-radius: 8px; border: 1px solid #495057; background: rgba(255,255,255,0.1); color: white !important; font-size: 0.9rem;">
                        <button type="submit" class="btn px-3" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; border-radius: 8px; white-space: nowrap;">
                            Subscribe
                        </button>
                    </form>
                    <div class="mt-4">
                        <p class="mb-2" style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.8);"><i class="fas fa-envelope me-2"></i>support@corefivegadgets.com</p>
                        <p class="mb-0" style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.8);"><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-top" style="border-color: rgba(255,255,255,0.1) !important;">
            <div class="container py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                        <p class="mb-0" style="font-size: 0.9rem; color: rgba(255, 255, 255, 0.7);">© 2025 CoreFive Gadgets. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/200px-Visa_Inc._logo.svg.png" alt="Visa" style="height: 24px; opacity: 0.7; margin: 0 8px;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/200px-Mastercard-logo.svg.png" alt="Mastercard" style="height: 24px; opacity: 0.7; margin: 0 8px;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/American_Express_logo_%282018%29.svg/200px-American_Express_logo_%282018%29.svg.png" alt="Amex" style="height: 24px; opacity: 0.7; margin: 0 8px;">
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Auth Modals -->
    <?php if(auth()->guard()->guest()): ?>
        <?php echo $__env->make('auth.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script>
        // Expose auth state to frontend and provide helper to show login modal
        window.isAuthenticated = <?php echo json_encode(Auth::check(), 15, 512) ?>;
        function showLoginModal() {
            const el = document.getElementById('loginModal');
            if (el) {
                const bs = new bootstrap.Modal(el);
                bs.show();
                return true;
            }
            // fallback: redirect to login route
            try { window.location.href = '<?php echo e(route('login')); ?>'; } catch(e) { window.location.href = '/login'; }
            return false;
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\E-Commerce-Laravel-main\resources\views/layouts/app.blade.php ENDPATH**/ ?>