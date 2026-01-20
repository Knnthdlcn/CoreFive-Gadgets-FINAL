@extends('layouts.app')

@section('title', 'Update Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12); overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 26px 24px;">
                    <h4 class="m-0" style="color:#fff; font-weight: 800;">Update Password</h4>
                    <p class="m-0" style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">Change your account password.</p>
                </div>

                <div class="card-body" style="padding: 24px;">
                    <form method="POST" action="{{ route('user-password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700; color: #222;">Current Password</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px;" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700; color: #222;">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px;" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700; color: #222;">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px;" required>
                        </div>

                        <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color: #fff; border: none; font-weight: 800; padding: 12px 20px; border-radius: 10px;">
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
