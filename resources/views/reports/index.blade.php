@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">التقارير</h1>
            <p class="text-gray-600 mt-2">إدارة وعرض جميع التقارير المتاحة</p>
        </div>
        <div class="flex space-x-4 space-x-reverse">
            <a href="{{ route('reports.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-chart-line ml-2"></i>
                لوحة التحكم
            </a>
            <a href="{{ route('reports.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus ml-2"></i>
                إنشاء تقرير
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="ابحث عن تقرير...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع التقرير</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الأنواع</option>
                    <option value="sales" {{ request('type') == 'sales' ? 'selected' : '' }}>المبيعات</option>
                    <option value="performance" {{ request('type') == 'performance' ? 'selected' : '' }}>الأداء</option>
                    <option value="market" {{ request('type') == 'market' ? 'selected' : '' }}>السوق</option>
                    <option value="financial" {{ request('type') == 'financial' ? 'selected' : '' }}>المالي</option>
                    <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>مخصص</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="generating" {{ request('status') == 'generating' ? 'selected' : '' }}>قيد الإنشاء</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter ml-2"></i>
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التقرير
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإنشاء
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحجم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المشاهدات
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                                    @if ($report->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($report->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ __('reports.types.' . $report->type, $report->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $report->getStatusColorAttribute() === 'green' ? 'bg-green-100 text-green-800' : ($report->getStatusColorAttribute() === 'blue' ? 'bg-blue-100 text-blue-800' : ($report->getStatusColorAttribute() === 'red' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $report->getStatusLabelAttribute() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->getFormattedFileSizeAttribute() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->view_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <div class="flex space-x-2 space-x-reverse">
                                    @if ($report->status === 'completed' && $report->file_path)
                                        <a href="{{ route('reports.download', $report) }}" class="text-blue-600 hover:text-blue-900" title="تحميل">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('reports.show', $report) }}" class="text-green-600 hover:text-green-900" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($report->status === 'completed')
                                        <a href="{{ route('reports.regenerate', $report) }}" class="text-yellow-600 hover:text-yellow-900" title="إعادة إنشاء">
                                            <i class="fas fa-sync"></i>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('reports.destroy', $report) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا التقرير؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد تقارير</p>
                                    <p class="text-sm">ابدأ بإنشاء أول تقرير لك</p>
                                    <a href="{{ route('reports.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
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
