@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('content')
<div class="container py-5 content-with-footer">
    <div class="mb-4">
        <h2 class="mb-2" style="font-weight: 800; color: #2c3e50;">Terms & Conditions</h2>
        <p class="text-muted mb-0">The basic rules for using this website and placing orders.</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <h5 style="font-weight: 800; color: #2c3e50;">Using the site</h5>
            <ul class="text-muted" style="line-height: 1.7;">
                <li>Provide accurate information when creating an account and placing orders.</li>
                <li>Do not misuse the site or attempt unauthorized access.</li>
                <li>Prices and availability may change without notice.</li>
            </ul>

            <h5 class="mt-4" style="font-weight: 800; color: #2c3e50;">Orders</h5>
            <ul class="text-muted" style="line-height: 1.7;">
                <li>Orders are confirmed after successful checkout and verification (if applicable).</li>
                <li>We may cancel orders in case of suspected fraud, inventory issues, or payment problems.</li>
            </ul>

            <h5 class="mt-4" style="font-weight: 800; color: #2c3e50;">Contact</h5>
            <p class="text-muted mb-0" style="line-height: 1.7;">
                For questions about these terms, please <a href="{{ route('contact.index') }}" style="color: #1565c0; text-decoration: none; font-weight: 700;">contact us</a>.
            </p>
        </div>
    </div>
</div>
@endsection
