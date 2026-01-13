<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CoreFive Gadgets - Premium tech products and accessories">
    <meta name="keywords" content="gadgets, electronics, products">
    <meta name="author" content="CoreFive Gadgets">
    <meta property="og:title" content="CoreFive Gadgets">
    <meta property="og:description" content="Discover premium products for your business and lifestyle">
    <meta property="og:type" content="website">
    <title>@yield('title', 'CoreFive Gadgets - Shop Premium Tech Products')</title>
    
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
    @vite(['resources/css/app.css'])
    
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
            /* Reserve space for fixed header */
            padding-top: 60px;
            /* Massive overlap to kill the gap */
            margin-top: -12px;
            background-color: transparent;
        }
        footer {
            flex-shrink: 0;
        }

        /* Eliminate any navbar separators and shadows */
        .navbar { 
            border-bottom: 0 !important; 
            box-shadow: none !important;
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

        /* Product modal image sizing */
        #productViewModal .product-img {
            max-width: 100%;
            max-height: 70vh;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        #productViewModal .modal-dialog {
            max-width: 920px;
        }

        #productViewModal .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 18px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        #productViewModal .modal-header {
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            color: #fff;
            padding: 18px 20px;
            border: none;
        }

        #productViewModal .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        #productViewModal .modal-body {
            padding: 24px 28px;
            background: #f9fafb;
        }

        #productViewModal #pvDesc {
            font-size: 0.96rem;
            color: #4b5563;
        }

        #productViewModal #pvPrice {
            color: #1565c0;
            font-weight: 800;
            letter-spacing: -0.2px;
        }

        #productViewModal .modal-footer {
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 16px 20px;
            gap: 10px;
        }

        #productViewModal .modal-footer .btn-primary {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            border: none;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 10px;
            box-shadow: 0 8px 18px rgba(21, 101, 192, 0.25);
        }

        #productViewModal .modal-footer .btn-success {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            border: none;
            color: #222;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 10px;
            box-shadow: 0 8px 18px rgba(255, 193, 7, 0.25);
        }

        #productViewModal .modal-footer .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
            border: 1px solid #e5e7eb;
            font-weight: 600;
            border-radius: 10px;
        }

        /* Back to top button */
        #backToTop {
            position: fixed;
            right: 24px;
            bottom: 28px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            box-shadow: 0 10px 24px rgba(255, 193, 7, 0.35);
            color: #222;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
        }

        #backToTop.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);
        }

        #backToTop:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 14px 28px rgba(255, 193, 7, 0.45);
        }

        /* Cart badge animation - BIG and NOTICEABLE */
        @keyframes badgePop {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.5);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        #cartBadge.pop {
            animation: badgePop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); box-shadow: none !important; border-bottom: none !important;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" style="font-weight: 700; font-size: 1.4rem; letter-spacing: 0.5px;">
                <i class="fas fa-microchip me-2" style="color: #ffc107;"></i>
                <span>CoreFive Gadgets</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('home') }}" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('products.index') }}" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        @auth
                            <a class="nav-link px-3 position-relative" href="{{ route('cart.index') }}" style="font-weight: 500; transition: all 0.3s ease;">
                                <i class="fas fa-shopping-cart me-1"></i>Cart
                                <span id="cartBadge" style="position: absolute; top: -2px; right: 2px; color: #ffc107; font-size: 1rem; font-weight: bold; display: none; transform-origin: center;">5</span>
                            </a>
                        @else
                            <a href="#" class="nav-link px-3 position-relative" onclick="event.preventDefault(); showLoginModal();" style="font-weight: 500; transition: all 0.3s ease;">
                                <i class="fas fa-shopping-cart me-1"></i>Cart
                            </a>
                        @endauth
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('contact.index') }}" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-envelope me-1"></i>Contact Us
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3 d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" style="font-weight: 500;">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #ffc107;">
                                @else
                                    <i class="fas fa-user-circle me-1" style="font-size: 1.1rem;"></i>
                                @endif
                                {{ Auth::user()->first_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <button class="btn btn-sm px-4 ms-2" id="navLoginBtn" data-bs-toggle="modal" data-bs-target="#loginModal" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; border-radius: 20px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </button>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        @yield('content')
    </div>

    <button id="backToTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

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
                            <a href="{{ route('home') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Home
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('products.index') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Products
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('cart.index') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Cart
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('contact.index') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Contact Us
                            </a>
                        </li>
                        @auth
                        <li class="mb-2">
                            <a href="{{ route('profile') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>My Profile
                            </a>
                        </li>
                        @endauth
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
                        @csrf
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
    @guest
        @include('auth.modals')
    @endguest

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
    <script>
        // Expose auth state to frontend and provide helper to show login modal
        window.isAuthenticated = @json(Auth::check());
        window.userId = @json(Auth::check() ? Auth::user()->id : null);
        function showLoginModal() {
            const el = document.getElementById('loginModal');
            if (el) {
                const bs = new bootstrap.Modal(el);
                bs.show();
                return true;
            }
            // fallback: redirect to login route
            try { window.location.href = '{{ route('login') }}'; } catch(e) { window.location.href = '/login'; }
            return false;
        }

        // Back to top behavior
        (function() {
            const btn = document.getElementById('backToTop');
            if (!btn) return;
            let lastY = window.scrollY;
            let hideTimer;

            const shouldShow = () => {
                const y = window.scrollY;
                const docH = document.documentElement.scrollHeight;
                const winH = window.innerHeight;
                const nearBottom = y + winH >= docH - 200;
                const scrollingUp = y < lastY;
                lastY = y;
                return (y > 300 && scrollingUp) || nearBottom;
            };

            const scheduleHide = () => {
                clearTimeout(hideTimer);
                hideTimer = setTimeout(() => {
                    btn.classList.remove('show');
                }, 1200);
            };

            window.addEventListener('scroll', () => {
                if (shouldShow()) {
                    btn.classList.add('show');
                    scheduleHide();
                } else {
                    btn.classList.remove('show');
                }
            });

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
    </script>
</body>
</html>
