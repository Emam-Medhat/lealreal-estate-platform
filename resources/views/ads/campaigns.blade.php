@extends('layouts.app')

@section('title', 'الحملات الإعلانية')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">الحملات الإعلانية</h1>
                <p class="text-gray-600">إدارة وتنظيم الحملات الإعلانية</p>
            </div>
            <a href="{{ route('ads.campaigns.create') }}" 
               class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium flex items-center space-x-2 space-x-reverse transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                <i class="fas fa-plus"></i>
                <span>إنشاء حملة جديدة</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Campaigns -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي الحملات</p>
                    <p class="text-3xl font-bold">{{ $campaigns->count() }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-bullhorn ml-1"></i>
                        جميع الحملات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Campaigns -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">حملات نشطة</p>
                    <p class="text-3xl font-bold">{{ $campaigns->where('status', 'active')->count() }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-play-circle ml-1"></i>
                        الحملات النشطة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-play-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Spent -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">إجمالي الإنفاق</p>
                    <p class="text-3xl font-bold">{{ number_format($campaigns->sum('total_spent'), 2) }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-money-bill-wave ml-1"></i>
                        ريال سعودي
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Impressions -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">إجمالي الظهور</p>
                    <p class="text-3xl font-bold">{{ number_format($campaigns->sum('total_impressions')) }}</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-eye ml-1"></i>
                        إجمالي المشاهدات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-eye text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('ads.campaigns.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">بحث</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                           id="search" name="search" value="{{ request('search') }}" placeholder="بحث في الحملات...">
                </div>
                <div class="md:col-span-3">
                    <label for="objective" class="block text-sm font-medium text-gray-700 mb-2">الهدف</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                            id="objective" name="objective">
                        <option value="">جميع الأهداف</option>
                        <option value="awareness" {{ request('objective') == 'awareness' ? 'selected' : '' }}>زيادة الوعي</option>
                        <option value="traffic" {{ request('objective') == 'traffic' ? 'selected' : '' }}>زيادة الزيارات</option>
                        <option value="conversions" {{ request('objective') == 'conversions' ? 'selected' : '' }}>زيادة التحويلات</option>
                        <option value="engagement" {{ request('objective') == 'engagement' ? 'selected' : '' }}>زيادة التفاعل</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                            id="status" name="status">
                        <option value="">جميع الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>موقف</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                    <a href="{{ route('ads.campaigns.index') }}" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition-all duration-300 text-center">
                        <i class="fas fa-redo ml-2"></i>
                        إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحملة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الهدف</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الإعلانات</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الأداء</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الميزانية</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($campaigns as $campaign)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $campaign->name }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ Str::limit($campaign->description, 50) }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $campaign->start_date->format('Y-m-d') }} - {{ $campaign->end_date->format('Y-m-d') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $campaign->objective == 'awareness' ? 'bg-blue-100 text-blue-800' : 
                                       ($campaign->objective == 'traffic' ? 'bg-indigo-100 text-indigo-800' : 
                                       ($campaign->objective == 'conversions' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ $campaign->objective_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $campaign->status == 'active' ? 'bg-green-100 text-green-800' : 
                                       ($campaign->status == 'paused' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($campaign->status == 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ $campaign->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-center">
                                    <div class="font-semibold text-gray-900">{{ $campaign->ads->count() }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $campaign->ads->where('status', 'active')->count() }} نشط
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">ظهور:</span>
                                        <span class="font-medium">{{ number_format($campaign->total_impressions) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">نقرات:</span>
                                        <span class="font-medium">{{ number_format($campaign->total_clicks) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">CTR:</span>
                                        <span class="font-medium">{{ number_format($campaign->average_ctr, 2) }}%</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">الإجمالي:</span>
                                        <span class="font-medium">{{ number_format($campaign->total_spent, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">المتبقي:</span>
                                        <span class="font-medium">{{ number_format($campaign->budget->remaining_budget ?? 0, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1 mt-2">
                                        @php $width = $campaign->budget_utilization; @endphp
                                        <div class="bg-blue-600 h-1 rounded-full transition-all duration-300" style="width: {{ $width }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('ads.campaigns.show', $campaign->id) }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ads.campaigns.edit', $campaign->id) }}" 
                                       class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-150" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($campaign->status == 'draft')
                                        <form action="{{ route('ads.campaigns.launch', $campaign->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="إطلاق">
                                                <i class="fas fa-rocket"></i>
                                            </button>
                                        </form>
                                    @elseif($campaign->status == 'active')
                                        <form action="{{ route('ads.campaigns.pause', $campaign->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-150" title="إيقاف">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @elseif($campaign->status == 'paused')
                                        <form action="{{ route('ads.campaigns.resume', $campaign->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="استئناف">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('ads.campaigns.duplicate', $campaign->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150" title="تكرار">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('ads.campaigns.destroy', $campaign->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الحملة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-bullhorn text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-lg mb-4">لا توجد حملات حالياً</p>
                                    <a href="{{ route('ads.campaigns.create') }}" 
                                       class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                                        <i class="fas fa-plus ml-2"></i>
                                        إنشاء حملة جديدة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-sm text-gray-600">
                عرض {{ $campaigns->firstItem() }} - {{ $campaigns->lastItem() }} من {{ $campaigns->total() }} حملة
            </div>
            <div class="flex justify-center sm:justify-start">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
