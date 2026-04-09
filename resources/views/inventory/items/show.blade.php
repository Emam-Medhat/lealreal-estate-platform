@extends('admin.layouts.admin')

@section('title', 'تفاصيل العنصر')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تفاصيل العنصر</h1>
            <p class="text-gray-600 mt-1">معلومات العنصر والمخزون</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.items.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
            <a href="{{ route('inventory.items.edit', $item) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-edit"></i>
                <span>تعديل</span>
            </a>
        </div>
    </div>
</div>

<!-- Item Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات العنصر</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center space-x-4 space-x-reverse mb-6">
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $item->name }}</h3>
                        @if($item->name_ar)
                            <p class="text-gray-500">{{ $item->name_ar }}</p>
                        @endif
                        @if($item->item_code)
                            <p class="text-gray-500">كود: {{ $item->item_code }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-barcode text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">SKU</p>
                            <p class="text-gray-900">
                                @if($item->sku)
                                    {{ $item->sku }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-tag text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الفئة</p>
                            <p class="text-gray-900">{{ $item->getCategoryName() }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-cube text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الوحدة</p>
                            <p class="text-gray-900">{{ $item->unit }}</p>
                            @if($item->unit_ar)
                                <p class="text-sm text-gray-500">{{ $item->unit_ar }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-info-circle text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الحالة</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($item->status == 'active')
                                    bg-green-100 text-green-800
                                @elseif($item->status == 'inactive')
                                    bg-yellow-100 text-yellow-800
                                @elseif($item->status == 'discontinued')
                                    bg-red-100 text-red-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif">
                                @if($item->status == 'active')
                                    نشط
                                @elseif($item->status == 'inactive')
                                    غير نشط
                                @elseif($item->status == 'discontinued')
                                    متوقف
                                @else
                                    نفد المخزون
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-building text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">العلامة التجارية</p>
                            <p class="text-gray-900">
                                @if($item->brand)
                                    {{ $item->brand }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-cog text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الموديل</p>
                            <p class="text-gray-900">
                                @if($item->model)
                                    {{ $item->model }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-truck text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">المورد</p>
                            <p class="text-gray-900">
                                @if($item->supplier)
                                    {{ $item->getSupplierName() }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الموقع</p>
                            <p class="text-gray-900">
                                @if($item->location)
                                    {{ $item->location }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($item->description || $item->description_ar)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-2">الوصف</h4>
                <div class="text-gray-700">
                    @if($item->description)
                        <p>{{ $item->description }}</p>
                    @endif
                    @if($item->description_ar)
                        <p class="mt-2">{{ $item->description_ar }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Stock Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات المخزون</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="bg-blue-100 rounded-full p-3 mb-2 inline-block">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $item->quantity }}</p>
                <p class="text-sm text-gray-600">الكمية الحالية</p>
            </div>
            
            <div class="text-center">
                <div class="bg-yellow-100 rounded-full p-3 mb-2 inline-block">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-yellow-600">{{ $item->min_quantity ?? 0 }}</p>
                <p class="text-sm text-gray-600">الحد الأدنى</p>
            </div>
            
            <div class="text-center">
                <div class="bg-green-100 rounded-full p-3 mb-2 inline-block">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ $item->max_quantity ?? 0 }}</p>
                <p class="text-sm text-gray-600">الحد الأقصى</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-100 rounded-full p-3 mb-2 inline-block">
                    <i class="fas fa-bell text-red-600 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-red-600">{{ $item->reorder_point ?? 0 }}</p>
                <p class="text-sm text-gray-600">نقطة إعادة الطلب</p>
            </div>
        </div>
        
        <!-- Stock Status -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">حالة المخزون</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($item->quantity == 0)
                            نفد المخزون بالكامل
                        @elseif($item->quantity <= ($item->reorder_point ?? 0))
                            الكمية منخفضة، يجب إعادة الطلب
                        @elseif($item->quantity >= ($item->max_quantity ?? 999999))
                            الكمية زائدة عن الحد الأقصى
                        @else
                            المخزون في الحالة الطبيعية
                        @endif
                    </p>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($item->quantity == 0)
                            bg-red-100 text-red-800
                        @elseif($item->quantity <= ($item->reorder_point ?? 0))
                            bg-yellow-100 text-yellow-800
                        @elseif($item->quantity >= ($item->max_quantity ?? 999999))
                            bg-orange-100 text-orange-800
                        @else
                            bg-green-100 text-green-800
                        @endif">
                        @if($item->quantity == 0)
                            نفد المخزون
                        @elseif($item->quantity <= ($item->reorder_point ?? 0))
                            منخفض
                        @elseif($item->quantity >= ($item->max_quantity ?? 999999))
                            زائد
                        @else
                            طبيعي
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات التسعير</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-dollar-sign text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">التكلفة للوحدة</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($item->unit_cost, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-tag text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">سعر البيع</p>
                        <p class="text-2xl font-bold text-green-600">
                            @if($item->selling_price)
                                ${{ number_format($item->selling_price, 2) }}
                            @else
                                <span class="text-gray-400">غير محدد</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        @if($item->unit_cost && $item->quantity)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">إجمالي قيمة المخزون</h4>
                        <p class="text-sm text-gray-600 mt-1">التكلفة × الكمية</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">${{ number_format($item->unit_cost * $item->quantity, 2) }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Additional Information -->
@if($item->barcode || $item->qr_code || $item->warranty_expiry || $item->expiry_date || $item->notes || $item->notes_ar)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات إضافية</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($item->barcode)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-barcode text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">الباركود</p>
                        <p class="text-gray-900 font-mono">{{ $item->barcode }}</p>
                    </div>
                </div>
            @endif
            
            @if($item->qr_code)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-qrcode text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">رمز QR</p>
                        <p class="text-gray-900 font-mono">{{ $item->qr_code }}</p>
                    </div>
                </div>
            @endif
            
            @if($item->warranty_expiry)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-shield-alt text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">انتهاء الضمان</p>
                        <p class="text-gray-900">{{ $item->warranty_expiry->format('Y-m-d') }}</p>
                    </div>
                </div>
            @endif
            
            @if($item->expiry_date)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-calendar-alt text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">انتهاء الصلاحية</p>
                        <p class="text-gray-900">{{ $item->expiry_date->format('Y-m-d') }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        @if($item->notes || $item->notes_ar)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 mb-2">ملاحظات</h4>
                <div class="text-gray-700">
                    @if($item->notes)
                        <p>{{ $item->notes }}</p>
                    @endif
                    @if($item->notes_ar)
                        <p class="mt-2">{{ $item->notes_ar }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('inventory.items.edit', $item) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-edit ml-2"></i>
            تعديل العنصر
        </a>
        
        <a href="{{ route('inventory.movements.create') }}?item_id={{ $item->id }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-exchange-alt ml-2"></i>
            تسجيل حركة
        </a>
        
        <a href="{{ route('inventory.items.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>
</div>
@endsection
