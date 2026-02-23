@extends('layouts.app')

@section('title', $property->title . ' | ' . config('app.name', 'منصة العقارات'))

@section('content')
<div class="property-show-page" style="background-color: #f3f4f6; min-height: 100vh; padding-bottom: 3rem;">
    
    <!-- Gallery Section -->
    <div class="container-fluid p-0 mb-4">
        <div class="row g-0" style="height: 500px;">
            <!-- Main Image -->
            <div class="col-md-8 h-100">
                @if($property->media && $property->media->count() > 0)
                    @php
                        $mainImage = $property->media->first();
                        $imagePath = Str::startsWith($mainImage->file_path, 'properties/') 
                                    ? 'storage/' . $mainImage->file_path 
                                    : 'storage/properties/' . $mainImage->file_path;
                    @endphp
                    <div style="height: 100%; width: 100%; overflow: hidden; position: relative;">
                        <img src="{{ asset($imagePath) }}" 
                             class="w-100 h-100" 
                             style="object-fit: cover; cursor: pointer;"
                             alt="{{ $property->title }}"
                             onclick="openGallery(0)">
                         <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-{{ $property->listing_type == 'sale' ? 'primary' : 'success' }} fs-6 px-3 py-2 shadow-sm">
                                {{ $property->listing_type == 'sale' ? 'للبيع' : 'للإيجار' }}
                            </span>
                            @if($property->featured)
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2 shadow-sm ms-2">
                                    <i class="fas fa-star me-1"></i> مميز
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">
                        <div class="text-center">
                            <i class="fas fa-image fa-4x mb-3"></i>
                            <p>لا توجد صور للعقار</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Side Images -->
            <div class="col-md-4 h-100 d-none d-md-block">
                <div class="row g-0 h-100">
                    @if($property->media && $property->media->count() > 1)
                        @foreach($property->media->skip(1)->take(2) as $index => $media)
                            @php
                                $subImagePath = Str::startsWith($media->file_path, 'properties/') 
                                            ? 'storage/' . $media->file_path 
                                            : 'storage/properties/' . $media->file_path;
                            @endphp
                            <div class="col-12 h-50 border-bottom border-start border-white">
                                <div style="height: 100%; width: 100%; overflow: hidden;">
                                    <img src="{{ asset($subImagePath) }}" 
                                         class="w-100 h-100" 
                                         style="object-fit: cover; cursor: pointer; transition: transform 0.3s;"
                                         onmouseover="this.style.transform='scale(1.05)'"
                                         onmouseout="this.style.transform='scale(1)'"
                                         alt="صورة العقار {{ $index + 2 }}"
                                         onclick="openGallery({{ $index + 1 }})">
                                </div>
                            </div>
                        @endforeach
                    @else
                         <div class="col-12 h-100 bg-light d-flex align-items-center justify-content-center border-start border-white">
                            <p class="text-muted">المزيد من الصور قريباً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Main Content Column -->
            <div class="col-lg-8">
                
                <!-- Title & Header -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <h1 class="h2 fw-bold text-dark mb-2">{{ $property->title }}</h1>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    {{ $property->address ?? '' }} {{ $property->city ? '، ' . $property->city : '' }}
                                </p>
                            </div>
                            <div class="text-end mt-3 mt-md-0">
                                <h2 class="text-primary fw-bold mb-0">
                                    {{ number_format($property->price, 0) }} 
                                    <small class="fs-6 text-muted">{{ $property->currency ?? 'ريال' }}</small>
                                </h2>
                                @if($property->price_per_sqm)
                                    <small class="text-muted d-block mt-1">
                                        {{ number_format($property->price_per_sqm, 0) }} ريال / م²
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Key Specs -->
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <i class="fas fa-bed fa-lg text-primary mb-2"></i>
                                    <div class="fw-bold fs-5">{{ $property->bedrooms ?? $property->details->bedrooms ?? '-' }}</div>
                                    <small class="text-muted">غرف نوم</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <i class="fas fa-bath fa-lg text-primary mb-2"></i>
                                    <div class="fw-bold fs-5">{{ $property->bathrooms ?? $property->details->bathrooms ?? '-' }}</div>
                                    <small class="text-muted">حمامات</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <i class="fas fa-ruler-combined fa-lg text-primary mb-2"></i>
                                    <div class="fw-bold fs-5">{{ $property->area ?? $property->details->area ?? '-' }}</div>
                                    <small class="text-muted">متر مربع</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <i class="fas fa-building fa-lg text-primary mb-2"></i>
                                    <div class="fw-bold fs-5">{{ $property->propertyType->name ?? 'غير محدد' }}</div>
                                    <small class="text-muted">النوع</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">وصف العقار</h4>
                        <div class="text-muted" style="line-height: 1.8; white-space: pre-line;">
                            {{ $property->description ?? 'لا يوجد وصف متاح.' }}
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                @if($property->amenities && $property->amenities->count() > 0)
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">المرافق والخدمات</h4>
                            <div class="row g-3">
                                @foreach($property->amenities as $amenity)
                                    <div class="col-6 col-md-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="{{ $amenity->icon ?? 'fas fa-check' }} text-success small"></i>
                                            </div>
                                            <span>{{ $amenity->name }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Property Details Grid -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="fw-bold m-0 text-primary">تفاصيل العقار</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-hashtag text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">رقم العقار</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->property_code ?? $property->id }}</span>
                            </div>
                            
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-building text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">نوع العقار</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->propertyType->name ?? '-' }}</span>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">الحالة</span>
                                </div>
                                <span class="badge bg-{{ $property->status == 'active' ? 'success' : 'secondary' }} fs-6">
                                    {{ $property->status == 'active' ? 'متاح' : $property->status }}
                                </span>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-ruler-combined text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">مساحة الأرض</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->land_area ? number_format($property->land_area) . ' ' . ($property->land_area_unit ?? 'م²') : '-' }}</span>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">سنة البناء</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->year_built ?? $property->details->year_built ?? '-' }}</span>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-car text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">مواقف السيارات</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->parking_spaces ?? $property->details->parking_spaces ?? '-' }}</span>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-layer-group text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">عدد الطوابق</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->floors ?? $property->details->floors ?? '-' }}</span>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">تاريخ العرض</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->listed_date ? \Carbon\Carbon::parse($property->listed_date)->format('Y-m-d') : $property->created_at->format('Y-m-d') }}</span>
                            </div>

                            @if($property->energy_rating)
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-bolt text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">تصنيف الطاقة</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->energy_rating }}</span>
                            </div>
                            @endif

                            @if($property->zoning)
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-signs text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">المنطقة (Zoning)</span>
                                </div>
                                <span class="fw-bold fs-5">{{ $property->zoning }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Materials -->
                @if(!empty($property->materials) && is_array($property->materials))
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="fw-bold m-0 text-primary">مواد البناء والتشطيب</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($property->materials as $material)
                                <span class="badge bg-light text-dark border p-2 fs-6">{{ $material }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Specifications -->
                @if(!empty($property->specifications) && is_array($property->specifications))
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="fw-bold m-0 text-primary">المواصفات الفنية</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <ul class="list-group list-group-flush">
                             @foreach($property->specifications as $key => $value)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                    <span class="text-muted">
                                        @if(is_string($key)) {{ $key }} @else <i class="fas fa-check text-primary me-2 small"></i> @endif
                                    </span>
                                    <span class="fw-bold">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Financial Details -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="fw-bold m-0 text-primary">تفاصيل مالية</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-4">
                            @if($property->listing_type == 'rent' || $property->rental_price)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">سعر الإيجار</span>
                                    <span class="fw-bold">{{ number_format($property->rental_price ?? $property->price, 2) }} {{ $property->currency ?? 'ريال' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">دورية الدفع</span>
                                    <span class="fw-bold">{{ $property->rental_frequency ?? 'شهري' }}</span>
                                </div>
                            </div>
                            @endif
                            
                            @if($property->service_charges)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">رسوم الخدمات</span>
                                    <span class="fw-bold">{{ number_format($property->service_charges, 2) }}</span>
                                </div>
                            </div>
                            @endif
                            
                            @if($property->maintenance_fees)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">رسوم الصيانة</span>
                                    <span class="fw-bold">{{ number_format($property->maintenance_fees, 2) }}</span>
                                </div>
                            </div>
                            @endif

                            @if($property->property_tax)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">الضريبة العقارية</span>
                                    <span class="fw-bold">{{ number_format($property->property_tax, 2) }}</span>
                                </div>
                            </div>
                            @endif

                            @if($property->insurance_cost)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">تكلفة التأمين</span>
                                    <span class="fw-bold">{{ number_format($property->insurance_cost, 2) }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Utilities & Systems -->
                @if($property->heating_system || $property->water_heating || $property->insulation || $property->ventilation || $property->air_conditioning || $property->solar_panels || $property->double_glazing)
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="fw-bold m-0 text-primary">المرافق والأنظمة</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-4">
                            @if($property->heating_system)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-fire text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">نظام التدفئة</span>
                                </div>
                                <span class="fw-bold">{{ $property->heating_system }}</span>
                            </div>
                            @endif

                            @if($property->air_conditioning)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-snowflake text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">تكييف الهواء</span>
                                </div>
                                <span class="fw-bold">{{ $property->air_conditioning }}</span>
                            </div>
                            @endif

                            @if($property->water_heating)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-water text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">تسخين المياه</span>
                                </div>
                                <span class="fw-bold">{{ $property->water_heating }}</span>
                            </div>
                            @endif

                            @if($property->insulation)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-shield-alt text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">العزل</span>
                                </div>
                                <span class="fw-bold">{{ $property->insulation }}</span>
                            </div>
                            @endif

                            @if($property->ventilation)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-wind text-muted me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">التهوية</span>
                                </div>
                                <span class="fw-bold">{{ $property->ventilation }}</span>
                            </div>
                            @endif

                            @if($property->solar_panels)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-solar-panel text-success me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">ألواح شمسية</span>
                                </div>
                                <span class="badge bg-success">متوفر</span>
                            </div>
                            @endif

                            @if($property->double_glazing)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-window-maximize text-info me-2" style="width: 20px;"></i>
                                    <span class="text-muted small">زجاج مزدوج</span>
                                </div>
                                <span class="badge bg-info">متوفر</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Legal & Ownership Details -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="fw-bold m-0 text-primary">تفاصيل قانونية وملكية</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-4">
                            @if($property->ownership_type)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">نوع الملكية</span>
                                    <span class="fw-bold">{{ $property->ownership_type }}</span>
                                </div>
                            </div>
                            @endif

                            @if($property->registration_number)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">رقم التسجيل</span>
                                    <span class="fw-bold">{{ $property->registration_number }}</span>
                                </div>
                            </div>
                            @endif

                            @if($property->deed_number)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">رقم الصك (Deed)</span>
                                    <span class="fw-bold">{{ $property->deed_number }}</span>
                                </div>
                            </div>
                            @endif

                            @if($property->land_use)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">استخدام الأرض</span>
                                    <span class="fw-bold">{{ $property->land_use }}</span>
                                </div>
                            </div>
                            @endif

                            @if($property->zoning_type)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">نوع المنطقة</span>
                                    <span class="fw-bold">{{ $property->zoning_type }}</span>
                                </div>
                            </div>
                            @endif

                            @if($property->occupancy_permit)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">تصريح الإشغال</span>
                                    <span class="fw-bold">{{ $property->occupancy_permit }}</span>
                                </div>
                            </div>
                            @endif
                            
                            @if($property->building_permit)
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <span class="text-muted">رقم رخصة البناء</span>
                                    <span class="fw-bold">{{ $property->building_permit }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Important Dates -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="fw-bold m-0 text-primary">تواريخ هامة</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row g-4">
                            @if($property->listed_date)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-calendar-alt text-muted me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">تاريخ الإدراج</small>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($property->listed_date)->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($property->available_from)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-calendar-check text-success me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">متاح من تاريخ</small>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($property->available_from)->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($property->expiry_date)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-calendar-times text-danger me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">تاريخ الانتهاء</small>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($property->expiry_date)->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($property->sold_date)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-handshake text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">تاريخ البيع</small>
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($property->sold_date)->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Features Lists (Interior/Exterior) -->
                @if(!empty($property->interior_features) || !empty($property->exterior_features))
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="row">
                            @if(!empty($property->interior_features) && is_array($property->interior_features))
                            <div class="col-md-6 mb-3">
                                <h5 class="fw-bold mb-3 border-bottom pb-2">المميزات الداخلية</h5>
                                <ul class="list-unstyled">
                                    @foreach($property->interior_features as $feature)
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="fas fa-check-circle text-primary me-2"></i> 
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            @if(!empty($property->exterior_features) && is_array($property->exterior_features))
                            <div class="col-md-6 mb-3">
                                <h5 class="fw-bold mb-3 border-bottom pb-2">المميزات الخارجية</h5>
                                <ul class="list-unstyled">
                                    @foreach($property->exterior_features as $feature)
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i> 
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Nearby Places -->
                @php
                    $nearby = [
                        'schools' => ['icon' => 'fa-school', 'label' => 'مدارس'],
                        'hospitals' => ['icon' => 'fa-hospital', 'label' => 'مستشفيات'],
                        'shopping_centers' => ['icon' => 'fa-shopping-cart', 'label' => 'مراكز تسوق'],
                        'transportation' => ['icon' => 'fa-bus', 'label' => 'مواصلات'],
                        'restaurants' => ['icon' => 'fa-utensils', 'label' => 'مطاعم'],
                        'parks' => ['icon' => 'fa-tree', 'label' => 'حدائق'],
                    ];
                    $hasNearby = false;
                    foreach($nearby as $key => $val) {
                        if(!empty($property->$key) && is_array($property->$key)) $hasNearby = true;
                    }
                @endphp

                @if($hasNearby)
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4 text-primary">الأماكن القريبة</h4>
                        <div class="row">
                            @foreach($nearby as $key => $data)
                                @if(!empty($property->$key) && is_array($property->$key))
                                <div class="col-md-6 mb-4">
                                    <h6 class="fw-bold mb-2 d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas {{ $data['icon'] }} text-secondary small"></i>
                                        </div>
                                        {{ $data['label'] }}
                                    </h6>
                                    <ul class="list-group list-group-flush border-start border-2 ps-3 ms-3">
                                        @foreach($property->$key as $place)
                                            <li class="list-group-item px-0 py-1 border-0 bg-transparent">
                                                <span class="text-muted">{{ $place }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Media Links -->
                @if($property->virtual_tour_url || $property->video_url || $property->floor_plan_url)
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">وسائط متعددة</h4>
                        <div class="d-flex gap-3 flex-wrap">
                            @if($property->virtual_tour_url)
                                <a href="{{ $property->virtual_tour_url }}" target="_blank" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-vr-cardboard me-2"></i> جولة افتراضية 360°
                                </a>
                            @endif
                            @if($property->video_url)
                                <a href="{{ $property->video_url }}" target="_blank" class="btn btn-outline-danger btn-lg">
                                    <i class="fab fa-youtube me-2"></i> فيديو العقار
                                </a>
                            @endif
                             @if($property->floor_plan_url)
                                <a href="{{ $property->floor_plan_url }}" target="_blank" class="btn btn-outline-info btn-lg">
                                    <i class="fas fa-drafting-compass me-2"></i> المخطط الهندسي
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- AI Market Analysis (Kept as requested functionality) -->
                <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-gradient text-white p-3" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                        <h5 class="m-0 font-weight-bold"><i class="fas fa-robot me-2"></i>تحليل الذكاء الاصطناعي</h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <p class="text-muted mb-4">
                            احصل على تقييم فوري مدعوم بالذكاء الاصطناعي لهذا العقار بناءً على اتجاهات السوق الحالية.
                        </p>
                        
                        <button id="ai-predict-btn" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                            <i class="fas fa-chart-line me-2"></i>تحليل السعر والاستثمار
                        </button>

                        <div id="ai-loading" class="d-none mt-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-primary">جاري تحليل بيانات السوق...</p>
                        </div>

                        <div id="ai-result" class="d-none mt-4 text-start">
                            <div class="alert alert-light border shadow-sm rounded-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6 border-end">
                                        <h6 class="text-uppercase text-xs font-weight-bold text-primary mb-1">نطاق السعر المقدر</h6>
                                        <div class="h3 mb-0 font-weight-bold text-dark" id="pred-price"></div>
                                        <div class="small text-muted mt-1">الدقة: <span id="pred-confidence"></span>%</div>
                                    </div>
                                    <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small font-weight-bold">نقاط الاستثمار</span>
                                                <span class="small font-weight-bold text-success" id="pred-score-val"></span>
                                            </div>
                                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                                <div id="pred-score-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge" id="pred-risk"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                
                <!-- Property Stats -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">إحصائيات العقار</h5>
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <div class="p-2 bg-light rounded-3">
                                    <i class="fas fa-eye text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->views_count ?? 0 }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">مشاهدة</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-light rounded-3">
                                    <i class="fas fa-heart text-danger mb-2"></i>
                                    <div class="fw-bold">{{ $property->favorites_count ?? 0 }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">مفضلة</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-light rounded-3">
                                    <i class="fas fa-comment-dots text-success mb-2"></i>
                                    <div class="fw-bold">{{ $property->inquiries_count ?? 0 }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">استفسار</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Developer/Company Info -->
                @if($property->developer || ($property->company && (!$property->agent || $property->company->id !== $property->agent->company_id)))
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">{{ $property->developer ? 'المطور العقاري' : 'الشركة المعلنة' }}</h5>
                        <div class="d-flex align-items-center mb-3">
                            @php 
                                $entity = $property->developer ?? $property->company;
                            @endphp
                            <div class="rounded bg-light d-flex align-items-center justify-content-center me-3 border" style="width: 50px; height: 50px; overflow: hidden;">
                                @if($entity->logo)
                                    <img src="{{ asset('storage/' . $entity->logo) }}" class="w-100 h-100 object-fit-cover" alt="{{ $entity->name }}">
                                @else
                                    <i class="fas fa-building text-secondary"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $entity->name }}</h6>
                                @if($entity->website)
                                    <a href="{{ $entity->website }}" target="_blank" class="text-muted small text-decoration-none">
                                        <i class="fas fa-link me-1"></i> الموقع الرسمي
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Agent Contact -->
                @if($property->agent)
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">تواصل مع الوكيل</h5>
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 border" style="width: 60px; height: 60px;">
                                    @if($property->agent->photo)
                                        <img src="{{ asset('storage/' . $property->agent->photo) }}" class="rounded-circle w-100 h-100 object-fit-cover" alt="{{ $property->agent->name }}">
                                    @else
                                        <i class="fas fa-user fa-lg text-secondary"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $property->agent->name }}</h6>
                                    <p class="text-muted small mb-0">{{ $property->company->name ?? 'وسيط عقاري' }}</p>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="tel:{{ $property->agent->phone }}" class="btn btn-outline-primary">
                                    <i class="fas fa-phone me-2"></i> اتصال
                                </a>
                                <a href="https://wa.me/{{ $property->agent->phone }}" target="_blank" class="btn btn-success text-white">
                                    <i class="fab fa-whatsapp me-2"></i> واتساب
                                </a>
                                <a href="mailto:{{ $property->agent->email }}" class="btn btn-light border">
                                    <i class="fas fa-envelope me-2"></i> بريد إلكتروني
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Schedule Tour -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">حجز موعد للمعاينة</h5>
                        <p class="text-muted small mb-3">هل أنت مهتم بهذا العقار؟ املأ النموذج أدناه لطلب معاينة.</p>
                        <form action="#" method="POST"> <!-- Route to be implemented -->
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="الاسم الكامل" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="البريد الإلكتروني" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" placeholder="رقم الهاتف" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="ملاحظات إضافية..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">إرسال الطلب</button>
                        </form>
                    </div>
                </div>

                <!-- Location Map Placeholder -->
                @if($property->latitude && $property->longitude)
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-0 rounded-3 overflow-hidden">
                            <iframe 
                                width="100%" 
                                height="300" 
                                frameborder="0" 
                                style="border:0" 
                                src="https://maps.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}&hl=ar&z=14&output=embed" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3 bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner">
                            @if($property->media)
                                @foreach($property->media as $index => $media)
                                    @php
                                        $fullPath = \Illuminate\Support\Str::startsWith($media->file_path, 'properties/')
                                                    ? 'storage/' . $media->file_path
                                                    : 'storage/properties/' . $media->file_path;
                                    @endphp
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset($fullPath) }}" class="d-block w-100" style="max-height: 85vh; object-fit: contain;" alt="صورة المعرض {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 20px;"></span>
                            <span class="visually-hidden">السابق</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 20px;"></span>
                            <span class="visually-hidden">التالي</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
// Gallery Modal Logic
function openGallery(index) {
    const galleryModal = new bootstrap.Modal(document.getElementById('galleryModal'));
    const carouselElement = document.getElementById('galleryCarousel');
    const carousel = new bootstrap.Carousel(carouselElement);
    carousel.to(index);
    galleryModal.show();
}

// AI Prediction Logic
document.getElementById('ai-predict-btn')?.addEventListener('click', async function() {
    const btn = this;
    const loading = document.getElementById('ai-loading');
    const result = document.getElementById('ai-result');
    
    btn.classList.add('d-none');
    loading.classList.remove('d-none');
    result.classList.add('d-none');

    try {
        const response = await fetch('/api/ai/price-prediction/property/{{ $property->id }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('pred-price').innerText = 
                new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'EGP', maximumSignificantDigits: 3 }).format(data.prediction.price_min) + 
                ' - ' + 
                new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'EGP', maximumSignificantDigits: 3 }).format(data.prediction.price_max);
            
            const conf = Math.round(data.prediction.confidence * 100);
            document.getElementById('pred-confidence').innerText = conf;
            
            const score = data.prediction.investment_score;
            document.getElementById('pred-score-val').innerText = score + '/10';
            document.getElementById('pred-score-bar').style.width = (score * 10) + '%';
            
            const risk = data.prediction.risk_level;
            const riskBadge = document.getElementById('pred-risk');
            riskBadge.innerText = (risk === 'low' ? 'مخاطرة منخفضة' : (risk === 'medium' ? 'مخاطرة متوسطة' : 'مخاطرة عالية'));
            riskBadge.className = 'badge ' + (risk === 'low' ? 'bg-success' : (risk === 'medium' ? 'bg-warning' : 'bg-danger'));

            loading.classList.add('d-none');
            result.classList.remove('d-none');
        } else {
            alert('فشل التحليل. يرجى المحاولة لاحقاً.');
            btn.classList.remove('d-none');
            loading.classList.add('d-none');
        }
    } catch (error) {
        console.error(error);
        alert('حدث خطأ في الشبكة.');
        btn.classList.remove('d-none');
        loading.classList.add('d-none');
    }
});
</script>
@endpush
@endsection
