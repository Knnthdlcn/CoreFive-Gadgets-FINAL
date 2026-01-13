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
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-5">
                        <h3 class="mb-4" style="font-weight: 700; color: #2c3e50;">Send us a Message</h3>
                        <form id="contactForm" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold" style="color: #2c3e50;">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control form-control-lg" placeholder="Your name" minlength="2" style="border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 16px; transition: all 0.3s ease;">
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold" style="color: #2c3e50;">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="name@example.com" style="border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 16px; transition: all 0.3s ease;">
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label fw-semibold" style="color: #2c3e50;">Message</label>
                                <textarea id="message" name="message" rows="5" class="form-control form-control-lg" placeholder="Write your message here..." minlength="10" style="border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 16px; transition: all 0.3s ease; resize: none;"></textarea>
                            </div>
                            <button type="submit" class="btn btn-lg w-100 send-btn" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #1f2a34; font-weight: 700; border-radius: 10px; padding: 14px; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.25); transition: all 0.3s ease;">
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
                    <div class="card border-0 shadow contact-info-card" style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease;">
                        <div class="card-body p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <div class="d-flex align-items-start">
                                <div class="icon-wrapper me-3" style="width: 50px; height: 50px; background: #fff8e1; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-map-marker-alt" style="font-size: 1.5rem; color: #ffb300;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-2 fw-bold" style="color: #2c3e50;">Visit Us</h5>
                                    <p class="mb-0 text-muted">123 Main Street, Manila, Philippines</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Call Us Card -->
                    <div class="card border-0 shadow contact-info-card" style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease;">
                        <div class="card-body p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <div class="d-flex align-items-start">
                                <div class="icon-wrapper me-3" style="width: 50px; height: 50px; background: #fff8e1; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-phone-alt" style="font-size: 1.5rem; color: #ffb300;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-2 fw-bold" style="color: #2c3e50;">Call Us</h5>
                                    <p class="mb-0 text-muted">+63 912 302 4591</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Card -->
                    <div class="card border-0 shadow contact-info-card" style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease;">
                        <div class="card-body p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <div class="d-flex align-items-start">
                                <div class="icon-wrapper me-3" style="width: 50px; height: 50px; background: #fff8e1; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-envelope" style="font-size: 1.5rem; color: #ffb300;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-2 fw-bold" style="color: #2c3e50;">Email</h5>
                                    <p class="mb-0 text-muted">support@corefivegadgets.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1055">
        <div id="successToast" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    Message sent successfully! We'll get back to you soon.
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Add focus effects for inputs
            document.addEventListener('DOMContentLoaded', function() {
                const inputs = document.querySelectorAll('#contactForm input, #contactForm textarea');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.style.borderColor = '#495057';
                        this.style.boxShadow = '0 0 0 0.2rem rgba(73, 80, 87, 0.1)';
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
                        this.style.boxShadow = '0 8px 18px rgba(255, 193, 7, 0.35)';
                        this.style.background = 'linear-gradient(135deg, #ffb300 0%, #ff8f00 100%)';
                    });
                    sendBtn.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 4px 12px rgba(255, 193, 7, 0.25)';
                        this.style.background = 'linear-gradient(135deg, #ffc107 0%, #ff9800 100%)';
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
                const messageInput = document.getElementById('message');
                
                // Clear previous validation states
                nameInput.classList.remove('is-invalid');
                emailInput.classList.remove('is-invalid');
                messageInput.classList.remove('is-invalid');
                
                let isValid = true;
                
                // Validate name
                if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
                    nameInput.classList.add('is-invalid');
                    nameInput.style.borderColor = '#dc3545';
                    if (!nameInput.value.trim()) nameInput.focus();
                    isValid = false;
                }
                
                // Validate email
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailInput.value.trim() || !emailPattern.test(emailInput.value.trim())) {
                    emailInput.classList.add('is-invalid');
                    emailInput.style.borderColor = '#dc3545';
                    if (isValid) emailInput.focus();
                    isValid = false;
                }
                
                // Validate message
                if (!messageInput.value.trim() || messageInput.value.trim().length < 10) {
                    messageInput.classList.add('is-invalid');
                    messageInput.style.borderColor = '#dc3545';
                    if (isValid) messageInput.focus();
                    isValid = false;
                }
                
                if (!isValid) {
                    return;
                }
                
                const formData = new FormData(e.target);
                try {
                    const response = await fetch('{{ route("contact.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (response.ok) {
                        e.target.reset();
                        Toast.show('Message sent successfully!');
                    } else {
                        Toast.show('Error sending message');
                    }
                } catch (error) {
                    Toast.show('Error sending message');
                }
            });
            
            // Remove validation error when user starts typing
            ['name', 'email', 'message'].forEach(id => {
                const field = document.getElementById(id);
                if (field) {
                    field.addEventListener('input', () => {
                        field.classList.remove('is-invalid');
                        field.style.borderColor = '#e0e0e0';
                    });
                }
            });
        </script>
    @endpush
@endsection
