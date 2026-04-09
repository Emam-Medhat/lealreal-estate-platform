@extends('layouts.app')

@section('content')
    <div class="checkout-page"
        style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 40px 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Page Header -->
                    <div class="text-center mb-5">
                        <h1 class="display-5 fw-bold mb-3" style="color: #2c3e50;">
                            <i class="fas fa-lock me-3 text-success"></i>إتمام عملية الدفع الآمن
                        </h1>
                        <p class="lead text-muted">استكمل حجز العقار الخاص بك بخطوات بسيطة وآمنة</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: 12px;">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success shadow-sm border-0 mb-4" style="border-radius: 12px;">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Main Content -->
                        <div class="col-lg-8">
                            <!-- Property Summary Card -->
                            <div class="card shadow-lg mb-4 border-0" style="border-radius: 15px;">
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-4">
                                        <i class="fas fa-home me-2 text-primary"></i>ملخص العقار
                                    </h5>

                                    <div class="d-flex gap-4 align-items-start">
                                        <div class="property-image-wrapper"
                                            style="width: 180px; height: 120px; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            @if($property->main_image)
                                                <img src="{{ $property->main_image }}" class="w-100 h-100"
                                                    style="object-fit: cover;" alt="{{ $property->title }}">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-2" style="color: #2c3e50; font-size: 1.1rem;">
                                                {{ $property->title }}</h6>
                                            <p class="text-muted mb-2">
                                                <i
                                                    class="fas fa-map-marker-alt me-2 text-danger"></i>{{ $property->full_address }}
                                            </p>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-success text-white px-3 py-2"
                                                    style="font-size: 0.9rem;">
                                                    <i class="fas fa-tag me-1"></i>{{ number_format($property->price) }}
                                                    {{ $property->currency }}
                                                </span>
                                                @if($property->bedrooms)
                                                    <span class="badge bg-light text-dark px-2 py-1">
                                                        <i class="fas fa-bed me-1"></i>{{ $property->bedrooms }} غرف
                                                    </span>
                                                @endif
                                                @if($property->area)
                                                    <span class="badge bg-light text-dark px-2 py-1">
                                                        <i class="fas fa-ruler-combined me-1"></i>{{ $property->area }}م²
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Methods Card -->
                            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-4">
                                        <i class="fas fa-credit-card me-2 text-primary"></i>اختر وسيلة الدفع
                                    </h5>

                                    <form action="{{ route('payments.process', $property->id) }}" method="POST"
                                        id="payment-form">
                                        @csrf
                                        <div class="payment-methods-grid">
                                            @foreach($paymentMethods as $index => $method)
                                                <div class="payment-method-item">
                                                    <label class="payment-method-label">
                                                        <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                                            class="payment-method-radio" {{ $loop->first ? 'checked' : '' }}>

                                                        <div class="payment-method-card">
                                                            <div class="payment-icon">
                                                                <i class="{{ $method->icon }} fa-2x"></i>
                                                            </div>
                                                            <div class="payment-info">
                                                                <div class="payment-name">{{ $method->name }}</div>
                                                                <div class="payment-desc">دفع آمن وسريع</div>
                                                            </div>
                                                            <div class="payment-radio">
                                                                <div class="radio-circle"></div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-lg btn-primary w-100 py-3 fw-bold"
                                                style="border-radius: 10px; font-size: 1.1rem; background: linear-gradient(45deg, #007bff, #0056b3);">
                                                <i class="fas fa-lock me-2"></i>
                                                ادفع الآن
                                                <span
                                                    class="badge bg-light text-primary ms-2">{{ number_format($property->price) }}
                                                    {{ $property->currency }}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Security Features -->
                            <div class="card shadow-sm mb-4 border-0" style="border-radius: 15px; background: #f8f9fa;">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-shield-alt text-success me-2"></i>ضمانات الأمان
                                    </h6>

                                    <div class="security-features">
                                        <div class="security-item mb-3">
                                            <i class="fas fa-lock text-success me-3"></i>
                                            <span>تشفير SSL 256-بت</span>
                                        </div>
                                        <div class="security-item mb-3">
                                            <i class="fas fa-certificate text-success me-3"></i>
                                            <span>متوافق مع PCI-DSS</span>
                                        </div>
                                        <div class="security-item mb-3">
                                            <i class="fas fa-check-circle text-success me-3"></i>
                                            <span>تأكيد فوري للحجز</span>
                                        </div>
                                        <div class="security-item">
                                            <i class="fas fa-user-shield text-success me-3"></i>
                                            <span>حماية البيانات الشخصية</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Support Card -->
                            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-headset text-primary me-2"></i>هل تحتاج مساعدة؟
                                    </h6>

                                    <p class="text-muted mb-4">تواصل مع فريق الدعم الفني إذا واجهتك أي مشكلة أثناء الدفع.
                                    </p>

                                    <div class="d-grid gap-2">
                                        <a href="https://wa.me/201000000000" class="btn btn-success w-100">
                                            <i class="fab fa-whatsapp me-2"></i>تحدث مع وكيل الآن
                                        </a>
                                        <a href="tel:+201000000000" class="btn btn-outline-success w-100">
                                            <i class="fas fa-phone me-2"></i>اتصل بنا
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .payment-methods-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-method-label {
            cursor: pointer;
            display: block;
        }

        .payment-method-card {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: white;
            transition: all 0.3s ease;
            position: relative;
        }

        .payment-method-card:hover {
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        }

        .payment-method-radio:checked+.payment-method-card {
            border-color: #007bff;
            background: linear-gradient(45deg, rgba(0, 123, 255, 0.05), rgba(0, 123, 255, 0.1));
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
        }

        .payment-method-radio {
            display: none;
        }

        .payment-icon {
            margin-left: 15px;
            color: #007bff;
            width: 50px;
            text-align: center;
        }

        .payment-info {
            flex-grow: 1;
        }

        .payment-name {
            font-weight: 600;
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .payment-desc {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .payment-radio {
            width: 24px;
            height: 24px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .payment-method-radio:checked+.payment-method-card .payment-radio {
            border-color: #007bff;
            background: #007bff;
        }

        .payment-method-radio:checked+.payment-method-card .radio-circle {
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            display: block;
        }

        .security-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .checkout-page {
                padding: 20px 0;
            }

            .payment-methods-grid {
                grid-template-columns: 1fr;
            }

            .payment-method-card {
                padding: 15px;
            }

            .payment-icon {
                margin-left: 10px;
                width: 40px;
            }

            .payment-icon i {
                font-size: 1.5rem !important;
            }
        }
    </style>
@endsection