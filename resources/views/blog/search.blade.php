@extends('layouts.app')

@section('title', 'بحث في المدونة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">بحث في المدونة</h1>
        <p class="text-gray-600">ابحث عن المقالات والمواضيع التي تهمك</p>
    </div>

    <!-- Search Form -->
    <div class="max-w-2xl mx-auto mb-8">
        <form action="{{ route('blog.search') }}" method="GET" class="relative">
            <div class="flex">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $query ?? '' }}" 
                    placeholder="ابحث عن مقالات..." 
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    autofocus
                >
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition duration-200"
                >
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    @if(isset($query))
        <!-- Search Results -->
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    نتائج البحث عن: "{{ $query }}"
                </h2>
                <p class="text-gray-600">
                    {{ $posts->total() }} نتيجة تم العثور عليها
                </p>
            </div>

            @if($posts->count() > 0)
                <!-- Results Grid -->
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                            @if($post->featured_image)
                                <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset($post->featured_image) }}')">
                                </div>
                            @else
                                <div class="h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <div class="p-6">
                                <div class="mb-3">
                                    @if($post->category)
                                        <span class="inline-block px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-100 rounded-full">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    <span class="text-gray-500 text-sm mr-2">
                                        {{ $post->published_at->format('Y-m-d') }}
                                    </span>
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    <a href="{{ route('blog.show', $post) }}" class="hover:text-blue-600 transition duration-200">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}
                                </p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        @if($post->author)
                                            <img src="{{ $post->author->avatar ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $post->author->name }}" 
                                                 class="w-8 h-8 rounded-full mr-2">
                                            <span class="text-sm text-gray-600">{{ $post->author->name }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <i class="fas fa-eye text-sm ml-1"></i>
                                        <span class="text-sm">{{ $post->views }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-12">
                    <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد نتائج</h3>
                    <p class="text-gray-600 mb-6">
                        لم يتم العثور على أي مقالات تطابق بحثك. حاول استخدام كلمات مفتاحية مختلفة.
                    </p>
                    <a href="{{ route('blog.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        العودة إلى المدونة
                    </a>
                </div>
            @endif
        </div>
    @else
        <!-- Search Suggestions -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">
                    <i class="fas fa-lightbulb ml-2"></i>
                    نصائح للبحث
                </h3>
                <ul class="space-y-2 text-blue-700">
                    <li>• استخدم كلمات مفتاحية محددة وواضحة</li>
                    <li>• يمكنك البحث باللغة العربية أو الإنجليزية</li>
                    <li>• جرب استخدام مرادفات لكلماتك المفتاحية</li>
                    <li>• ابحث عن أسماء المدن أو المناطق العقارية</li>
                    <li>• يمكنك البحث عن مواضيع مثل "تمويل" أو "استثمار"</li>
                </ul>
            </div>
            
            <!-- Popular Categories -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">الفئات الشائعة</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @if($categories = \App\Models\BlogCategory::withCount('posts')->get())
                        @foreach($categories->take(6) as $category)
                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                               class="flex items-center justify-between p-4 bg-white rounded-lg shadow hover:shadow-md transition duration-200">
                                <span class="font-medium text-gray-800">{{ $category->name }}</span>
                                <span class="text-sm text-gray-500">{{ $category->posts_count }} مقال</span>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
