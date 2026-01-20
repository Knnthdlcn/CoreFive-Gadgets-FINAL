@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <!-- Hero Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%);">
        <div class="container text-center text-white">
            <h1 class="display-4 fw-bold mb-3">Get in Touch</h1>
            <p class="lead mb-0">We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
        </div>
    </section>

        <section class="container py-5">
            <div class="row g-4">
                <!-- Contact Form -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                        <div class="card-body p-4">
                            <!-- Success Message Alert -->
                            <div id="successAlert" style="display: none; margin-bottom: 18px; padding: 16px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 4px solid #28a745; border-radius: 10px; animation: slideDown 0.4s ease;">
                                <div style="display: flex; align-items: flex-start; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.25rem;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h5 style="color: #155724; font-weight: 700; margin: 0 0 6px 0;">Message Sent Successfully!</h5>
                                        <p style="color: #155724; margin: 0; font-size: 0.92rem; line-height: 1.5;">Thank you for reaching out! We've received your message and our team will get back to you within 24-48 hours.</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('successAlert').style.display='none'" style="background: none; border: none; color: #155724; font-size: 1.05rem; cursor: pointer; padding: 0; width: 22px; height: 22px; flex-shrink: 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Error Message Alert -->
                            <div id="errorAlert" style="display: none; margin-bottom: 18px; padding: 16px; background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%); border-left: 4px solid #dc3545; border-radius: 10px; animation: slideDown 0.4s ease;">
                                <div style="display: flex; align-items: flex-start; gap: 10px;">
                                    <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-exclamation-triangle" style="color: #dc3545; font-size: 1.15rem;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h5 style="color: #7f1d1d; font-weight: 700; margin: 0 0 6px 0;">Message Not Sent</h5>
                                        <p id="errorAlertText" style="color: #7f1d1d; margin: 0; font-size: 0.92rem; line-height: 1.5;">Error sending message. Please try again.</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('errorAlert').style.display='none'" style="background: none; border: none; color: #7f1d1d; font-size: 1.05rem; cursor: pointer; padding: 0; width: 22px; height: 22px; flex-shrink: 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <h3 class="mb-3" style="font-weight: 700; color: #2c3e50;">Send us a Message</h3>
                            <form id="contactForm" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold" style="color: #2c3e50; font-size: 0.95rem;">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Your name" minlength="2" required pattern="[A-Za-z]+([-'][A-Za-z]+)*(\s+[A-Za-z]+([-'][A-Za-z]+)*)*" title="Letters, spaces, hyphens, and apostrophes only" style="border-radius: 9px; border: 2px solid #e0e0e0; padding: 12px 14px; transition: all 0.3s ease; font-size: 0.94rem;">
                                    <small style="color: #999; display: block; margin-top: 4px;">How should we address you?</small>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold" style="color: #2c3e50; font-size: 0.95rem;">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" style="border-radius: 9px; border: 2px solid #e0e0e0; padding: 12px 14px; transition: all 0.3s ease; font-size: 0.94rem;">
                                    <small style="color: #999; display: block; margin-top: 4px;">We'll reply to this address</small>
                                </div>
                                <div class="mb-3">
                                    <label for="contact" class="form-label fw-semibold" style="color: #2c3e50; font-size: 0.95rem;">Phone Number</label>
                                    <input type="tel" id="contact" name="contact" class="form-control" placeholder="09XXXXXXXXX" inputmode="numeric" maxlength="11" autocomplete="tel" style="border-radius: 9px; border: 2px solid #e0e0e0; padding: 12px 14px; transition: all 0.3s ease; font-size: 0.94rem;">
                                    <small style="color: #999; display: block; margin-top: 4px;">Format: 09XXXXXXXXX (11 digits)</small>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label fw-semibold" style="color: #2c3e50; font-size: 0.95rem;">Message</label>
                                    <textarea id="message" name="message" rows="5" class="form-control" placeholder="Write your message here..." minlength="10" style="border-radius: 9px; border: 2px solid #e0e0e0; padding: 12px 14px; transition: all 0.3s ease; resize: none; font-size: 0.94rem;"></textarea>
                                    <small style="color: #999; display: block; margin-top: 4px;">Please provide details about your inquiry</small>
                                </div>
                                <button type="submit" class="btn w-100 send-btn" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); border: none; color: white; font-weight: 700; border-radius: 9px; padding: 12px; box-shadow: 0 3px 10px rgba(21, 101, 192, 0.28); transition: all 0.3s ease;">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-5">
                    <div class="h-100 d-flex flex-column gap-4">
                        <!-- Visit Us Card -->
                        <div class="card border-0 shadow contact-info-card" style="border-radius: 14px; overflow: hidden; transition: all 0.3s ease;">
                            <div class="card-body p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <div class="d-flex align-items-start">
                                    <div class="icon-wrapper me-3" style="width: 42px; height: 42px; background: #e3f2fd; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-map-marker-alt" style="font-size: 1.25rem; color: #1565c0;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold" style="color: #2c3e50;">Visit Us</h5>
                                        <p class="mb-0 text-muted">123 Main Street, Manila, Philippines</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Call Us Card -->
                        <div class="card border-0 shadow contact-info-card" style="border-radius: 14px; overflow: hidden; transition: all 0.3s ease;">
                            <div class="card-body p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <div class="d-flex align-items-start">
                                    <div class="icon-wrapper me-3" style="width: 42px; height: 42px; background: #e3f2fd; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-phone-alt" style="font-size: 1.25rem; color: #1565c0;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold" style="color: #2c3e50;">Call Us</h5>
                                        <p class="mb-0 text-muted">+63 912 302 4591</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Card -->
                        <div class="card border-0 shadow contact-info-card" style="border-radius: 14px; overflow: hidden; transition: all 0.3s ease;">
                            <div class="card-body p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <div class="d-flex align-items-start">
                                    <div class="icon-wrapper me-3" style="width: 42px; height: 42px; background: #e3f2fd; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-envelope" style="font-size: 1.25rem; color: #1565c0;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold" style="color: #2c3e50;">Email</h5>
                                        <p class="mb-0 text-muted">support@corefivegadgets.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
                // Add focus effects for inputs
                document.addEventListener('DOMContentLoaded', function() {
                    const inputs = document.querySelectorAll('#contactForm input, #contactForm textarea');
                    inputs.forEach(input => {
                        input.addEventListener('focus', function() {
                            this.style.borderColor = '#1565c0';
                            this.style.boxShadow = '0 0 0 3px rgba(21, 101, 192, 0.1)';
                        });
                        input.addEventListener('blur', function() {
                            if (!this.classList.contains('is-invalid')) {
                                this.style.borderColor = '#e0e0e0';
                                this.style.boxShadow = 'none';
                            }
                        });
                    });

                    // Send button hover effect
                    const sendBtn = document.querySelector('.send-btn');
                    if (sendBtn) {
                        sendBtn.addEventListener('mouseenter', function() {
                            this.style.transform = 'translateY(-2px)';
                            this.style.boxShadow = '0 8px 18px rgba(21, 101, 192, 0.4)';
                            this.style.background = 'linear-gradient(135deg, #0d47a1 0%, #1565c0 100%)';
                        });
                        sendBtn.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = '0 4px 12px rgba(21, 101, 192, 0.3)';
                            this.style.background = 'linear-gradient(135deg, #1565c0 0%, #0d47a1 100%)';
                        });
                    }

                    // Info cards hover effect
                    const infoCards = document.querySelectorAll('.contact-info-card');
                    infoCards.forEach(card => {
                        card.addEventListener('mouseenter', function() {
                            this.style.transform = 'translateY(-4px)';
                            this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
                        });
                        card.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
                        });
                    });
                });

                document.getElementById('contactForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                
                    // Get form fields
                    const nameInput = document.getElementById('name');
                    const emailInput = document.getElementById('email');
                    const contactInput = document.getElementById('contact');
                    const messageInput = document.getElementById('message');
                
                    // Clear previous validation states
                    [nameInput, emailInput, contactInput, messageInput].forEach(input => {
                        input.classList.remove('is-invalid');
                        input.style.borderColor = '#e0e0e0';
                        input.style.background = 'white';
                    });
                
                    let isValid = true;
                    let errorMessage = '';
                
                    // Validate name
                    const nameValue = nameInput.value.trim();
                    const namePattern = /^[A-Za-z]+(?:[-'][A-Za-z]+)*(?:\s+[A-Za-z]+(?:[-'][A-Za-z]+)*)*$/;
                    if (!nameValue || nameValue.length < 2 || !namePattern.test(nameValue)) {
                        nameInput.classList.add('is-invalid');
                        nameInput.style.borderColor = '#dc3545';
                        nameInput.style.background = '#fff5f5';
                        if (!nameValue) nameInput.focus();
                        isValid = false;
                        errorMessage = "Full name must contain letters, spaces, hyphens, and apostrophes only";
                    }
                
                    // Validate email
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailInput.value.trim() || !emailPattern.test(emailInput.value.trim())) {
                        emailInput.classList.add('is-invalid');
                        emailInput.style.borderColor = '#dc3545';
                        emailInput.style.background = '#fff5f5';
                        if (isValid) emailInput.focus();
                        isValid = false;
                        if (!errorMessage) errorMessage = 'Please enter a valid email address';
                    }

                    // Validate contact
                    const phonePattern = /^09\d{9}$/;
                    if (!contactInput.value.trim() || !phonePattern.test(contactInput.value.trim())) {
                        contactInput.classList.add('is-invalid');
                        contactInput.style.borderColor = '#dc3545';
                        contactInput.style.background = '#fff5f5';
                        if (isValid) contactInput.focus();
                        isValid = false;
                        if (!errorMessage) errorMessage = 'Please enter a valid phone number (09XXXXXXXXX)';
                    }
                
                    // Validate message
                    if (!messageInput.value.trim() || messageInput.value.trim().length < 10) {
                        messageInput.classList.add('is-invalid');
                        messageInput.style.borderColor = '#dc3545';
                        messageInput.style.background = '#fff5f5';
                        if (isValid) messageInput.focus();
                        isValid = false;
                        if (!errorMessage) errorMessage = 'Message must be at least 10 characters';
                    }
                
                    if (!isValid) {
                        return;
                    }

                    const successAlert = document.getElementById('successAlert');
                    const errorAlert = document.getElementById('errorAlert');
                    const errorAlertText = document.getElementById('errorAlertText');
                    if (successAlert) successAlert.style.display = 'none';
                    if (errorAlert) errorAlert.style.display = 'none';
                
                    const formData = new FormData(e.target);
                    try {
                        const response = await fetch('{{ route("contact.store") }}', {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (response.ok) {
                            // Show success alert
                            document.getElementById('successAlert').style.display = 'block';
                            document.getElementById('successAlert').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            e.target.reset();
                        
                            // Hide alert after 8 seconds
                            setTimeout(() => {
                                const alert = document.getElementById('successAlert');
                                if (alert) {
                                    alert.style.opacity = '0';
                                    alert.style.transition = 'opacity 0.5s ease';
                                    setTimeout(() => {
                                        alert.style.display = 'none';
                                        alert.style.opacity = '1';
                                    }, 500);
                                }
                            }, 8000);
                        } else {
                            let message = 'Error sending message. Please try again.';
                            try {
                                const data = await response.json();
                                if (data?.message) message = data.message;
                                if (data?.errors) {
                                    const firstKey = Object.keys(data.errors)[0];
                                    const firstErr = firstKey ? data.errors[firstKey]?.[0] : null;
                                    if (firstErr) message = firstErr;
                                }
                            } catch (_) {
                                // ignore JSON parse issues
                            }

                            if (errorAlertText) errorAlertText.textContent = message;
                            if (errorAlert) {
                                errorAlert.style.display = 'block';
                                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            }
                        }
                    } catch (error) {
                        if (errorAlertText) errorAlertText.textContent = 'Error sending message. Please try again.';
                        if (errorAlert) {
                            errorAlert.style.display = 'block';
                            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                });
            
                // Remove validation error when user starts typing
                ['name', 'email', 'contact', 'message'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.addEventListener('input', () => {
                            field.classList.remove('is-invalid');
                            field.style.borderColor = '#e0e0e0';
                            field.style.background = 'white';
                        });
                    }
                });

                // Force contact input to digits only and max length 11
                document.getElementById('contact')?.addEventListener('input', function() {
                    const digitsOnly = this.value.replace(/\D/g, '').slice(0, 11);
                    if (this.value !== digitsOnly) {
                        this.value = digitsOnly;
                    }
                });
            </script>
@endsection
