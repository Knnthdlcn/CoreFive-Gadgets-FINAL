@extends('layouts.app')

@section('title', 'Returns & Refunds')

@section('content')
<div class="container py-5 content-with-footer">
    <div class="mb-4">
        <h2 class="mb-2" style="font-weight: 800; color: #2c3e50;">Returns & Refunds</h2>
        <p class="text-muted mb-0">Quick guide on eligibility, timelines, and how to request a return.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body" style="padding: 24px;">
                    <h5 class="mb-3" style="font-weight: 800; color: #2c3e50;">Return eligibility</h5>
                    <ul class="text-muted" style="line-height: 1.7;">
                        <li>Returns are available for eligible items after delivery.</li>
                        <li>Requests must be submitted within <strong>7 days</strong> of delivery (where applicable).</li>
                        <li>Items must be unused and in original packaging unless the item arrived damaged/defective.</li>
                    </ul>

                    <hr class="my-4" />

                    <h5 class="mb-3" style="font-weight: 800; color: #2c3e50;">How to request a return</h5>
                    <ol class="text-muted" style="line-height: 1.7;">
                        <li>Go to <strong>My Orders</strong>.</li>
                        <li>Open the order and go to the <strong>Returns</strong> tab.</li>
                        <li>Select items, provide a reason, and submit your request.</li>
                    </ol>

                    <hr class="my-4" />

                    <h5 class="mb-3" style="font-weight: 800; color: #2c3e50;">Refund timeline</h5>
                    <p class="text-muted mb-0" style="line-height: 1.7;">
                        Refund timing depends on your payment method and the return status. Once approved and processed, most refunds complete within 3–10 business days.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body" style="padding: 24px;">
                    <h5 class="mb-3" style="font-weight: 800; color: #2c3e50;">Contact us</h5>
                    <p class="text-muted mb-3">Need help with a return? Send us a message.</p>
                    <a href="{{ route('contact.index') }}" class="btn btn-warning" style="border-radius: 10px; font-weight: 800;">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
