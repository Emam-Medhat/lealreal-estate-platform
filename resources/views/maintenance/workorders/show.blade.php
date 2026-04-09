@extends('admin.layouts.admin')

@section('title', 'تفاصيل أمر العمل')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تفاصيل أمر العمل</h1>
            <p class="text-gray-600 mt-1">معلومات أمر العمل والصيانة</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('maintenance.workorders.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
            <a href="{{ route('maintenance.workorders.edit', $workOrder) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-edit"></i>
                <span>تعديل</span>
            </a>
        </div>
    </div>
</div>

@if($workOrder)
<!-- Work Order Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات أمر العمل</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center space-x-4 space-x-reverse mb-6">
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $workOrder->work_order_number }}</h3>
                        <p class="text-gray-500">{{ $workOrder->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-heading text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">العنوان</p>
                            <p class="text-gray-900 font-medium">{{ $workOrder->title }}</p>
                            @if($workOrder->title_ar)
                                <p class="text-sm text-gray-500">{{ $workOrder->title_ar }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-align-left text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الوصف</p>
                            <p class="text-gray-900">{{ $workOrder->description }}</p>
                            @if($workOrder->description_ar)
                                <p class="text-sm text-gray-500 mt-1">{{ $workOrder->description_ar }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-flag text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الأولوية</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($workOrder->priority == 'low')
                                    bg-gray-100 text-gray-800
                                @elseif($workOrder->priority == 'medium')
                                    bg-blue-100 text-blue-800
                                @elseif($workOrder->priority == 'high')
                                    bg-orange-100 text-orange-800
                                @else
                                    bg-red-100 text-red-800
                                @endif">
                                @if($workOrder->priority == 'low')
                                    منخفض
                                @elseif($workOrder->priority == 'medium')
                                    متوسط
                                @elseif($workOrder->priority == 'high')
                                    عالي
                                @else
                                    طوارئ
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-tools text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">النوع</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                @if($workOrder->type == 'repair')
                                    إصلاح
                                @elseif($workOrder->type == 'maintenance')
                                    صيانة
                                @elseif($workOrder->type == 'installation')
                                    تثبيت
                                @elseif($workOrder->type == 'inspection')
                                    فحص
                                @elseif($workOrder->type == 'replacement')
                                    استبدال
                                @else
                                    أخرى
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-info-circle text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">الحالة</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($workOrder->status == 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif($workOrder->status == 'assigned')
                                    bg-blue-100 text-blue-800
                                @elseif($workOrder->status == 'in_progress')
                                    bg-indigo-100 text-indigo-800
                                @elseif($workOrder->status == 'paused')
                                    bg-gray-100 text-gray-800
                                @elseif($workOrder->status == 'completed')
                                    bg-green-100 text-green-800
                                @else
                                    bg-red-100 text-red-800
                                @endif">
                                @if($workOrder->status == 'pending')
                                    قيد الانتظار
                                @elseif($workOrder->status == 'assigned')
                                    مكلف
                                @elseif($workOrder->status == 'in_progress')
                                    قيد التنفيذ
                                @elseif($workOrder->status == 'paused')
                                    متوقف
                                @elseif($workOrder->status == 'completed')
                                    مكتمل
                                @else
                                    ملغي
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-building text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">العقار</p>
                            <p class="text-gray-900">
                                @if($workOrder->property)
                                    {{ $workOrder->property->title }}
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
                                @if($workOrder->location)
                                    {{ $workOrder->location }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i class="fas fa-user text-gray-400 w-5"></i>
                        <div>
                            <p class="text-sm text-gray-500">المنشئ</p>
                            <p class="text-gray-900">
                                @if($workOrder->createdBy)
                                    {{ $workOrder->createdBy->name }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات التكليف</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center space-x-3 space-x-reverse">
                <i class="fas fa-user-tie text-gray-400 w-5"></i>
                <div>
                    <p class="text-sm text-gray-500">المكلف إليه</p>
                    <p class="text-gray-900">
                        @if($workOrder->assignedTo)
                            {{ $workOrder->assignedTo->name }}
                        @else
                            <span class="text-gray-400">غير محدد</span>
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <i class="fas fa-users text-gray-400 w-5"></i>
                <div>
                    <p class="text-sm text-gray-500">الفريق المكلف</p>
                    <p class="text-gray-900">
                        @if($workOrder->assignedTeam)
                            {{ $workOrder->assignedTeam->name }}
                        @else
                            <span class="text-gray-400">غير محدد</span>
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <i class="fas fa-truck text-gray-400 w-5"></i>
                <div>
                    <p class="text-sm text-gray-500">مقدم الخدمة</p>
                    <p class="text-gray-900">
                        @if($workOrder->serviceProvider)
                            {{ $workOrder->serviceProvider->name }}
                        @else
                            <span class="text-gray-400">غير محدد</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        @if($workOrder->maintenanceRequest)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-clipboard text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">طلب الصيانة المرتبط</p>
                        <p class="text-gray-900">
                            <a href="{{ route('maintenance.requests.show', $workOrder->maintenanceRequest) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                {{ $workOrder->maintenanceRequest->title }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Scheduling Information -->
@if($workOrder->scheduled_date || $workOrder->estimated_duration)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات الجدولة</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($workOrder->scheduled_date)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-calendar text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">التاريخ المجدول</p>
                        <p class="text-gray-900">{{ $workOrder->scheduled_date->format('Y-m-d') }}</p>
                        @if($workOrder->scheduled_time)
                            <p class="text-sm text-gray-500">{{ $workOrder->scheduled_time }}</p>
                        @endif
                    </div>
                </div>
            @endif
            
            @if($workOrder->estimated_duration)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-clock text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">المدة التقديرية</p>
                        <p class="text-gray-900">{{ $workOrder->estimated_duration }} دقيقة</p>
                    </div>
                </div>
            @endif
            
            @if($workOrder->started_at)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-play text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">بدأ في</p>
                        <p class="text-gray-900">{{ $workOrder->started_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Cost Information -->
@if($workOrder->estimated_cost || $workOrder->actual_cost)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات التكلفة</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @if($workOrder->estimated_cost)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-dollar-sign text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">التكلفة التقديرية</p>
                        <p class="text-2xl font-bold text-blue-600">${{ number_format($workOrder->estimated_cost, 2) }}</p>
                    </div>
                </div>
            @endif
            
            @if($workOrder->actual_cost)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-calculator text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">التكلفة الفعلية</p>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($workOrder->actual_cost, 2) }}</p>
                    </div>
                </div>
            @endif
            
            @if($workOrder->labor_cost)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-hard-hat text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">تكلفة العمالة</p>
                        <p class="text-lg font-bold text-orange-600">${{ number_format($workOrder->labor_cost, 2) }}</p>
                    </div>
                </div>
            @endif
            
            @if($workOrder->material_cost)
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i class="fas fa-box text-gray-400 w-5"></i>
                    <div>
                        <p class="text-sm text-gray-500">تكلفة المواد</p>
                        <p class="text-lg font-bold text-purple-600">${{ number_format($workOrder->material_cost, 2) }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Additional Information -->
@if($workOrder->access_instructions || $workOrder->safety_requirements || $workOrder->notes)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات إضافية</h2>
    </div>
    <div class="p-6">
        <div class="space-y-6">
            @if($workOrder->access_instructions)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">تعليمات الوصول</h4>
                    <p class="text-gray-700">{{ $workOrder->access_instructions }}</p>
                </div>
            @endif
            
            @if($workOrder->safety_requirements)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">متطلبات السلامة</h4>
                    <p class="text-gray-700">{{ $workOrder->safety_requirements }}</p>
                </div>
            @endif
            
            @if($workOrder->notes)
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">ملاحظات</h4>
                    <p class="text-gray-700">{{ $workOrder->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('maintenance.workorders.edit', $workOrder) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-edit ml-2"></i>
            تعديل أمر العمل
        </a>
        
        @if($workOrder->status == 'pending')
            <form method="POST" action="{{ route('maintenance.workorders.start', $workOrder) }}" 
                  class="inline"
                  onsubmit="return confirm('هل أنت متأكد من بدء أمر العمل؟');">
                @csrf
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-play ml-2"></i>
                    بدء العمل
                </button>
            </form>
        @endif
        
        @if($workOrder->status == 'in_progress')
            <form method="POST" action="{{ route('maintenance.workorders.complete', $workOrder) }}" 
                  class="inline"
                  onsubmit="return confirm('هل أنت متأكد من إكمال أمر العمل؟');">
                @csrf
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-check ml-2"></i>
                    إكمال العمل
                </button>
            </form>
        @endif
        
        <form method="POST" action="{{ route('maintenance.workorders.cancel', $workOrder) }}" 
              class="inline"
              onsubmit="return confirm('هل أنت متأكد من إلغاء أمر العمل؟');">
            @csrf
            <button type="submit" 
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-times ml-2"></i>
                إلغاء أمر العمل
            </button>
        </form>
        
        <a href="{{ route('maintenance.workorders.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>
</div>

@else
<!-- Work Order Not Found -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
    <div class="flex flex-col items-center">
        <div class="bg-gray-100 rounded-full p-4 mb-4">
            <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">أمر العمل غير موجود</h3>
        <p class="text-gray-500 mb-4">لم يتم العثور على أمر العمل المطلوب</p>
        <a href="{{ route('maintenance.workorders.index') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لأوامر العمل
        </a>
    </div>
</div>
@endif
@endsection
