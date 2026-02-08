@extends('layouts.app')

@section('title', 'Add Property')

@section('content')
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
                            <h1 class="page-title">Add New Property</h1>
                            <p class="page-subtitle">List your property on our platform and reach thousands of potential
                                buyers</p>
                        </div>
                    </div>
                    <a href="{{ route('optimized.properties.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Properties</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Property Form -->
        <form method="POST" action="{{ route('optimized.properties.store') }}" enctype="multipart/form-data"
            id="propertyForm">
            @csrf

            <!-- Success/Error Messages -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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
                                <div class="step-label">Basic Info</div>
                            </div>
                            <div class="step-item" data-step="2">
                                <div class="step-circle">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span class="step-number">2</span>
                                </div>
                                <div class="step-label">Location</div>
                            </div>
                            <div class="step-item" data-step="3">
                                <div class="step-circle">
                                    <i class="fas fa-list-ul"></i>
                                    <span class="step-number">3</span>
                                </div>
                                <div class="step-label">Details</div>
                            </div>
                            <div class="step-item" data-step="4">
                                <div class="step-circle">
                                    <i class="fas fa-camera"></i>
                                    <span class="step-number">4</span>
                                </div>
                                <div class="step-label">Media</div>
                            </div>
                            <div class="step-item" data-step="5">
                                <div class="step-circle">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span class="step-number">5</span>
                                </div>
                                <div class="step-label">Pricing</div>
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
                            <h3 class="form-card-title">Basic Information</h3>
                            <p class="form-card-subtitle"> </p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        Property Title <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-heading"></i>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            id="title" name="title" value="{{ old('title') }}"
                                            placeholder="e.g., Luxury Villa with Pool" required>
                                    </div>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_type" class="form-label">
                                        Property Type <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-building"></i>
                                        <select class="form-select @error('property_type_id') is-invalid @enderror"
                                            id="property_type" name="property_type_id" required>
                                            <option value="">Select Property Type</option>
                                            @foreach($propertyTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('property_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('property_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="listing_type" class="form-label">
                                        Listing Type <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-tag"></i>
                                        <select class="form-select @error('listing_type') is-invalid @enderror"
                                            id="listing_type" name="listing_type" required>
                                            <option value="">Select Listing Type</option>
                                            <option value="sale" {{ old('listing_type') == 'sale' ? 'selected' : '' }}>For
                                                Sale</option>
                                            <option value="rent" {{ old('listing_type') == 'rent' ? 'selected' : '' }}>For
                                                Rent</option>
                                            <option value="lease" {{ old('listing_type') == 'lease' ? 'selected' : '' }}>For
                                                Lease</option>
                                        </select>
                                    </div>
                                    @error('listing_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">
                                        Status <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-toggle-on"></i>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft
                                            </option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">
                                        Property Description <span class="required">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="6"
                                        placeholder="Provide a detailed description of your property..."
                                        required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Provide a detailed description (minimum 50 characters)
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="feature-toggles">
                                    <div class="toggle-item">
                                        <input type="checkbox" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                                        <label for="featured">
                                            <i class="fas fa-star"></i>
                                            <span>Featured Property</span>
                                            <small>Highlight this property on homepage</small>
                                        </label>
                                    </div>
                                    <div class="toggle-item">
                                        <input type="checkbox" id="premium" name="premium" value="1" {{ old('premium') ? 'checked' : '' }}>
                                        <label for="premium">
                                            <i class="fas fa-crown"></i>
                                            <span>Premium Listing</span>
                                            <small>Get priority in search results</small>
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
                            <h3 class="form-card-title">Location Information</h3>
                            <p class="form-card-subtitle">Specify the exact location of your property</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="address" class="form-label">
                                        Street Address <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map-pin"></i>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                                            id="address" name="address" value="{{ old('address') }}"
                                            placeholder="e.g., 123 Main Street" required>
                                    </div>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city" class="form-label">
                                        City <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-city"></i>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                            id="city" name="city" value="{{ old('city') }}" placeholder="e.g., Riyadh"
                                            required>
                                    </div>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state" class="form-label">State/Province</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map"></i>
                                        <input type="text" class="form-control @error('state') is-invalid @enderror"
                                            id="state" name="state" value="{{ old('state') }}"
                                            placeholder="e.g., Central Province">
                                    </div>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country" class="form-label">
                                        Country <span class="required">*</span>
                                    </label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-globe"></i>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror"
                                            id="country" name="country" value="{{ old('country') ?? 'Saudi Arabia' }}"
                                            required>
                                    </div>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-mail-bulk"></i>
                                        <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                            id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                                            placeholder="e.g., 12345">
                                    </div>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="neighborhood" class="form-label">Neighborhood</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-home"></i>
                                        <input type="text" class="form-control @error('neighborhood') is-invalid @enderror"
                                            id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}"
                                            placeholder="e.g., Al Malqa">
                                    </div>
                                    @error('neighborhood')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="district" class="form-label">District</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-location-arrow"></i>
                                        <input type="text" class="form-control @error('district') is-invalid @enderror"
                                            id="district" name="district" value="{{ old('district') }}"
                                            placeholder="e.g., North District">
                                    </div>
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-compass"></i>
                                        <input type="number" step="any"
                                            class="form-control @error('latitude') is-invalid @enderror" id="latitude"
                                            name="latitude" value="{{ old('latitude') }}" placeholder="e.g., 24.7136">
                                    </div>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-location-crosshairs"></i>
                                        <input type="number" step="any"
                                            class="form-control @error('longitude') is-invalid @enderror" id="longitude"
                                            name="longitude" value="{{ old('longitude') }}" placeholder="e.g., 46.6753">
                                    </div>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn-location" onclick="getCurrentLocation()">
                                        <i class="fas fa-crosshairs"></i>
                                        <span>Get Current Location</span>
                                    </button>
                                    <button type="button" class="btn-location" onclick="geocodeAddress()">
                                        <i class="fas fa-search-location"></i>
                                        <span>Verify Address & Get Coordinates</span>
                                    </button>
                                </div>
                                
                                <!-- Address Verification Status -->
                                <div id="addressVerification" class="mt-3" style="display: none;">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <div class="spinner-border spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div>Verifying address and getting coordinates...</div>
                                    </div>
                                </div>
                                
                                <div id="addressResult" class="mt-3" style="display: none;">
                                    <!-- Results will be shown here -->
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
                            <h3 class="form-card-title">Property Details</h3>
                            <p class="form-card-subtitle">Add specifications and features</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bedrooms" class="form-label">Bedrooms</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-bed"></i>
                                        <input type="number" class="form-control @error('bedrooms') is-invalid @enderror"
                                            id="bedrooms" name="bedrooms" value="{{ old('bedrooms') }}" min="0"
                                            placeholder="0">
                                    </div>
                                    @error('bedrooms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bathrooms" class="form-label">Bathrooms</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-bath"></i>
                                        <input type="number" class="form-control @error('bathrooms') is-invalid @enderror"
                                            id="bathrooms" name="bathrooms" value="{{ old('bathrooms') }}" min="0"
                                            placeholder="0">
                                    </div>
                                    @error('bathrooms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="floors" class="form-label">Floors</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-layer-group"></i>
                                        <input type="number" class="form-control @error('floors') is-invalid @enderror"
                                            id="floors" name="floors" value="{{ old('floors') }}" min="0" placeholder="0">
                                    </div>
                                    @error('floors')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="parking_spaces" class="form-label">Parking</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-car"></i>
                                        <input type="number"
                                            class="form-control @error('parking_spaces') is-invalid @enderror"
                                            id="parking_spaces" name="parking_spaces" value="{{ old('parking_spaces') }}"
                                            min="0" placeholder="0">
                                    </div>
                                    @error('parking_spaces')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="year_built" class="form-label">Year Built</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                        <input type="number" class="form-control @error('year_built') is-invalid @enderror"
                                            id="year_built" name="year_built" value="{{ old('year_built') }}" min="1900"
                                            max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                                    </div>
                                    @error('year_built')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="area" class="form-label">
                                        Living Area <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-with-icon">
                                            <i class="fas fa-ruler-combined"></i>
                                            <input type="number" class="form-control @error('area') is-invalid @enderror"
                                                id="area" name="area" value="{{ old('area') }}" required min="1"
                                                placeholder="1000">
                                        </div>
                                        <select class="form-select area-unit @error('area_unit') is-invalid @enderror"
                                            id="area_unit" name="area_unit" required>
                                            <option value="sq_m" {{ old('area_unit') == 'sq_m' ? 'selected' : '' }}>m²
                                            </option>
                                            <option value="sq_ft" {{ old('area_unit') == 'sq_ft' ? 'selected' : '' }}>ft²
                                            </option>
                                        </select>
                                    </div>
                                    @error('area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="land_area" class="form-label">Land Area</label>
                                    <div class="input-group">
                                        <div class="input-with-icon">
                                            <i class="fas fa-ruler"></i>
                                            <input type="number"
                                                class="form-control @error('land_area') is-invalid @enderror" id="land_area"
                                                name="land_area" value="{{ old('land_area') }}" min="1" placeholder="1500">
                                        </div>
                                        <select class="form-select area-unit @error('land_area_unit') is-invalid @enderror"
                                            id="land_area_unit" name="land_area_unit">
                                            <option value="sq_m" {{ old('land_area_unit') == 'sq_m' ? 'selected' : '' }}>m²
                                            </option>
                                            <option value="sq_ft" {{ old('land_area_unit') == 'sq_ft' ? 'selected' : '' }}>ft²
                                            </option>
                                            <option value="acre" {{ old('land_area_unit') == 'acre' ? 'selected' : '' }}>acre
                                            </option>
                                            <option value="hectare" {{ old('land_area_unit') == 'hectare' ? 'selected' : '' }}>hectare</option>
                                        </select>
                                    </div>
                                    @error('land_area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                            <h3 class="form-card-title">Amenities & Features</h3>
                            <p class="form-card-subtitle">Select all that apply to your property</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="amenities-section-title">
                                    <i class="fas fa-star"></i>
                                    Amenities
                                </h6>
                                <div class="amenities-grid">
                                    @foreach($amenities as $amenity)
                                        <label class="amenity-checkbox">
                                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                            <span class="amenity-label">
                                                @if($amenity->icon)
                                                    <i class="{{ $amenity->icon }}"></i>
                                                @endif
                                                {{ $amenity->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="amenities-section-title">
                                    <i class="fas fa-gem"></i>
                                    Special Features
                                </h6>
                                <div class="amenities-grid">
                                    @foreach($features as $feature)
                                        <label class="amenity-checkbox">
                                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }}>
                                            <span class="amenity-label">
                                                @if($feature->icon)
                                                    <i class="{{ $feature->icon }}"></i>
                                                @endif
                                                {{ $feature->name }}
                                                @if($feature->is_premium)
                                                    <span class="premium-badge">Premium</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
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
                            <h3 class="form-card-title">Property Images</h3>
                            <p class="form-card-subtitle">Upload high-quality images to showcase your property</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="upload-area" id="uploadArea">
                                    <input type="file" class="file-input" id="images" name="images[]" multiple
                                        accept="image/*">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <h4>Drag & Drop Images Here</h4>
                                        <p>or click to browse</p>
                                        <span class="upload-hint">JPEG, PNG, GIF • Max 10MB each</span>
                                    </div>
                                </div>
                                <div id="imagePreview" class="image-preview-grid"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <div class="info-box-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <h6 class="info-box-title">Photo Guidelines</h6>
                                    <ul class="info-box-list">
                                        <li><i class="fas fa-check"></i>Upload at least 5 high-quality photos</li>
                                        <li><i class="fas fa-check"></i>Include exterior and interior shots</li>
                                        <li><i class="fas fa-check"></i>Show all main rooms and features</li>
                                        <li><i class="fas fa-check"></i>Ensure good lighting and clear focus</li>
                                        <li><i class="fas fa-check"></i>Avoid heavy filters or editing</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Room-specific Image Uploads -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">Room-Specific Photos</h5>
                            </div>

                            <!-- Living Room -->
                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('living_room')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-couch"></i>
                                        <span>Living Room</span>
                                    </div>
                                    <input type="file" name="room_images[living_room][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Living Room Photos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('kitchen')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-utensils"></i>
                                        <span>Kitchen</span>
                                    </div>
                                    <input type="file" name="room_images[kitchen][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Kitchen Photos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('bedrooms')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-bed"></i>
                                        <span>Bedrooms</span>
                                    </div>
                                    <input type="file" name="room_images[bedrooms][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Bedroom Photos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('bathrooms')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-bath"></i>
                                        <span>Bathrooms</span>
                                    </div>
                                    <input type="file" name="room_images[bathrooms][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Bathroom Photos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('entrance')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-door-open"></i>
                                        <span>Main Entrance</span>
                                    </div>
                                    <input type="file" name="room_images[entrance][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Entrance Photos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('outdoor')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-tree"></i>
                                        <span>Garden/Outdoor</span>
                                    </div>
                                    <input type="file" name="room_images[outdoor][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Outdoor Photos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('garage')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-car"></i>
                                        <span>Garage/Parking</span>
                                    </div>
                                    <input type="file" name="room_images[garage][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Garage Photos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="room-upload-card" onclick="openRoomFileInput('amenities')">
                                    <div class="room-upload-header">
                                        <i class="fas fa-swimming-pool"></i>
                                        <span>Pool/Amenities</span>
                                    </div>
                                    <input type="file" name="room_images[amenities][]" multiple accept="image/*"
                                        class="room-file-input">
                                    <div class="room-upload-area">
                                        <i class="fas fa-camera"></i>
                                        <p>Add Amenities Photos</p>
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
                            <h3 class="form-card-title">Virtual Tour</h3>
                            <p class="form-card-subtitle">Add a 360° virtual tour link (optional)</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="form-group">
                            <label for="virtual_tour_url" class="form-label">Virtual Tour URL</label>
                            <div class="input-with-icon">
                                <i class="fas fa-link"></i>
                                <input type="url" class="form-control @error('virtual_tour_url') is-invalid @enderror"
                                    id="virtual_tour_url" name="virtual_tour_url" value="{{ old('virtual_tour_url') }}"
                                    placeholder="https://example.com/virtual-tour">
                            </div>
                            @error('virtual_tour_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Add a link to your virtual tour (YouTube, Matterport, etc.)
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
                            <h3 class="form-card-title">Pricing Information</h3>
                            <p class="form-card-subtitle">Set the price and payment terms</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">
                                        Price <span class="required">*</span>
                                    </label>
                                    <div class="price-input-group">
                                        <select class="currency-select @error('currency') is-invalid @enderror"
                                            id="currency" name="currency" required>
                                            <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                            <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED</option>
                                        </select>
                                        <input type="number"
                                            class="form-control price-input @error('price') is-invalid @enderror" id="price"
                                            name="price" value="{{ old('price') }}" required min="0" step="0.01"
                                            placeholder="0.00">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_frequency" class="form-label">Payment Frequency</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-calendar-check"></i>
                                        <select class="form-select @error('payment_frequency') is-invalid @enderror"
                                            id="payment_frequency" name="payment_frequency">
                                            <option value="">Select Frequency</option>
                                            <option value="monthly" {{ old('payment_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="quarterly" {{ old('payment_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="annually" {{ old('payment_frequency') == 'annually' ? 'selected' : '' }}>Annually</option>
                                        </select>
                                    </div>
                                    @error('payment_frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="price-options">
                                    <label class="option-checkbox">
                                        <input type="checkbox" id="is_negotiable" name="is_negotiable" value="1" {{ old('is_negotiable') ? 'checked' : '' }}>
                                        <span class="option-label">
                                            <i class="fas fa-handshake"></i>
                                            <span>Price is negotiable</span>
                                        </span>
                                    </label>
                                    <label class="option-checkbox">
                                        <input type="checkbox" id="includes_vat" name="includes_vat" value="1" {{ old('includes_vat') ? 'checked' : '' }}>
                                        <span class="option-label">
                                            <i class="fas fa-receipt"></i>
                                            <span>Price includes VAT</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_charges" class="form-label">Service Charges</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-coins"></i>
                                        <input type="number"
                                            class="form-control @error('service_charges') is-invalid @enderror"
                                            id="service_charges" name="service_charges" value="{{ old('service_charges') }}"
                                            min="0" step="0.01" placeholder="0.00">
                                    </div>
                                    @error('service_charges')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="maintenance_fees" class="form-label">Maintenance Fees</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-tools"></i>
                                        <input type="number"
                                            class="form-control @error('maintenance_fees') is-invalid @enderror"
                                            id="maintenance_fees" name="maintenance_fees"
                                            value="{{ old('maintenance_fees') }}" min="0" step="0.01" placeholder="0.00">
                                    </div>
                                    @error('maintenance_fees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                            <span>Previous</span>
                        </button>
                        <div class="nav-center">
                            <button type="button" class="btn-draft" onclick="saveDraft()">
                                <i class="fas fa-save"></i>
                                <span>Save Draft</span>
                            </button>
                        </div>
                        <button type="button" class="btn-nav btn-next" id="nextBtn">
                            <span>Next</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn-nav btn-submit" id="submitBtn">
                            <i class="fas fa-check"></i>
                            <span>Create Property</span>
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
                    showToast('Please fill in all required fields', 'warning');
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
                    showToast('Getting your location...', 'info');
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                            document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                            showToast('Location obtained successfully!', 'success');
                        },
                        function (error) {
                            showToast('Unable to get your location. Please enter manually.', 'error');
                        }
                    );
                } else {
                    showToast('Geolocation is not supported by your browser.', 'error');
                }
            };

            // Geocode address to get coordinates
            window.geocodeAddress = function () {
                const address = document.getElementById('address').value;
                const city = document.getElementById('city').value;
                const state = document.getElementById('state').value;
                const country = document.getElementById('country').value;
                
                if (!address || !city || !country) {
                    showToast('Please fill in Address, City, and Country fields first.', 'warning');
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
                                            <h6 class="alert-heading mb-2">Address Verified Successfully!</h6>
                                            <p class="mb-2"><strong>Found Location:</strong> ${result.display_name}</p>
                                            <div class="row small">
                                                <div class="col-md-6">
                                                    <strong>Coordinates:</strong><br>
                                                    Latitude: ${lat.toFixed(6)}<br>
                                                    Longitude: ${lng.toFixed(6)}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Address Details:</strong><br>
                                                    ${result.address.road || 'N/A'}<br>
                                                    ${result.address.city || result.address.town || result.address.village || 'N/A'}
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="showLocationPreview(${lat}, ${lng})">
                                                    <i class="fas fa-map me-1"></i>Preview Location
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="clearAddressResult()">
                                                    <i class="fas fa-times me-1"></i>Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            resultDiv.style.display = 'block';
                            
                            showToast('Address verified and coordinates updated!', 'success');
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
                                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Multiple Locations Found</h6>
                                                <p class="mb-3">Please select the correct location:</p>
                                                <div class="list-group">
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
                                                            Select
                                                        </button>
                                                    </div>
                                                </div>
                                            `;
                                        });
                                        
                                        optionsHtml += `
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mt-3" onclick="clearAddressResult()">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>
                                            </div>
                                        `;
                                        
                                        resultDiv.innerHTML = optionsHtml;
                                        resultDiv.style.display = 'block';
                                    } else {
                                        // No results found
                                        resultDiv.innerHTML = `
                                            <div class="alert alert-danger">
                                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Address Not Found</h6>
                                                <p class="mb-2">Could not find coordinates for the entered address.</p>
                                                <div class="small">
                                                    <strong>Tips:</strong><br>
                                                    • Check spelling and try again<br>
                                                    • Include more specific address details<br>
                                                    • Try using nearby landmarks<br>
                                                    • Use "Get Current Location" if you're at the property
                                                </div>
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearAddressResult()">
                                                        <i class="fas fa-redo me-1"></i>Try Again
                                                    </button>
                                                </div>
                                            </div>
                                        `;
                                        resultDiv.style.display = 'block';
                                        
                                        showToast('Address not found. Please check the address and try again.', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Geocoding error:', error);
                                    resultDiv.innerHTML = `
                                        <div class="alert alert-danger">
                                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Geocoding Error</h6>
                                            <p>Unable to verify address. Please check your internet connection and try again.</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearAddressResult()">
                                                <i class="fas fa-redo me-1"></i>Try Again
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
                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Geocoding Error</h6>
                                <p>Unable to verify address. Please check your internet connection and try again.</p>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearAddressResult()">
                                    <i class="fas fa-redo me-1"></i>Try Again
                                </button>
                            </div>
                        `;
                        resultDiv.style.display = 'block';
                        showToast('Error verifying address. Please try again.', 'error');
                    });
            };

            // Select location from multiple options
            window.selectLocation = function(lat, lng, displayName) {
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
                
                const resultDiv = document.getElementById('addressResult');
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Location Selected!</h6>
                        <p class="mb-2"><strong>${displayName}</strong></p>
                        <p class="mb-0 small">Coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                    </div>
                `;
                
                showToast('Location selected successfully!', 'success');
            };

            // Clear address result
            window.clearAddressResult = function() {
                document.getElementById('addressVerification').style.display = 'none';
                document.getElementById('addressResult').style.display = 'none';
            };

            // Show location preview (optional - could open a modal with map)
            window.showLocationPreview = function(lat, lng) {
                // For now, just show a simple preview
                const resultDiv = document.getElementById('addressResult');
                resultDiv.innerHTML += `
                    <div class="alert alert-info mt-2">
                        <h6 class="alert-heading"><i class="fas fa-map me-2"></i>Location Preview</h6>
                        <p class="mb-0">Coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                        <small class="text-muted">You can see the exact location on the map after creating the property.</small>
                    </div>
                `;
            };

            // Save draft
            window.saveDraft = function () {
                showToast('Draft saved successfully!', 'success');
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
                    toast.style.animation = 'slideInRight 0.3s ease reverse';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            };

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
                                        <p style="color: #10b981; font-weight: 600;">${files.length} photo(s) selected</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof setupRoomUploads === 'function') {
                setupRoomUploads();
            }
        });
    </script>
@endsection