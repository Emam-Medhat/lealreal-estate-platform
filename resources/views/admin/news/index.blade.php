@extends('layouts.app')

@section('title', 'News Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">News Articles</h1>
                    <p class="text-gray-600">Manage news articles and press releases</p>
                </div>
                <a href="{{ route('admin.news.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    New Article
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" placeholder="Search articles..." value="{{ request('search') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                    <select name="featured" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured</option>
                        <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Not Featured</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($news ?? [] as $article)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    @if ($article->featured_image)
                        <div class="h-48 bg-gray-200">
                            <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-newspaper text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-800 line-clamp-2">{{ $article->title }}</h3>
                            @if ($article->is_featured)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 ml-2">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ $article->excerpt }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span><i class="fas fa-folder mr-1"></i> {{ $article->category->name ?? 'Uncategorized' }}</span>
                            <span>{{ $article->published_at ? $article->published_at->format('M j, Y') : 'Not published' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 
                                   ($article->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ ucfirst($article->status) }}
                            </span>
                            <div class="flex space-x-2">
                                <a href="{{ route('news.show', $article->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="{{ route('admin.news.edit', $article) }}" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.news.destroy', $article) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-newspaper text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No news articles found</h3>
                    <p class="text-gray-600 mb-4">Get started by publishing your first news article.</p>
                    <a href="{{ route('admin.news.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Article
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if (isset($news) && $news->hasPages())
            <div class="mt-8">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
