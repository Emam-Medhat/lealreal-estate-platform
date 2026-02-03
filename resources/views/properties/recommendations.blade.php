@extends('layouts.app')

@section('title', 'توصيات العقارات | ' . config('app.name', 'منصة العقارات'))

@section('content')
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">توصيات العقارات</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">اكتشف أفضل العقارات التي تناسب احتياجاتك وتفضيلاتك</p>
            <div class="mt-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 10px 20px;">
                    <i class="fas fa-arrow-left ml-2"></i> العودة للوحة التحكم
                </a>
            </div>
        </div>

        @if($recommendedProperties->count() > 0)
            <div class="row">
                @foreach($recommendedProperties as $property)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                            <!-- Property Image Section -->
                            @if($property->media->count() > 0)
                                <div class="position-relative overflow-hidden" style="border-radius: 15px 15px 0 0;">
                                    <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" 
                                         class="card-img-top w-100" 
                                         alt="{{ $property->title }}"
                                         style="height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                                    
                                    <!-- Badges -->
                                    <div class="position-absolute top-0 start-0 p-3">
                                        @if($property->featured)
                                            <span class="badge" style="background: #3498db; border: none; padding: 5px 12px; font-weight: 600;">مميز</span>
                                        @endif
                                    </div>
                                    <div class="position-absolute top-0 end-0 p-3">
                                        @if($property->listing_type === 'rent')
                                            <span class="badge" style="background: #e74c3c; border: none; padding: 5px 12px; font-weight: 600;">للإيجار</span>
                                        @else
                                            <span class="badge" style="background: #27ae60; border: none; padding: 5px 12px; font-weight: 600;">للبيع</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Overlay Effect -->
                                    <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                                        <div class="text-white">
                                            <small class="d-block"><i class="fas fa-eye"></i> {{ $property->views_count ?? 0 }} مشاهدة</small>
                                            <small class="d-block"><i class="fas fa-heart"></i> {{ $property->favorites_count ?? 0 }} مفضلة</small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 250px; border-radius: 15px 15px 0 0;">
                                    <div class="text-center">
                                        <i class="fas fa-home fa-4x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد صور متاحة</p>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Property Details -->
                            <div class="card-body p-4">
                                <!-- Property Title -->
                                <h5 class="card-title mb-3" style="color: #2c3e50; font-weight: 600;">
                                    {{ Str::limit($property->title, 50) }}
                                </h5>
                                
                                <!-- Location -->
                                <div class="mb-3">
                                    <i class="fas fa-map-marker-alt text-primary ml-2"></i>
                                    <span style="color: #7f8c8d;">{{ $property->city ?? 'غير محدد' }}, {{ $property->state ?? 'غير محدد' }}</span>
                                </div>
                                
                                <!-- Price -->
                                <div class="text-center mb-4">
                                    @if($property->price)
                                        <h4 class="mb-0" style="color: #3498db; font-weight: 700; font-size: 1.5rem;">
                                            @if($property->listing_type === 'rent')
                                                {{ number_format($property->price) }} ريال/شهر
                                            @else
                                                {{ number_format($property->price) }} ريال
                                            @endif
                                        </h4>
                                        <small class="text-muted">{{ $property->currency ?? 'ريال' }}</small>
                                    @else
                                        <p class="text-muted">السعر غير محدد</p>
                                    @endif
                                </div>
                                
                                <!-- Property Stats -->
                                <div class="row text-center mb-4" style="background: #f8f9fa; border-radius: 10px; padding: 15px;">
                                    <div class="col-4">
                                        <div style="padding: 10px; border-radius: 8px; background: white; transition: all 0.3s ease;">
                                            <small style="color: #7f8c8d; font-size: 0.75rem; font-weight: 600;">غرف نوم</small>
                                            <div style="font-size: 1.1rem; font-weight: 700; color: #2c3e50;">{{ $property->bedrooms ?? '---' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div style="padding: 10px; border-radius: 8px; background: white; transition: all 0.3s ease;">
                                            <small style="color: #7f8c8d; font-size: 0.75rem; font-weight: 600;">حمامات</small>
                                            <div style="font-size: 1.1rem; font-weight: 700; color: #2c3e50;">{{ $property->bathrooms ?? '---' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div style="padding: 10px; border-radius: 8px; background: white; transition: all 0.3s ease;">
                                            <small style="color: #7f8c8d; font-size: 0.75rem; font-weight: 600;">المساحة</small>
                                            <div style="font-size: 1.1rem; font-weight: 700; color: #2c3e50;">{{ $property->area ?? '---' }}{{ $property->area_unit ?? 'م²' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Agent Info -->
                                <div class="d-flex justify-content-between align-items-center" style="background: #f8f9fa; border-radius: 10px; padding: 12px;">
                                    <div>
                                        <small class="text-muted d-block">الوكيل</small>
                                        <strong style="color: #3498db;">{{ $property->agent->name ?? 'غير محدد' }}</strong>
                                    </div>
                                    <a href="{{ route('properties.show', $property->id) }}" class="btn btn-primary" style="border-radius: 10px; padding: 8px 20px; font-weight: 600;">
                                        <i class="fas fa-eye ml-1"></i> عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5" style="background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-home fa-4x text-muted mb-3" style="opacity: 0.5;"></i>
                <h3 class="text-muted mb-3">لا توجد توصيات متاحة</h3>
                <p class="text-muted mb-4">ابدأ في تصفح العقارات للحصول على توصيات مخصصة تناسب احتياجاتك.</p>
                <a href="{{ route('properties.index') }}" class="btn btn-primary btn-lg" style="border-radius: 10px; padding: 12px 30px;">
                    <i class="fas fa-search ml-2"></i> تصفح العقارات
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .property-stats .col-4 > div:hover {
        background: #3498db !important;
        color: white !important;
        transform: scale(1.05);
    }
    
    .property-stats .col-4 > div:hover * {
        color: white !important;
    }
</style>
@endsection
