<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin:0; padding:0; background:#f6f7fb; font-family: Arial, Helvetica, sans-serif;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px 16px;">
        <div style="background: #06131a; color:#fff; border-radius: 14px 14px 0 0; padding: 18px 20px;">
            <div style="font-weight: 800; font-size: 18px;">CoreFive Gadgets</div>
            <div style="opacity: 0.8; font-size: 13px; margin-top: 4px;">Order confirmation</div>
        </div>

        <div style="background:#fff; border-radius: 0 0 14px 14px; padding: 22px 20px; border: 1px solid #e9ecf3; border-top: none;">
            <p style="margin: 0 0 12px 0; color:#1f2d3a; font-size: 14px;">
                Hi {{ $order->user?->name ?? 'there' }},
            </p>

            <p style="margin: 0 0 14px 0; color:#1f2d3a; font-size: 14px; line-height: 1.6;">
                Thanks for your order! We’ve received your order <strong>{{ $order->display_order_number }}</strong> and we’ll start processing it right away.
            </p>

            <div style="background:#f8f9fa; border: 1px solid #eef1f6; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px;">
                <div style="color:#51606d; font-size: 13px; line-height: 1.6;">
                    <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
                    <div><strong>Placed:</strong> {{ optional($order->created_at)->format('M d, Y h:i A') }}</div>
                    <div><strong>Shipping method:</strong> {{ $order->shipping_method }}</div>
                    <div><strong>Payment method:</strong> {{ $order->payment_method }}</div>
                </div>
            </div>

            <h4 style="margin: 0 0 10px 0; color:#2c3e50; font-size: 14px;">Items</h4>

            <table style="width: 100%; border-collapse: collapse; border: 1px solid #eef1f6; border-radius: 12px; overflow: hidden;">
                <thead>
                    <tr style="background:#f3f8ff;">
                        <th style="text-align:left; padding: 10px 12px; font-size: 12px; color:#51606d;">Product</th>
                        <th style="text-align:center; padding: 10px 12px; font-size: 12px; color:#51606d;">Qty</th>
                        <th style="text-align:right; padding: 10px 12px; font-size: 12px; color:#51606d;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td style="padding: 10px 12px; border-top: 1px solid #eef1f6; color:#1f2d3a; font-size: 13px;">
                                {{ $item->product?->product_name ?? ('Product #' . $item->product_id) }}
                                @if(!empty($item->variant_name))
                                    <div style="color:#51606d; font-size: 12px; margin-top: 2px;">Variant: {{ $item->variant_name }}</div>
                                @endif
                            </td>
                            <td style="padding: 10px 12px; border-top: 1px solid #eef1f6; text-align:center; color:#1f2d3a; font-size: 13px;">
                                {{ $item->quantity }}
                            </td>
                            <td style="padding: 10px 12px; border-top: 1px solid #eef1f6; text-align:right; color:#1f2d3a; font-size: 13px;">
                                ₱{{ number_format((float) $item->price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 14px;">
                <div style="display:flex; justify-content: space-between; color:#51606d; font-size: 13px; padding: 4px 0;">
                    <span>Subtotal</span>
                    <span>₱{{ number_format((float) $order->subtotal, 2) }}</span>
                </div>
                <div style="display:flex; justify-content: space-between; color:#51606d; font-size: 13px; padding: 4px 0;">
                    <span>Shipping</span>
                    <span>₱{{ number_format((float) $order->shipping_fee, 2) }}</span>
                </div>
                <div style="display:flex; justify-content: space-between; color:#1f2d3a; font-size: 14px; font-weight: 800; padding: 8px 0; border-top: 1px solid #eef1f6; margin-top: 6px;">
                    <span>Total</span>
                    <span>₱{{ number_format((float) $order->total, 2) }}</span>
                </div>
            </div>

            <hr style="border:none; border-top: 1px solid #eef1f6; margin: 18px 0;">

            <h4 style="margin: 0 0 8px 0; color:#2c3e50; font-size: 14px;">Shipping address</h4>
            <div style="color:#51606d; font-size: 13px; line-height: 1.6; white-space: pre-wrap; background:#f8f9fa; border: 1px solid #eef1f6; border-radius: 12px; padding: 12px 14px;">
                {{ $order->shipping_address }}
            </div>

            @if(!empty($order->order_notes))
                <h4 style="margin: 14px 0 8px 0; color:#2c3e50; font-size: 14px;">Order notes</h4>
                <div style="color:#51606d; font-size: 13px; line-height: 1.6; white-space: pre-wrap; background:#f8f9fa; border: 1px solid #eef1f6; border-radius: 12px; padding: 12px 14px;">
                    {{ $order->order_notes }}
                </div>
            @endif

            <hr style="border:none; border-top: 1px solid #eef1f6; margin: 18px 0;">

            <p style="margin: 0; color:#8794a1; font-size: 12px; line-height: 1.5;">
                If you have any questions, just reply to this email and we’ll help you out.
            </p>
        </div>

        <div style="text-align:center; color:#9aa6b2; font-size: 12px; margin-top: 14px;">
            © {{ date('Y') }} CoreFive Gadgets
        </div>
    </div>
</body>
</html>
