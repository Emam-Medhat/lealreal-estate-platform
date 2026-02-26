@extends('layouts.app')

@section('title', $property->title . ' | ' . config('app.name', 'منصة العقارات'))

@section('content')
<!-- Debug Section (Remove in production) -->
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
    <h4 class="text-yellow-800 font-bold mb-2">Debug Information:</h4>
    <div class="text-sm text-yellow-700">
        <p>Property ID: {{ $property->id }}</p>
        <p>Media Count: {{ $property->media ? $property->media->count() : 0 }}</p>
        @if($property->media && $property->media->count() > 0)
            <p>First Media File Path: {{ $property->media->first()->file_path }}</p>
            @php
                $mainImage = $property->media->first();
                if($mainImage->file_path) {
                    $imagePath = 'storage/' . $mainImage->file_path;
                } else {
                    $imagePath = null;
                }
            @endphp
            <p>Generated Image Path: {{ $imagePath }}</p>
            <p>Full Asset URL: {{ asset($imagePath) }}</p>
            <p>File Exists: @file_exists(public_path($imagePath)) ? 'Yes' : 'No'</p>
            <p>Storage Link: @if(php_uname('s') === 'Windows NT')
                {{ base_path('storage\app\public') . '\\' . str_replace('/', '\\', $mainImage->file_path) }}
            @else
                {{ base_path('storage/app/public') . '/' . $mainImage->file_path }}
            @endif</p>
        @endif
    </div>
</div>

<div class="min-h-screen bg-gray-50">
    <!-- Hero Gallery Section -->
    <div class="relative h-[500px] overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/50 via-blue-700/20 to-transparent z-10"></div>

        @if($property->media && $property->media->count() > 0)
            @php
                $mainImage = $property->media->first();
                // إصلاح مسار الصورة - الصور تخزن في properties/{propertyId}/filename
                if($mainImage->file_path) {
                    // المسار الصحيح هو storage/properties/{propertyId}/filename
                    $imagePath = 'storage/' . $mainImage->file_path;
                } else {
                    $imagePath = null;
                }
            @endphp
            @if($imagePath)
                <img src="{{ asset($imagePath) }}"
                     class="w-full h-full object-cover"
                     alt="{{ $property->title }}"
                     onclick="openGallery(0)"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/800x500/e5e7eb/6b7280?text=صورة+غير+متاحة';">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                    <div class="text-center text-white">
                        <i class="fas fa-exclamation-triangle text-6xl mb-4 opacity-50"></i>
                        <p class="text-xl opacity-75">مسار الصورة غير صحيح</p>
                    </div>
                </div>
            @endif
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-image text-6xl mb-4 opacity-50"></i>
                    <p class="text-xl opacity-75">لا توجد صور للعقار</p>
                </div>
            </div>
        @endif

        <!-- Floating Badges -->
        <div class="absolute top-6 left-6 z-20 flex flex-wrap gap-3">
            <span class="px-4 py-2 rounded-full text-sm font-bold shadow-lg backdrop-blur-sm {{ $property->listing_type == 'sale' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white' : 'bg-gradient-to-r from-green-600 to-green-700 text-white' }}">
                <i class="fas fa-{{ $property->listing_type == 'sale' ? 'tag' : 'home' }} ml-1"></i>
                {{ $property->listing_type == 'sale' ? 'للبيع' : 'للإيجار' }}
            </span>
            @if($property->featured)
                <span class="px-4 py-2 rounded-full text-sm font-bold shadow-lg bg-gradient-to-r from-amber-500 to-amber-600 text-white">
                    <i class="fas fa-star ml-1"></i> مميز
                </span>
            @endif
        </div>

        <!-- Side Images -->
        @if($property->media && $property->media->count() > 1)
            <div class="absolute top-6 right-6 z-20 w-32 space-y-3">
                @foreach($property->media->skip(1)->take(2) as $index => $media)
                    @php
                        // إصلاح مسار الصور الجانبية
                        if($media->file_path) {
                            $subImagePath = 'storage/' . $media->file_path;
                        } else {
                            $subImagePath = null;
                        }
                    @endphp
                    @if($subImagePath)
                        <div class="relative group cursor-pointer overflow-hidden rounded-xl shadow-lg"
                             onclick="openGallery({{ $index + 1 }})">
                            <img src="{{ asset($subImagePath) }}"
                                 class="w-full h-24 object-cover transition-transform duration-300 group-hover:scale-110"
                                 alt="صورة العقار {{ $index + 2 }}"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/128x96/e5e7eb/6b7280?text=خطأ';">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center text-sm text-gray-600 mb-6">
            <a href="{{ route('optimized.properties.index') }}" class="text-blue-600">العقارات</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">{{ $property->title }}</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Title & Price Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="p-8">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
                            <div class="flex-1">
                                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3 leading-tight">
                                    {{ $property->title }}
                                </h1>
                                <div class="flex items-center text-gray-600 mb-4">
                                    <i class="fas fa-map-marker-alt text-red-500 ml-2 text-lg"></i>
                                    <span class="text-lg">{{ $property->address ?? '' }} {{ $property->city ? '، ' . $property->city : '' }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-heart ml-2 text-red-500"></i> حفظ
                                    </button>
                                    <button class="px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-share-alt ml-2 text-blue-500"></i> مشاركة
                                    </button>
                                </div>
                            </div>
                            <div class="text-center lg:text-left">
                                <div class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    {{ number_format($property->price, 0) }}
                                    <span class="text-lg lg:text-xl text-gray-600">{{ $property->currency ?? 'ريال' }}</span>
                                </div>
                                @if($property->price_per_sqm)
                                    <div class="text-sm text-gray-500 mt-2">
                                        {{ number_format($property->price_per_sqm, 0) }} ريال / م²
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Key Features Grid -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 text-center border border-blue-100">
                                <i class="fas fa-bed text-2xl text-blue-600 mb-2"></i>
                                <div class="text-xl font-bold text-gray-900">{{ $property->bedrooms ?? $property->details->bedrooms ?? '-' }}</div>
                                <div class="text-sm text-gray-600">غرف نوم</div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 text-center border border-green-100">
                                <i class="fas fa-bath text-2xl text-green-600 mb-2"></i>
                                <div class="text-xl font-bold text-gray-900">{{ $property->bathrooms ?? $property->details->bathrooms ?? '-' }}</div>
                                <div class="text-sm text-gray-600">حمامات</div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 text-center border border-purple-100">
                                <i class="fas fa-ruler-combined text-2xl text-purple-600 mb-2"></i>
                                <div class="text-xl font-bold text-gray-900">{{ $property->area ?? $property->details->area ?? '-' }}</div>
                                <div class="text-sm text-gray-600">متر مربع</div>
                            </div>
                            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 text-center border border-amber-100">
                                <i class="fas fa-building text-2xl text-amber-600 mb-2"></i>
                                <div class="text-xl font-bold text-gray-900">{{ $property->propertyType->name ?? 'غير محدد' }}</div>
                                <div class="text-sm text-gray-600">النوع</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 ml-3"></i>
                            وصف العقار
                        </h3>
                        <div class="text-gray-700 leading-relaxed text-lg whitespace-pre-line bg-gray-50 rounded-xl p-6 border border-gray-200">
                            {{ $property->description ?? 'لا يوجد وصف متاح.' }}
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                @if($property->amenities && $property->amenities->count() > 0)
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-star text-amber-500 ml-3"></i>
                                المرافق والخدمات
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($property->amenities as $amenity)
                                    <div class="flex items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100 hover:shadow-lg transition-all duration-300">
                                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center ml-3">
                                            <i class="{{ $amenity->icon ?? 'fas fa-check' }} text-green-600"></i>
                                        </div>
                                        <span class="text-gray-800 font-medium">{{ $amenity->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-brain text-gray-700 ml-3"></i>
                            ذكاء العقار
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">درجة الاستثمار</div>
                            <div class="text-2xl font-bold">{{ $property->investment_score ?? '-' }}/10</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">السعر المتوقع</div>
                            <div class="text-2xl font-bold">{{ $property->predicted_price ? number_format($property->predicted_price, 0) : '-' }}</div>
                            @if($property->overpriced_flag)
                                <div class="mt-1 text-xs text-red-600">سعر أعلى من المتوقع</div>
                            @endif
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">الثقة</div>
                            <div class="text-2xl font-bold">{{ $property->confidence_score ? number_format($property->confidence_score, 0) . '%' : '-' }}</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">درجة الاحتيال</div>
                            <div class="text-2xl font-bold">{{ $property->fraud_score ?? 0 }}</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">مستوى الثقة</div>
                            <div class="text-2xl font-bold">{{ strtoupper($property->trust_level ?? '-') }}</div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-500 mb-1">العائد المتوقع</div>
                            <div class="text-2xl font-bold">{{ $property->expected_appreciation ? number_format($property->expected_appreciation, 1) . '%' : '-' }}</div>
                        </div>
                    </div>
                </div>

                @if($property->latitude && $property->longitude)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-map ml-3"></i>
                            موقع العقار
                        </h3>
                    </div>
                    <div class="p-6">
                        <div id="property-map" class="w-full h-80 rounded-xl border"></div>
                    </div>
                </div>
                @endif

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="p-6 flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-user-astronaut text-purple-600 ml-3"></i>
                            توصيات لك
                        </h3>
                        <button id="load-recos" class="px-3 py-2 text-sm rounded bg-purple-50 text-purple-700 hover:bg-purple-100">
                            تحديث
                        </button>
                    </div>
                    <div id="reco-list" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                </div>

                <!-- Property Details -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-info-circle ml-3"></i>
                            تفاصيل العقار
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-hashtag text-blue-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">رقم العقار</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->property_code ?? $property->id }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-building text-purple-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">نوع العقار</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->propertyType->name ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-check-circle text-green-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">الحالة</div>
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-bold {{ $property->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $property->status == 'active' ? 'متاح' : $property->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-ruler-combined text-indigo-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">مساحة الأرض</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->land_area ? number_format($property->land_area) . ' ' . ($property->land_area_unit ?? 'م²') : '-' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-calendar-alt text-amber-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">سنة البناء</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->year_built ?? $property->details->year_built ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-car text-red-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">مواقف السيارات</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->parking_spaces ?? $property->details->parking_spaces ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-layer-group text-teal-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">عدد الطوابق</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->floors ?? $property->details->floors ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-clock text-pink-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">تاريخ العرض</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->listed_date ? \Carbon\Carbon::parse($property->listed_date)->format('Y-m-d') : $property->created_at->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                @if($property->energy_rating)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-bolt text-yellow-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">تصنيف الطاقة</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ $property->energy_rating }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Details -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-coins ml-3"></i>
                            تفاصيل مالية
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($property->listing_type == 'rent' || $property->rental_price)
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                        <span class="text-gray-600 font-medium">سعر الإيجار</span>
                                        <span class="font-bold text-gray-900 text-lg">{{ number_format($property->rental_price ?? $property->price, 2) }} {{ $property->currency ?? 'ريال' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                        <span class="text-gray-600 font-medium">دورية الدفع</span>
                                        <span class="font-bold text-gray-900 text-lg">{{ $property->rental_frequency ?? 'شهري' }}</span>
                                    </div>
                                </div>
                            @endif

                            @if($property->service_charges)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">رسوم الخدمات</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ number_format($property->service_charges, 2) }}</span>
                                </div>
                            @endif

                            @if($property->maintenance_fees)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">رسوم الصيانة</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ number_format($property->maintenance_fees, 2) }}</span>
                                </div>
                            @endif

                            @if($property->property_tax)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">الضريبة العقارية</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ number_format($property->property_tax, 2) }}</span>
                                </div>
                            @endif

                            @if($property->insurance_cost)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">تكلفة التأمين</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ number_format($property->insurance_cost, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Legal & Ownership Details -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-gavel ml-3"></i>
                            تفاصيل قانونية وملكية
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($property->ownership_type)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">نوع الملكية</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ $property->ownership_type }}</span>
                                </div>
                            @endif

                            @if($property->registration_number)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">رقم التسجيل</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ $property->registration_number }}</span>
                                </div>
                            @endif

                            @if($property->deed_number)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">رقم الصك (Deed)</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ $property->deed_number }}</span>
                                </div>
                            @endif

                            @if($property->land_use)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">استخدام الأرض</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ $property->land_use }}</span>
                                </div>
                            @endif

                            @if($property->zoning_type)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">نوع المنطقة</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ $property->zoning_type }}</span>
                                </div>
                            @endif

                            @if($property->occupancy_permit)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">تصريح الإشغال</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ $property->occupancy_permit }}</span>
                                </div>
                            @endif

                            @if($property->building_permit)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">رقم رخصة البناء</span>
                                    <span class="font-bold text-gray-900 text-lg">{{ $property->building_permit }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Important Dates -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-600 to-orange-600 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-calendar-alt ml-3"></i>
                            تواريخ هامة
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($property->listed_date)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="far fa-calendar-alt text-blue-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">تاريخ الإدراج</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ \Carbon\Carbon::parse($property->listed_date)->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($property->available_from)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="far fa-calendar-check text-green-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">متاح من تاريخ</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ \Carbon\Carbon::parse($property->available_from)->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($property->expiry_date)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="far fa-calendar-times text-red-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">تاريخ الانتهاء</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ \Carbon\Carbon::parse($property->expiry_date)->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($property->sold_date)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <i class="fas fa-handshake text-purple-600 ml-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm text-gray-500">تاريخ البيع</div>
                                        <div class="font-bold text-gray-900 text-lg">{{ \Carbon\Carbon::parse($property->sold_date)->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Utilities & Systems -->
                @if($property->heating_system || $property->water_heating || $property->insulation || $property->ventilation || $property->air_conditioning || $property->solar_panels || $property->double_glazing)
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white p-6">
                            <h3 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-cogs ml-3"></i>
                                المرافق والأنظمة
                            </h3>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($property->heating_system)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-fire text-red-600 ml-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">نظام التدفئة</div>
                                            <div class="font-bold text-gray-900 text-lg">{{ $property->heating_system }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($property->air_conditioning)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-snowflake text-blue-600 ml-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">تكييف الهواء</div>
                                            <div class="font-bold text-gray-900 text-lg">{{ $property->air_conditioning }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($property->water_heating)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-water text-cyan-600 ml-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">تسخين المياه</div>
                                            <div class="font-bold text-gray-900 text-lg">{{ $property->water_heating }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($property->insulation)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-shield-alt text-green-600 ml-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">العزل</div>
                                            <div class="font-bold text-gray-900 text-lg">{{ $property->insulation }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($property->ventilation)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-wind text-purple-600 ml-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">التهوية</div>
                                            <div class="font-bold text-gray-900 text-lg">{{ $property->ventilation }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($property->solar_panels)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-solar-panel text-yellow-600 ml-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">ألواح شمسية</div>
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-bold">متوفر</span>
                                        </div>
                                    </div>
                                @endif

                                @if($property->double_glazing)
                                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                        <i class="fas fa-window-maximize text-indigo-600 ml-3 text-lg"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">زجاج مزدوج</div>
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">متوفر</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Agent Information -->
                @if($property->agent)
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-600 to-green-600 text-white p-6">
                            <h3 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-user-tie ml-3"></i>
                                {{ __('properties.agent_info') }}
                            </h3>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center ml-4 overflow-hidden border-4 border-gray-200">
                                    @if($property->agent->photo)
                                        <img src="{{ asset('storage/' . $property->agent->photo) }}" class="w-full h-full object-cover" alt="{{ $property->agent->name }}">
                                    @else
                                        <i class="fas fa-user text-gray-400 text-3xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-xl">{{ $property->agent->name }}</div>
                                    <div class="text-gray-600">{{ $property->company->name ?? __('properties.real_estate_broker') }}</div>
                                    <div class="flex items-center mt-2">
                                        <div class="flex items-center text-yellow-500 ml-2">
                                            @for($i = 0; $i < 5; $i++)
                                                <i class="fas fa-star {{ $i == 0 ? '' : 'text-xs' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-gray-600 ml-2">({{ $property->agent->rating ?? 4.5 }}/5.0)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <a href="tel:{{ $property->agent->phone }}" class="flex items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-phone text-blue-600 ml-3"></i>
                                    <span class="font-medium text-gray-900">{{ $property->agent->phone }}</span>
                                </a>

                                <a href="mailto:{{ $property->agent->email }}" class="flex items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                                    <i class="fas fa-envelope text-green-600 ml-3"></i>
                                    <span class="font-medium text-gray-900">{{ $property->agent->email }}</span>
                                </a>

                                @if($property->agent->license_number)
                                    <div class="flex items-center p-4 bg-purple-50 rounded-xl">
                                        <i class="fas fa-certificate text-purple-600 ml-3"></i>
                                        <div>
                                            <div class="text-sm text-gray-500">{{ __('properties.license_number') }}</div>
                                            <div class="font-bold text-gray-900">{{ $property->agent->license_number }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if($property->agent->specializations)
                                    <div class="p-4 bg-amber-50 rounded-xl">
                                        <div class="text-sm text-gray-500 mb-2">{{ __('properties.specializations') }}</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($property->agent->specializations as $spec)
                                                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">{{ $spec }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Materials & Features -->
                @if(!empty($property->materials) && is_array($property->materials))
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-hammer text-orange-600 ml-3"></i>
                                {{ __('properties.building_materials') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($property->materials as $material)
                                    <span class="px-4 py-2 bg-gradient-to-r from-orange-50 to-amber-50 text-orange-800 rounded-full text-sm font-medium border border-orange-200">
                                        {{ $material }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Interior & Exterior Features -->
                @if(!empty($property->interior_features) || !empty($property->exterior_features))
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-home text-indigo-600 ml-3"></i>
                                {{ __('properties.property_features') }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if(!empty($property->interior_features) && is_array($property->interior_features))
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <i class="fas fa-door-open text-blue-500 ml-2"></i>
                                            {{ __('properties.interior_features') }}
                                        </h4>
                                        <ul class="space-y-2">
                                            @foreach($property->interior_features as $feature)
                                                <li class="flex items-center text-gray-700">
                                                    <i class="fas fa-check-circle text-green-500 ml-2"></i>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(!empty($property->exterior_features) && is_array($property->exterior_features))
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <i class="fas fa-tree text-green-500 ml-2"></i>
                                            {{ __('properties.exterior_features') }}
                                        </h4>
                                        <ul class="space-y-2">
                                            @foreach($property->exterior_features as $feature)
                                                <li class="flex items-center text-gray-700">
                                                    <i class="fas fa-check-circle text-green-500 ml-2"></i>
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

                @php
                    $nearbyData = [];

                    // Check for neighborhoods/districts
                    if (!empty($property->neighborhood)) {
                        $nearbyData[] = [
                            'icon' => 'fa-map-marker-alt',
                            'label' => __('properties.nearby_neighborhoods'),
                            'places' => is_array($property->neighborhood) ? $property->neighborhood : [$property->neighborhood]
                        ];
                    }

                    if (!empty($property->district)) {
                        $nearbyData[] = [
                            'icon' => 'fa-city',
                            'label' => __('properties.district'),
                            'places' => is_array($property->district) ? $property->district : [$property->district]
                        ];
                    }

                    // Check for nearby places arrays
                    $nearbyPlaces = [
                        'schools' => ['icon' => 'fa-school', 'label' => __('properties.schools')],
                        'hospitals' => ['icon' => 'fa-hospital', 'label' => __('properties.hospitals')],
                        'shopping_centers' => ['icon' => 'fa-shopping-cart', 'label' => __('properties.shopping_centers')],
                        'transportation' => ['icon' => 'fa-bus', 'label' => __('properties.transportation')],
                        'restaurants' => ['icon' => 'fa-utensils', 'label' => __('properties.restaurants')],
                        'parks' => ['icon' => 'fa-tree', 'label' => __('properties.parks')],
                        'mosques' => ['icon' => 'fa-mosque', 'label' => __('properties.mosques')],
                        'pharmacies' => ['icon' => 'fa-pharmacy', 'label' => __('properties.pharmacies')],
                        'banks' => ['icon' => 'fa-university', 'label' => __('properties.banks')],
                        'gas_stations' => ['icon' => 'fa-gas-pump', 'label' => __('properties.gas_stations')],
                    ];

                    foreach ($nearbyPlaces as $key => $data) {
                        $value = $property->$key ?? null;
                        if ($value && (is_array($value) || !empty($value))) {
                            $nearbyData[] = [
                                'icon' => $data['icon'],
                                'label' => $data['label'],
                                'places' => is_array($value) ? $value : [$value]
                            ];
                        }
                    }
                @endphp

                @if(!empty($nearbyData))
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-map-marked-alt text-red-500 ml-3"></i>
                                {{ __('properties.nearby_places') }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($nearbyData as $index => $data)
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center ml-2">
                                                <i class="fas {{ $data['icon'] }} text-blue-600 text-sm"></i>
                                            </div>
                                            {{ $data['label'] }}
                                        </h4>
                                        <div class="space-y-3">
                                            @foreach($data['places'] as $place)
                                                <div class="flex items-center p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:from-blue-50 hover:to-blue-100 transition-all duration-300">
                                                    <i class="fas fa-map-pin text-red-400 ml-3"></i>
                                                    <div>
                                                        <div class="font-medium text-gray-800">{{ $place }}</div>
                                                        <div class="text-sm text-gray-500">{{ __('properties.distance_km', ['distance' => rand(1, 5)]) }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Media Links -->
                @if($property->virtual_tour_url || $property->video_url || $property->floor_plan_url)
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-photo-video text-purple-600 ml-3"></i>
                                {{ __('properties.multimedia') }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @if($property->virtual_tour_url)
                                    <a href="{{ $property->virtual_tour_url }}" target="_blank" class="flex items-center justify-center p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300">
                                        <i class="fas fa-vr-cardboard text-xl mb-2"></i>
                                        <span class="font-medium">{{ __('properties.virtual_tour') }}</span>
                                    </a>
                                @endif
                                @if($property->video_url)
                                    <a href="{{ $property->video_url }}" target="_blank" class="flex items-center justify-center p-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300">
                                        <i class="fab fa-youtube text-xl mb-2"></i>
                                        <span class="font-medium">{{ __('properties.property_video') }}</span>
                                    </a>
                                @endif
                                @if($property->floor_plan_url)
                                    <a href="{{ $property->floor_plan_url }}" target="_blank" class="flex items-center justify-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300">
                                        <i class="fas fa-drafting-compass text-xl mb-2"></i>
                                        <span class="font-medium">{{ __('properties.floor_plan') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 lg:sticky lg:top-6 h-fit">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="text-xl font-bold text-gray-900 mb-4">ملخص سريع</div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-500">النوع</div>
                                <div class="font-bold text-gray-900">{{ $property->propertyType->name ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-500">المساحة</div>
                                <div class="font-bold text-gray-900">{{ $property->area ?? '-' }} {{ $property->area_unit ?? 'م²' }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-500">غرف</div>
                                <div class="font-bold text-gray-900">{{ $property->bedrooms ?? $property->details->bedrooms ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-500">حمامات</div>
                                <div class="font-bold text-gray-900">{{ $property->bathrooms ?? $property->details->bathrooms ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg col-span-2">
                                <div class="text-xs text-gray-500">رقم العقار</div>
                                <div class="font-bold text-gray-900">{{ $property->property_code ?? $property->id }}</div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <a href="tel:{{ $property->agent->phone ?? '' }}" class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg text-center font-semibold hover:bg-blue-700 transition">
                                اتصال
                            </a>
                            <button class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                                احجز زيارة
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Inquiry Form Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden" id="inquiry-form-section">
                    <div class="p-8">
                        <h3 class="text-2xl font-bold mb-6 text-center text-gray-900">{{ __('properties.interested_in_property') }}</h3>
                        <div id="inquiry-form-container">
                            <form id="inquiry-form" method="POST" action="{{ route('inquiries.store') }}">
                                @csrf
                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                <div class="space-y-4">
                                    @guest
                                    <input type="text" name="name" placeholder="{{ __('common.full_name') }}" required class="w-full px-4 py-3 bg-gray-100 rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition">
                                    <input type="email" name="email" placeholder="{{ __('common.email') }}" required class="w-full px-4 py-3 bg-gray-100 rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition">
                                    <input type="tel" name="phone" placeholder="{{ __('common.phone') }}" required class="w-full px-4 py-3 bg-gray-100 rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition">
                                    @endguest
                                    <textarea name="message" placeholder="{{ __('properties.your_message') }}" rows="4" class="w-full px-4 py-3 bg-gray-100 rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all duration-300 shadow-lg flex items-center justify-center">
                                        <i class="fas fa-paper-plane ml-2"></i>
                                        {{ __('properties.send_inquiry') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div id="inquiry-success-message" class="hidden text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full mx-auto flex items-center justify-center mb-4">
                                <i class="fas fa-check-circle text-5xl text-green-500"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900">{{ __('properties.inquiry_sent_success') }}</h4>
                            <p class="text-gray-600 mt-2">{{ __('properties.inquiry_sent_desc') }}</p>
                        </div>
                    </div>
                </div>


                <!-- Property Stats -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-chart-bar text-blue-600 ml-3"></i>
                                {{ __('properties.property_stats') }}
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <i class="fas fa-eye text-2xl text-blue-600 mb-2"></i>
                                <div class="text-xl font-bold text-gray-900">{{ $property->views_count ?? 0 }}</div>
                                <div class="text-sm text-gray-600">{{ __('properties.views') }}</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-xl">
                                <i class="fas fa-heart text-2xl text-red-600 mb-2"></i>
                                <div class="text-xl font-bold text-gray-900">{{ $property->favorites_count ?? 0 }}</div>
                                <div class="text-sm text-gray-600">{{ __('properties.favorites') }}</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-xl">
                                <i class="fas fa-comment-dots text-2xl text-green-600 mb-2"></i>
                                <div class="text-xl font-bold text-gray-900">{{ $property->inquiries_count ?? 0 }}</div>
                                <div class="text-sm text-gray-600">استفسار</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Developer/Company Info -->
                @if($property->developer || ($property->company && (!$property->agent || $property->company->id !== $property->agent->company_id)))
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-building text-purple-600 ml-3"></i>
                                {{ $property->developer ? 'المطور العقاري' : 'الشركة المعلنة' }}
                            </h3>
                            @php
                                $entity = $property->developer ?? $property->company;
                            @endphp
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center ml-4 overflow-hidden border-2 border-gray-200">
                                    @if($entity->logo)
                                        <img src="{{ asset('storage/' . $entity->logo) }}" class="w-full h-full object-cover" alt="{{ $entity->name }}">
                                    @else
                                        <i class="fas fa-building text-gray-400 text-2xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-lg">{{ $entity->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $entity->type ?? 'شركة عقارية' }}</div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                @if($entity->phone)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-phone text-blue-600 ml-3"></i>
                                    <span>{{ $entity->phone }}</span>
                                </div>
                                @endif
                                @if($entity->email)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-envelope text-green-600 ml-3"></i>
                                    <span class="text-sm">{{ $entity->email }}</span>
                                </div>
                                @endif
                                @if($entity->website)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-globe text-purple-600 ml-3"></i>
                                    <a href="{{ $entity->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $entity->website }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- AI Analysis -->
                <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-xl overflow-hidden text-white">
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-4 flex items-center">
                            <i class="fas fa-robot ml-3"></i>
                            تحليل الذكاء الاصطناعي
                        </h3>
                        <p class="text-white/90 mb-6 text-sm leading-relaxed">
                            احصل على تقييم فوري مدعوم بالذكاء الاصطناعي لهذا العقار بناءً على اتجاهات السوق الحالية.
                        </p>
                        <button id="ai-predict-btn" class="w-full bg-white text-purple-600 font-bold py-4 px-6 rounded-xl hover:bg-purple-50 transition-all duration-300 shadow-lg">
                            <i class="fas fa-chart-line ml-2"></i>
                            تحليل السعر والاستثمار
                        </button>

                        <div id="ai-loading" class="hidden mt-6 text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                            <p class="mt-3 text-white/90">جاري تحليل بيانات السوق...</p>
                        </div>

                        <div id="ai-result" class="hidden mt-6">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl p-6">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <div class="text-white/80 text-sm mb-1">نطاق السعر المقدر</div>
                                        <div class="text-2xl font-bold" id="pred-price"></div>
                                        <div class="text-white/80 text-sm mt-1">الدقة: <span id="pred-confidence"></span>%</div>
                                    </div>
                                    <div>
                                        <div class="text-white/80 text-sm mb-1">نقاط الاستثمار</div>
                                        <div class="flex items-center mb-2">
                                            <div class="text-xl font-bold" id="pred-score-val"></div>
                                        </div>
                                        <div class="w-full bg-white/30 rounded-full h-2">
                                            <div id="pred-score-bar" class="bg-white h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                        </div>
                                        <div class="mt-2">
                                            <span class="inline-block px-3 py-1 bg-white/30 rounded-full text-xs font-bold" id="pred-risk"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 pb-12">
    @if(!empty($similarProperties) && count($similarProperties) > 0)
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="p-6 flex items-center justify-between">
            <h3 class="text-2xl font-bold text-gray-900">عقارات مشابهة</h3>
            <a href="{{ route('optimized.properties.index') }}" class="text-blue-600 text-sm">عرض الكل</a>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($similarProperties as $sProperty)
                @include('properties.partials.card', ['property' => $sProperty])
            @endforeach
        </div>
    </div>
    @endif
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
/* Custom animations */
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out;
}

/* Hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Gradient text */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($property->latitude && $property->longitude)
    const pm = L.map('property-map').setView([{{ $property->latitude }}, {{ $property->longitude }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(pm);
    L.marker([{{ $property->latitude }}, {{ $property->longitude }}]).addTo(pm).bindPopup(`{{ $property->title }}`);
    @endif
    async function loadRecos() {
        try {
            const res = await fetch('/api/intelligence/recommendations/feed');
            if (!res.ok) return;
            const json = await res.json();
            const list = document.getElementById('reco-list');
            list.innerHTML = '';
            (json.feed || []).slice(0, 6).forEach(item => {
                const el = document.createElement('div');
                el.className = 'border rounded-xl p-4 flex items-center justify-between';
                el.innerHTML = `<div class="text-sm">#${item.property_id}</div><div class="text-xs text-slate-600">Match ${item.match}%</div><a href="/properties/${item.property_id}" class="text-blue-600 text-sm">عرض</a>`;
                list.appendChild(el);
            });
        } catch (e) {}
    }
    const btn = document.getElementById('load-recos');
    if (btn) {
        btn.addEventListener('click', loadRecos);
        loadRecos();
    }
    const inquiryForm = document.getElementById('inquiry-form');
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formContainer = document.getElementById('inquiry-form-container');
            const successMessage = document.getElementById('inquiry-success-message');
            const submitButton = this.querySelector('button[type="submit"]');
            const buttonIcon = submitButton.querySelector('i');
            const originalButtonContent = submitButton.innerHTML;

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>`;

            const formData = new FormData(this);

            fetch('{{ route("inquiries.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    formContainer.classList.add('hidden');
                    successMessage.classList.remove('hidden');
                } else {
                    // Handle validation errors
                    alert('حدث خطأ: ' + (data.message || 'يرجى التحقق من البيانات المدخلة.'));
                    // Restore button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.');
                // Restore button
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonContent;
            });
        });
    }

    // AI Analysis functionality
    const aiPredictBtn = document.getElementById('ai-predict-btn');
    if(aiPredictBtn) {
        aiPredictBtn.addEventListener('click', function() {
            const btn = this;
            const loading = document.getElementById('ai-loading');
            const result = document.getElementById('ai-result');

            btn.classList.add('hidden');
            loading.classList.remove('hidden');
            result.classList.add('hidden');

            // Simulate AI analysis (replace with actual API call)
            setTimeout(() => {
                // Mock data - replace with real API response
                const mockData = {
                    price_range: '1,200,000 - 1,500,000',
                    confidence: 87,
                    investment_score: 78,
                    risk_level: 'منخفض'
                };

                // Update UI with results
                document.getElementById('pred-price').textContent = mockData.price_range;
                document.getElementById('pred-confidence').textContent = mockData.confidence;
                document.getElementById('pred-score-val').textContent = mockData.investment_score;
                document.getElementById('pred-score-bar').style.width = mockData.investment_score + '%';
                document.getElementById('pred-risk').textContent = mockData.risk_level;

                loading.classList.add('hidden');
                result.classList.remove('hidden');
                btn.classList.remove('hidden');
            }, 2000);
        });
    }

    // Gallery functionality
    window.openGallery = function(index) {
        // Implement gallery modal or lightbox
        console.log('Opening gallery at index:', index);
        alert('Gallery functionality to be implemented. Opening image at index: ' + index);
    }

    // Smooth scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.space-y-6 > div, .lg\\:col-span-2 > div').forEach(el => {
        observer.observe(el);
    });
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function(e) {
            const id = this.getAttribute('href').slice(1);
            const target = document.getElementById(id);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
</script>
@endsection
