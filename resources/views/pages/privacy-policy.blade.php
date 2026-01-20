@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="container py-5 content-with-footer">
    <div class="mb-4">
        <h2 class="mb-2" style="font-weight: 800; color: #2c3e50;">Privacy Policy</h2>
        <p class="text-muted mb-0">How we collect, use, and protect your information.</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <h5 style="font-weight: 800; color: #2c3e50;">Overview</h5>
            <p class="text-muted" style="line-height: 1.7;">
                We use your information to process orders, provide customer support, and improve your shopping experience. We do not sell your personal data.
            </p>

            <h5 class="mt-4" style="font-weight: 800; color: #2c3e50;">What we collect</h5>
            <ul class="text-muted" style="line-height: 1.7;">
                <li>Account details (name, email)</li>
                <li>Shipping details and order history</li>
                <li>Basic usage information (for analytics and security)</li>
            </ul>

            <h5 class="mt-4" style="font-weight: 800; color: #2c3e50;">Security</h5>
            <p class="text-muted mb-0" style="line-height: 1.7;">
                We apply reasonable safeguards to protect your data. If you believe your account is compromised, please contact us.
            </p>
        </div>
    </div>
</div>
@endsection
