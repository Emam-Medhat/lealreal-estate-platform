@extends('admin.layouts.admin')

@section('title', 'تفاصيل المورد')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تفاصيل المورد</h1>
            <p class="text-gray-600 mt-1">معلومات المورد والبيانات المتعلقة به</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.suppliers.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
            <a href="{{ route('inventory.suppliers.edit', $supplier) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-edit"></i>
                <span>تعديل</span>
            </a>
        </div>
    </div>
</div>

<!-- Supplier Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات المورد</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center space-x-4 space-x-reverse mb-6">
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-truck text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $supplier->name }}</h3>
                        @if($supplier->code)
                            <p class="text-gray-500">كود المورد: {{ $supplier->code }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-envelope text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">البريد الإلكتروني</p>
                            @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $supplier->email }}
                                </a>
                            @else
                                <span class="text-gray-400">غير محدد</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-phone text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الهاتف</p>
                            @if($supplier->phone)
                                <a href="tel:{{ $supplier->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $supplier->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">غير محدد</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-globe text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الموقع الإلكتروني</p>
                            @if($supplier->website)
                                <a href="{{ $supplier->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    {{ $supplier->website }}
                                </a>
                            @else
                                <span class="text-gray-400">غير محدد</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">العنوان</p>
                            <p class="text-gray-900">
                                @if($supplier->address)
                                    {{ $supplier->address }}
                                    @if($supplier->city), {{ $supplier->city }}@endif
                                    @if($supplier->state), {{ $supplier->state }}@endif
                                    @if($supplier->country), {{ $supplier->country }}@endif
                                    @if($supplier->postal_code), {{ $supplier->postal_code }}@endif
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-info-circle text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الوصف</p>
                            <p class="text-gray-900">
                                @if($supplier->description)
                                    {{ $supplier->description }}
                                @else
                                    <span class="text-gray-400">لا يوجد وصف</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-credit-card text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الحد الائتماني</p>
                            <p class="text-gray-900">
                                @if($supplier->credit_limit)
                                    ${{ number_format($supplier->credit_limit, 2) }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-clock text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">شروط الدفع</p>
                            <p class="text-gray-900">
                                @if($supplier->payment_terms)
                                    {{ $supplier->payment_terms }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($supplier->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-sticky-note text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">ملاحظات</p>
                        <p class="text-gray-900">{{ $supplier->notes }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Status Badge -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">حالة المورد</h3>
            <p class="text-gray-600 mt-1">الحالة الحالية للمورد</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($supplier->status == 'active')
                    bg-green-100 text-green-800
                @else
                    bg-red-100 text-red-800
                @endif">
                @if($supplier->status == 'active')
                    نشط
                @else
                    غير نشط
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('inventory.suppliers.edit', $supplier) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-edit ml-2"></i>
            تعديل المورد
        </a>
        
        <form method="POST" action="{{ route('inventory.suppliers.destroy', $supplier) }}" 
              class="inline"
              onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-trash ml-2"></i>
                حذف المورد
            </button>
        </form>
        
        <a href="{{ route('inventory.suppliers.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>
</div>
@endsection
