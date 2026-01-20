@extends('layouts.app')

@section('title', 'FAQs')

@section('content')
<div class="container py-5 content-with-footer">
    <div class="mb-4">
        <h2 class="mb-2" style="font-weight: 800; color: #2c3e50;">FAQs</h2>
        <p class="text-muted mb-0">Common questions about ordering, shipping, returns, and accounts.</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item" style="border-radius: 10px; overflow: hidden;">
                    <h2 class="accordion-header" id="faqOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
                            How do I track my order?
                        </button>
                    </h2>
                    <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted" style="line-height: 1.7;">
                            Go to <strong>My Orders</strong>, open your order, and check the Shipping tab for updates.
                        </div>
                    </div>
                </div>

                <div class="accordion-item mt-2" style="border-radius: 10px; overflow: hidden;">
                    <h2 class="accordion-header" id="faqTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                            Can I return an item?
                        </button>
                    </h2>
                    <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted" style="line-height: 1.7;">
                            If eligible, you can request a return from <strong>My Orders</strong> within the return window after delivery.
                        </div>
                    </div>
                </div>

                <div class="accordion-item mt-2" style="border-radius: 10px; overflow: hidden;">
                    <h2 class="accordion-header" id="faqThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                            Do I need an account to order?
                        </button>
                    </h2>
                    <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted" style="line-height: 1.7;">
                            Yes — you’ll be asked to sign in (or create an account) before checkout so you can track orders and manage returns.
                        </div>
                    </div>
                </div>

                <div class="accordion-item mt-2" style="border-radius: 10px; overflow: hidden;">
                    <h2 class="accordion-header" id="faqFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
                            What payment methods are supported?
                        </button>
                    </h2>
                    <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted" style="line-height: 1.7;">
                            Payment options are shown at checkout. Availability may vary by location.
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <p class="mb-0 text-muted">Still need help? <a href="{{ route('contact.index') }}" style="color: #1565c0; text-decoration: none; font-weight: 700;">Contact us</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
