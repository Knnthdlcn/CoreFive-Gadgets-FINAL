@extends('layouts.app')

@section('title', 'Reset Password (OTP)')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0" style="border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12); overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); padding: 26px 24px;">
                    <h4 class="m-0" style="color:#fff; font-weight: 800;">Reset Password</h4>
                    <p class="m-0" style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">Enter your email + 6-digit code, then choose a new password.</p>
                </div>

                <div class="card-body" style="padding: 24px;">
                    @if (session('status'))
                        <div class="alert" role="alert" style="border-radius: 12px; border: 1px solid rgba(212,175,55,0.35); background: rgba(212,175,55,0.12); color: #1f2d3a; font-weight: 800;">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.otp.update') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700;">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $email ?? '') }}" class="form-control @error('email') is-invalid @enderror" style="border-radius: 12px; padding: 12px 14px; background: #f5f9ff; border-color: rgba(26,58,82,0.25);" autocomplete="email" inputmode="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700;">OTP Code</label>
                            <input type="text" name="code" inputmode="numeric" maxlength="6" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror" style="border-radius: 12px; padding: 12px 14px; letter-spacing: 6px; font-weight: 900; text-align:center; background: #f5f9ff; border-color: rgba(26,58,82,0.25);" placeholder="______">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700;">New Password</label>
                            <div class="input-group" style="border-radius: 12px; overflow: hidden;">
                                <input id="new_password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" style="padding: 12px 14px; background: #f5f9ff; border-color: rgba(26,58,82,0.25);" autocomplete="new-password" aria-describedby="password-strength-text">
                                <button id="toggle_new_password" class="btn btn-outline-secondary" type="button" style="border-color: rgba(26,58,82,0.25); font-weight: 800;">Show</button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="mt-2">
                                <div class="progress" style="height: 10px; border-radius: 999px; background: rgba(26,58,82,0.10);">
                                    <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%; background: #94a3b8; border-radius: 999px;"></div>
                                </div>
                                <div id="password-strength-text" class="form-text" style="color:#6b7b88; font-weight: 700; margin-top: 8px;">Password strength: —</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 700;">Confirm Password</label>
                            <div class="input-group" style="border-radius: 12px; overflow: hidden;">
                                <input id="confirm_password" type="password" name="password_confirmation" class="form-control" style="padding: 12px 14px; background: #f5f9ff; border-color: rgba(26,58,82,0.25);" autocomplete="new-password">
                                <button id="toggle_confirm_password" class="btn btn-outline-secondary" type="button" style="border-color: rgba(26,58,82,0.25); font-weight: 800;">Show</button>
                            </div>
                            <div id="password-match-text" class="form-text" style="color:#6b7b88; font-weight: 700; margin-top: 8px;">Passwords match: —</div>
                        </div>

                        <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color:#fff; border:none; border-radius: 12px; padding: 12px 16px; font-weight: 900; letter-spacing: 0.2px; box-shadow: 0 10px 22px rgba(21, 101, 192, 0.28);">
                            Update Password
                        </button>

                        <div class="text-center mt-3" style="display:flex; gap:12px; justify-content:center;">
                            <a href="{{ route('password.request', ['email' => old('email', $email ?? '')]) }}" style="text-decoration:none; font-weight: 700; color: #1565c0;">Request new OTP</a>
                            <span style="color:#cbd5e1;">•</span>
                            <a href="{{ route('home') }}" style="text-decoration:none; font-weight: 700; color: #1565c0;">Back to Home</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.querySelector('input[name="code"]')?.addEventListener('input', function() {
    const digitsOnly = this.value.replace(/\D/g, '').slice(0, 6);
    if (this.value !== digitsOnly) {
        this.value = digitsOnly;
    }
});

function setPasswordVisibility(inputEl, buttonEl, visible) {
    inputEl.type = visible ? 'text' : 'password';
    buttonEl.textContent = visible ? 'Hide' : 'Show';
}

function passwordStrengthScore(password) {
    if (!password) return 0;

    let score = 0;
    const length = password.length;

    // Length
    if (length >= 8) score++;
    if (length >= 12) score++;

    // Character variety
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    return Math.min(score, 5);
}

function renderStrength(password) {
    const bar = document.getElementById('password-strength-bar');
    const text = document.getElementById('password-strength-text');
    if (!bar || !text) return;

    const score = passwordStrengthScore(password);
    const percent = Math.round((score / 5) * 100);

    let label = '—';
    let color = '#94a3b8';

    if (password.length === 0) {
        label = '—';
        color = '#94a3b8';
    } else if (score <= 1) {
        label = 'Weak';
        color = '#ef4444';
    } else if (score === 2) {
        label = 'Fair';
        color = '#f59e0b';
    } else if (score === 3) {
        label = 'Good';
        color = '#22c55e';
    } else {
        label = 'Strong';
        color = '#16a34a';
    }

    bar.style.width = percent + '%';
    bar.style.background = color;
    text.textContent = 'Password strength: ' + label;
}

function renderMatch(newPassword, confirmPassword) {
    const text = document.getElementById('password-match-text');
    if (!text) return;

    if (!newPassword && !confirmPassword) {
        text.textContent = 'Passwords match: —';
        text.style.color = '#6b7b88';
        return;
    }

    const matches = newPassword === confirmPassword;
    text.textContent = 'Passwords match: ' + (matches ? 'Yes' : 'No');
    text.style.color = matches ? '#16a34a' : '#ef4444';
}

const newPasswordEl = document.getElementById('new_password');
const confirmPasswordEl = document.getElementById('confirm_password');
const toggleNewBtn = document.getElementById('toggle_new_password');
const toggleConfirmBtn = document.getElementById('toggle_confirm_password');

if (newPasswordEl && toggleNewBtn) {
    let visible = false;
    toggleNewBtn.addEventListener('click', function() {
        visible = !visible;
        setPasswordVisibility(newPasswordEl, toggleNewBtn, visible);
        newPasswordEl.focus();
    });
}

if (confirmPasswordEl && toggleConfirmBtn) {
    let visible = false;
    toggleConfirmBtn.addEventListener('click', function() {
        visible = !visible;
        setPasswordVisibility(confirmPasswordEl, toggleConfirmBtn, visible);
        confirmPasswordEl.focus();
    });
}

newPasswordEl?.addEventListener('input', function() {
    renderStrength(this.value);
    renderMatch(this.value, confirmPasswordEl?.value ?? '');
});

confirmPasswordEl?.addEventListener('input', function() {
    renderMatch(newPasswordEl?.value ?? '', this.value);
});

// Initial render
renderStrength(newPasswordEl?.value ?? '');
renderMatch(newPasswordEl?.value ?? '', confirmPasswordEl?.value ?? '');
</script>
@endsection
