@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <style>
        /* Profile photo crop modal polish (opaque + modern, scoped to this modal only)
           NOTE: do NOT use class name `cropper-modal` here because Cropper.js defines
           `.cropper-modal { opacity: ... }` which makes the whole modal look transparent.
        */
        .profile-cropper-modal .modal-dialog {
            max-width: 860px;
            opacity: 1 !important; /* defend against theme rules */
        }
        .profile-cropper-modal .modal-content {
            background: #ffffff !important; /* force opaque */
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.38);
            opacity: 1 !important;
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }
        .profile-cropper-modal .modal-header {
            background: linear-gradient(135deg, #06131a 0%, #1a3a52 100%) !important;
            border: 0;
            padding: 16px 18px;
            opacity: 1 !important;
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }
        .profile-cropper-modal .modal-title {
            color: #fff;
            font-weight: 900;
            margin: 0;
            letter-spacing: 0.2px;
        }
        .cropper-help {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.92rem;
            margin: 2px 0 0;
            line-height: 1.3;
        }
        .profile-cropper-modal .modal-body {
            background: #0b1720 !important;
            padding: 16px;
            opacity: 1 !important;
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }
        .cropper-stage {
            width: 100%;
            min-height: 320px;
            max-height: 60vh;
            background: #0b1720;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #cropperImage {
            width: 100%;
            max-height: 60vh;
            display: block;
            object-fit: contain;
        }
        .cropper-actions {
            border: 0;
            background: #ffffff !important;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            opacity: 1 !important;
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }

        /* Darken ONLY the cropper modal backdrop (no blur, no glass) */
        .modal-backdrop.cropper-backdrop.show {
            background-color: rgba(0, 0, 0, 0.88) !important;
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }
        .cropper-actions-left,
        .cropper-actions-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .cropper-actions .btn {
            border-radius: 12px;
            font-weight: 750;
            padding: 10px 12px;
            line-height: 1;
            box-shadow: none;
        }
        .cropper-actions .btn-outline-secondary {
            border-color: #dee2e6;
            color: #334155;
            background: #f8fafc;
        }
        .cropper-actions .btn-outline-secondary:hover {
            background: #eef2f7;
        }
        .cropper-actions .btn-outline-secondary:active {
            transform: translateY(1px);
        }
        #cropperUpload {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border: none;
            color: #1f2937;
            padding: 10px 14px;
        }
        #cropperUpload:active {
            transform: translateY(1px);
        }
        @media (max-width: 576px) {
            .profile-cropper-modal .modal-dialog {
                margin: 0.75rem;
            }
            .profile-cropper-modal .modal-body {
                padding: 12px;
            }
            .cropper-actions {
                padding: 12px;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <main class="container py-5 content-with-footer">
        <!-- Page Header -->
        <div class="mb-5">
            <h2 class="mb-2" style="font-weight: 700; font-size: 2rem; color: #2c3e50;">My Profile</h2>
            <p class="text-muted mb-0">Manage your account information and settings</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm text-center" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 32px;">
                        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                            <img src="{{ asset('storage/' . $user->avatar) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <div id="profilePhotoDisplay">
                                    @if(Auth::user()->profile_photo)
                                        <img id="profilePhotoPreview" src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" class="rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ffc107;">
                                    @else
                                        <i id="profilePhotoIcon" class="fas fa-user-circle" style="font-size: 5rem; color: #ffc107;"></i>
                                    @endif
                                </div>
                            </div>
                            <h4 class="mb-1" style="font-weight: 700; color: #2c3e50;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
                            <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
                            <div class="d-grid gap-2">
                                <div id="photoSizeAlert" class="alert alert-danger" role="alert" style="display:none; border-radius: 10px;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Profile photo is too large. Maximum allowed is <strong>5MB</strong>.
                                </div>

                                <div id="photoUploadAlert" class="alert alert-danger" role="alert" style="display:none; border-radius: 10px;"></div>

                                <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*" style="display: none;" onchange="handleProfilePhotoChange(this)">
                                <button type="button" class="btn" onclick="document.getElementById('profilePhotoInput').click();" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; border-radius: 8px; padding: 10px;">
                                    <i class="fas fa-camera me-2"></i>Change Photo
                                </button>
                                <small class="text-muted" style="font-weight: 600;">Max file size: 5MB</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Crop / Adjust Modal -->
            <div class="modal fade profile-cropper-modal" id="profilePhotoCropModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title">Adjust profile photo</h5>
                                <p class="cropper-help">Drag to move • Scroll/pinch to zoom • Square crop</p>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="cropper-stage">
                                <img id="cropperImage" alt="Crop" />
                            </div>
                        </div>

                        <div class="modal-footer cropper-actions">
                            <div class="cropper-actions-left">
                                <button type="button" class="btn btn-outline-secondary" id="cropperRotateLeft">
                                    <i class="fas fa-rotate-left me-1"></i>Rotate
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="cropperZoomIn">
                                    <i class="fas fa-magnifying-glass-plus me-1"></i>Zoom In
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="cropperZoomOut">
                                    <i class="fas fa-magnifying-glass-minus me-1"></i>Zoom Out
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="cropperReset">
                                    <i class="fas fa-rotate me-1"></i>Reset
                                </button>
                            </div>

                            <div class="cropper-actions-right">
                                <button type="button" class="btn" id="cropperUpload">
                                    <span id="cropperUploadText"><i class="fas fa-check me-1"></i>Use Photo</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <h5 class="card-title mb-4" style="font-weight: 700; color: #2c3e50;">
                            <i class="fas fa-info-circle me-2" style="color: #ffc107;"></i>Account Information
                        </h5>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ Auth::user()->first_name }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ Auth::user()->last_name }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #495057;">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #495057;">Phone Number</label>
                                <input type="tel" name="contact" class="form-control" value="{{ Auth::user()->contact }}" placeholder="09XXXXXXXXX" inputmode="numeric" maxlength="11" autocomplete="tel" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                <small class="text-muted">Format: 09XXXXXXXXX</small>
                            </div>

                            <hr class="my-4" style="border-color: #e9ecef;">

                            <h6 class="mb-3" style="font-weight: 700; color: #2c3e50;">Shipping Address (Philippines)</h6>

                            <input type="hidden" name="address" id="profileAddressManual" value="{{ Auth::user()->address }}">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">Region</label>
                                    <select id="profileRegion" name="address_region_code" class="form-select" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                        <option value="">Select region</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">Province</label>
                                    <select id="profileProvince" name="address_province_code" class="form-select" disabled style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                        <option value="">Select province</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">City / Municipality</label>
                                    <select id="profileCity" name="address_city_code" class="form-select" disabled style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                        <option value="">Select city/municipality</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">Barangay</label>
                                    <select id="profileBarangay" name="address_barangay_code" class="form-select" disabled style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                        <option value="">Select barangay</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">Street / Building / Unit</label>
                                    <input type="text" id="profileStreet" name="address_street" class="form-control" value="{{ Auth::user()->address_street }}" placeholder="House no., street, subdivision, unit" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">Postal Code</label>
                                    <input type="text" id="profilePostal" name="address_postal_code" class="form-control" value="{{ Auth::user()->address_postal_code }}" placeholder="e.g. 1000" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #495057;">Address Preview</label>
                                <textarea id="profileAddressPreview" class="form-control" rows="2" readonly style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px; background: #f8f9fa;">{{ Auth::user()->address }}</textarea>
                                <small class="text-muted">This is what will be used at checkout when you choose “saved address”.</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; border-radius: 8px; padding: 12px; box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3); transition: all 0.3s ease;">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <h5 class="card-title mb-4" style="font-weight: 700; color: #2c3e50;">
                            <i class="fas fa-lock me-2" style="color: #ffc107;"></i>Change Password
                        </h5>
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #495057;">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #495057;">New Password</label>
                                <input type="password" name="password" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #495057;">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-warning" style="font-weight: 600; border-radius: 8px; padding: 12px; border-width: 2px; transition: all 0.3s ease;">
                                    <i class="fas fa-key me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Logout (bottom of account page) -->
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <h5 class="card-title mb-3" style="font-weight: 700; color: #2c3e50;">
                            <i class="fas fa-right-from-bracket me-2" style="color: #dc3545;"></i>Logout
                        </h5>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-danger" style="font-weight: 700; border-radius: 10px; padding: 12px; border-width: 2px;">
                                    <i class="fas fa-sign-out-alt me-2"></i>Log out
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/ph-address.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const manualAddress = document.getElementById('profileAddressManual');
            const preview = document.getElementById('profileAddressPreview');

            const clearManual = () => {
                if (manualAddress) manualAddress.value = '';
            };

            // Force contact input to digits only and max length 11
            document.querySelector('input[name="contact"]')?.addEventListener('input', function() {
                const digitsOnly = this.value.replace(/\D/g, '').slice(0, 11);
                if (this.value !== digitsOnly) this.value = digitsOnly;
            });

            window.handleProfilePhotoChange = (input) => {
                const alertEl = document.getElementById('photoSizeAlert');
                const uploadAlert = document.getElementById('photoUploadAlert');
                if (alertEl) alertEl.style.display = 'none';
                if (uploadAlert) { uploadAlert.style.display = 'none'; uploadAlert.textContent = ''; }

                const file = input?.files?.[0];
                if (!file) return;

                // If the original file is huge, we can still crop it client-side,
                // but warn to avoid memory issues.
                const warnBytes = 25 * 1024 * 1024; // 25MB
                if (file.size > warnBytes && uploadAlert) {
                    uploadAlert.className = 'alert alert-warning';
                    uploadAlert.style.display = 'block';
                    uploadAlert.textContent = 'Large image selected. Cropping may take a moment.';
                }

                openCropperModal(file);
            };

            let cropper = null;
            let cropperObjectUrl = null;
            const cropperModalEl = document.getElementById('profilePhotoCropModal');
            const cropperImageEl = document.getElementById('cropperImage');
            const cropperUploadBtn = document.getElementById('cropperUpload');
            const cropperUploadText = document.getElementById('cropperUploadText');

            const applyCropperBackdrop = () => {
                // Bootstrap appends a backdrop div to <body> when a modal is shown.
                // Tag the most-recent backdrop so ONLY this modal gets a darker, solid backdrop.
                const backdrops = document.querySelectorAll('.modal-backdrop');
                const lastBackdrop = backdrops[backdrops.length - 1];
                lastBackdrop?.classList.add('cropper-backdrop');
            };

            const showUploadError = (message) => {
                const uploadAlert = document.getElementById('photoUploadAlert');
                if (!uploadAlert) return;
                uploadAlert.className = 'alert alert-danger';
                uploadAlert.style.display = 'block';
                uploadAlert.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + (message || 'The profile photo failed to upload.');
                uploadAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            };

            const openCropperModal = (file) => {
                if (!cropperModalEl || !cropperImageEl || !window.bootstrap) {
                    // Fallback: submit original file (may fail on 2MB servers)
                    document.getElementById('photoForm')?.submit();
                    return;
                }

                if (cropperObjectUrl) {
                    URL.revokeObjectURL(cropperObjectUrl);
                    cropperObjectUrl = null;
                }

                cropperObjectUrl = URL.createObjectURL(file);
                cropperImageEl.src = cropperObjectUrl;

                const modal = bootstrap.Modal.getOrCreateInstance(cropperModalEl, {
                    backdrop: 'static',
                    keyboard: false,
                });
                modal.show();

                // Ensure backdrop is applied after Bootstrap inserts it
                setTimeout(applyCropperBackdrop, 0);

                // Initialize Cropper after image loads
                cropperImageEl.onload = () => {
                    cropper?.destroy();
                    cropper = new Cropper(cropperImageEl, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        background: false,
                        responsive: true,
                        movable: true,
                        zoomable: true,
                        rotatable: true,
                        guides: false,
                        center: true,
                        highlight: false,
                    });
                };
            };

            // Cropper action buttons
            document.getElementById('cropperRotateLeft')?.addEventListener('click', () => cropper?.rotate(-90));
            document.getElementById('cropperZoomIn')?.addEventListener('click', () => cropper?.zoom(0.1));
            document.getElementById('cropperZoomOut')?.addEventListener('click', () => cropper?.zoom(-0.1));
            document.getElementById('cropperReset')?.addEventListener('click', () => cropper?.reset());

            // Cleanup when modal closes
            cropperModalEl?.addEventListener('hidden.bs.modal', () => {
                cropper?.destroy();
                cropper = null;
                if (cropperObjectUrl) {
                    URL.revokeObjectURL(cropperObjectUrl);
                    cropperObjectUrl = null;
                }
                document.querySelectorAll('.modal-backdrop.cropper-backdrop')
                    .forEach((el) => el.classList.remove('cropper-backdrop'));
                const input = document.getElementById('profilePhotoInput');
                if (input) input.value = '';
            });

            cropperModalEl?.addEventListener('shown.bs.modal', applyCropperBackdrop);

            cropperUploadBtn?.addEventListener('click', async () => {
                if (!cropper) return;

                const button = cropperUploadBtn;
                button.disabled = true;
                if (cropperUploadText) cropperUploadText.textContent = 'Uploading...';

                try {
                    const canvas = cropper.getCroppedCanvas({
                        width: 600,
                        height: 600,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    const blob = await new Promise((resolve) => {
                        canvas.toBlob((b) => resolve(b), 'image/jpeg', 0.9);
                    });

                    if (!blob) {
                        throw new Error('Unable to generate cropped image');
                    }

                    const maxBytes = 5 * 1024 * 1024; // 5MB
                    if (blob.size > maxBytes) {
                        document.getElementById('photoSizeAlert')?.style && (document.getElementById('photoSizeAlert').style.display = 'block');
                        showUploadError('Cropped image is still larger than 5MB. Please zoom out a bit or choose a smaller photo.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('profile_photo', new File([blob], 'profile.jpg', { type: 'image/jpeg' }));

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(@json(route('profile.photo')), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        const message = data?.message || data?.error || 'The profile photo failed to upload.';
                        showUploadError(message);
                        return;
                    }

                    // Update UI without full reload
                    if (data?.profile_photo_url) {
                        const display = document.getElementById('profilePhotoDisplay');
                        if (display) {
                            display.innerHTML = '<img id="profilePhotoPreview" src="' + data.profile_photo_url + '?v=' + Date.now() + '" alt="Profile" class="rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ffc107;">';
                        }
                    }

                    // Close modal
                    bootstrap.Modal.getInstance(cropperModalEl)?.hide();
                } catch (e) {
                    showUploadError('The profile photo failed to upload.');
                } finally {
                    button.disabled = false;
                    if (cropperUploadText) cropperUploadText.innerHTML = '<i class="fas fa-check me-1"></i>Use Photo';
                }
            });

            if (window.PHAddress && window.PHAddress.initSelector) {
                window.PHAddress.initSelector({
                    regionSelect: '#profileRegion',
                    provinceSelect: '#profileProvince',
                    citySelect: '#profileCity',
                    barangaySelect: '#profileBarangay',
                    streetInput: '#profileStreet',
                    postalInput: '#profilePostal',
                    previewTextarea: '#profileAddressPreview',
                    onAnyChange: clearManual,
                    initial: {
                        region: @json(Auth::user()->address_region_code),
                        province: @json(Auth::user()->address_province_code),
                        city: @json(Auth::user()->address_city_code),
                        barangay: @json(Auth::user()->address_barangay_code),
                    },
                });
            }

            // If user has no structured fields yet, keep preview as-is.
            if (preview && !preview.value) {
                preview.value = @json(Auth::user()->address);
            }
        });
    </script>
@endpush
