@extends('admin.layout')

@section('title', 'Return Details')

@section('content')
    <div class="admin-header">
        <h1>Return #{{ $orderReturn->id }}</h1>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Returns
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 15px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-rotate-left me-2"></i>Requested Items
                </h5>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderReturn->items as $ri)
                                @php($oi = $ri->orderItem)
                                <tr>
                                    <td>
                                        <strong>{{ $oi->product->product_name ?? 'Product' }}</strong>
                                        @if(!empty($oi->variant_name))
                                            <br><small style="color:#7f8c8d;">Variant: {{ $oi->variant_name }}</small>
                                        @endif
                                        <br><small style="color:#7f8c8d;">Order item ID: {{ $oi->id }}</small>
                                    </td>
                                    <td><strong>{{ $ri->quantity }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 15px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                    <strong>Reason:</strong>
                    <div style="margin-top: 6px; white-space: pre-wrap;">{{ $orderReturn->reason ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card p-4">
                <h5 style="margin-bottom: 20px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-receipt me-2"></i>Order
                </h5>
                <p style="margin:0;">
                    <strong>{{ $orderReturn->order->display_order_number ?? ('Order #' . $orderReturn->order_id) }}</strong>
                </p>
                <p style="margin: 5px 0 0 0; color:#7f8c8d;">
                    {{ $orderReturn->order->created_at->format('M d, Y h:i A') }}
                </p>
                <p style="margin: 10px 0 0 0; color:#7f8c8d;">
                    <strong>Customer:</strong><br>
                    {{ $orderReturn->order->user->first_name ?? '' }} {{ $orderReturn->order->user->last_name ?? '' }}<br>
                    {{ $orderReturn->order->user->email ?? '' }}
                </p>
                <a href="{{ route('admin.orders.show', $orderReturn->order) }}" class="btn btn-admin btn-admin-primary w-100" style="margin-top: 12px;">
                    <i class="fas fa-eye me-2"></i>View Order
                </a>
            </div>

            <div class="admin-card p-4 mt-4">
                <h5 style="margin-bottom: 15px; color: #2c3e50; font-weight: 700;">
                    <i class="fas fa-flag me-2"></i>Return Status
                </h5>

                <form action="{{ route('admin.returns.update-status', $orderReturn) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-control" style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 10px; margin-bottom: 15px;">
                        @foreach(['requested','approved','in_transit','received','rejected','closed'] as $s)
                            <option value="{{ $s }}" @if($orderReturn->status === $s) selected @endif>
                                {{ ucfirst(str_replace('_',' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-admin btn-admin-primary w-100">
                        <i class="fas fa-save me-2"></i>Update Return
                    </button>
                </form>

                <p style="margin: 15px 0 0 0; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                    <strong>Requested:</strong><br>
                    {{ ($orderReturn->requested_at ?? $orderReturn->created_at)->format('M d, Y h:i A') }}
                </p>
                @if($orderReturn->deadline_at)
                    <p style="margin: 10px 0 0 0; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                        <strong>Deadline:</strong><br>
                        {{ $orderReturn->deadline_at->format('M d, Y') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
