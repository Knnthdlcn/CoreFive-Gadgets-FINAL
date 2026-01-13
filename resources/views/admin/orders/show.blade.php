@extends('admin.layout')

@section('title', 'Order Details')

@section('content')
    <div class="admin-header">
        <h1>Order #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-box me-2"></i>Order Items
                </h5>
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->product_name }}</strong>
                                        <br>
                                        <small style="color: #7f8c8d;">Product ID: {{ $item->product->product_id }}</small>
                                    </td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td><strong>${{ number_format($item->quantity * $item->price, 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="border-top: 2px solid #e9ecef; padding-top: 15px; margin-top: 15px;">
                    <div style="display: flex; justify-content: flex-end; gap: 30px;">
                        <div>
                            <p style="color: #7f8c8d; margin: 0;">Subtotal:</p>
                            <p style="color: #7f8c8d; margin: 5px 0 0 0;">Shipping:</p>
                            <p style="color: #2c3e50; font-weight: 700; font-size: 1.1rem; margin: 10px 0 0 0;">Total:</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin: 0;"><strong>${{ number_format($order->subtotal, 2) }}</strong></p>
                            <p style="margin: 5px 0 0 0;"><strong>${{ number_format($order->shipping_fee, 2) }}</strong></p>
                            <p style="color: #1565c0; font-weight: 700; font-size: 1.3rem; margin: 10px 0 0 0;">${{ number_format($order->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card p-4 mt-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-truck me-2"></i>Shipping Address
                </h5>
                <p style="margin: 0; line-height: 1.8;">
                    {{ $order->shipping_address }}
                </p>
                <p style="margin: 15px 0 0 0; color: #7f8c8d;">
                    <strong>Shipping Method:</strong> {{ ucfirst($order->shipping_method) }}
                </p>
            </div>

            <div class="admin-card p-4 mt-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-credit-card me-2"></i>Payment Method
                </h5>
                <p style="margin: 0;">{{ ucfirst($order->payment_method) }}</p>
                @if($order->order_notes)
                    <p style="margin: 15px 0 0 0; padding: 12px; background: #f8f9fa; border-left: 3px solid #1565c0; border-radius: 6px;">
                        <strong>Order Notes:</strong><br>
                        {{ $order->order_notes }}
                    </p>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-user me-2"></i>Customer Details
                </h5>
                <p style="margin: 0;">
                    <strong>{{ $order->user->first_name }} {{ $order->user->last_name }}</strong>
                </p>
                <p style="margin: 5px 0 0 0; color: #7f8c8d;">{{ $order->user->email }}</p>
                <p style="margin: 5px 0 0 0; color: #7f8c8d;">{{ $order->user->phone ?? 'No phone' }}</p>
            </div>

            <div class="admin-card p-4 mt-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-info-circle me-2"></i>Order Status
                </h5>
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-control" style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 10px; margin-bottom: 15px;">
                        <option value="pending" @if($order->status === 'pending') selected @endif>Pending</option>
                        <option value="processing" @if($order->status === 'processing') selected @endif>Processing</option>
                        <option value="shipped" @if($order->status === 'shipped') selected @endif>Shipped</option>
                        <option value="completed" @if($order->status === 'completed') selected @endif>Completed</option>
                        <option value="cancelled" @if($order->status === 'cancelled') selected @endif>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-admin btn-admin-primary w-100">
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </form>

                <p style="margin: 15px 0 0 0; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                    <strong>Order Date:</strong><br>
                    {{ $order->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>
    </div>
@endsection
