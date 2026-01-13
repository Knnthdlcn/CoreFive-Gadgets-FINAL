@extends('admin.layout')

@section('title', 'Orders')

@section('content')
    <div class="admin-header">
        <h1>Orders Management</h1>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}" style="text-decoration: none; color: #1565c0; font-weight: 600;">#{{ $order->id }}</a></td>
                            <td>
                                <strong>{{ $order->user->first_name }} {{ $order->user->last_name }}</strong>
                                <br>
                                <small style="color: #7f8c8d;">{{ $order->user->email }}</small>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                            <td>
                                <span class="badge" style="background: @if($order->status === 'completed') #28a745 @elseif($order->status === 'processing') #1565c0 @elseif($order->status === 'shipped') #17a2b8 @elseif($order->status === 'pending') #ff9800 @else #6c757d @endif; color: white; padding: 6px 12px;">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-admin btn-admin-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <p>No orders found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
