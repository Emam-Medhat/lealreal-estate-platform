@extends('admin.layouts.admin')

@section('title', 'المخزون - العناصر')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">عناصر المخزون</h1>
            <p class="text-gray-600 mt-1">إدارة عناصر المخزون والمواد</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.items.create') }}" 
               class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-plus"></i>
                <span>عنصر جديد</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي العناصر</p>
                <p class="text-2xl font-bold text-blue-600">{{ $items->total() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-boxes text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">القيمة الإجمالية</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($items->sum(function($item) { return $item->quantity * $item->unit_cost; }), 2) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">منخفض المخزون</p>
                <p class="text-2xl font-bold text-orange-600">{{ $items->where('quantity', '<=', $items->first()->min_quantity ?? 0)->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">نفد المخزون</p>
                <p class="text-2xl font-bold text-red-600">{{ $items->where('quantity', 0)->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <form method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="ابحث بالاسم، الكود، أو الوصف">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="tools" {{ request('category') == 'tools' ? 'selected' : '' }}>أدوات</option>
                    <option value="materials" {{ request('category') == 'materials' ? 'selected' : '' }}>مواد</option>
                    <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>معدات</option>
                    <option value="supplies" {{ request('category') == 'supplies' ? 'selected' : '' }}>لوازم</option>
                    <option value="safety" {{ request('category') == 'safety' ? 'selected' : '' }}>سلامة</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>متوقف</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>نفد المخزون</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مستوى المخزون</label>
                <select name="stock_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>منخفض</option>
                    <option value="normal" {{ request('stock_level') == 'normal' ? 'selected' : '' }}>طبيعي</option>
                    <option value="high" {{ request('stock_level') == 'high' ? 'selected' : '' }}>مرتفع</option>
                </select>
            </div>
        </div>
        
        <div class="flex items-center justify-end">
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
        </div>
    </form>
</div>

<!-- Items Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($items->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الكود</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الاسم</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الفئة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الكمية</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الوحدة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">التكلفة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">القيمة الإجمالية</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $item->item_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                    @if($item->description)
                                        <div class="text-sm text-gray-500 truncate">{{ Str::limit($item->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    @if($item->category == 'tools') أدوات
                                    @elseif($item->category == 'materials') مواد
                                    @elseif($item->category == 'equipment') معدات
                                    @elseif($item->category == 'supplies') لوازم
                                    @elseif($item->category == 'safety') سلامة
                                    @else أخرى
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="text-sm font-medium {{ $item->quantity <= $item->min_quantity ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $item->quantity }}
                                    </span>
                                    @if($item->quantity <= $item->min_quantity)
                                        <i class="fas fa-exclamation-triangle text-red-500 text-xs" title="منخفض المخزون"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $item->unit }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ number_format($item->unit_cost, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ number_format($item->quantity * $item->unit_cost, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($item->status == 'active') bg-green-100 text-green-700
                                    @elseif($item->status == 'inactive') bg-gray-100 text-gray-700
                                    @elseif($item->status == 'discontinued') bg-red-100 text-red-700
                                    @else bg-orange-100 text-orange-700
                                    @endif">
                                    @if($item->status == 'active') نشط
                                    @elseif($item->status == 'inactive') غير نشط
                                    @elseif($item->status == 'discontinued') متوقف
                                    @else نفد المخزون
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('inventory.items.show', $item) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory.items.edit', $item) }}" 
                                       class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventory.items.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                title="حذف"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    عرض {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} من {{ $items->total() }} عنصر
                </div>
                <div class="flex items-center space-x-2 space-x-reverse">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <i class="fas fa-boxes text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">لا توجد عناصر</h3>
            <p class="text-gray-600 mb-8">ابدأ بإضافة عناصر جديدة إلى المخزون</p>
            <a href="{{ route('inventory.items.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-plus"></i>
                <span>عنصر جديد</span>
            </a>
        </div>
    @endif
</div>
@endsection
