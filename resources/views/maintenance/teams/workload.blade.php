@extends('admin.layouts.admin')

@section('title', 'عبء عمل الفريق')

@section('content')
@if($team)
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">عبء عمل الفريق</h1>
            <p class="text-gray-600 mt-1">تحليل عبء العمل وأداء الفريق</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('maintenance.teams.show', $team) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

<!-- Team Info Header -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="p-6">
        <div class="flex items-center space-x-4 space-x-reverse">
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $team->name }}</h3>
                <p class="text-gray-500">{{ $team->specialization_label }} • {{ $team->members ? $team->members->count() : 0 }} عضو</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي أوامر العمل</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-clipboard-list text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">قيد الانتظار</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">قيد التنفيذ</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-indigo-100 rounded-full p-3">
                <i class="fas fa-cog text-indigo-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">مكتملة هذا الشهر</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed_this_month'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Work Orders List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">أوامر العمل</h2>
    </div>
    
    @if($workOrders && $workOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الأمر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($workOrders as $workOrder)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $workOrder->work_order_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $workOrder->title }}</div>
                                @if($workOrder->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($workOrder->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($workOrder->status == 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($workOrder->status == 'assigned')
                                        bg-blue-100 text-blue-800
                                    @elseif($workOrder->status == 'in_progress')
                                        bg-indigo-100 text-indigo-800
                                    @elseif($workOrder->status == 'completed')
                                        bg-green-100 text-green-800
                                    @elseif($workOrder->status == 'cancelled')
                                        bg-red-100 text-red-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    @if($workOrder->status == 'pending')
                                        قيد الانتظار
                                    @elseif($workOrder->status == 'assigned')
                                        مكلف
                                    @elseif($workOrder->status == 'in_progress')
                                        قيد التنفيذ
                                    @elseif($workOrder->status == 'completed')
                                        مكتمل
                                    @elseif($workOrder->status == 'cancelled')
                                        ملغي
                                    @else
                                        {{ $workOrder->status }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
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
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($workOrder->property)
                                        {{ $workOrder->property->title }}
                                    @else
                                        <span class="text-gray-400">غير محدد</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $workOrder->created_at->format('Y-m-d') }}</div>
                                <div class="text-sm text-gray-500">{{ $workOrder->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('maintenance.workorders.show', $workOrder) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($workOrder->status == 'pending')
                                        <form method="POST" action="{{ route('maintenance.workorders.start', $workOrder) }}" 
                                              class="inline"
                                              onsubmit="return confirm('هل أنت متأكد من بدء أمر العمل؟');">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-800 transition-colors duration-150"
                                                    title="بدء">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($workOrder->status == 'in_progress')
                                        <form method="POST" action="{{ route('maintenance.workorders.complete', $workOrder) }}" 
                                              class="inline"
                                              onsubmit="return confirm('هل أنت متأكد من إكمال أمر العمل؟');">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-purple-600 hover:text-purple-800 transition-colors duration-150"
                                                    title="إكمال">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <div class="bg-gray-100 rounded-full p-4 mb-4 inline-block">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أوامر عمل</h3>
            <p class="text-gray-500 mb-4">لم يتم تكليف هذا الفريق بأي أوامر عمل بعد</p>
            <a href="{{ route('maintenance.workorders.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إنشاء أمر عمل جديد
            </a>
        </div>
    @endif
</div>

<!-- Performance Metrics -->
@if($stats['total'] > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">مؤشرات الأداء</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 mb-2">
                    @php
                        $completionRate = $stats['total'] > 0 ? (($stats['completed_this_month'] / $stats['total']) * 100) : 0;
                    @endphp
                    {{ number_format($completionRate, 1) }}%
                </div>
                <p class="text-sm text-gray-600">معدل الإنجاز هذا الشهر</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                </div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 mb-2">
                    @php
                        $activeRate = $stats['total'] > 0 ? ((($stats['pending'] + $stats['in_progress']) / $stats['total']) * 100) : 0;
                    @endphp
                    {{ number_format($activeRate, 1) }}%
                </div>
                <p class="text-sm text-gray-600">معدل النشاط</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $activeRate }}%"></div>
                </div>
            </div>
            
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 mb-2">
                    @if($team->max_concurrent_jobs > 0)
                        {{ number_format((($stats['in_progress'] / $team->max_concurrent_jobs) * 100), 1) }}%
                    @else
                        0%
                    @endif
                </div>
                <p class="text-sm text-gray-600">عبء العمل الحالي</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    @if($team->max_concurrent_jobs > 0)
                        <div class="bg-{{ $stats['in_progress'] >= $team->max_concurrent_jobs ? 'red' : 'orange' }}-500 h-2 rounded-full" 
                             style="width: {{ min(100, ($stats['in_progress'] / $team->max_concurrent_jobs) * 100) }}%"></div>
                    @else
                        <div class="bg-gray-500 h-2 rounded-full" style="width: 0%"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@else
<!-- Team Not Found -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
    <div class="flex flex-col items-center">
        <div class="bg-gray-100 rounded-full p-4 mb-4">
            <i class="fas fa-users text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">الفريق غير موجود</h3>
        <p class="text-gray-500 mb-4">لم يتم العثور على الفريق المطلوب</p>
        <a href="{{ route('maintenance.teams.index') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للفرق
        </a>
    </div>
</div>
@endif
@endsection
