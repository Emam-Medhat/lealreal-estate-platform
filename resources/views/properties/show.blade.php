@extends('layouts.app')

@section('title', $property->title . ' | ' . config('app.name', 'منصة العقارات'))

@section('content')
<div style="background: #f8f9fa; min-height: 100vh;">
    <!-- Header Section -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-3">
                        <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 0.9rem;">
                            {{ $property->listing_type == 'sale' ? 'للبيع' : 'للإيجار' }}
                        </span>
                        @if($property->featured)
                            <span style="background: #f39c12; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; margin-right: 10px;">
                                <i class="fas fa-star"></i> مميز
                            </span>
                        @endif
                    </div>
                    <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 15px;">{{ $property->title }}</h1>
                    <div style="font-size: 1.1rem; opacity: 0.9;">
                        <i class="fas fa-map-marker-alt"></i> {{ $property->city ?? 'غير محدد' }}, {{ $property->country ?? '' }}
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    @if($property->price)
                        <div style="background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px;">
                            <div style="font-size: 2.5rem; font-weight: 700;">{{ number_format($property->price) }}</div>
                            <div style="font-size: 1.1rem; opacity: 0.8;">{{ $property->currency ?? 'ريال' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Left Column - Images and Details -->
            <div class="col-lg-8">
                <!-- Property Images -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <div class="card-body p-0">
                        <div style="position: relative; height: 400px; overflow: hidden; border-radius: 15px;">
                            @if($property->media && $property->media->first())
                                <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     alt="{{ $property->title }}"
                                     onerror="this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                            @else
                                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     alt="{{ $property->title }}">
                            @endif
                        </div>
                        
                        <!-- Image Gallery -->
                        @if($property->media && $property->media->count() > 1)
                            <div class="p-3">
                                <div class="row g-2">
                                    @foreach($property->media->take(4) as $media)
                                        <div class="col-3">
                                            <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                 style="width: 100%; height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer;"
                                                 alt="Property image">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Property Details -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">تفاصيل العقار</h3>
                        
                        <div class="row text-center mb-4">
                            @if($property->bedrooms > 0)
                                <div class="col-md-3 col-6 mb-3">
                                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                        <i class="fas fa-bed" style="color: #3498db; font-size: 2rem;"></i>
                                        <div style="font-size: 1.5rem; font-weight: 600; color: #2c3e50;">{{ $property->bedrooms }}</div>
                                        <div style="color: #7f8c8d;">غرف نوم</div>
                                    </div>
                                </div>
                            @endif
                            @if($property->bathrooms > 0)
                                <div class="col-md-3 col-6 mb-3">
                                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                        <i class="fas fa-bath" style="color: #3498db; font-size: 2rem;"></i>
                                        <div style="font-size: 1.5rem; font-weight: 600; color: #2c3e50;">{{ $property->bathrooms }}</div>
                                        <div style="color: #7f8c8d;">حمام</div>
                                    </div>
                                </div>
                            @endif
                            @if($property->area > 0)
                                <div class="col-md-3 col-6 mb-3">
                                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                        <i class="fas fa-expand" style="color: #3498db; font-size: 2rem;"></i>
                                        <div style="font-size: 1.5rem; font-weight: 600; color: #2c3e50;">{{ $property->area }}</div>
                                        <div style="color: #7f8c8d;">متر مربع</div>
                                    </div>
                                </div>
                            @endif
                            @if($property->floors)
                                <div class="col-md-3 col-6 mb-3">
                                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                        <i class="fas fa-building" style="color: #3498db; font-size: 2rem;"></i>
                                        <div style="font-size: 1.5rem; font-weight: 600; color: #2c3e50;">{{ $property->floors }}</div>
                                        <div style="color: #7f8c8d;">طابق</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Complete Property Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px;">المعلومات الأساسية</h5>
                                <div style="color: #7f8c8d;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                        <span><i class="fas fa-home"></i> نوع العقار:</span>
                                        <strong style="color: #2c3e50;">{{ $property->propertyType?->name ?? 'غير محدد' }}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                        <span><i class="fas fa-tag"></i> الغرض:</span>
                                        <strong style="color: #2c3e50;">{{ $property->listing_type == 'sale' ? 'للبيع' : 'للإيجار' }}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                        <span><i class="fas fa-barcode"></i> كود العقار:</span>
                                        <strong style="color: #2c3e50;">{{ $property->property_code }}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                        <span><i class="fas fa-calendar"></i> سنة البناء:</span>
                                        <strong style="color: #2c3e50;">{{ $property->year_built ?? 'غير محدد' }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px;">المساحة والأبعاد</h5>
                                <div style="color: #7f8c8d;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                        <span><i class="fas fa-expand-arrows-alt"></i> المساحة الإجمالية:</span>
                                        <strong style="color: #2c3e50;">{{ $property->area }} {{ $property->area_unit ?? 'متر مربع' }}</strong>
                                    </div>
                                    @if($property->bedrooms > 0)
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                            <span><i class="fas fa-bed"></i> عدد الغرف:</span>
                                            <strong style="color: #2c3e50;">{{ $property->bedrooms }} غرفة</strong>
                                        </div>
                                    @endif
                                    @if($property->bathrooms > 0)
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                            <span><i class="fas fa-bath"></i> عدد الحمامات:</span>
                                            <strong style="color: #2c3e50;">{{ $property->bathrooms }} حمام</strong>
                                        </div>
                                    @endif
                                    @if($property->floors)
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                            <span><i class="fas fa-layer-group"></i> عدد الطوابق:</span>
                                            <strong style="color: #2c3e50;">{{ $property->floors }} طابق</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($property->description)
                            <div style="color: #2c3e50; line-height: 1.8; margin-top: 30px;">
                                <h4 style="font-weight: 600; margin-bottom: 15px;">الوصف</h4>
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                    <p style="margin: 0;">{{ $property->description }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Additional Features -->
                        @if($property->amenities)
                            <div style="margin-top: 30px;">
                                <h4 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px;">المرافق والتسهيلات</h4>
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                    <p style="margin: 0; color: #7f8c8d;">{{ $property->amenities }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Nearby Places -->
                        @if($property->nearby_places)
                            <div style="margin-top: 30px;">
                                <h4 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px;">أماكن قريبة</h4>
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                    <p style="margin: 0; color: #7f8c8d;">{{ $property->nearby_places }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Location -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">الموقع</h3>
                        <div style="color: #7f8c8d;">
                            <p style="margin-bottom: 10px;"><i class="fas fa-map-marker-alt"></i> {{ $property->address }}</p>
                            <p style="margin-bottom: 10px;"><i class="fas fa-city"></i> {{ $property->city }}</p>
                            <p><i class="fas fa-globe"></i> {{ $property->country }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions and Info -->
            <div class="col-lg-4">
                <!-- Contact Card -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); position: sticky; top: 20px;">
                    <div class="card-body p-4">
                        <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">تواصل معنا</h3>
                        
                        <div class="d-grid gap-3">
                            <button class="btn w-100" style="background: #3498db; color: white; border: none; border-radius: 10px; padding: 12px;">
                                <i class="fas fa-phone"></i> اتصل الآن
                            </button>
                            <button class="btn w-100" style="background: #25d366; color: white; border: none; border-radius: 10px; padding: 12px;">
                                <i class="fab fa-whatsapp"></i> واتساب
                            </button>
                            <button class="btn w-100 btn-outline-primary" style="border-radius: 10px; padding: 12px;">
                                <i class="fas fa-envelope"></i> إرسال رسالة
                            </button>
                        </div>

                        <hr style="margin: 20px 0;">

                        <div class="text-center">
                            <button class="btn btn-outline-danger w-100" style="border-radius: 10px; padding: 12px;">
                                <i class="fas fa-heart"></i> إضافة للمفضلة
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Property Info -->
                <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">معلومات العقار</h3>
                        
                        <div style="color: #7f8c8d;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span><i class="fas fa-barcode"></i> كود العقار:</span>
                                <strong style="color: #2c3e50;">{{ $property->property_code }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span><i class="fas fa-home"></i> نوع العقار:</span>
                                <strong style="color: #2c3e50;">{{ $property->propertyType?->name ?? 'غير محدد' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span><i class="fas fa-tag"></i> الغرض:</span>
                                <strong style="color: #2c3e50;">{{ $property->listing_type == 'sale' ? 'للبيع' : 'للإيجار' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span><i class="fas fa-calendar"></i> سنة البناء:</span>
                                <strong style="color: #2c3e50;">{{ $property->year_built ?? 'غير محدد' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span><i class="fas fa-expand-arrows-alt"></i> المساحة:</span>
                                <strong style="color: #2c3e50;">{{ $property->area }} {{ $property->area_unit ?? 'متر مربع' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span><i class="fas fa-eye"></i> عدد المشاهدات:</span>
                                <strong style="color: #2c3e50;">{{ $property->views_count ?? 0 }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span><i class="fas fa-heart"></i> المفضلة:</span>
                                <strong style="color: #2c3e50;">{{ $property->favorites_count ?? 0 }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0;">
                                <span><i class="fas fa-info-circle"></i> الحالة:</span>
                                <strong style="color: #27ae60;">{{ $property->status == 'active' ? 'متاح' : 'غير متاح' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agent Information -->
                @if($property->agent)
                    <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">معلومات الوكيل</h3>
                            
                            <div class="text-center">
                                @if($property->agent->avatar)
                                    <img src="{{ asset('storage/' . $property->agent->avatar) }}" 
                                         style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 15px; object-fit: cover;"
                                         alt="{{ $property->agent->name }}">
                                @else
                                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8f9fa; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user" style="font-size: 2rem; color: #7f8c8d;"></i>
                                    </div>
                                @endif
                                <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">{{ $property->agent->name }}</h5>
                                <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 15px;">وكيل عقارات</p>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
                                        <i class="fas fa-phone"></i> اتصل بالوكيل
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" style="border-radius: 8px;">
                                        <i class="fab fa-whatsapp"></i> واتساب
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Virtual Tour -->
                @if($property->virtual_tour_url)
                    <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">جولة افتراضية</h3>
                            
                            <a href="{{ $property->virtual_tour_url }}" target="_blank" class="btn btn-primary w-100" style="border-radius: 10px;">
                                <i class="fas fa-vr-cardboard"></i> بدء الجولة الافتراضية
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Video Tour -->
                @if($property->video_url)
                    <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">فيديو العقار</h3>
                            
                            <a href="{{ $property->video_url }}" target="_blank" class="btn btn-danger w-100" style="border-radius: 10px;">
                                <i class="fas fa-play"></i> مشاهدة الفيديو
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Similar Properties -->
                @if(isset($similarProperties) && $similarProperties->count() > 0)
                    <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px;">عقارات مشابهة</h3>
                            
                            @foreach($similarProperties->take(3) as $similar)
                                <div class="d-flex mb-3" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
                                    @if($similar->media && $similar->media->first())
                                        <img src="{{ asset('storage/' . $similar->media->first()->file_path) }}" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-left: 15px;"
                                             alt="{{ $similar->title }}">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-left: 15px;"
                                             alt="{{ $similar->title }}">
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">{{ Str::limit($similar->title, 30) }}</h6>
                                        @if($similar->price)
                                            <div style="color: #3498db; font-weight: 600;">{{ number_format($similar->price) }} {{ $similar->currency ?? 'ريال' }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
