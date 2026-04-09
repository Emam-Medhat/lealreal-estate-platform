@extends('layouts.app')

@section('title', $review->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('reviews.index') }}" class="text-gray-700 hover:text-gray-900">
                    <i class="fas fa-home ml-2"></i>
                    التقييمات
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-left text-gray-400 ml-2"></i>
                    <span class="text-gray-500">{{ $review->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Review Header -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $review->title }}</h1>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star text-yellow-400"></i>
                            @else
                                <i class="far fa-star text-gray-300"></i>
                            @endif
                        @endfor
                        <span class="mr-2 text-lg font-bold">{{ $review->rating }}</span>
                    </div>
                    
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        @if($review->status === 'approved') bg-green-100 text-green-800
                        @elseif($review->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $review->getStatusText() }}
                    </span>

                    @if($review->is_verified)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-check-circle ml-1"></i>
                            موثق
                        </span>
                    @endif

                    @if($review->sentiment)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                            @if($review->sentiment === 'positive') bg-green-100 text-green-800
                            @elseif($review->sentiment === 'negative') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $review->getSentimentText() }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex space-x-2 space-x-reverse">
                @if(auth()->user()->id === $review->user_id || auth()->user()->hasRole('admin'))
                    <a href="{{ route('reviews.edit', $review->id) }}" 
                       class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-edit ml-2"></i>تعديل
                    </a>
                @endif
                
                @if(auth()->user()->hasRole('admin'))
                    @if($review->status === 'pending')
                        <form action="{{ route('reviews.approve', $review->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check ml-2"></i>موافقة
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        <!-- User Info -->
        <div class="flex items-center mb-6">
            <div class="flex-shrink-0 h-12 w-12">
                <img class="h-12 w-12 rounded-full" src="{{ $review->user->avatar ?? 'images/default-avatar.png' }}" alt="">
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-900">{{ $review->user->name }}</p>
                <p class="text-xs text-gray-500">{{ $review->created_at->locale('ar')->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- Review Content -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">التقييم</h3>
                <div class="prose prose-lg text-gray-700">
                    {!! nl2br(e($review->content)) !!}
                </div>
            </div>

            @if($review->pros)
                <div>
                    <h3 class="text-lg font-semibold text-green-700 mb-3">
                        <i class="fas fa-thumbs-up ml-2"></i>الإيجابيات
                    </h3>
                    <div class="prose prose-lg text-gray-700">
                        {!! nl2br(e($review->pros)) !!}
                    </div>
                </div>
            @endif

            @if($review->cons)
                <div>
                    <h3 class="text-lg font-semibold text-red-700 mb-3">
                        <i class="fas fa-thumbs-down ml-2"></i>السلبيات
                    </h3>
                    <div class="prose prose-lg text-gray-700">
                        {!! nl2br(e($review->cons)) !!}
                    </div>
                </div>
            @endif

            @if($review->recommendation !== null)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">التوصية</h3>
                    <div class="flex items-center">
                        @if($review->recommendation)
                            <i class="fas fa-thumbs-up text-green-600 text-2xl ml-3"></i>
                            <span class="text-lg font-medium text-green-700">يوصي بهذا العنصر</span>
                        @else
                            <i class="fas fa-thumbs-down text-red-600 text-2xl ml-3"></i>
                            <span class="text-lg font-medium text-red-700">لا يوصي بهذا العنصر</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Vote Actions -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-4 space-x-reverse">
                    @if(!auth()->user() || auth()->user()->id !== $review->user_id)
                        @if(!$review->hasUserVoted(auth()->user()->id ?? null))
                            <form action="{{ route('reviews.helpful', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                                    <i class="fas fa-thumbs-up ml-2"></i>
                                    مفيد ({{ $review->isHelpful() }})
                                </button>
                            </form>
                            <form action="{{ route('reviews.notHelpful', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                    <i class="fas fa-thumbs-down ml-2"></i>
                                    غير مفيد ({{ $review->isNotHelpful() }})
                                </button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">لقد قمت بالتصويت بالفعل</span>
                        @endif
                    @endif

                    <form action="{{ route('reviews.report', $review->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200">
                            <i class="fas fa-flag ml-2"></i>
                            إبلاغ
                        </button>
                    </form>
                </div>

                <div class="text-sm text-gray-500">
                    <i class="fas fa-eye ml-1"></i>
                    {{ rand(50, 500) }} مشاهدة
                </div>
            </div>
        </div>
    </div>

    <!-- Reviewable Info -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">عنصر التقييم</h3>
        <div class="flex items-center">
            <div class="flex-shrink-0 h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                @if($review->reviewable_type === 'App\Models\Property')
                    <i class="fas fa-home text-gray-600 text-2xl"></i>
                @elseif($review->reviewable_type === 'App\Models\Agent')
                    <i class="fas fa-user-tie text-gray-600 text-2xl"></i>
                @else
                    <i class="fas fa-star text-gray-600 text-2xl"></i>
                @endif
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-900">
                    @if($review->reviewable_type === 'App\Models\Property')
                        عقار
                    @elseif($review->reviewable_type === 'App\Models\Agent')
                        وكيل عقاري
                    @else
                        {{ $review->reviewable_type }}
                    @endif
                </p>
                <p class="text-xs text-gray-500">معرف: {{ $review->reviewable_id }}</p>
            </div>
            <div class="mr-auto">
                <a href="#" class="text-blue-600 hover:text-blue-900">
                    عرض التفاصيل <i class="fas fa-arrow-left ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Responses -->
    @if($review->responses->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">الردود ({{ $review->responses->count() }})</h3>
            
            <div class="space-y-4">
                @foreach($review->responses as $response)
                    <div class="border-l-4 
                        @if($response->is_official) border-blue-500 bg-blue-50
                        @else border-gray-300 bg-gray-50
                        @endif p-4 rounded-r-lg">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $response->user->avatar ?? 'images/default-avatar.png' }}" alt="">
                                </div>
                                <div class="mr-3">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">{{ $response->user->name }}</p>
                                        @if($response->is_official)
                                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                رد رسمي
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $response->created_at->locale('ar')->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>

                            @if(auth()->user()->id === $response->user_id || auth()->user()->hasRole('admin'))
                                <div class="flex space-x-2 space-x-reverse">
                                    <a href="{{ route('review-responses.edit', $response->id) }}" 
                                       class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('review-responses.destroy', $response->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3 prose prose-sm text-gray-700">
                            {!! nl2br(e($response->content)) !!}
                        </div>

                        <!-- Response Votes -->
                        <div class="mt-3 flex items-center space-x-4 space-x-reverse">
                            @if(!auth()->user() || auth()->user()->id !== $response->user_id)
                                @if(!$response->hasUserVoted(auth()->user()->id ?? null))
                                    <form action="{{ route('review-responses.helpful', $response->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-green-600 hover:text-green-800">
                                            <i class="fas fa-thumbs-up ml-1"></i>
                                            مفيد ({{ $response->isHelpful() }})
                                        </button>
                                    </form>
                                    <form action="{{ route('review-responses.notHelpful', $response->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                            <i class="fas fa-thumbs-down ml-1"></i>
                                            غير مفيد ({{ $response->isNotHelpful() }})
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Add Response -->
    @if(auth()->user() && auth()->user()->id !== $review->user_id && $review->canBeRespondedTo())
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">إضافة رد</h3>
            
            <form action="{{ route('review-responses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="review_id" value="{{ $review->id }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرد</label>
                    <textarea name="content" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="اكتب ردك هنا..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane ml-2"></i>
                        إرسال الرد
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
