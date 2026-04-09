@extends('layouts.app')

@section('title', 'المصروفات المالية')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-8 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-purple-500/5"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-3">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-3 mr-4 shadow-lg">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">المصروفات المالية</h1>
                                <p class="text-gray-600 text-lg mt-1">إدارة وتتبع جميع المصروفات</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.financial.dashboard') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center group">
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                        العودة للوحة التحكم
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-chart-pie text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-blue-700">إجمالي المصروفات</p>
                        <p class="text-3xl font-bold text-blue-900">{{ number_format($stats['total_expenses']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-yellow-700">معلقة</p>
                        <p class="text-3xl font-bold text-yellow-900">{{ number_format($stats['pending_expenses']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-green-700">معتمدة</p>
                        <p class="text-3xl font-bold text-green-900">{{ number_format($stats['approved_expenses']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-times-circle text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-red-700">مرفوضة</p>
                        <p class="text-3xl font-bold text-red-900">{{ number_format($stats['rejected_expenses']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-money-bill text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-purple-700">إجمالي المبلغ</p>
                        <p class="text-3xl font-bold text-purple-900">{{ number_format($stats['total_amount'], 2) }} ج.م</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border-2 border-indigo-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-indigo-700">هذا الشهر</p>
                        <p class="text-3xl font-bold text-indigo-900">{{ number_format($stats['this_month_expenses'], 2) }} ج.م</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-teal-50 to-teal-100 border-2 border-teal-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl p-4 shadow-lg">
                        <i class="fas fa-history text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-teal-700">الشهر الماضي</p>
                        <p class="text-3xl font-bold text-teal-900">{{ number_format($stats['last_month_expenses'], 2) }} ج.م</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-8">
                <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-6 flex items-center">
                    <i class="fas fa-chart-bar text-blue-500 ml-3 text-2xl"></i>
                    توزيع المصروفات حسب الفئة
                </h3>
                @if($categories->count() > 0)
                    <div class="space-y-4">
                        @foreach($categories as $category)
                            <div class="flex items-center justify-between p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-200 rounded-2xl hover:from-blue-100 hover:to-blue-200 transition-all duration-200 transform hover:scale-102">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full ml-4 shadow-lg"></div>
                                    <span class="font-bold text-blue-900 text-lg">{{ $category->name }}</span>
                                </div>
                                <div class="text-left">
                                    <span class="text-xl font-bold text-blue-900">{{ number_format($category->total, 2) }} ج.م</span>
                                    <span class="text-sm text-blue-600 block font-medium">{{ $category->count }} مصروف</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <i class="fas fa-inbox text-gray-300 text-7xl mb-6"></i>
                        <p class="text-gray-500 text-xl">لا توجد مصروفات حالياً</p>
                        <p class="text-gray-400 text-lg mt-3">سيتم عرض المصروفات هنا عند إضافتها</p>
                    </div>
                @endif
            </div>
            
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-8">
                <h3 class="text-2xl font-bold bg-gradient-to-r from-yellow-800 to-orange-600 bg-clip-text text-transparent mb-6 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 ml-3 text-2xl"></i>
                    نصائح سريعة
                </h3>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4 p-4 bg-white/80 rounded-xl hover:bg-white transition-colors duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="fas fa-check text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">مراجعة دورية</h4>
                            <p class="text-gray-600 text-base">قم بمراجعة المصروفات أسبوعياً للتحكم أفضل</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 p-4 bg-white/80 rounded-xl hover:bg-white transition-colors duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">وضع ميزانية</h4>
                            <p class="text-gray-600 text-base">حدد ميزانية شهرية لكل فئة</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 p-4 bg-white/80 rounded-xl hover:bg-white transition-colors duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="fas fa-tags text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg">تصنيف دقيق</h4>
                            <p class="text-gray-600 text-base">استخدم فئات دقيقة للتحليل الأفضل</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent flex items-center">
                    <i class="fas fa-list text-blue-500 ml-4 text-2xl"></i>
                    قائمة المصروفات
                </h3>
            </div>
            
            <div class="overflow-x-auto bg-white">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">الفئة</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">الوصف</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if($expenses->count() > 0)
                            @foreach($expenses as $expense)
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 transition-all duration-150">
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center ml-4 shadow-lg">
                                                <i class="fas fa-user text-white text-base"></i>
                                            </div>
                                            <span class="font-bold text-gray-900 text-lg">{{ $expense->user_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-sm font-bold rounded-full border-2 border-blue-300">
                                            {{ $expense->category_name ?? 'غير مصنف' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-gray-700 text-base">{{ $expense->description ?? '-' }}</td>
                                    <td class="px-6 py-4 font-bold text-xl text-gray-900">{{ number_format($expense->amount, 2) }} ج.م</td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        @if($expense->status == 'pending')
                                            <span class="px-4 py-2 bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 text-sm font-bold rounded-full border-2 border-yellow-300">معلقة</span>
                                        @elseif($expense->status == 'approved')
                                            <span class="px-4 py-2 bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-sm font-bold rounded-full border-2 border-green-300">معتمدة</span>
                                        @elseif($expense->status == 'rejected')
                                            <span class="px-4 py-2 bg-gradient-to-r from-red-100 to-red-200 text-red-800 text-sm font-bold rounded-full border-2 border-red-300">مرفوضة</span>
                                        @else
                                            <span class="px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 text-sm font-bold rounded-full border-2 border-gray-300">{{ $expense->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6 text-gray-700 text-base">{{ \Carbon\Carbon::parse($expense->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center bg-gradient-to-r from-gray-50 to-gray-100">
                                    <i class="fas fa-inbox text-gray-300 text-8xl mb-6"></i>
                                    <p class="text-gray-500 text-2xl font-bold">لا توجد مصروفات حالياً</p>
                                    <p class="text-gray-400 text-lg mt-4">سيتم عرض المصروفات هنا عند إضافتها</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($expenses, 'hasPages') && $expenses->hasPages())
                <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t-2 border-gray-200 flex items-center justify-between">
                    <div class="text-base text-gray-700 font-medium">
                        عرض {{ method_exists($expenses, 'firstItem') ? $expenses->firstItem() : $expenses->first() }} إلى {{ method_exists($expenses, 'lastItem') ? $expenses->lastItem() : $expenses->last() }} من {{ $expenses->total() }} نتيجة
                    </div>
                    <div class="flex items-center space-x-3">
                        {{ $expenses->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
