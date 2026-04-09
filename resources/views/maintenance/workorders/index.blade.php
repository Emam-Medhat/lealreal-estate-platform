@extends('admin.layouts.admin')

@section('title', 'أوامر العمل')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">أوامر العمل</h1>
            <p class="text-gray-600 mt-1">إدارة أوامر العمل والصيانة</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('maintenance.workorders.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-plus"></i>
                <span>إنشاء أمر عمل</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي أوامر العمل</p>
                <p class="text-2xl font-bold text-gray-900">{{ $workOrders->total() }}</p>
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
                <p class="text-2xl font-bold text-yellow-600">{{ $workOrders->where('status', 'pending')->count() }}</p>
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
                <p class="text-2xl font-bold text-blue-600">{{ $workOrders->where('status', 'in_progress')->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-cogs text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">مكتملة</p>
                <p class="text-2xl font-bold text-green-600">{{ $workOrders->where('status', 'completed')->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('maintenance.workorders.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[300px]">
            <div class="relative">
                <input type="text" name="search" 
                       value="{{ request('search') }}"
                       placeholder="البحث عن أمر عمل..." 
                       class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">جميع الحالات</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
        </select>
        
        <select name="priority" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">جميع الأولويات</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
            <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>طوارئ</option>
        </select>
        
        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-filter ml-2"></i>
            فلترة
        </button>
        
        <a href="{{ route('maintenance.workorders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-redo ml-2"></i>
            إعادة تعيين
        </a>
    </form>
</div>

<!-- Work Orders Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرقم</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموقع</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفريق</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($workOrders as $workOrder)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                #{{ $workOrder->id }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $workOrder->title }}
                            </div>
                            @if($workOrder->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($workOrder->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($workOrder->status == 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif($workOrder->status == 'in_progress')
                                    bg-blue-100 text-blue-800
                                @elseif($workOrder->status == 'completed')
                                    bg-green-100 text-green-800
                                @elseif($workOrder->status == 'cancelled')
                                    bg-red-100 text-red-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif">
                                @if($workOrder->status == 'pending')
                                    قيد الانتظار
                                @elseif($workOrder->status == 'in_progress')
                                    قيد التنفيذ
                                @elseif($workOrder->status == 'completed')
                                    مكتمل
                                @elseif($workOrder->status == 'cancelled')
                                    ملغي
                                @else
                                    غير محدد
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
                                @elseif($workOrder->priority == 'emergency')
                                    bg-red-100 text-red-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif">
                                @if($workOrder->priority == 'low')
                                    منخفض
                                @elseif($workOrder->priority == 'medium')
                                    متوسط
                                @elseif($workOrder->priority == 'high')
                                    عالي
                                @elseif($workOrder->priority == 'emergency')
                                    طوارئ
                                @else
                                    غير محدد
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
                            <div class="text-sm text-gray-900">
                                @if($workOrder->assignedTeam)
                                    {{ $workOrder->assignedTeam->name }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $workOrder->created_at->format('Y-m-d') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('maintenance.workorders.show', $workOrder) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('maintenance.workorders.edit', $workOrder) }}" 
                                   class="text-green-600 hover:text-green-800 transition-colors duration-150"
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($workOrder->status == 'pending')
                                    <form method="POST" action="{{ route('maintenance.workorders.start', $workOrder) }}" 
                                          class="inline"
                                          onsubmit="return confirm('هل أنت متأكد من بدء أمر العمل؟');">
                                        @csrf
                                        <button type="submit" 
                                                class="text-yellow-600 hover:text-yellow-800 transition-colors duration-150"
                                                title="بدء">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 rounded-full p-4 mb-4">
                                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أوامر عمل</h3>
                                <p class="text-gray-500 mb-4">لم يتم إنشاء أي أوامر عمل بعد</p>
                                <a href="{{ route('maintenance.workorders.create') }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-plus ml-2"></i>
                                    إنشاء أمر عمل أول
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($workOrders->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $workOrders->links() }}
        </div>
    @endif
</div>
@endsection
