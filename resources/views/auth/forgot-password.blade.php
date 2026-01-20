@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12); overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 26px 24px;">
                    <h4 class="m-0" style="color:#fff; font-weight: 800;">Forgot Password</h4>
                    <p class="m-0" style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">We’ll send a 6-digit code to your email.</p>
                </div>

                <div class="card-body" style="padding: 24px;">
                    @if (session('status'))
                        <div class="alert" role="alert" style="border-radius: 12px; border: 1px solid rgba(212,175,55,0.35); background: rgba(212,175,55,0.12); color: #1f2d3a; font-weight: 700;">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700;">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', request('email')) }}" class="form-control @error('email') is-invalid @enderror" style="border-radius: 12px; padding: 12px 14px; background: #f5f9ff; border-color: rgba(26,58,82,0.25);" placeholder="you@example.com" autocomplete="email" inputmode="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" style="color:#6b7b88;">Enter the email linked to your account.</div>
                        </div>

                        <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color:#fff; border:none; border-radius: 12px; padding: 12px 16px; font-weight: 900; letter-spacing: 0.2px; box-shadow: 0 10px 22px rgba(21, 101, 192, 0.28);">
                            Send OTP Code
                        </button>

                        <div class="text-center mt-3" style="font-size: 0.95rem;">
                            <a
                                href="{{ route('verification.guest.notice', ['email' => old('email', request('email')), 'next' => 'password-reset']) }}"
                                style="text-decoration:none; font-weight: 700; color: #1565c0;"
                            >Already have a verification code?</a>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('home') }}" style="text-decoration:none; font-weight: 700; color: #1565c0;">Back to Home</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
