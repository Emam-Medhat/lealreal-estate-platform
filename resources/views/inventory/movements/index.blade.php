@extends('admin.layouts.admin')

@section('title', 'حركات المخزون')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">حركات المخزون</h1>
            <p class="text-gray-600 mt-1">تتبع حركة المخزون والعمليات</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.movements.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-plus"></i>
                <span>إضافة حركة</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي الحركات</p>
                <p class="text-2xl font-bold text-gray-900">{{ $movements->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-exchange-alt text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">حركات الوارد</p>
                <p class="text-2xl font-bold text-green-600">{{ $movements->where('type', 'in')->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">حركات الصادر</p>
                <p class="text-2xl font-bold text-red-600">{{ $movements->where('type', 'out')->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-arrow-up text-red-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">حركات التحويل</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $movements->where('type', 'transfer')->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-arrows-alt-h text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('inventory.movements.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[300px]">
            <div class="relative">
                <input type="text" name="search" 
                       value="{{ request('search') }}"
                       placeholder="البحث عن حركة..." 
                       class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">جميع الأنواع</option>
            <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>وارد</option>
            <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>صادر</option>
            <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>تحويل</option>
        </select>
        
        <input type="date" name="date_from" value="{{ request('date_from') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="من تاريخ">
        
        <input type="date" name="date_to" value="{{ request('date_to') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="إلى تاريخ">
        
        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-filter ml-2"></i>
            فلترة
        </button>
        
        <a href="{{ route('inventory.movements.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-redo ml-2"></i>
            إعادة تعيين
        </a>
    </form>
</div>

<!-- Movements Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنصر</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السبب</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $movement->created_at->format('Y-m-d H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $movement->item_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
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
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $movement->quantity }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $movement->reason }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($movement->user_id)
                                    <span class="text-blue-600">{{ $movement->user_id }}</span>
                                @else
                                    <span class="text-gray-500">النظام</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('inventory.movements.show', $movement->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 rounded-full p-4 mb-4">
                                    <i class="fas fa-exchange-alt text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد حركات</h3>
                                <p class="text-gray-500 mb-4">لم يتم تسجيل أي حركات للمخزون بعد</p>
                                <a href="{{ route('inventory.movements.create') }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-plus ml-2"></i>
                                    إضافة حركة أولى
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(method_exists($movements, 'hasPages') && $movements->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $movements->links() }}
        </div>
    @endif
</div>
@endsection
