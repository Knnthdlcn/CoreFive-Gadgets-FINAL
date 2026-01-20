@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container py-4 py-lg-5 auth-page-container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 auth-login-card" style="border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12); overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 26px 24px;">
                    <h4 class="m-0" style="color:#fff; font-weight: 800;">Sign in</h4>
                    <p class="m-0" style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">Log in to continue shopping.</p>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div id="pageLoginError" class="alert alert-danger d-none" style="border-radius: 12px;"></div>

                    <a href="{{ route('auth.google.redirect') }}" class="btn w-100" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-weight: 800; color: #1f2d3a; background: #fff; display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom: 16px;">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" style="width:18px;height:18px;"> Continue with Google
                    </a>

                    <div class="auth-login-divider" style="display:flex; align-items:center; gap:12px; margin: 8px 0 18px 0;">
                        <div style="flex:1; height:1px; background:#f0f0f0;"></div>
                        <div style="color:#999; font-size:0.85rem; font-weight:700;">or login with email</div>
                        <div style="flex:1; height:1px; background:#f0f0f0;"></div>
                    </div>

                    <form id="pageLoginForm" autocomplete="on">
                        @csrf
                        <div class="mb-3">
                            <label for="pageLoginEmail" class="form-label" style="font-weight: 700; color: #222;">Email Address</label>
                            <input type="email" class="form-control" id="pageLoginEmail" name="email" autocomplete="email" inputmode="email" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px;" placeholder="you@example.com">
                        </div>

                        <div class="mb-2">
                            <label for="pageLoginPassword" class="form-label" style="font-weight: 700; color: #222;">Password</label>
                            <input type="password" class="form-control" id="pageLoginPassword" name="password" autocomplete="current-password" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px;" placeholder="••••••••">
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('password.otp.request') }}" style="color: #1565c0; text-decoration: none; font-weight: 700; font-size: 0.92rem;">Forgot password?</a>
                        </div>

                        <button id="pageLoginSubmit" type="submit" class="btn w-100" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color: #fff; border: none; font-weight: 800; padding: 12px 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(21, 101, 192, 0.25);">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>

                    <div class="text-center" style="margin-top: 16px;">
                        <span style="color:#6b7280; font-weight:600;">Don’t have an account?</span>
                        <a href="#" onclick="showSignupModal(); return false;" style="color:#1565c0; font-weight:800; text-decoration:none;">Create one</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media (max-width: 575.98px) {
        .auth-page-container {
            padding-top: 12px !important;
            padding-bottom: 18px !important;
        }

        .auth-login-card {
            border-radius: 14px !important;
        }

        .auth-login-card .card-header {
            padding: 18px 16px !important;
        }

        .auth-login-card .card-body {
            padding: 16px !important;
        }

        .auth-login-card .form-control {
            padding: 10px 14px !important;
        }

        .auth-login-card .btn {
            padding: 10px 14px !important;
        }

        .auth-login-divider {
            margin: 6px 0 12px 0 !important;
        }

        #pageLoginError {
            margin-bottom: 12px !important;
            font-size: 0.92rem;
        }
    }
</style>
@endpush

@include('auth.modals')

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('pageLoginForm');
    const submitBtn = document.getElementById('pageLoginSubmit');
    const errorEl = document.getElementById('pageLoginError');
    const emailEl = document.getElementById('pageLoginEmail');
    const passEl = document.getElementById('pageLoginPassword');

    const showErr = (msg) => {
        if (!errorEl) return;
        errorEl.textContent = msg || 'Unable to sign in.';
        errorEl.classList.remove('d-none');
    };
    const clearErr = () => {
        if (!errorEl) return;
        errorEl.textContent = '';
        errorEl.classList.add('d-none');
    };

    emailEl?.addEventListener('input', clearErr);
    passEl?.addEventListener('input', clearErr);

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErr();

        const email = (emailEl?.value || '').trim();
        const password = String(passEl?.value || '');
        if (!email || !password) {
            showErr('Please enter your email and password.');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.85';
        }

        try {
            const res = await fetch('{{ route('login') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                showErr(data?.error || data?.message || 'Invalid email or password.');
                return;
            }

            const redirectUrl = data?.redirect_url || '{{ route('home') }}';
            window.location.href = redirectUrl;
        } catch (err) {
            console.error(err);
            showErr('Network error. Please try again.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
        }
    });
});

function showSignupModal() {
    const el = document.getElementById('signupModal');
    if (!el) return;
    const modal = new bootstrap.Modal(el);
    modal.show();
}
</script>
@endsection
