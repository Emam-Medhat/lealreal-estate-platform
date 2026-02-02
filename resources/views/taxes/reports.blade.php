@extends('admin.layouts.admin')

@section('title', 'تقارير الضرائب')

@push('styles')
<style>
/* Custom animations and styles */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.animate-slide-in {
    animation: slideInRight 0.5s ease-out;
}

.animate-pulse-once {
    animation: pulse 0.3s ease-in-out;
}

/* Card hover effects */
.card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Button effects */
.btn-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: all 0.3s ease;
}

.btn-gradient:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b4199 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Enhanced table styles */
.table-modern {
    border-collapse: separate;
    border-spacing: 0;
}

.table-modern thead th {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table-modern tbody tr {
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: #f8fafc;
    transform: scale(1.01);
}

/* Badge styles */
.badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.badge-modern:hover {
    transform: scale(1.05);
}

/* Loading skeleton */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Enhanced pagination */
.pagination-modern {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    align-items: center;
}

.pagination-modern .page-link {
    border: 2px solid #e2e8f0;
    color: #64748b;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.pagination-modern .page-link:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background-color: #eff6ff;
}

.pagination-modern .page-item.active .page-link {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-color: #3b82f6;
    color: white;
}

/* Glass morphism effect */
.glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Enhanced form controls */
.form-modern {
    transition: all 0.2s ease;
    border: 2px solid #e2e8f0;
}

.form-modern:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Status indicators */
.status-indicator {
    position: relative;
    display: inline-flex;
    align-items: center;
}

.status-indicator::before {
    content: '';
    position: absolute;
    right: -8px;
    top: 50%;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.status-indicator.active::before {
    background-color: #10b981;
}

.status-indicator.inactive::before {
    background-color: #ef4444;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-8 animate-fade-in">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 px-8 py-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-black opacity-10"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="animate-slide-in">
                            <h1 class="text-4xl font-bold text-white flex items-center mb-2">
                                <i class="fas fa-chart-bar ml-4 text-3xl"></i>
                                تقارير الضرائب
                            </h1>
                            <p class="text-blue-100 text-lg">عرض وتحليل تقارير الضرائب والامتثال الضريبي</p>
                        </div>
                        
                        <div class="flex space-x-reverse space-x-4 animate-slide-in" style="animation-delay: 0.2s;">
                            <a href="{{ route('taxes.index') }}" 
                               class="bg-white text-blue-600 px-6 py-3 rounded-xl hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                                <i class="fas fa-arrow-left ml-2"></i>
                                العودة
                            </a>
                            <button class="btn-gradient text-white px-6 py-3 rounded-xl font-medium shadow-lg">
                                <i class="fas fa-download ml-2"></i>
                                تصدير تقرير
                            </button>
                            <button class="bg-white text-blue-600 px-6 py-3 rounded-xl hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold">
                                <i class="fas fa-file-pdf ml-2"></i>
                                إنشاء PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Taxes -->
            <div class="card-hover bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">إجمالي الضرائب</p>
                        <p class="text-4xl font-bold mb-2">{{ $totalTaxes }}</p>
                        <p class="text-blue-100 text-xs">
                            <i class="fas fa-calculator ml-1"></i>
                            جميع الأنواع
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4 animate-pulse-once">
                        <i class="fas fa-calculator text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Taxes -->
            <div class="card-hover bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">الضرائب النشطة</p>
                        <p class="text-4xl font-bold mb-2">{{ $activeTaxes }}</p>
                        <p class="text-green-100 text-xs">
                            <i class="fas fa-check-circle ml-1"></i>
                            حالياً نشطة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4 animate-pulse-once" style="animation-delay: 0.3s;">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Inactive Taxes -->
            <div class="card-hover bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in" style="animation-delay: 0.3s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium mb-1">الضرائب غير النشطة</p>
                        <p class="text-4xl font-bold mb-2">{{ $inactiveTaxes }}</p>
                        <p class="text-yellow-100 text-xs">
                            <i class="fas fa-pause-circle ml-1"></i>
                            غير نشطة
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4 animate-pulse-once" style="animation-delay: 0.4s;">
                        <i class="fas fa-pause-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Property Taxes -->
            <div class="card-hover bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg animate-fade-in" style="animation-delay: 0.4s;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">ضرائب العقارات</p>
                        <p class="text-4xl font-bold mb-2">{{ $propertyTaxes }}</p>
                        <p class="text-purple-100 text-xs">
                            <i class="fas fa-home ml-1"></i>
                            عقارية فقط
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4 animate-pulse-once" style="animation-delay: 0.5s;">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Reports Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('taxes.reports.analytics') }}" 
               class="card-hover glass-effect rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 group animate-fade-in" style="animation-delay: 0.5s;">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">التحليلات</h3>
                        <p class="text-sm text-gray-600 mt-1">عرض رسوم بيانية وتحليلات متقدمة</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-3 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('taxes.reports.summary') }}" 
               class="card-hover glass-effect rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 group animate-fade-in" style="animation-delay: 0.6s;">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600 transition-colors">ملخص</h3>
                        <p class="text-sm text-gray-600 mt-1">ملخص شامل للضرائب والإيرادات</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-3 group-hover:bg-green-200 transition-colors">
                        <i class="fas fa-file-alt text-green-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('taxes.reports.compliance') }}" 
               class="card-hover glass-effect rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 group animate-fade-in" style="animation-delay: 0.7s;">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">الامتثال</h3>
                        <p class="text-sm text-gray-600 mt-1">تقارير الامتثال الضريبي</p>
                    </div>
                    <div class="bg-purple-100 rounded-lg p-3 group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('taxes.reports.trends') }}" 
               class="card-hover glass-effect rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-300 group animate-fade-in" style="animation-delay: 0.8s;">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600 transition-colors">الاتجاهات</h3>
                        <p class="text-sm text-gray-600 mt-1">اتجاهات الضرائب وتحليلها</p>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-3 group-hover:bg-orange-200 transition-colors">
                        <i class="fas fa-chart-area text-orange-600 text-xl"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('taxes.reports.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" placeholder="بحث عن ضريبة..." 
                               value="{{ request('search') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">جميع الأنواع</option>
                            <option value="property" {{ request('type') == 'property' ? 'selected' : '' }}>ضريبة العقارات</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>ضريبة الدخل</option>
                            <option value="capital_gains" {{ request('type') == 'capital_gains' ? 'selected' : '' }}>ضريبة الأرباح الرأسمالية</option>
                            <option value="vat" {{ request('type') == 'vat' ? 'selected' : '' }}>ضريبة القيمة المضافة</option>
                            <option value="stamp_duty" {{ request('type') == 'stamp_duty' ? 'selected' : '' }}>ضريبة الطابع</option>
                            <option value="municipality" {{ request('type') == 'municipality' ? 'selected' : '' }}>ضريبة البلدية</option>
                            <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div>
                        <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشطة</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشطة</option>
                        </select>
                    </div>
                    <div class="flex space-x-reverse space-x-2">
                        <input type="date" name="date_from" 
                               value="{{ request('date_from') }}"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="من تاريخ">
                        <input type="date" name="date_to" 
                               value="{{ request('date_to') }}"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="إلى تاريخ">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-search ml-2"></i>
                            بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Taxes Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-blue-600 ml-2"></i>
                    قائمة الضرائب
                    <span class="mr-auto text-sm text-gray-500">{{ $taxes->total() }} ضريبة</span>
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                @if($taxes->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرقم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النسبة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ السريان</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($taxes as $tax)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tax->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $tax->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $tax->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($tax->type == 'vat') bg-blue-100 text-blue-800
                                        @elseif($tax->type == 'income') bg-green-100 text-green-800
                                        @elseif($tax->type == 'property') bg-purple-100 text-purple-800
                                        @elseif($tax->type == 'capital_gains') bg-orange-100 text-orange-800
                                        @elseif($tax->type == 'stamp_duty') bg-red-100 text-red-800
                                        @elseif($tax->type == 'municipality') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($tax->type == 'vat') ضريبة القيمة المضافة
                                        @elseif($tax->type == 'income') ضريبة الدخل
                                        @elseif($tax->type == 'property') ضريبة العقارات
                                        @elseif($tax->type == 'capital_gains') ضريبة الأرباح الرأسمالية
                                        @elseif($tax->type == 'stamp_duty') ضريبة الطابع
                                        @elseif($tax->type == 'municipality') ضريبة البلدية
                                        @else {{ $tax->type }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($tax->rate, 2) }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($tax->is_active) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($tax->is_active) نشط @else غير نشط @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tax->effective_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-left">
                                    <div class="flex items-center space-x-2">
                                        <a href="#" class="text-blue-600 hover:text-blue-900 transition-colors" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                عرض {{ $taxes->firstItem() }} إلى {{ $taxes->lastItem() }} من {{ $taxes->total() }} نتيجة
                            </div>
                            {{ $taxes->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-bar text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد ضرائب</h3>
                        <p class="text-gray-500 mb-6">لم يتم العثور على أي ضرائب تطابق معايير البحث</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Export and Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-download text-blue-600 ml-2"></i>
                    التصدير والإجراءات
                </h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-md font-medium text-gray-700 mb-3">تصدير البيانات</h4>
                    <div class="space-y-3">
                        <button class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">
                            <i class="fas fa-file-excel ml-2"></i>
                            تصدير Excel
                        </button>
                        <button class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-file-pdf ml-2"></i>
                            تصدير PDF
                        </button>
                        <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-file-csv ml-2"></i>
                            تصدير CSV
                        </button>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-md font-medium text-gray-700 mb-3">تقارير مخصص</h4>
                    <div class="space-y-3">
                        <button class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors font-medium">
                            <i class="fas fa-magic ml-2"></i>
                            إنشاء تقرير مخصص
                        </button>
                        <button class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                            <i class="fas fa-clock ml-2"></i>
                            جدولة التقرير
                        </button>
                        <button class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors font-medium">
                            <i class="fas fa-cog ml-2"></i>
                            إعدادات التقارير
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    });

    document.querySelectorAll('.bg-white').forEach(el => {
        observer.observe(el);
    });

    // Export functionality
    const exportButtons = document.querySelectorAll('button[class*="bg-"][class*="text-white"]');
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const buttonText = this.textContent.trim();
            if (buttonText.includes('Excel')) {
                // Handle Excel export
                console.log('Exporting to Excel...');
            } else if (buttonText.includes('PDF')) {
                // Handle PDF export
                console.log('Exporting to PDF...');
            } else if (buttonText.includes('CSV')) {
                // Handle CSV export
                console.log('Exporting to CSV...');
            }
        });
    });
});
</script>

@push('styles')
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Pagination styling */
.pagination {
    display: flex;
    gap: 0.25rem;
}

.pagination .page-item .page-link {
    border: none;
    color: #6b7280;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.pagination .page-item.active .page-link {
    background-color: #2563eb;
    color: white;
}

.pagination .page-item:not(.active):not(.disabled) .page-link:hover {
    background-color: #f3f4f6;
    color: #1f2937;
}
</style>
@endpush
