

<?php $__env->startSection('title', __('properties.add_new_property')); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* ========== Global Styles ========== */
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #818cf8;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .property-create-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* ========== Page Header ========== */
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.5s ease;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--gray-600);
            margin: 5px 0 0 0;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: var(--gray-100);
            color: var(--gray-700);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: var(--gray-200);
            transform: translateX(-5px);
        }

        /* ========== Progress Steps ========== */
        .progress-steps-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            position: relative;
            animation: slideUp 0.5s ease;
        }

        .progress-line {
            position: absolute;
            top: 60px;
            left: 10%;
            right: 10%;
            height: 4px;
            background: var(--gray-200);
            z-index: 1;
        }

        .progress-line::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            width: 0%;
            transition: width 0.5s ease;
        }

        .steps-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .step-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .step-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            border: 4px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.4s;
            box-shadow: var(--shadow-md);
        }

        .step-circle i {
            font-size: 24px;
            color: var(--gray-400);
            display: block;
        }

        .step-circle .step-number {
            display: none;
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        .step-item.active .step-circle {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            transform: scale(1.1);
        }

        .step-item.active .step-circle i {
            color: white;
        }

        .step-item.completed .step-circle {
            border-color: var(--success-color);
            background: var(--success-color);
        }

        .step-item.completed .step-circle i {
            display: none;
        }

        .step-item.completed .step-circle .step-number {
            display: block;
        }

        .step-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-500);
            transition: all 0.3s;
        }

        .step-item.active .step-label {
            color: var(--primary-color);
            font-size: 15px;
        }

        /* ========== Form Card ========== */
        .step-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .step-content.active {
            display: block;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .form-card-header {
            background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
            padding: 30px;
            border-bottom: 2px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .form-card-header .header-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .form-card-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .form-card-subtitle {
            font-size: 13px;
            color: var(--gray-600);
            margin: 5px 0 0 0;
        }

        .form-card-body {
            padding: 40px;
        }

        /* ========== Form Groups ========== */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 10px;
            display: block;
        }

        .required {
            color: var(--danger-color);
            margin-left: 3px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 16px;
            z-index: 2;
        }

        .input-with-icon .form-control,
        .input-with-icon .form-select {
            padding-left: 48px;
        }

        .form-control,
        .form-select {
            height: 50px;
            padding: 12px 16px;
            font-size: 14px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--danger-color);
        }

        textarea.form-control {
            height: auto;
            resize: vertical;
        }

        .form-hint {
            margin-top: 8px;
            font-size: 13px;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-hint i {
            color: var(--primary-color);
        }

        /* ========== Feature Toggles ========== */
        .feature-toggles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .toggle-item input[type="checkbox"] {
            display: none;
        }

        .toggle-item label {
            display: flex;
            align-items: start;
            gap: 12px;
            padding: 20px;
            background: var(--gray-50);
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        /* ========== Navigation ========== */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 24px 32px;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            gap: 16px;
        }

        .nav-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .btn-prev {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .btn-prev:hover {
            background: var(--gray-200);
            transform: translateX(-5px);
        }

        .btn-next {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .btn-next:hover {
            transform: translateX(5px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            display: none;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-draft {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: white;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-draft:hover {
            border-color: var(--gray-400);
            background: var(--gray-50);
        }

        /* ========== Toast Notifications ========== */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            min-width: 300px;
            padding: 16px 20px;
            margin-bottom: 12px;
            border-radius: 10px;
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInRight 0.3s ease;
        }

        .toast.success {
            background: var(--success-color);
            color: white;
        }

        .toast.warning {
            background: var(--warning-color);
            color: white;
        }

        .toast.error {
            background: var(--danger-color);
            color: white;
        }

        .toast.info {
            background: var(--primary-color);
            color: white;
        }

        /* ========== Animations ========== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* ========== Room Upload Cards ========== */
        .room-upload-card {
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f9fafb;
        }

        .room-upload-card:hover {
            border-color: #4f46e5;
            background: #f0f9ff;
        }

        .room-upload-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            color: #374151;
        }

        .room-upload-header i {
            color: #4f46e5;
            font-size: 18px;
        }

        .room-file-input {
            display: none;
        }

        .room-upload-area {
            padding: 20px;
            border-radius: 8px;
            background: white;
            border: 1px solid #e5e7eb;
        }

        .room-upload-area i {
            font-size: 24px;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .room-upload-area p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        /* ========== Responsive ========== */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 20px;
            }

            .steps-wrapper {
                overflow-x: auto;
            }

            .step-circle {
                width: 50px;
                height: 50px;
            }

            .step-circle i {
                font-size: 18px;
            }

            .step-label {
                font-size: 12px;
            }

            .form-card-body {
                padding: 24px;
            }

            .form-navigation {
                flex-wrap: wrap;
                gap: 12px;
            }

            .nav-center {
                width: 100%;
                order: 3;
            }

            .btn-draft {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <div class="container-fluid py-4 property-create-page">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <h1 class="page-title"><?php echo e(__('properties.add_new_property')); ?></h1>
                            <p class="page-subtitle"><?php echo e(__('properties.add_property_desc')); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('optimized.properties.index')); ?>" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span><?php echo e(__('properties.back_to_properties')); ?></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Property Form -->
        <form method="POST" action="<?php echo e(route('optimized.properties.store')); ?>" enctype="multipart/form-data"
            id="propertyForm">
            <?php echo csrf_field(); ?>

            <!-- Success/Error Messages -->
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong><?php echo e(__('common.fill_required_fields')); ?>:</strong>
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Progress Steps -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="progress-steps-container">
                        <div class="progress-line" id="progressLine"></div>
                        <div class="steps-wrapper">
                            <div class="step-item active" data-step="1">
                                <div class="step-circle">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="step-number">1</span>
                                </div>
                                <div class="step-label"><?php echo e(__('properties.basic_info')); ?></div>
                            </div>
                            <div class="step-item" data-step="2">
                                <div class="step-circle">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span class="step-number">2</span>
                                </div>
                                <div class="step-label"><?php echo e(__('properties.location')); ?></div>
                            </div>
                            <div class="step-item" data-step="3">
                                <div class="step-circle">
                                    <i class="fas fa-list-ul"></i>
                                    <span class="step-number">3</span>
                                </div>
                                <div class="step-label"><?php echo e(__('common.details')); ?></div>
                            </div>
                            <div class="step-item" data-step="4">
                                <div class="step-circle">
                                    <i class="fas fa-camera"></i>
                                    <span class="step-number">4</span>
                                </div>
                                <div class="step-label"><?php echo e(__('properties.media')); ?></div>
                            </div>
                            <div class="step-item" data-step="5">
                                <div class="step-circle">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span class="step-number">5</span>
                                </div>
                                <div class="step-label"><?php echo e(__('properties.pricing')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Basic Information -->
            <div class="step-content active" data-step="1">
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="header-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h3 class="form-card-title"><?php echo e(__('properties.basic_info')); ?></h3>
                            <p class="form-card-subtitle"> </p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_id" class="form-label"><?php echo e(__('properties.property_owner')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user"></i>
                                        <select class="form-select <?php $__errorArgs = ['owner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="owner_id"
                                            name="owner_id">
                                            <option value=""><?php echo e(__('properties.select_owner_optional')); ?></option>
                                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($client->id); ?>" <?php echo e(old('owner_id') == $client->id ? 'selected' : ''); ?>>
                                                    <?php echo e($client->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <?php $__errorArgs = ['owner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        <?php echo e(__('properties.property_title')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-heading"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="title" name="title" value="<?php echo e(old('title')); ?>"
                                            placeholder="<?php echo e(__('properties.title_placeholder')); ?>" required>
                                    </div>
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_type" class="form-label">
                                        <?php echo e(__('common.type')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-building"></i>
                                        <select class="form-select <?php $__errorArgs = ['property_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="property_type" name="property_type_id" required>
                                            <option value=""><?php echo e(__('properties.select_property_type')); ?></option>
                                            <?php $__currentLoopData = $propertyTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($type->id); ?>" <?php echo e(old('property_type_id') == $type->id ? 'selected' : ''); ?>>
                                                    <?php echo e($type->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <?php $__errorArgs = ['property_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="listing_type" class="form-label">
                                        <?php echo e(__('properties.listing_type')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-tag"></i>
                                        <select class="form-select <?php $__errorArgs = ['listing_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="listing_type" name="listing_type" required>
                                            <option value=""><?php echo e(__('properties.select_listing_type')); ?></option>
                                            <option value="sale" <?php echo e(old('listing_type') == 'sale' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.for_sale')); ?>

                                            </option>
                                            <option value="rent" <?php echo e(old('listing_type') == 'rent' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.for_rent')); ?>

                                            </option>
                                            <option value="lease" <?php echo e(old('listing_type') == 'lease' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.for_lease')); ?>

                                            </option>
                                        </select>
                                    </div>
                                    <?php $__errorArgs = ['listing_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">
                                        <?php echo e(__('common.status')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-toggle-on"></i>
                                        <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status"
                                            name="status" required>
                                            <option value="draft" <?php echo e(old('status') == 'draft' ? 'selected' : ''); ?>>
                                                <?php echo e(__('common.draft')); ?>

                                            </option>
                                            <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>
                                                <?php echo e(__('common.active')); ?>

                                            </option>
                                            <option value="inactive" <?php echo e(old('status') == 'inactive' ? 'selected' : ''); ?>>
                                                <?php echo e(__('common.inactive')); ?>

                                            </option>
                                        </select>
                                    </div>
                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">
                                        <?php echo e(__('properties.property_description')); ?> <span class="required">*</span>
                                    </label>
                                    <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="description" name="description" rows="6"
                                        placeholder="<?php echo e(__('properties.description_placeholder')); ?>"
                                        required><?php echo e(old('description')); ?></textarea>
                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <?php echo e(__('properties.description_hint')); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="feature-toggles">
                                    <div class="toggle-item">
                                        <input type="checkbox" id="featured" name="featured" value="1" <?php echo e(old('featured') ? 'checked' : ''); ?>>
                                        <label for="featured">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo e(__('properties.featured_property')); ?></span>
                                            <small><?php echo e(__('properties.featured_property_desc')); ?></small>
                                        </label>
                                    </div>
                                    <div class="toggle-item">
                                        <input type="checkbox" id="premium" name="premium" value="1" <?php echo e(old('premium') ? 'checked' : ''); ?>>
                                        <label for="premium">
                                            <i class="fas fa-crown"></i>
                                            <span><?php echo e(__('properties.premium_listing')); ?></span>
                                            <small><?php echo e(__('properties.premium_listing_desc')); ?></small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Location Information -->
            <div class="step-content" data-step="2">
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="header-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 class="form-card-title"><?php echo e(__('properties.location_info')); ?></h3>
                            <p class="form-card-subtitle"><?php echo e(__('properties.location_info_desc')); ?></p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="address" class="form-label">
                                        <?php echo e(__('properties.street_address')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map-pin"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="address" name="address" value="<?php echo e(old('address')); ?>"
                                            placeholder="<?php echo e(__('properties.location_placeholder')); ?>" required>
                                    </div>
                                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city" class="form-label">
                                        <?php echo e(__('common.city')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-city"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="city" name="city" value="<?php echo e(old('city')); ?>"
                                            placeholder="<?php echo e(__('properties.city_placeholder')); ?>" required>
                                    </div>
                                    <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state" class="form-label"><?php echo e(__('properties.state_province')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="state" name="state" value="<?php echo e(old('state')); ?>"
                                            placeholder="<?php echo e(__('properties.state_placeholder')); ?>">
                                    </div>
                                    <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country" class="form-label">
                                        <?php echo e(__('common.country')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-globe"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="country" name="country"
                                            value="<?php echo e(old('country') ?? __('common.saudi_arabia')); ?>" required>
                                    </div>
                                    <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postal_code" class="form-label"><?php echo e(__('properties.postal_code')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-mail-bulk"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="postal_code" name="postal_code" value="<?php echo e(old('postal_code')); ?>"
                                            placeholder="<?php echo e(__('properties.postal_code_placeholder')); ?>">
                                    </div>
                                    <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="neighborhood" class="form-label"><?php echo e(__('properties.neighborhood')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-home"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['neighborhood'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="neighborhood" name="neighborhood" value="<?php echo e(old('neighborhood')); ?>"
                                            placeholder="<?php echo e(__('properties.neighborhood_placeholder')); ?>">
                                    </div>
                                    <?php $__errorArgs = ['neighborhood'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="district" class="form-label"><?php echo e(__('properties.district')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-location-arrow"></i>
                                        <input type="text" class="form-control <?php $__errorArgs = ['district'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="district" name="district" value="<?php echo e(old('district')); ?>"
                                            placeholder="<?php echo e(__('properties.district_placeholder')); ?>">
                                    </div>
                                    <?php $__errorArgs = ['district'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude" class="form-label"><?php echo e(__('properties.latitude')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-compass"></i>
                                        <input type="number" step="any"
                                            class="form-control <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="latitude"
                                            name="latitude" value="<?php echo e(old('latitude')); ?>" placeholder="e.g., 24.7136">
                                    </div>
                                    <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude" class="form-label"><?php echo e(__('properties.longitude')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-location-crosshairs"></i>
                                        <input type="number" step="any"
                                            class="form-control <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="longitude"
                                            name="longitude" value="<?php echo e(old('longitude')); ?>" placeholder="e.g., 46.6753">
                                    </div>
                                    <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-primary w-100 mb-3"
                                        onclick="getCurrentLocation()">
                                        <i
                                            class="fas fa-map-marker-alt me-2"></i><?php echo e(__('properties.get_current_location')); ?>

                                    </button>
                                    <button type="button" class="btn btn-primary w-100" id="verifyAddressBtn"
                                        onclick="verifyAddress()">
                                        <i class="fas fa-search-location me-2"></i><?php echo e(__('properties.verify_address')); ?>

                                    </button>
                                </div>

                                <!-- Address Verification Status -->
                                <div id="addressVerification" class="mt-3" style="display: none;">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <div class="spinner-border spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div><?php echo e(__('properties.verifying_address')); ?></div>
                                    </div>
                                </div>
                                <div id="addressResult" class="mt-3" style="display: none;">
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong><?php echo e(__('properties.found_address')); ?>:</strong>
                                        <span id="foundAddress"></span>
                                    </div>
                                </div>
                                <div id="addressError" class="mt-3" style="display: none;">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <?php echo e(__('properties.coordinates_not_found')); ?>

                                    </div>
                                </div>
                                <div id="addressRequiredError" class="mt-3" style="display: none;">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?php echo e(__('properties.address_city_required')); ?>

                                    </div>
                                </div>
                                <div id="locationError" class="mt-3" style="display: none;">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <span id="locationErrorMessage"><?php echo e(__('properties.location_error')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Property Details -->
            <div class="step-content" data-step="3">
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="header-icon">
                            <i class="fas fa-list-ul"></i>
                        </div>
                        <div>
                            <h3 class="form-card-title"><?php echo e(__('common.details')); ?></h3>
                            <p class="form-card-subtitle"><?php echo e(__('properties.property_details')); ?></p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bedrooms" class="form-label"><?php echo e(__('properties.bedrooms')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-bed"></i>
                                        <input type="number" class="form-control <?php $__errorArgs = ['bedrooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="bedrooms" name="bedrooms" value="<?php echo e(old('bedrooms')); ?>" min="0"
                                            placeholder="0">
                                    </div>
                                    <?php $__errorArgs = ['bedrooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bathrooms" class="form-label"><?php echo e(__('properties.bathrooms')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-bath"></i>
                                        <input type="number" class="form-control <?php $__errorArgs = ['bathrooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="bathrooms" name="bathrooms" value="<?php echo e(old('bathrooms')); ?>" min="0"
                                            placeholder="0">
                                    </div>
                                    <?php $__errorArgs = ['bathrooms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="floors" class="form-label"><?php echo e(__('properties.floors')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-layer-group"></i>
                                        <input type="number" class="form-control <?php $__errorArgs = ['floors'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="floors" name="floors" value="<?php echo e(old('floors')); ?>" min="0" placeholder="0">
                                    </div>
                                    <?php $__errorArgs = ['floors'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="parking_spaces" class="form-label"><?php echo e(__('properties.parking_spaces')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-car"></i>
                                        <input type="number" class="form-control <?php $__errorArgs = ['parking_spaces'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="parking_spaces" name="parking_spaces" value="<?php echo e(old('parking_spaces')); ?>" min="0"
                                            placeholder="0">
                                    </div>
                                    <?php $__errorArgs = ['parking_spaces'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="year_built" class="form-label"><?php echo e(__('properties.year_built')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                        <input type="number" class="form-control <?php $__errorArgs = ['year_built'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="year_built" name="year_built" value="<?php echo e(old('year_built')); ?>" min="1800"
                                            max="<?php echo e(date('Y')); ?>" placeholder="<?php echo e(date('Y')); ?>">
                                    </div>
                                    <?php $__errorArgs = ['year_built'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="area" class="form-label">
                                        <?php echo e(__('properties.living_area')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-ruler-combined"></i>
                                        <input type="number" class="form-control <?php $__errorArgs = ['area'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="area" name="area" value="<?php echo e(old('area')); ?>" required min="1"
                                            placeholder="100">
                                    </div>
                                    <?php $__errorArgs = ['area'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="area_unit" class="form-label">
                                        <?php echo e(__('properties.area_unit')); ?> <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-ruler"></i>
                                        <select class="form-select <?php $__errorArgs = ['area_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="area_unit" name="area_unit" required>
                                            <option value="sq_m" <?php echo e(old('area_unit') == 'sq_m' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.sqm')); ?>

                                            </option>
                                            <option value="sq_ft" <?php echo e(old('area_unit') == 'sq_ft' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.sqft')); ?>

                                            </option>
                                        </select>
                                    </div>
                                    <?php $__errorArgs = ['area_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="land_area" class="form-label"><?php echo e(__('properties.land_area')); ?>

                                        (<?php echo e(__('properties.sqm')); ?>)</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-ruler"></i>
                                        <input type="number" class="form-control <?php $__errorArgs = ['land_area'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="land_area" name="land_area" value="<?php echo e(old('land_area')); ?>" min="0"
                                            placeholder="0">
                                    </div>
                                    <?php $__errorArgs = ['land_area'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="land_area_unit" class="form-label"><?php echo e(__('properties.land_area_unit')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-ruler"></i>
                                        <select class="form-select <?php $__errorArgs = ['land_area_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="land_area_unit" name="land_area_unit">
                                            <option value="sq_m" <?php echo e(old('land_area_unit') == 'sq_m' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.sqm')); ?>

                                            </option>
                                            <option value="sq_ft" <?php echo e(old('land_area_unit') == 'sq_ft' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.sqft')); ?>

                                            </option>
                                            <option value="acre" <?php echo e(old('land_area_unit') == 'acre' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.acre')); ?>

                                            </option>
                                            <option value="hectare" <?php echo e(old('land_area_unit') == 'hectare' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.hectare')); ?>

                                            </option>
                                        </select>
                                    </div>
                                    <?php $__errorArgs = ['land_area_unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="finishing_type"
                                        class="form-label"><?php echo e(__('properties.finishing_type')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-paint-roller"></i>
                                        <select class="form-select <?php $__errorArgs = ['finishing_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="finishing_type" name="finishing_type">
                                            <option value=""><?php echo e(__('common.select')); ?></option>
                                            <option value="super_lux" <?php echo e(old('finishing_type') == 'super_lux' ? 'selected' : ''); ?>><?php echo e(__('properties.super_lux')); ?></option>
                                            <option value="lux" <?php echo e(old('finishing_type') == 'lux' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.lux')); ?>

                                            </option>
                                            <option value="semi_finished" <?php echo e(old('finishing_type') == 'semi_finished' ? 'selected' : ''); ?>><?php echo e(__('properties.semi_finished')); ?></option>
                                            <option value="core_shell" <?php echo e(old('finishing_type') == 'core_shell' ? 'selected' : ''); ?>><?php echo e(__('properties.core_shell')); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="interface" class="form-label"><?php echo e(__('properties.interface')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-compass"></i>
                                        <select class="form-select <?php $__errorArgs = ['interface'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="interface"
                                            name="interface">
                                            <option value=""><?php echo e(__('common.select')); ?></option>
                                            <option value="north" <?php echo e(old('interface') == 'north' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.north')); ?>

                                            </option>
                                            <option value="south" <?php echo e(old('interface') == 'south' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.south')); ?>

                                            </option>
                                            <option value="east" <?php echo e(old('interface') == 'east' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.east')); ?>

                                            </option>
                                            <option value="west" <?php echo e(old('interface') == 'west' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.west')); ?>

                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="furnished"
                                        class="form-label"><?php echo e(__('properties.furnished_question')); ?></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-couch"></i>
                                        <select class="form-select <?php $__errorArgs = ['furnished'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="furnished"
                                            name="furnished">
                                            <option value="no" <?php echo e(old('furnished') == 'no' ? 'selected' : ''); ?>>
                                                <?php echo e(__('common.no')); ?>

                                            </option>
                                            <option value="yes" <?php echo e(old('furnished') == 'yes' ? 'selected' : ''); ?>>
                                                <?php echo e(__('common.yes')); ?>

                                            </option>
                                            <option value="semi" <?php echo e(old('furnished') == 'semi' ? 'selected' : ''); ?>>
                                                <?php echo e(__('properties.furnished_semi')); ?>

                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Features -->
            <div class="form-card mt-4">
                <div class="form-card-header">
                    <div class="header-icon">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <h4 class="detail-section-title"><?php echo e(__('properties.detailed_features')); ?></h4>
                    <p class="text-muted small mb-3"><?php echo e(__('properties.detailed_features_desc')); ?></p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="materials" class="form-label"><?php echo e(__('properties.building_materials')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-cubes"></i>
                                <textarea class="form-control" id="materials" name="materials" rows="2"
                                    placeholder="<?php echo e(__('properties.materials_placeholder')); ?>"><?php echo e(old('materials')); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="interior_features"
                                class="form-label"><?php echo e(__('properties.interior_features')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-couch"></i>
                                <textarea class="form-control" id="interior_features" name="interior_features" rows="3"
                                    placeholder="<?php echo e(__('properties.interior_placeholder')); ?>"><?php echo e(old('interior_features')); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exterior_features"
                                class="form-label"><?php echo e(__('properties.exterior_features')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-tree"></i>
                                <textarea class="form-control" id="exterior_features" name="exterior_features" rows="3"
                                    placeholder="<?php echo e(__('properties.exterior_placeholder')); ?>"><?php echo e(old('exterior_features')); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Nearby Places -->
    <div class="form-card mt-4">
        <div class="form-card-header">
            <div class="header-icon">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <h4 class="detail-section-title"><?php echo e(__('properties.nearby_places')); ?></h4>
            <p class="text-muted small mb-3"><?php echo e(__('properties.nearby_places_desc')); ?></p>
        </div>
    </div>
    <div class="form-card-body">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="schools" class="form-label"><?php echo e(__('properties.schools')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-school"></i>
                        <input type="text" class="form-control" id="schools" name="schools" value="<?php echo e(old('schools')); ?>"
                            placeholder="e.g., King Saud University (2km)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="hospitals" class="form-label"><?php echo e(__('properties.hospitals')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-hospital"></i>
                        <input type="text" class="form-control" id="hospitals" name="hospitals"
                            value="<?php echo e(old('hospitals')); ?>" placeholder="e.g., Kingdom Hospital (5km)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="shopping_centers" class="form-label"><?php echo e(__('properties.shopping_centers')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <input type="text" class="form-control" id="shopping_centers" name="shopping_centers"
                            value="<?php echo e(old('shopping_centers')); ?>" placeholder="e.g., Riyadh Park (10 mins)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="restaurants" class="form-label"><?php echo e(__('properties.restaurants')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-utensils"></i>
                        <input type="text" class="form-control" id="restaurants" name="restaurants"
                            value="<?php echo e(old('restaurants')); ?>" placeholder="e.g., Various options nearby">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="transportation" class="form-label"><?php echo e(__('properties.transportation')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-bus"></i>
                        <input type="text" class="form-control" id="transportation" name="transportation"
                            value="<?php echo e(old('transportation')); ?>" placeholder="e.g., Metro Station (500m)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="universities" class="form-label"><?php echo e(__('properties.universities')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-graduation-cap"></i>
                        <input type="text" class="form-control" id="universities" name="universities"
                            value="<?php echo e(old('universities')); ?>" placeholder="e.g., King Saud University (5km)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="metro_stations" class="form-label"><?php echo e(__('properties.metro_stations')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-subway"></i>
                        <input type="text" class="form-control" id="metro_stations" name="metro_stations"
                            value="<?php echo e(old('metro_stations')); ?>" placeholder="e.g., Olaya Station (500m)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cinemas" class="form-label"><?php echo e(__('properties.cinemas')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-film"></i>
                        <input type="text" class="form-control" id="cinemas" name="cinemas" value="<?php echo e(old('cinemas')); ?>"
                            placeholder="e.g., VOX Cinemas (2km)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="parks" class="form-label"><?php echo e(__('properties.parks')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-tree"></i>
                        <input type="text" class="form-control" id="parks" name="parks" value="<?php echo e(old('parks')); ?>"
                            placeholder="e.g., King Abdullah Park (1km)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="gyms" class="form-label"><?php echo e(__('properties.gyms')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-dumbbell"></i>
                        <input type="text" class="form-control" id="gyms" name="gyms" value="<?php echo e(old('gyms')); ?>"
                            placeholder="e.g., Fitness Time (300m)">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nearby_others" class="form-label"><?php echo e(__('properties.other_places')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-map-signs"></i>
                        <input type="text" class="form-control" id="nearby_others" name="nearby_others"
                            value="<?php echo e(old('nearby_others')); ?>"
                            placeholder="<?php echo e(__('properties.other_places_placeholder')); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Legal & Energy Details -->
    <div class="form-card mt-4">
        <div class="form-card-header">
            <div class="header-icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <div>
                <h3 class="form-card-title"><?php echo e(__('properties.legal_energy_details')); ?></h3>
                <p class="form-card-subtitle"><?php echo e(__('properties.legal_energy_details_desc')); ?></p>
            </div>
        </div>
        <div class="form-card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ownership_type" class="form-label"><?php echo e(__('properties.ownership_type')); ?></label>
                        <div class="input-with-icon">
                            <i class="fas fa-file-signature"></i>
                            <select class="form-select" id="ownership_type" name="ownership_type">
                                <option value=""><?php echo e(__('properties.select_type')); ?></option>
                                <option value="freehold" <?php echo e(old('ownership_type') == 'freehold' ? 'selected' : ''); ?>>
                                    <?php echo e(__('properties.freehold')); ?>

                                </option>
                                <option value="leasehold" <?php echo e(old('ownership_type') == 'leasehold' ? 'selected' : ''); ?>>
                                    <?php echo e(__('properties.leasehold')); ?>

                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="deed_number" class="form-label"><?php echo e(__('properties.deed_number')); ?></label>
                        <div class="input-with-icon">
                            <i class="fas fa-certificate"></i>
                            <input type="text" class="form-control" id="deed_number" name="deed_number"
                                value="<?php echo e(old('deed_number')); ?>" placeholder="<?php echo e(__('properties.deed_number')); ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="registration_number"
                            class="form-label"><?php echo e(__('properties.registration_number')); ?></label>
                        <div class="input-with-icon">
                            <i class="fas fa-registered"></i>
                            <input type="text" class="form-control" id="registration_number" name="registration_number"
                                value="<?php echo e(old('registration_number')); ?>"
                                placeholder="<?php echo e(__('properties.registration_number')); ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="zoning" class="form-label"><?php echo e(__('properties.zoning')); ?></label>
                        <div class="input-with-icon">
                            <i class="fas fa-draw-polygon"></i>
                            <input type="text" class="form-control" id="zoning" name="zoning" value="<?php echo e(old('zoning')); ?>"
                                placeholder="e.g., Residential R1">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="energy_rating" class="form-label"><?php echo e(__('properties.energy_rating')); ?></label>
                        <div class="input-with-icon">
                            <i class="fas fa-leaf"></i>
                            <select class="form-select" id="energy_rating" name="energy_rating">
                                <option value=""><?php echo e(__('properties.select_rating')); ?></option>
                                <option value="A+" <?php echo e(old('energy_rating') == 'A+' ? 'selected' : ''); ?>>A+</option>
                                <option value="A" <?php echo e(old('energy_rating') == 'A' ? 'selected' : ''); ?>>A</option>
                                <option value="B" <?php echo e(old('energy_rating') == 'B' ? 'selected' : ''); ?>>B</option>
                                <option value="C" <?php echo e(old('energy_rating') == 'C' ? 'selected' : ''); ?>>C</option>
                                <option value="D" <?php echo e(old('energy_rating') == 'D' ? 'selected' : ''); ?>>D</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <h6 class="mb-3"><?php echo e(__('properties.additional_features')); ?></h6>
                    <div class="feature-toggles">
                        <div class="toggle-item">
                            <input type="checkbox" id="solar_panels" name="solar_panels" value="1" <?php echo e(old('solar_panels') ? 'checked' : ''); ?>>
                            <label for="solar_panels">
                                <i class="fas fa-solar-panel"></i>
                                <span><?php echo e(__('properties.solar_panels')); ?></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <input type="checkbox" id="water_heating" name="water_heating" value="1" <?php echo e(old('water_heating') ? 'checked' : ''); ?>>
                            <label for="water_heating">
                                <i class="fas fa-water"></i>
                                <span><?php echo e(__('properties.water_heating')); ?></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <input type="checkbox" id="insulation" name="insulation" value="1" <?php echo e(old('insulation') ? 'checked' : ''); ?>>
                            <label for="insulation">
                                <i class="fas fa-temperature-low"></i>
                                <span><?php echo e(__('properties.insulation')); ?></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <input type="checkbox" id="double_glazing" name="double_glazing" value="1" <?php echo e(old('double_glazing') ? 'checked' : ''); ?>>
                            <label for="double_glazing">
                                <i class="fas fa-window-maximize"></i>
                                <span><?php echo e(__('properties.double_glazing')); ?></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <input type="checkbox" id="air_conditioning" name="air_conditioning" value="1" <?php echo e(old('air_conditioning') ? 'checked' : ''); ?>>
                            <label for="air_conditioning">
                                <i class="fas fa-snowflake"></i>
                                <span><?php echo e(__('properties.air_conditioning')); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Amenities and Features -->
    <div class="form-card mt-4">
        <div class="form-card-header">
            <div class="header-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h3 class="form-card-title"><?php echo e(__('properties.amenities_features')); ?></h3>
                <p class="form-card-subtitle"><?php echo e(__('properties.amenities_features_desc')); ?></p>
            </div>
        </div>
        <div class="form-card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="amenities-section-title">
                        <i class="fas fa-star"></i>
                        <?php echo e(__('properties.amenities')); ?>

                    </h6>
                    <div class="amenities-grid">
                        <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="<?php echo e($amenity->id); ?>" <?php echo e(in_array($amenity->id, old('amenities', [])) ? 'checked' : ''); ?>>
                                <span class="amenity-label">
                                    <?php if($amenity->icon): ?>
                                        <i class="<?php echo e($amenity->icon); ?>"></i>
                                    <?php endif; ?>
                                    <?php echo e($amenity->name); ?>

                                </span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="amenities-section-title">
                        <i class="fas fa-gem"></i>
                        <?php echo e(__('properties.special_features')); ?>

                    </h6>
                    <div class="amenities-grid">
                        <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="features[]" value="<?php echo e($feature->id); ?>" <?php echo e(in_array($feature->id, old('features', [])) ? 'checked' : ''); ?>>
                                <span class="amenity-label">
                                    <?php if($feature->icon): ?>
                                        <i class="<?php echo e($feature->icon); ?>"></i>
                                    <?php endif; ?>
                                    <?php echo e($feature->name); ?>

                                    <?php if($feature->is_premium): ?>
                                        <span class="premium-badge"><?php echo e(__('common.premium')); ?></span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Step 4: Media Upload -->
    <div class="step-content" data-step="4">
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <div>
                    <h3 class="form-card-title"><?php echo e(__('properties.property_images')); ?></h3>
                    <p class="form-card-subtitle"><?php echo e(__('properties.upload_images_desc')); ?></p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="upload-area" id="uploadArea">
                            <input type="file" class="file-input" id="images" name="images[]" multiple accept="image/*">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h4><?php echo e(__('properties.drag_drop_images')); ?></h4>
                                <p><?php echo e(__('properties.click_to_browse')); ?></p>
                                <span class="upload-hint"><?php echo e(__('properties.image_spec')); ?></span>
                            </div>
                        </div>
                        <div id="imagePreview" class="image-preview-grid"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <div class="info-box-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h6 class="info-box-title"><?php echo e(__('properties.photo_guidelines')); ?></h6>
                            <ul class="info-box-list">
                                <li><i class="fas fa-check"></i><?php echo e(__('properties.photo_guideline_1')); ?></li>
                                <li><i class="fas fa-check"></i><?php echo e(__('properties.photo_guideline_2')); ?></li>
                                <li><i class="fas fa-check"></i><?php echo e(__('properties.photo_guideline_3')); ?></li>
                                <li><i class="fas fa-check"></i><?php echo e(__('properties.photo_guideline_4')); ?></li>
                                <li><i class="fas fa-check"></i><?php echo e(__('properties.photo_guideline_5')); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><?php echo e(__('properties.property_video')); ?></h5>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label"><?php echo e(__('properties.video_url')); ?></label>
                            <div class="input-with-icon">
                                <i class="fab fa-youtube"></i>
                                <input type="url" class="form-control" name="video_url"
                                    placeholder="https://youtube.com/..." value="<?php echo e(old('video_url')); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room-specific Image Uploads -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><?php echo e(__('properties.room_specific_photos')); ?></h5>
                    </div>

                    <!-- Living Room -->
                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('living_room')">
                            <div class="room-upload-header">
                                <i class="fas fa-couch"></i>
                                <span><?php echo e(__('properties.living_room')); ?></span>
                            </div>
                            <input type="file" name="room_images[living_room][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_living_room_photos')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('kitchen')">
                            <div class="room-upload-header">
                                <i class="fas fa-utensils"></i>
                                <span><?php echo e(__('properties.kitchen')); ?></span>
                            </div>
                            <input type="file" name="room_images[kitchen][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_kitchen_photos')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('bedrooms')">
                            <div class="room-upload-header">
                                <i class="fas fa-bed"></i>
                                <span><?php echo e(__('properties.bedrooms')); ?></span>
                            </div>
                            <input type="file" name="room_images[bedrooms][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_bedroom_photos')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('bathrooms')">
                            <div class="room-upload-header">
                                <i class="fas fa-bath"></i>
                                <span><?php echo e(__('properties.bathrooms')); ?></span>
                            </div>
                            <input type="file" name="room_images[bathrooms][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_bathroom_photos')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('entrance')">
                            <div class="room-upload-header">
                                <i class="fas fa-door-open"></i>
                                <span><?php echo e(__('properties.main_entrance')); ?></span>
                            </div>
                            <input type="file" name="room_images[entrance][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_entrance_photos')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('outdoor')">
                            <div class="room-upload-header">
                                <i class="fas fa-tree"></i>
                                <span><?php echo e(__('properties.garden_outdoor')); ?></span>
                            </div>
                            <input type="file" name="room_images[outdoor][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_outdoor_photos')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('garage')">
                            <div class="room-upload-header">
                                <i class="fas fa-car"></i>
                                <span><?php echo e(__('properties.garage_parking')); ?></span>
                            </div>
                            <input type="file" name="room_images[garage][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_garage_photos')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="room-upload-card" onclick="openRoomFileInput('amenities')">
                            <div class="room-upload-header">
                                <i class="fas fa-swimming-pool"></i>
                                <span><?php echo e(__('properties.pool_amenities')); ?></span>
                            </div>
                            <input type="file" name="room_images[amenities][]" multiple accept="image/*"
                                class="room-file-input">
                            <div class="room-upload-area">
                                <i class="fas fa-camera"></i>
                                <p><?php echo e(__('properties.add_amenities_photos')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card mt-4">
            <div class="form-card-header">
                <div class="header-icon">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <h3 class="form-card-title"><?php echo e(__('properties.virtual_tour')); ?></h3>
                    <p class="form-card-subtitle"><?php echo e(__('properties.virtual_tour_desc')); ?></p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="form-group">
                    <label for="virtual_tour_url" class="form-label"><?php echo e(__('properties.virtual_tour_url')); ?></label>
                    <div class="input-with-icon">
                        <i class="fas fa-link"></i>
                        <input type="url" class="form-control <?php $__errorArgs = ['virtual_tour_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="virtual_tour_url" name="virtual_tour_url" value="<?php echo e(old('virtual_tour_url')); ?>"
                            placeholder="https://example.com/virtual-tour">
                    </div>
                    <?php $__errorArgs = ['virtual_tour_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <?php echo e(__('properties.virtual_tour_hint')); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 5: Pricing -->
    <div class="step-content" data-step="5">
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <h3 class="form-card-title"><?php echo e(__('properties.pricing_information')); ?></h3>
                    <p class="form-card-subtitle"><?php echo e(__('properties.pricing_desc')); ?></p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price" class="form-label">
                                <?php echo e(__('properties.price')); ?> <span class="required">*</span>
                            </label>
                            <div class="price-input-group">
                                <select class="currency-select <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="currency"
                                    name="currency" required>
                                    <option value="SAR" <?php echo e(old('currency') == 'SAR' ? 'selected' : ''); ?>>SAR</option>
                                    <option value="USD" <?php echo e(old('currency') == 'USD' ? 'selected' : ''); ?>>USD</option>
                                    <option value="EUR" <?php echo e(old('currency') == 'EUR' ? 'selected' : ''); ?>>EUR</option>
                                    <option value="GBP" <?php echo e(old('currency') == 'GBP' ? 'selected' : ''); ?>>GBP</option>
                                    <option value="AED" <?php echo e(old('currency') == 'AED' ? 'selected' : ''); ?>>AED</option>
                                </select>
                                <input type="number" class="form-control price-input <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="price" name="price" value="<?php echo e(old('price')); ?>" required min="0" step="0.01"
                                    placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="original_price" class="form-label"><?php echo e(__('properties.original_price')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-tag"></i>
                                <input type="number" class="form-control <?php $__errorArgs = ['original_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="original_price" name="original_price" value="<?php echo e(old('original_price')); ?>" min="0"
                                    step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-hint"><?php echo e(__('properties.original_price_hint')); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_frequency"
                                class="form-label"><?php echo e(__('properties.payment_frequency')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-check"></i>
                                <select class="form-select <?php $__errorArgs = ['payment_frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="payment_frequency" name="payment_frequency">
                                    <option value=""><?php echo e(__('properties.select_frequency')); ?></option>
                                    <option value="monthly" <?php echo e(old('payment_frequency') == 'monthly' ? 'selected' : ''); ?>>
                                        <?php echo e(__('properties.monthly')); ?>

                                    </option>
                                    <option value="quarterly" <?php echo e(old('payment_frequency') == 'quarterly' ? 'selected' : ''); ?>>
                                        <?php echo e(__('properties.quarterly')); ?>

                                    </option>
                                    <option value="annually" <?php echo e(old('payment_frequency') == 'annually' ? 'selected' : ''); ?>>
                                        <?php echo e(__('properties.annually')); ?>

                                    </option>
                                </select>
                            </div>
                            <?php $__errorArgs = ['payment_frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rent_period" class="form-label"><?php echo e(__('properties.rent_period')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-clock"></i>
                                <select class="form-select <?php $__errorArgs = ['rent_period'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="rent_period"
                                    name="rent_period">
                                    <option value=""><?php echo e(__('properties.select_period')); ?></option>
                                    <option value="Daily" <?php echo e(old('rent_period') == 'Daily' ? 'selected' : ''); ?>>
                                        <?php echo e(__('properties.daily')); ?>

                                    </option>
                                    <option value="Weekly" <?php echo e(old('rent_period') == 'Weekly' ? 'selected' : ''); ?>>
                                        <?php echo e(__('properties.weekly')); ?>

                                    </option>
                                    <option value="Monthly" <?php echo e(old('rent_period') == 'Monthly' ? 'selected' : ''); ?>>
                                        <?php echo e(__('properties.monthly')); ?>

                                    </option>
                                    <option value="Yearly" <?php echo e(old('rent_period') == 'Yearly' ? 'selected' : ''); ?>>
                                        <?php echo e(__('properties.yearly')); ?>

                                    </option>
                                </select>
                            </div>
                            <?php $__errorArgs = ['rent_period'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="price-options">
                            <label class="option-checkbox">
                                <input type="checkbox" id="is_negotiable" name="is_negotiable" value="1" <?php echo e(old('is_negotiable') ? 'checked' : ''); ?>>
                                <span class="option-label">
                                    <i class="fas fa-handshake"></i>
                                    <span><?php echo e(__('properties.price_is_negotiable')); ?></span>
                                </span>
                            </label>
                            <label class="option-checkbox">
                                <input type="checkbox" id="includes_vat" name="includes_vat" value="1" <?php echo e(old('includes_vat') ? 'checked' : ''); ?>>
                                <span class="option-label">
                                    <i class="fas fa-receipt"></i>
                                    <span><?php echo e(__('properties.price_includes_vat')); ?></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="service_charges" class="form-label"><?php echo e(__('properties.service_charges')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-coins"></i>
                                <input type="number" class="form-control <?php $__errorArgs = ['service_charges'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="service_charges" name="service_charges" value="<?php echo e(old('service_charges')); ?>" min="0"
                                    step="0.01" placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['service_charges'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="maintenance_fees" class="form-label"><?php echo e(__('properties.maintenance_fees')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-tools"></i>
                                <input type="number" class="form-control <?php $__errorArgs = ['maintenance_fees'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="maintenance_fees" name="maintenance_fees" value="<?php echo e(old('maintenance_fees')); ?>"
                                    min="0" step="0.01" placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['maintenance_fees'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="property_tax" class="form-label"><?php echo e(__('properties.property_tax')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <input type="number" class="form-control <?php $__errorArgs = ['property_tax'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="property_tax" name="property_tax" value="<?php echo e(old('property_tax')); ?>" min="0"
                                    step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tax_year" class="form-label"><?php echo e(__('properties.tax_year')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar"></i>
                                <input type="number" class="form-control <?php $__errorArgs = ['tax_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="tax_year" name="tax_year" value="<?php echo e(old('tax_year')); ?>" min="2000"
                                    max="<?php echo e(date('Y') + 1); ?>" placeholder="<?php echo e(date('Y')); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="insurance_cost" class="form-label"><?php echo e(__('properties.insurance_cost')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-shield-alt"></i>
                                <input type="number" class="form-control <?php $__errorArgs = ['insurance_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="insurance_cost" name="insurance_cost" value="<?php echo e(old('insurance_cost')); ?>" min="0"
                                    step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates & Availability -->
        <div class="form-card mt-4">
            <div class="form-card-header">
                <div class="header-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h3 class="form-card-title"><?php echo e(__('properties.availability_dates')); ?></h3>
                    <p class="form-card-subtitle"><?php echo e(__('properties.availability_dates_desc')); ?></p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rental_start_date" class="form-label"><?php echo e(__('properties.available_from')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-plus"></i>
                                <input type="date" class="form-control <?php $__errorArgs = ['rental_start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="rental_start_date" name="rental_start_date" value="<?php echo e(old('rental_start_date')); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rental_end_date" class="form-label"><?php echo e(__('properties.available_until')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-minus"></i>
                                <input type="date" class="form-control <?php $__errorArgs = ['rental_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="rental_end_date" name="rental_end_date" value="<?php echo e(old('rental_end_date')); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expiry_date" class="form-label"><?php echo e(__('properties.listing_expiry_date')); ?></label>
                            <div class="input-with-icon">
                                <i class="fas fa-hourglass-end"></i>
                                <input type="date" class="form-control <?php $__errorArgs = ['expiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="expiry_date" name="expiry_date" value="<?php echo e(old('expiry_date')); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="form-navigation">
                <button type="button" class="btn-nav btn-prev" id="prevBtn">
                    <i class="fas fa-arrow-left"></i>
                    <span><?php echo e(__('common.previous')); ?></span>
                </button>
                <div class="nav-center">
                    <button type="button" class="btn-draft" onclick="saveDraft()">
                        <i class="fas fa-save"></i>
                        <span>Save Draft</span>
                    </button>
                </div>
                <button type="button" class="btn-nav btn-next" id="nextBtn">
                    <span><?php echo e(__('common.next')); ?></span>
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn-nav btn-submit" id="submitBtn" style="display: none;">
                    <i class="fas fa-check"></i>
                    <span><?php echo e(__('common.finish')); ?></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden fields to ensure data is always sent -->
    <input type="hidden" name="hidden_field_1" value="value_1">
    <input type="hidden" name="hidden_field_2" value="value_2">
    <!-- Inputs hidden by CSS tabs are still submitted by the browser. -->
    </form>
    </div>

    <!-- Toast Notification -->
    <div id="toastContainer" class="toast-container"></div>

    <script>
        // Prevent global pollution - wrap everything
        (function () {
            'use strict';

            // Global state
            let currentStep = 1;
            const totalSteps = 5;

            // Update progress line
            function updateProgressLine() {
                const progressLine = document.getElementById('progressLine');
                if (progressLine) {
                    const percentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
                    const lineAfter = progressLine.querySelector('::after') || progressLine;
                    if (lineAfter.style) {
                        lineAfter.style.setProperty('--progress-width', percentage + '%');
                    }
                    // Fallback for ::after
                    progressLine.style.background = `linear-gradient(90deg, var(--primary-color) ${percentage}%, var(--gray-200) ${percentage}%)`;
                }
            }

            // Handle next click
            window.handleNextClick = function () {
                console.log('Next button clicked directly!');
                console.log('Current step:', currentStep);

                const isValid = validateCurrentStep();
                console.log('Validation result:', isValid);

                if (isValid) {
                    console.log('Moving to next step...');
                    changeStep(1);
                } else {
                    console.log('Validation failed, staying on current step');
                    showToast("<?php echo e(__('common.fill_required_fields')); ?>", 'warning');
                }
            };

            // Change step function
            function changeStep(direction) {
                console.log('Changing step:', currentStep, 'Direction:', direction);

                // Validate before moving forward
                if (direction > 0 && !validateStep(currentStep)) {
                    return;
                }

                // Hide current step
                const currentStepContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
                const currentStepIndicator = document.querySelector(`.step-item[data-step="${currentStep}"]`);

                if (currentStepContent) {
                    currentStepContent.classList.remove('active');
                }
                if (currentStepIndicator) {
                    currentStepIndicator.classList.remove('active');
                    if (direction > 0) {
                        currentStepIndicator.classList.add('completed');
                    }
                }

                // Update step
                currentStep += direction;
                console.log('New step:', currentStep);

                // Show new step
                const newStepContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
                const newStepIndicator = document.querySelector(`.step-item[data-step="${currentStep}"]`);

                if (newStepContent) {
                    newStepContent.classList.add('active');
                }
                if (newStepIndicator) {
                    newStepIndicator.classList.add('active');
                }

                // Update navigation
                updateNavigationButtons();
                updateProgressLine();

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            // Validate step
            window.validateStep = function (step) {
                const stepContent = document.querySelector(`.step-content[data-step="${step}"]`);
                if (!stepContent) return false;

                const requiredFields = stepContent.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value || field.value.trim() === '') {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    showToast('Please fill in all required fields', 'warning');
                }

                return isValid;
            };

            // Validate current step (alias for validateStep)
            function validateCurrentStep() {
                return validateStep(currentStep);
            }

            // Show toast notification
            function showToast(message, type = 'info') {
                // Create toast element if it doesn't exist
                let toastContainer = document.getElementById('toastContainer');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'toastContainer';
                    toastContainer.style.cssText = `
                                                            position: fixed;
                                                            top: 20px;
                                                            right: 20px;
                                                            z-index: 9999;
                                                        `;
                    document.body.appendChild(toastContainer);
                }

                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'warning' ? 'warning' : 'info'} alert-dismissible fade show`;
                toast.style.cssText = `
                                                        margin-bottom: 10px;
                                                        min-width: 300px;
                                                        animation: slideInRight 0.3s ease;
                                                    `;
                toast.innerHTML = `
                                                        ${message}
                                                        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                                                    `;

                toastContainer.appendChild(toast);

                // Auto remove after 3 seconds
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 3000);
            }

            // Update navigation buttons
            function updateNavigationButtons() {
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const submitBtn = document.getElementById('submitBtn');

                if (prevBtn) {
                    prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-flex';
                }

                if (currentStep === totalSteps) {
                    if (nextBtn) nextBtn.style.display = 'none';
                    if (submitBtn) submitBtn.style.display = 'inline-flex';
                } else {
                    if (nextBtn) nextBtn.style.display = 'inline-flex';
                    if (submitBtn) submitBtn.style.display = 'none';
                }
            }

            // Get current location
            window.getCurrentLocation = function () {
                if (navigator.geolocation) {
                    showToast("<?php echo e(__('properties.location_getting')); ?>", 'info');
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                            document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                            showToast("<?php echo e(__('properties.location_obtained')); ?>", 'success');
                        },
                        function (error) {
                            showToast("<?php echo e(__('properties.location_failed')); ?>", 'error');
                        }
                    );
                } else {
                    showToast("<?php echo e(__('properties.geolocation_unsupported')); ?>", 'error');
                }
            };

            // Geocode address to get coordinates
            window.geocodeAddress = function () {
                const address = document.getElementById('address').value;
                const city = document.getElementById('city').value;
                const state = document.getElementById('state').value;
                const country = document.getElementById('country').value;

                if (!address || !city || !country) {
                    showToast("<?php echo e(__('properties.address_required')); ?>", 'warning');
                    return;
                }

                // Show loading
                const verificationDiv = document.getElementById('addressVerification');
                const resultDiv = document.getElementById('addressResult');

                verificationDiv.style.display = 'block';
                resultDiv.style.display = 'none';

                // Build full address
                const fullAddress = `${address}, ${city}, ${state ? state + ', ' : ''}${country}`;

                // Use Nominatim API (OpenStreetMap) for geocoding
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}&limit=1&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        verificationDiv.style.display = 'none';

                        if (data && data.length > 0) {
                            const result = data[0];
                            const lat = parseFloat(result.lat);
                            const lng = parseFloat(result.lon);

                            // Update coordinates
                            document.getElementById('latitude').value = lat.toFixed(6);
                            document.getElementById('longitude').value = lng.toFixed(6);

                            // Show success result
                            resultDiv.innerHTML = `
                                                                <div class="alert alert-success">
                                                                    <div class="d-flex align-items-start">
                                                                        <i class="fas fa-check-circle me-3 mt-1"></i>
                                                                        <div class="flex-grow-1">
                                                                            <h6 class="alert-heading mb-2"><?php echo e(__('properties.address_verified')); ?></h6>
                                                                            <p class="mb-2"><strong><?php echo e(__('properties.found_location')); ?></strong> ${result.display_name}</p>
                                                                            <div class="row small">
                                                                                <div class="col-md-6">
                                                                                    <strong><?php echo e(__('properties.coordinates')); ?></strong><br>
                                                                                    <?php echo e(__('properties.latitude')); ?>: ${lat.toFixed(6)}<br>
                                                                                    <?php echo e(__('properties.longitude')); ?>: ${lng.toFixed(6)}
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <strong><?php echo e(__('properties.address_details')); ?></strong><br>
                                                                                    ${result.address.road || 'N/A'}<br>
                                                                                    ${result.address.city || result.address.town || result.address.village || 'N/A'}
                                                                                </div>
                                                                            </div>
                                                                            <div class="mt-3">
                                                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="showLocationPreview(${lat}, ${lng})">
                                                                                    <i class="fas fa-map me-1"></i><?php echo e(__('properties.preview_location')); ?>

                                                                                </button>
                                                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="clearAddressResult()">
                                                                                    <i class="fas fa-times me-1"></i><?php echo e(__('common.clear')); ?>

                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            `;
                            resultDiv.style.display = 'block';

                            showToast("<?php echo e(__('properties.address_verified_toast')); ?>", 'success');
                        } else {
                            // Try alternative search with less strict format
                            const simpleAddress = `${address}, ${city}, ${country}`;
                            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(simpleAddress)}&limit=3`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data && data.length > 0) {
                                        // Show multiple options
                                        let optionsHtml = `
                                                                            <div class="alert alert-warning">
                                                                                <h6 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i><?php echo e(__('properties.multiple_locations_found')); ?></h6>
                                                                                <p class="mb-2 small"><?php echo e(__('properties.select_correct_location')); ?></p>
                                                                                <div class="list-group list-group-flush smaller">
                                                                        `;

                                        data.forEach((result, index) => {
                                            const lat = parseFloat(result.lat);
                                            const lng = parseFloat(result.lon);
                                            optionsHtml += `
                                                                                <div class="list-group-item list-group-item-action">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <h6 class="mb-1">${result.display_name}</h6>
                                                                                            <small class="text-muted">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>
                                                                                        </div>
                                                                                        <button type="button" class="btn btn-sm btn-primary" onclick="selectLocation(${lat}, ${lng}, '${result.display_name.replace(/'/g, "\\'")}')">
                                                                                            <?php echo e(__('common.select')); ?>

                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            `;
                                        });

                                        optionsHtml += `
                                                                                </div>
                                                                                <button type="button" class="btn btn-sm btn-outline-secondary mt-3" onclick="clearAddressResult()">
                                                                                    <i class="fas fa-times me-1"></i><?php echo e(__('common.cancel')); ?>

                                                                                </button>
                                                                            </div>
                                                                        `;

                                        resultDiv.innerHTML = optionsHtml;
                                        resultDiv.style.display = 'block';
                                    } else {
                                        // No results found
                                        resultDiv.innerHTML = `
                                                                             <div class="alert alert-danger">
                                                                                 <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i><?php echo e(__('properties.address_not_found')); ?></h6>
                                                                                 <p class="mb-2"><?php echo e(__('properties.address_not_found_desc')); ?></p>
                                                                                 <div class="small">
                                                                                     <strong><?php echo e(__('common.tips')); ?>:</strong><br>
                                                                                     • <?php echo e(__('properties.tip_1')); ?><br>
                                                                                     • <?php echo e(__('properties.tip_2')); ?><br>
                                                                                     • <?php echo e(__('properties.tip_3')); ?><br>
                                                                                     • <?php echo e(__('properties.tip_4')); ?>

                                                                                 </div>
                                                                                 <div class="mt-3">
                                                                                     <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearAddressResult()">
                                                                                         <i class="fas fa-redo me-1"></i><?php echo e(__('common.try_again')); ?>

                                                                                     </button>
                                                                                 </div>
                                                                             </div>
                                                                        `;
                                        resultDiv.style.display = 'block';
                                        showToast("<?php echo e(__('properties.address_not_found_toast')); ?>", 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Geocoding error:', error);
                                    resultDiv.innerHTML = `
                                                                     <div class="alert alert-danger">
                                                                         <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i><?php echo e(__('properties.geocoding_error')); ?></h6>
                                                                         <p><?php echo e(__('properties.geocoding_error_desc')); ?></p>
                                                                         <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearAddressResult()">
                                                                             <i class="fas fa-redo me-1"></i><?php echo e(__('common.try_again')); ?>

                                                                         </button>
                                                                     </div>
                                                                    `;
                                    resultDiv.style.display = 'block';
                                });
                        }
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                        verificationDiv.style.display = 'none';
                        resultDiv.innerHTML = `
                                                            <div class="alert alert-danger">
                                                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i><?php echo e(__('properties.geocoding_error')); ?></h6>
                                                                <p><?php echo e(__('properties.geocoding_error_desc')); ?></p>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearAddressResult()">
                                                                    <i class="fas fa-redo me-1"></i><?php echo e(__('common.try_again')); ?>

                                                                </button>
                                                            </div>
                                                        `;
                        resultDiv.style.display = 'block';
                        showToast("<?php echo e(__('properties.geocoding_error_toast')); ?>", 'error');
                    });
            };

            // Select location from multiple options
            window.selectLocation = function (lat, lng, displayName) {
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);

                const resultDiv = document.getElementById('addressResult');
                resultDiv.innerHTML = `
                                                     <div class="alert alert-success">
                                                         <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i><?php echo e(__('properties.location_selected')); ?></h6>
                                                         <p class="mb-2"><strong>${displayName}</strong></p>
                                                         <p class="mb-0 small"><?php echo e(__('properties.coordinates')); ?> ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                                                     </div>
                                                `;
                showToast("<?php echo e(__('properties.location_selected_toast')); ?>", 'success');
            };

            // Clear address result
            window.clearAddressResult = function () {
                document.getElementById('addressVerification').style.display = 'none';
                document.getElementById('addressResult').style.display = 'none';
            };

            // Show location preview (optional - could open a modal with map)
            window.showLocationPreview = function (lat, lng) {
                // For now, just show a simple preview
                const resultDiv = document.getElementById('addressResult');
                resultDiv.innerHTML += `
                                                     <div class="alert alert-info mt-2">
                                                         <h6 class="alert-heading"><i class="fas fa-map me-2"></i><?php echo e(__('properties.preview_location')); ?></h6>
                                                         <p class="mb-0"><?php echo e(__('properties.coordinates')); ?>: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                                                         <small class="text-muted"><?php echo e(__('properties.location_preview_desc')); ?></small>
                                                     </div>
                                                `;
            };

            // Save draft
            window.saveDraft = function () {
                showToast("<?php echo e(__('properties.draft_saved')); ?>", 'success');
            };

            // Show toast notification
            window.showToast = function (message, type = 'info') {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = `toast ${type}`;

                const icon = {
                    success: 'fa-check-circle',
                    warning: 'fa-exclamation-triangle',
                    error: 'fa-times-circle',
                    info: 'fa-info-circle'
                }[type] || 'fa-info-circle';
                  toast.innerHTML = `
                                                        <i class="fas ${icon}"></i>
                                                        <span>${message}</span>
                                                    `;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            };

            // Date Validation Logic
            const startDateInput = document.getElementById('rental_start_date');
            const endDateInput = document.getElementById('rental_end_date');

            if (startDateInput && endDateInput) {
                // Set initial min date if start date is already selected
                if (startDateInput.value) {
                    endDateInput.min = startDateInput.value;
                }

                startDateInput.addEventListener('change', function () {
                    const startDate = this.value;
                    endDateInput.min = startDate;

                    // If end date is before start date, clear it
                    if (endDateInput.value && endDateInput.value < startDate) {
                        endDateInput.value = '';
                        window.showToast('Rental end date has been reset because it cannot be before the start date.', 'warning');
                    }
                });

                endDateInput.addEventListener('change', function () {
                    if (startDateInput.value && this.value < startDateInput.value) {
                        this.value = '';
                        window.showToast('Rental end date must be after or equal to start date.', 'error');
                    }
                });
            }

            // Remove image preview
            window.removeImage = function (button) {
                const item = button.closest('.image-preview-item');
                if (item) {
                    item.style.animation = 'scaleIn 0.3s ease reverse';
                    setTimeout(() => item.remove(), 300);
                }
            };

            // Room upload functions
            window.openRoomFileInput = function (roomType) {
                console.log(' DIRECT CLICK for room:', roomType);
                const input = document.querySelector(`input[name="room_images[${roomType}][]"]`);
                if (input) {
                    input.click();
                } else {
                    console.error('Input not found for room:', roomType);
                }
            };

            window.handleRoomFileSelect = function (roomType, input) {
                const files = input.files;
                const card = input.closest('.room-upload-card');
                if (card && files.length > 0) {
                    card.style.borderColor = '#10b981';
                    card.style.background = '#f0fdf4';
                    showToast(`${files.length} ${roomType} image(s) selected`, 'success');
                }
            };

            window.setupRoomUploads = function () {
                console.log(' Setting up room uploads...');
                const roomInputs = document.querySelectorAll('.room-file-input');
                console.log(' Found', roomInputs.length, 'room inputs');

                roomInputs.forEach(input => {
                    const roomType = input.name.match(/room_images\[(.*?)\]/)?.[1];
                    if (roomType) {
                        console.log(` Setting up ${roomType} input`);
                        input.addEventListener('change', function () {
                            handleRoomFileSelect(roomType, this);
                        });
                    }
                });
            };

            // Image preview handler
            function handleImagePreview(e) {
                const files = Array.from(e.target.files);
                const previewContainer = document.getElementById('imagePreview');

                if (!previewContainer) return;

                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const div = document.createElement('div');
                            div.className = 'image-preview-item';
                            div.innerHTML = `
                                                                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                                                                    <button type="button" class="remove-btn" onclick="removeImage(this)">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Room upload handlers
            function setupRoomUploads() {
                console.log('Setting up room uploads...');
                const roomCards = document.querySelectorAll('.room-upload-card');
                console.log('Found room cards:', roomCards.length);

                roomCards.forEach((card, index) => {
                    console.log(`Setting up card ${index}:`, card);

                    card.addEventListener('click', function (e) {
                        console.log('Room card clicked:', e.target);
                        const fileInput = this.querySelector('.room-file-input');
                        console.log('File input found:', fileInput);

                        if (fileInput) {
                            console.log('Triggering file input click');
                            fileInput.click();
                        } else {
                            console.error('No file input found in card');
                        }
                    });

                    const fileInput = card.querySelector('.room-file-input');
                    if (fileInput) {
                        fileInput.addEventListener('change', function (e) {
                            console.log('File input changed:', e.target.files.length, 'files');
                            const files = e.target.files;
                            const uploadArea = card.querySelector('.room-upload-area');

                            if (files.length > 0) {
                                uploadArea.innerHTML = `
                                                                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                                                        <p style="color: #10b981; font-weight: 600;">${files.length} <?php echo e(__('properties.photos_selected')); ?></p>
                                                                    `;
                                card.style.borderColor = '#10b981';
                                card.style.background = '#f0fdf4';
                            }
                        });
                    }
                });
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Property form initialized');


                // Setup room uploads
                setupRoomUploads();

                // Setup navigation buttons
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');

                if (prevBtn) {
                    prevBtn.addEventListener('click', function () {
                        changeStep(-1);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function () {
                        console.log('Next button clicked');
                        console.log('Current step:', currentStep);

                        const isValid = validateCurrentStep();
                        console.log('Validation result:', isValid);

                        if (isValid) {
                            console.log('Moving to next step...');
                            changeStep(1);
                        } else {
                            console.log('Validation failed, staying on current step');
                        }
                    });
                }

                // Setup image preview
                const imagesInput = document.getElementById('images');
                if (imagesInput) {
                    imagesInput.addEventListener('change', handleImagePreview);
                }

                // Setup drag and drop for upload area
                const uploadArea = document.getElementById('uploadArea');
                if (uploadArea) {
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        uploadArea.addEventListener(eventName, preventDefaults, false);
                    });

                    function preventDefaults(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    // Click handler for upload area
                    uploadArea.addEventListener('click', function (e) {
                        if (imagesInput && e.target !== imagesInput) {
                            imagesInput.click();
                        }
                    });

                    uploadArea.addEventListener('drop', function (e) {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        if (imagesInput) {
                            imagesInput.files = files;
                            handleImagePreview({ target: imagesInput });
                        }
                    }, false);
                }

                // Form submission validation
                const propertyForm = document.getElementById('propertyForm');
                if (propertyForm) {
                    propertyForm.addEventListener('submit', function (e) {
                        console.log('=== FORM SUBMISSION STARTED ===');

                        // Ensure all steps are visible before submission
                        const allSteps = document.querySelectorAll('.step-content');
                        allSteps.forEach(step => {
                            step.style.display = 'block';
                        });

                        // Log all form data
                        const formData = new FormData(this);
                        console.log('=== ALL FORM DATA ===');
                        for (let [key, value] of formData.entries()) {
                            console.log(`${key}: ${value}`);
                        }

                        // Check specific required fields
                        const requiredFields = ['price', 'currency', 'city', 'country', 'address'];
                        let missingFields = [];

                        console.log('=== CHECKING REQUIRED FIELDS ===');
                        requiredFields.forEach(fieldName => {
                            const field = document.querySelector(`[name="${fieldName}"]`);
                            if (field) {
                                console.log(`Field ${fieldName}: value="${field.value}", type="${field.type}"`);
                                if (!field.value || field.value.trim() === '') {
                                    missingFields.push(fieldName);
                                    field.classList.add('is-invalid');
                                    console.log(`❌ MISSING: ${fieldName}`);
                                } else {
                                    console.log(`✅ FOUND: ${fieldName} = ${field.value}`);
                                }
                            } else {
                                console.log(`❌ FIELD NOT FOUND: ${fieldName}`);
                                missingFields.push(fieldName);
                            }
                        });

                        if (missingFields.length > 0) {
                            e.preventDefault();
                            console.error('❌ SUBMISSION BLOCKED - Missing fields:', missingFields);
                            showToast(`Please fill in: ${missingFields.join(', ')}`, 'warning');
                            return false;
                        }

                        console.log('✅ SUBMISSION ALLOWED');
                    });
                }
            });

            // Remove is-invalid class on input
            document.querySelectorAll('.form-control, .form-select').forEach(field => {
                field.addEventListener('input', function () {
                    this.classList.remove('is-invalid');
                });
            });

            // Initialize first step
            updateNavigationButtons();
            updateProgressLine();
        })();
    </script>

    <script>
        // Setup room uploads on load
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof setupRoomUploads === 'function') {
                setupRoomUploads();
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/properties/create.blade.php ENDPATH**/ ?>