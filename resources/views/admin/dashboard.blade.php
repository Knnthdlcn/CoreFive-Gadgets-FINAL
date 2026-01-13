@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-header">
        <div>
            <h1>Dashboard</h1>
            <p style="color: #7f8c8d; margin: 5px 0 0 0;">Welcome back, {{ Auth::user()->first_name }}!</p>
        </div>
        <div style="text-align: right;">
            <p style="color: #7f8c8d; margin: 0;">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <h4><i class="fas fa-box me-2"></i>Total Products</h4>
                <div class="stat-value">{{ $stats['total_products'] }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card orange">
                <h4><i class="fas fa-shopping-cart me-2"></i>Total Orders</h4>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card green">
                <h4><i class="fas fa-dollar-sign me-2"></i>Total Revenue</h4>
                <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card purple">
                <h4><i class="fas fa-users me-2"></i>Total Users</h4>
                <div class="stat-value">{{ $stats['total_users'] }}</div>
            </div>
        </div>
    </div>

    <!-- Order Status Cards -->
    <div class="row mb-4 g-3">
        <div class="col-lg-4 col-md-6">
            <div class="admin-card p-4">
                <h5 style="color: #7f8c8d; font-weight: 600; margin-bottom: 10px;">Pending Orders</h5>
                <p style="font-size: 2.2rem; font-weight: 700; color: #ff9800; margin: 0;">{{ $stats['pending_orders'] }}</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="admin-card p-4">
                <h5 style="color: #7f8c8d; font-weight: 600; margin-bottom: 10px;">Completed Orders</h5>
                <p style="font-size: 2.2rem; font-weight: 700; color: #28a745; margin: 0;">{{ $stats['completed_orders'] }}</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="admin-card p-4">
                <h5 style="color: #7f8c8d; font-weight: 600; margin-bottom: 10px;">Contact Messages</h5>
                <p style="font-size: 2.2rem; font-weight: 700; color: #1565c0; margin: 0;">{{ $stats['total_contacts'] }}</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-6">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-clock me-2"></i>Recent Orders
                </h5>
                @if($recent_orders->count())
                    <div class="table-responsive">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                    <tr>
                                        <td><a href="{{ route('admin.orders.show', $order) }}" style="text-decoration: none; color: #1565c0; font-weight: 600;">#{{ $order->id }}</a></td>
                                        <td>{{ $order->user->first_name }} {{ $order->user->last_name }}</td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="badge" style="background: @if($order->status === 'completed') #28a745 @elseif($order->status === 'pending') #ff9800 @else #6c757d @endif; color: white;">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="color: #7f8c8d; text-align: center; margin: 20px 0;">No orders yet</p>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="btn btn-admin btn-admin-primary mt-3 w-100">View All Orders</a>
            </div>
        </div>

        <!-- Recent Contacts -->
        <div class="col-lg-6">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-envelope me-2"></i>Recent Contact Messages
                </h5>
                @if($recent_contacts->count())
                    <div class="space-y-3">
                        @foreach($recent_contacts as $contact)
                            <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 3px solid #1565c0;">
                                <p style="margin: 0 0 5px 0; font-weight: 600; color: #2c3e50;">{{ $contact->name }}</p>
                                <p style="margin: 0 0 5px 0; font-size: 0.9rem; color: #7f8c8d;">{{ $contact->email }}</p>
                                <p style="margin: 0; font-size: 0.85rem; color: #999;">{{ Str::limit($contact->message, 60) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #7f8c8d; text-align: center; margin: 20px 0;">No messages yet</p>
                @endif
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-admin btn-admin-primary mt-3 w-100">View All Messages</a>
            </div>
        </div>
    </div>

    <style>
        .space-y-3 > * + * {
            margin-top: 12px;
        }
    </style>
@endsection
