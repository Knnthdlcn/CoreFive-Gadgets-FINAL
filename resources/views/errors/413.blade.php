@extends('layouts.app')

@section('title', 'Content Too Large')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12); overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 26px 24px;">
                    <h4 class="m-0" style="color:#fff; font-weight: 800;">Content Too Large</h4>
                    <p class="m-0" style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">Your upload was bigger than the server allows.</p>
                </div>

                <div class="card-body" style="padding: 24px;">
                    <div class="alert alert-danger" role="alert" style="border-radius: 12px;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $message ?? 'Upload too large. Maximum allowed is 5MB.' }}
                    </div>

                    <p class="text-muted" style="margin: 0 0 16px 0;">
                        Tip: choose a smaller image or compress it before uploading.
                    </p>

                    <div class="d-flex gap-2" style="flex-wrap: wrap;">
                        <a href="{{ url()->previous() }}" class="btn" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color:#fff; border:none; border-radius: 10px; padding: 10px 14px; font-weight: 800;">
                            Go Back
                        </a>
                        <a href="{{ route('profile') }}" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 10px 14px; font-weight: 800;">
                            Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
