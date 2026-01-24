@extends('layouts.app')

@section('title', $post->title . ' - Blog')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Article Header -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-8">
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
                <span>/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-blue-600">Blog</a>
                @if($post->category)
                    <span>/</span>
                    <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-blue-600">
                        {{ $post->category->name }}
                    </a>
                @endif
            </nav>

            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
                
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $post->author->avatar ?? asset('images/default-avatar.png') }}" 
                             alt="{{ $post->author->name }}" 
                             class="w-12 h-12 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $post->author->name }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $post->published_at->format('M d, Y') }} • {{ $post->reading_time }} min read
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span><i class="fas fa-eye mr-1"></i>{{ $post->views }} views</span>
                        @if($post->allow_comments)
                            <span><i class="fas fa-comments mr-1"></i>Comments</span>
                        @endif
                    </div>
                </div>

                @if($post->category || $post->tags->count() > 0)
                    <div class="flex flex-wrap items-center gap-2">
                        @if($post->category)
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                                {{ $post->category->name }}
                            </span>
                        @endif
                        @foreach($post->tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" 
                               class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Featured Image -->
            @if($post->featured_image)
                <div class="mb-8">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-96 object-cover rounded-xl shadow-lg">
                </div>
            @endif

            <!-- Article Body -->
            <article class="prose prose-lg max-w-none">
                {!! $post->content !!}
            </article>

            <!-- Share Section -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Share this article</h3>
                    <div class="flex space-x-3">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f mr-2"></i>Share
                        </button>
                        <button class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors">
                            <i class="fab fa-twitter mr-2"></i>Tweet
                        </button>
                        <button class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors">
                            <i class="fab fa-linkedin-in mr-2"></i>Share
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
                <div class="mt-16">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $related)
                            <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                @if($related->featured_image)
                                    <img src="{{ asset('storage/' . $related->featured_image) }}" 
                                         alt="{{ $related->title }}" 
                                         class="w-full h-48 object-cover">
                                @endif
                                <div class="p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">
                                        <a href="{{ route('blog.show', $related->slug) }}" class="hover:text-blue-600">
                                            {{ $related->title }}
                                        </a>
                                    </h4>
                                    <p class="text-gray-600 text-sm mb-3">{{ $related->getFormattedExcerpt(100) }}</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <span>{{ $related->published_at->format('M d, Y') }}</span>
                                        <span>{{ $related->reading_time }} min read</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Comments Section -->
            @if($post->allow_comments)
                <div class="mt-16">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">Comments</h3>
                    
                    <!-- Comment Form -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Leave a Comment</h4>
                        <form class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" placeholder="Your Name" 
                                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="email" placeholder="Your Email" 
                                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <textarea placeholder="Your Comment" rows="4" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Post Comment
                            </button>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-6">
                        <!-- Sample Comment -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-start space-x-4">
                                <img src="https://picsum.photos/seed/user1/40/40.jpg" 
                                     alt="User" 
                                     class="w-10 h-10 rounded-full">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-semibold text-gray-900">John Doe</h5>
                                        <span class="text-sm text-gray-500">2 days ago</span>
                                    </div>
                                    <p class="text-gray-700">Great article! Very informative and well-written. Looking forward to more content like this.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
