@extends('layouts.app')

@section('title', 'الاستبيانات')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">الاستبيانات</h1>
        <p class="text-gray-600">شارك رأيك وساعدنا على تحسين خدماتنا</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-poll text-blue-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي الاستبيانات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $surveys->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-play-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">النشطة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $surveys->where('status', 'published')->where('starts_at', '<=', now())->where(function($q) { $q->where('expires_at', '>=', now())->orWhereNull('expires_at'); })->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي الردود</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $surveys->sum('response_count') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">متوسط المشاركة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($surveys->avg('response_count') ?? 0, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('surveys.search') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="ابحث في الاستبيانات...">
                        <button type="submit" class="absolute left-2 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">جميع الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الجمهور المستهدف</label>
                    <select name="target_audience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">جميع الجماهير</option>
                        <option value="all_users" {{ request('target_audience') == 'all_users' ? 'selected' : '' }}>جميع المستخدمين</option>
                        <option value="property_owners" {{ request('target_audience') == 'property_owners' ? 'selected' : '' }}>أصحاب العقارات</option>
                        <option value="agents" {{ request('target_audience') == 'agents' ? 'selected' : '' }}>الوكلاء</option>
                        <option value="buyers" {{ request('target_audience') == 'buyers' ? 'selected' : '' }}>المشترون</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between">
                <div class="flex space-x-2 space-x-reverse">
                    <a href="{{ route('surveys.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-redo ml-2"></i>إعادة تعيين
                    </a>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter ml-2"></i>تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between mb-6">
        @if(auth()->user()->hasRole('admin'))
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('surveys.create') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus ml-2"></i>إنشاء استبيان
                </a>
                <a href="{{ route('surveys.my') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-user ml-2"></i>استبياناتي
                </a>
            </div>
        @endif
    </div>

    <!-- Surveys Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($surveys as $survey)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-poll text-blue-600 text-xl"></i>
                            </div>
                            <div class="mr-3">
                                <p class="text-sm font-medium text-gray-900">{{ $survey->getTargetAudienceText() }}</p>
                                <p class="text-xs text-gray-500">{{ $survey->questions->count() }} أسئلة</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end space-y-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($survey->status === 'published') bg-green-100 text-green-800
                                @elseif($survey->status === 'draft') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $survey->getStatusText() }}
                            </span>

                            @if($survey->isActive())
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    نشط
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Title and Description -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $survey->title }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        {{ Str::limit($survey->description, 100) }}
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $survey->response_count }}</p>
                            <p class="text-xs text-gray-500">مشاركة</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $survey->getCompletionRate() }}%</p>
                            <p class="text-xs text-gray-500">مشاركة</p>
                        </div>
                    </div>

                    <!-- Time Info -->
                    <div class="space-y-2 text-sm text-gray-500">
                        @if($survey->starts_at)
                            <div class="flex items-center">
                                <i class="fas fa-calendar ml-2"></i>
                                <span>يبدأ: {{ $survey->getStartDateArabic() }}</span>
                            </div>
                        @endif
                        @if($survey->expires_at)
                            <div class="flex items-center">
                                <i class="fas fa-clock ml-2"></i>
                                <span>ينتهي: {{ $survey->getEndDateArabic() }}</span>
                            </div>
                        @endif
                        @if($survey->isUpcoming())
                            <div class="flex items-center text-blue-600">
                                <i class="fas fa-hourglass-start ml-2"></i>
                                <span>سيبدأ {{ $survey->getRemainingDaysText() }}</span>
                            </div>
                        @elseif($survey->isExpired())
                            <div class="flex items-center text-red-600">
                                <i class="fas fa-hourglass-end ml-2"></i>
                                <span>منتهي</span>
                            </div>
                        @else
                            <div class="flex items-center text-green-600">
                                <i class="fas fa-hourglass-half ml-2"></i>
                                <span>متبقي {{ $survey->getRemainingDaysText() }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Duration -->
                    <div class="mt-3 text-xs text-gray-500">
                        المدة: {{ $survey->getDurationText() }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-3 flex justify-between">
                    @if($survey->canBeParticipatedBy(auth()->user()))
                        <a href="{{ route('surveys.show', $survey->id) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-play ml-1"></i>المشاركة
                        </a>
                    @else
                        <a href="{{ route('surveys.show', $survey->id) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-eye ml-1"></i>عرض
                        </a>
                    @endif

                    @if(auth()->user()->hasRole('admin') || auth()->user()->id === $survey->created_by)
                        <div class="flex space-x-2 space-x-reverse">
                            <a href="{{ route('surveys.results', $survey->id) }}" 
                               class="text-purple-600 hover:text-purple-800">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            @if(auth()->user()->hasRole('admin'))
                                @if($survey->status === 'draft')
                                    <form action="{{ route('surveys.publish', $survey->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                @elseif($survey->status === 'published')
                                    <form action="{{ route('surveys.close', $survey->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('surveys.edit', $survey->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">لا توجد استبيانات حالياً</p>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('surveys.create') }}" 
                       class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus ml-2"></i>إنشاء استبيان
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($surveys->hasPages())
        <div class="mt-8">
            {{ $surveys->links() }}
        </div>
    @endif
</div>
@endsection
