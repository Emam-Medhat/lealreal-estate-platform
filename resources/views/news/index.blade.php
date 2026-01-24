@extends('layouts.app')

@section('title', 'News - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Real Estate News</h1>
                <p class="text-xl text-purple-100">Latest updates, market news, and industry insights</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Category Filter -->
                @if($categories->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('news.index') }}" 
                               class="px-4 py-2 rounded-full text-sm {{ !request('category') ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                                All News
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('news.index', ['category' => $category]) }}" 
                                   class="px-4 py-2 rounded-full text-sm {{ request('category') === $category ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                                    {{ ucfirst($category) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- News Articles -->
                <div class="space-y-6">
                    @foreach($news as $article)
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            <div class="flex flex-col md:flex-row">
                                @if($article->featured_image)
                                    <div class="md:w-1/3">
                                        <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                             alt="{{ $article->title }}" 
                                             class="w-full h-48 md:h-full object-cover">
                                    </div>
                                @endif
                                <div class="flex-1 p-6">
                                    <div class="flex items-center mb-3">
                                        @if($article->category)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full mr-2">
                                                {{ ucfirst($article->category) }}
                                            </span>
                                        @endif
                                        @if($article->is_featured)
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full mr-2">
                                                <i class="fas fa-star mr-1"></i>Featured
                                            </span>
                                        @endif
                                        <span class="text-sm text-gray-500">
                                            {{ $article->published_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-3">
                                        <a href="{{ route('news.show', $article->slug) }}" class="hover:text-purple-600">
                                            {{ $article->title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 mb-4">{{ $article->getFormattedExcerpt(150) }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <img src="{{ $article->author->avatar ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $article->author->name }}" 
                                                 class="w-8 h-8 rounded-full mr-2">
                                            <span class="text-sm text-gray-700">{{ $article->author->name }}</span>
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-eye mr-1"></i>{{ $article->views }}</span>
                                            <a href="{{ route('news.show', $article->slug) }}" 
                                               class="text-purple-600 hover:text-purple-700">
                                                Read More <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $news->links() }}
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Featured News -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Featured News</h3>
                    <div class="space-y-4">
                        @foreach($news->where('is_featured', true)->take(3) as $featured)
                            <div class="flex space-x-3">
                                @if($featured->featured_image)
                                    <img src="{{ asset('storage/' . $featured->featured_image) }}" 
                                         alt="{{ $featured->title }}" 
                                         class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 mb-1">
                                        <a href="{{ route('news.show', $featured->slug) }}" class="hover:text-purple-600">
                                            {{ Str::limit($featured->title, 50) }}
                                        </a>
                                    </h4>
                                    <p class="text-xs text-gray-500">{{ $featured->published_at->format('M d') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Categories -->
                @if($categories->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('news.index', ['category' => $category]) }}" 
                                   class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="text-gray-700">{{ ucfirst($category) }}</span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Newsletter -->
                <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-3">Stay Informed</h3>
                    <p class="text-purple-100 mb-4">Get the latest real estate news delivered to your inbox</p>
                    <form class="space-y-3">
                        <input type="email" placeholder="Your email" 
                               class="w-full px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-300">
                        <button type="submit" class="w-full px-4 py-2 bg-white text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
