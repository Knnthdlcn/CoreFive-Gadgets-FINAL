@extends('layouts.app')

@section('title', 'Shipping & Delivery')

@section('content')
<div class="container py-5 content-with-footer">
    <div class="mb-4">
        <h2 class="mb-2" style="font-weight: 800; color: #2c3e50;">Shipping & Delivery</h2>
        <p class="text-muted mb-0">Everything you need to know about shipping options, costs, and delivery timelines.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body" style="padding: 24px;">
                    <h5 class="mb-3" style="font-weight: 800; color: #2c3e50;">Delivery timelines</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Option</th>
                                    <th>Estimated delivery</th>
                                    <th>Fee</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight: 600;">Standard</td>
                                    <td>3–7 business days</td>
                                    <td>₱0</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Express</td>
                                    <td>1–2 business days</td>
                                    <td>₱199</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;">Next day</td>
                                    <td>Next business day</td>
                                    <td>₱399</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4" />

                    <h5 class="mb-3" style="font-weight: 800; color: #2c3e50;">Tracking & delivery updates</h5>
                    <ul class="text-muted mb-0" style="line-height: 1.7;">
                        <li>Once your order ships, you’ll see status updates in <strong>My Orders</strong>.</li>
                        <li>If a courier tracking number is available, it will appear in your order’s Shipping tab.</li>
                        <li>Delivery timelines are estimates and can vary due to weather, peak seasons, or remote locations.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body" style="padding: 24px;">
                    <h5 class="mb-3" style="font-weight: 800; color: #2c3e50;">Need help?</h5>
                    <p class="text-muted mb-3">If you have questions about shipping or delivery, contact us and we’ll help right away.</p>
                    <a href="{{ route('contact.index') }}" class="btn btn-warning" style="border-radius: 10px; font-weight: 800;">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
