

<?php $__env->startSection('title', 'My Profile'); ?>

<?php $__env->startSection('content'); ?>
    <main class="container py-5 content-with-footer">
        <!-- Page Header -->
        <div class="mb-5">
            <h2 class="mb-2" style="font-weight: 700; font-size: 2rem; color: #2c3e50;">My Profile</h2>
            <p class="text-muted mb-0">Manage your account information and settings</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm text-center" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 32px;">
                        <form action="<?php echo e(route('profile.photo')); ?>" method="POST" enctype="multipart/form-data" id="photoForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="mb-3">
                                <?php if(Auth::user()->profile_photo): ?>
                                    <img src="<?php echo e(asset('storage/' . Auth::user()->profile_photo)); ?>" alt="Profile" class="rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ffc107;">
                                <?php else: ?>
                                    <i class="fas fa-user-circle" style="font-size: 5rem; color: #ffc107;"></i>
                                <?php endif; ?>
                            </div>
                            <h4 class="mb-1" style="font-weight: 700; color: #2c3e50;"><?php echo e(Auth::user()->first_name); ?> <?php echo e(Auth::user()->last_name); ?></h4>
                            <p class="text-muted mb-3"><?php echo e(Auth::user()->email); ?></p>
                            <div class="d-grid gap-2">
                                <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*" style="display: none;" onchange="document.getElementById('photoForm').submit();">
                                <button type="button" class="btn" onclick="document.getElementById('profilePhotoInput').click();" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #222; font-weight: 600; border-radius: 8px; padding: 10px;">
                                    <i class="fas fa-camera me-2"></i>Change Photo
                                </button>
                            </div>
                        </form>
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
                        <form action="<?php echo e(route('profile.update')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo e(Auth::user()->first_name); ?>" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" style="font-weight: 600; color: #495057;">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo e(Auth::user()->last_name); ?>" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #495057;">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo e(Auth::user()->email); ?>" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px;">
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
                        <form action="<?php echo e(route('profile.password')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            
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
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\E-Commerce-Laravel-main\resources\views/profile.blade.php ENDPATH**/ ?>