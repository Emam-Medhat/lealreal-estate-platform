@extends('layouts.app')

@section('title', 'تقارير الضرائب')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 mb-8 border border-white/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-3">
                            <i class="fas fa-chart-bar ml-3"></i>
                            تقارير الضرائب
                        </h1>
                        <p class="text-gray-600 text-lg">عرض وتحليل تقارير الضرائب والامتثال الضريبي</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('taxes.index') }}" class="bg-white/80 backdrop-blur text-gray-700 px-6 py-3 rounded-xl hover:bg-white transition-all duration-300 shadow-md border border-gray-200">
                            <i class="fas fa-arrow-left ml-2"></i>
                            العودة
                        </a>
                        <button class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-download ml-2"></i>
                            تصدير تقرير
                        </button>
                        <button class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-3 rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-file-pdf ml-2"></i>
                            إنشاء PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-700 text-sm font-bold mb-2">إجمالي الضرائب</p>
                                <p class="text-4xl font-bold text-gray-900">{{ $totalTaxes ?? 0 }}</p>
                                <div class="mt-2 flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calculator ml-2 text-blue-600"></i>
                                    جميع الأنواع
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-calculator text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-700 text-sm font-bold mb-2">ضرائب نشطة</p>
                                <p class="text-4xl font-bold text-gray-900">{{ $activeTaxes ?? 0 }}</p>
                                <div class="mt-2 flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle ml-2 text-green-600"></i>
                                    مفعلة حالياً
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-check-circle text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-red-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-700 text-sm font-bold mb-2">ضرائب غير نشطة</p>
                                <p class="text-4xl font-bold text-gray-900">{{ $inactiveTaxes ?? 0 }}</p>
                                <div class="mt-2 flex items-center text-sm text-gray-600">
                                    <i class="fas fa-times-circle ml-2 text-red-600"></i>
                                    معطلة حالياً
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-times-circle text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-700 text-sm font-bold mb-2">ضرائب العقارات</p>
                                <p class="text-4xl font-bold text-gray-900">{{ $propertyTaxes ?? 0 }}</p>
                                <div class="mt-2 flex items-center text-sm text-gray-600">
                                    <i class="fas fa-home ml-2 text-purple-600"></i>
                                    عقارية فقط
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-home text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Taxes Table -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 border-b border-gray-200/50">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center mb-4 lg:mb-0">
                            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl p-2 ml-3 shadow-lg">
                                <i class="fas fa-list text-white"></i>
                            </div>
                            قائمة الضرائب
                        </h3>
                        
                        <!-- Filters -->
                        <form method="GET" class="flex flex-wrap gap-3">
                            <select name="tax_type" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                                <option value="">جميع الأنواع</option>
                                <option value="property" {{ request('tax_type') == 'property' ? 'selected' : '' }}>عقاري</option>
                                <option value="sales" {{ request('tax_type') == 'sales' ? 'selected' : '' }}>مبيعات</option>
                                <option value="income" {{ request('tax_type') == 'income' ? 'selected' : '' }}>دخل</option>
                            </select>
                            
                            <select name="is_active" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                                <option value="">جميع الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                            
                            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="من تاريخ" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            
                            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="إلى تاريخ" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            
                            <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-filter ml-2"></i>
                                فلترة
                            </button>
                            
                            <a href="{{ route('taxes.reports.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-redo ml-2"></i>
                                إعادة تعيين
                            </a>
                        </form>
                    </div>
                </div>
                
                <div class="p-6">
                    @if(isset($taxes) && $taxes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الاسم</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">النوع</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">النسبة</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاريخ البدء</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($taxes as $tax)
                                        <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-blue-50 transition-all duration-300 hover:shadow-md">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900">{{ $tax->name }}</div>
                                                <div class="text-xs text-gray-600">{{ $tax->description }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $tax->tax_type }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-bold">
                                                <span class="bg-gradient-to-r from-purple-100 to-blue-100 text-purple-700 px-2 py-1 rounded-lg">{{ $tax->rate }}%</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                                    @if($tax->is_active) bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300
                                                    @else bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300 @endif">
                                                    @if($tax->is_active) 
                                                        <i class="fas fa-check-circle ml-1"></i> نشط
                                                    @else 
                                                        <i class="fas fa-times-circle ml-1"></i> غير نشط 
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $tax->effective_date ? $tax->effective_date->format('Y-m-d') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-reverse space-x-2">
                                                    <a href="{{ route('taxes.show', $tax) }}" class="text-purple-600 hover:text-purple-800 transition-colors duration-300 hover:scale-110 transform" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('taxes.edit', $tax) }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-300 hover:scale-110 transform" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $taxes->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-chart-bar text-3xl text-purple-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">لا توجد ضرائب</h3>
                            <p class="text-gray-500 mb-6">لم يتم إضافة أي ضرائب حتى الآن</p>
                            <a href="{{ route('taxes.create') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-plus ml-2"></i>
                                إضافة ضريبة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.backdrop-blur-lg {
    backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
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
</style>
@endsection
