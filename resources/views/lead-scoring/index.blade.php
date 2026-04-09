@extends('admin.layouts.admin')

@section('title', 'تقييم العملاء')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تقييم العملاء</h1>
            <p class="text-gray-600 mt-1">إدارة وتتبع نظام تقييم العملاء المحتملين</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-scoring.create') }}" 
               class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-plus"></i>
                <span>قاعدة تقييم جديدة</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي التقييمات</p>
                <p class="text-2xl font-bold text-gray-900">{{ $scores->total() }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-chart-bar text-purple-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">متوسط التقييم</p>
                <p class="text-2xl font-bold text-blue-600">{{ $scores->isNotEmpty() ? number_format($scores->avg('score'), 1) : 0 }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-calculator text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">أعلى تقييم</p>
                <p class="text-2xl font-bold text-green-600">{{ $scores->isNotEmpty() ? number_format($scores->max('score'), 1) : 0 }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-trophy text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">أدنى تقييم</p>
                <p class="text-2xl font-bold text-orange-600">{{ $scores->isNotEmpty() ? number_format($scores->min('score'), 1) : 0 }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-arrow-down text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Lead Scores Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($scores->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">العميل</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">التقييم</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المصدر</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المُقيّم</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">التاريخ</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($scores as $score)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="bg-gray-200 rounded-full p-2">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $score->lead->first_name }} {{ $score->lead->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $score->lead->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ min($score->score, 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($score->score, 1) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($score->lead->status)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $score->lead->status->color }}20; color: {{ $score->lead->status->color }}">
                                        {{ $score->lead->status->name }}
                                    </span>
                                @else
                                    <span class="text-gray-500 text-sm">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($score->lead->source)
                                    <span class="text-sm text-gray-600">{{ $score->lead->source->name }}</span>
                                @else
                                    <span class="text-gray-500 text-sm">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($score->calculatedBy)
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <div class="bg-gray-200 rounded-full p-1">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $score->calculatedBy->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">نظام</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $score->created_at->format('Y-m-d H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('lead-scoring.show', $score) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('lead-scoring.edit', $score) }}" 
                                       class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('lead-scoring.destroy', $score) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                title="حذف"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
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
                    عرض {{ $scores->firstItem() ?? 0 }} - {{ $scores->lastItem() ?? 0 }} من {{ $scores->total() }} تقييم
                </div>
                <div class="flex items-center space-x-2 space-x-reverse">
                    {{ $scores->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <i class="fas fa-chart-bar text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">لا توجد تقييمات</h3>
            <p class="text-gray-600 mb-8">ابدأ بإنشاء قواعد تقييم للعملاء المحتملين</p>
            <a href="{{ route('lead-scoring.create') }}" 
               class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-plus"></i>
                <span>قاعدة تقييم جديدة</span>
            </a>
        </div>
    @endif
</div>
@endsection
