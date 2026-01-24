@extends('layouts.app')

@section('title', 'Guides - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Real Estate Guides</h1>
                <p class="text-xl text-green-100">Step-by-step guides for buying, selling, and investing in real estate</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Category Filter -->
                        @if($categories->count() > 0)
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select onchange="window.location.href='?category='+this.value" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                            {{ ucfirst($category) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Difficulty Filter -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                            <select onchange="window.location.href='?difficulty='+this.value" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">All Levels</option>
                                <option value="beginner" {{ request('difficulty') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ request('difficulty') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ request('difficulty') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Guides Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($guides as $guide)
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            @if($guide->featured_image)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $guide->featured_image) }}" 
                                         alt="{{ $guide->title }}" 
                                         class="w-full h-48 object-cover">
                                    @if($guide->is_featured)
                                        <div class="absolute top-4 right-4 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="p-6">
                                <div class="flex items-center mb-3">
                                    <span class="px-2 py-1 bg-{{ $guide->getDifficultyColor() }}-100 text-{{ $guide->getDifficultyColor() }}-700 text-xs rounded-full mr-2">
                                        {{ $guide->getDifficultyLabel() }}
                                    </span>
                                    @if($guide->category)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full mr-2">
                                            {{ ucfirst($guide->category) }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">
                                    <a href="{{ route('guides.show', $guide->slug) }}" class="hover:text-green-600">
                                        {{ $guide->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-4">{{ $guide->getFormattedExcerpt(120) }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <img src="{{ $guide->author->avatar ?? asset('images/default-avatar.png') }}" 
                                             alt="{{ $guide->author->name }}" 
                                             class="w-8 h-8 rounded-full mr-2">
                                        <span class="text-sm text-gray-700">{{ $guide->author->name }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3 text-sm text-gray-500">
                                        <span><i class="fas fa-clock mr-1"></i>{{ $guide->reading_time }} min</span>
                                        <span><i class="fas fa-eye mr-1"></i>{{ $guide->views }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $guides->links() }}
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Popular Guides -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Guides</h3>
                    <div class="space-y-4">
                        @foreach($guides->sortByDesc('views')->take(5) as $popular)
                            <div class="flex space-x-3">
                                @if($popular->featured_image)
                                    <img src="{{ asset('storage/' . $popular->featured_image) }}" 
                                         alt="{{ $popular->title }}" 
                                         class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 mb-1">
                                        <a href="{{ route('guides.show', $popular->slug) }}" class="hover:text-green-600">
                                            {{ Str::limit($popular->title, 40) }}
                                        </a>
                                    </h4>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500">
                                        <span>{{ $popular->reading_time }} min</span>
                                        <span>{{ $popular->views }} views</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Difficulty Levels -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Difficulty Levels</h3>
                    <div class="space-y-3">
                        <a href="{{ route('guides.index', ['difficulty' => 'beginner']) }}" 
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                                <span class="text-gray-700">Beginner</span>
                            </div>
                            <span class="text-sm text-gray-500">Easy to follow</span>
                        </a>
                        <a href="{{ route('guides.index', ['difficulty' => 'intermediate']) }}" 
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></span>
                                <span class="text-gray-700">Intermediate</span>
                            </div>
                            <span class="text-sm text-gray-500">Some experience</span>
                        </a>
                        <a href="{{ route('guides.index', ['difficulty' => 'advanced']) }}" 
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <span class="w-3 h-3 bg-red-500 rounded-full mr-3"></span>
                                <span class="text-gray-700">Advanced</span>
                            </div>
                            <span class="text-sm text-gray-500">Expert level</span>
                        </a>
                    </div>
                </div>

                <!-- Categories -->
                @if($categories->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('guides.index', ['category' => $category]) }}" 
                                   class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="text-gray-700">{{ ucfirst($category) }}</span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Newsletter -->
                <div class="bg-gradient-to-r from-green-600 to-green-800 text-white rounded-xl p-6">
                    <h3 class="text-lg font-semibold mb-3">Learn More</h3>
                    <p class="text-green-100 mb-4">Get expert tips and guides delivered to your inbox</p>
                    <form class="space-y-3">
                        <input type="email" placeholder="Your email" 
                               class="w-full px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-300">
                        <button type="submit" class="w-full px-4 py-2 bg-white text-green-600 rounded-lg font-semibold hover:bg-green-50 transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
