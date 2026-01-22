<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" style="z-index: 1050;">
    <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-2 sm:p-0 overflow-y-auto" style="padding: 16px;">
        <div class="w-full max-w-md max-h-[90vh] overflow-hidden rounded-xl bg-white modal-content" style="border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25); max-height:90vh; overflow:hidden;">
            <!-- Header with gradient background -->
            <div class="modal-header" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); border: none; border-radius: 16px 16px 0 0; padding: 32px 28px 24px;">
                <div>
                    <h5 class="modal-title" style="color: white; font-size: 1.6rem; font-weight: 700; margin: 0;">Welcome Back</h5>
                    <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin: 6px 0 0 0;">Sign in to your account</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="opacity: 0.7;"></button>
            </div>
            
            <form id="loginForm">
                @csrf
                <div class="modal-body" style="padding: 28px; overflow-y: auto; max-height: calc(90vh - 120px); -webkit-overflow-scrolling: touch;">
                    <!-- Error Alert -->
                    <div id="loginErrorAlert" style="display: none; margin-bottom: 20px; padding: 14px 16px; background: #fee; border: 1px solid #fcc; border-radius: 10px; color: #c33; font-size: 0.93rem; animation: slideDown 0.3s ease;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fas fa-exclamation-circle" style="margin-top: 2px; flex-shrink: 0;"></i>
                            <div id="loginErrorText"></div>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="loginEmail" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Email Address</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="you@example.com">
                        <small style="color: #999; display: block; margin-top: 6px;">Enter the email linked to your account</small>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-2">
                        <label for="loginPassword" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Password</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="loginPassword" name="password" autocomplete="current-password" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="••••••••">
                            <button type="button" class="btn" id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #666; cursor: pointer; padding: 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Forgot Password Link -->
                    <div style="text-align: right; margin-bottom: 24px;">
                        <a href="{{ route('password.request') }}" style="color: #1565c0; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease;">Forgot password?</a>
                    </div>

                    <!-- Google Login -->
                    <a href="{{ route('auth.google.redirect') }}" class="btn w-100" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-weight: 700; color: #1f2d3a; background: #fff; display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom: 18px;">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" style="width:18px;height:18px;"> Continue with Google
                    </a>
                </div>

                <!-- Footer -->
                <div class="modal-footer" style="border-top: 1px solid #f0f0f0; padding: 20px 28px; background: transparent; position: sticky; bottom: 0; z-index: 10;">
                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color: white; border: none; font-weight: 600; padding: 12px 20px; border-radius: 10px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </div>

                <!-- Sign Up Link -->
                <div class="text-center pb-4" style="border-top: 1px solid #f0f0f0;">
                    <p style="color: #666; font-size: 0.9rem; margin: 16px 0 0 0;">
                        Don't have an account? 
                        <a href="#" onclick="switchToSignup(event)" style="color: #1565c0; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">Create one</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Signup Modal -->
<div class="modal fade" id="signupModal" tabindex="-1" style="z-index: 1050;">
    <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-2 sm:p-0 overflow-y-auto" style="padding: 16px;">
        <div class="w-full max-w-lg max-h-[90vh] overflow-hidden rounded-xl bg-white modal-content" style="border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25); max-height:90vh; overflow:hidden;">
            <!-- Header with gradient background -->
            <div class="modal-header" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%); border: none; border-radius: 16px 16px 0 0; padding: 32px 28px 24px; position: sticky; top: 0; z-index: 10;">
                <div>
                    <h5 class="modal-title" style="color: white; font-size: 1.6rem; font-weight: 700; margin: 0;">Create Your Account</h5>
                    <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin: 6px 0 0 0;">Join CoreFive Gadgets and start shopping</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="opacity: 0.7;"></button>
            </div>
            
            <form id="signupForm" autocomplete="off">
                @csrf
                <div class="modal-body" style="padding: 28px; overflow-y: auto; max-height: calc(90vh - 120px); -webkit-overflow-scrolling: touch;">
                    <!-- Error Alert -->
                    <div id="signupErrorAlert" style="display: none; margin-bottom: 20px; padding: 14px 16px; background: #fee; border: 1px solid #fcc; border-radius: 10px; color: #c33; font-size: 0.93rem; animation: slideDown 0.3s ease;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fas fa-exclamation-circle" style="margin-top: 2px; flex-shrink: 0;"></i>
                            <div id="signupErrorText"></div>
                        </div>
                    </div>

                    <!-- Google Signup -->
                    <a href="{{ route('auth.google.redirect') }}" class="btn w-100" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-weight: 700; color: #1f2d3a; background: #fff; display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom: 16px;">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" style="width:18px;height:18px;"> Continue with Google
                    </a>

                    <div style="display:flex; align-items:center; gap:12px; margin: 8px 0 18px 0;">
                        <div style="flex:1; height:1px; background:#f0f0f0;"></div>
                        <div style="color:#999; font-size:0.85rem; font-weight:600;">or sign up with email</div>
                        <div style="flex:1; height:1px; background:#f0f0f0;"></div>
                    </div>

                    <!-- Name Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div>
                            <label for="firstName" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="John">
                        </div>
                        <div>
                            <label for="lastName" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="Doe">
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="signupEmail" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Email Address</label>
                        <input type="email" class="form-control" id="signupEmail" name="email" autocomplete="username" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="you@example.com">
                        <small style="color: #999; display: block; margin-top: 6px;">We'll use this to verify your account</small>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="signupPassword" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Password</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="signupPassword" name="password" autocomplete="new-password" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="••••••••">
                            <button type="button" class="btn" id="toggleSignupPassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #666; cursor: pointer; padding: 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Password strength indicator -->
                        <div id="passwordStrength" style="margin-top: 8px; display: none;">
                            <div style="height: 4px; background: #f0f0f0; border-radius: 2px; overflow: hidden;">
                                <div id="passwordStrengthBar" style="height: 100%; width: 0%; background: #dc3545; transition: width 0.3s, background 0.3s; border-radius: 2px;"></div>
                            </div>
                            <small id="passwordStrengthText" style="color: #666; display: block; margin-top: 4px;">Weak password</small>
                        </div>
                        <small style="color: #999; display: block; margin-top: 6px;">At least 8 characters with uppercase, lowercase, and numbers</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="signupPasswordConfirm" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Confirm Password</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="signupPasswordConfirm" name="password_confirmation" autocomplete="new-password" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="••••••••">
                            <button type="button" class="btn" id="toggleSignupPasswordConfirm" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #666; cursor: pointer; padding: 0;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" style="margin-top: 8px; display: none; padding: 8px; border-radius: 6px; font-size: 0.9rem;"></div>
                    </div>

                    <!-- Phone Field -->
                    <div class="mb-4">
                        <label for="contact" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Phone Number (Optional)</label>
                        <input type="tel" class="form-control" id="contact" name="contact" pattern="09[0-9]{9}" inputmode="numeric" maxlength="11" autocomplete="tel" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="09XXXXXXXXX">
                        <small style="color: #999; display: block; margin-top: 6px;">For order updates and support</small>
                    </div>

                    <!-- Address Field -->
                    <div class="mb-4">
                        <label for="address" class="form-label" style="font-weight: 600; color: #222; font-size: 0.95rem; margin-bottom: 8px;">Address (Optional)</label>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div style="grid-column: 1 / -1;">
                                <input type="text" class="form-control" id="signupStreet" name="address_street" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="House no., street, subdivision, unit">
                            </div>
                            <div>
                                <select class="form-select" id="signupRegion" name="address_region_code" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;">
                                    <option value="">Select region</option>
                                </select>
                            </div>
                            <div>
                                <select class="form-select" id="signupProvince" name="address_province_code" disabled style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;">
                                    <option value="">Select province</option>
                                </select>
                            </div>
                            <div>
                                <select class="form-select" id="signupCity" name="address_city_code" disabled style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;">
                                    <option value="">Select city/municipality</option>
                                </select>
                            </div>
                            <div>
                                <select class="form-select" id="signupBarangay" name="address_barangay_code" disabled style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;">
                                    <option value="">Select barangay</option>
                                </select>
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <input type="text" class="form-control" id="signupPostal" name="address_postal_code" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease;" placeholder="Postal code (optional)">
                            </div>
                        </div>

                        <textarea class="form-control" id="address" name="address" rows="2" readonly style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; transition: all 0.3s ease; resize: none; background: #f8f9fa;" placeholder="Address preview (auto-filled)"></textarea>
                        <small style="color: #999; display: block; margin-top: 6px;">Pick your address above — this preview is what we’ll use for shipping.</small>
                    </div>

                    <!-- Terms checkbox -->
                    <div class="mb-4">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <input type="checkbox" id="agreeTerms" name="agreeTerms" style="margin-top: 4px; cursor: pointer;">
                            <label for="agreeTerms" style="color: #666; font-size: 0.9rem; margin: 0; cursor: pointer;">
                                I agree to the <a href="{{ route('pages.terms') }}" style="color: #1565c0; text-decoration: none;">Terms & Conditions</a> and <a href="{{ route('pages.privacy') }}" style="color: #1565c0; text-decoration: none;">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer" style="border-top: 1px solid #f0f0f0; padding: 20px 28px; background: transparent; position: sticky; bottom: 0; z-index: 10;">
                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color: white; border: none; font-weight: 600; padding: 12px 20px; border-radius: 10px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center pb-4" style="border-top: 1px solid #f0f0f0;">
                    <p style="color: #666; font-size: 0.9rem; margin: 16px 0 0 0;">
                        Already have an account? 
                        <a href="#" onclick="switchToLogin(event)" style="color: #1565c0; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">Log in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/ph-address.js') }}"></script>

<style>
@media (max-width: 640px) {
    .modal.fade.show, .modal.fade.in {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    body.modal-open {
        overflow: hidden !important;
        touch-action: none;
    }
    .modal-content {
        max-height: 90vh !important;
        overflow: hidden !important;
    }
    .modal-body {
        overflow-y: auto !important;
        max-height: calc(90vh - 120px) !important;
        -webkit-overflow-scrolling: touch !important;
    }
}
</style>

<script>
function switchToLogin(e) {
    e.preventDefault();
    const signupModal = bootstrap.Modal.getInstance(document.getElementById('signupModal'));
    if (signupModal) signupModal.hide();
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}

function switchToSignup(e) {
    e.preventDefault();
    const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
    if (loginModal) loginModal.hide();
    const signupModal = new bootstrap.Modal(document.getElementById('signupModal'));
    signupModal.show();
}

// Prevent password fields from carrying over between accounts/modal opens
document.getElementById('signupModal')?.addEventListener('shown.bs.modal', () => {
    const pw = document.getElementById('signupPassword');
    const pw2 = document.getElementById('signupPasswordConfirm');
    if (pw) pw.value = '';
    if (pw2) pw2.value = '';

    // Reset strength/match UI (if visible)
    const strengthDiv = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    const matchDiv = document.getElementById('passwordMatch');

    if (strengthDiv) strengthDiv.style.display = 'none';
    if (strengthBar) strengthBar.style.width = '0%';
    if (strengthText) strengthText.textContent = 'Weak password';
    if (matchDiv) {
        matchDiv.style.display = 'none';
        matchDiv.textContent = '';
    }
});

document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear any previous error
    const errorAlert = document.getElementById('loginErrorAlert');
    errorAlert.style.display = 'none';
    
    // Clear previous red highlights
    document.querySelectorAll('#loginForm .form-control').forEach(input => {
        input.style.borderColor = '#e0e0e0';
        input.style.background = 'white';
    });
    
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    
    let hasError = false;
    let errorMessage = '';
    
    // Validation with highlighting
    if (!email) {
        document.getElementById('loginEmail').style.borderColor = '#dc3545';
        document.getElementById('loginEmail').style.background = '#fff5f5';
        errorMessage = 'Please enter your email address';
        hasError = true;
    }
    if (!password) {
        document.getElementById('loginPassword').style.borderColor = '#dc3545';
        document.getElementById('loginPassword').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Please enter your password'; }
        hasError = true;
    }
    
    if (hasError) {
        showLoginError(errorMessage);
        return;
    }
    
    const formData = new FormData(e.target);
    try {
        const response = await fetch('{{ route("login") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (response.ok) {
            window.location.href = data.redirect_url || '{{ route("home") }}';
        } else {
            // Show error in the alert box
            const errorText = document.getElementById('loginErrorText');
            errorText.textContent = data.message || data.error || 'Invalid email or password. Please try again.';
            errorAlert.style.display = 'block';
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    } catch (error) {
        const errorText = document.getElementById('loginErrorText');
        errorText.textContent = 'An error occurred. Please try again.';
        errorAlert.style.display = 'block';
    }
});

function showLoginError(message) {
    const errorAlert = document.getElementById('loginErrorAlert');
    const errorText = document.getElementById('loginErrorText');
    errorText.textContent = message;
    errorAlert.style.display = 'block';
    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

document.getElementById('signupForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear any previous error
    const errorAlert = document.getElementById('signupErrorAlert');
    errorAlert.style.display = 'none';
    
    // Clear previous red highlights
    document.querySelectorAll('#signupForm .form-control').forEach(input => {
        input.style.borderColor = '#e0e0e0';
        input.style.background = 'white';
    });
    document.getElementById('agreeTerms').style.borderColor = '';
    
    // Get form values
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('signupPasswordConfirm').value;
    const contact = document.getElementById('contact').value.trim();
    const agreeTerms = document.getElementById('agreeTerms').checked;
    
    // Track validation errors
    let hasError = false;
    let errorMessage = '';
    
    // Validation with highlighting
    if (!firstName || firstName.length < 2) {
        document.getElementById('firstName').style.borderColor = '#dc3545';
        document.getElementById('firstName').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'First name must be at least 2 characters'; hasError = true; }
    }
    if (!lastName || lastName.length < 2) {
        document.getElementById('lastName').style.borderColor = '#dc3545';
        document.getElementById('lastName').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Last name must be at least 2 characters'; hasError = true; }
    }
    if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        document.getElementById('signupEmail').style.borderColor = '#dc3545';
        document.getElementById('signupEmail').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Please enter a valid email address'; hasError = true; }
    }
    if (!password) {
        document.getElementById('signupPassword').style.borderColor = '#dc3545';
        document.getElementById('signupPassword').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Password is required'; hasError = true; }
    } else if (password.length < 8) {
        document.getElementById('signupPassword').style.borderColor = '#dc3545';
        document.getElementById('signupPassword').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Password must be at least 8 characters'; hasError = true; }
    } else if (!/[A-Z]/.test(password)) {
        document.getElementById('signupPassword').style.borderColor = '#dc3545';
        document.getElementById('signupPassword').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Password must contain at least one uppercase letter'; hasError = true; }
    } else if (!/[a-z]/.test(password)) {
        document.getElementById('signupPassword').style.borderColor = '#dc3545';
        document.getElementById('signupPassword').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Password must contain at least one lowercase letter'; hasError = true; }
    } else if (!/[0-9]/.test(password)) {
        document.getElementById('signupPassword').style.borderColor = '#dc3545';
        document.getElementById('signupPassword').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Password must contain at least one number'; hasError = true; }
    }
    if (!confirmPassword) {
        document.getElementById('signupPasswordConfirm').style.borderColor = '#dc3545';
        document.getElementById('signupPasswordConfirm').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Please confirm your password'; hasError = true; }
    } else if (password !== confirmPassword) {
        document.getElementById('signupPasswordConfirm').style.borderColor = '#dc3545';
        document.getElementById('signupPasswordConfirm').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Passwords do not match'; hasError = true; }
    }
    if (contact && !contact.match(/^09[0-9]{9}$/)) {
        document.getElementById('contact').style.borderColor = '#dc3545';
        document.getElementById('contact').style.background = '#fff5f5';
        if (!hasError) { errorMessage = 'Please enter a valid phone number (09XXXXXXXXX format)'; hasError = true; }
    }
    if (!agreeTerms) {
        document.getElementById('agreeTerms').style.borderColor = '#dc3545';
        document.getElementById('agreeTerms').style.outline = '2px solid #dc3545';
        if (!hasError) { errorMessage = 'You must agree to the Terms & Conditions'; hasError = true; }
    }
    
    if (hasError) {
        showSignupError(errorMessage);
        return;
    }
    
    // Submit form if all validations pass
    const formData = new FormData(e.target);
    try {
        const response = await fetch('{{ route("signup") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (response.ok) {
            window.location.href = data.redirect_url || '{{ route("home") }}';
        } else {
            showSignupError(data.message || 'Signup failed. Email may already be in use.');
        }
    } catch (error) {
        showSignupError('An error occurred. Please try again.');
    }
});

function showSignupError(message) {
    const errorAlert = document.getElementById('signupErrorAlert');
    const errorText = document.getElementById('signupErrorText');
    errorText.textContent = message;
    errorAlert.style.display = 'block';
    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

document.getElementById('togglePassword')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('loginPassword');
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Signup password visibility toggle
document.getElementById('toggleSignupPassword')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('signupPassword');
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

document.getElementById('toggleSignupPasswordConfirm')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('signupPasswordConfirm');
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Password strength indicator
document.getElementById('signupPassword')?.addEventListener('input', function() {
    const password = this.value;
    const strengthDiv = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    
    let strength = 0;
    if (password.length >= 8) strength += 20;
    if (password.length >= 12) strength += 10;
    if (/[a-z]/.test(password)) strength += 20;
    if (/[A-Z]/.test(password)) strength += 20;
    if (/[0-9]/.test(password)) strength += 20;
    if (/[!@#$%^&*]/.test(password)) strength += 10;
    
    if (password.length === 0) {
        strengthDiv.style.display = 'none';
    } else {
        strengthDiv.style.display = 'block';
        strengthBar.style.width = strength + '%';
        
        if (strength < 40) {
            strengthBar.style.background = '#dc3545';
            strengthText.textContent = 'Weak password';
            strengthText.style.color = '#dc3545';
        } else if (strength < 70) {
            strengthBar.style.background = '#ffc107';
            strengthText.textContent = 'Fair password';
            strengthText.style.color = '#ffc107';
        } else {
            strengthBar.style.background = '#28a745';
            strengthText.textContent = 'Strong password';
            strengthText.style.color = '#28a745';
        }
    }
    
    // Check password match
    checkPasswordMatch();
});

// Password match indicator
document.getElementById('signupPasswordConfirm')?.addEventListener('input', checkPasswordMatch);

function checkPasswordMatch() {
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('signupPasswordConfirm').value;
    const matchDiv = document.getElementById('passwordMatch');
    
    if (confirmPassword.length === 0) {
        matchDiv.style.display = 'none';
    } else {
        matchDiv.style.display = 'block';
        if (password === confirmPassword) {
            matchDiv.style.background = '#d4edda';
            matchDiv.style.color = '#155724';
            matchDiv.style.borderLeft = '3px solid #28a745';
            matchDiv.textContent = '✓ Passwords match';
        } else {
            matchDiv.style.background = '#f8d7da';
            matchDiv.style.color = '#721c24';
            matchDiv.style.borderLeft = '3px solid #dc3545';
            matchDiv.textContent = '✗ Passwords do not match';
        }
    }
}

// Add input focus styling for login
document.getElementById('loginEmail')?.addEventListener('focus', function() {
    this.style.borderColor = '#1565c0';
    this.style.boxShadow = '0 0 0 3px rgba(21, 101, 192, 0.1)';
});
document.getElementById('loginEmail')?.addEventListener('blur', function() {
    this.style.borderColor = '#e0e0e0';
    this.style.boxShadow = 'none';
});

document.getElementById('loginPassword')?.addEventListener('focus', function() {
    this.style.borderColor = '#1565c0';
    this.style.boxShadow = '0 0 0 3px rgba(21, 101, 192, 0.1)';
});
document.getElementById('loginPassword')?.addEventListener('blur', function() {
    this.style.borderColor = '#e0e0e0';
    this.style.boxShadow = 'none';
});

// Add input focus styling for signup
const signupInputs = ['firstName', 'lastName', 'signupEmail', 'signupPassword', 'signupPasswordConfirm', 'contact', 'signupStreet', 'signupRegion', 'signupProvince', 'signupCity', 'signupBarangay', 'signupPostal', 'address'];
signupInputs.forEach(id => {
    document.getElementById(id)?.addEventListener('focus', function() {
        this.style.borderColor = '#1565c0';
        this.style.boxShadow = '0 0 0 3px rgba(21, 101, 192, 0.1)';
    });
    document.getElementById(id)?.addEventListener('blur', function() {
        this.style.borderColor = '#e0e0e0';
        this.style.boxShadow = 'none';
    });
});

// Force phone input to digits only and max length 11
document.getElementById('contact')?.addEventListener('input', function() {
    const digitsOnly = this.value.replace(/\D/g, '').slice(0, 11);
    if (this.value !== digitsOnly) {
        this.value = digitsOnly;
    }
});

// Initialize PH address dropdowns for signup
document.addEventListener('DOMContentLoaded', () => {
    if (window.PHAddress && window.PHAddress.initSelector) {
        (async () => {
            try {
                await window.PHAddress.initSelector({
                    regionSelect: '#signupRegion',
                    provinceSelect: '#signupProvince',
                    citySelect: '#signupCity',
                    barangaySelect: '#signupBarangay',
                    streetInput: '#signupStreet',
                    postalInput: '#signupPostal',
                    previewTextarea: '#address',
                    onAnyChange: () => {},
                    initial: {},
                });
            } catch (e) {
                // Non-blocking: signup can still proceed without address dropdown data.
                console.warn('PH address init failed', e);
            }
        })();
    }
});
</script>
