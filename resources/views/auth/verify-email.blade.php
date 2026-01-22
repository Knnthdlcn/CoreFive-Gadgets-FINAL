@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12); overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 26px 24px;">
                    <h4 class="m-0" style="color:#fff; font-weight: 800;">Verify your email</h4>
                    <p class="m-0" style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">We’ve sent a 6-digit verification code to your email address.</p>
                </div>

                <div class="card-body" style="padding: 24px;">
                    @if (session('status') === 'verification-code-sent')
                        <div class="alert alert-success" role="alert">
                            A new 6-digit verification code has been sent to your email address.
                        </div>
                    @endif


                    <p style="color:#4b5563; margin-bottom: 18px;">
                        Enter the 6-digit verification code sent to your email.
                    </p>

                    <form method="POST" action="{{ route('verification.otp.verify') }}" class="mb-3">
                        @csrf
                        <label for="code" class="form-label" style="font-weight: 700; color:#1f2d3a;">Verification code</label>
                        <input
                            id="code"
                            name="code"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            pattern="\d{6}"
                            maxlength="6"
                            class="form-control @error('code') is-invalid @enderror"
                            style="border-radius: 10px; padding: 12px 14px; letter-spacing: 6px; font-weight: 900; text-align: center;"
                            placeholder="123456"
                            required
                        />
                        @error('code')
                            <div class="text-danger mt-1" style="font-size: 0.9rem;">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn mt-3" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color:#fff; border:none; border-radius: 10px; padding: 12px 16px; font-weight: 800; box-shadow: 0 8px 18px rgba(21, 101, 192, 0.25);">
                            Verify
                        </button>
                    </form>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color:#fff; border:none; border-radius: 10px; padding: 12px 16px; font-weight: 800; box-shadow: 0 8px 18px rgba(21, 101, 192, 0.25);">
                            Resend code
                        </button>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('home') }}" style="text-decoration:none; font-weight: 700; color: #1565c0;">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
