@extends('layouts.app')

@section('title', 'الشهادات')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">الشهادات</h1>
        <p class="text-gray-600">شهادات العملاء وتجاربهم</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-quote-right text-blue-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي الشهادات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $testimonials->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">المنشورة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $testimonials->where('status', 'published')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">في انتظار المراجعة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $testimonials->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">المميزة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $testimonials->where('featured', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('testimonials.search') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="ابحث في الشهادات...">
                        <button type="submit" class="absolute left-2 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في انتظار المراجعة</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع المشروع</label>
                    <select name="project_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">جميع الأنواع</option>
                        <option value="residential" {{ request('project_type') == 'residential' ? 'selected' : '' }}>سكني</option>
                        <option value="commercial" {{ request('project_type') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                        <option value="industrial" {{ request('project_type') == 'industrial' ? 'selected' : '' }}>صناعي</option>
                        <option value="land" {{ request('project_type') == 'land' ? 'selected' : '' }}>أرض</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between">
                <div class="flex space-x-2 space-x-reverse">
                    <a href="{{ route('testimonials.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
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
                <a href="{{ route('testimonials.create') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus ml-2"></i>إضافة شهادة
                </a>
                <a href="{{ route('testimonials.my') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-user ml-2"></i>شهاداتي
                </a>
            </div>
        @endif
    </div>

    <!-- Testimonials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($testimonials as $testimonial)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($testimonial->client_image)
                                    <img class="h-12 w-12 rounded-full object-cover" 
                                         src="{{ $testimonial->getClientImageUrl() }}" alt="">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="mr-3">
                                <p class="text-sm font-medium text-gray-900">{{ $testimonial->client_name }}</p>
                                @if($testimonial->client_position)
                                    <p class="text-xs text-gray-500">{{ $testimonial->client_position }}</p>
                                @endif
                                @if($testimonial->client_company)
                                    <p class="text-xs text-gray-500">{{ $testimonial->client_company }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-end space-y-1">
                            @if($testimonial->featured)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-star ml-1"></i>مميزة
                                </span>
                            @endif

                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($testimonial->status === 'published') bg-green-100 text-green-800
                                @elseif($testimonial->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $testimonial->getStatusText() }}
                            </span>
                        </div>
                    </div>

                    <!-- Rating -->
                    @if($testimonial->rating)
                        <div class="flex items-center mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial->rating)
                                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                                @else
                                    <i class="far fa-star text-gray-300 text-sm"></i>
                                @endif
                            @endfor
                            <span class="mr-2 text-sm text-gray-600">{{ $testimonial->rating }}/5</span>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $testimonial->title }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            {{ Str::limit($testimonial->content, 150) }}
                        </p>
                    </div>

                    <!-- Project Info -->
                    @if($testimonial->project_type)
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="fas fa-building ml-2"></i>
                            <span>{{ $testimonial->getProjectTypeText() }}</span>
                            @if($testimonial->project_location)
                                <span class="mr-2">• {{ $testimonial->project_location }}</span>
                            @endif
                        </div>
                    @endif

                    <!-- Video -->
                    @if($testimonial->hasVideo())
                        <div class="mb-4">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-lg overflow-hidden">
                                @if($testimonial->getVideoThumbnail())
                                    <img src="{{ $testimonial->getVideoThumbnail() }}" alt="Video thumbnail" 
                                         class="w-full h-full object-cover">
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <button class="bg-red-600 text-white rounded-full p-3 hover:bg-red-700">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Date -->
                    <div class="text-xs text-gray-500">
                        @if($testimonial->published_at)
                            منشور في {{ $testimonial->getPublishedDateArabic() }}
                        @else
                            {{ $testimonial->created_at->locale('ar')->translatedFormat('d F Y') }}
                        @endifICI
                    </厂的
                    </ suit
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-3 flex justify-between">
                    <a href="{{ route('testimonials.show', $testimonial->id) }}" 
                       class="text-blue-600 hover:text Georgia
                       text-sm font-medium">
                        <i class="fas fa-eye ml-1"></i>عرض
                    </a>

                    @if(auth()->user()->hasRole('admin') || auth()->user()->id === $testimonial->user_id)
                        <div class="flex space-x-2 space-x-reverse">
                            @if(auth()->user()->hasRole('admin'))
                                @if($testimonial->status === 'pending')
                                    <form action="{{ route('testimonials.approve', $testimonial->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('testimonials.feature', $testimonial->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
ANTA
                           大概率
                            <a href="{{ route('testimonials.edit', $testimonial->id) }}" 
                               class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">لا توجد شهادات حالياً</p>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('testimonials.create') }}" 
                       class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus ml-2"></i>إضافة شهادة
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($testimonials->hasPages())
        <div class="mt-8">
            {{ $testimonials->links() }}
        </div>
    @endif
</div>
@endsection
