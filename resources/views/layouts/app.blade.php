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

        /* Mobile header + bottom nav spacing */
        @media (max-width: 991.98px) {
            .main-content {
                padding-top: 108px;
                margin-top: 0;
                padding-bottom: 72px;
            }

            /* One-line category/sort row (horizontal scroll, no wrap) */
            .category-scroll {
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                padding-bottom: 6px;
                gap: 8px !important;
            }
            .category-scroll::-webkit-scrollbar {
                display: none;
            }
            .category-scroll .btn {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 7px 14px !important;
                font-size: 0.82rem !important;
                line-height: 1.1 !important;
                border-radius: 999px !important;
                font-weight: 800 !important;
            }

            /* Denser product cards (mobile 2-column) */
            .product-card .card-img-wrapper {
                height: 190px !important;
            }

            /* Product action buttons (mobile): compact cart + full-width primary */
            .product-card .product-actions {
                flex-direction: column !important;
                align-items: stretch !important;
                justify-content: initial !important;
                gap: 10px !important;
            }
            .product-card .product-actions .badge {
                align-self: flex-start;
            }
            .product-card .product-actions-buttons {
                width: 100% !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                gap: 6px !important;
                min-width: 0 !important;
            }
            .product-card .add-to-cart-btn {
                height: 44px !important;
                width: 40px !important;
                flex: 0 0 40px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 14px !important;
                border-width: 2px !important;
                box-shadow: none !important;
            }
            .product-card .buy-now-btn {
                height: 44px !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
                padding: 0 10px !important;
                border-radius: 14px !important;
                font-size: 0.82rem !important;
                line-height: 1.1 !important;
                white-space: nowrap;
                font-weight: 900 !important;
                box-shadow: 0 10px 22px rgba(255, 193, 7, 0.22) !important;
                overflow: visible;
            }

            /* On very narrow phones, prioritize the text over the icon */
            @media (max-width: 420px) {
                .product-card .buy-now-btn i {
                    display: none !important;
                }
            }
            .product-card .add-to-cart-btn i,
            .product-card .buy-now-btn i {
                font-size: 0.95rem !important;
            }

            .product-card .buy-now-btn:disabled,
            .product-card .add-to-cart-btn:disabled {
                opacity: 0.55;
                box-shadow: none !important;
            }
        }

        /* Mobile header (CoreFive theme) */
        .mobile-topbar {
            background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.18);
        }
        .mobile-topbar .search-wrap {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 193, 7, 0.35);
            border-radius: 14px;
            overflow: hidden;
        }
        .mobile-topbar input[type="search"] {
            background: rgba(0, 0, 0, 0.18);
            border: 0;
            font-size: 0.95rem;
            padding: 12px 12px;
            color: rgba(255, 255, 255, 0.96);
        }
        .mobile-topbar input[type="search"]::placeholder {
            color: rgba(255, 255, 255, 0.72);
        }
        .mobile-topbar input[type="search"]:focus {
            outline: none;
            box-shadow: none;
        }
        .mobile-topbar .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(255, 193, 7, 0.35);
            background: rgba(0, 0, 0, 0.12);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .mobile-topbar .icon-btn:active {
            transform: scale(0.98);
        }

        /* Mobile bottom nav */
        .mobile-bottom-nav {
            background: #fff;
            border-top: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 -10px 24px rgba(0,0,0,0.08);
        }
        .mobile-bottom-nav a {
            color: #6b7280;
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 600;
        }
        .mobile-bottom-nav a .nav-ico {
            font-size: 1.05rem;
            margin-bottom: 2px;
        }
        .mobile-bottom-nav a.active {
            color: #ffc107;
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
    
</style>
    @stack('styles')

    <style>
        /* Global override to ensure modal headers are compact across the site */
        .modal .modal-header { padding: 10px 14px !important; }
        .modal .modal-title { font-size: 1rem !important; line-height: 1.1 !important; }
        #signupModal .modal-header, #loginModal .modal-header { padding-top: 8px !important; padding-bottom: 8px !important; }
        /* Slightly shrink the big Google button inside modals to save vertical space */
        .modal .btn.w-100 { padding: 10px 12px !important; font-size: 0.95rem !important; }
        @media (max-width: 480px) {
            .modal .modal-title { font-size: 0.95rem !important; }
            .modal .modal-header { padding: 8px 10px !important; }
            .modal .btn.w-100 { padding: 9px 10px !important; font-size: 0.92rem !important; }
            .modal .modal-content { max-height: 96vh !important; }
        }

        /* Extra small devices: ensure modal sits above fixed topbar and is compact */
        @media (max-width: 420px) {
            /* place modal above the fixed mobile topbar */
            .modal { z-index: 1400 !important; }
            .modal-backdrop { z-index: 1350 !important; }

            /* make header non-sticky so content can scroll and header stays visible */
            #signupModal .modal-header,
            #loginModal .modal-header {
                position: static !important;
                top: auto !important;
                padding-top: 6px !important;
                padding-bottom: 6px !important;
            }

            #signupModal .modal-title,
            #loginModal .modal-title {
                font-size: 0.92rem !important;
                line-height: 1.05 !important;
            }

            #signupModal .modal-body,
            #loginModal .modal-body {
                padding: 14px !important;
            }

            .modal .form-control { padding: 10px 12px !important; font-size: 0.9rem !important; }
            .modal .mb-4 { margin-bottom: 12px !important; }
            .modal .btn.w-100 { padding: 8px 10px !important; font-size: 0.9rem !important; }
            .modal .modal-content { max-height: 98vh !important; margin: 8px !important; }
        }
    </style>
</head>
<body>
    <!-- MOBILE TOP HEADER (portrait/mobile) -->
    <div class="mobile-topbar fixed-top d-lg-none" style="z-index: 1200;">
        <div class="container py-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('home') }}" class="icon-btn" aria-label="Home">
                    <i class="fas fa-store"></i>
                </a>

                <form class="flex-grow-1" action="{{ route('products.index') }}" method="GET">
                    <div class="search-wrap d-flex align-items-center">
                        <i class="fas fa-magnifying-glass px-3" style="color: rgba(255, 193, 7, 0.98);"></i>
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            class="form-control"
                            placeholder="Search products"
                            aria-label="Search products"
                        />
                        <button class="btn" type="submit" style="background: transparent; border: 0; color: rgba(255, 193, 7, 0.98); padding: 0 12px;">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                </form>

                @php
                    if (Auth::check()) {
                        $cartCountMobile = \App\Models\CartItem::where('user_id', Auth::id())->count();
                    } else {
                        $cartCountMobile = count(session()->get('cart', []));
                    }
                @endphp

                <a href="{{ route('cart.index') }}" class="icon-btn" aria-label="Cart">
                    <i class="fas fa-cart-shopping"></i>
                    <span
                        class="badge bg-white text-dark"
                        data-cart-count-badge
                        style="position:absolute; top:-6px; right:-6px; font-size: 0.68rem; border-radius: 999px; {{ $cartCountMobile > 0 ? '' : 'display:none;' }}"
                    >{{ $cartCountMobile }}</span>
                </a>

                @auth
                    <a href="{{ route('profile') }}" class="icon-btn" aria-label="Profile">
                        <i class="fas fa-user"></i>
                    </a>
                @else
                    <button type="button" class="icon-btn" aria-label="Login" onclick="showLoginModal()">
                        <i class="fas fa-right-to-bracket"></i>
                    </button>
                @endauth
            </div>

            <div class="d-flex gap-2 mt-2" style="overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
                <a class="px-3 py-2" href="{{ route('products.index') }}" style="white-space: nowrap; border-radius: 999px; background: rgba(255, 193, 7, 0.16); border: 1px solid rgba(255, 193, 7, 0.28); color:#fff; text-decoration:none; font-weight: 800; font-size: 0.82rem;">
                    0% Interest
                </a>
                <a class="px-3 py-2" href="{{ route('products.index') }}" style="white-space: nowrap; border-radius: 999px; background: rgba(255, 193, 7, 0.16); border: 1px solid rgba(255, 193, 7, 0.28); color:#fff; text-decoration:none; font-weight: 800; font-size: 0.82rem;">
                    Free Shipping
                </a>
                <a class="px-3 py-2" href="{{ route('products.index') }}" style="white-space: nowrap; border-radius: 999px; background: rgba(255, 193, 7, 0.16); border: 1px solid rgba(255, 193, 7, 0.28); color:#fff; text-decoration:none; font-weight: 800; font-size: 0.82rem;">
                    Hot Deals
                </a>
                <a class="px-3 py-2" href="{{ route('products.index') }}" style="white-space: nowrap; border-radius: 999px; background: rgba(255, 193, 7, 0.16); border: 1px solid rgba(255, 193, 7, 0.28); color:#fff; text-decoration:none; font-weight: 800; font-size: 0.82rem;">
                    New Arrivals
                </a>
            </div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top d-none d-lg-block" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); box-shadow: none !important; border-bottom: none !important;">
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
                        <a class="nav-link px-3 position-relative" href="{{ route('cart.index') }}" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            @php
                                if (Auth::check()) {
                                    $cartCount = \App\Models\CartItem::where('user_id', Auth::id())->count();
                                } else {
                                    $cartCount = count(session()->get('cart', []));
                                }
                            @endphp
                            <span id="cartCount" class="badge bg-warning text-dark" data-cart-count-badge style="position: absolute; top: 0px; right: 8px; font-size: 0.7rem; {{ $cartCount > 0 ? '' : 'display: none;' }}">{{ $cartCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('contact.index') }}" style="font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-envelope me-1"></i>Contact Us
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('orders.index') }}" style="font-weight: 500; transition: all 0.3s ease;">
                                <i class="fas fa-shopping-bag me-1"></i>Orders
                            </a>
                        </li>
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

    <!-- MOBILE BOTTOM NAV -->
    <nav class="mobile-bottom-nav fixed-bottom d-lg-none" style="z-index: 1200;">
        <div class="container">
            <div class="d-flex justify-content-between py-2">
                <a href="{{ route('home') }}" class="d-flex flex-column align-items-center {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-house nav-ico"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('products.index') }}" class="d-flex flex-column align-items-center {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box nav-ico"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('cart.index') }}" class="d-flex flex-column align-items-center position-relative {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    <i class="fas fa-cart-shopping nav-ico"></i>
                    <span
                        class="badge bg-warning text-dark"
                        data-cart-count-badge
                        style="position:absolute; top:-2px; right: 22px; font-size: 0.62rem; border-radius: 999px; {{ ($cartCountMobile ?? 0) > 0 ? '' : 'display:none;' }}"
                    >{{ $cartCountMobile ?? 0 }}</span>
                    <span>Cart</span>
                </a>
                <a href="{{ route('orders.index') }}" class="d-flex flex-column align-items-center {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <i class="fas fa-bag-shopping nav-ico"></i>
                    <span>Orders</span>
                </a>
                @auth
                    <a href="{{ route('profile') }}" class="d-flex flex-column align-items-center {{ request()->routeIs('profile') ? 'active' : '' }}">
                        <i class="fas fa-user nav-ico"></i>
                        <span>Me</span>
                    </a>
                @else
                    <a href="#" class="d-flex flex-column align-items-center" onclick="showLoginModal(); return false;">
                        <i class="fas fa-user nav-ico"></i>
                        <span>Me</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

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
                            <a href="{{ route('pages.shipping') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Shipping & Delivery
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('pages.returns') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Returns & Refunds
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('pages.faqs') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>FAQs
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('pages.privacy') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Privacy Policy
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('pages.terms') }}" class="text-decoration-none" style="transition: all 0.3s ease; font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                                <i class="fas fa-chevron-right me-2" style="font-size: 0.7rem;"></i>Terms & Conditions
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3" style="font-weight: 700; color: #ffc107;">Join Our Newsletter</h5>
                    <p class="mb-3" style="font-size: 0.9rem; color: rgba(255, 255, 255, 0.8);">Get product launches and exclusive offers — 1 email a week.</p>
                    <form id="footerNewsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2" novalidate>
                        @csrf
                        <input
                            id="footerNewsletterEmail"
                            type="email"
                            name="newsletter_email"
                            value="{{ old('newsletter_email') }}"
                            class="form-control {{ $errors->has('newsletter_email') ? 'is-invalid' : '' }}"
                            placeholder="you@gmail.com"
                            autocomplete="email"
                            inputmode="email"
                            style="border-radius: 8px; border: 1px solid #495057; background: rgba(255,255,255,0.1); color: white !important; font-size: 0.9rem;"
                        >
                        <button type="submit" class="btn px-3" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; border-radius: 8px; white-space: nowrap;">
                            Subscribe
                        </button>
                    </form>
                    @if(session('newsletter_success'))
                        <div class="mt-2" style="font-size: 0.85rem; color: rgba(40, 167, 69, 0.95); font-weight: 700;">
                            {{ session('newsletter_success') }}
                        </div>
                    @endif
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

        // Footer newsletter: disable browser tooltip and highlight red instead
        (function () {
            const form = document.getElementById('footerNewsletterForm');
            const input = document.getElementById('footerNewsletterEmail');
            if (!form || !input) return;

            const setInvalid = (isInvalid) => {
                if (isInvalid) {
                    input.classList.add('is-invalid');
                    input.style.borderColor = '#dc3545';
                    input.style.boxShadow = '0 0 0 .2rem rgba(220,53,69,.25)';
                } else {
                    input.classList.remove('is-invalid');
                    input.style.borderColor = '';
                    input.style.boxShadow = '';
                }
            };

            input.addEventListener('input', () => setInvalid(false));

            form.addEventListener('submit', (e) => {
                const email = (input.value || '').trim();
                if (!email) {
                    e.preventDefault();
                    setInvalid(true);
                    input.focus();
                    return;
                }
                // Basic email check (server will still validate)
                const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                if (!ok) {
                    e.preventDefault();
                    setInvalid(true);
                    input.focus();
                }
            });
        })();

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
