@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
<div class="container mx-auto px-4 py-8">
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full -ml-24 -mb-24"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4 backdrop-blur-sm border border-white border-opacity-30">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-1">التقارير</h1>
                            <p class="text-white text-lg">إدارة وعرض جميع التقارير المتاحة</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-reverse space-x-3">
                    <a href="{{ route('reports.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-chart-line ml-2"></i>
                        لوحة التحكم
                    </a>
                    <a href="{{ route('reports.create') }}" class="inline-flex items-center px-6 py-3 bg-slate-600 text-white rounded-xl text-sm font-medium hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء تقرير
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Filters -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-8">
    <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-bold text-black mb-2">البحث</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200"
                   placeholder="ابحث عن تقرير...">
        </div>
        <div>
            <label class="block text-sm font-bold text-black mb-2">نوع التقرير</label>
            <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200">
                <option value="">جميع الأنواع</option>
                <option value="sales" {{ request('type') == 'sales' ? 'selected' : '' }}>المبيعات</option>
                <option value="performance" {{ request('type') == 'performance' ? 'selected' : '' }}>الأداء</option>
                <option value="market" {{ request('type') == 'market' ? 'selected' : '' }}>السوق</option>
                <option value="financial" {{ request('type') == 'financial' ? 'selected' : '' }}>المالي</option>
                <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>مخصص</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-black mb-2">الحالة</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200">
                <option value="">جميع الحالات</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="generating" {{ request('status') == 'generating' ? 'selected' : '' }}>قيد الإنشاء</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:ring-slate-500 transition-all duration-200 transform hover:scale-105 shadow-md">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
        </div>
    </form>
</div>

<!-- Reports List -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">
                        التقرير
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">
                        النوع
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">
                        الحالة
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">
                        الإنشاء
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">
                        الحجم
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">
                        المشاهدات
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">
                        الإجراءات
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($reports as $report)
                    <tr class="hover:bg-gray-50 transition-all duration-200 hover:shadow-sm">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-black">{{ $report->title }}</div>
                                @if ($report->description)
                                    <div class="text-sm text-black">{{ Str::limit($report->description, 50) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-black border border-blue-200">
                                {{ __('reports.types.' . $report->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($report->status === 'completed') bg-green-100 text-black border border-green-200
                                @elseif($report->status === 'generating') bg-blue-100 text-black border border-blue-200
                                @elseif($report->status === 'failed') bg-red-100 text-black border border-red-200
                                @elseif($report->status === 'scheduled') bg-purple-100 text-black border border-purple-200
                                @else bg-gray-100 text-black border border-gray-200 @endif">
                                @if($report->status === 'completed') مكتمل
                                @elseif($report->status === 'generating') قيد الإنشاء
                                @elseif($report->status === 'failed') فشل
                                @elseif($report->status === 'scheduled') مجدول
                                @else غير معروف @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            {{ $report->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            {{ $report->status === 'completed' && $report->file_path ? '2.5 MB' : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                {{ $report->view_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-reverse space-x-2">
                                    @if ($report->status === 'completed' && $report->file_path)
                                        <a href="{{ route('reports.download', $report) }}" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="تحميل">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('reports.show', $report) }}" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($report->status === 'completed')
                                        <a href="{{ route('reports.regenerate', $report) }}" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="إعادة إنشاء">
                                            <i class="fas fa-sync"></i>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('reports.destroy', $report) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا التقرير؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-black">
                                <div class="py-8">
                                    <i class="fas fa-file-alt text-6xl text-gray-400 mb-4"></i>
                                    <h3 class="text-xl font-semibold text-black mb-2">لا توجد تقارير</h3>
                                    <p class="text-black mb-6">ابدأ بإنشاء أول تقرير لك</p>
                                    <a href="{{ route('reports.create') }}" class="inline-flex items-center px-6 py-3 bg-slate-600 text-white rounded-xl text-sm font-medium hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-600 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                        <i class="fas fa-plus ml-2"></i>
                                        إنشاء تقرير
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($reports->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
