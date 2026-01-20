@extends('admin.layout')

@section('title', 'User Details')

@section('content')
    <div class="admin-header">
        <h1>{{ $user->first_name }} {{ $user->last_name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-info-circle me-2"></i>User Information
                </h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p style="color: #7f8c8d; margin: 0 0 5px 0;"><strong>First Name</strong></p>
                        <p style="margin: 0 0 15px 0;">{{ $user->first_name }}</p>

                        <p style="color: #7f8c8d; margin: 0 0 5px 0;"><strong>Email</strong></p>
                        <p style="margin: 0 0 15px 0;">{{ $user->email }}</p>
                    @if(!empty($user->banned_at))
                        <div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 18px;">
                            <strong>Account is banned.</strong>
                            <span class="ms-2">This user cannot log in. You can unban the account anytime.</span>
                        </div>
                    @endif
                    </div>
                    <div class="col-md-6">
                        <p style="color: #7f8c8d; margin: 0 0 5px 0;"><strong>Last Name</strong></p>
                        <p style="margin: 0 0 15px 0;">{{ $user->last_name }}</p>

                        <p style="color: #7f8c8d; margin: 0 0 5px 0;"><strong>Phone</strong></p>
                        <p style="margin: 0 0 15px 0;">{{ $user->contact ?? 'Not provided' }}</p>
                    </div>
                </div>

                <p style="color: #7f8c8d; margin: 0 0 5px 0;"><strong>Joined</strong></p>
                <p style="margin: 0;">{{ $user->created_at->format('M d, Y h:i A') }}</p>
            </div>

            @if($user_orders->count())
                <div class="admin-card p-4 mt-4">
                    <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                        <i class="fas fa-shopping-cart me-2"></i>User Orders
                    </h5>
                    <div class="table-responsive">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user_orders as $order)
                                    <tr>
                                        <td><a href="{{ route('admin.orders.show', $order) }}" style="text-decoration: none; color: #1565c0; font-weight: 600;">{{ $order->display_order_number }}</a></td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>₱{{ number_format($order->total, 2) }}</td>
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
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-chart-line me-2"></i>User Statistics
                </h5>
                <p style="margin: 0 0 15px 0;">
                    <strong>Total Orders:</strong><br>
                    <span style="font-size: 2rem; font-weight: 700; color: #1565c0;">{{ $user_orders->count() }}</span>
                </p>
                <p style="margin: 0 0 15px 0;">
                    <strong>Total Spent:</strong><br>
                    <span style="font-size: 2rem; font-weight: 700; color: #28a745;">₱{{ number_format($user_orders->sum('total'), 2) }}</span>
                </p>
                @if($user_orders->count())
                    <p style="margin: 0;">
                        <strong>Average Order Value:</strong><br>
                        <span style="font-size: 1.3rem; font-weight: 700; color: #ff9800;">₱{{ number_format($user_orders->avg('total'), 2) }}</span>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
