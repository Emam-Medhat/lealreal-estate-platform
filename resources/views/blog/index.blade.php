@extends('layouts.app')

@section('title', 'Blog - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Real Estate Blog</h1>
                <p class="text-xl text-blue-100">Expert insights, market trends, and property tips</p>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="container mx-auto px-4 -mt-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <form action="{{ route('blog.search') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <input type="text" name="q" placeholder="Search articles..." 
                       value="{{ request('q') }}" 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </form>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Featured Posts -->
                @if($featuredPosts->count() > 0)
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Posts</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($featuredPosts as $post)
                                <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                             alt="{{ $post->title }}" 
                                             class="w-full h-48 object-cover">
                                    @endif
                                    <div class="p-6">
                                        <div class="flex items-center mb-3">
                                            @if($post->category)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full mr-2">
                                                    {{ $post->category->name }}
                                                </span>
                                            @endif
                                            <span class="text-sm text-gray-500">
                                                {{ $post->published_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-3">
                                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 mb-4">{{ $post->getFormattedExcerpt(120) }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <img src="{{ $post->author->avatar ?? asset('images/default-avatar.png') }}" 
                                                     alt="{{ $post->author->name }}" 
                                                     class="w-8 h-8 rounded-full mr-2">
                                                <span class="text-sm text-gray-700">{{ $post->author->name }}</span>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $post->reading_time }} min read</span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Posts -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Posts</h2>
                    <div class="space-y-6">
                        @foreach($posts as $post)
                            <article class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                                <div class="flex flex-col md:flex-row gap-6">
                                    @if($post->featured_image)
                                        <div class="md:w-1/3">
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                 alt="{{ $post->title }}" 
                                                 class="w-full h-48 md:h-32 object-cover rounded-lg">
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center mb-3">
                                            @if($post->category)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full mr-2">
                                                    {{ $post->category->name }}
                                                </span>
                                            @endif
                                            <span class="text-sm text-gray-500">
                                                {{ $post->published_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-3">
                                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 mb-4">{{ $post->getFormattedExcerpt(150) }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <img src="{{ $post->author->avatar ?? asset('images/default-avatar.png') }}" 
                                                     alt="{{ $post->author->name }}" 
                                                     class="w-8 h-8 rounded-full mr-2">
                                                <span class="text-sm text-gray-700">{{ $post->author->name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span>{{ $post->reading_time }} min read</span>
                                                <span>{{ $post->views }} views</span>
                                            </div>
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
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Categories -->
                @if($categories->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('blog.category', $category->slug) }}" 
                                   class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="text-gray-700">{{ $category->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $category->active_posts_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Popular Tags -->
                @if($popular_tags->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($popular_tags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" 
                                   class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-blue-100 hover:text-blue-700 transition-colors">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Newsletter Signup -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-3">Stay Updated</h3>
                    <p class="text-blue-100 mb-4">Get the latest real estate insights delivered to your inbox</p>
                    <form class="space-y-3">
                        <input type="email" placeholder="Your email" 
                               class="w-full px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <button type="submit" class="w-full px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
