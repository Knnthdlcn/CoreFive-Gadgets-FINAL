<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - CoreFive Gadgets</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90' fill='%23ffd900'>⚙</text></svg>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f7fa;
            font-family: 'Inter', sans-serif;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
        }

        .admin-sidebar .sidebar-brand {
            padding: 20px;
            font-size: 1.3rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-sidebar .sidebar-brand i {
            color: #ffc107;
            font-size: 1.5rem;
        }

        .admin-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            margin: 5px 0;
        }

        .admin-sidebar .nav-link:hover {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
            border-left-color: #ffc107;
        }

        .admin-sidebar .nav-link.active {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.15);
            border-left-color: #ffc107;
            font-weight: 600;
        }

        .admin-sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }

        .admin-main {
            margin-left: 260px;
            flex: 1;
            padding: 30px;
        }

        .admin-header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: #2c3e50;
            font-weight: 700;
        }

        .admin-header .breadcrumb {
            margin: 0;
            background: transparent;
        }

        .admin-card {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .admin-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #1565c0;
        }

        .stat-card.orange {
            border-left-color: #ff9800;
        }

        .stat-card.green {
            border-left-color: #28a745;
        }

        .stat-card.purple {
            border-left-color: #7c3aed;
        }

        .stat-card h4 {
            color: #7f8c8d;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .admin-table {
            margin: 0;
        }

        .admin-table thead {
            background: #f8f9fa;
        }

        .admin-table thead th {
            border: none;
            color: #7f8c8d;
            font-weight: 600;
            padding: 15px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-table tbody td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .admin-table tbody tr:hover {
            background: #f8f9fa;
        }

        .btn-admin {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-admin-primary {
            background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
            color: white;
        }

        .btn-admin-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);
            color: white;
        }

        .btn-admin-danger {
            background: #dc3545;
            color: white;
        }

        .btn-admin-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-admin-success {
            background: #28a745;
            color: white;
        }

        .btn-admin-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .alert-admin {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .pagination {
            margin-top: 30px;
        }

        /* Laravel pagination may render SVG arrows (Tailwind default). Constrain them in admin just in case. */
        nav[aria-label="Pagination Navigation"] svg {
            width: 18px;
            height: 18px;
        }

        .page-link {
            color: #1565c0;
            border: 1px solid #dee2e6;
        }

        .page-link:hover {
            color: #0d47a1;
            background: #e7f1ff;
        }

        .page-item.active .page-link {
            background: #1565c0;
            border-color: #1565c0;
            color: #fff;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-header {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-microchip"></i>
                <span>CoreFive Admin</span>
            </div>

            <nav class="nav flex-column">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>

                <a href="{{ route('admin.returns.index') }}" class="nav-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                    <i class="fas fa-rotate-left"></i>
                    <span>Returns</span>
                </a>

                <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    <span>Contacts</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>Categories</span>
                </a>

                <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 20px 0;">

                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Store</span>
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" style="display: none;" id="adminLogoutForm">
                    @csrf
                </form>

                <button class="nav-link" onclick="document.getElementById('adminLogoutForm').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            @if ($errors->any())
                <div class="alert alert-danger alert-admin" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Errors</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-admin" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
