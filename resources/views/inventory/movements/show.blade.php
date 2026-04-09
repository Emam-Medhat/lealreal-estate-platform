@extends('admin.layouts.admin')

@section('title', 'تفاصيل الحركة')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تفاصيل الحركة</h1>
            <p class="text-gray-600 mt-1">معلومات حركة المخزون</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.movements.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

@if($movement)
<!-- Movement Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات الحركة</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center space-x-4 space-x-reverse mb-6">
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">حركة #{{ $movement->id }}</h3>
                        <p class="text-gray-500">{{ $movement->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-box text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">العنصر</p>
                            <p class="text-gray-900 font-medium">{{ $movement->item_name }}</p>
                            @if($movement->sku)
                                <p class="text-sm text-gray-500">SKU: {{ $movement->sku }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-exchange-alt text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">نوع الحركة</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($movement->type == 'in')
                                    bg-green-100 text-green-800
                                @elseif($movement->type == 'out')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif">
                                @if($movement->type == 'in')
                                    وارد
                                @elseif($movement->type == 'out')
                                    صادر
                                @else
                                    تحويل
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-sort-numeric-up text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الكمية</p>
                            <p class="text-gray-900 font-medium text-lg">{{ $movement->quantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-tag text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">السبب</p>
                            <p class="text-gray-900">
                                @if($movement->reason)
                                    {{ $movement->reason }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($movement->reference)
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <i class="fas fa-file-invoice text-gray-400 w-5"></i>
                            <div>
                                <p class="text-sm text-gray-500">المرجع</p>
                                <p class="text-gray-900">{{ $movement->reference }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-user text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">المستخدم</p>
                            <p class="text-gray-900">
                                @if($movement->user_id)
                                    <span class="text-blue-600">مستخدم #{{ $movement->user_id }}</span>
                                @else
                                    <span class="text-gray-500">النظام</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($movement->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-sticky-note text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">ملاحظات</p>
                        <p class="text-gray-900">{{ $movement->notes }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Location Information -->
@if($movement->location_from || $movement->location_to)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الموقع</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($movement->location_from)
            <div class="flex items-center space-x-3 space-x-reverse">
                <i class="fas fa-map-marker-alt text-red-400 w-5"></i>
                <div>
                    <p class="text-sm text-gray-500">من</p>
                    <p class="text-gray-900 font-medium">{{ $movement->location_from }}</p>
                </div>
            </div>
        @endif
        
        @if($movement->location_to)
            <div class="flex items-center space-x-3 space-x-reverse">
                <i class="fas fa-map-marker-alt text-green-400 w-5"></i>
                <div>
                    <p class="text-sm text-gray-500">إلى</p>
                    <p class="text-gray-900 font-medium">{{ $movement->location_to }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Cost Information -->
@if($movement->unit_cost || $movement->total_cost)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات التكلفة</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($movement->unit_cost)
            <div class="flex items-center space-x-3 space-x-reverse">
                <i class="fas fa-dollar-sign text-gray-400 w-5"></i>
                <div>
                    <p class="text-sm text-gray-500">التكلفة للوحدة</p>
                    <p class="text-gray-900 font-medium">${{ number_format($movement->unit_cost, 2) }}</p>
                </div>
            </div>
        @endif
        
        @if($movement->total_cost)
            <div class="flex items-center space-x-3 space-x-reverse">
                <i class="fas fa-calculator text-gray-400 w-5"></i>
                <div>
                    <p class="text-sm text-gray-500">إجمالي التكلفة</p>
                    <p class="text-gray-900 font-medium text-lg">${{ number_format($movement->total_cost, 2) }}</p>
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
        <a href="{{ route('inventory.movements.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
        
        <a href="{{ route('inventory.items.show', $movement->inventory_id) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-box ml-2"></i>
            عرض العنصر
        </a>
    </div>
</div>

@else
<!-- Movement Not Found -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
    <div class="flex flex-col items-center">
        <div class="bg-gray-100 rounded-full p-4 mb-4">
            <i class="fas fa-exchange-alt text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">الحركة غير موجودة</h3>
        <p class="text-gray-500 mb-4">لم يتم العثور على الحركة المطلوبة</p>
        <a href="{{ route('inventory.movements.index') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للحركات
        </a>
    </div>
</div>
@endif
@endsection
