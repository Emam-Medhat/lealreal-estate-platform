@extends('admin.layouts.admin')

@section('title', 'الموردين')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الموردين</h1>
            <p class="text-gray-600 mt-1">إدارة الموردين ومعلومات الاتصال</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.suppliers.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-plus"></i>
                <span>إضافة مورد</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي الموردين</p>
                <p class="text-2xl font-bold text-gray-900">{{ $suppliers->total() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-truck text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">موردين نشطين</p>
                <p class="text-2xl font-bold text-green-600">{{ $suppliers->where('is_active', true)->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">موردين غير نشطين</p>
                <p class="text-2xl font-bold text-red-600">{{ $suppliers->where('is_active', false)->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('inventory.suppliers.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[300px]">
            <div class="relative">
                <input type="text" name="search" 
                       value="{{ request('search') }}"
                       placeholder="البحث عن مورد..." 
                       class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">جميع الحالات</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
        </select>
        
        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-filter ml-2"></i>
            فلترة
        </button>
        
        <a href="{{ route('inventory.suppliers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-redo ml-2"></i>
            إعادة تعيين
        </a>
    </form>
</div>

<!-- Suppliers Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المورد</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاتصال</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="bg-gray-100 rounded-full p-2">
                                    <i class="fas fa-truck text-gray-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $supplier->name }}</div>
                                    @if($supplier->code)
                                        <div class="text-sm text-gray-500">كود: {{ $supplier->code }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($supplier->contact_person)
                                    {{ $supplier->contact_person }}
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($supplier->email)
                                    <a href="mailto:{{ $supplier->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $supplier->email }}
                                    </a>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($supplier->phone)
                                    <a href="tel:{{ $supplier->phone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $supplier->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($supplier->is_active)
                                    bg-green-100 text-green-800
                                @else
                                    bg-red-100 text-red-800
                                @endif">
                                @if($supplier->is_active)
                                    نشط
                                @else
                                    غير نشط
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('inventory.suppliers.show', $supplier) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.suppliers.edit', $supplier) }}" 
                                   class="text-green-600 hover:text-green-800 transition-colors duration-150"
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('inventory.suppliers.destroy', $supplier) }}" 
                                      class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 rounded-full p-4 mb-4">
                                    <i class="fas fa-truck text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد موردين</h3>
                                <p class="text-gray-500 mb-4">لم يتم إضافة أي موردين بعد</p>
                                <a href="{{ route('inventory.suppliers.create') }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-plus ml-2"></i>
                                    إضافة مورد أول
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($suppliers->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $suppliers->links() }}
        </div>
    @endif
</div>
@endsection
